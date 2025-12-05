<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>{{ $nama_surat ?? 'SPTJM' }}</title>
</head>
<body style="font-family: 'Times New Roman', Times, serif; font-size: 12pt; line-height: 1.5; margin: 0;">

<div style="border-bottom: 3px double black; padding-bottom: 10px; margin-bottom: 20px;">
    <table style="width: 100%; border-collapse: collapse;">
        <tr>
            <td style="width: 15%; text-align: center; vertical-align: middle;">
                <img src="{{ $logo_base64 }}" alt="Logo" style="width: 100px; height: auto;">
            </td>
            <td style="width: 85%; text-align: center; vertical-align: middle;">
                <span
                    style="font-size: 16pt; font-weight: bold; display: block;">KEMENTERIAN AGAMA REPUBLIK INDONESIA</span>
                <span style="font-size: 16pt; font-weight: bold; display: block;">KANTOR KEMENTERIAN AGAMA KOTA GORONTALO</span>
                <span style="font-size: 12pt; display: block;">
                        Jalan Arif Rahman Hakim Nomor 22 Kelurahan Dulalowo Timur Kota Gorontalo<br>
                        Telepon/WA Center : 081143302000<br>
                        Website : https://gorontalokota.kemenag.go.id Email : gorontalokota@kemenag.go.id
                    </span>
            </td>
        </tr>
    </table>
</div>

<div style="text-align: center; margin-bottom: 20px;">
    <span style="font-weight: bold; font-size: 14pt; display: block;">SURAT PERNYATAAN TANGGUNG JAWAB MUTLAK</span>
    <span style="display: block; text-transform: uppercase;">NOMOR : {{ $nomor_surat ?? '.......' }}</span>
</div>

<div style="margin-bottom: 15px;">
    Yang bertanda tangan di bawah ini:
    <table style="width: 100%; border-collapse: collapse; margin-top: 5px;">
        <tr>
            <td style="width: 20%; vertical-align: top;">Nama</td>
            <td style="width: 2%; vertical-align: top;">:</td>
            <td style="width: 78%; font-weight: bold;">{{ $signer->name ?? '-' }}</td>
        </tr>
        <tr>
            <td style="vertical-align: top;">NIP</td>
            <td style="vertical-align: top;">:</td>
            <td>{{ $signer->nip ?? '-' }}</td>
        </tr>
        <tr>
            <td style="vertical-align: top;">Jabatan</td>
            <td style="vertical-align: top;">:</td>
            <td>{{ $signer->jabatan ?? '-' }}</td>
        </tr>
    </table>
</div>

<div style="text-align: justify; margin-bottom: 10px;">
    Dengan ini menyatakan dan menjamin kebenaran dan bertanggung jawab atas dokumen dan data
    yang disampaikan dalam {{ $perihal ?? 'Usulan' }} telah sesuai dengan ketentuan
    peraturan perundang-undangan yang berlaku:
</div>

<table style="width: 100%; border-collapse: collapse; border: 1px solid black; margin-bottom: 10px;">
    <thead>
    <tr style="background-color: #f2f2f2; text-align: center; font-weight: bold;">
        <th style="border: 1px solid black; padding: 5px; width: 5%;">No</th>
        <th style="border: 1px solid black; padding: 5px; width: 25%;">Nama</th>
        <th style="border: 1px solid black; padding: 5px; width: 20%;">NIP</th>
        <th style="border: 1px solid black; padding: 5px; width: 15%;">Golongan Awal</th>
        <th style="border: 1px solid black; padding: 5px; width: 15%;">Golongan Yang Diusulkan</th>
        <th style="border: 1px solid black; padding: 5px; width: 20%;">Jabatan</th>
    </tr>
    </thead>
    <tbody>
    @if(isset($list_pegawai) && count($list_pegawai) > 0)
        @foreach($list_pegawai as $index => $pegawai)
            <tr>
                <td style="border: 1px solid black; padding: 5px; text-align: center; vertical-align: top;">{{ $index + 1 }}</td>
                <td style="border: 1px solid black; padding: 5px; vertical-align: top;">{{ $pegawai->nama }}</td>
                <td style="border: 1px solid black; padding: 5px; vertical-align: top;">{{ $pegawai->nip }}</td>

                {{-- Golongan Awal & Usulan (Nanti di controller harus disiapkan datanya) --}}
                <td style="border: 1px solid black; padding: 5px; text-align: center; vertical-align: top;">
                    {{ $pegawai->golongan_awal ?? '-' }}
                </td>
                <td style="border: 1px solid black; padding: 5px; text-align: center; vertical-align: top;">
                    {{ $pegawai->golongan_usulan ?? '-' }}
                </td>

                <td style="border: 1px solid black; padding: 5px; vertical-align: top;">
                    {{ $pegawai->jabatan_pegawai ?? '-' }}
                </td>
            </tr>
        @endforeach
    @else
        <tr>
            <td style="border: 1px solid black; padding: 5px; text-align: center;"></td>
            <td style="border: 1px solid black; padding: 5px;"></td>
            <td style="border: 1px solid black; padding: 5px;"></td>
            <td style="border: 1px solid black; padding: 5px; text-align: center;"></td>
            <td style="border: 1px solid black; padding: 5px; text-align: center;"></td>
            <td style="border: 1px solid black; padding: 5px;"></td>
        </tr>
    @endif
    </tbody>
</table>

<div style="text-align: justify; margin-bottom: 30px;">
    <p style="margin-bottom: 10px;">
        Apabila dikemudian hari ditemukan adanya dokumen dan data tersebut ternyata tidak benar, maka
        saya siap bertanggung jawab dan diberikan sanksi secara administratif maupun pidana.
    </p>
    <p>
        Demikian pernyataan ini saya buat dengan sadar dan tanpa tekanan dari pihak manapun.
    </p>
</div>

<table style="width: 100%; border: none; page-break-inside: avoid;">
    <tr>
        <td style="width: 50%;"></td>

        <td style="width: 50%; vertical-align: top;">
            <div style="margin-bottom: 5px;">
                {{ $lokasi_tanggal ?? 'Kecamatan Kota Tengah, ' . ($date ?? '04 Desember 2025') }}
            </div>

            <div style="margin-bottom: 5px;">Yang membuat pernyataan</div>
            <div style="font-weight: bold; margin-bottom: 80px;">{{ $signer->jabatan ?? 'Kepala Kantor' }},</div>

            <div style="font-weight: bold; text-decoration: underline;">
                {{ $signer->name ?? 'Dr. Misnawaty S. Nuna, S.Ag.,M.H' }}
            </div>
            <div>NIP. {{ $signer->nip ?? '197205251997032003' }}</div>
        </td>
    </tr>
</table>

</body>
</html>
