<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>{{ $nama_surat ?? 'Surat Pengantar' }}</title>
</head>
<body style="font-family: 'Times New Roman', Times, serif; font-size: 12pt; line-height: 1.5; margin: 0;">

<div style="border-bottom: 3px double black; padding-bottom: 10px; margin-bottom: 10px;">
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

<div style="text-align: right; margin-bottom: 20px;">
    {{ $date }}
</div>

<div style="text-align: left; margin-bottom: 20px;">
    Yth. Kepala Kantor Wilayah Kementerian Agama Provinsi Gorontalo
</div>

<div style="text-align: center; margin-bottom: 20px;">
    <span style="font-weight: bold; text-decoration: underline; font-size: 14pt; display: block;">SURAT PENGANTAR</span>
    <span style="display: block;">NOMOR : {{ $nomor_surat ?? '111' }}</span>
</div>

<table style="width: 100%; border-collapse: collapse; border: 1px solid black;">
    <thead>
    <tr style="background-color: #f2f2f2;">
        <th style="border: 1px solid black; padding: 8px; width: 5%; text-align: center;">No</th>
        <th style="border: 1px solid black; padding: 8px; width: 50%; text-align: center;">Jenis Yang Dikirim</th>
        <th style="border: 1px solid black; padding: 8px; width: 20%; text-align: center;">Banyaknya</th>
        <th style="border: 1px solid black; padding: 8px; width: 25%; text-align: center;">Keterangan</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td style="border: 1px solid black; padding: 8px; vertical-align: top; text-align: center;">1</td>

        <td style="border: 1px solid black; padding: 8px; vertical-align: top; text-align: justify;">
            <div style="margin-bottom: 5px;">
                {{ $perihal ?? 'Usul Kenaikan Pangkat Periode Januari 2025' }}
            </div>

            <ol style="padding-left: 15px; margin: 0;">
                @if(isset($list_pegawai) && count($list_pegawai) > 0)
                    @foreach($list_pegawai as $pegawai)
                        <li style="margin-bottom: 5px;">
                            <strong>{{ $pegawai->nama }}</strong><br>
                            NIP. {{ $pegawai->nip }}
                        </li>
                    @endforeach
                @else
                    <li>

                    </li>
                @endif
            </ol>
        </td>

        <td style="border: 1px solid black; padding: 8px; vertical-align: top; text-align: center;">
            {{ $banyaknya_berkas ?? '12 (Dua Belas) Berkas' }}
        </td>

        <td style="border: 1px solid black; padding: 8px; vertical-align: top; text-align: left;">
            {{ $keterangan ?? 'Dikirim dengan hormat, untuk diproses lebih lanjut. Terima Kasih' }}
        </td>
    </tr>
    </tbody>
</table>

<table style="width: 100%; margin-top: 30px; border: none; page-break-inside: avoid;">

    <tr>
        <td style="width: 60%; vertical-align: top; padding-bottom: 10px;">
            Diterima tanggal: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; / &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; / 2025
        </td>
        <td style="width: 40%; vertical-align: top;">
        </td>
    </tr>

    <tr>
        <td style="vertical-align: top;">
            Penerima,<br>
            .......................................................
        </td>
        <td style="vertical-align: top;">
            Pengirim<br>
            {{ $signer->jabatan ?? 'Kepala Kantor' }},
        </td>
    </tr>

    <tr>
        <td style="height: 70px;">
        </td>
        <td style="height: 70px;">
        </td>
    </tr>

    <tr>
        <td style="vertical-align: bottom;">

        </td>
        <td style="vertical-align: bottom; font-weight: bold;">
            {{ $signer->name ?? 'Dr. Misnawaty S. Nuna, S.Ag.,M.H' }}
        </td>
    </tr>

    <tr>
        <td style="vertical-align: top;">
            NIP.
        </td>
        <td style="vertical-align: top;">
            NIP. {{ $signer->nip ?? '197205251997032003' }}
        </td>
    </tr>

    <tr>
        <td style="vertical-align: top; padding-top: 10px;">
            No.Telepon<br>
            No.Faksimile
        </td>
        <td>
        </td>
    </tr>
</table>

</body>
</html>
