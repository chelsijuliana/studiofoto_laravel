<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - Rikoza Studio</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { background: #F7F5F0; font-family: 'Helvetica Neue', Arial, sans-serif; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 20px; }
        .card { background: white; border-radius: 20px; padding: 40px 36px; width: 100%; max-width: 440px; box-shadow: 0 4px 24px rgba(0,0,0,.08); }
        .brand { font-size: 13px; font-weight: 800; color: #C9A84C; letter-spacing: 3px; margin-bottom: 28px; }
        h1 { font-size: 22px; font-weight: 800; color: #0F0F0F; margin-bottom: 8px; }
        .subtitle { font-size: 14px; color: #6B6B6B; margin-bottom: 28px; line-height: 1.5; }
        .label { font-size: 10px; font-weight: 700; color: #B0A99A; letter-spacing: 1.5px; margin-bottom: 8px; display: block; }
        .field { width: 100%; border: 1px solid rgba(0,0,0,.12); border-radius: 12px; padding: 14px 16px; font-size: 14px; color: #0F0F0F; background: #F7F5F0; outline: none; transition: border-color .2s; margin-bottom: 16px; }
        .field:focus { border-color: #C9A84C; border-width: 1.5px; }
        .btn { width: 100%; background: #C9A84C; color: white; border: none; border-radius: 12px; padding: 16px; font-size: 14px; font-weight: 800; letter-spacing: 1px; cursor: pointer; margin-top: 8px; }
        .btn:hover { background: #b8943e; }
        .error { background: #FEF2F2; border: 1px solid #FECACA; border-radius: 10px; padding: 12px; font-size: 13px; color: #DC2626; margin-bottom: 16px; }
    </style>
</head>
<body>
    <div class="card">
        <div class="brand">RIKOZA STUDIO</div>
        <h1>Buat Password Baru</h1>
        <p class="subtitle">Masukkan password baru untuk akun <strong>{{ $email }}</strong></p>

        @if($errors->any())
            <div class="error">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="/reset-password">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            <input type="hidden" name="email" value="{{ $email }}">

            <label class="label">PASSWORD BARU</label>
            <input type="password" name="password" class="field" placeholder="Minimal 8 karakter" required>

            <label class="label">KONFIRMASI PASSWORD BARU</label>
            <input type="password" name="password_confirmation" class="field" placeholder="Ulangi password baru" required>

            <button type="submit" class="btn">SIMPAN PASSWORD BARU</button>
        </form>
    </div>
</body>
</html>