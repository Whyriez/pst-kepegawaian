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
        // 1. Query Dasar
        $query = User::latest();

        // 2. Fitur Search (Nama atau Email)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // 3. Fitur Filter Role
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // 4. Pagination
        $users = $query->paginate(10)->withQueryString();

        return view('pages.admin.manajemen_akun.index', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'role'     => 'required|in:admin,user',
        ]);



        try {
            DB::transaction(function () use ($request) {
                $admin = Auth::user();
                $satuanKerjaId = $admin->pegawai->satuan_kerja_id;

                // 1. Buat Data User (Login)
                $user = User::create([
                    'name'            => $request->name,
                    'email'           => $request->email,
                    'password'        => Hash::make($request->password),
                    'role'            => $request->role,
                ]);

                Pegawai::create([
                    'user_id'         => $user->id,
                    'satuan_kerja_id' => $satuanKerjaId,
                    'nama_lengkap'    => $user->name,
                    'nip'             => $request->nip ?? null,
                ]);
            });

            return back()->with('success', 'Akun dan Data Pegawai berhasil ditambahkan!');
        } catch (\Exception $e) {
            // Jika error, kembalikan pesan errornya
            return back()->with('error', 'Gagal menambahkan akun: ' . $e->getMessage())->withInput();
        }

        return back()->with('success', 'Akun berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'role'  => 'required|in:admin,user',
            // Password nullable, jika kosong berarti tidak diubah
            'password' => 'nullable|min:6',
        ]);

        $data = [
            'name'  => $request->name,
            'email' => $request->email,
            'role'  => $request->role,
        ];

        // Cek jika password diisi, maka update password
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return back()->with('success', 'Data akun berhasil diperbarui!');
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
