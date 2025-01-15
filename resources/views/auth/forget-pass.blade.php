<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('/css/login.css')}}">

</head>
<body>
    <div class="login_form">
        <h3>Reset Password</h3>
        <div class="separator">
            <span></span>
        </div>
        <form method="POST" action="{{ route('password.update') }}">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">

            <div class="input_box">
                <label for="email">Email address</label>
                <input type="email" id="email" name="email" required>
                @error('email')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="input_box">
                <div class="password_title">
                    <label for="password">Password</label>
                </div>
                <input type="password" id="password" name="password" required>
                @error('password')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="input_box">
                <div class="password_title">
                    <label for="password_confirmation">Confirm Password</label>
                </div>
                <input type="password" id="password_confirmation" name="password_confirmation" required>
                @error('password_confirmation')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <button type="submit">Reset Password</button>
        </form>
    </div>
  
</body>
</html>
