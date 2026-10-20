<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="margin:0;padding:0;background:#F7F5F0;font-family:'Helvetica Neue',Arial,sans-serif">
    <table width="100%" cellpadding="0" cellspacing="0" style="padding:40px 20px">
        <tr>
            <td align="center">
                <table width="100%" cellpadding="0" cellspacing="0" style="max-width:520px;background:white;border-radius:20px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,.08)">
                    <!-- Header emas -->
                    <tr>
                        <td style="background:#C9A84C;padding:32px;text-align:center">
                            <div style="font-size:28px;font-weight:900;color:white;letter-spacing:4px">RIKOZA</div>
                            <div style="font-size:12px;color:rgba(255,255,255,.8);letter-spacing:6px;margin-top:4px">S T U D I O</div>
                        </td>
                    </tr>
                    <!-- Body -->
                    <tr>
                        <td style="padding:40px 36px">
                            <p style="font-size:22px;font-weight:800;color:#0F0F0F;margin:0 0 8px">Halo, {{ $nama }}! 👋</p>
                            <p style="font-size:15px;color:#6B6B6B;margin:0 0 28px;line-height:1.6">
                                Terima kasih sudah mendaftar di <strong style="color:#0F0F0F">Rikoza Studio</strong>. Satu langkah lagi — verifikasi email kamu untuk mengaktifkan akun.
                            </p>
                            <div style="text-align:center;margin:32px 0">
                                <a href="{{ $verifyUrl }}"
                                   style="display:inline-block;background:#C9A84C;color:white;font-size:15px;font-weight:800;letter-spacing:1px;padding:16px 40px;border-radius:12px;text-decoration:none">
                                    VERIFIKASI EMAIL SAYA
                                </a>
                            </div>
                            <p style="font-size:13px;color:#B0A99A;margin:0 0 6px">Atau salin link ini ke browser:</p>
                            <p style="font-size:12px;color:#C9A84C;word-break:break-all;margin:0">{{ $verifyUrl }}</p>
                            <hr style="border:none;border-top:1px solid #EEE;margin:28px 0">
                            <p style="font-size:12px;color:#B0A99A;margin:0;line-height:1.6">
                                Link ini berlaku selama <strong>24 jam</strong>. Jika kamu tidak mendaftar di Rikoza Studio, abaikan email ini.
                            </p>
                        </td>
                    </tr>
                    <!-- Footer -->
                    <tr>
                        <td style="background:#F7F5F0;padding:20px 36px;text-align:center">
                            <p style="font-size:11px;color:#B0A99A;margin:0">
                                © {{ date('Y') }} Rikoza Studio · Jl. Piai Tangah, Kec. Pauh, Kota Padang
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>