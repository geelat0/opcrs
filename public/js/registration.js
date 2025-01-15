// CAPITALIZATION OF EVERY WORD IN AN INPUT FIELD
// /public/js/main.js
$(document).ready(function() {
    // Function to capitalize the first letter of every word in a string
    function capitalizeFirstLetterOfEveryWord(str) {
        return str.replace(/\b\w/g, function(char) {
            return char.toUpperCase();
        });
    }

    // Add event listener for input field changes on elements with class 'capitalize-every-word'
    $('.capitalize').on('input', function() {
        let inputValue = $(this).val();
        let capitalizedValue = capitalizeFirstLetterOfEveryWord(inputValue);
        $(this).val(capitalizedValue);
    });
});



// REGISTRATION FORM VALIDATION
// /public/js/main.js
$(document).ready(function() {
    $('#registrationForm').submit(function(event) {
        // Prevent form submission
        event.preventDefault();

        // Reset any existing validation errors
        $('.form-control').removeClass('is-invalid');
        $('.invalid-feedback').text('');

        // Validate form fields
        let isValid = true;

        // Validate First Name field
        let firstName = $('#firstName').val().trim();
        if (firstName === '') {
            $('#firstName').addClass('is-invalid');
            $('#firstNameError').text('First Name is required.');
            isValid = false;
        }

        // Validate Middle Name field
        let middleName = $('#middleName').val().trim();
        if (middleName === '') {
            $('#middleName').addClass('is-invalid');
            $('#middleNameError').text('Middle Name is required.');
            isValid = false;
        }

        // Validate Last Name field
        let lastName = $('#lastName').val().trim();
        if (lastName === '') {
            $('#lastName').addClass('is-invalid');
            $('#lastNameError').text('Last Name is required.');
            isValid = false;
        }

        // Validate College Required field
        let province = $('#province').val();
        if (province === '') {
            $('#province').addClass('is-invalid');
            $('#collegeError').text('Please select your Province.');
            isValid = false;
        }

        let role = $('#rolesDropdown').val();
        if (role === '') {
            $('#rolesDropdown').addClass('is-invalid');
            $('#RoleError').text('Please select a role.');
            isValid = false;
        }

        // // Validate Department Required field
        // let department = $('#department').val();
        // if (department === '') {
        //     $('#department').addClass('is-invalid');
        //     $('#departmentError').text('Please select your Department.');
        //     isValid = false;
        // }

        // Validate position field
        let position = $('#position').val().trim();
        if (position === '') {
            $('#position').addClass('is-invalid');
            $('#positionError').text('Position is required.');
            isValid = false;
        }

        // Validate employee ID Numer field
        // let idNum = $('#idNum').val().trim();
        // if (idNum === '') {
        //     $('#idNum').addClass('is-invalid');
        //     $('#IdNumError').text('Position is required.');
        //     isValid = false;
        // }


        // Validate PH Contact Number field
        let contactNumber = $('#contactNumber').val().trim();
        if (contactNumber === '') {
            $('#contactNumber').addClass('is-invalid');
            $('#contactNumberError').text('PH Contact Number is required.');
            isValid = false;
        } else if (!isValidPhoneNumber(contactNumber)) {
            $('#contactNumber').addClass('is-invalid');
            $('#contactNumberError').text('Enter a valid PH Contact Number.');
            isValid = false;
        }

        // Validate Email field
        let email = $('#email').val().trim();
        if (email === '') {
            $('#email').addClass('is-invalid');
            $('#emailError').text('Email is required.');
            isValid = false;
        } else if (!isValidEmail(email)) {
            $('#email').addClass('is-invalid');
            $('#emailError').text('Enter a valid email address.');
            isValid = false;
        }

        // Validate Password field
        let password = $('#password').val();
        if (password === '') {
            $('#password').addClass('is-invalid');
            $('#passwordError').text('Password is required.');
            isValid = false;
        } else if (password.length < 6) {
            $('#password').addClass('is-invalid');
            $('#passwordError').text('Password must be at least 6 characters.');
            isValid = false;
        }

        // Validate Confirm Password field
        let confirmPassword = $('#confirmPassword').val();
        if (confirmPassword === '') {
            $('#confirmPassword').addClass('is-invalid');
            $('#confirmPasswordError').text('Confirm Password is required.');
            isValid = false;
        } else if (confirmPassword !== password) {
            $('#confirmPassword').addClass('is-invalid');
            $('#confirmPasswordError').text('Passwords do not match.');
            isValid = false;
        }

        // If form is valid, submit it
        if (isValid) {
            $.ajax({
                url: $(this).attr('action'),
                method: $(this).attr('method'),
                data: $(this).serialize(),
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: response.message, // Assuming your API returns a message
                            showConfirmButton: true,
                        }).then(function() {
                            // Redirect to the desired URL
                            window.location.href = '/'; // Replace with your desired URL
                        });
                    }else{
                        var errors = response.errors;
                        var errorMessage = '';
                        Object.keys(errors).forEach(function(key) {
                            errorMessage += key + ': ' + errors[key][0] + '<br>';
                        });
                        Swal.fire({
                            icon: 'error',
                            title: 'Registration Failed',
                            html: errorMessage
                        });
                    }
                    
                   
                },
                error: function(xhr, status, error) {
                    // Handle errors if any
                    console.error(xhr.responseText);
                    // You can show an error message here if needed
                }
            });
        }
    });

    // Function to validate email format
    function isValidEmail(email) {
        // Basic email validation regex
        let emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }

    // Function to validate PH Contact Number format (simple check)
    function isValidPhoneNumber(phoneNumber) {
        // Basic PH contact number validation (example)
        let phoneRegex = /^\d{11}$/; // Change as per your validation rules
        return phoneRegex.test(phoneNumber);
    }

});




