<!DOCTYPE html>
<html lang="id">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"></head>
<body style="margin:0;padding:0;background:#F7F5F0;font-family:'Helvetica Neue',Arial,sans-serif">
    <table width="100%" cellpadding="0" cellspacing="0" style="padding:40px 20px">
        <tr>
            <td align="center">
                <table width="100%" cellpadding="0" cellspacing="0" style="max-width:520px;background:white;border-radius:20px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,.08)">
                    <tr>
                        <td style="background:#C9A84C;padding:32px;text-align:center">
                            <div style="font-size:28px;font-weight:900;color:white;letter-spacing:4px">RIKOZA</div>
                            <div style="font-size:12px;color:rgba(255,255,255,.8);letter-spacing:6px;margin-top:4px">S T U D I O</div>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:40px 36px">
                            <p style="font-size:22px;font-weight:800;color:#0F0F0F;margin:0 0 8px">Reset Password 🔐</p>
                            <p style="font-size:15px;color:#6B6B6B;margin:0 0 8px;line-height:1.6">Halo, <strong style="color:#0F0F0F">{{ $nama }}</strong>!</p>
                            <p style="font-size:15px;color:#6B6B6B;margin:0 0 28px;line-height:1.6">
                                Kami menerima permintaan reset password untuk akun Rikoza Studio kamu. Klik tombol di bawah untuk membuat password baru.
                            </p>
                            <div style="text-align:center;margin:32px 0">
                                <a href="{{ $resetUrl }}"
                                   style="display:inline-block;background:#C9A84C;color:white;font-size:15px;font-weight:800;letter-spacing:1px;padding:16px 40px;border-radius:12px;text-decoration:none">
                                    RESET PASSWORD SAYA
                                </a>
                            </div>
                            <p style="font-size:13px;color:#B0A99A;margin:0 0 6px">Atau salin link ini ke browser:</p>
                            <p style="font-size:12px;color:#C9A84C;word-break:break-all;margin:0 0 24px">{{ $resetUrl }}</p>
                            <div style="background:#FFF8F0;border:1px solid #F0D9A0;border-radius:10px;padding:14px 16px">
                                <p style="font-size:13px;color:#8B6914;margin:0;line-height:1.6">
                                    ⏰ Link ini berlaku selama <strong>60 menit</strong>.<br>
                                    Jika kamu tidak meminta reset password, abaikan email ini — password kamu aman.
                                </p>
                            </div>
                        </td>
                    </tr>
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