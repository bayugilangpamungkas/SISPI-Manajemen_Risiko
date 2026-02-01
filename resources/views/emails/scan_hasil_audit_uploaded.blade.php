<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scan Hasil Audit Uploaded</title>
</head>

<body style="margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #f4f4f4;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f4f4f4; padding: 20px 0;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0"
                    style="background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                    <!-- Header -->
                    <tr>
                        <td
                            style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%); padding: 30px 20px; text-align: center;">
                            <h1 style="color: #ffffff; margin: 0; font-size: 24px; font-weight: bold;">
                                ✅ Scan Hasil Audit Berhasil Diupload
                            </h1>
                            <p style="color: #ffffff; margin: 10px 0 0 0; font-size: 14px; opacity: 0.9;">
                                Dokumen Telah Ditandatangani dan Diupload ke Sistem SISPI
                            </p>
                        </td>
                    </tr>

                    <!-- Content -->
                    <tr>
                        <td style="padding: 30px 20px;">
                            <p style="color: #333333; font-size: 14px; line-height: 1.6; margin: 0 0 20px 0;">
                                Yth. Bapak/Ibu,
                            </p>

                            <p style="color: #333333; font-size: 14px; line-height: 1.6; margin: 0 0 20px 0;">
                                Kami informasikan bahwa <strong>scan hasil audit yang telah ditandatangani</strong>
                                telah berhasil diupload ke sistem SISPI.
                            </p>

                            <!-- Info Box -->
                            <table width="100%" cellpadding="15" cellspacing="0"
                                style="background-color: #f8f9fa; border-left: 4px solid #28a745; border-radius: 4px; margin: 20px 0;">
                                <tr>
                                    <td>
                                        <h3 style="color: #28a745; margin: 0 0 15px 0; font-size: 16px;">📋 Detail Hasil
                                            Audit</h3>

                                        <table width="100%" cellpadding="5" cellspacing="0">
                                            <tr>
                                                <td width="40%"
                                                    style="color: #495057; font-size: 13px; font-weight: bold; padding: 5px 0;">
                                                    Kode Risiko:</td>
                                                <td style="color: #212529; font-size: 13px; padding: 5px 0;">
                                                    {{ $hasilAudit->kode_risiko }}</td>
                                            </tr>
                                            <tr>
                                                <td
                                                    style="color: #495057; font-size: 13px; font-weight: bold; padding: 5px 0;">
                                                    Unit Kerja:</td>
                                                <td style="color: #212529; font-size: 13px; padding: 5px 0;">
                                                    {{ $hasilAudit->unit_kerja }}</td>
                                            </tr>
                                            <tr>
                                                <td
                                                    style="color: #495057; font-size: 13px; font-weight: bold; padding: 5px 0;">
                                                    Kegiatan:</td>
                                                <td style="color: #212529; font-size: 13px; padding: 5px 0;">
                                                    {{ $hasilAudit->kegiatan }}</td>
                                            </tr>
                                            <tr>
                                                <td
                                                    style="color: #495057; font-size: 13px; font-weight: bold; padding: 5px 0;">
                                                    Level Risiko:</td>
                                                <td style="color: #212529; font-size: 13px; padding: 5px 0;">
                                                    <strong>{{ $hasilAudit->level_risiko }}</strong>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td
                                                    style="color: #495057; font-size: 13px; font-weight: bold; padding: 5px 0;">
                                                    Tahun Anggaran:</td>
                                                <td style="color: #212529; font-size: 13px; padding: 5px 0;">
                                                    {{ $hasilAudit->tahun_anggaran }}</td>
                                            </tr>
                                            <tr>
                                                <td
                                                    style="color: #495057; font-size: 13px; font-weight: bold; padding: 5px 0;">
                                                    Auditor (Pemonev):</td>
                                                <td style="color: #212529; font-size: 13px; padding: 5px 0;">
                                                    {{ $hasilAudit->nama_pemonev }}</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <!-- Attachment Info -->
                            <table width="100%" cellpadding="12" cellspacing="0"
                                style="background-color: #e7f3ff; border-left: 4px solid #007bff; border-radius: 4px; margin: 20px 0;">
                                <tr>
                                    <td style="color: #004085; font-size: 13px; line-height: 1.5;">
                                        <strong>📎 File Terlampir:</strong><br>
                                        File scan hasil audit telah dilampirkan pada email ini. Anda dapat melihat
                                        dokumen yang telah ditandatangani melalui lampiran email.
                                    </td>
                                </tr>
                            </table>

                            @if ($hasilAudit->keterangan_scan)
                                <!-- Keterangan -->
                                <table width="100%" cellpadding="12" cellspacing="0"
                                    style="background-color: #fff3cd; border-left: 4px solid #ffc107; border-radius: 4px; margin: 20px 0;">
                                    <tr>
                                        <td style="color: #856404; font-size: 13px; line-height: 1.5;">
                                            <strong>📝 Keterangan:</strong><br>
                                            {{ $hasilAudit->keterangan_scan }}
                                        </td>
                                    </tr>
                                </table>
                            @endif

                            <!-- Upload Info -->
                            <table width="100%" cellpadding="5" cellspacing="0" style="margin: 20px 0;">
                                <tr>
                                    <td width="40%" style="color: #495057; font-size: 13px; font-weight: bold;">
                                        Diupload oleh:</td>
                                    <td style="color: #212529; font-size: 13px;">{{ $uploader->name }}
                                        ({{ $uploader->email }})</td>
                                </tr>
                                <tr>
                                    <td style="color: #495057; font-size: 13px; font-weight: bold; padding-top: 5px;">
                                        Tanggal Upload:</td>
                                    <td style="color: #212529; font-size: 13px; padding-top: 5px;">
                                        @if ($hasilAudit->tanggal_upload_scan)
                                            {{ $hasilAudit->tanggal_upload_scan->format('d F Y, H:i') }} WIB
                                        @else
                                            {{ now()->format('d F Y, H:i') }} WIB
                                        @endif
                                    </td>
                                </tr>
                            </table>

                            <!-- Button -->
                            <table width="100%" cellpadding="0" cellspacing="0" style="margin: 30px 0;">
                                <tr>
                                    <td align="center">
                                        <a href="{{ url('/manajemen-risiko/hasil-audit/' . $hasilAudit->id) }}"
                                            style="display: inline-block; padding: 14px 30px; background-color: #28a745; color: #ffffff; text-decoration: none; border-radius: 5px; font-weight: bold; font-size: 14px;">
                                            Lihat Detail di Sistem
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <!-- Note -->
                            <p
                                style="color: #6c757d; font-size: 12px; line-height: 1.5; margin: 20px 0 0 0; font-style: italic;">
                                Email ini dikirim secara otomatis oleh sistem SISPI (Sistem Informasi SPI). Mohon tidak
                                membalas email ini.
                            </p>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td
                            style="background-color: #f8f9fa; padding: 20px; text-align: center; border-top: 1px solid #dee2e6;">
                            <p style="margin: 0 0 5px 0; color: #212529; font-size: 13px; font-weight: bold;">
                                SATUAN PENGAWAS INTERNAL
                            </p>
                            <p style="margin: 0 0 5px 0; color: #495057; font-size: 13px;">
                                POLITEKNIK NEGERI MALANG
                            </p>
                            <p style="margin: 0; font-size: 12px;">
                                <a href="{{ url('/') }}"
                                    style="color: #007bff; text-decoration: none;">{{ url('/') }}</a>
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>
