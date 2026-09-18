<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simple Registration - OTP Flow</title>
    <!-- Icons -->
    <link href="https://cdn.jsdelivr.net/npm/@mdi/font@7.2.96/css/materialdesignicons.min.css" rel="stylesheet">

    <!-- CSRF Token (Required for Laravel) -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        /* --- VARIABLES & RESET --- */
        :root {
            --primary: #00b894;
            --primary-dark: #008f72;
            --secondary: #0984e3;
            --text-dark: #2d3436;
            --text-light: #636e72;
            --bg-gradient: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            --white: #ffffff;
            --danger: #ff7675;
            --success: #00b894;
            --radius: 16px;
            --shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
        }

        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Segoe UI', Roboto, Helvetica, sans-serif; }

        body {
            background: var(--bg-gradient);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        /* --- MAIN CARD --- */
        .auth-card {
            background: var(--white);
            width: 100%;
            max-width: 450px; /* Smaller width for simpler form */
            border-radius: 24px;
            box-shadow: var(--shadow);
            overflow: hidden;
            position: relative;
        }

        .card-header {
            text-align: center;
            padding: 30px 30px 20px 30px;
        }

        .logo-placeholder {
            width: 50px;
            height: 50px;
            background: var(--primary);
            border-radius: 50%;
            margin: 0 auto 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 24px;
        }

        .card-header h2 { font-size: 22px; color: var(--text-dark); margin-bottom: 5px; }
        .card-header p { color: var(--text-light); font-size: 14px; }

        /* --- STEPPER --- */
        .stepper-wrapper {
            display: flex;
            justify-content: space-between;
            margin: 20px 30px;
            position: relative;
        }

        .stepper-wrapper::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 2px;
            background: #dfe6e9;
            z-index: 0;
            transform: translateY(-50%);
        }

        .stepper-item {
            position: relative;
            z-index: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            background: var(--white);
            padding: 0 5px;
        }

        .step-counter {
            width: 28px;
            height: 28px;
            background: #dfe6e9;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            color: var(--text-light);
            font-size: 12px;
            transition: 0.3s;
        }

        .stepper-item.active .step-counter { background: var(--primary); color: white; }
        .stepper-item.completed .step-counter { background: var(--success); color: white; }

        .step-name { font-size: 10px; margin-top: 5px; color: var(--text-light); font-weight: 600; text-transform: uppercase; }

        /* --- FORM CONTENT --- */
        .card-body {
            padding: 0 30px 40px 30px;
        }

        .step-content {
            display: none;
            animation: fadeIn 0.4s ease-in-out;
        }

        .step-content.active { display: block; }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* --- FORM GROUPS --- */
        .form-section-title {
            font-size: 12px;
            color: var(--primary);
            font-weight: 800;
            text-transform: uppercase;
            margin-bottom: 15px;
            border-bottom: 1px solid #f1f2f6;
            padding-bottom: 5px;
        }

        .row { display: flex; gap: 15px; flex-wrap: wrap; }
        .col { flex: 1; min-width: 45%; }
        .col-full { width: 100%; }

        .form-group { margin-bottom: 15px; position: relative; }

        .form-label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 6px;
        }

        .form-control {
            width: 100%;
            padding: 12px 14px;
            border: 2px solid #dfe6e9;
            border-radius: 10px;
            font-size: 14px;
            transition: 0.2s;
            outline: none;
        }

        .form-control:focus { border-color: var(--primary); box-shadow: 0 0 0 3px rgba(0, 184, 148, 0.1); }
        .form-control.is-invalid { border-color: var(--danger); }

        /* Input Icons */
        .input-icon-wrapper { position: relative; }
        .input-icon {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #b2bec3;
            cursor: pointer;
        }

        .error-msg {
            color: var(--danger);
            font-size: 11px;
            margin-top: 4px;
            display: none;
        }
        .form-control.is-invalid + .error-msg { display: block; }

        /* --- BUTTONS --- */
        .btn {
            width: 100%;
            padding: 14px;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            transition: 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .btn-primary { background: var(--primary); color: white; box-shadow: 0 4px 15px rgba(0, 184, 148, 0.3); }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(0, 184, 148, 0.4); }
        .btn-primary:disabled { background: #b2bec3; cursor: not-allowed; transform: none; box-shadow: none; }

        .btn-secondary { background: #dfe6e9; color: var(--text-dark); margin-top: 10px; }
        .btn-secondary:hover { background: #b2bec3; }

        /* --- TOAST NOTIFICATIONS --- */
        .toast-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
        }

        .toast {
            background: white;
            padding: 15px 20px;
            border-radius: 8px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 12px;
            min-width: 300px;
            transform: translateX(120%);
            transition: transform 0.3s ease;
            border-left: 4px solid var(--primary);
        }

        .toast.show { transform: translateX(0); }
        .toast.error { border-left-color: var(--danger); }
        .toast-icon { font-size: 20px; }
        .toast.success .toast-icon { color: var(--success); }
        .toast.error .toast-icon { color: var(--danger); }
        .toast-msg { font-size: 14px; font-weight: 500; color: var(--text-dark); }

        /* --- LOADING SPINNER --- */
        .spinner {
            width: 20px; height: 20px;
            border: 2px solid rgba(255,255,255,0.3);
            border-radius: 50%;
            border-top-color: #fff;
            animation: spin 0.8s linear infinite;
            display: none;
        }
        @keyframes spin { to { transform: rotate(360deg); } }
    </style>
</head>
<body>

    <!-- Toast Container -->
    <div class="toast-container" id="toastContainer"></div>

    <div class="auth-card">
        <div class="card-header">
            <div class="logo-placeholder">
                <i class="mdi mdi-account-plus"></i>
            </div>
            <h2>Create Account</h2>
            <p>Simple Verification Flow</p>
        </div>

        <div class="stepper-wrapper">
            <div class="stepper-item active" id="indicator-1">
                <div class="step-counter">1</div>
                <div class="step-name">Profile</div>
            </div>
            <div class="stepper-item" id="indicator-2">
                <div class="step-counter">2</div>
                <div class="step-name">Verify</div>
            </div>
            <div class="stepper-item" id="indicator-3">
                <div class="step-counter">3</div>
                <div class="step-name">Done</div>
            </div>
        </div>

        <div class="card-body">
            <form id="regForm" onsubmit="return false;">

                <!-- STEP 1: PROFILE DETAILS -->
                <div class="step-content active" id="step-1">

                    <div class="form-section-title">Personal Information</div>

                    <div class="row">
                        <div class="col">
                            <div class="form-group">
                                <label class="form-label">Title</label>
                                <select class="form-control" name="title" required>
                                    <option value="Mr">Mr</option>
                                    <option value="Mrs">Mrs</option>
                                    <option value="Miss">Miss</option>
                                </select>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group">
                                <label class="form-label">Gender</label>
                                <select class="form-control" name="gender" required>
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col">
                            <div class="form-group">
                                <label class="form-label">First Name</label>
                                <input type="text" class="form-control" name="first_name" placeholder="John" required>
                                <div class="error-msg">First name is required</div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group">
                                <label class="form-label">Last Name</label>
                                <input type="text" class="form-control" name="last_name" placeholder="Doe" required>
                                <div class="error-msg">Last name is required</div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Email Address</label>
                        <div class="input-icon-wrapper">
                            <input type="email" class="form-control" name="email" placeholder="you@example.com" required>
                            <i class="mdi mdi-email input-icon" style="pointer-events: none;"></i>
                        </div>
                        <div class="error-msg">Valid email is required</div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Phone Number</label>
                        <div class="input-icon-wrapper">
                            <input type="tel" class="form-control" name="phone" id="phoneInput" placeholder="+91 0000000000" required>
                            <i class="mdi mdi-phone input-icon" style="pointer-events: none;"></i>
                        </div>
                        <div class="error-msg">Phone number is required</div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Password</label>
                        <div class="input-icon-wrapper">
                            <input type="password" class="form-control" name="password" id="passwordInput" placeholder="Create password" required>
                            <i class="mdi mdi-eye input-icon" onclick="togglePassword('passwordInput', this)"></i>
                        </div>
                        <div class="error-msg">Min 6 chars required</div>
                    </div>

                    <button type="button" class="btn btn-primary" id="btnGetOtp" onclick="processStep1()">
                        <span class="spinner"></span>
                        <span class="btn-text">Send Verification Code</span>
                    </button>
                </div>

                <!-- STEP 2: OTP VERIFICATION -->
                <div class="step-content" id="step-2">
                    <div style="text-align: center; margin-bottom: 20px;">
                        <div style="width: 60px; height: 60px; background: #e3f2fd; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 10px;">
                            <i class="mdi mdi-cellphone-message" style="font-size: 30px; color: var(--secondary);"></i>
                        </div>
                        <h3 style="font-size: 18px; color: var(--text-dark);">Verify Mobile</h3>
                        <p style="font-size: 13px; color: var(--text-light);">Enter the 4-digit code sent to <span id="display-phone" style="font-weight: bold;"></span></p>
                    </div>

                    <div class="form-group">
                        <input type="text" id="otpInput" class="form-control" style="letter-spacing: 5px; font-size: 20px; text-align: center;" placeholder="0000" maxlength="4">
                        <div class="error-msg" id="otp-error">Invalid OTP</div>
                    </div>

                    <button type="button" class="btn btn-primary" id="btnVerify" onclick="processStep2()">
                        <span class="spinner"></span>
                        <span class="btn-text">Verify & Register</span>
                    </button>

                    <button type="button" class="btn btn-secondary" onclick="goToStep(1)">
                        Back to Edit Profile
                    </button>

                    <div style="text-align: center; margin-top: 15px;">
                        <a href="#" style="font-size: 12px; color: var(--secondary); text-decoration: none;" onclick="resendOtp()">Resend Code</a>
                    </div>
                </div>

                <!-- STEP 3: SUCCESS -->
                <div class="step-content" id="step-3" style="text-align: center;">
                    <div style="margin-bottom: 20px;">
                        <i class="mdi mdi-check-circle" style="font-size: 80px; color: var(--success);"></i>
                    </div>
                    <h2 style="color: var(--text-dark); margin-bottom: 10px;">Success!</h2>
                    <p style="color: var(--text-light); margin-bottom: 20px;">Your account has been created.</p>
                    <button type="button" class="btn btn-primary" onclick="window.location.href='{{ url('dashboard') }}'">
                        Go to Dashboard
                    </button>
                </div>

            </form>
        </div>
    </div>

    <script>
        // --- UTILITIES ---
        function getCSRFToken() {
            return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        }

        function showToast(msg, type = 'success') {
            const container = document.getElementById('toastContainer');
            const toast = document.createElement('div');
            toast.className = `toast ${type}`;
            toast.innerHTML = `
                <i class="mdi ${type === 'success' ? 'mdi-check-circle' : 'mdi-alert-circle'} toast-icon"></i>
                <span class="toast-msg">${msg}</span>
            `;
            container.appendChild(toast);

            requestAnimationFrame(() => toast.classList.add('show'));
            setTimeout(() => {
                toast.classList.remove('show');
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }

        function togglePassword(inputId, icon) {
            const input = document.getElementById(inputId);
            if (input.type === "password") {
                input.type = "text";
                icon.classList.replace('mdi-eye', 'mdi-eye-off');
            } else {
                input.type = "password";
                icon.classList.replace('mdi-eye-off', 'mdi-eye');
            }
        }

        function goToStep(stepNum) {
            document.querySelectorAll('.step-content').forEach(el => el.classList.remove('active'));
            document.getElementById(`step-${stepNum}`).classList.add('active');

            document.querySelectorAll('.stepper-item').forEach(el => {
                el.classList.remove('active', 'completed');
                const counter = parseInt(el.querySelector('.step-counter').innerText);
                if (counter < stepNum) el.classList.add('completed');
                if (counter === stepNum) el.classList.add('active');
            });
        }

        function setLoading(btnId, isLoading) {
            const btn = document.getElementById(btnId);
            const spinner = btn.querySelector('.spinner');
            const text = btn.querySelector('.btn-text');

            if (isLoading) {
                btn.disabled = true;
                spinner.style.display = 'inline-block';
                text.style.opacity = '0.7';
            } else {
                btn.disabled = false;
                spinner.style.display = 'none';
                text.style.opacity = '1';
            }
        }

        // --- LOGIC ---

        // 1. Process Profile Step (Send OTP)
        async function processStep1() {
            const form = document.getElementById('regForm');

            if (!form.checkValidity()) {
                form.reportValidity();
                const invalids = form.querySelectorAll(':invalid');
                invalids.forEach(input => {
                    input.classList.add('is-invalid');
                    input.addEventListener('input', () => input.classList.remove('is-invalid'), {once: true});
                });
                return;
            }

            const phone = document.getElementById('phoneInput').value;
            document.getElementById('display-phone').innerText = phone;

            setLoading('btnGetOtp', true);

            try {
                const response = await fetch("{{url('api/v1/send-otp')}}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': getCSRFToken()
                    },
                    body: JSON.stringify({ phone: phone })
                });

                const data = await response.json();

                if (data.status) {
                    // Show OTP for testing purposes (remove in production)
                    const displayOtp = data.otp ? ` (Dev: ${data.otp})` : '';
                    showToast(`OTP Sent successfully${displayOtp}`, 'success');
                    goToStep(2);
                    document.getElementById('otpInput').focus();
                } else {
                    showToast(data.message || 'Error sending OTP', 'error');
                }
            } catch (error) {
                console.error(error);
                showToast('Network error sending OTP', 'error');
            } finally {
                setLoading('btnGetOtp', false);
            }
        }

        // 2. Verify OTP & Register
        async function processStep2() {
            const phone = document.getElementById('phoneInput').value;
            const otp = document.getElementById('otpInput').value;
            const otpError = document.getElementById('otp-error');

            setLoading('btnVerify', true);

            try {
                // A. First, Verify OTP
                const verifyResponse = await fetch("{{url('api/v1/verify-otp')}}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': getCSRFToken()
                    },
                    body: JSON.stringify({ phone: phone, otp: otp })
                });

                const verifyData = await verifyResponse.json();

                if (!verifyData.status) {
                    document.getElementById('otpInput').classList.add('is-invalid');
                    otpError.style.display = 'block';
                    otpError.innerText = verifyData.message || 'Invalid OTP';
                    showToast('Verification failed', 'error');
                    setLoading('btnVerify', false);
                    return;
                }

                // B. If OTP Valid, Register User
                const formData = new FormData(document.getElementById('regForm'));
                formData.append('password_confirmation', formData.get('password'));

                // // The frontend form is simple, but the backend requires professional data.
                // // We inject dummy values to satisfy Laravel validation.
                // formData.append('experience', '0');
                // formData.append('profession_type', '1');
                // formData.append('country', '1');
                // formData.append('state', '1');
                // formData.append('city', '1');

                const registerResponse = await fetch("{{url('api/v1/doctor-register')}}", {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': getCSRFToken()
                        // Do NOT set Content-Type for FormData, browser handles it
                    },
                    body: formData
                });

                // Handle non-2xx responses for registration
                if (!registerResponse.ok) {
                    const errorData = await registerResponse.json();
                    throw new Error(errorData.message || JSON.stringify(errorData.errors || {}));
                }

                const registerData = await registerResponse.json();
                console.log("User Registered:", registerData);

                showToast('Account created successfully!', 'success');
                goToStep(3); // Success Step

            } catch (error) {
                console.error(error);
                showToast('Registration failed: ' + error.message, 'error');
            } finally {
                setLoading('btnVerify', false);
            }
        }

        // 3. Resend OTP
        async function resendOtp() {
            const phone = document.getElementById('phoneInput').value;
            if(!phone) return;

            try {
                const response = await fetch("{{url('api/v1/send-otp')}}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': getCSRFToken()
                    },
                    body: JSON.stringify({ phone: phone })
                });
                const data = await response.json();
                if(data.status) {
                    showToast('New OTP sent', 'success');
                } else {
                    showToast('Failed to resend', 'error');
                }
            } catch (e) {
                showToast('Network error', 'error');
            }
        }

    </script>
</body>
</html>
