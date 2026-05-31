<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PLAYLEARN - Sign Up</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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

        .custom-select-container { position: relative; }
        .custom-select-container.open { z-index: 9999 !important; }
        .custom-select-list { display: none; position: absolute; top: 100%; left: 0; width: 100%; background: #ffffff; border: 1px solid #e2e8f0; max-height: 160px; overflow-y: auto; border-radius: 0.375rem; box-shadow: 0 10px 25px -5px rgb(0 0 0 / 0.2); }
        .custom-select-container.open .custom-select-list { display: block; }
        .custom-select-list li { padding: 6px 12px; cursor: pointer; font-size: 0.875rem; color: #334155; }
        .custom-select-list li:hover { background: #f1f5f9; }
        .custom-select-list li.dropdown-header { color: #94a3b8; font-weight: 600; cursor: default; border-bottom: 1px solid #e2e8f0; pointer-events: none; }
        .custom-select-list li.selected-item { background: #0f172a; color: #ffffff; }
        .custom-select-trigger { padding: 8px 12px; border: 1px solid #e2e8f0; border-radius: 0.375rem; font-size: 0.875rem; cursor: pointer; position: relative; background: #ffffff; color: #94a3b8; user-select: none; }
        .custom-select-trigger.has-value { color: #0f172a; }
        .custom-select-trigger::after { content: ''; position: absolute; right: 12px; top: 50%; transform: translateY(-50%); border-left: 4px solid transparent; border-right: 4px solid transparent; border-top: 4px solid #94a3b8; }
        
        .role-option.selected { background-color: #0f172a; color: white; border-color: #0f172a; }
        .gender-option.selected { background-color: #f1f5f9; border-color: #0f172a; }
        .error-text { color: #ef4444; font-size: 0.75rem; margin-top: 2px; display: none; }

        #toast-container { position: fixed; top: 20px; right: 20px; z-index: 99999; display: flex; flex-direction: column; gap: 10px; pointer-events: none; }
        .custom-toast { pointer-events: auto; min-width: 260px; padding: 12px 20px; border-radius: 10px; background: #ffffff; box-shadow: 0 10px 30px rgba(0,0,0,0.15); color: #333333; font-family: 'Segoe UI', sans-serif; font-size: 13px; font-weight: 600; display: flex; align-items: center; gap: 10px; transform: translateX(120%); transition: transform 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55), opacity 0.4s ease; opacity: 0; border-left: 5px solid transparent; }
        .custom-toast.show { transform: translateX(0); opacity: 1; }
        .custom-toast.success { border-left-color: #10b981; } 
        .custom-toast.error { border-left-color: #ef4444; }   
        .toast-icon { font-size: 18px; }
        .custom-toast.success .toast-icon { color: #10b981; }
        .custom-toast.error .toast-icon { color: #ef4444; }
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
            <a href="privacy.php" class="hover:text-white transition-colors">Privacy Policy</a>
            <a href="terms.php" class="hover:text-white transition-colors">Terms of Service</a>
        </div>
        <div class="absolute inset-0 bg-white/[0.05] pointer-events-none" style="background-image: radial-gradient(#fff 1px, transparent 1px); background-size: 24px 24px; opacity: 0.1;"></div>
    </div>

    <div class="h-full w-full overflow-y-auto bg-background flex flex-col items-center px-6 lg:px-10 relative pt-12 pb-8">
        
        <div class="w-full max-w-[480px] shrink-0">
            <div class="text-center mb-6">
                <h1 class="text-3xl font-bold tracking-tight mb-1">Sign up to PlayLearn</h1>
                <p class="text-muted-foreground text-sm">Join us and start learning!</p>
            </div>

            <form id="signupForm" class="space-y-2.5" novalidate onsubmit="event.preventDefault(); submitForm();">
                <div class="flex gap-4 relative" style="z-index: 101;">
                    <div class="flex-1 space-y-1">
                        <label class="text-xs font-medium">I am a...</label>
                        <div class="flex gap-2">
                            <div class="role-option selected flex-1 text-center py-2 border border-border/60 rounded-md cursor-pointer transition-colors text-xs font-medium" data-value="parent" onclick="selectRole(this)">Parent</div>
                        </div>
                        <input type="hidden" id="selectedRole" value="parent">
                    </div>
                    <div class="flex-1 space-y-1">
                        <label class="text-xs font-medium">Gender <span class="text-muted-foreground font-normal">(optional)</span></label>
                        <div class="flex gap-2">
                            <div class="gender-option flex-1 text-center py-0.5 border border-border/60 rounded-md cursor-pointer transition-colors text-xl" data-value="girl" onclick="selectGender(this)">👧</div>
                            <div class="gender-option flex-1 text-center py-0.5 border border-border/60 rounded-md cursor-pointer transition-colors text-xl" data-value="boy" onclick="selectGender(this)">👦</div>
                        </div>
                    </div>
                </div>

                <div class="space-y-1 relative" style="z-index: 100;">
                    <label class="text-xs font-medium">Birthday</label>
                    <div class="flex gap-2">
                        <div class="custom-select-container flex-1" id="container-month">
                            <div class="custom-select-trigger flex w-full h-10 px-3 py-1.5 text-sm bg-background border border-border/60 rounded-md items-center cursor-pointer" id="trigger-month" onclick="toggleDropdown(this)">Month</div>
                            <ul class="custom-select-list" id="list-month"></ul>
                        </div>
                        <div class="custom-select-container flex-1" id="container-day">
                            <div class="custom-select-trigger flex w-full h-10 px-3 py-1.5 text-sm bg-background border border-border/60 rounded-md items-center cursor-pointer" id="trigger-day" onclick="toggleDropdown(this)">Day</div>
                            <ul class="custom-select-list" id="list-day"></ul>
                        </div>
                        <div class="custom-select-container flex-1" id="container-year">
                            <div class="custom-select-trigger flex w-full h-10 px-3 py-1.5 text-sm bg-background border border-border/60 rounded-md items-center cursor-pointer" id="trigger-year" onclick="toggleDropdown(this)">Year</div>
                            <ul class="custom-select-list" id="list-year"></ul>
                        </div>
                    </div>
                </div>

                <div class="space-y-1 relative" style="z-index: 10;">
                    <label class="text-xs font-medium">Email</label>
                    <div class="flex gap-2">
                        <input type="email" id="reg_email" placeholder="Enter email address" oninput="checkFormValidity()" class="flex-1 w-full h-10 px-3 py-1.5 text-sm bg-background border border-border/60 rounded-md focus:outline-none focus:ring-2 focus:ring-primary relative z-10">
                        <button type="button" id="otp_send_btn" onclick="sendOTP()" class="inline-flex items-center justify-center h-10 px-4 text-xs font-bold bg-primary text-primary-foreground rounded-md hover:bg-primary/90 transition-colors w-24 cursor-pointer relative z-20">Send OTP</button>
                    </div>
                </div>

                <div id="otp_section" style="display: none; z-index: 9;" class="p-3 border border-dashed border-border/60 rounded-md bg-slate-50 transition-all duration-300 relative">
                    <label class="text-xs font-medium block mb-1">Verification Code</label>
                    <div class="flex flex-wrap items-center gap-3">
                        <input type="text" id="otp_code" placeholder="6-digit" maxlength="6" oninput="checkFormValidity()" class="w-24 h-9 px-2 py-1 text-sm text-center tracking-widest bg-background border border-border/60 rounded-md focus:outline-none focus:ring-2 focus:ring-primary shrink-0">
                        <span id="timer" class="text-xs text-muted-foreground font-bold shrink-0"></span>
                        <p class="text-[10px] text-muted-foreground leading-tight flex-1 min-w-[150px] m-0">
                            <i class="fas fa-info-circle mr-1"></i> Still no code? Double check email spelling and try again.
                        </p>
                    </div>
                </div>

                <div class="space-y-1 relative" style="z-index: 8;">
                    <label class="text-xs font-medium">Username</label>
                    <input type="text" id="username" autocomplete="off" placeholder="Username" oninput="validateUsername()" class="flex w-full h-10 px-3 py-1.5 text-sm bg-background border border-border/60 rounded-md focus:outline-none focus:ring-2 focus:ring-primary">
                    <div id="usernameError" class="error-text"></div>
                </div>

                <div class="space-y-1 relative" style="z-index: 7;">
                    <label class="text-xs font-medium">Password</label>
                    <div class="relative">
                    <input type="password" id="password" autocomplete="new-password" placeholder="Min 8 characters" 
                           onfocus="showHints()" 
                           onblur="hideHints()" 
                           oninput="validatePassword(); checkFormValidity()" 
                           class="flex w-full h-10 px-3 py-1.5 pr-10 text-sm bg-background border border-border/60 rounded-md focus:outline-none focus:ring-2 focus:ring-primary">

                    <button type="button" id="togglePasswordBtn" class="absolute right-3 top-1/2 -translate-y-1/2 text-muted-foreground hover:text-foreground">
                        <svg id="eyeIcon" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"/><path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"/><path d="M6.61 6.61A13.526 13.526 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"/><line x1="2" x2="22" y1="2" y2="22"/></svg>
                    </button>
                </div>
                    <div id="passwordHints" class="mt-2 space-y-1 hidden transition-all duration-300">
                        <div id="hint-length" class="text-[10px] text-red-500 flex items-center gap-1">
                            <i class="fas fa-times-circle"></i> 8-200 characters
                        </div>
                        <div id="hint-uppercase" class="text-[10px] text-red-500 flex items-center gap-1">
                            <i class="fas fa-times-circle"></i> At least one uppercase letter
                        </div>
                        <div id="hint-number" class="text-[10px] text-red-500 flex items-center gap-1">
                            <i class="fas fa-times-circle"></i> At least one number
                        </div>
                    </div>
                </div>

                <button type="submit" id="signupBtn" disabled class="inline-flex items-center justify-center w-full h-11 text-sm font-bold bg-primary text-primary-foreground rounded-md hover:bg-primary/90 transition-colors disabled:opacity-50 disabled:cursor-not-allowed mt-2 relative" style="z-index: 6;">Sign Up</button>
            </form>

            <div class="text-center text-xs text-muted-foreground mt-4 relative" style="z-index: 5;">Already have an account? <a href="login.php" class="text-foreground font-bold hover:underline">Log In</a></div>
        </div>
        
    </div>
</div>

<script>
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

    const monthsData = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
    populateList('list-month', 'trigger-month', monthsData, "Month");
    const daysData = []; for(let i=1; i<=31; i++) daysData.push(i);
    populateList('list-day', 'trigger-day', daysData, "Day");
    const yearsData = [];
    const currentYear = new Date().getFullYear();
    const maxParentBirthYear = currentYear - 18;
    for(let i=maxParentBirthYear; i>=1920; i--) yearsData.push(i);
    populateList('list-year', 'trigger-year', yearsData, "Year");

    function isAdultBirthdaySelected() {
        const mText = document.getElementById('trigger-month').innerText;
        const dText = document.getElementById('trigger-day').innerText;
        const yText = document.getElementById('trigger-year').innerText;
        if (mText === "Month" || dText === "Day" || yText === "Year") return false;

        const month = monthsData.indexOf(mText);
        const day = parseInt(dText, 10);
        const year = parseInt(yText, 10);
        const birthday = new Date(year, month, day);
        const adultCutoff = new Date();
        adultCutoff.setFullYear(adultCutoff.getFullYear() - 18);
        return birthday <= adultCutoff;
    }

    function populateList(listId, triggerId, dataArray, headerLabel) {
        const ul = document.getElementById(listId);
        let headerLi = document.createElement('li');
        headerLi.innerText = headerLabel; headerLi.className = 'dropdown-header';
        ul.appendChild(headerLi);
        dataArray.forEach(item => {
            let li = document.createElement('li'); li.innerText = item;
            li.onclick = function(e) { e.stopPropagation(); selectOption(triggerId, item, this); };
            ul.appendChild(li);
        });
    }

    function toggleDropdown(triggerElement) { 
        const container = triggerElement.parentElement;
        const isOpen = container.classList.contains('open');
        closeAllDropdowns(); 
        if (!isOpen) container.classList.add('open'); 
    }
    
    function selectOption(triggerId, selectedValue, clickedLi) {
        const trigger = document.getElementById(triggerId);
        trigger.innerText = selectedValue; trigger.classList.add('has-value');
        const ul = clickedLi.parentElement; const allLis = ul.querySelectorAll('li');
        allLis.forEach(li => li.classList.remove('selected-item'));
        clickedLi.classList.add('selected-item');
        closeAllDropdowns(); checkFormValidity();
    }
    
    function closeAllDropdowns() { 
        document.querySelectorAll('.custom-select-container').forEach(el => el.classList.remove('open')); 
    }
    
    window.onclick = function(event) { if (!event.target.matches('.custom-select-trigger')) closeAllDropdowns(); }

    function selectRole(element) {
        document.querySelectorAll('.role-option').forEach(el => el.classList.remove('selected'));
        element.classList.add('selected');
        document.getElementById('selectedRole').value = element.getAttribute('data-value');
        checkFormValidity();
    }

    function selectGender(element) {
        const isAlreadySelected = element.classList.contains('selected');
        document.querySelectorAll('.gender-option').forEach(opt => opt.classList.remove('selected'));
        if (!isAlreadySelected) element.classList.add('selected');
        checkFormValidity();
    }

    function validateUsername() {
        const usernameInput = document.getElementById('username').value.trim();
        const errorDiv = document.getElementById('usernameError');
        if (usernameInput === "") { errorDiv.style.display = 'none'; checkFormValidity(); return; }
        fetch('check_username.php?username=' + encodeURIComponent(usernameInput))
        .then(res => res.json()).then(data => {
            if (data.taken) {
                const sug1 = usernameInput + Math.floor(Math.random() * 100 + 10);
                const sug2 = usernameInput + Math.floor(Math.random() * 1000 + 100);
                errorDiv.innerHTML = `Name taken. <div class="flex gap-2 mt-1"><span class="text-xs text-muted-foreground">Try:</span><span class="text-xs cursor-pointer underline" onclick="fillUsername('${sug1}')">${sug1}</span><span class="text-xs cursor-pointer underline" onclick="fillUsername('${sug2}')">${sug2}</span></div>`;
                errorDiv.style.display = 'block';
            } else { errorDiv.style.display = 'none'; }
            checkFormValidity();
        }).catch(() => {});
    }

    function fillUsername(name) { document.getElementById('username').value = name; document.getElementById('usernameError').style.display = 'none'; checkFormValidity(); }

    let countdown;
    function sendOTP() {
        // ✅ 强制抹除手机端可能注入的零宽幽灵字符，防报错！
        let email = document.getElementById('reg_email').value.trim().toLowerCase();
        email = email.replace(/[\u200B-\u200D\uFEFF\s]/g, '');

        // ✅ 放弃移动端容易出 BUG 的严苛正则，改用原生的字符串包含判断
        if (!email || email.indexOf('@') === -1 || email.indexOf('.') === -1) {
            return showToast("Please enter a valid formatted email!", "error");
        }
        
        const btn = document.getElementById('otp_send_btn');
        btn.disabled = true; btn.innerText = "Checking...";

        let checkData = new FormData(); checkData.append('identifier', email);
        
        fetch('verify_account.php', { method: 'POST', body: checkData })
        .then(r => r.json())
        .then(data => {
            if (data.status === 'success') {
                showToast("This email is already registered! Redirecting...", "error");
                setTimeout(() => window.location.href = "login.php", 2000);
            } else {
                btn.innerText = "Sending...";
                let formData = new FormData(); formData.append('email', email);
                return fetch('send_otp.php', { method: 'POST', body: formData });
            }
        })
        .then(response => {
            if (response) return response.json();
        })
        .then(otpData => {
            if (otpData && otpData.status === 'success') {
                showToast("OTP code sent to your email!", "success");
                const otpSec = document.getElementById('otp_section'); 
                otpSec.style.display = 'block';
                let seconds = 60;
                btn.disabled = true;
                countdown = setInterval(() => {
                    seconds--; btn.innerText = `Retry (${seconds}s)`;
                    if (seconds <= 0) { clearInterval(countdown); btn.disabled = false; btn.innerText = "Send OTP"; }
                }, 1000);
            } else if (otpData) {
                showToast(otpData.message, "error");
                btn.disabled = false; btn.innerText = "Send OTP";
            }
        })
        .catch(function() {
            showToast("System connection error. Please try again.", "error");
            btn.disabled = false; btn.innerText = "Send OTP";
        });
    }

    function checkFormValidity() {
    // 1. 获取所有输入框的内容
    const mText = document.getElementById('trigger-month').innerText;
    const dText = document.getElementById('trigger-day').innerText;
    const yText = document.getElementById('trigger-year').innerText;
    const emailVal = document.getElementById('reg_email').value.trim();
    const otpVal = document.getElementById('otp_code').value.trim();
    const userVal = document.getElementById('username').value.trim();
    const passVal = document.getElementById('password').value;

    // 2. ✅ 获取用户名错误提示元素 (修复未定义问题)
    const userErr = document.getElementById('usernameError');

    // 3. ✅ 密码复杂度判定
    const hasUppercase = /[A-Z]/.test(passVal);
    const hasNumber = /[0-9]/.test(passVal);
    const isLengthValid = passVal.length >= 8 && passVal.length <= 200;

    // 4. 综合判断逻辑
    let isValid = (mText !== "Month" && dText !== "Day" && yText !== "Year") &&
                  (emailVal.includes('@')) && 
                  (otpVal.length === 6) &&
                  (userVal !== "" && userErr.style.display !== 'block') &&
                  (isLengthValid && hasUppercase && hasNumber) &&
                  isAdultBirthdaySelected();

    // 5. 控制按钮状态
    document.getElementById('signupBtn').disabled = !isValid;
}

    function submitForm() {
        const mText = document.getElementById('trigger-month').innerText;
        const m = monthsData.indexOf(mText) + 1;
        const genderBtn = document.querySelector('.gender-option.selected');
        if (!isAdultBirthdaySelected()) {
            showToast("Parent accounts must be registered by an adult.", "error");
            return;
        }
        let formData = new FormData();
        formData.append('role', document.getElementById('selectedRole').value);
        formData.append('username', document.getElementById('username').value.trim());
        formData.append('password', document.getElementById('password').value);
        // ✅ 提交注册时，同样抹除手机可能带来的幽灵空格
        let safeEmail = document.getElementById('reg_email').value.trim().toLowerCase().replace(/[\u200B-\u200D\uFEFF\s]/g, '');
        formData.append('email', safeEmail);
        formData.append('otp_code', document.getElementById('otp_code').value.trim());
        formData.append('month', m); formData.append('day', document.getElementById('trigger-day').innerText);
        formData.append('year', document.getElementById('trigger-year').innerText);
        formData.append('gender', genderBtn ? genderBtn.getAttribute('data-value') : '');
        
        const btn = document.getElementById('signupBtn');
        btn.innerText = "Processing..."; btn.disabled = true;
        fetch('process_signup.php', { method: 'POST', body: formData }).then(res => res.json()).then(data => {
            if (data.status === "success") { showToast("Registration successful! Redirecting...", "success"); setTimeout(() => window.location.href = "login.php", 1500); }
            else { showToast(data.message, "error"); btn.innerText = "Sign Up"; btn.disabled = false; }
        }).catch(() => {
            showToast("Registration failed. Try again.", "error");
            btn.innerText = "Sign Up"; btn.disabled = false;
        });
    }

    const toggleBtn = document.getElementById('togglePasswordBtn');
    const passwordInput = document.getElementById('password');
    let showPassword = false; let mouseX = window.innerWidth / 2; let mouseY = window.innerHeight / 2;
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
            if (passwordInput.value.length > 0 && showPassword) { deltaX = -50; deltaY = -40; }
            const maxDistance = parseFloat(pupil.getAttribute('data-max') || 5);
            const distance = Math.min(Math.sqrt(deltaX ** 2 + deltaY ** 2), maxDistance);
            const angle = Math.atan2(deltaY, deltaX);
            pupil.style.transform = `translate(${Math.cos(angle) * distance}px, ${Math.sin(angle) * distance}px)`;
        });
    }
    function calculatePos(el) { if(!el) return { faceX: 0, faceY: 0, skew: 0 }; const rect = el.getBoundingClientRect(); const deltaX = mouseX - (rect.left + rect.width / 2); const deltaY = mouseY - (rect.top + rect.height / 3); return { faceX: Math.max(-15, Math.min(15, deltaX / 20)), faceY: Math.max(-10, Math.min(10, deltaY / 30)), skew: Math.max(-6, Math.min(6, -deltaX / 120)) }; }
    function updateBodyPos() {
        const pPos = calculatePos(charPurple); const bPos = calculatePos(charBlack); const oPos = calculatePos(charOrange); const yPos = calculatePos(charYellow);
        if (passwordInput.value.length > 0 && showPassword) { charPurple.style.transform = `skewX(0deg)`; eyesPurple.style.left = '20px'; eyesPurple.style.top = '35px'; charBlack.style.transform = `skewX(0deg)`; eyesBlack.style.left = '10px'; eyesBlack.style.top = '28px'; charOrange.style.transform = `skewX(0deg)`; eyesOrange.style.left = '50px'; eyesOrange.style.top = '85px'; charYellow.style.transform = `skewX(0deg)`; eyesYellow.style.left = '20px'; eyesYellow.style.top = '35px'; mouthYellow.style.left = '10px'; mouthYellow.style.top = '88px'; } 
        else { charPurple.style.transform = `skewX(${pPos.skew}deg)`; eyesPurple.style.left = `${45 + pPos.faceX}px`; eyesPurple.style.top = `${40 + pPos.faceY}px`; charBlack.style.transform = `skewX(${bPos.skew}deg)`; eyesBlack.style.left = `${26 + bPos.faceX}px`; eyesBlack.style.top = `${32 + bPos.faceY}px`; charOrange.style.transform = `skewX(${oPos.skew}deg)`; eyesOrange.style.left = `${82 + oPos.faceX}px`; eyesOrange.style.top = `${90 + oPos.faceY}px`; charYellow.style.transform = `skewX(${yPos.skew}deg)`; eyesYellow.style.left = `${52 + yPos.faceX}px`; eyesYellow.style.top = `${40 + yPos.faceY}px`; mouthYellow.style.left = `${40 + yPos.faceX}px`; mouthYellow.style.top = `${88 + yPos.faceY}px`; }
    }
    passwordInput.addEventListener('input', () => { updateBodyPos(); checkFormValidity(); });
    toggleBtn.addEventListener('click', () => {
        showPassword = !showPassword; passwordInput.type = showPassword ? 'text' : 'password';
        const eyeIcon = document.getElementById('eyeIcon');
        if(showPassword) { eyeIcon.innerHTML = `<path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/>`; }
        else { eyeIcon.innerHTML = `<path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"/><path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"/><path d="M6.61 6.61A13.526 13.526 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"/><line x1="2" x2="22" y1="2" y2="22"/>`; }
        updateBodyPos(); updatePupils();
    });
    function blinkLoop(id) { const eyes = document.querySelectorAll(`#${id} .eye-ball`); if(!eyes.length) return; setInterval(() => { eyes.forEach(e => e.classList.add('blink')); setTimeout(() => eyes.forEach(e => e.classList.remove('blink')), 150); }, Math.random() * 4000 + 3000); }
    blinkLoop('eyes-purple'); blinkLoop('eyes-black');
    // ✅ 显示提示
    function showHints() {
        document.getElementById('passwordHints').classList.remove('hidden');
    }

    // ✅ 隐藏提示（如果输入框是空的则隐藏，如果有内容则保留显示方便查看）
    function hideHints() {
        const pwd = document.getElementById('password').value;
        if (pwd.length === 0) {
            document.getElementById('passwordHints').classList.add('hidden');
        }
    }                                                               
    function validatePassword() {
    const pwd = document.getElementById('password').value;
    
    // 逻辑判定
    const hasUppercase = /[A-Z]/.test(pwd);
    const hasNumber = /[0-9]/.test(pwd);
    const isLengthValid = pwd.length >= 8 && pwd.length <= 200;

    // 获取 DOM 元素
    const hLength = document.getElementById('hint-length');
    const hUpper = document.getElementById('hint-uppercase');
    const hNumber = document.getElementById('hint-number');

    // 更新状态函数
    const updateStatus = (el, isValid) => {
        if (isValid) {
            el.classList.remove('text-red-500');
            el.classList.add('text-green-500');
            el.querySelector('i').className = 'fas fa-check-circle';
        } else {
            el.classList.remove('text-green-500');
            el.classList.add('text-red-500');
            el.querySelector('i').className = 'fas fa-times-circle';
        }
    };

    updateStatus(hLength, isLengthValid);
    updateStatus(hUpper, hasUppercase);
    updateStatus(hNumber, hasNumber);
}
</script>
</body>
</html>
