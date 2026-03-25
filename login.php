<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <title>PLAYLEARN - Log In</title>
    <style>
        /* --- 继承你之前的完美样式 --- */
        body {
            background-color: #151313; 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        /* 右上角变成跳回注册页的按钮 */
        .signup-header-btn {
            position: absolute;
            top: 20px;
            right: 30px;
            
            width: 100px;         
            height: 38px;          
            display: flex;         
            justify-content: center;
            align-items: center;
            box-sizing: border-box; 
         

            background-color: white;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-weight: bold;
            font-size: 14px;       
            cursor: pointer;
            transition: 0.2s;
            text-decoration: none;
            color: black;
        }
        .signup-header-btn:hover { background-color: #f0f0f0; }

        .logo {
            text-align: center;
            font-size: 40px; 
            font-weight: 900;
            margin-bottom: 25px; 
            letter-spacing: 2px;
            color: #ffffff; 
        }

        .login-container {
            background-color: #2b2b2b; 
            color: white;
            width: 400px;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            box-sizing: border-box;
        }

        .subtitle {
            text-align: center;
            font-size: 14px;
            margin-bottom: 30px;
            font-weight: bold;
        }

        .form-group {
            margin-bottom: 20px;
            display: flex;
            flex-direction: column;
        }

        .form-group label {
            font-size: 15px;
            margin-bottom: 5px;
            color: #dcdcdc;
        }

        /* --- 升级版输入框样式 --- */
        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 12px;
            padding-right: 40px; /* ✅ 留出小眼睛的空间 */
            border-radius: 6px;
            border: 1px solid #555;
            background-color: #1a1a1a;
            color: white;
            box-sizing: border-box;
            font-size: 14px;
            transition: background-color 0.2s;
        }
        input:focus { outline: none; border-color: #aaaaaa; }

        /* ✅ 小眼睛按钮专属样式 */
        .password-input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
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

        .submit-btn {
            width: 100%; padding: 15px; background-color: #fff; color: #000;
            border: none; border-radius: 8px; font-size: 16px; font-weight: bold;
            cursor: pointer; margin-top: 20px; transition: 0.2s;
        }
        .submit-btn:hover { background-color: #e6e6e6; }
        
        /* 禁用状态的灰色按钮 */
        .submit-btn:disabled {
            background-color: #555555 !important;
            color: #888888 !important;
            cursor: not-allowed;
        }

        /* 1. 框内新标题样式 */
        .login-header-title {
            text-align: center;
            font-size: 28px;
            font-weight: 900;
            margin-bottom: 30px;
            color: #ffffff;
            letter-spacing: 1px;
        }

        /* 2. 辅助链接容器 */
        .footer-links {
            text-align: center;
            margin-top: 20px;
        }

        .helper-link {
            color: #dcdcdc;
            font-size: 16px;
            text-decoration: none;
            transition: 0.2s;
        }
        .helper-link:hover { text-decoration: underline; color: #ffffff; }

        /* 3. 隔离线样式 */
        .separator-line {
            border: 0;
            border-top: 1px solid #555; /* 深灰色细线 */
            margin: 20px 0;
        }

        /* 4. 底部注册提示 */
        .signup-prompt {
            font-size: 16px;
            color: #dcdcdc;
        }

        .signup-link {
            color: #ffffff;
            font-weight: bold;
            text-decoration: none;
            margin-left: 5px;
        }
        .signup-link:hover { text-decoration: underline; }

        body {
            flex-direction: row; /* 或者直接删掉这一行，让它恢复默认居中 */
        }
    </style>
</head>
<body>

    <a href="signup.php" class="signup-header-btn">Sign Up</a>

    <div class="login-container">
        <div class="login-header-title">Login to PlayLearn</div>

        <div class="form-group">
            <label>Username / Email</label>
            <input type="text" id="loginIdentifier" placeholder="Enter username or email" onkeyup="checkLoginValidity()">
        </div>

        <div class="form-group">
            <label>Password</label>
            <div class="password-input-wrapper">
                <input type="password" id="password" placeholder="Enter your password" onkeyup="checkLoginValidity()">
                
                <button type="button" class="toggle-pwd-btn" onclick="togglePasswordVisibility('password', this)">
                    <i class="fas fa-eye-slash"></i>
                </button>
            </div>
        </div>

        <button class="submit-btn" id="loginBtn" onclick="submitLogin()" disabled>Log In</button>

        <div class="footer-links">
            <a href="javascript:void(0)" onclick="goToRecovery()" class="helper-link">Forgot Password or Username?</a>
            
            <hr class="separator-line"> <div class="signup-prompt">
                Don't have an account? <a href="signup.php" class="signup-link">Sign Up</a>
            </div>
        </div>
    </div>
    <script>
        // 1. 检查两个框是否都填了，控制按钮亮起
        function checkLoginValidity() {
            const user = document.getElementById('loginIdentifier').value.trim()
            const pass = document.getElementById('password').value;
            const btn = document.getElementById('loginBtn');
            
            if (user !== "" && pass !== "") {
                btn.disabled = false;
            } else {
                btn.disabled = true;
            }
        }

        // 2. 发送数据给 process_login.php
        function submitLogin() {
            const user = document.getElementById('loginIdentifier').value.trim();
            const pass = document.getElementById('password').value;
            const btn = document.getElementById('loginBtn');

            btn.innerText = "Verifying...";
            btn.disabled = true;

            let formData = new FormData();
            formData.append('username', user);
            formData.append('password', pass);

            fetch('process_login.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                btn.innerText = "Log In";
                btn.disabled = false;

                if (data.status === "success") {
                    // ✅ 1. 使用后端返回的正式用户名 2. 增加页面跳转
                    const displayName = data.username ? data.username : user;
                    alert("🎉 Welcome back, " + displayName + "! Ready to play?");
                    window.location.href = "index.php"; // 跳转到主页
                } else {
                    alert("❌ Login failed: " + data.message);
                    document.getElementById('password').value = "";
                    checkLoginValidity(); 
                }
            })
            .catch(error => {
                btn.innerText = "Log In";
                btn.disabled = false;
                console.error('Error:', error);
                alert("Network connection error. Please try again.");
            });
        }

        function goToRecovery() {
            // 获取用户当前在框里打的字
            const currentInput = document.getElementById('loginIdentifier').value.trim();
            
            // 如果框里有字，就把它拼接在网址后面带过去
            if (currentInput !== "") {
                // encodeURIComponent 是为了防止用户输入奇怪的符号破坏网址
                window.location.href = "recovery.php?user=" + encodeURIComponent(currentInput);
            } else {
                // 如果框是空的，就正常跳转
                window.location.href = "recovery.php";
            }
        }

        // === 密码显示/隐藏 切换魔法 ===
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