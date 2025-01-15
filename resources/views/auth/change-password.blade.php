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
        <h3>Change Password</h3>
        <div class="separator">
            <span></span>
        </div>
        <form id="changePasswordForm">
            @csrf
            <input type="hidden" name="user_id" value="{{ $user->id }}">
            <div class="form-group">
                <label for="current_password">Current Password</label>
                <input type="password" class="form-control" id="current_password" name="current_password">
                <div id="current_passwordError" class="invalid-feedback"></div>
            </div>
            <div class="form-group">
                <label for="new_password">New Password</label>
                <input type="password" class="form-control" id="new_password" name="new_password">
                <div id="new_passwordError" class="invalid-feedback"></div>
            </div>
            <div class="form-group">
                <label for="new_password_confirmation">Confirm New Password</label>
                <input type="password" class="form-control" id="new_password_confirmation" name="new_password_confirmation">
                <div id="new_password_confirmationError" class="invalid-feedback"></div>
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

    $('#changePasswordForm').on('submit', function(e) {
            e.preventDefault();
            var formData = $(this).serialize();

            $.ajax({
                url: '{{ route('password.change') }}', 
                method: 'POST',
                data: formData,
                success: function(response) {
                    
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: response.message,
                            showConfirmButton: true,
                        });
                        $('#changePasswordForm')[0].reset();
                        window.location.href = '/dash-home';
                    } else {
                        var errors = response.errors;
                        Object.keys(errors).forEach(function(key) {
                            var inputField = $('#changePasswordForm [name=' + key + ']');
                            inputField.addClass('is-invalid');
                            $('#changePasswordForm #' + key + 'Error').text(errors[key][0]);
                        });
                    }
                },
                error: function(xhr) {
                    console.log(xhr.responseText);
                }
            });
        });
});
</script>
