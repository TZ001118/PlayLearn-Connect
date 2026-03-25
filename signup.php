<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <title>PLAYLEARN - Sign Up</title>
    <style>
        /* --- GLOBAL STYLES --- */
        body {
            background-color: #151313; 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            display: flex;
            flex-direction: column; 
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            position: relative;
        }

        .login-btn {
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
            color: black;
        }
        .login-btn:hover { background-color: #f0f0f0; }

        .signup-container {
            background-color: #2b2b2b; 
            color: white;
            width: 400px;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }

        /* 【修改2：加上 color: white，稍微调大一点字体和底部距离】 */
        .logo {
            text-align: center;
            font-size: 40px; 
            font-weight: 900;
            margin-bottom: 25px; 
            letter-spacing: 2px;
            color: #ffffff; 
        }

        .subtitle {
            text-align: center;
            font-size: 20px;
            margin-bottom: 30px;
            font-weight: 600;
            font-family: 'Fredoka', sans-serif;
        }

        .form-group {
            margin-bottom: 20px;
            display: flex;
            flex-direction: column;
        }

        .form-group label {
            font-size: 14px;
            margin-bottom: 5px;
            color: #dcdcdc;
        }

        /* --- 升级版输入框样式 --- */
        input[type="text"], input[type="password"], input[type="email"] {
            width: 100%;
            padding: 12px;
            /* ✅ 关键：给右侧留出 40px 的空间，防止密码文字和小眼睛重叠 */
            padding-right: 40px; 
            border-radius: 6px;
            border: 1px solid #555;
            background-color: #1a1a1a;
            color: white;
            box-sizing: border-box;
            font-size: 14px;
            transition: background-color 0.2s;
        }
        
        /* ✅ 新增：小眼睛按钮的专用样式 */
        .password-input-wrapper {
            position: relative; /* 核心：让父容器作为定位基准 */
            display: flex;
            align-items: center;
        }
        
        .toggle-pwd-btn {
            position: absolute; /* 核心：将按钮绝对定位在输入框右侧 */
            right: 12px;
            background: none;
            border: none;
            font-size: 16px; /* 调整图标大小 */
            color: #888888;  /* 默认灰色，不显眼 */
            cursor: pointer;
            padding: 0;
            outline: none;
            transition: color 0.2s;
            z-index: 10; /* 确保它在输入框上面 */
        }
        
        .toggle-pwd-btn:hover {
            color: #ffffff; /* 鼠标放上去变白 */
        }
        input:focus { outline: none; border-color: #aaaaaa; }

        .birthday-group {
            display: flex;
            gap: 10px;
        }

        .custom-select-container {
            flex: 1;
            position: relative; 
        }

        .custom-select-trigger {
            width: 100%;
            padding: 12px;
            border-radius: 6px;
            border: 1px solid #555;
            background-color: #1a1a1a; 
            color: #dcdcdc; 
            cursor: pointer;
            position: relative;
            box-sizing: border-box;
            font-size: 14px;
            transition: background-color 0.2s;
            user-select: none; 
        }

        .custom-select-trigger::after {
            content: '';
            position: absolute;
            top: 50%;
            right: 12px;
            transform: translateY(-50%);
            border-left: 5px solid transparent;
            border-right: 5px solid transparent;
            border-top: 5px solid #fff;
        }

        .custom-select-container.open .custom-select-trigger {
            background-color: #3a3a3a; 
        }

        .custom-select-trigger.has-value {
            color: #ffffff; 
        }

        .custom-select-trigger:hover {
            background-color: #3a3a3a !important; /* 颜色变浅，你可以根据喜好调整 */
            border-color: #777 !important;       /* 边框也稍微亮一点 */
        }

        .gender-option:hover i {
    color: #ffffff;
    transform: scale(1.1); /* 图标稍微变大一点点 */
}

        .custom-select-list {
            display: none; 
            position: absolute;
            top: 100%;
            left: 0;
            width: 100%;
            background-color: #ffffff; 
            border-radius: 0 0 6px 6px;
            margin-top: -2px;
            z-index: 100;
            list-style: none;
            padding: 0;
            max-height: 200px; 
            overflow-y: auto;
            box-shadow: 0 5px 15px rgba(0,0,0,0.5);
            font-size: 14px;
            border: 1px solid #ccc;
            border-top: none;
        }

        .custom-select-container.open .custom-select-list {
            display: block;
        }

        .custom-select-list li {
            padding: 10px 15px;
            color: #333333; 
            cursor: pointer;
        }

        .custom-select-list li.selected-item {
            background-color: #1a1a1a; 
            color: #ffffff; 
        }

        .custom-select-list li:hover {
            background-color: #007bff; 
            color: #ffffff; 
        }

        .custom-select-list li.dropdown-header {
            color: #aaaaaa;
            font-weight: bold;
            cursor: default; 
            pointer-events: none;
            border-bottom: 1px solid #eeeeee; 
        }

        /* --- OTHER ELEMENTS --- */
        .gender-group { display: flex; gap: 10px; }
        /* ✅ 1. 基础状态 (确保 Emoji 默认居中、大小合适) */
        .gender-option {
            flex: 1;
            padding: 10px;
            border: 1px solid #555;
            border-radius: 6px;
            text-align: center;
            cursor: pointer;
            background-color: #1a1a1a;
            
            /* 新增：让 Emoji 在 Hover 时有平滑过渡 */
            transition: all 0.2s ease-in-out;
            font-size: 20px; /* 稍微调大一点 Emoji，让它更清晰 */
            display: flex;
            justify-content: center;
            align-items: center;
        }

        /* ✅ 2. 👧 Hover 状态 (鼠标指上去时的魔法) */
        .gender-option:hover {
            background-color: #333333 !important; /* 背景变浅 (就像 Month 框那样) */
            border-color: #ffffff !important;       /* 边框变白 */
            
            /* 核心反馈：让 Emoji 本身有一些微动效 */
            transform: scale(1.10) translateY(-2px); /* 放大 15% 并微微上浮，非常有动感 */
            
            /* 增加：让 Emoji 看起来在闪闪发光 */
            text-shadow: 0 0 10px rgba(255, 255, 255, 0.6); 
        }

        /* ✅ 3. Selected 状态 (选中后的样式) */
        .gender-option.selected {
            background-color: #ffffffd5 !important;  
            border-color: #ffffff !important;
            color: #000000 !important;
            transform: scale(1); /* 选中时恢复原状，增加确定感 */
        }

        .error-text { color: #ff4d4d; font-size: 12px; margin-top: 5px; display: none; }
        
        .suggestions-container {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-top: 10px;
            flex-wrap: wrap; 
        }

        .suggestions-label {
            color: #ffffff;
            font-weight: bold;
            font-size: 14px;
        }

        .suggestion-link {
            color: #dcdcdc; 
            cursor: pointer;
            text-decoration: none; 
            background-color: #2b2b2b; 
            border: 1px solid #777; 
            padding: 6px 16px; 
            border-radius: 20px; 
            font-size: 14px;
            transition: 0.2s;
        }

        .suggestion-link:hover {
            background-color: #3a3a3a; 
            border-color: #fff;
            color: #fff;
        }

        .submit-btn {
            width: 100%; padding: 15px; background-color: #fff; color: #000;
            border: none; border-radius: 8px; font-size: 16px; font-weight: bold;
            cursor: pointer; margin-top: 10px; transition: 0.2s;
        }
        
        .submit-btn:disabled {
            background-color: #555555 !important; 
            color: #888888 !important; 
            cursor: not-allowed; 
        }
        .submit-btn:hover { background-color: #e6e6e6; }
        
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
            cursor: pointer;
            padding: 0;
            outline: none;
            opacity: 0.6; /* 默认稍微暗一点 */
            transition: opacity 0.2s;
        }
        
        .toggle-pwd-btn:hover {
            opacity: 1; /* 鼠标放上去变亮 */
        }

    </style>
</head>
<body>

    <button class="login-btn" onclick="window.location.href='login.php'">Log In</button>

    <div class="logo">PLAYLEARN</div>

    <div class="signup-container">
        <div class="subtitle">SIGN UP AND START LEARNING!</div>

        <div class="form-group">
            <label>Birthday</label>
            <div class="birthday-group">
                <div class="custom-select-container" id="container-month">
                    <div class="custom-select-trigger" id="trigger-month" onclick="toggleDropdown(this)">Month</div>
                    <ul class="custom-select-list" id="list-month"></ul>
                </div>
                <div class="custom-select-container" id="container-day">
                    <div class="custom-select-trigger" id="trigger-day" onclick="toggleDropdown(this)">Day</div>
                    <ul class="custom-select-list" id="list-day"></ul>
                </div>
                <div class="custom-select-container" id="container-year">
                    <div class="custom-select-trigger" id="trigger-year" onclick="toggleDropdown(this)">Year</div>
                    <ul class="custom-select-list" id="list-year"></ul>
                </div>
            </div>
        </div>

        <div class="form-group" id="parent-email-group">
            <label>Email</label>
            <input type="email" id="userEmail" placeholder="Enter email address" 
                onfocus="checkBirthdayFirst('email')" onkeyup="checkFormValidity()">
            <div id="emailBirthdayError" class="error-text">Birthday must be set first</div>
        </div>

        <div class="form-group">
            <label>Username</label>
            <input type="text" id="username" placeholder="Don't use your real name" 
                onfocus="checkBirthdayFirst('username')" onkeyup="validateUsername()">
            <div id="usernameError" class="error-text"></div>
        </div>

        <div class="form-group">
            <label>Password</label>
            <div class="password-input-wrapper">
                <input type="password" id="password" placeholder="At least 8 characters" onkeyup="validatePassword(); checkFormValidity()">
                
                <button type="button" class="toggle-pwd-btn" onclick="togglePasswordVisibility('password', this)">
                    <i class="fas fa-eye-slash"></i> 
                </button>
            </div>
            <div id="passwordError" class="error-text">Passwords must be between 8 and 200 characters long.</div>
        </div>

        <div class="form-group">
            <label>Gender (optional)</label>
            <div class="gender-group">
                <div class="gender-option" data-value="girl" onclick="selectGender(this)">👧</div>
                <div class="gender-option" data-value="boy" onclick="selectGender(this)">👦</div>
            </div>
        </div>

        <button class="submit-btn" id="signupBtn" onclick="submitForm()" disabled>Sign Up</button>
    </div>

    <script>
        // --- 1. POPULATE CUSTOM DROPDOWNS ---
        const monthsData = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
        // 【修改：在最后面多传一个参数，告诉它标题叫什么】
        populateList('list-month', 'trigger-month', monthsData, "Month");

        const daysData = [];
        for(let i=1; i<=31; i++) daysData.push(i);
        populateList('list-day', 'trigger-day', daysData, "Day");

        const yearsData = [];
        const currentYear = new Date().getFullYear();
        for(let i=currentYear; i>=1920; i--) yearsData.push(i);
        populateList('list-year', 'trigger-year', yearsData, "Year");

        // 【修改：接收 headerLabel 参数，并把它放在列表的最上面】
        function populateList(listId, triggerId, dataArray, headerLabel) {
            const ul = document.getElementById(listId);
            
            // 1. 先创建一个作为标题的 <li>
            let headerLi = document.createElement('li');
            headerLi.innerText = headerLabel; // 写入 Month, Day 或 Year
            headerLi.className = 'dropdown-header'; // 赋予不可点击的 CSS 样式
            ul.appendChild(headerLi); // 把它塞进列表的最顶部

            // 2. 然后再循环塞入真实的选项
            dataArray.forEach(item => {
                let li = document.createElement('li');
                li.innerText = item;
                li.onclick = function(e) {
                    e.stopPropagation(); 
                    selectOption(triggerId, item, this); 
                };
                ul.appendChild(li);
            });
        }

        // --- 2. DROPDOWN INTERACTION LOGIC ---
        function toggleDropdown(triggerElement) {
            closeAllDropdowns(); 
            triggerElement.parentElement.classList.toggle('open');
        }

        function selectOption(triggerId, selectedValue, clickedLi) {
            const trigger = document.getElementById(triggerId);
            trigger.innerText = selectedValue; 
            trigger.classList.add('has-value'); 

            const ul = clickedLi.parentElement;
            const allLis = ul.querySelectorAll('li');
            allLis.forEach(li => li.classList.remove('selected-item')); 
            clickedLi.classList.add('selected-item'); 

            closeAllDropdowns();
            checkAge(); 
            checkFormValidity();
        }

        function closeAllDropdowns() {
            document.querySelectorAll('.custom-select-container').forEach(el => {
                el.classList.remove('open');
            });
        }

        window.onclick = function(event) {
            if (!event.target.matches('.custom-select-trigger')) {
                closeAllDropdowns();
            }
        }

        // --- 3. CORE LOGIC: AGE CHECK & VALIDATION ---
        function checkAge() {
            const mText = document.getElementById('trigger-month').innerText;
            const dText = document.getElementById('trigger-day').innerText;
            const yText = document.getElementById('trigger-year').innerText;

            // 只要有一个没选，就直接退出，不运行后面的逻辑
            if (mText === "Month" || dText === "Day" || yText === "Year") return;

            // 逻辑修正：只隐藏关于“生日未设置”的特定错误
            const birthdayErr = document.getElementById('emailBirthdayError');
            if (birthdayErr) birthdayErr.style.display = 'none';

            // 检查用户名框，如果是提示“先填生日”，则隐藏它
            const userErr = document.getElementById('usernameError');
            if (userErr && userErr.innerText.includes("Birthday")) {
                userErr.style.display = 'none';
            }

            // ✅ 核心：联动表单总检查，让按钮有机会变亮
            checkFormValidity();
        }

        function checkBirthdayFirst(target) {
            const mText = document.getElementById('trigger-month').innerText;
            const dText = document.getElementById('trigger-day').innerText;
            const yText = document.getElementById('trigger-year').innerText;
            const isBirthdaySet = !(mText === "Month" || dText === "Day" || yText === "Year");

            // 1. 如果用户点的是 Email 框
            if (target === 'email') {
                const emailError = document.getElementById('emailBirthdayError');
                emailError.style.display = isBirthdaySet ? 'none' : 'block';
            }

            // 2. 如果用户点的是 Username 框
            if (target === 'username') {
                const userError = document.getElementById('usernameError');
                const emailValue = document.getElementById('userEmail').value.trim();

                if (!isBirthdaySet) {
                    userError.innerHTML = "Birthday must be set first";
                    userError.style.display = 'block';
                } else if (emailValue === "" || !emailValue.includes('@')) {
                    // 如果生日填了，但 Email 没填好
                    userError.innerHTML = "Please fill in a valid Email first";
                    userError.style.display = 'block';
                } else {
                    userError.style.display = 'none';
                }
            }
        }

        const takenUsernames = ["abc", "admin", "player1", "test"]; 

        function validateUsername() {
            const usernameInput = document.getElementById('username').value.trim();
            const errorDiv = document.getElementById('usernameError');

            // 如果框是空的，隐藏错误
            if (usernameInput === "") {
                errorDiv.style.display = 'none';
                checkFormValidity();
                return;
            }

            // 发送请求给后端去查数据库
            fetch('check_username.php?username=' + encodeURIComponent(usernameInput))
            .then(response => response.json())
            .then(data => {
                if (data.taken) {
                    // ❌ 数据库说名字被占用了！生成 3 个随机建议给他
                    const sug1 = usernameInput + Math.floor(Math.random() * 100 + 10);
                    const sug2 = usernameInput + Math.floor(Math.random() * 1000 + 100);
                    const sug3 = usernameInput + Math.floor(Math.random() * 1000 + 100);

                    errorDiv.innerHTML = `
                        This username is already in use.
                        <div class="suggestions-container">
                            <span class="suggestions-label">Try:</span>
                            <a class="suggestion-link" onclick="fillUsername('${sug1}')">${sug1}</a>
                            <a class="suggestion-link" onclick="fillUsername('${sug2}')">${sug2}</a>
                            <a class="suggestion-link" onclick="fillUsername('${sug3}')">${sug3}</a>
                        </div>
                    `;
                    errorDiv.style.display = 'block';
                } else {
                    // ✅ 数据库说名字没人用，隐藏错误
                    errorDiv.style.display = 'none';
                }
                
                // 查完之后，重新检查一下整个表单，看看 Sign Up 按钮能不能亮起来
                checkFormValidity();
            })
            .catch(error => console.error('Error checking username:', error));
        }

        function fillUsername(newUsername) {
            const input = document.getElementById('username');
            input.value = newUsername;
            document.getElementById('usernameError').style.display = 'none';
            input.focus();
            checkFormValidity();
        }

        function validatePassword() {
            const pwd = document.getElementById('password').value;
            const errorDiv = document.getElementById('passwordError');
            if (pwd.length > 0 && (pwd.length < 8 || pwd.length > 200)) {
                errorDiv.style.display = 'block';
            } else {
                errorDiv.style.display = 'none';
            }
        }

        function selectGender(element) {
            const isAlreadySelected = element.classList.contains('selected');

            let options = document.getElementsByClassName('gender-option');
            for(let opt of options) {
                opt.classList.remove('selected');
            }

            if (!isAlreadySelected) {
                element.classList.add('selected');
            }
            
        }

        // 替换掉原来的 submitForm 函数
        function submitForm() {
            // 1. 收集生日数据
            const mText = document.getElementById('trigger-month').innerText;
            const dText = document.getElementById('trigger-day').innerText;
            const yText = document.getElementById('trigger-year').innerText;
            
            // 将月份英文转成数字
            const m = monthsData.indexOf(mText) + 1; 
            const d = parseInt(dText);
            const y = parseInt(yText);

            // 2. 收集其他数据
            const username = document.getElementById('username').value.trim();
            const password = document.getElementById('password').value;
            const userEmail = document.getElementById('userEmail').value.trim();
            
            // 收集性别 (更稳健的做法)
            let gender = "";
            const selectedGenderBtn = document.querySelector('.gender-option.selected');
            if (selectedGenderBtn) {
                gender = selectedGenderBtn.getAttribute('data-value'); // 直接抓取 girl 或 boy
            }

            // 3. 把数据打包进 FormData
            let formData = new FormData();
            formData.append('username', username);
            formData.append('password', password);
            formData.append('month', m);
            formData.append('day', d);
            formData.append('year', y);
            formData.append('email', userEmail);
            formData.append('gender', gender);

            // 4. 发送给后端的 PHP 文件
            // 把按钮文字变成 Loading，防止用户重复点击
            const btn = document.getElementById('signupBtn');
            const originalBtnText = btn.innerText;
            btn.innerText = "Processing...";
            btn.disabled = true;

            fetch('process_signup.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                // 恢复按钮状态
                btn.innerText = originalBtnText;
                btn.disabled = false;

                if (data.status === "success") {
                    alert("🎉 注册成功！马上带你去登录页面...");
                    // 这里未来可以写一行代码，自动跳转到 login.html
                    // window.location.href = "login.html";
                } else {
                    alert("❌ 发生错误: " + data.message);
                }
            })
            .catch(error => {
                btn.innerText = originalBtnText;
                btn.disabled = false;
                console.error('Error:', error);
                alert("网络连接出错，请确保 XAMPP 正在运行。");
            });
        }

        // --- 4. 表单总检查逻辑 (控制 Sign Up 按钮亮起/变灰) ---
        function checkFormValidity() {
            let isValid = true; // 假设一开始是合法的，下面开始一项项检查

            // 1. 检查生日是否已填
            const mText = document.getElementById('trigger-month').innerText;
            const dText = document.getElementById('trigger-day').innerText;
            const yText = document.getElementById('trigger-year').innerText;
            if (mText === "Month" || dText === "Day" || yText === "Year") isValid = false;

            // 2. 检查家长邮箱（如果未满16岁显示出来了，就必须填，且要包含 @ 符号）
            const emailInput = document.getElementById('userEmail').value.trim();
            // 检查是否为空，以及是否包含 @ 符号
            if (emailInput === "" || !emailInput.includes('@')) {
                isValid = false;
            }

            // 3. 检查用户名（不能为空，且不能有冲突红字）
            const username = document.getElementById('username').value.trim();
            const usernameError = document.getElementById('usernameError');
            if (username === "" || (usernameError.style.display === 'block' && usernameError.innerHTML.includes("already in use"))) isValid = false;

            // 4. 检查密码（必须是 8 到 200 位）
            const password = document.getElementById('password').value;
            if (password.length < 8 || password.length > 200) isValid = false;

            // 5. 根据检查结果，控制按钮
            const btn = document.getElementById('signupBtn');
            if (isValid) {
                btn.disabled = false; // 资料齐全，按钮变白发亮！
            } else {
                btn.disabled = true;  // 资料不齐，按钮继续变灰
            }
        }

        // === 密码显示/隐藏 切换魔法 ===
        function togglePasswordVisibility(inputId, btnElement) {
            const inputField = document.getElementById(inputId);
            const iconElement = btnElement.querySelector('i'); // 获取按钮内的小图标 (<i> 标签)
            
            // 如果现在是密码模式 (黑点)
            if (inputField.type === "password") {
                inputField.type = "text";       // 变成明文
                // 核心更换：换成 FontAwesome 的“睁开眼”图标
                iconElement.classList.remove('fa-eye-slash');
                iconElement.classList.add('fa-eye');
            } else {
                // 如果现在是明文模式
                inputField.type = "password";   // 变回黑点
                // 核心更换：换回“闭眼带有斜线”的图标
                iconElement.classList.remove('fa-eye');
                iconElement.classList.add('fa-eye-slash');
            }
        }
    </script>
</body>
</html>