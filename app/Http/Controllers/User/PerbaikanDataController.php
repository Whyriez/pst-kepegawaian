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
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class PerbaikanDataController extends Controller
{
    public function index()
    {
        $pegawaiId = Auth::user()->pegawai->id;

        // Ambil riwayat pengajuan (Slug: perbaikan-data-asn)
        $pengajuans = Pengajuan::with('jenisLayanan', 'pegawai')
            ->where('pegawai_id', $pegawaiId)
            ->whereHas('jenisLayanan', function ($query) {
                $query->where('slug', 'perbaikan-data-asn');
            })
            ->latest()
            ->paginate(10);

        return view('pages.user.perbaikan_data.index', compact('pengajuans'));
    }

    public function create()
    {
        if (!CekPeriode::isBuka('perbaikan-data-asn')) {
            return redirect()->route('perbaikan_data')
                ->with('error', 'Maaf, Periode pengajuan Perbaikan Data ASN sedang DITUTUP.');
        }

        $pegawai = Pegawai::with('satuanKerja')->where('user_id', Auth::id())->first();

        // Pastikan slug 'perbaikan-data-asn' ada di tabel jenis_layanans
        $layanan = JenisLayanan::with('syaratDokumens')->where('slug', 'perbaikan-data-asn')->first();
        $syarat = $layanan ? $layanan->syaratDokumens : collect([]);

        return view('pages.user.perbaikan_data.create', compact('pegawai', 'syarat'));
    }

    public function store(Request $request)
    {
        if (!CekPeriode::isBuka('perbaikan-data-asn')) {
            return redirect()->back()->with('error', 'Gagal! Periode pengajuan telah berakhir.');
        }

        $request->validate([
            'nip_display_perbaikan_data_asn' => 'required|exists:pegawais,nip',
        ], [
            'nip_display_perbaikan_data_asn.required' => 'NIP wajib diisi.',
            'nip_display_perbaikan_data_asn.exists' => 'NIP tidak ditemukan dalam data pegawai.',
        ]);


        try {
            DB::beginTransaction();

            $pegawai = Pegawai::where('nip', $request->nip_display_perbaikan_data_asn)->firstOrFail();
            $layanan = JenisLayanan::where('slug', 'perbaikan-data-asn')->firstOrFail();

            $tiket = 'PBD-ASN-' . date('Ymd') . '-' . rand(1000, 9999);

            $pengajuan = Pengajuan::create([
                'nomor_tiket' => $tiket,
                'pegawai_id' => $pegawai->id,
                'jenis_layanan_id' => $layanan->id,
                'status' => 'pending',
                'prioritas' => 'sedang',
                'tanggal_pengajuan' => now(),
                'data_tambahan' => [
                    'jabatan' => $request->jabatan_pegawai_perbaikan_data_asn,
                    'pangkat' => $request->pangkat_pegawai_perbaikan_data_asn,
                    'unit_kerja' => $request->unit_kerja_pegawai_perbaikan_data_asn,
                    'golongan_ruang' => $request->golongan_ruang_pegawai_perbaikan_data_asn,
                ]
            ]);

            foreach ($layanan->syaratDokumens as $dokumen) {
                $inputName = 'file_' . $dokumen->id;

                if ($request->hasFile($inputName)) {
                    $file = $request->file($inputName);
                    $filename = $tiket . '_' . Str::slug($dokumen->nama_dokumen) . '.' . $file->getClientOriginalExtension();
                    $path = $file->storeAs('documents/perbaikan_data_asn', $filename, 'public');

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
            return redirect()->route('perbaikan_data')->with('success', 'Pengajuan Perbaikan Data ASN berhasil dikirim! Tiket: ' . $tiket);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal: ' . $e->getMessage())->withInput();
        }
    }

    public function edit(Request $request)
    {
        $id = $request->query('id');

        if (!$id) {
            return redirect()->route('perbaikan_data')->with('error', 'ID Pengajuan tidak ditemukan.');
        }

        // Ambil Data & Validasi
        $pengajuan = Pengajuan::with(['pegawai', 'dokumenPengajuans', 'jenisLayanan.syaratDokumens'])
            ->where('id', $id)
            ->where('pegawai_id', Auth::user()->pegawai->id)
            ->whereHas('jenisLayanan', function ($q) {
                $q->where('slug', 'perbaikan-data-asn');
            })
            ->firstOrFail();

        // Cek Status
        if (!in_array($pengajuan->status, ['pending', 'perbaikan'])) {
            return redirect()->route('perbaikan_data')
                ->with('error', 'Pengajuan ini sedang diproses atau sudah selesai.');
        }

        $syarat = $pengajuan->jenisLayanan->syaratDokumens;

        return view('pages.user.perbaikan_data.edit', compact('pengajuan', 'syarat'));
    }

    public function update(Request $request, $id)
    {
        // Validasi
        $request->validate([
            'jabatan_pegawai_perbaikan_data_asn' => 'required',
            'unit_kerja_pegawai_perbaikan_data_asn' => 'required',
        ], [
            'jabatan_pegawai_perbaikan_data_asn.required' => 'Jabatan pegawai wajib diisi.',
            'unit_kerja_pegawai_perbaikan_data_asn.required' => 'Unit kerja pegawai wajib diisi.',
        ]);


        try {
            DB::beginTransaction();

            $pengajuan = Pengajuan::where('id', $id)
                ->where('pegawai_id', Auth::user()->pegawai->id)
                ->firstOrFail();

            // 1. Update Data JSON
            $dataTambahan = $pengajuan->data_tambahan;

            $dataTambahan['jabatan'] = $request->jabatan_pegawai_perbaikan_data_asn;
            $dataTambahan['pangkat'] = $request->pangkat_pegawai_perbaikan_data_asn;
            $dataTambahan['unit_kerja'] = $request->unit_kerja_pegawai_perbaikan_data_asn;
            $dataTambahan['golongan_ruang'] = $request->golongan_ruang_pegawai_perbaikan_data_asn;

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
                    $path = $file->storeAs('documents/perbaikan_data_asn', $filename, 'public');

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
            return redirect()->route('perbaikan_data')->with('success', 'Perbaikan data berhasil dikirim!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal update: ' . $e->getMessage());
        }
    }
}
