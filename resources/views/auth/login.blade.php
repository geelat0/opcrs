<!doctype html>

<html
  lang="en"
  class="light-style layout-wide customizer-hide"
  dir="ltr"
  data-theme="theme-default"
  data-assets-path="../../assets/"
  data-template="vertical-menu-template">
  <head>
    <meta charset="utf-8" />
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>OPCR</title>

    <meta name="description" content="" />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="../../assets/img/favicon/favicon.ico" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
      rel="stylesheet" />

    <!-- Icons -->
    <link rel="stylesheet" href="../../assets/vendor/fonts/boxicons.css" />
    <link rel="stylesheet" href="../../assets/vendor/fonts/fontawesome.css" />
    <link rel="stylesheet" href="../../assets/vendor/fonts/flag-icons.css" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="../../assets/vendor/css/rtl/core.css" class="template-customizer-core-css" />
    <link rel="stylesheet" href="../../assets/vendor/css/rtl/theme-default.css" class="template-customizer-theme-css" />
    <link rel="stylesheet" href="../../assets/css/demo.css" />

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="../../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />
    <link rel="stylesheet" href="../../assets/vendor/libs/typeahead-js/typeahead.css" />
    <!-- Vendor -->
    <link rel="stylesheet" href="../../assets/vendor/libs/@form-validation/form-validation.css" />

    <!-- Page CSS -->
    <!-- Page -->
    <link rel="stylesheet" href="../../assets/vendor/css/pages/page-auth.css" />

    <!-- Helpers -->
    <script src="../../assets/vendor/js/helpers.js"></script>
    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->
    <!--? Template customizer: To hide customizer set displayCustomizer value false in config.js.  -->
    <script src="../../assets/vendor/js/template-customizer.js"></script>
    <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
    <script src="../../assets/js/config.js"></script>
  </head>

<body>
    <!-- Content -->

    <div class="authentication-wrapper authentication-cover">
      <div class="authentication-inner row m-0">
        <!-- /Left Text -->
        {{-- <div class="d-none d-lg-flex col-lg-7 col-xl-8 align-items-center p-5" style="background-color: #f1f1f1">
          <div class="w-100 d-flex justify-content-center">
            <img
              src="../../assets/img/illustrations/opcr_login_wall.jpg"
              class="img-fluid"
              alt="Login image"
              width="700"
              data-app-dark-img="illustrations/opcr_login_wall.png"
              data-app-light-img="illustrations/opcr_login_wall.png" />
          </div>
        </div> --}}
        <div class="d-none d-lg-flex col-lg-7 col-xl-8 d-flex bg-primary"
            style=" background-image: url('{{ asset('assets/img/illustrations/opcr_login_wall4.png') }}');
                    background-size: cover;
                    background-position: center;
                    background-repeat: no-repeat;">
           <div class="w-100 d-flex justify-content-center">
               <div class="d-flex justify-content-center align-items-center">
                   <!-- You can add other content here if needed -->
               </div>
           </div>
       </div>
        <!-- /Left Text -->

        <!-- Login -->
        <div class="d-flex col-12 col-lg-5 col-xl-4 align-items-center authentication-bg p-sm-5 p-4">
          <div class="w-px-400 mx-auto">
            <!-- Logo -->
            <div class="app-brand mb-3">
              <a href="index.html" class="app-brand-link gap-2">
                <img src="https://upload.wikimedia.org/wikipedia/commons/3/39/Department_of_Labor_and_Employment_%28DOLE%29.svg" class="logo-container" alt="Login" style="width: 10%;">
                <span class="app-brand-text demo text-body fw-bold text-uppercase">OPCR</span>
              </a>
            </div>
            <!-- /Logo -->
            <h4 class="mb-2">Welcome to Office Program Commitment Review</h4>
            <p class="mb-4">Please sign-in to your account to track your opcr</p>

            <form id="loginForm" class="mb-3">
                @csrf
              <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="text" class="form-control" id="email" name="email" placeholder="@gmail.com" autofocus />
                <div class="invalid-feedback" id="emailError"></div>
              </div>
              <div class="mb-3 form-password-toggle">
                <div class="d-flex justify-content-between">
                  <label class="form-label" for="password">Password</label>
                  <a href="/forgot-password">
                    <small>Forgot Password?</small>
                  </a>
                </div>
                <div class="input-group input-group-merge">
                  <input
                    type="password"
                    id="password"
                    class="form-control"
                    name="password"
                    placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                    aria-describedby="password" />
                  <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
                  <div class="invalid-feedback" id="passwordError"></div>
                </div>
              </div>
              <div class="mb-3">
                {{-- <div class="form-check">
                  <input class="form-check-input" type="checkbox" id="remember-me" name="remember"/>
                  <label class="form-check-label" for="remember-me"> Remember Me </label>
                </div> --}}
              </div>
              <button type="submit" class="btn btn-primary d-grid w-100">Sign in</button>
            </form>

          </div>
        </div>
        <!-- /Login -->
      </div>
    </div>

    <!-- / Content -->

    <!-- Core JS -->
    <!-- build:js assets/vendor/js/core.js -->

    <script src="../../assets/vendor/libs/jquery/jquery.js"></script>
    <script src="../../assets/vendor/libs/popper/popper.js"></script>
    <script src="../../assets/vendor/js/bootstrap.js"></script>
    <script src="../../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
    <script src="../../assets/vendor/libs/hammer/hammer.js"></script>
    <script src="../../assets/vendor/libs/i18n/i18n.js"></script>
    <script src="../../assets/vendor/libs/typeahead-js/typeahead.js"></script>
    <script src="../../assets/vendor/js/menu.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>



    <!-- endbuild -->

    <!-- Vendors JS -->
    <script src="../../assets/vendor/libs/@form-validation/popular.js"></script>
    <script src="../../assets/vendor/libs/@form-validation/bootstrap5.js"></script>
    <script src="../../assets/vendor/libs/@form-validation/auto-focus.js"></script>

    <!-- Main JS -->
    <script src="../../assets/js/main.js"></script>

    <!-- Page JS -->
    <script src="../../assets/js/pages-auth.js"></script>

  <script>
    $('#loginForm').on('submit', function(event) {
      event.preventDefault();
      $.ajax({
        url: '{{ route('login') }}',
        method: 'POST',
        data: {
          email: $('#email').val(),
          password: $('#password').val(),
          // remember: $('#remember-me').is(':checked') ? 1 : 0,
          _token: '{{ csrf_token() }}'
        },
        success: function(response) {
              if (response.success) {
                window.location.href = response.redirect;
              } else {
                if(response.message){
                    Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: response.message,
                    showConfirmButton: true,
                });

                }else{

                    var errors = response.errors;
                    Object.keys(errors).forEach(function(key) {
                        var inputField = $('#loginForm [name=' + key + ']');
                        inputField.addClass('is-invalid');
                        $('#loginForm #' + key + 'Error').text(errors[key][0]);
                    });

                }



              }
            },
            error: function() {
              Swal.fire({
                icon: 'error',
                title: 'Failed!',
                text: 'Something went wrong.',
                showConfirmButton: true,
              });
            }
          });
        });

        $('#loginForm').find('input, select').on('keyup change', function() {
            $(this).removeClass('is-invalid');
            var errorId = $(this).attr('name') + 'Error';
            $('#' + errorId).text('');
        });
  </script>
</body>
</html>
