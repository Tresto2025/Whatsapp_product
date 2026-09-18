<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Appointment | Dashboard</title>
    <!-- plugins:css -->
    <link rel="stylesheet" href="{{ asset('admin') }}/assets/vendors/mdi/css/materialdesignicons.min.css">
    <link rel="stylesheet" href="{{ asset('admin') }}/assets/vendors/flag-icon-css/css/flag-icon.min.css">
    <link rel="stylesheet" href="{{ asset('admin') }}/assets/vendors/bootstrap-material-design/css/bootstrap-material-design.min.css">
    <link rel="stylesheet" href="{{ asset('admin') }}/assets/vendors/css/vendor.bundle.base.css">
    <!-- endinject -->
    <!-- Plugin css for this page -->
    <link rel="stylesheet" href="{{ asset('admin') }}/assets/vendors/font-awesome/css/font-awesome.min.css" />
    <link rel="stylesheet" href="{{ asset('admin') }}/assets/vendors/bootstrap-datepicker/bootstrap-datepicker.min.css">
    <link rel="stylesheet" href="{{ asset('admin') }}/assets/vendors/jquery-bar-rating/css-stars.css">
    <link rel="stylesheet" href="{{ asset('admin') }}/assets/vendors/jvectormap/jquery-jvectormap.css">
    <link rel="stylesheet" href="{{ asset('admin') }}/assets/vendors/font-awesome/css/font-awesome.min.css" />
    
    <link rel="stylesheet" href="{{ asset('admin') }}/assets/vendors/select2/select2.min.css">
    <link rel="stylesheet" href="{{ asset('admin') }}/assets/vendors/select2-bootstrap-theme/select2-bootstrap.min.css">
    <!-- End plugin css for this page -->
    <!-- inject:css -->
    <!-- endinject -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <!-- Layout styles -->
    <link rel="stylesheet" href="{{ asset('admin') }}/assets/css/demo_1/style.css">
    <!-- End layout styles -->
    <link rel="shortcut icon" href="{{ asset('uploads') }}/logo/favicon.png" />
    
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <style>
.service_placeholder::placeholder {
    font-size: 14px !important;
}
</style>
    <style>
        .hint_pass {font-size: 12px;color: #4f4e4e;}
        .border-primary {border-color: #28bf96 !important;}
        .content-wrapper::after {
            content: "";position: fixed;bottom: -100px;right: 20px;width: 300px;height: 300px;
            background: url('https://coco.thebigflame.in/logo/coco-logo4.png') no-repeat center center;
            background-size: contain;opacity: 0.1;pointer-events: none;z-index: 1;
        }
        .text-danger {color: #fc5a5a !important;}
        .badge-danger{background-color: #fc5a5a;border: 1px solid #fc5a5a;}
        .badge-info, .preview-list .preview-item .preview-thumbnail .badge.badge-offline{
            border: 1px solid #04a9f6;background-color: #04a9f6;
        }
        .text-success {color: #00b67a !important;}
        .badge-success, .preview-list .preview-item .preview-thumbnail .badge.badge-online {
            border: 1px solid #00b67a;color: #ffffff;
        }
        .badge-success, .preview-list .preview-item .preview-thumbnail .badge.badge-online {
            color: #fff;background-color: #00b67a;
        }
        .mdc-card.info-card.info-card--danger .card-inner .card-icon-wrapper {
            background-color: #f00;
            -webkit-box-shadow: 0 0 10px 5px rgba(252, 90, 90, 0.35);
            box-shadow: 0 0 10px 5px rgba(253, 4, 4, 0.35);
        }
        .mdc-card.info-card.info-card--primary .card-inner .card-icon-wrapper {
            background-color: #00b4d8;
            -webkit-box-shadow: 0 0 10px 5px rgba(122, 0, 255, 0.35);
            box-shadow: 0 0 10px 5px rgba(58, 20, 191, 0.35);
        }
        .sidebar .nav .nav-item.active > .nav-link {background: unset;}
        .sidebar .nav .nav-item.active > .nav-link i.menu-icon {color: #000;}
        .sidebar .nav .nav-item.active > .nav-link .menu-title {color: #000;}
        .sidebar .nav .nav-item .nav-link {padding: 0.6rem 0.75rem;}
        [class*=" bmd-label"], [class^="bmd-label"] {color: unset;}
        .form-group {margin-bottom: 0.5rem;}
        .bmd-form-group {position: relative;padding-top: 1.75rem;}
        textarea.form-control{background-image: linear-gradient(0deg,#253237 2px,rgba(0,150,136,0) 0),linear-gradient(0deg,rgba(0,0,0,.26) 0.5px,transparent 0);}
        .form-control{font-size: 1rem;}
        .navbar .navbar-brand-wrapper .navbar-brand img{width: 70%;max-width: 100%;height: unset;}
        .table thead th{color: #253237;}
        .table, .jsgrid .jsgrid-table {
  color: #253237;
}
        /*.sidebar {background: linear-gradient(#253237, #253237) !important;}*/
        .sidebar {background: linear-gradient(#28bf96, #28bf96) !important}
        /*.navbar .navbar-brand-wrapper{background: #253237;}*/
        .navbar .navbar-brand-wrapper{background: #fff;}
        .form-sample {margin-bottom: 0;}
        td i {font-size: 20px !important;text-align: left;color: #253237;}
        .custom-file-control, .form-control, 
        .is-focused .custom-file-control, 
        .is-focused .form-control {background-image: linear-gradient(0deg,#253237 2px,rgba(0,150,136,0) 0),linear-gradient(0deg,rgba(0,0,0,.26) 1px,transparent 0);}
        .is-focused [class*=" bmd-label"], .is-focused [class^="bmd-label"]{color: #253237;}
        .btn-primary, .wizard > .actions a{background: #28bf96 !important;color: #fff !important;}
        .btn-secondary {background: #28bf96 !important;color: #fff !important;}
        .fa.fa-edit, .fa.fa-copy {position: relative;top: 1px;color: #02408c;}
        .fa.fa-trash-o {position: relative;top: -0.5px;color: #fc5a5a;}
        input.form-control, 
        .asColorPicker-input, 
        .dataTables_wrapper select, 
        .jsgrid .jsgrid-table .jsgrid-filter-row input[type="text"], 
        .jsgrid .jsgrid-table .jsgrid-filter-row select, 
        .jsgrid .jsgrid-table .jsgrid-filter-row input[type="number"],.select2-search__field, 
        .typeahead, .tt-query, .tt-hint{padding: 0.94rem 0 0 0;}
        .navbar .top-navbar-title {color: #253237;}
        .navbar .navbar-menu-wrapper .navbar-toggler {color: #253237;}
        .mdi.mdi-settings.rotating, .mdi.mdi-bell, .mdi.mdi-email, .text-black {color: #253237;}
        .custom-tooltip{padding-top: 5px;}
        .fa.fa-search,.fa.fa-eye {color: #000;}
        div:where(.swal2-container) div:where(.swal2-popup){padding: 1.25em 1.25em;}
        .font-weight-normal {font-weight: bold !important;color: #28bf96;}
        .table th, .jsgrid .jsgrid-table th, 
        .table td, .jsgrid .jsgrid-table td {font-size: 14px;}
        .bmd-form-group .bmd-label-static, 
        .bmd-form-group.is-filled .bmd-label-floating, 
        .bmd-form-group .is-filled .bmd-label-floating, 
        .bmd-form-group.is-focused .bmd-label-floating, 
        .bmd-form-group .is-focused .bmd-label-floating {font-size: 15px;}
        .form-group label {font-size: 14px;color:#000;}
        .bmd-form-group .bmd-label-static, 
        .bmd-form-group.is-filled .bmd-label-floating, 
        .bmd-form-group .is-filled .bmd-label-floating, 
        .bmd-form-group.is-focused .bmd-label-floating, 
        .bmd-form-group .is-focused .bmd-label-floating {
            color: #000;
        }
        .form-control[readonly]{background-color: transparent;}
        .url-input{overflow-wrap: break-word;white-space: normal;display: inline-block;width: 200px;text-decoration: underline;}
        .url-input.review-url{width: 400px;}
        .with-white{color: #fff !important;}
        .card .card-body {padding: 15px 15px;}
        .text-primary, .list-wrapper .completed .remove {color: #0340f7 !important;}
        .text-info {color: #253237 !important;}
        .mdc-card.info-card.info-card--info .card-inner .card-icon-wrapper{background-color: #28bf96;box-shadow: 0 0 10px 5px rgba(49, 179, 124, 0.77);}
        .sidebar-fixed .sidebar .nav{max-height: calc(65vh - 0px);}
        select.form-control, 
        select.asColorPicker-input, 
        .dataTables_wrapper select, 
        .jsgrid .jsgrid-table 
        .jsgrid-filter-row select, 
        .select2-container--default select.select2-selection--single, 
        .select2-container--default .select2-selection--single select.select2-search__field, 
        select.typeahead, 
        select.tt-query, 
        select.tt-hint,
        textarea.form-control{
  padding: 1.40rem 0;
        }
        .custom-apointment-btn {
  display: grid;
  text-align: center;
  gap: 10px;
}
.cancel-btn {
  background-color: #fc5a5a;
  border-radius: 25px;
  text-align: center;
  color: #fff;
  padding: 3px 0px;
}
.reschedule-btn {
  background-color: #ef7f1a;
  border-radius: 25px;
  text-align: center;
  color: #fff;
  padding: 3px 0px;
}
.missing_btn {
  background-color: #fc5a5a;
  border-radius: 25px;
  text-align: center;
  color: #fff;
  padding: 3px 0px;
}
.check_in_btn{
    background-color: #00b67a;
    border-radius: 25px;
    text-align: center;
    color: #fff;
    padding: 3px 0px;
}
.view-btn {
  background-color: #02408c;
  border-radius: 25px;
  text-align: center;
  color: #fff;
  padding: 3px 0px;
}
.cancel-btn:hover, .reschedule-btn:hover, .dashboard-a-tag:hover, .view-btn:hover {
  color: #fff;
  text-decoration: unset;
}

/* Overlay effect similar to avgrund */
.modal-backdrop.show {
    backdrop-filter: blur(4px);
    background: rgba(0,0,0,0.6) !important;
}

/* Modal scaling animation */
.modal.fade .modal-dialog {
    transform: scale(0.7);
    opacity: 0;
    transition: all 0.3s ease-in-out;
}

.modal.fade.show .modal-dialog {
    transform: scale(1);
    opacity: 1;
}

/* Rounded glassy style */
.modal-content {
    border-radius: 20px;
    box-shadow: 0 15px 40px rgba(0,0,0,0.4);
    border: none;
    background: #fff;
    overflow: hidden;
}

/* Header */
.modal-header {
    background: linear-gradient(135deg, #4a90e2, #357abd);
    color: white;
    border-bottom: none;
}

/* Close button */
.modal-header .btn-close {
    filter: invert(1);
}

/* Footer buttons */
.modal-footer .btn {
    border-radius: 12px;
    padding: 8px 16px;
    font-weight: 500;
}

   .mdc-card.info-card .card-inner .card-icon-wrapper i {
        font-size: 30px !important;
    }
    .center-content{
        justify-content: center;
    }
    .mdc-card.info-card{
        padding: 25px 25px;
    }
    
    /* Toggle Switch Style */
.switch {
  position: relative;
  display: inline-block;
  width: 46px;
  height: 24px;
}
.switch input {
  opacity: 0;
  width: 0;
  height: 0;
}
.slider {
  position: absolute;
  cursor: pointer;
  top: 0; left: 0; right: 0; bottom: 0;
  background-color: #ccc;
  transition: .4s;
  border-radius: 24px;
}
.slider:before {
  position: absolute;
  content: "";
  height: 18px;
  width: 18px;
  left: 3px;
  bottom: 3px;
  background-color: white;
  transition: .4s;
  border-radius: 50%;
}
input:checked + .slider {
  background-color: #4CAF50; /* green */
}
input:checked + .slider:before {
  transform: translateX(22px);
}
.slider.round {
  border-radius: 24px;
}
  .footer .justify-content-sm-between {
    justify-content: end !important;
  }
  select.form-control, select.asColorPicker-input, .dataTables_wrapper select, .jsgrid .jsgrid-table .jsgrid-filter-row select, .select2-container--default select.select2-selection--single, .select2-container--default .select2-selection--single select.select2-search__field, select.typeahead, select.tt-query, select.tt-hint, textarea.form-control {
    padding: 1.40rem 0 0.0rem 0;
}
/*.custom_setting_dropdown:hover .dropdown-menu {
  display: block;
  margin-top: 0; 
}*/
.navbar .navbar-menu-wrapper .navbar-nav .nav-item.dropdown .dropdown-menu.navbar-dropdown {
  background: linear-gradient(#28bf96, #28bf96) !important;
}
.dropdown-item.preview-item:hover {
  background-color: unset;
}
.preview-thumbnail i, .preview-list .preview-item .preview-item-content h6{
	color: #fff !important;
}
.razorpay-payment-button {
  background: #ef7f19 !important;
  color: #fff !important;
  border: none;
  font-size: 12px;
  line-height: 1;
  font-family: "Roboto-bold", sans-serif;
  font-weight: 600;
  box-shadow: none;
  display: inline-block;
  text-align: center;
  vertical-align: middle;
  padding: 0.775rem 0.75rem;
  border-radius: 0.1875rem;
  transition: color 0.15s ease-in-out, background-color 0.15s ease-in-out, border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out, -webkit-box-shadow 0.15s ease-in-out;
  text-decoration: none;
  text-transform: uppercase;
  letter-spacing: 0;
}
.select2-container .select2-selection--multiple {min-height: 35px;}
.select2-container {top: 10px;}

    </style>
    <style>
        .ck-editor__editable_inline {
    min-height: 400px;
}
    </style>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCFpI3IuqWxHr4rz87uT-7HO-ZIS-f1I7Y&v=beta&libraries=places"></script>
  </head>
  <body class="sidebar-fixed">
    <div class="container-scroller">
      <!-- partial:partials/_navbar.html -->
      <nav class="navbar default-layout-navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row">
        <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-center">
          <a class="navbar-brand brand-logo" href="#" style="text-align: center;color: #fff !important;float: unset;box-shadow: unset !important;margin-right: 0;">
              <img src="{{ asset('uploads') }}/logo/logo.png" alt="logo" />
            </a>
          <a class="navbar-brand brand-logo-mini" href="#">A</a>
        </div>
        <div class="navbar-menu-wrapper d-flex align-items-center">
          <button class="navbar-toggler align-self-center" type="button" data-toggle="minimize">
            <span class="mdi mdi-menu"></span>
          </button>
          <h4 class="top-navbar-title d-none d-lg-block">Greetings Admin!</h4>
          <ul class="navbar-nav navbar-nav-right custom_setting-hover">
            @auth
            @if(Auth::user()->role == 2)
                <li class="nav-item d-flex align-items-center mr-3">
                    <span class="mr-2 font-weight-bold">Booking<span id="toggleStatus" class="ml-2">{{ Auth::user()->booking_enabled ? 'Enabled' : 'Disabled' }}</span></span>
                    <label class="switch mb-0">
                        <input type="checkbox" id="bookingToggle" 
                               data-id="{{ Auth::user()->id }}" 
                               {{ Auth::user()->booking_enabled ? 'checked' : '' }}>
                        <span class="slider round"></span>
                    </label>
                </li>
            @endif
            @endauth
            <li class="nav-item dropdown custom_setting_dropdown">
              <a class="nav-link count-indicator dropdown-toggle" id="messageDropdown" href="#" data-toggle="dropdown" aria-expanded="false">
                <i class="mdi mdi-settings"></i>
              </a>
              <div class="dropdown-menu dropdown-menu-right navbar-dropdown preview-list" aria-labelledby="messageDropdown">
                <div class="dropdown-divider"></div>
                @php
                $id = Auth::user()->id;
                @endphp
                @if($id == 1)
                <a class="dropdown-item preview-item" href="{{ route('admin-password') }}">
                  <div class="preview-thumbnail">
                    <i class="mdi mdi-account-key ml-1 text-primary"></i>
                  </div>
                  <div class="preview-item-content d-flex align-items-start flex-column justify-content-center">
                    <h6 class="preview-subject ellipsis mb-1 font-weight-normal">Change Password</h6>
                  </div>
                </a>
                @else
                <a class="dropdown-item preview-item" href="{{ route('doctor-my-profile') }}">
                  <div class="preview-thumbnail">
                    <i class="mdi mdi-account-settings ml-1 text-primary"></i>
                  </div>
                  <div class="preview-item-content d-flex align-items-start flex-column justify-content-center">
                    <h6 class="preview-subject ellipsis mb-1 font-weight-normal">Update Profile</h6>
                  </div>
                </a>
                <a class="dropdown-item preview-item" href="{{ route('doctor-my-password') }}">
                  <div class="preview-thumbnail">
                    <i class="mdi mdi-account-key ml-1 text-primary"></i>
                  </div>
                  <div class="preview-item-content d-flex align-items-start flex-column justify-content-center">
                    <h6 class="preview-subject ellipsis mb-1 font-weight-normal">Change Password</h6>
                  </div>
                </a>
                @endif
                
                <div class="dropdown-divider"></div>
                <a class="dropdown-item preview-item" href="{{ route('user-logout') }}">
                  <div class="preview-thumbnail">
                    <i class="mdi mdi-logout text-primary"></i> </div>
                  <div class="preview-item-content d-flex align-items-start flex-column justify-content-center">
                    <h6 class="preview-subject ellipsis mb-1 font-weight-normal">Log Out</h6>
                  </div>
                </a>
              </div>
            </li>
          </ul>
          <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button" data-toggle="offcanvas">
            <span class="mdi mdi-menu"></span>
          </button>
        </div>
      </nav>
      <div class="container-fluid page-body-wrapper">
        @include('layouts.sidebar')
        @yield('content')
  <!-- partial:partials/_footer.html -->
          <footer class="footer">
            <div class="footer-inner-wraper">
              <div class="d-sm-flex justify-content-center justify-content-sm-between">
                <span class="text-muted text-center text-sm-left d-block d-sm-inline-block">Copyright © 2025 <a href="#">TatkalDoctor</a>. All rights reserved.</span>
              </div>
            </div>
          </footer>
          <!-- partial -->
        </div>
        <!-- main-panel ends -->
      </div>
      <!-- page-body-wrapper ends -->
    </div>
    <!-- container-scroller -->
    <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>

    <!-- plugins:js -->
    <script src="{{ asset('admin') }}/assets/vendors/js/vendor.bundle.base.js"></script>
    <script src="{{ asset('admin') }}/assets/vendors/bootstrap-material-design/js/bootstrap-material-design.min.js"></script>
    <!-- endinject -->
    <!-- Plugin js for this page -->
    <script src="{{ asset('admin') }}/assets/vendors/chart.js/Chart.min.js"></script>
    <script src="{{ asset('admin') }}/assets/vendors/jquery-circle-progress/js/circle-progress.min.js"></script>
    <script src="{{ asset('admin') }}/assets/vendors/jquery-bar-rating/jquery.barrating.min.js"></script>
    <script src="{{ asset('admin') }}/assets/vendors/jvectormap/jquery-jvectormap.min.js"></script>
    <script src="{{ asset('admin') }}/assets/vendors/jvectormap/jquery-jvectormap-world-mill-en.js"></script>
    <script src="{{ asset('admin') }}/assets/vendors/flot/jquery.flot.js"></script>
    <script src="{{ asset('admin') }}/assets/vendors/flot/jquery.flot.resize.js"></script>
    <script src="{{ asset('admin') }}/assets/vendors/flot/jquery.flot.categories.js"></script>
    <script src="{{ asset('admin') }}/assets/vendors/flot/jquery.flot.fillbetween.js"></script>
    <script src="{{ asset('admin') }}/assets/vendors/flot/jquery.flot.stack.js"></script>
    <script src="{{ asset('admin') }}/assets/vendors/flot/jquery.flot.pie.js"></script>
    <script src="{{ asset('admin') }}/assets/vendors/bootstrap-datepicker/bootstrap-datepicker.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>
    <!-- End plugin js for this page -->
    <!-- inject:js -->
    <script src="{{ asset('admin') }}/assets/js/off-canvas.js"></script>
    <script src="{{ asset('admin') }}/assets/js/hoverable-collapse.js"></script>
    <script src="{{ asset('admin') }}/assets/js/misc.js"></script>
    <script src="{{ asset('admin') }}/assets/js/settings.js"></script>
    <script src="{{ asset('admin') }}/assets/js/todolist.js"></script>
    <!-- endinject -->
    <!-- Custom js for this page -->
    <script src="{{ asset('admin') }}/assets/js/dashboard.js?5657657"></script>
    <!-- End custom js for this page -->
    <script src="{{ asset('admin') }}/assets/vendors/select2/select2.min.js"></script>
    <script src="{{ asset('admin') }}/assets/vendors/typeahead.js/typeahead.bundle.min.js"></script>
    <script src="{{ asset('admin') }}/assets/js/file-upload.js"></script>
    <script src="{{ asset('admin') }}/assets/js/typeahead.js"></script>
    <script src="{{ asset('admin') }}/assets/js/select2.js"></script>
    <script src="{{ asset('admin') }}/assets/js/tooltips.js"></script>
    <script src="{{ asset('admin') }}/assets/js/popover.js"></script>
    <script src="{{ asset('admin') }}/assets/js/clipboard.js"></script>
    
    <script src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
    <script>
        $('#doctors-dynamic-data').on('click', '.update-doctor-status', function() {
            $(this).toggleClass("fa-toggle-on fa-toggle-off");
            $(this).toggleClass("text-success text-danger");
            var oldText = $(this).closest('tr').find("td.active-inactive span").text();
            var newText = $(this).closest('tr').find("td.active-inactive span").data('text');
            $(this).closest('tr').find("td.active-inactive span").text(newText).data('text',oldText);
            var oldColor = $(this).closest('tr').find("td.active-inactive span").data('old');
            var newColor = $(this).closest('tr').find("td.active-inactive span").data('new');
            $(this).closest('tr').find("td.active-inactive span").removeClass(oldColor).addClass(newColor).data('old', newColor).data('new', oldColor);
            var valuedata = $(this).attr('data-id');
            $.ajax({
                data:{
                    '_token': '{{ csrf_token() }}',
                    'id':valuedata
                },
                url: '{{ route('update-doctor-status') }}',
                type: 'post',
                beforeSend: function() {
                },
                success: function(data) {
                },
                error: function(jqXHR, textStatus, errorThrown) {
                }
            })
        });
        /*delete Admin Doctor functionality*/
        $(document).on('click', '.delete-doctor-btn', function(e) {
            e.preventDefault();
            var id = $(this).data('id');
            Swal.fire({
                title: 'Are you sure?',
                text: "Do you want to delete this doctor?",
                showCancelButton: true,
                confirmButtonColor: '#fb6421',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    deleteAdminDoctor(id);
                }
            });
        });
        function deleteAdminDoctor(id) {
            $.ajax({
                url: '{{ route('doctor.destroy', '') }}' + '/' + id,
                type: 'GET',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    Swal.fire({
                        title: 'Deleted!',
                        text: 'Doctor has been deleted successfully.',
                        confirmButtonColor: '#fb6421'
                    }).then(() => {
                        window.location.href = '{{route('doctor.index')}}';
                    });
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.log('Error:', errorThrown);
                }
            });
        }
        
        $(function() {
        $('#appointments_date').daterangepicker({
            singleDatePicker: true,
            autoUpdateInput: false,
            locale: {
                format: 'YYYY-MM-DD',
                cancelLabel: 'Clear'
            }
        });

        // set date when selected
        $('#appointments_date').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('YYYY-MM-DD'));
            filterAppointments();
        });
    
        // clear on cancel
        $('#appointments_date').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
            filterAppointments();
        });
    
        // separate clear button
        $('#clearDate').on('click', function() {
            $('#appointments_date').val('');
        });
    });
        $(document).ready(function () {
            $('#start_time').daterangepicker({
                singleDatePicker: true,
                timePicker: true,
                timePicker24Hour: true,
                timePickerSeconds: false,
                showDropdowns: false,
                autoApply: true,
                opens: 'center',
                locale: {
                    format: 'HH:mm',
                    applyLabel: "OK",
                    cancelLabel: "Cancel",
                }
            }).on('show.daterangepicker', function (ev, picker) {
                picker.container.find(".calendar-table").hide();
                picker.container.find(".ranges").hide();
            });

            $('#end_time').daterangepicker({
                singleDatePicker: true,
                timePicker: true,
                timePicker24Hour: true,
                timePickerSeconds: false,
                showDropdowns: false, 
                autoApply: true, 
                opens: 'center', 
                locale: {
                    format: 'HH:mm',
                    applyLabel: "OK",
                    cancelLabel: "Cancel",
                }
            }).on('show.daterangepicker', function (ev, picker) {
                picker.container.find(".calendar-table").hide();
                picker.container.find(".ranges").hide();
            });
        });
        $(document).ready(function () {
            $('#break_start_time').daterangepicker({
                singleDatePicker: true,
                timePicker: true,
                timePicker24Hour: true,
                timePickerSeconds: false,
                showDropdowns: false,
                autoApply: true,
                opens: 'center',
                locale: {
                    format: 'HH:mm',
                    applyLabel: "OK",
                    cancelLabel: "Cancel",
                }
            }).on('show.daterangepicker', function (ev, picker) {
                picker.container.find(".calendar-table").hide();
                picker.container.find(".ranges").hide();
            });

            $('#break_end_time').daterangepicker({
                singleDatePicker: true,
                timePicker: true,
                timePicker24Hour: true,
                timePickerSeconds: false,
                showDropdowns: false, 
                autoApply: true, 
                opens: 'center', 
                locale: {
                    format: 'HH:mm',
                    applyLabel: "OK",
                    cancelLabel: "Cancel",
                }
            }).on('show.daterangepicker', function (ev, picker) {
                picker.container.find(".calendar-table").hide();
                picker.container.find(".ranges").hide();
            });
        });
        
        $('#doctors_search').keyup(function(){
            var search = $("#doctors_search").val();
            $.ajax({
                url: '{{ route('filter-doctors') }}',
                type: "GET",
                data: {
                    "search": search,
                },
                beforeSend:function(){
                    $('#loading').show();
                },
                complete:function(){
                    $('#loading').hide();
                },
                success:function(res){
                    $("#doctors-dynamic-data").html(res);
                    $('.clear-filter').show('slide');
                },
                error:function(jqXHR, ajaxOptions, thrownError){
                }
            });
        });
        
        $('#doctor_search').on('keyup', function() {
            let query = $(this).val();
            if (query.length === 0) {
                $('#doctor_id').val(''); // Clear doctor_id when input is empty
                $('#doctor_suggestions').remove();
            }
            if (query.length >= 2) {
                $.ajax({
                    url: "{{ route('doctor.search') }}",
                    data: { q: query },
                    success: function(data) {
                        let list = '';
                        data.forEach(function(doctor) {
                            list += `<div class="doctor-option" data-id="${doctor.id}">${doctor.name} (${doctor.email})</div>`;
                        });
                        $('#doctor_suggestions').remove();
                        $('#doctor_search').after(`<div id="doctor_suggestions" style="border:1px solid #ccc; background:#fff;">${list}</div>`);
                    }
                });
            }
        });
        
        /*delete Admin Services functionality*/
        $(document).on('click', '.delete-service-btn', function(e) {
            e.preventDefault();
            var id = $(this).data('id');
            Swal.fire({
                title: 'Are you sure?',
                text: "Do you want to delete this service?",
                showCancelButton: true,
                confirmButtonColor: '#fb6421',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    deleteAdminservice(id);
                }
            });
        });
        function deleteAdminservice(id) {
            $.ajax({
                url: '{{ route('doctor.services.destroy', '') }}' + '/' + id,
                type: 'GET',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    Swal.fire({
                        title: 'Deleted!',
                        text: 'Service has been deleted successfully.',
                        confirmButtonColor: '#fb6421'
                    }).then(() => {
                        window.location.href = '{{route('doctor.services.index')}}';
                    });
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.log('Error:', errorThrown);
                }
            });
        }
    
        $(document).on('click', '.doctor-option', function() {
            $('#doctor_id').val($(this).data('id'));
            $('#doctor_search').val($(this).text());
            $('#doctor_suggestions').remove();
        });
        
        $('#sms-dynamic-data').on('click', '.update-sms-status', function() {
            $(this).toggleClass("fa-toggle-on fa-toggle-off");
            $(this).toggleClass("text-success text-danger");
            var oldText = $(this).closest('tr').find("td.active-inactive span").text();
            var newText = $(this).closest('tr').find("td.active-inactive span").data('text');
            $(this).closest('tr').find("td.active-inactive span").text(newText).data('text',oldText);
            var oldColor = $(this).closest('tr').find("td.active-inactive span").data('old');
            var newColor = $(this).closest('tr').find("td.active-inactive span").data('new');
            $(this).closest('tr').find("td.active-inactive span").removeClass(oldColor).addClass(newColor).data('old', newColor).data('new', oldColor);
            var valuedata = $(this).attr('data-id');
            $.ajax({
                data:{
                    '_token': '{{ csrf_token() }}',
                    'id':valuedata
                },
                url: '{{ route('sms.status') }}',
                type: 'post',
                beforeSend: function() {
                },
                success: function(data) {
                },
                error: function(jqXHR, textStatus, errorThrown) {
                }
            })
        });
        
        $('#plan-dynamic-data').on('click', '.update-plan-status', function() {
            $(this).toggleClass("fa-toggle-on fa-toggle-off");
            $(this).toggleClass("text-success text-danger");
            var oldText = $(this).closest('tr').find("td.active-inactive span").text();
            var newText = $(this).closest('tr').find("td.active-inactive span").data('text');
            $(this).closest('tr').find("td.active-inactive span").text(newText).data('text',oldText);
            var oldColor = $(this).closest('tr').find("td.active-inactive span").data('old');
            var newColor = $(this).closest('tr').find("td.active-inactive span").data('new');
            $(this).closest('tr').find("td.active-inactive span").removeClass(oldColor).addClass(newColor).data('old', newColor).data('new', oldColor);
            var valuedata = $(this).attr('data-id');
            $.ajax({
                data:{
                    '_token': '{{ csrf_token() }}',
                    'id':valuedata
                },
                url: '{{ route('message_plans.status') }}',
                type: 'post',
                beforeSend: function() {
                },
                success: function(data) {
                },
                error: function(jqXHR, textStatus, errorThrown) {
                }
            })
        });
        
        $('#service-dynamic-data').on('click', '.update-service-status', function() {
            $(this).toggleClass("fa-toggle-on fa-toggle-off");
            $(this).toggleClass("text-success text-danger");
            var oldText = $(this).closest('tr').find("td.active-inactive span").text();
            var newText = $(this).closest('tr').find("td.active-inactive span").data('text');
            $(this).closest('tr').find("td.active-inactive span").text(newText).data('text',oldText);
            var oldColor = $(this).closest('tr').find("td.active-inactive span").data('old');
            var newColor = $(this).closest('tr').find("td.active-inactive span").data('new');
            $(this).closest('tr').find("td.active-inactive span").removeClass(oldColor).addClass(newColor).data('old', newColor).data('new', oldColor);
            var valuedata = $(this).attr('data-id');
            $.ajax({
                data:{
                    '_token': '{{ csrf_token() }}',
                    'id':valuedata
                },
                url: '{{ route('doctor.services.status') }}',
                type: 'post',
                beforeSend: function() {
                },
                success: function(data) {
                },
                error: function(jqXHR, textStatus, errorThrown) {
                }
            })
        });
        /*delete Admin Doctor functionality*/
        $(document).on('click', '.delete-plan-btn', function(e) {
            e.preventDefault();
            var id = $(this).data('id');
            Swal.fire({
                title: 'Are you sure?',
                text: "Do you want to delete this plan?",
                showCancelButton: true,
                confirmButtonColor: '#fb6421',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    deleteAdminplan(id);
                }
            });
        });
        function deleteAdminplan(id) {
            $.ajax({
                url: '{{ route('message_plans.destroy', '') }}' + '/' + id,
                type: 'GET',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    Swal.fire({
                        title: 'Deleted!',
                        text: 'Plan has been deleted successfully.',
                        confirmButtonColor: '#fb6421'
                    }).then(() => {
                        window.location.href = '{{route('message_plans.index')}}';
                    });
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.log('Error:', errorThrown);
                }
            });
        }
        
        /*apointment Search*/
        function filterAppointments() {
    var search = $("#appointments_search").val();
    var date = $("#appointments_date").val();

    $.ajax({
        url: '{{ route('filter-appointments') }}',
        type: "GET",
        data: {
            "search": search,
            "date": date
        },
        beforeSend:function(){
            $('#loading').show();
        },
        complete:function(){
            $('#loading').hide();
        },
        success:function(res){
            $("#appointment-dynamic-data").html(res);
            $('.clear-filter').show('slide');
        },
        error:function(jqXHR, ajaxOptions, thrownError){
        }
    });
}

// trigger on typing or picking date
$('#appointments_search').keyup(filterAppointments);

        
        /*cancle apointment*/
        /*delete Admin Doctor functionality*/
        $(document).on('click', '.cancel-appointment', function(e) {
            e.preventDefault();
            var id = $(this).data('id');
        
            Swal.fire({
                title: 'Cancel Appointment',
                input: 'text',
                inputLabel: 'Reason for cancellation',
                inputPlaceholder: 'Enter reason...',
                inputAttributes: {
                    required: true
                },
                showCancelButton: true,
                confirmButtonColor: '#ef7f1a',
                cancelButtonColor: '#fc5a5a',
                confirmButtonText: 'Cancel Appointment',
                preConfirm: (reason) => {
                    if (!reason) {
                        Swal.showValidationMessage("Reason is required");
                    }
                    return reason;
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    cancelDoctorAppointment(id, result.value);
                }
            });
        });

        function cancelDoctorAppointment(id, reason) {
            $.ajax({
                url: '{{ route('appointment.cancel', '') }}/' + id,
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    reason: reason
                },
                success: function(response) {
                    Swal.fire({
                        title: 'Cancelled!',
                        text: response.success,
                        icon: 'success',
                        confirmButtonColor: '#ef7f1a'
                    }).then(() => {
                        window.location.href = '{{ route('doctor.appointments') }}';
                    });
                },
        
                error: function(xhr) {
                    let message = "Something went wrong.";
                    if(xhr.responseJSON && xhr.responseJSON.error){
                        message = xhr.responseJSON.error;
                    }
                    Swal.fire({
                        title: 'Error!',
                        text: message,
                        icon: 'error',
                        confirmButtonColor: '#d33'
                    });
                }
            });
        }

        $(document).ready(function() {
             $(document).on('click', '.reschedule-btn', function(e) {
                    e.preventDefault();
                let id = $(this).data("id");
                let date = $(this).data("date");
                let time = $(this).data("time");
        
                $("#appointment_id").val(id);
                $("#reschedule_date").val(date);
                $.post("{{ route('appointment.get-slots') }}", {
                    _token: "{{ csrf_token() }}",
                    date: date
                }, function(data){
                    let dropdown = $('#reschedule_time');
                    dropdown.empty();
                    dropdown.append('<option value="">-- Select Time --</option>');
                    $.each(data, function(index, slot){
                        dropdown.append('<option value="'+slot+'">'+slot+'</option>');
                    });
        
                });
                var myModal = new bootstrap.Modal(document.getElementById('rescheduleModal'));
                myModal.show();
            });

            $("#rescheduleForm").on("submit", function(e) {
                e.preventDefault();
            
                $.ajax({
                    url: "{{ route('appointments.reschedule') }}",
                    type: "POST",
                    data: $(this).serialize(),
                    success: function(response) {
                        // Trigger the cancel button click
                        $("#cancelBtn").trigger("click");
            
                        // Show SweetAlert
                        Swal.fire({
                            title: 'Rescheduled!',
                            text: 'Appointment has been rescheduled successfully.',
                            icon: 'success',
                            confirmButtonColor: '#ef7f1a'
                        }).then(() => {
                            window.location.href = '{{ route('doctor.appointments') }}';
                        });
                    },
                    error: function(xhr) {
                        let message = "Could not reschedule appointment.";
                        if(xhr.responseJSON && xhr.responseJSON.error){
                            message = xhr.responseJSON.error;
                        }
                        Swal.fire({
                            title: 'Error!',
                            text: message,
                            icon: 'error',
                            confirmButtonColor: '#d33'
                        });
                    }
                });
            });
        });
        
        $(document).ready(function(){
            function loadSlots(date){
                $.post("{{ route('appointment.get-slots') }}", {
                    _token: "{{ csrf_token() }}",
                    date: date
                }, function(data){
                    let dropdown = $('#reschedule_time');
                    dropdown.empty();
                    dropdown.append('<option value="">-- Select Time --</option>');
                    $.each(data, function(index, slot){
                        dropdown.append('<option value="'+slot+'">'+slot+'</option>');
                    });
        
                });
            }
            let defaultDate = $('#reschedule_date').val();
            if(defaultDate){
                loadSlots(defaultDate);
            }
            $('#reschedule_date').change(function(){
                let date = $(this).val();
                loadSlots(date);
            });
        });
        
        /*appointment missing*/
        $(document).on('click', '.missing-appointment', function(e) {
            e.preventDefault();
            var id = $(this).data('id');
            Swal.fire({
                title: 'Mark appointment as missed?',
                text: "Are you sure you want to mark this appointment as missed?",
                showCancelButton: true,
                confirmButtonColor: '#ef7f1a',
                cancelButtonColor: '#fc5a5a',
                confirmButtonText: 'Yes, mark as missed',
                cancelButtonText: 'No'
            }).then((result) => {
                if (result.isConfirmed) {
                    missDoctorAppointment(id);
                }
            });
        });
        function missDoctorAppointment(id) {
            $.ajax({
                url: '{{ route('appointment.missing', '') }}' + '/' + id,
                type: 'GET',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    Swal.fire({
                        title: 'Appointment marked',
                        text: 'You have successfully marked this appointment as missed.',
                        confirmButtonColor: '#ef7f1a'
                    }).then(() => {
                        window.location.href = '{{route('doctor.appointments')}}';
                    });
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.log('Error:', errorThrown);
                }
            });
        }
        
        /*appointment missing*/
        $(document).on('click', '.checkin-appointment', function(e) {
            e.preventDefault();
            var id = $(this).data('id');
            Swal.fire({
                title: '',
                text: "Confirm check-in for this appointment?",
                showCancelButton: true,
                confirmButtonColor: '#ef7f1a',
                cancelButtonColor: '#fc5a5a',
                confirmButtonText: 'Yes',
                cancelButtonText: 'No'
            }).then((result) => {
                if (result.isConfirmed) {
                    checkinDoctorAppointment(id);
                }
            });
        });
        function checkinDoctorAppointment(id) {
            $.ajax({
                url: '{{ route('appointment.checkin', '') }}' + '/' + id,
                type: 'GET',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    window.location.href = '{{route('doctor.appointments')}}';
                    /*Swal.fire({
                        title: 'Checked In',
                        text: 'You have successfully checked in for your appointment.',
                        confirmButtonColor: '#ef7f1a'
                    }).then(() => {
                        window.location.href = '{{route('doctor.appointments')}}';
                    });*/
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.log('Error:', errorThrown);
                }
            });
        }
        
        $(document).ready(function() {
            $('#bookingToggle').on('change', function() {
        var doctorId = $(this).data('id');
        var status = $(this).is(':checked') ? 1 : 0;

        $.ajax({
            url: "{{ route('doctor.toggleBooking') }}",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                doctor_id: doctorId,
                booking_enabled: status
            },
            success: function(res) {
                $('#toggleStatus').text(status ? 'Enabled' : 'Disabled');
                toastr.success('Booking status updated successfully!');
            },
            error: function() {
                toastr.error('Could not update booking status.');
            }
        });
    });
});


    </script>
    <script>
    document.addEventListener("DOMContentLoaded", function() {
        document.querySelectorAll('.copy-credentials-btn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                let username = this.getAttribute('data-username');
                let password = this.getAttribute('data-password');
                
                let credentials = `Username: ${username}\nPassword: ${password}`;
                
                // Copy to clipboard
                navigator.clipboard.writeText(credentials).then(function() {
                    Swal.fire({
                        title: 'Copied!',
                        html: `<b>Username:</b> ${username}<br><b>Password:</b> ${password}`,
                        icon: 'success',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#fb6421'
                    });
                }).catch(function(err) {
                    Swal.fire({
                        title: 'Oops!',
                        text: 'Failed to copy credentials.',
                        icon: 'error',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#d33'
                    });
                });
            });
        });
    });
    $(document).on("click", "#cancelBtn", function() {
        var myModalEl = document.getElementById('rescheduleModal');
        var modal = bootstrap.Modal.getInstance(myModalEl);
        modal.hide();
    });
    
    $(document).on("click", ".custom_setting_dropdown > a", function(e) {
    e.preventDefault(); // stop Bootstrap's dropdown handler
    e.stopPropagation(); // avoid body click closing it immediately

    let $parent = $(this).closest(".custom_setting_dropdown");
    let $menu = $parent.find(".dropdown-menu");

    // Close other open dropdowns if needed
    $(".custom_setting_dropdown").not($parent).removeClass("show").find(".dropdown-menu").removeClass("show");

    // Toggle current dropdown
    $parent.toggleClass("show");
    $menu.toggleClass("show");
});

// Hide on clicking outside
$(document).on("click", function() {
    $(".custom_setting_dropdown").removeClass("show").find(".dropdown-menu").removeClass("show");
});
$(document).ready(function() {
    $('.send_to').select2();
    $('.send_to').on('change', function() {
        let values = $(this).val();
        if (values && values.includes('all')) {
            $(this).val(['all']).trigger('change');
        }
    });
});

    </script>
    <script>
        let autocomplete;
    
        function initialize() {
            const input = document.getElementById("address");
            autocomplete = new google.maps.places.Autocomplete(input);
            autocomplete.setFields(["address_component", "geometry"]);
            autocomplete.addListener("place_changed", fillInAddress);
        }
    
        function fillInAddress() {
            const place = autocomplete.getPlace();
            clearFields();
            place.address_components.forEach((component) => {
                const types = component.types;
                if (types.includes("locality")) {
                    document.getElementById("area_name").value = component.long_name;
                }
                if (types.includes("administrative_area_level_2")) {
                    document.getElementById("city_name").value = component.long_name;
                }
                if (types.includes("postal_code")) {
                    document.getElementById("postal_code").value = component.long_name;
                }
                if (types.includes("country")) {
                    document.getElementById("country_name").value = component.long_name;
                }
            });
            if (place.geometry) {
                const lat = place.geometry.location.lat();
                const lng = place.geometry.location.lng();
                document.getElementById("latitude").value = lat;
                document.getElementById("longitude").value = lng;
            }
        }
    
        function clearFields() {
            document.getElementById("area_name").value = "";
            document.getElementById("city_name").value = "";
            document.getElementById("postal_code").value = "";
            document.getElementById("country_name").value = "";
            document.getElementById("latitude").value = "";
            document.getElementById("longitude").value = "";
        }
        google.maps.event.addDomListener(window, 'load', initialize);
    </script>
    <script>
document.addEventListener("DOMContentLoaded", function() {
    const select = document.querySelector('select[name="title"]');
    if(select && select.value) {
        select.value = select.value; // force refresh
        select.dispatchEvent(new Event('change')); // BMD trigger
    }
});
</script>

<script>
document.getElementById('serviceForm').addEventListener('submit', function(event) {
    let checked = document.querySelectorAll('.service-checkbox:checked').length;

    if (checked < 2 || checked > 5) {
        event.preventDefault();
        Swal.fire({
            icon: 'warning',
            title: 'Invalid Selection',
            text: 'Please select minimum 2 and maximum 5 services.',
            confirmButtonColor: '#3085d6',
        });
    }
});
</script>
<script>
    /*document.addEventListener("DOMContentLoaded", function () {
        const wrapper = document.getElementById('services-wrapper');
        const maxServices = 5;

        wrapper.addEventListener('click', function (e) {
            if (e.target.classList.contains('btn-add-service')) {
                const count = wrapper.querySelectorAll('.service-item').length;
                if (count < maxServices) {
                    const newInput = document.createElement('div');
                    newInput.classList.add('input-group', 'mb-2', 'service-item');
                    newInput.innerHTML = `
                        <input type="text" name="services[]" class="form-control service-input" 
                               placeholder="Enter Service Title (only alphabets)" required   maxlength="50">
                        <button type="button" class="btn btn-danger btn-remove-service">-</button>
                    `;
                    wrapper.appendChild(newInput);
                } else {
                    alert("You can add a maximum of 5 services.");
                }
            }

            if (e.target.classList.contains('btn-remove-service')) {
                e.target.closest('.service-item').remove();
            }
        });
    });*/
</script>


<!-- JS -->
<!-- jQuery UI (make sure these are after jQuery) -->
<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {

    function initServiceAutocomplete(element) {
        $(element).autocomplete({
            source: function(request, response) {
                $.ajax({
                    url: "{{ route('services.search') }}",
                    data: { q: request.term },
                    success: function(data) {
                        response(data.map(item => item.toUpperCase()));
                    }
                });
            },
            minLength: 1,
            select: function(event, ui) {
                element.value = ui.item.value.toUpperCase();
            }
        });
    }

    document.querySelectorAll('.service-input').forEach(function(input){
        initServiceAutocomplete(input);
    });

    document.addEventListener("input", function(e) {
        if (e.target.classList.contains("service-input")) {
            e.target.value = e.target.value.toUpperCase();
        }
    });

    const wrapper = document.getElementById('services-wrapper');
    const maxServices = 10;
    
    wrapper.addEventListener('click', function(e) {

        if (e.target.classList.contains('btn-add-service')) {

            const count = wrapper.querySelectorAll('.service-item').length;

            if(count < maxServices) {

                const newInput = document.createElement('div');
                newInput.classList.add('input-group','mb-2','service-item');

                newInput.innerHTML = `
                    <input type="text" name="services[]" 
                           class="form-control service-input service_placeholder" 
                           placeholder="Enter Service Title" maxlength="50" required>
                    <button type="button" class="btn btn-danger btn-remove-service">-</button>
                `;

                wrapper.appendChild(newInput);

                const newServiceInput = newInput.querySelector('.service-input');
                initServiceAutocomplete(newServiceInput);

            } else {
                alert("Maximum 10 services allowed");
            }
        }

        if(e.target.classList.contains('btn-remove-service')) {
            e.target.closest('.service-item').remove();
        }

    });


});
</script>

<script>
document.addEventListener("DOMContentLoaded", function () {


    /* ===============================
       DAY TOGGLE
    =============================== */
    document.querySelectorAll('.day-checkbox').forEach(cb => {
        cb.addEventListener('change', function(){
            const section = this.closest('.card').querySelector('.day-time-section');
            section.classList.toggle('d-none', !this.checked);
        });
    });

    /* ===============================
       SLOT TYPE TOGGLE
    =============================== */
    document.querySelectorAll('.day-slot-type').forEach(select => {
        select.addEventListener('change', function(){
            const section = this.closest('.day-time-section');
            const single = section.querySelector('.single-slot-box');
            const double = section.querySelector('.double-slot-box');

            if(this.value === 'single'){
                single.classList.remove('d-none');
                double.classList.add('d-none');
            }
            else if(this.value === 'double'){
                single.classList.add('d-none');
                double.classList.remove('d-none');
            }
            else{
                single.classList.add('d-none');
                double.classList.add('d-none');
            }
        });
    });

    /* =========================================================
REAL TIME SLOT VALIDATION (MAX 10 SLOTS)
========================================================== */

function timeToMinutes(time){
    const parts = time.split(':').map(Number);
    return parts[0]*60 + parts[1];
}

function minutesToTime(minutes){
    const h = Math.floor(minutes/60).toString().padStart(2,'0');
    const m = (minutes%60).toString().padStart(2,'0');
    return h+":"+m;
}

/* ===== END TIME DROPDOWN FILTER ===== */

function filterEndTimes(startSelect, endSelect, gapInput){

    const gap = parseInt(gapInput.value);
    const start = startSelect.value;

    if(!gap || !start) return;

    const startMin = timeToMinutes(start);
    const maxEnd = startMin + (gap * 10);

    Array.from(endSelect.options).forEach(opt => {

        if(!opt.value) return;

        const optMin = timeToMinutes(opt.value);

        if(optMin <= startMin || optMin > maxEnd){
            opt.style.display = "none";
        }else{
            opt.style.display = "block";
        }

    });

    endSelect.value = "";
}
    
    function attachRealtimeValidation(section){
    
        const gapInput = section.querySelector('.day-slot-gap');
        if(!gapInput) return;
    
        /* ===== SINGLE SLOT ===== */
    
        const singleStart = section.querySelector('.single-start');
        const singleEnd = section.querySelector('.single-end');
    
        if(singleStart && singleEnd){
            singleStart.addEventListener("change",function(){
                filterEndTimes(singleStart,singleEnd,gapInput);
            });
            singleEnd.addEventListener('change', function(){
                const gap = parseInt(gapInput.value);
                if(!gap || !singleStart.value || !singleEnd.value) return;
                const startMin = timeToMinutes(singleStart.value);
                const endMin = timeToMinutes(singleEnd.value);
                if(endMin <= startMin){
                    Swal.fire({
                        icon:'warning',
                        title:'End time must be after start time'
                    });
                    singleEnd.value="";
                    return;
                }
    
                const totalSlots = Math.floor((endMin - startMin)/gap);
                if(totalSlots > 10){
                    const maxEnd = startMin + (gap*10);
                    singleEnd.value = minutesToTime(maxEnd);
                    Swal.fire({
                        icon:'warning',
                        title:'Maximum 10 slots allowed',
                        text:'End time adjusted automatically'
                    });
                }
            });
        }
    
    
        /* ===== DOUBLE SLOT ===== */
    
        const fs = section.querySelector('[name*="first_half_start"]');
        const fe = section.querySelector('[name*="first_half_end"]');
        const ss = section.querySelector('[name*="second_half_start"]');
        const se = section.querySelector('[name*="second_half_end"]');
    
        /* FIRST HALF START */
        if(fs){
            fs.addEventListener("change",function(){
                filterEndTimes(fs,fe,gapInput);
                if(fe) fe.value="";
                if(ss) ss.value="";
                if(se) se.value="";
            });
        }
    
    
        /* FIRST HALF END */
    
        if(fe){
            fe.addEventListener("change",function(){
                const gap = parseInt(gapInput.value);
                if(!gap || !fs.value || !fe.value) return;
                const startMin = timeToMinutes(fs.value);
                const endMin = timeToMinutes(fe.value);
                const total = Math.floor((endMin-startMin)/gap);
                if(total>10){
                    fe.value = minutesToTime(startMin + (gap*10));
                    Swal.fire({
                        icon:'warning',
                        title:'Max 10 slots in first half'
                    });
                }
    
                /* SECOND HALF START FILTER */
                if(ss && fe.value){
                    const firstEnd = timeToMinutes(fe.value);
                    Array.from(ss.options).forEach(opt=>{
                        if(!opt.value) return;
                        const optMin = timeToMinutes(opt.value);
                        if(optMin <= firstEnd){
                            opt.style.display="none";
                        }else{
                            opt.style.display="block";
                        }
                    });
                    ss.value="";
                    if(se) se.value="";
                }
            });
        }
    
        /* SECOND HALF START */
        if(ss){
            ss.addEventListener("change",function(){
                if(!fe || !fe.value || !ss.value) return;
                const firstEnd = timeToMinutes(fe.value);
                const secondStart = timeToMinutes(ss.value);
                if(secondStart <= firstEnd){
                    Swal.fire({
                        icon:'warning',
                        title:'Second half must start after first half ends'
                    });
                    ss.value="";
                    return;
                }
                filterEndTimes(ss,se,gapInput);
            });
        }
    
        /* SECOND HALF END */
        if(se){
            se.addEventListener("change",function(){
                const gap = parseInt(gapInput.value);
                if(!gap || !ss.value || !se.value) return;
                const startMin = timeToMinutes(ss.value);
                const endMin = timeToMinutes(se.value);
                const total = Math.floor((endMin-startMin)/gap);
                if(total>10){
                    se.value = minutesToTime(startMin+(gap*10));
                    Swal.fire({
                        icon:'warning',
                        title:'Max 10 slots in second half'
                    });
                }
    
                if(fe && fe.value){
                    const firstEnd = timeToMinutes(fe.value);
                    if(startMin <= firstEnd){
                        Swal.fire({
                            icon:'warning',
                            title:'Second half must start after first half ends'
                        });
                        ss.value="";
                        se.value="";
                    }
                }
            });
        }
    
    
        /* GAP CHANGE RESET */
    
        gapInput.addEventListener("change",function(){
            if(singleStart) singleStart.value="";
            if(singleEnd) singleEnd.value="";
    
            if(fs) fs.value="";
            if(fe) fe.value="";
    
            if(ss) ss.value="";
            if(se) se.value="";
        });
    }
    
    /* APPLY TO ALL DAYS */
    document.querySelectorAll('.day-time-section').forEach(section=>{
        attachRealtimeValidation(section);
    });
    
    /* =========================================================
       COMPLETE DAY VALIDATION (STEP BY STEP ORDER)
    ========================================================= */
    
    const form = document.querySelector("form");
    form.addEventListener("submit", function(e){
        let hasError = false;
        document.querySelectorAll('.day-checkbox').forEach(cb => {
            if(cb.checked){
                const section = cb.closest('.card').querySelector('.day-time-section');
                const slotType = section.querySelector('.day-slot-type')?.value;
                const gap = section.querySelector('.day-slot-gap')?.value;
    
                if(!slotType){
                    hasError = true;
    
                    Swal.fire({
                        icon: 'warning',
                        title: 'Slot Type Missing',
                        text: 'Please select slot type for selected day.'
                    });
    
                    return;
                }
    
                /* ===== 2️⃣ SLOT GAP CHECK ===== */
                if(!gap){
                    hasError = true;
    
                    Swal.fire({
                        icon: 'warning',
                        title: 'Slot Gap Missing',
                        text: 'Please select slot gap for selected day.'
                    });
    
                    return;
                }
    
                /* ===== 3️⃣ SINGLE SLOT CHECK ===== */
                if(slotType === 'single'){
    
                    const start = section.querySelector('.single-start')?.value;
                    const end = section.querySelector('.single-end')?.value;
    
                    if(!start || !end){
                        hasError = true;
    
                        Swal.fire({
                            icon: 'warning',
                            title: 'Time Missing',
                            text: 'Please fill start and end time.'
                        });
    
                        return;
                    }
                }
    
                /* ===== 4️⃣ DOUBLE SLOT CHECK ===== */
                if(slotType === 'double'){
    
                    const fs = section.querySelector('[name*="first_half_start"]')?.value;
                    const fe = section.querySelector('[name*="first_half_end"]')?.value;
                    const ss = section.querySelector('[name*="second_half_start"]')?.value;
                    const se = section.querySelector('[name*="second_half_end"]')?.value;
    
                    if(!fs || !fe || !ss || !se){
                        hasError = true;
    
                        Swal.fire({
                            icon: 'warning',
                            title: 'Time Missing',
                            text: 'Please fill all time fields for both halves.'
                        });
    
                        return;
                    }
                }
    
            }
    
        });
    
        if(hasError){
            e.preventDefault();
        }
    
    });

});
</script>
@stack('scripts')



  </body>
</html>