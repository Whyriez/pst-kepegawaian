<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pegawai;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ManajemenAkunController extends Controller
{
    public function index(Request $request)
    {
        // Ambil juga relasi pegawai agar NIP bisa ditampilkan di tabel/modal
        $query = User::with('pegawai')->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    // Tambahan: Cari by NIP juga
                    ->orWhereHas('pegawai', function ($q2) use ($search) {
                        $q2->where('nip', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        $users = $query->paginate(10)->withQueryString();

        return view('pages.admin.manajemen_akun.index', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nip'      => 'required|numeric|digits:18|unique:pegawais,nip', // <--- Validasi NIP Wajib
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'role'     => 'required|in:admin,user',
        ], [
            'nip.unique' => 'NIP sudah terdaftar pada akun lain.',
            'nip.digits' => 'NIP harus 18 digit.',
        ]);

        try {
            DB::transaction(function () use ($request) {
                // Ambil Satker Admin yang sedang login
                // Asumsi: Admin punya data pegawai & satker. Jika null, set default atau handle error.
                $adminSatkerId = Auth::user()->pegawai->satuan_kerja_id ?? 1;

                // 1. Buat User (Login Data)
                $user = User::create([
                    'name'     => $request->name,
                    'email'    => $request->email,
                    'password' => Hash::make($request->password),
                    'role'     => $request->role,
                    'satuan_kerja_id' => $adminSatkerId, // Simpan satker di user juga jika perlu
                ]);

                // 2. Buat Pegawai (Biodata & NIP)
                Pegawai::create([
                    'user_id'         => $user->id,
                    'satuan_kerja_id' => $adminSatkerId,
                    'nama_lengkap'    => $request->name,
                    'nip'             => $request->nip, // <--- Simpan NIP
                ]);
            });

            return back()->with('success', 'Akun berhasil ditambahkan! Login menggunakan NIP tersebut.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal: ' . $e->getMessage())->withInput();
        }
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        // Ambil data pegawai terkait user ini
        $pegawai = $user->pegawai;

        $request->validate([
            // Validasi NIP: Unique kecuali punya diri sendiri
            'nip'      => ['required', 'numeric', 'digits:18', Rule::unique('pegawais')->ignore($pegawai->id ?? 0)],
            'name'     => 'required|string|max:255',
            'email'    => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'role'     => 'required|in:admin,user',
            'password' => 'nullable|min:6',
        ]);

        try {
            DB::transaction(function () use ($request, $user, $pegawai) {
                // 1. Update User
                $userData = [
                    'name'  => $request->name,
                    'email' => $request->email,
                    'role'  => $request->role,
                ];
                if ($request->filled('password')) {
                    $userData['password'] = Hash::make($request->password);
                }
                $user->update($userData);

                // 2. Update Pegawai (NIP & Nama)
                if ($pegawai) {
                    $pegawai->update([
                        'nama_lengkap' => $request->name,
                        'nip'          => $request->nip
                    ]);
                } else {
                    // Jaga-jaga jika user lama belum punya data di tabel pegawais
                    // Buat baru datanya
                    Pegawai::create([
                        'user_id' => $user->id,
                        'satuan_kerja_id' => Auth::user()->pegawai->satuan_kerja_id ?? 1,
                        'nama_lengkap' => $request->name,
                        'nip' => $request->nip
                    ]);
                }
            });

            return back()->with('success', 'Data akun & NIP berhasil diperbarui!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal update: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);

        // Mencegah hapus diri sendiri (opsional tapi disarankan)
        if ($user->id == auth()->id()) {
            return back()->with('error', 'Anda tidak bisa menghapus akun sendiri!');
        }

        $user->delete();
        return back()->with('success', 'Akun berhasil dihapus!');
    }
}
