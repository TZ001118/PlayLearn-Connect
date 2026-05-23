<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PLAYLEARN - Account Recovery</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<<<<<<< HEAD
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: { DEFAULT: '#0f172a', foreground: '#f8fafc' },
                        background: '#ffffff',
                        foreground: '#020817',
                        muted: { foreground: '#64748b' },
                        border: '#e2e8f0',
                        accent: '#f1f5f9'
                    }
                }
=======
    <title>PlayLearn - Account Recovery</title>
    <style>
        /* --- 基础与背景样式 --- */
        body {
            background-color: #151313;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            color: white;
        }

        .recovery-container {
            background-color: #2b2b2b; /* 深色卡片 */
            width: 450px;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            box-sizing: border-box;
        }

        /* --- 文字排版样式 --- */
        h2 {
            text-align: center;
            font-size: 26px;
            font-weight: 900;
            margin-bottom: 25px;
            margin-top: 0;
            letter-spacing: 0.5px;
        }

        .user-display-email {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .user-display-name {
            text-align: center;
            font-size: 22px;
            font-weight: bold;
            margin-bottom: 0px;
        }
        .user-display-subname {
            text-align: center;
            font-size: 14px;
            color: #aaaaaa;
            margin-bottom: 20px;
        }

        .instruction-text {
            text-align: center;
            font-size: 14px;
            color: #dcdcdc;
            line-height: 1.6;
            margin-bottom: 25px;
        }

        /* --- 输入框样式 --- */
        .form-group {
            margin-bottom: 20px;
            display: flex;
            flex-direction: column;
        }

        .form-group label {
            font-size: 14px;
            margin-bottom: 8px;
            color: #ffffff;
            font-weight: bold;
        }

        input[type="text"], input[type="email"], input[type="password"] {
            width: 100%;
            padding: 14px;
            padding-right: 40px; 
            border-radius: 6px;
            border: 1px solid #444; 
            background-color: #1a1a1a; 
            color: #ffffff; 
            box-sizing: border-box;
            font-size: 15px;
            transition: 0.2s;
        }

        input:focus { 
            outline: none; 
            border-color: #666; 
            background-color: #222; 
        }

        /* --- 按钮样式 --- */
        .submit-btn {
            width: 100%;
            padding: 15px;
            background-color: #3b5998; 
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: 0.2s;
            margin-top: 10px;
        }
        .submit-btn:hover { background-color: #2d4373; }
        .submit-btn:disabled { background-color: #444; color: #888; cursor: not-allowed; }

        .resend-btn {
            width: 100%;
            padding: 15px;
            background-color: transparent;
            color: #888888;
            border: none;
            font-size: 14px;
            cursor: pointer;
            margin-top: 5px;
        }
        .resend-btn.active { color: #3b5998; font-weight: bold; }

        .back-to-login {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: #aaaaaa;
            font-size: 13px;
            text-decoration: none;
        }
        .back-to-login:hover { color: white; text-decoration: underline; }

        /* 核心魔法：用来隐藏画面的类 */
        .hidden { display: none !important; }

        /* --- 账号选择卡片样式 (图 4 专用) --- */
        .account-card {
            background-color: #333333;
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 15px;
            cursor: pointer;
            transition: background-color 0.2s, border-color 0.2s;
            border: 1px solid transparent;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .account-card:hover { 
            background-color: #3f3f3f; 
            border-color: #666; 
        }
        .acc-name { 
            font-size: 16px; 
            font-weight: bold; 
            color: white; 
            margin-bottom: 4px; 
        }
        .acc-subname { 
            font-size: 13px; 
            color: #aaaaaa; 
        }

        /* --- 密码可见切换按钮样式 --- */
        .password-input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }
        
        .password-input-wrapper input {
            width: 100%;
            padding-right: 40px; 
        }
        
        .toggle-pwd-btn {
            position: absolute;
            right: 12px;
            background: none;
            border: none;
            font-size: 16px;
            color: #888888; 
            cursor: pointer;
            padding: 0;
            outline: none;
            transition: color 0.2s;
            z-index: 10;
        }
        
        .toggle-pwd-btn:hover {
            color: #ffffff; 
        }

    </style>
</head>
<body>

    <div class="recovery-container">
        
        <div id="view-1">
            <h2>PlayLearn Account Recovery</h2>
            <div class="form-group">
                <label>Username / Email</label>
                <input type="text" id="identifierInput" placeholder="Enter your username or email" oninput="resetInputState()">
                <div id="recoveryError" style="color: #ff4d4d; font-size: 13px; margin-top: 5px; display: none;"></div>
            </div>
            <button class="submit-btn" id="btn-next-1" onclick="processStep1()" disabled>Next</button>
            <a href="login.php" class="back-to-login">Back to Login</a>
        </div>

        <div id="view-2" class="hidden">
            <h2>PlayLearn Account Recovery</h2>
            <div class="user-display-email" id="displayEmailText">user@gmail.com</div>
            <p class="instruction-text">We just sent a 6-digit verification code to the email address you provided, if it's associated with an account. Check all your folders, including spam.</p>
            
            <div class="form-group">
                <label>Enter the code</label>
                <input type="text" id="otpCode" placeholder="6-digit code" maxlength="6" oninput="resetOTPState()">
                <div id="otpError" style="color: #ff4d4d; font-size: 13px; margin-top: 5px; display: none;"></div>
            </div>
            <button class="submit-btn" id="btn-verify" onclick="processVerify()" disabled>Verify</button>
            <button class="resend-btn" id="resendBtn" disabled onclick="resendOtp()">Resend code in 30s</button>
            <a href="javascript:void(0)" class="back-to-login" onclick="goBackTo1()">Start Over</a>
        </div>

        <div id="view-3" class="hidden">
            <h2>PlayLearn Account Recovery</h2>
            <div class="user-display-name" id="displayNameText">Username</div>
            <div class="user-display-subname" id="displaySubnameText">@username</div>
            
            <p class="instruction-text">Enter an email that may be associated with your account. If you made a purchase before, you can try your billing email.</p>
            
            <div class="form-group">
                <label>Email</label>
                <input type="email" id="associatedEmail" placeholder="Enter email" oninput="resetEmailInputState()">
                <div id="emailMatchError" style="color: #ff4d4d; font-size: 13px; margin-top: 5px; display: none;"></div>
            </div>
            <button class="submit-btn" id="btn-next-3" onclick="processStep3()" disabled>Next</button>
            <a href="javascript:void(0)" class="back-to-login" onclick="goBackTo1()">Start Over</a>
        </div>

        <div id="view-4" class="hidden">
            <h2>Reset Password</h2>
            <p class="instruction-text">Choose the account you want to recover.</p>
            
            <div id="account-list-container"></div>
            
            <a href="javascript:void(0)" class="back-to-login" onclick="goBackTo1()">Cancel</a>
        </div>

        <div id="view-5" class="hidden">
            <h2>Reset Password</h2>
            <div class="user-display-name" id="resetNameText">Username</div>
            <div class="user-display-subname" id="resetSubnameText">@username</div>
            
            <p class="instruction-text">Create a strong password that's different from your old one.</p>
            
            <div class="form-group">
                <label>New password</label>
                <div class="password-input-wrapper">
                    <input type="password" id="newPassword" placeholder="Do not use your old password" oninput="checkNewPassword()">
                    <button type="button" class="toggle-pwd-btn" onclick="togglePasswordVisibility('newPassword', this)">
                        <i class="fas fa-eye-slash"></i>
                    </button>
                </div>
                <div id="pwdLengthError" style="color: #ff4d4d; font-size: 13px; margin-top: 5px; display: none;">Passwords must be between 8 and 200 characters long.</div>
            </div>
            
            <div class="form-group">
                <label>Confirm new password</label>
                <div class="password-input-wrapper">
                    <input type="password" id="confirmPassword" placeholder="Confirm new password" oninput="checkNewPassword()">
                    <button type="button" class="toggle-pwd-btn" onclick="togglePasswordVisibility('confirmPassword', this)">
                        <i class="fas fa-eye-slash"></i>
                    </button>
                </div>
                <div id="pwdMatchError" style="color: #ff4d4d; font-size: 13px; margin-top: 5px; display: none;">Passwords do not match.</div>
            </div>
            
            <button class="submit-btn" id="btn-update-pwd" onclick="submitNewPassword()" disabled>Update password</button>
            <a href="javascript:void(0)" class="back-to-login" onclick="goBackTo1()">Cancel</a>
        </div>

    </div>

    <script>
        let currentRealEmail = "";
        let maskedEmailFromBackend = ""; 
        
        // === ✅ 新增功能：真正向服务器请求发送 OTP 邮件 ===
        function sendOtpEmail(email) {
            let formData = new FormData();
            formData.append('email', email);

            // 调用后端真正发邮件的接口
            fetch('send_otp.php', { method: 'POST', body: formData })
            .then(response => response.json())
            .then(data => {
                if (data.status !== "success") {
                    console.error("Failed to send email:", data.message);
                    alert("⚠️ Notice: Email sending failed. Please check your mail server configuration.");
                } else {
                    console.log("OTP Email successfully sent!");
                }
            })
            .catch(error => {
                console.error('Error sending email:', error);
            });
        }

        // === ✅ 新增功能：点击重发按钮触发真实邮件重发 ===
        function resendOtp() {
            // 重新开启倒计时
            startCountdown(); 
            // 真正发邮件
            sendOtpEmail(currentRealEmail); 
        }

        function resetInputState() {
            const val = document.getElementById('identifierInput').value.trim();
            const errorDiv = document.getElementById('recoveryError');
            const btn = document.getElementById('btn-next-1');

            if (val.length === 0) {
                errorDiv.style.display = 'none';
                btn.disabled = true;
            } else {
                btn.disabled = false;
            }
        }

        function resetOTPState() {
            document.getElementById('otpError').style.display = 'none';
            const val = document.getElementById('otpCode').value.trim();
            document.getElementById('btn-verify').disabled = (val.length < 6); 
        }

        function processVerify() {
            const enteredCode = document.getElementById('otpCode').value.trim();
            const btnVerify = document.getElementById('btn-verify');

            btnVerify.innerText = "Verifying...";
            btnVerify.disabled = true;

            let formData = new FormData();
            formData.append('otp_code', enteredCode);

            fetch('verify_otp.php', { method: 'POST', body: formData })
            .then(response => response.json())
            .then(data => {
                btnVerify.innerText = "Verify";
                
                if (data.status === "success") {
                    document.getElementById('view-2').classList.add('hidden');

                    if (window.tempAccounts && window.tempAccounts.length > 1) {
                        renderAccountCards(window.tempAccounts);
                        document.getElementById('view-4').classList.remove('hidden');
                    } 
                    else if (window.tempAccounts && window.tempAccounts.length === 1) {
                        const onlyAcc = window.tempAccounts[0];
                        selectAccount(onlyAcc.username, onlyAcc.username);
                    }
                    else {
                        alert("Session expired or no account found. Please try again.");
                        goBackTo1();
                    }
                } else {
                    document.getElementById('otpError').innerText = data.message;
                    document.getElementById('otpError').style.display = 'block';
                    btnVerify.disabled = true; 
                }
            })
            .catch(error => {
                btnVerify.innerText = "Verify";
                btnVerify.disabled = false;
                console.error('Error:', error);
                alert("Network error.");
            });
        }

        function resetEmailInputState() {
            document.getElementById('emailMatchError').style.display = 'none';
            const val = document.getElementById('associatedEmail').value.trim();
            document.getElementById('btn-next-3').disabled = !val.includes('@');
        }

        function processStep3() {
            const enteredEmail = document.getElementById('associatedEmail').value.trim();
            const btn3 = document.getElementById('btn-next-3');
            const errorDiv3 = document.getElementById('emailMatchError');

            btn3.innerText = "Checking...";
            btn3.disabled = true;

            setTimeout(() => {
                btn3.innerText = "Next";

                if (enteredEmail.toLowerCase() === currentRealEmail.toLowerCase()) {
                    document.getElementById('view-3').classList.add('hidden');
                    document.getElementById('displayEmailText').innerText = maskedEmailFromBackend;
                    document.getElementById('view-2').classList.remove('hidden');
                    startCountdown(); 
                    
                    // ✅ 核心修改：跳转到输入验证码页面时，真正发送邮件！
                    sendOtpEmail(currentRealEmail); 
                } else {
                    errorDiv3.innerText = "This email does not match the account.";
                    errorDiv3.style.display = 'block';
                    btn3.disabled = true; 
                }
            }, 500); 
        }

        function processStep1() {
            const inputVal = document.getElementById('identifierInput').value.trim();
            const btn = document.getElementById('btn-next-1');
            const errorDiv = document.getElementById('recoveryError');
            
            errorDiv.style.display = 'none';
            btn.innerText = "Checking...";
            btn.disabled = true;

            let formData = new FormData();
            formData.append('identifier', inputVal);

            fetch('verify_account.php', { method: 'POST', body: formData })
            .then(response => response.json())
            .then(data => {
                btn.innerText = "Next"; 

                if (data.status === "success") {
                    currentRealEmail = data.email; 
                    maskedEmailFromBackend = data.masked_email;
                    window.tempAccounts = data.accounts; 
                    const isEmailInput = inputVal.includes('@');

                    if (isEmailInput) {
                        document.getElementById('view-1').classList.add('hidden');
                        document.getElementById('displayEmailText').innerText = data.masked_email;
                        document.getElementById('view-2').classList.remove('hidden');
                        startCountdown();
                        
                        // ✅ 核心修改：如果是用邮箱找回，跳转到输入验证码页面时，真正发送邮件！
                        sendOtpEmail(currentRealEmail);
                    } else {
                        document.getElementById('view-1').classList.add('hidden');
                        document.getElementById('displayNameText').innerText = data.accounts[0].username;
                        document.getElementById('displaySubnameText').innerText = "@" + data.accounts[0].username.toLowerCase();
                        document.getElementById('view-3').classList.remove('hidden');
                    }
                } else {
                    errorDiv.innerText = data.message;
                    errorDiv.style.display = 'block';
                    btn.disabled = false; 
                }
            })
            .catch(error => {
                btn.innerText = "Next";
                btn.disabled = false;
                console.error('Error:', error);
                alert("Network error.");
            });
        }
        
        function renderAccountCards(accountsList) {
            const container = document.getElementById('account-list-container');
            container.innerHTML = ""; 

            accountsList.forEach(acc => {
                const card = document.createElement('div');
                card.className = 'account-card';
                
                card.onclick = function() {
                    selectAccount(acc.username, acc.username);
                };

                card.innerHTML = `
                    <div class="acc-name">${acc.username}</div>
                    <div class="acc-subname">@${acc.username.toLowerCase()}</div>
                `;
                container.appendChild(card);
            });
        }

        function goBackTo1() {
            document.getElementById('view-2').classList.add('hidden');
            document.getElementById('view-3').classList.add('hidden');
            document.getElementById('view-4').classList.add('hidden');
            document.getElementById('view-5').classList.add('hidden');
            
            document.getElementById('view-1').classList.remove('hidden');

            document.getElementById('identifierInput').value = "";   
            document.getElementById('otpCode').value = "";           
            document.getElementById('associatedEmail').value = "";   
            document.getElementById('newPassword').value = "";       
            document.getElementById('confirmPassword').value = "";   

            document.getElementById('btn-next-1').disabled = true;
            document.getElementById('btn-verify').disabled = true;   
            document.getElementById('btn-update-pwd').disabled = true;

            document.getElementById('recoveryError').style.display = 'none';
            document.getElementById('otpError').style.display = 'none';
            document.getElementById('emailMatchError').style.display = 'none';
            document.getElementById('pwdLengthError').style.display = 'none';
            document.getElementById('pwdMatchError').style.display = 'none';

            clearInterval(timer);
            const resendBtn = document.getElementById('resendBtn');
            if(resendBtn) {
                resendBtn.innerText = "Resend Code";
                resendBtn.disabled = true;
            }
        }

        let timer;
        function startCountdown() {
            let timeLeft = 30;
            const resendBtn = document.getElementById('resendBtn');
            resendBtn.disabled = true;
            resendBtn.classList.remove('active');
            clearInterval(timer);

            timer = setInterval(() => {
                timeLeft--;
                resendBtn.innerText = `Resend code in ${timeLeft}s`;

                if (timeLeft <= 0) {
                    clearInterval(timer);
                    resendBtn.innerText = "Resend Code";
                    resendBtn.disabled = false;
                    resendBtn.classList.add('active');
                }
            }, 1000);
        }

        window.onload = function() {
            const urlParams = new URLSearchParams(window.location.search);
            const userFromLogin = urlParams.get('user');

            if (userFromLogin) {
                document.getElementById('identifierInput').value = userFromLogin;
                resetInputState();
                processStep1();
            }
        }

        let accountToRecover = ""; 

        function selectAccount(displayName, subName) {
            accountToRecover = subName; 
            document.getElementById('resetNameText').innerText = displayName;
            document.getElementById('resetSubnameText').innerText = "@" + subName.toLowerCase();
            
            document.getElementById('view-4').classList.add('hidden');
            document.getElementById('view-2').classList.add('hidden'); 
            document.getElementById('view-5').classList.remove('hidden');
        }

        function checkNewPassword() {
            const pwd1 = document.getElementById('newPassword').value;
            const pwd2 = document.getElementById('confirmPassword').value;
            const btnUpdate = document.getElementById('btn-update-pwd');
            
            const lenError = document.getElementById('pwdLengthError');
            const matchError = document.getElementById('pwdMatchError');

            let isLengthValid = (pwd1.length >= 8 && pwd1.length <= 200);
            
            if (pwd1.length > 0 && !isLengthValid) {
                lenError.style.display = 'block';
            } else {
                lenError.style.display = 'none';
            }

            let isMatch = (pwd1 === pwd2);
            
            if (pwd2.length > 0 && !isMatch) {
                matchError.style.display = 'block';
            } else {
                matchError.style.display = 'none';
            }

            if (isLengthValid && isMatch && pwd2.length > 0) {
                btnUpdate.disabled = false;
            } else {
                btnUpdate.disabled = true;
            }
        }

        function submitNewPassword() {
            const finalPwd = document.getElementById('newPassword').value;
            const btnUpdate = document.getElementById('btn-update-pwd');
            
            btnUpdate.innerText = "Updating...";
            btnUpdate.disabled = true;

            let formData = new FormData();
            formData.append('username', accountToRecover); 
            formData.append('new_password', finalPwd);    

            fetch('update_password.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === "success") {
                    alert("🎉 Success! Your password has been updated.\nPlease log in with your new password.");
                    window.location.href = 'login.php';
                } else {
                    alert("❌ Error: " + data.message);
                    btnUpdate.innerText = "Update password";
                    btnUpdate.disabled = false;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert("Network error. Could not update password.");
                btnUpdate.innerText = "Update password";
                btnUpdate.disabled = false;
            });
        }

        function togglePasswordVisibility(inputId, btnElement) {
            const inputField = document.getElementById(inputId);
            const iconElement = btnElement.querySelector('i'); 
            
            if (inputField.type === "password") {
                inputField.type = "text";       
                iconElement.classList.remove('fa-eye-slash');
                iconElement.classList.add('fa-eye');
            } else {
                inputField.type = "password";   
                iconElement.classList.remove('fa-eye');
                iconElement.classList.add('fa-eye-slash');
>>>>>>> 4f5123f4f40ff24341cfcb8ebd54648461a4dfed
            }
        }
    </script>
    <style>
        .eye-ball { transition: height 0.15s ease-out; }
        .blink { height: 2px !important; overflow: hidden; }
        .blink .pupil { opacity: 0; }
        .smooth-transform { transition: transform 0.7s ease-in-out, left 0.7s ease-in-out, top 0.7s ease-in-out, height 0.7s ease-in-out; }
        
        .signup-header-btn {
            position: absolute; top: 15px; right: 20px; z-index: 1000;
            width: 90px; height: 34px; display: flex; justify-content: center; align-items: center;
            background-color: white; border: 1px solid #e2e8f0; border-radius: 8px;
            font-weight: bold; font-size: 13px; cursor: pointer; transition: 0.2s;
            text-decoration: none; color: black; box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        .signup-header-btn:hover { background-color: #f8fafc; }

        .error-text { color: #ef4444; font-size: 0.75rem; margin-top: 4px; display: none; }

        #toast-container { position: fixed; top: 20px; right: 20px; z-index: 99999; display: flex; flex-direction: column; gap: 10px; pointer-events: none; }
        .custom-toast { pointer-events: auto; min-width: 260px; padding: 12px 20px; border-radius: 10px; background: #ffffff; box-shadow: 0 10px 30px rgba(0,0,0,0.15); color: #333333; font-family: 'Segoe UI', sans-serif; font-size: 13px; font-weight: 600; display: flex; align-items: center; gap: 10px; transform: translateX(120%); transition: transform 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55), opacity 0.4s ease; opacity: 0; border-left: 5px solid transparent; }
        .custom-toast.show { transform: translateX(0); opacity: 1; }
        .custom-toast.success { border-left-color: #10b981; } 
        .custom-toast.error { border-left-color: #ef4444; }   
        .toast-icon { font-size: 18px; }
        .custom-toast.success .toast-icon { color: #10b981; }
        .custom-toast.error .toast-icon { color: #ef4444; }

        /* 专门为视图切换加的 */
        .hidden-view { display: none !important; }
        
        .account-card {
            padding: 12px 16px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            margin-bottom: 10px;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            flex-direction: column;
            background-color: #ffffff;
        }
        .account-card:hover {
            background-color: #f8fafc;
            border-color: #cbd5e1;
            box-shadow: 0 2px 4px rgba(0,0,0,0.02);
        }
        .account-card.selected {
            border-color: #0f172a;
            border-width: 2px;
            background-color: #f8fafc;
        }
    </style>
</head>
<body class="h-screen w-full flex bg-background font-sans text-foreground overflow-hidden">

<div id="toast-container"></div>
<a href="login.php" class="signup-header-btn">Log In</a>

<div class="h-screen w-full grid lg:grid-cols-2">
    
    <div class="relative hidden lg:flex flex-col justify-between bg-gradient-to-br from-primary/90 via-primary to-primary/80 p-12 text-primary-foreground h-full overflow-hidden">
        <div class="absolute inset-0" style="background-image: radial-gradient(#334155 1px, transparent 1px); background-size: 24px 24px;"></div>
        <div class="relative z-20 flex items-center gap-2 text-lg font-semibold">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m12 3-1.912 5.813a2 2 0 0 1-1.275 1.275L3 12l5.813 1.912a2 2 0 0 1 1.275 1.275L12 21l1.912-5.813a2 2 0 0 1 1.275-1.275L21 12l-5.813-1.912a2 2 0 0 1-1.275-1.275L12 3Z"/></svg>
            <span>PlayLearn</span>
        </div>
        <div class="relative z-20 flex items-end justify-center h-[500px]">
            <div class="relative" style="width: 550px; height: 400px;">
                <div id="char-purple" class="absolute bottom-0 smooth-transform" style="left: 70px; width: 180px; height: 400px; background-color: #6C3FF5; border-radius: 10px 10px 0 0; z-index: 1; transform-origin: bottom center;">
                    <div id="eyes-purple" class="absolute flex gap-8 smooth-transform" style="left: 45px; top: 40px;">
                        <div class="eye-ball w-[18px] h-[18px] rounded-full bg-white flex items-center justify-center overflow-hidden"><div class="pupil w-[7px] h-[7px] bg-[#2D2D2D] rounded-full transition-transform duration-100 ease-out" data-max="5"></div></div>
                        <div class="eye-ball w-[18px] h-[18px] rounded-full bg-white flex items-center justify-center overflow-hidden"><div class="pupil w-[7px] h-[7px] bg-[#2D2D2D] rounded-full transition-transform duration-100 ease-out" data-max="5"></div></div>
                    </div>
                </div>
                <div id="char-black" class="absolute bottom-0 smooth-transform" style="left: 240px; width: 120px; height: 310px; background-color: #2D2D2D; border-radius: 8px 8px 0 0; z-index: 2; transform-origin: bottom center;">
                    <div id="eyes-black" class="absolute flex gap-6 smooth-transform" style="left: 26px; top: 32px;">
                        <div class="eye-ball w-[16px] h-[16px] rounded-full bg-white flex items-center justify-center overflow-hidden"><div class="pupil w-[6px] h-[6px] bg-[#2D2D2D] rounded-full transition-transform duration-100 ease-out" data-max="4"></div></div>
                        <div class="eye-ball w-[16px] h-[16px] rounded-full bg-white flex items-center justify-center overflow-hidden"><div class="pupil w-[6px] h-[6px] bg-[#2D2D2D] rounded-full transition-transform duration-100 ease-out" data-max="4"></div></div>
                    </div>
                </div>
                <div id="char-orange" class="absolute bottom-0 smooth-transform" style="left: 0px; width: 240px; height: 200px; background-color: #FF9B6B; border-radius: 120px 120px 0 0; z-index: 3; transform-origin: bottom center;">
                    <div id="eyes-orange" class="absolute flex gap-8 transition-all duration-200 ease-out" style="left: 82px; top: 90px;">
                        <div class="pupil w-[12px] h-[12px] bg-[#2D2D2D] rounded-full transition-transform duration-100 ease-out" data-max="5"></div>
                        <div class="pupil w-[12px] h-[12px] bg-[#2D2D2D] rounded-full transition-transform duration-100 ease-out" data-max="5"></div>
                    </div>
                </div>
                <div id="char-yellow" class="absolute bottom-0 smooth-transform" style="left: 310px; width: 140px; height: 230px; background-color: #E8D754; border-radius: 70px 70px 0 0; z-index: 4; transform-origin: bottom center;">
                    <div id="eyes-yellow" class="absolute flex gap-6 transition-all duration-200 ease-out" style="left: 52px; top: 40px;">
                        <div class="pupil w-[12px] h-[12px] bg-[#2D2D2D] rounded-full transition-transform duration-100 ease-out" data-max="5"></div>
                        <div class="pupil w-[12px] h-[12px] bg-[#2D2D2D] rounded-full transition-transform duration-100 ease-out" data-max="5"></div>
                    </div>
                    <div id="mouth-yellow" class="absolute w-20 h-[4px] bg-[#2D2D2D] rounded-full transition-all duration-200 ease-out" style="left: 40px; top: 88px;"></div>
                </div>
            </div>
        </div>
        <div class="relative z-50 flex items-center gap-8 text-sm text-primary-foreground/60">
            <a href="privacy.php" class="hover:text-white transition-colors cursor-pointer">Privacy Policy</a>
            <a href="terms.php" class="hover:text-white transition-colors cursor-pointer">Terms of Service</a>
        </div>
        <div class="absolute inset-0 bg-white/[0.05] pointer-events-none" style="background-image: radial-gradient(#fff 1px, transparent 1px); background-size: 24px 24px; opacity: 0.1;"></div>
    </div>

    <div class="h-full w-full overflow-y-auto bg-background flex flex-col justify-center items-center px-6 lg:px-12 relative pt-[80px] lg:pt-0 pb-10">
        <div class="w-full max-w-[420px] shrink-0 my-auto">
            
            <div id="view-1">
                <div class="text-center mb-8">
                    <h1 class="text-3xl font-bold tracking-tight mb-2">Account Recovery</h1>
                    <p class="text-muted-foreground text-sm">Enter your username or email</p>
                </div>
                <div class="space-y-4">
                    <div class="space-y-1.5 relative z-10">
                        <label class="text-sm font-medium">Username / Email</label>
                        <input type="text" id="identifierInput" placeholder="Enter your identifier" oninput="resetInputState()" class="flex w-full h-11 px-3 py-2 text-sm bg-background border border-border/60 rounded-md focus:outline-none focus:ring-2 focus:ring-primary">
                    </div>
                    <button id="btn-next-1" onclick="processStep1()" disabled class="inline-flex items-center justify-center w-full h-11 text-sm font-bold bg-primary text-primary-foreground rounded-md hover:bg-primary/90 transition-colors disabled:opacity-50 disabled:cursor-not-allowed mt-4 relative z-10">Next</button>
                </div>
                <div class="text-center text-sm text-muted-foreground mt-6 relative z-10">Remembered your password? <a href="login.php" class="text-foreground font-bold hover:underline">Log In</a></div>
            </div>

            <div id="view-2" class="hidden-view">
                <div class="text-center mb-8">
                    <h1 class="text-3xl font-bold tracking-tight mb-2">Verify OTP</h1>
                    <p class="text-muted-foreground text-sm">Code sent to <span id="displayEmailText" class="font-bold text-primary"></span></p>
                </div>
                <div class="space-y-4">
                    <div id="otp_section" class="p-4 border border-dashed border-border/60 rounded-md bg-slate-50 transition-all duration-300 relative z-10">
                        <label class="text-sm font-medium block mb-2 text-center">Verification Code</label>
                        <div class="flex flex-col items-center gap-3">
                            <input type="text" id="otpCode" placeholder="6-digit code" maxlength="6" oninput="resetOTPState()" class="w-32 h-10 px-3 py-2 text-sm text-center tracking-widest bg-background border border-border/60 rounded-md focus:outline-none focus:ring-2 focus:ring-primary">
                        </div>
                    </div>
                    <button id="btn-verify" onclick="processVerify()" disabled class="inline-flex items-center justify-center w-full h-11 text-sm font-bold bg-primary text-primary-foreground rounded-md hover:bg-primary/90 transition-colors disabled:opacity-50 disabled:cursor-not-allowed mt-4 relative z-10">Verify</button>
                    <button id="resendBtn" disabled class="w-full text-sm text-muted-foreground mt-2 disabled:opacity-50 transition-colors">Resend code in 30s</button>
                </div>
                <div class="text-center text-sm text-muted-foreground mt-6 relative z-10"><a href="javascript:void(0)" onclick="goBackTo1()" class="text-foreground font-bold hover:underline">Start Over</a></div>
            </div>

            <div id="view-3" class="hidden-view">
                <div class="text-center mb-8">
                    <h1 class="text-3xl font-bold tracking-tight mb-2" id="displayNameText">Username</h1>
                    <p class="text-muted-foreground text-sm" id="displaySubnameText">@username</p>
                </div>
                <div class="space-y-4">
                    <p class="text-sm text-slate-600 mb-4">Enter an email that may be associated with your account.</p>
                    <div class="space-y-1.5 relative z-10">
                        <label class="text-sm font-medium">Email</label>
                        <input type="email" id="associatedEmail" placeholder="Enter email" oninput="resetEmailInputState()" class="flex w-full h-11 px-3 py-2 text-sm bg-background border border-border/60 rounded-md focus:outline-none focus:ring-2 focus:ring-primary">
                    </div>
                    <button id="btn-next-3" onclick="processStep3()" disabled class="inline-flex items-center justify-center w-full h-11 text-sm font-bold bg-primary text-primary-foreground rounded-md hover:bg-primary/90 transition-colors disabled:opacity-50 disabled:cursor-not-allowed mt-4 relative z-10">Next</button>
                </div>
                <div class="text-center text-sm text-muted-foreground mt-6 relative z-10"><a href="javascript:void(0)" onclick="goBackTo1()" class="text-foreground font-bold hover:underline">Start Over</a></div>
            </div>

            <div id="view-4" class="hidden-view">
                <div class="text-center mb-6">
                    <h1 class="text-3xl font-bold tracking-tight mb-2">Select Account</h1>
                    <p class="text-muted-foreground text-sm">Choose the account you want to recover.</p>
                </div>
                <div class="space-y-2" id="account-list-container">
                    </div>
                <div class="text-center text-sm text-muted-foreground mt-6 relative z-10"><a href="javascript:void(0)" onclick="goBackTo1()" class="text-foreground font-bold hover:underline">Cancel</a></div>
            </div>

            <div id="view-5" class="hidden-view">
                <div class="text-center mb-6">
                    <h1 class="text-3xl font-bold tracking-tight mb-2">Reset Password</h1>
                    <p class="text-muted-foreground text-sm">For <span id="resetNameText" class="font-bold text-primary"></span> (<span id="resetSubnameText"></span>)</p>
                </div>
                <div class="space-y-4">
                    <div class="space-y-1.5 relative z-10">
                        <label class="text-sm font-medium">New Password</label>
                        <div class="relative">
                            <input type="password" id="newPassword" autocomplete="new-password" placeholder="Min 8 characters" oninput="checkNewPassword()" class="flex w-full h-11 px-3 py-2 pr-10 text-sm bg-background border border-border/60 rounded-md focus:outline-none focus:ring-2 focus:ring-primary">
                            <button type="button" class="absolute right-3 top-1/2 -translate-y-1/2 text-muted-foreground hover:text-foreground" onclick="togglePwd('newPassword', this)">
                                <svg class="eye-icon" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"/><path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"/><path d="M6.61 6.61A13.526 13.526 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"/><line x1="2" x2="22" y1="2" y2="22"/></svg>
                            </button>
                        </div>
                        <div id="pwdLengthError" class="error-text">Passwords must be between 8 and 200 characters long.</div>
                    </div>
                    
                    <div class="space-y-1.5 relative z-10">
                        <label class="text-sm font-medium">Confirm Password</label>
                        <div class="relative">
                            <input type="password" id="confirmPassword" autocomplete="new-password" placeholder="Confirm new password" oninput="checkNewPassword()" class="flex w-full h-11 px-3 py-2 pr-10 text-sm bg-background border border-border/60 rounded-md focus:outline-none focus:ring-2 focus:ring-primary">
                            <button type="button" class="absolute right-3 top-1/2 -translate-y-1/2 text-muted-foreground hover:text-foreground" onclick="togglePwd('confirmPassword', this)">
                                <svg class="eye-icon" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"/><path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"/><path d="M6.61 6.61A13.526 13.526 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"/><line x1="2" x2="22" y1="2" y2="22"/></svg>
                            </button>
                        </div>
                        <div id="pwdMatchError" class="error-text">Passwords do not match.</div>
                    </div>

                    <button id="btn-update-pwd" onclick="submitNewPassword()" disabled class="inline-flex items-center justify-center w-full h-11 text-sm font-bold bg-primary text-primary-foreground rounded-md hover:bg-primary/90 transition-colors disabled:opacity-50 disabled:cursor-not-allowed mt-4 relative z-10">Update Password</button>
                </div>
                <div class="text-center text-sm text-muted-foreground mt-6 relative z-10"><a href="javascript:void(0)" onclick="goBackTo1()" class="text-foreground font-bold hover:underline">Cancel</a></div>
            </div>

        </div>
    </div>
</div>

<script>
    // ---------------------------------------------------------
    // Toast Notification System
    // ---------------------------------------------------------
    function showToast(message, type = 'success') {
        const container = document.getElementById('toast-container');
        const toast = document.createElement('div');
        toast.className = `custom-toast ${type}`;
        const icon = type === 'success' ? '<i class="fas fa-check-circle toast-icon"></i>' : '<i class="fas fa-exclamation-circle toast-icon"></i>';
        toast.innerHTML = `${icon} <span>${message}</span>`;
        container.appendChild(toast);
        setTimeout(() => toast.classList.add('show'), 10);
        setTimeout(() => { toast.classList.remove('show'); setTimeout(() => toast.remove(), 400); }, 3000);
    }

    // ---------------------------------------------------------
    // Recovery Logic Variables
    // ---------------------------------------------------------
    let currentRealEmail = "";
    let maskedEmailFromBackend = ""; 
    let accountToRecover = "";

    // ---------------------------------------------------------
    // VIEW 1 Logic
    // ---------------------------------------------------------
    function resetInputState() {
        const val = document.getElementById('identifierInput').value.trim();
        const btn = document.getElementById('btn-next-1');
        btn.disabled = (val.length === 0);
    }

    function processStep1() {
        const inputVal = document.getElementById('identifierInput').value.trim();
        const btn = document.getElementById('btn-next-1');
        
        btn.innerText = "Checking...";
        btn.disabled = true;

        let formData = new FormData();
        formData.append('identifier', inputVal);

        fetch('verify_account.php', { method: 'POST', body: formData })
        .then(response => response.json())
        .then(data => {
            btn.innerText = "Next"; 
            if (data.status === "success") {
                window.tempAccounts = [{ username: data.username }]; 
                accountToRecover = data.username; 
                maskedEmailFromBackend = data.masked_email;

                document.getElementById('view-1').classList.add('hidden-view');
                document.getElementById('displayEmailText').innerText = data.masked_email;
                document.getElementById('view-2').classList.remove('hidden-view');
                startCountdown();
            } else {
                showToast(data.message, "error");
                btn.disabled = false; 
            }
        })
        .catch(error => {
            btn.innerText = "Next";
            btn.disabled = false;
            console.error('JS Error:', error);
            showToast("Network error.", "error");
        });
    }

    // ---------------------------------------------------------
    // VIEW 2 Logic
    // ---------------------------------------------------------
    function resetOTPState() {
        const val = document.getElementById('otpCode').value.trim();
        document.getElementById('btn-verify').disabled = (val.length < 6); 
    }

    function processVerify() {
        const enteredCode = document.getElementById('otpCode').value.trim();
        const btnVerify = document.getElementById('btn-verify');

        btnVerify.innerText = "Verifying...";
        btnVerify.disabled = true;

        let formData = new FormData();
        formData.append('otp_code', enteredCode);

        fetch('verify_otp.php', { method: 'POST', body: formData })
        .then(response => response.json())
        .then(data => {
            btnVerify.innerText = "Verify";
            
            if (data.status === "success") {
                document.getElementById('view-2').classList.add('hidden-view');

                if (window.tempAccounts && window.tempAccounts.length > 1) {
                    renderAccountCards(window.tempAccounts);
                    document.getElementById('view-4').classList.remove('hidden-view');
                } else if (window.tempAccounts && window.tempAccounts.length === 1) {
                    const onlyAcc = window.tempAccounts[0];
                    selectAccount(onlyAcc.username, onlyAcc.username); 
                } else {
                    showToast("Session expired. Please try again.", "error");
                    goBackTo1();
                }
            } else {
                showToast(data.message, "error");
                btnVerify.disabled = true; 
            }
        })
        .catch(error => {
            btnVerify.innerText = "Verify";
            btnVerify.disabled = false;
            console.error('Error:', error);
            showToast("Network error.", "error");
        });
    }

    // ---------------------------------------------------------
    // VIEW 3 Logic
    // ---------------------------------------------------------
    function resetEmailInputState() {
        const val = document.getElementById('associatedEmail').value.trim();
        document.getElementById('btn-next-3').disabled = !val.includes('@');
    }

    function processStep3() {
        const enteredEmail = document.getElementById('associatedEmail').value.trim();
        const btn3 = document.getElementById('btn-next-3');

        btn3.innerText = "Checking...";
        btn3.disabled = true;

        setTimeout(() => {
            btn3.innerText = "Next";
            if (enteredEmail.toLowerCase() === currentRealEmail.toLowerCase()) {
                document.getElementById('view-3').classList.add('hidden-view');
                document.getElementById('displayEmailText').innerText = maskedEmailFromBackend;
                document.getElementById('view-2').classList.remove('hidden-view');
                startCountdown(); 
            } else {
                showToast("This email does not match the account.", "error");
                btn3.disabled = true; 
            }
        }, 500); 
    }

    // ---------------------------------------------------------
    // VIEW 4 Logic
    // ---------------------------------------------------------
    function renderAccountCards(accountsList) {
        const container = document.getElementById('account-list-container');
        container.innerHTML = ""; 

        accountsList.forEach(acc => {
            const card = document.createElement('div');
            card.className = 'account-card';
            
            card.onclick = function() {
                selectAccount(acc.username, acc.username);
            };

            card.innerHTML = `
                <div class="text-sm font-bold text-primary">${acc.username}</div>
                <div class="text-xs text-muted-foreground">@${acc.username.toLowerCase()}</div>
            `;
            container.appendChild(card);
        });
    }

    function selectAccount(displayName, subName) {
        accountToRecover = subName; 
        document.getElementById('resetNameText').innerText = displayName;
        document.getElementById('resetSubnameText').innerText = "@" + subName.toLowerCase();
        
        document.getElementById('view-4').classList.add('hidden-view');
        document.getElementById('view-2').classList.add('hidden-view'); 
        
        document.getElementById('view-5').classList.remove('hidden-view');
    }

    // ---------------------------------------------------------
    // VIEW 5 Logic
    // ---------------------------------------------------------
    function checkNewPassword() {
        const pwd1 = document.getElementById('newPassword').value;
        const pwd2 = document.getElementById('confirmPassword').value;
        const btnUpdate = document.getElementById('btn-update-pwd');
        const lenError = document.getElementById('pwdLengthError');
        const matchError = document.getElementById('pwdMatchError');

        let isLengthValid = (pwd1.length >= 8 && pwd1.length <= 200);
        lenError.style.display = (pwd1.length > 0 && !isLengthValid) ? 'block' : 'none';

        let isMatch = (pwd1 === pwd2);
        matchError.style.display = (pwd2.length > 0 && !isMatch) ? 'block' : 'none';

        btnUpdate.disabled = !(isLengthValid && isMatch && pwd2.length > 0);
    }

    function submitNewPassword() {
        const finalPwd = document.getElementById('newPassword').value;
        const enteredOtp = document.getElementById('otpCode').value.trim(); 
        const btnUpdate = document.getElementById('btn-update-pwd');
        
        btnUpdate.innerText = "Updating...";
        btnUpdate.disabled = true;

        let formData = new FormData();
        formData.append('username', accountToRecover); 
        formData.append('new_password', finalPwd);    
        formData.append('otp_code', enteredOtp);      

        fetch('update_password.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === "success") {
                showToast("Password updated successfully!", "success");
                setTimeout(() => { window.location.href = 'login.php'; }, 1500);
            } else {
                showToast(data.message, "error");
                btnUpdate.innerText = "Update Password";
                btnUpdate.disabled = false;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast("Network error. Could not update password.", "error");
            btnUpdate.innerText = "Update Password";
            btnUpdate.disabled = false;
        });
    }

    function togglePwd(inputId, btn) {
        const input = document.getElementById(inputId);
        const type = input.type === 'password' ? 'text' : 'password';
        input.type = type;
        if(type === 'password') {
            btn.innerHTML = `<svg class="eye-icon" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"/><path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"/><path d="M6.61 6.61A13.526 13.526 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"/><line x1="2" x2="22" y1="2" y2="22"/></svg>`;
        } else {
            btn.innerHTML = `<svg class="eye-icon" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>`;
        }
        updatePupils();
    }

    // ---------------------------------------------------------
    // Global Navigation & Auto-fill
    // ---------------------------------------------------------
    function goBackTo1() {
        document.getElementById('view-2').classList.add('hidden-view');
        document.getElementById('view-3').classList.add('hidden-view');
        document.getElementById('view-4').classList.add('hidden-view');
        document.getElementById('view-5').classList.add('hidden-view');
        
        document.getElementById('view-1').classList.remove('hidden-view');

        document.getElementById('identifierInput').value = "";   
        document.getElementById('otpCode').value = "";           
        document.getElementById('associatedEmail').value = "";   
        document.getElementById('newPassword').value = "";       
        document.getElementById('confirmPassword').value = "";   

        document.getElementById('btn-next-1').disabled = true;
        document.getElementById('btn-verify').disabled = true;   
        document.getElementById('btn-update-pwd').disabled = true;

        document.getElementById('pwdLengthError').style.display = 'none';
        document.getElementById('pwdMatchError').style.display = 'none';

        clearInterval(timer);
        const resendBtn = document.getElementById('resendBtn');
        if(resendBtn) {
            resendBtn.innerText = "Resend Code";
            resendBtn.disabled = true;
        }
    }

    let timer;
    function startCountdown() {
        let timeLeft = 30;
        const resendBtn = document.getElementById('resendBtn');
        resendBtn.disabled = true;
        resendBtn.classList.remove('text-primary');
        clearInterval(timer);

        timer = setInterval(() => {
            timeLeft--;
            resendBtn.innerText = `Resend code in ${timeLeft}s`;

            if (timeLeft <= 0) {
                clearInterval(timer);
                resendBtn.innerText = "Resend Code";
                resendBtn.disabled = false;
                resendBtn.classList.add('text-primary');
            }
        }, 1000);
    }

    window.onload = function() {
        const urlParams = new URLSearchParams(window.location.search);
        const userFromLogin = urlParams.get('user');

        if (userFromLogin) {
            document.getElementById('identifierInput').value = userFromLogin;
            resetInputState();
            processStep1();
        }
    }

    // ---------------------------------------------------------
    // Animal Animation Logic (Matched with signup.php)
    // ---------------------------------------------------------
    let showPassword = false; 
    let mouseX = window.innerWidth / 2; let mouseY = window.innerHeight / 2;
    const charPurple = document.getElementById('char-purple'); const eyesPurple = document.getElementById('eyes-purple');
    const charBlack = document.getElementById('char-black'); const eyesBlack = document.getElementById('eyes-black');
    const charOrange = document.getElementById('char-orange'); const eyesOrange = document.getElementById('eyes-orange');
    const charYellow = document.getElementById('char-yellow'); const eyesYellow = document.getElementById('eyes-yellow');
    const mouthYellow = document.getElementById('mouth-yellow'); const pupils = document.querySelectorAll('.pupil');

    window.addEventListener('mousemove', (e) => { mouseX = e.clientX; mouseY = e.clientY; updatePupils(); updateBodyPos(); });
    
    function updatePupils() {
        pupils.forEach(pupil => {
            const rect = pupil.parentElement.getBoundingClientRect();
            const centerX = rect.left + rect.width / 2; const centerY = rect.top + rect.height / 2;
            let deltaX = mouseX - centerX; let deltaY = mouseY - centerY;
            
            // 如果是在图5且正在输入密码（睁开眼睛）
            const pwd1 = document.getElementById('newPassword');
            const pwd2 = document.getElementById('confirmPassword');
            const isActivePassword = !document.getElementById('view-5').classList.contains('hidden-view') && 
                                     ((pwd1.value.length > 0 && pwd1.type === 'text') || (pwd2.value.length > 0 && pwd2.type === 'text'));

            if (isActivePassword) { deltaX = -50; deltaY = -40; }
            
            const maxDistance = parseFloat(pupil.getAttribute('data-max') || 5);
            const distance = Math.min(Math.sqrt(deltaX ** 2 + deltaY ** 2), maxDistance);
            const angle = Math.atan2(deltaY, deltaX);
            pupil.style.transform = `translate(${Math.cos(angle) * distance}px, ${Math.sin(angle) * distance}px)`;
        });
    }
    
    function calculatePos(el) { if(!el) return { faceX: 0, faceY: 0, skew: 0 }; const rect = el.getBoundingClientRect(); const deltaX = mouseX - (rect.left + rect.width / 2); const deltaY = mouseY - (rect.top + rect.height / 3); return { faceX: Math.max(-15, Math.min(15, deltaX / 20)), faceY: Math.max(-10, Math.min(10, deltaY / 30)), skew: Math.max(-6, Math.min(6, -deltaX / 120)) }; }
    
    function updateBodyPos() {
        const pPos = calculatePos(charPurple); const bPos = calculatePos(charBlack); const oPos = calculatePos(charOrange); const yPos = calculatePos(charYellow);
        
        const pwd1 = document.getElementById('newPassword');
        const pwd2 = document.getElementById('confirmPassword');
        const isActivePassword = !document.getElementById('view-5').classList.contains('hidden-view') && 
                                 ((pwd1.value.length > 0 && pwd1.type === 'text') || (pwd2.value.length > 0 && pwd2.type === 'text'));

        if (isActivePassword) { 
            charPurple.style.transform = `skewX(0deg)`; eyesPurple.style.left = '20px'; eyesPurple.style.top = '35px'; 
            charBlack.style.transform = `skewX(0deg)`; eyesBlack.style.left = '10px'; eyesBlack.style.top = '28px'; 
            charOrange.style.transform = `skewX(0deg)`; eyesOrange.style.left = '50px'; eyesOrange.style.top = '85px'; 
            charYellow.style.transform = `skewX(0deg)`; eyesYellow.style.left = '20px'; eyesYellow.style.top = '35px'; mouthYellow.style.left = '10px'; mouthYellow.style.top = '88px'; 
        } else { 
            charPurple.style.transform = `skewX(${pPos.skew}deg)`; eyesPurple.style.left = `${45 + pPos.faceX}px`; eyesPurple.style.top = `${40 + pPos.faceY}px`; 
            charBlack.style.transform = `skewX(${bPos.skew}deg)`; eyesBlack.style.left = `${26 + bPos.faceX}px`; eyesBlack.style.top = `${32 + bPos.faceY}px`; 
            charOrange.style.transform = `skewX(${oPos.skew}deg)`; eyesOrange.style.left = `${82 + oPos.faceX}px`; eyesOrange.style.top = `${90 + oPos.faceY}px`; 
            charYellow.style.transform = `skewX(${yPos.skew}deg)`; eyesYellow.style.left = `${52 + yPos.faceX}px`; eyesYellow.style.top = `${40 + yPos.faceY}px`; mouthYellow.style.left = `${40 + yPos.faceX}px`; mouthYellow.style.top = `${88 + yPos.faceY}px`; 
        }
    }
    
    function blinkLoop(id) { const eyes = document.querySelectorAll(`#${id} .eye-ball`); if(!eyes.length) return; setInterval(() => { eyes.forEach(e => e.classList.add('blink')); setTimeout(() => eyes.forEach(e => e.classList.remove('blink')), 150); }, Math.random() * 4000 + 3000); }
    blinkLoop('eyes-purple'); blinkLoop('eyes-black');
</script>
</body>
</html>