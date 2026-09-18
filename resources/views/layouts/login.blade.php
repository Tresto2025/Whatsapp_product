<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="robots" content="noindex, nofollow">
        <title>Tatkal Doctor </title>
        <link rel="stylesheet" href="{{ asset('admin') }}/assets/vendors/mdi/css/materialdesignicons.min.css">
        <link rel="stylesheet" href="{{ asset('admin') }}/assets/vendors/flag-icon-css/css/flag-icon.min.css">
        <link rel="stylesheet" href="{{ asset('admin') }}/assets/vendors/bootstrap-material-design/css/bootstrap-material-design.min.css">
        <link rel="stylesheet" href="{{ asset('admin') }}/assets/vendors/css/vendor.bundle.base.css">
        <link rel="stylesheet" href="{{ asset('admin') }}/assets/css/demo_1/style.css">
        <link rel="shortcut icon" href="{{ asset('uploads') }}/logo/favicon.png" />
        <style>
        .invalid-feedback, .font-medium.text-sm.text-green-600.mb-4 {
  color: #fc5a5a;
}
.form-group input {
  color: #495057; /* normal text color */
}

.form-group input::placeholder {
  color: #495057; /* placeholder color */
  opacity: 1;     /* makes sure it's not faded (some browsers add opacity) */
}
        .auth form .form-group label {color: #000;}
            .btn-primary, .wizard > .actions a {
                background: #28bf96 !important;
                color: #fff !important;
            }
            .text-primary {
                color: #28bf96 !important;
                
            }
            .content-wrapper {
                background: #fff;
            }
            .auth .login-half-bg{
                background: url("https://tatkaldoctor.com/public/admin/images/1.jpg");
                background-size: cover;
                background-position: bottom;
            }
            .auth-form-transparent .form-control:active,.auth .auth-form-transparent .form-control:focus {
                border-color: #000 !important;
            }
            .form-check .form-check-label input{
                opacity: 1;
            }
            
            
            /*.content-wrapper {
                background: #0059b5;
            }
            ::placeholder {
              color: #fff !important;
              opacity: 1;
            }
            
            ::-ms-input-placeholder {
              color: #fff !important;
            }
            h4, h6,label,.text-primary {
                color: #fff !important;
                
            }
            .auth-form-transparent .form-control:active {
                border-color: #fff !important;
            }
            .auth .login-half-bg{
                background: url("https://tatkaldoctor.com/public/admin/images/abstract_blue_3.jpg");
            }*/
        </style>
    </head>
    <body class="sidebar-fixed">
        @yield('content')
        <script src="{{ asset('admin') }}/assets/vendors/js/vendor.bundle.base.js"></script>
        <script src="{{ asset('admin') }}/assets/vendors/bootstrap-material-design/js/bootstrap-material-design.min.js"></script>
        <script src="{{ asset('admin') }}/assets/js/off-canvas.js"></script>
        <script src="{{ asset('admin') }}/assets/js/hoverable-collapse.js"></script>
        <script src="{{ asset('admin') }}/assets/js/misc.js"></script>
        <script src="{{ asset('admin') }}/assets/js/settings.js"></script>
        <script src="{{ asset('admin') }}/assets/js/todolist.js"></script>
        <script>
            window.addEventListener('pageshow', function(event) {
                if (event.persisted) {
                    // The page was restored from cache, so reload it
                    window.location.reload();
                }
            });
        </script>
    </body>
</html>
