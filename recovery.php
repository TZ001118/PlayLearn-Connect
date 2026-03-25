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
            padding-right: 40px; /* 给右边的小眼睛留出空间，防止文字被挡住 */
        }
        
        .toggle-pwd-btn {
            position: absolute;
            right: 12px;
            background: none;
            border: none;
            font-size: 16px;
            color: #888888; /* 调整为更高级的灰色 */
            cursor: pointer;
            padding: 0;
            outline: none;
            transition: color 0.2s;
            z-index: 10;
        }
        
        .toggle-pwd-btn:hover {
            color: #ffffff; /* 悬停时变白 */
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
            <button class="resend-btn" id="resendBtn" disabled>Resend code in 30s</button>
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
        

        // === 只要用户修改了输入框的内容，就执行这个 ===
        function resetInputState() {
            const val = document.getElementById('identifierInput').value.trim();
            const errorDiv = document.getElementById('recoveryError');
            const btn = document.getElementById('btn-next-1');

            // 只有当用户彻底清空输入框时，才主动隐藏错误提示
            if (val.length === 0) {
                errorDiv.style.display = 'none';
                btn.disabled = true;
            } else {
                btn.disabled = false;
            }
        }

        // === 图 2：打字时重置按钮状态 ===
        function resetOTPState() {
            document.getElementById('otpError').style.display = 'none';
            const val = document.getElementById('otpCode').value.trim();
            // 必须打满 6 位数，按钮才亮
            document.getElementById('btn-verify').disabled = (val.length < 6); 
        }

        // === 图 2 核心魔法：去后端核实验证码 ===
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
                    // 1. 验证成功，隐藏当前验证码界面
                    document.getElementById('view-2').classList.add('hidden');

                    // ✅ 核心逻辑增强：
                    // 检查 window.tempAccounts 是否存在且有数据
                    if (window.tempAccounts && window.tempAccounts.length > 1) {
                        // 场景 A：账户多于 1 个 -> 去 View 4 选账户
                        renderAccountCards(window.tempAccounts);
                        document.getElementById('view-4').classList.remove('hidden');
                    } 
                    else if (window.tempAccounts && window.tempAccounts.length === 1) {
                        // 场景 B：恰好只有 1 个账户 -> 直接去 View 5 改密码
                        const onlyAcc = window.tempAccounts[0];
                        selectAccount(onlyAcc.username, onlyAcc.username);
                    }
                    else {
                        // 场景 C：防御性逻辑，万一缓存丢了，提示并返回第一步
                        alert("Session expired or no account found. Please try again.");
                        goBackTo1();
                    }
                } else {
                    // 2. 验证失败逻辑
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

        // === 图 3：打字时重置按钮状态 ===
        function resetEmailInputState() {
            document.getElementById('emailMatchError').style.display = 'none';
            const val = document.getElementById('associatedEmail').value.trim();
            document.getElementById('btn-next-3').disabled = !val.includes('@');
        }

        // === 图 3 核心魔法：验证输入的邮箱对不对，对就去图 2 ===
        function processStep3() {
            const enteredEmail = document.getElementById('associatedEmail').value.trim();
            const btn3 = document.getElementById('btn-next-3');
            const errorDiv3 = document.getElementById('emailMatchError');

            btn3.innerText = "Checking...";
            btn3.disabled = true;

            // 这里用 setTimeout 假装思考了 0.5 秒，让 UX 体验更好
            setTimeout(() => {
                btn3.innerText = "Next";

                // 把用户打的邮箱，和我们大脑里记的真实邮箱对比 (全转小写)
                if (enteredEmail.toLowerCase() === currentRealEmail.toLowerCase()) {
                    // 答对了！隐藏图 3，前往图 2 (验证码页面)
                    document.getElementById('view-3').classList.add('hidden');
                    document.getElementById('displayEmailText').innerText = maskedEmailFromBackend;
                    document.getElementById('view-2').classList.remove('hidden');
                    startCountdown(); // 开启倒计时！
                } else {
                    // 答错了！弹出红字，按钮变灰
                    errorDiv3.innerText = "This email does not match the account.";
                    errorDiv3.style.display = 'block';
                    btn3.disabled = true; 
                }
            }, 500); 
        }

       // === 升级版核心魔法：先查数据库，再决定去哪 ===
        function processStep1() {
            const inputVal = document.getElementById('identifierInput').value.trim();
            const btn = document.getElementById('btn-next-1');
            const errorDiv = document.getElementById('recoveryError');
            
            // ✅ 新增：开始检查前，先确保红字是消失的，这样用户点第二次时会有“刷新”的感觉
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
                    // ... 这里的跳转逻辑保持不变 ...
                    currentRealEmail = data.email; 
                    maskedEmailFromBackend = data.masked_email;

                    window.tempAccounts = data.accounts; 
                    const isEmailInput = inputVal.includes('@');

                    if (isEmailInput) {
                        document.getElementById('view-1').classList.add('hidden');
                        document.getElementById('displayEmailText').innerText = data.masked_email;
                        document.getElementById('view-2').classList.remove('hidden');
                        startCountdown();
                    } else {
                        document.getElementById('view-1').classList.add('hidden');
                        document.getElementById('displayNameText').innerText = data.accounts[0].username;
                        document.getElementById('displaySubnameText').innerText = "@" + data.accounts[0].username.toLowerCase();
                        document.getElementById('view-3').classList.remove('hidden');
                    }
                } else {
                    // ✅ 关键：后端说没找到人，显示红字
                    errorDiv.innerText = data.message;
                    errorDiv.style.display = 'block';
                    // 这里不需要 btn.disabled = true，让用户可以直接改了再点
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
        
        // === 核心：根据数据库结果动态生成 HTML 卡片 ===
        function renderAccountCards(accountsList) {
            const container = document.getElementById('account-list-container');
            container.innerHTML = ""; // 清空

            accountsList.forEach(acc => {
                const card = document.createElement('div');
                card.className = 'account-card';
                
                // ✅ 修正后的点击事件：选好账号后，直接跳到图 5 设置新密码
                card.onclick = function() {
                    // 调用我们写好的选择函数，它会自动隐藏图 4 并显示图 5
                    selectAccount(acc.username, acc.username);
                };

                card.innerHTML = `
                    <div class="acc-name">${acc.username}</div>
                    <div class="acc-subname">@${acc.username.toLowerCase()}</div>
                `;
                container.appendChild(card);
            });
        }

        // === 返回第一步 (Start Over / Cancel) ===
        // === 返回第一步 (Start Over / Cancel) ===
        function goBackTo1() {
            // 1. 隐藏所有后续视图
            document.getElementById('view-2').classList.add('hidden');
            document.getElementById('view-3').classList.add('hidden');
            document.getElementById('view-4').classList.add('hidden');
            document.getElementById('view-5').classList.add('hidden');
            
            // 2. 显示图 1
            document.getElementById('view-1').classList.remove('hidden');

            // ✅ 核心修复：彻底清空所有输入框的内容
            document.getElementById('identifierInput').value = "";   // 清空第一步输入
            document.getElementById('otpCode').value = "";           // 清空验证码 (解决你的问题)
            document.getElementById('associatedEmail').value = "";   // 清空安全检查邮箱
            document.getElementById('newPassword').value = "";       // 清空新密码框
            document.getElementById('confirmPassword').value = "";   // 清空确认密码框

            // ✅ 核心修复：重置所有按钮和错误提示的状态
            document.getElementById('btn-next-1').disabled = true;
            document.getElementById('btn-verify').disabled = true;   // 验证码按钮也重置
            document.getElementById('btn-update-pwd').disabled = true;

            // 隐藏所有残留的红字报错
            document.getElementById('recoveryError').style.display = 'none';
            document.getElementById('otpError').style.display = 'none';
            document.getElementById('emailMatchError').style.display = 'none';
            document.getElementById('pwdLengthError').style.display = 'none';
            document.getElementById('pwdMatchError').style.display = 'none';

            // 💡 额外贴心优化：停止倒计时（如果有的话）
            clearInterval(timer);
            const resendBtn = document.getElementById('resendBtn');
            if(resendBtn) {
                resendBtn.innerText = "Resend Code";
                resendBtn.disabled = true;
            }
        }

        // === 倒计时逻辑 (图2用的) ===
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

        // === 页面加载完毕后自动执行的终极魔法 ===
        window.onload = function() {
            // 解析网址里携带的参数
            const urlParams = new URLSearchParams(window.location.search);
            const userFromLogin = urlParams.get('user');

            // 如果发现有传过来的用户名或邮箱
            if (userFromLogin) {
                // 1. 自动帮用户填进输入框
                document.getElementById('identifierInput').value = userFromLogin;
                
                // 2. 触发一次状态重置，把 Next 按钮点亮
                resetInputState();
                
                // 3. 自动触发去数据库查询的函数！（就像一只无形的手帮你点了 Next）
                processStep1();
            }
        }

        let accountToRecover = ""; // 记住用户最终选了哪个账号

        // === 点击账号卡片后的动作 (图 4 -> 图 5) ===
        function selectAccount(displayName, subName) {
            accountToRecover = subName; 
            document.getElementById('resetNameText').innerText = displayName;
            document.getElementById('resetSubnameText').innerText = "@" + subName.toLowerCase();
            
            // 隐藏所有可能的前置画面
            document.getElementById('view-4').classList.add('hidden');
            document.getElementById('view-2').classList.add('hidden'); // 以防万一
            
            // 显示图 5 改密码
            document.getElementById('view-5').classList.remove('hidden');
        }

        // === 图 5：检查两次密码是否一致 ===
        // === 图 5：实时检查密码长度和一致性 ===
        function checkNewPassword() {
            const pwd1 = document.getElementById('newPassword').value;
            const pwd2 = document.getElementById('confirmPassword').value;
            const btnUpdate = document.getElementById('btn-update-pwd');
            
            const lenError = document.getElementById('pwdLengthError');
            const matchError = document.getElementById('pwdMatchError');

            // 1. 检查长度 (8-200位)
            let isLengthValid = (pwd1.length >= 8 && pwd1.length <= 200);
            
            if (pwd1.length > 0 && !isLengthValid) {
                lenError.style.display = 'block';
            } else {
                lenError.style.display = 'none';
            }

            // 2. 检查一致性
            // 只有当第二个框有内容时才显示“不匹配”红字，避免用户还没开始打字就报错
            let isMatch = (pwd1 === pwd2);
            
            if (pwd2.length > 0 && !isMatch) {
                matchError.style.display = 'block';
            } else {
                matchError.style.display = 'none';
            }

            // 3. 只有两个条件都满足，按钮才亮起
            if (isLengthValid && isMatch && pwd2.length > 0) {
                btnUpdate.disabled = false;
            } else {
                btnUpdate.disabled = true;
            }
        }

        // === 最终提交新密码！ ===
        function submitNewPassword() {
            const finalPwd = document.getElementById('newPassword').value;
            const btnUpdate = document.getElementById('btn-update-pwd');
            
            btnUpdate.innerText = "Updating...";
            btnUpdate.disabled = true;

            // 准备要发给后端的数据
            let formData = new FormData();
            formData.append('username', accountToRecover); // 我们之前记住的用户名
            formData.append('new_password', finalPwd);    // 新密码

            fetch('update_password.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === "success") {
                    // 🎉 成功！弹出提示并跳回登录页
                    alert("🎉 Success! Your password has been updated.\nPlease log in with your new password.");
                    window.location.href = 'login.php';
                } else {
                    // ❌ 失败
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
        // === 密码显示/隐藏 切换魔法 (FontAwesome 版) ===
        function togglePasswordVisibility(inputId, btnElement) {
            const inputField = document.getElementById(inputId);
            const iconElement = btnElement.querySelector('i'); // 获取按钮里的小图标
            
            if (inputField.type === "password") {
                inputField.type = "text";       
                // 变成明文，换成睁眼图标
                iconElement.classList.remove('fa-eye-slash');
                iconElement.classList.add('fa-eye');
            } else {
                inputField.type = "password";   
                // 变回密码，换回闭眼斜线图标
                iconElement.classList.remove('fa-eye');
                iconElement.classList.add('fa-eye-slash');
            }
        }
    </script>
</body>
</html>