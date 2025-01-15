<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('/css/login.css')}}"></head>
<body>
    <div class="login_form">
        <h3>Forgot Password</h3>
        <div class="logo">
            <!-- You can place your logo here -->
            {{-- <img src="logo.png" alt="Logo"> --}}
        </div>
        @if (session('status'))
            <div class="alert alert-success" role="alert">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}">
            @csrf
            <div class="input_box">
                <label for="email">Email address</label>
                <input type="email" id="email" name="email" required>
                @error('email')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
            <button type="submit">Send Password Reset Link</button>
        </form>
    </div>
</body>
</html>
