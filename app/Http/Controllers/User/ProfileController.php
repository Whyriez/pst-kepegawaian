<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\SatuanKerja;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user()->load('pegawai.satuanKerja');

        // Ambil semua data Satuan Kerja untuk dropdown (Khusus Admin)
        $satuanKerjas = SatuanKerja::all();

        // Cek role user untuk menentukan view mana yang dipakai
        if ($user->role === 'admin') {
            return view('pages.admin.profile.index', compact('user', 'satuanKerjas'));
        }

        return view('pages.user.profile.index', compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        $pegawaiId = $user->pegawai ? $user->pegawai->id : null;

        // 1. Validasi
        $rules = [
            'nama'      => 'required|string|max:255',
            'email'     => 'required|email|unique:users,email,' . $user->id,
            'avatar'    => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',

            // Validasi Data Pegawai
            'nip'       => 'required|numeric|digits:18|unique:pegawais,nip,' . $pegawaiId,
            'jabatan'   => 'required|string|max:255',
            'pangkat'   => 'required|string|max:255',
            'golongan_ruang' => 'required|string|max:50',
            'tempat_lahir'   => 'required|string|max:255',
            'tanggal_lahir'  => 'required|date',
            'pendidikan_terakhir' => 'required|string|max:255',
        ];

        $messages = [
            // User
            'nama.required' => 'Nama wajib diisi.',
            'nama.string' => 'Nama harus berupa teks.',
            'nama.max' => 'Nama maksimal 255 karakter.',

            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah digunakan oleh pengguna lain.',

            'avatar.image' => 'Avatar harus berupa file gambar.',
            'avatar.mimes' => 'Avatar harus berformat jpeg, png, jpg, atau gif.',
            'avatar.max' => 'Ukuran avatar maksimal 2MB.',

            // Pegawai
            'nip.required' => 'NIP wajib diisi.',
            'nip.numeric' => 'NIP harus berupa angka.',
            'nip.digits' => 'NIP harus berjumlah 18 digit.',
            'nip.unique' => 'NIP sudah terdaftar.',

            'jabatan.required' => 'Jabatan wajib diisi.',
            'pangkat.required' => 'Pangkat wajib diisi.',
            'golongan_ruang.required' => 'Golongan ruang wajib diisi.',
            'tempat_lahir.required' => 'Tempat lahir wajib diisi.',

            'tanggal_lahir.required' => 'Tanggal lahir wajib diisi.',
            'tanggal_lahir.date' => 'Format tanggal lahir tidak valid.',

            'pendidikan_terakhir.required' => 'Pendidikan terakhir wajib diisi.',
        ];

        // Jika Admin, wajib pilih Satuan Kerja
        if ($user->role === 'admin') {
            $rules['satuan_kerja_id'] = 'required|exists:satuan_kerjas,id';
        }

        $request->validate($rules, $messages);

        DB::beginTransaction();
        try {
            // A. Update User
            $userData = [
                'name'  => $request->nama,
                'email' => $request->email,
            ];

            if ($request->hasFile('avatar')) {
                if ($user->avatar && Storage::exists('public/' . $user->avatar)) {
                    Storage::delete('public/' . $user->avatar);
                }
                $path = $request->file('avatar')->store('avatars', 'public');
                $userData['avatar'] = $path;
            }

            $user->update($userData);

            // B. Update Data Pegawai

            // Tentukan Satuan Kerja ID
            if ($user->role === 'admin') {
                // Jika Admin, ambil dari input form select
                $satuanKerjaId = $request->satuan_kerja_id;
            } else {
                // Jika User biasa, ambil yang sudah ada (tidak boleh ganti sendiri)
                // Atau set default jika null (tapi harusnya diurus admin)
                $satuanKerjaId = $user->pegawai->satuan_kerja_id ?? $user->satuan_kerja_id;
            }

            $user->pegawai()->updateOrCreate(
                ['user_id' => $user->id],
                [
                    'satuan_kerja_id' => $satuanKerjaId, // <--- Ini kunci utamanya
                    'nip'             => $request->nip,
                    'nama_lengkap'    => $request->nama,
                    'jabatan'         => $request->jabatan,
                    'pangkat'         => $request->pangkat,
                    'golongan_ruang'  => $request->golongan_ruang,
                    'tempat_lahir'    => $request->tempat_lahir,
                    'tanggal_lahir'   => $request->tanggal_lahir,
                    'pendidikan_terakhir' => $request->pendidikan_terakhir,
                ]
            );

            DB::commit();

            // Redirect sesuai role
            $redirectRoute = ($user->role === 'admin') ? 'admin.dashboard' : 'profile';

            // Untuk Admin kita bisa return ke profile lagi atau dashboard
            return redirect()->back()->with('success', 'Profil Admin berhasil diperbarui!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }
}
