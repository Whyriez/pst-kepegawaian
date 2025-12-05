<?php

namespace App\Http\Controllers\User;

use App\Helpers\CekPeriode;
use App\Http\Controllers\Controller;
use App\Models\DokumenPengajuan;
use App\Models\JenisLayanan;
use App\Models\Pegawai;
use App\Models\Pengajuan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PenugasanController extends Controller
{
    public function index()
    {
        $pegawaiId = Auth::user()->pegawai->id;

        // Ambil riwayat pengajuan (Slug: penugasan)
        $pengajuans = Pengajuan::with('jenisLayanan', 'pegawai')
            ->where('pegawai_id', $pegawaiId)
            ->whereHas('jenisLayanan', function ($query) {
                $query->where('slug', 'penugasan');
            })
            ->latest()
            ->paginate(10);

        return view('pages.user.penugasan.index', compact('pengajuans'));
    }

    public function create()
    {
        if (!CekPeriode::isBuka('penugasan')) {
            return redirect()->route('penugasan')
                ->with('error', 'Maaf, Periode pengajuan Penugasan sedang DITUTUP.');
        }

        $pegawai = Pegawai::with('satuanKerja')->where('user_id', Auth::id())->first();

        // Pastikan slug 'penugasan' ada di tabel jenis_layanans
        $layanan = JenisLayanan::with('syaratDokumens')->where('slug', 'penugasan')->first();
        $syarat = $layanan ? $layanan->syaratDokumens : collect([]);

        return view('pages.user.penugasan.create', compact('pegawai', 'syarat'));
    }

    public function store(Request $request)
    {
        if (!CekPeriode::isBuka('penugasan')) {
            return redirect()->back()->with('error', 'Gagal! Periode pengajuan telah berakhir.');
        }

        $request->validate([
            'nip_display_penugasan' => 'required|exists:pegawais,nip',
        ], [
            'nip_display_penugasan.required' => 'NIP wajib diisi.',
            'nip_display_penugasan.exists' => 'NIP tidak ditemukan dalam data pegawai.',
        ]);


        try {
            DB::beginTransaction();

            $pegawai = Pegawai::where('nip', $request->nip_display_penugasan)->firstOrFail();
            $layanan = JenisLayanan::where('slug', 'penugasan')->firstOrFail();

            $tiket = 'TGS-' . date('Ymd') . '-' . rand(1000, 9999);

            $pengajuan = Pengajuan::create([
                'nomor_tiket' => $tiket,
                'pegawai_id' => $pegawai->id,
                'jenis_layanan_id' => $layanan->id,
                'status' => 'pending',
                'prioritas' => 'sedang',
                'tanggal_pengajuan' => now(),
                'data_tambahan' => [
                    'jabatan' => $request->jabatan_penugasan,
                    'pangkat' => $request->pangkat_penugasan,
                    'unit_kerja' => $request->satuan_kerja_penugasan,
                    'golongan_ruang' => $request->golongan_ruang_penugasan,
                ]
            ]);

            foreach ($layanan->syaratDokumens as $dokumen) {
                $inputName = 'file_' . $dokumen->id;

                if ($request->hasFile($inputName)) {
                    $file = $request->file($inputName);
                    $filename = $tiket . '_' . Str::slug($dokumen->nama_dokumen) . '.' . $file->getClientOriginalExtension();
                    $path = $file->storeAs('documents/penugasan', $filename, 'public');

                    DokumenPengajuan::create([
                        'pengajuan_id' => $pengajuan->id,
                        'syarat_dokumen_id' => $dokumen->id,
                        'nama_file_asli' => $file->getClientOriginalName(),
                        'path_file' => $path,
                        'tipe_file' => $file->getClientMimeType(),
                        'ukuran_file' => $file->getSize() / 1024,
                    ]);
                } elseif ($dokumen->is_required) {
                    throw new \Exception("Dokumen wajib: {$dokumen->nama_dokumen} belum diunggah.");
                }
            }

            DB::commit();
            return redirect()->route('penugasan')->with('success', 'Pengajuan Penugasan berhasil dikirim! Tiket: ' . $tiket);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal: ' . $e->getMessage())->withInput();
        }
    }

    public function edit(Request $request)
    {
        $id = $request->query('id');

        if (!$id) {
            return redirect()->route('penugasan')->with('error', 'ID Pengajuan tidak ditemukan.');
        }

        // Ambil Data & Validasi
        $pengajuan = Pengajuan::with(['pegawai', 'dokumenPengajuans', 'jenisLayanan.syaratDokumens'])
            ->where('id', $id)
            ->where('pegawai_id', Auth::user()->pegawai->id)
            ->whereHas('jenisLayanan', function ($q) {
                $q->where('slug', 'penugasan');
            })
            ->firstOrFail();

        // Cek Status
        if (!in_array($pengajuan->status, ['pending', 'perbaikan'])) {
            return redirect()->route('penugasan')
                ->with('error', 'Pengajuan ini sedang diproses atau sudah selesai.');
        }

        $syarat = $pengajuan->jenisLayanan->syaratDokumens;

        return view('pages.user.penugasan.edit', compact('pengajuan', 'syarat'));
    }

    public function update(Request $request, $id)
    {
        // Validasi
        $request->validate([
            'jabatan_penugasan' => 'required',
            'satuan_kerja_penugasan' => 'required',
        ], [
            'jabatan_penugasan.required' => 'Jabatan penugasan wajib diisi.',
            'satuan_kerja_penugasan.required' => 'Satuan kerja penugasan wajib diisi.',
        ]);


        try {
            DB::beginTransaction();

            $pengajuan = Pengajuan::where('id', $id)
                ->where('pegawai_id', Auth::user()->pegawai->id)
                ->firstOrFail();

            // 1. Update Data JSON
            $dataTambahan = $pengajuan->data_tambahan;

            $dataTambahan['jabatan'] = $request->jabatan_penugasan;
            $dataTambahan['pangkat'] = $request->pangkat_penugasan;
            $dataTambahan['unit_kerja'] = $request->satuan_kerja_penugasan;
            $dataTambahan['golongan_ruang'] = $request->golongan_ruang_penugasan;

            $pengajuan->update([
                'data_tambahan' => $dataTambahan,
                'status' => 'pending',
                'catatan_admin' => null,
                'tanggal_pengajuan' => now(),
            ]);

            // 2. Update Dokumen
            $layanan = $pengajuan->jenisLayanan;

            foreach ($layanan->syaratDokumens as $dokumen) {
                $inputName = 'file_' . $dokumen->id;

                if ($request->hasFile($inputName)) {
                    $file = $request->file($inputName);

                    // Hapus File Lama
                    $oldDoc = DokumenPengajuan::where('pengajuan_id', $pengajuan->id)
                        ->where('syarat_dokumen_id', $dokumen->id)
                        ->first();

                    if ($oldDoc && Storage::disk('public')->exists($oldDoc->path_file)) {
                        Storage::disk('public')->delete($oldDoc->path_file);
                    }

                    // Upload File Baru
                    $filename = $pengajuan->nomor_tiket . '_' . Str::slug($dokumen->nama_dokumen) . '_' . time() . '.' . $file->getClientOriginalExtension();
                    $path = $file->storeAs('documents/penugasan', $filename, 'public');

                    DokumenPengajuan::updateOrCreate(
                        [
                            'pengajuan_id' => $pengajuan->id,
                            'syarat_dokumen_id' => $dokumen->id
                        ],
                        [
                            'nama_file_asli' => $file->getClientOriginalName(),
                            'path_file' => $path,
                            'tipe_file' => $file->getClientMimeType(),
                            'ukuran_file' => $file->getSize() / 1024,
                        ]
                    );
                }
            }

            DB::commit();
            return redirect()->route('penugasan')->with('success', 'Perbaikan data Penugasan berhasil dikirim!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal update: ' . $e->getMessage());
        }
    }
}
