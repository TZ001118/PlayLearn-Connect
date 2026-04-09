<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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
            }
        }
    </script>
</body>
</html>