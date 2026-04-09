<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <title>PLAYLEARN - Sign Up</title>
    <style>
        body { background-color: #151313; font-family: 'Segoe UI', sans-serif; margin: 0; display: flex; flex-direction: column; justify-content: center; align-items: center; min-height: 100vh; position: relative; }
        .login-btn { position: absolute; top: 20px; right: 30px; width: 100px; height: 38px; display: flex; justify-content: center; align-items: center; background-color: white; border: 1px solid #ccc; border-radius: 8px; font-weight: bold; font-size: 14px; cursor: pointer; color: black; text-decoration: none; }
        .signup-container { background-color: #2b2b2b; color: white; width: 400px; padding: 40px; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.2); }
        .logo { text-align: center; font-size: 40px; font-weight: 900; margin-bottom: 25px; letter-spacing: 2px; color: #ffffff; }
        .subtitle { text-align: center; font-size: 20px; margin-bottom: 30px; font-weight: 600; }
        .form-group { margin-bottom: 20px; display: flex; flex-direction: column; }
        .form-group label { font-size: 14px; margin-bottom: 5px; color: #dcdcdc; }
        input[type="text"], input[type="password"], input[type="email"] { width: 100%; padding: 12px; padding-right: 40px; border-radius: 6px; border: 1px solid #555; background-color: #1a1a1a; color: white; box-sizing: border-box; font-size: 14px; }
        .password-input-wrapper { position: relative; display: flex; align-items: center; }
        .toggle-pwd-btn { position: absolute; right: 12px; background: none; border: none; font-size: 16px; color: #888888; cursor: pointer; padding: 0; outline: none; z-index: 10; }
        .birthday-group { display: flex; gap: 10px; }
        .custom-select-container { flex: 1; position: relative; }
        .custom-select-trigger { width: 100%; padding: 12px; border-radius: 6px; border: 1px solid #555; background-color: #1a1a1a; color: #dcdcdc; cursor: pointer; font-size: 14px; position: relative; }
        .custom-select-trigger::after { content: ''; position: absolute; top: 50%; right: 12px; transform: translateY(-50%); border-left: 5px solid transparent; border-right: 5px solid transparent; border-top: 5px solid #fff; }
        .custom-select-list { display: none; position: absolute; top: 100%; left: 0; width: 100%; background-color: #ffffff; border-radius: 0 0 6px 6px; z-index: 100; list-style: none; padding: 0; max-height: 200px; overflow-y: auto; border: 1px solid #ccc; }
        .custom-select-container.open .custom-select-list { display: block; }
        .custom-select-list li { padding: 10px 15px; color: #333333; cursor: pointer; }
        .custom-select-list li:hover { background-color: #007bff; color: white; }
        .gender-group { display: flex; gap: 10px; }
        .gender-option { flex: 1; padding: 10px; border: 1px solid #555; border-radius: 6px; text-align: center; cursor: pointer; background-color: #1a1a1a; transition: all 0.2s; font-size: 20px; }
        .gender-option.selected { background-color: white !important; color: black !important; }
        .error-text { color: #ff4d4d; font-size: 12px; margin-top: 5px; display: none; }
        .suggestion-link { color: #dcdcdc; cursor: pointer; border: 1px solid #777; padding: 4px 10px; border-radius: 20px; font-size: 12px; margin-right: 5px; }
        .submit-btn { width: 100%; padding: 15px; background-color: #fff; color: #000; border: none; border-radius: 8px; font-size: 16px; font-weight: bold; cursor: pointer; margin-top: 10px; transition: 0.2s; }
        .submit-btn:disabled { background-color: #555 !important; color: #888 !important; cursor: not-allowed; }
    </style>
</head>
<body>

    <a href="login.php" class="login-btn">Log In</a>

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

        <div class="form-group">
            <label>Email</label>
            <input type="email" id="userEmail" placeholder="Enter email address" onkeyup="checkFormValidity()">
        </div>

        <div class="form-group">
            <label>Username</label>
            <input type="text" id="username" placeholder="Don't use your real name" onkeyup="validateUsername()">
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
            <div id="passwordError" class="error-text">Passwords must be 8-200 characters.</div>
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
        const monthsData = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
        populateList('list-month', 'trigger-month', monthsData, "Month");
        const daysData = Array.from({length: 31}, (_, i) => i + 1);
        populateList('list-day', 'trigger-day', daysData, "Day");
        const yearsData = Array.from({length: 100}, (_, i) => new Date().getFullYear() - i);
        populateList('list-year', 'trigger-year', yearsData, "Year");

        function populateList(listId, triggerId, dataArray, label) {
            const ul = document.getElementById(listId);
            dataArray.forEach(item => {
                let li = document.createElement('li');
                li.innerText = item;
                li.onclick = (e) => { e.stopPropagation(); selectOption(triggerId, item, li); };
                ul.appendChild(li);
            });
        }

        function toggleDropdown(el) { closeAll(); el.parentElement.classList.toggle('open'); }
        function closeAll() { document.querySelectorAll('.custom-select-container').forEach(c => c.classList.remove('open')); }
        function selectOption(tid, val, li) { document.getElementById(tid).innerText = val; closeAll(); checkFormValidity(); }

        function validateUsername() {
            const user = document.getElementById('username').value.trim();
            if(!user) return;
            fetch('check_username.php?username=' + encodeURIComponent(user))
            .then(r => r.json()).then(data => {
                const err = document.getElementById('usernameError');
                if(data.taken) { err.innerText = "Username taken"; err.style.display = 'block'; }
                else { err.style.display = 'none'; }
                checkFormValidity();
            });
        }

        function validatePassword() {
            const p = document.getElementById('password').value;
            document.getElementById('passwordError').style.display = (p.length > 0 && p.length < 8) ? 'block' : 'none';
        }

        function selectGender(el) {
            document.querySelectorAll('.gender-option').forEach(o => o.classList.remove('selected'));
            el.classList.add('selected');
        }

        function checkFormValidity() {
            const m = document.getElementById('trigger-month').innerText !== "Month";
            const e = document.getElementById('userEmail').value.includes('@');
            const u = document.getElementById('username').value.trim() !== "";
            const p = document.getElementById('password').value.length >= 8;
            document.getElementById('signupBtn').disabled = !(m && e && u && p);
        }

        function submitForm() {
            const btn = document.getElementById('signupBtn');
            btn.innerText = "Processing...";
            btn.disabled = true;

            let fd = new FormData();
            fd.append('username', document.getElementById('username').value);
            fd.append('password', document.getElementById('password').value);
            fd.append('email', document.getElementById('userEmail').value);
            fd.append('month', monthsData.indexOf(document.getElementById('trigger-month').innerText) + 1);
            fd.append('day', document.getElementById('trigger-day').innerText);
            fd.append('year', document.getElementById('trigger-year').innerText);
            
            const g = document.querySelector('.gender-option.selected');
            if(g) fd.append('gender', g.getAttribute('data-value'));

            fetch('process_signup.php', { method: 'POST', body: fd })
            .then(r => r.json()).then(data => {
                if(data.status === "success") {
                    alert("🎉 Registration successful!");
                    // ✅ 修复：跳转到正确的登录页
                    window.location.href = "login.php"; 
                } else {
                    alert("❌ Error: " + data.message);
                    btn.innerText = "Sign Up"; btn.disabled = false;
                }
            });
        }

        function togglePasswordVisibility(id, btn) {
            const input = document.getElementById(id);
            const icon = btn.querySelector('i');
            if(input.type === "password") { input.type = "text"; icon.className = 'fas fa-eye'; }
            else { input.type = "password"; icon.className = 'fas fa-eye-slash'; }
        }
    </script>
</body>
</html>