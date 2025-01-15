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
        <h3>One Time Password</h3>
        <div class="separator">
            <span></span>
        </div>
        <form id="OTPCheckForm">
            @csrf
            <div class="input_box">
                <input id="otp"
                        type="number" 
                        min="0" 
                        max="999999" 
                        step="1"
                        class="form-control{{ $errors->has('otp') ? ' is-invalid' : '' }}"
                        autocomplete="off"
                        name="otp" 
                        value=""  autofocus>
            </div>
            <button type="submit">Submit</button>
        </form>
    </div>
  
</body>
</html>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('js/registration.js') }}"></script>

<script>
    $(document).ready(function() {
    
        $('#OTPCheckForm').on('submit', function(e) {
                e.preventDefault();
                var formData = $(this).serialize();
    
                $.ajax({
                    url: '{{ route('auth.otp.check') }}', 
                    method: 'POST',
                    data: formData,
                    success: function(response) {
                        
                        if (response.success) {
                            window.location.href = response.redirect;
                           
                            $('#OTPCheckForm')[0].reset();
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Oh no!',
                                text: response.message,
                                showConfirmButton: true,
                            });
                        }
                    },
                    error: function(xhr) {
                        Swal.fire({
                                icon: 'error',
                                title: 'Oh no!',
                                text: 'Something went wrong.',
                                showConfirmButton: true,
                            });
                        console.log(xhr.responseText);
                    }
                });
            });
    });
</script>
