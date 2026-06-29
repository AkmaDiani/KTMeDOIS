<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Log in — KTM eDOIS</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    <div class="login-wrap">
        <div class="login-card">
            <div class="brand"><span class="dot"></span><span>KTM eDOIS</span></div>
            <div class="sub">Internal Review &amp; Approval Workflow</div>

            @if($errors->any())
                <div class="error-text">{{ $errors->first() }}</div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="field">
                    <label class="field-label">Staff Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" placeholder="hakim@ktm.com" required autofocus>
                </div>
                <div class="field">
                    <label class="field-label">Password</label>
                    <input type="password" name="password" required>
                </div>
                <button type="submit" class="btn btn-primary">Log in</button>
            </form>
        </div>
    </div>
</body>
</html>
