<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PLAYLEARN - Log In</title>
    <script src="https://cdn.tailwindcss.com"></script>
<<<<<<< HEAD
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
=======
>>>>>>> 4f5123f4f40ff24341cfcb8ebd54648461a4dfed
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
<<<<<<< HEAD
        .eye-ball { transition: height 0.15s ease-out; }
        .blink { height: 2px !important; overflow: hidden; }
        .blink .pupil { opacity: 0; }
        .smooth-transform { transition: transform 0.7s ease-in-out, left 0.7s ease-in-out, top 0.7s ease-in-out, height 0.7s ease-in-out; }
        
=======
        /* 眨眼动画过渡效果 */
        .eye-ball { transition: height 0.15s ease-out; }
        .blink { height: 2px !important; overflow: hidden; }
        .blink .pupil { opacity: 0; }
        
        /* ✅ 修复点：在这里加上了 height 的平滑过渡，让它长高变矮也有动画了 */
        .smooth-transform { transition: transform 0.7s ease-in-out, left 0.7s ease-in-out, top 0.7s ease-in-out, height 0.7s ease-in-out; }
        
        /* 右上角 Sign Up 按钮样式 (融合原版) */
>>>>>>> 4f5123f4f40ff24341cfcb8ebd54648461a4dfed
        .signup-header-btn {
            position: absolute; top: 20px; right: 30px; z-index: 50;
            width: 100px; height: 38px; display: flex; justify-content: center; align-items: center;
            background-color: white; border: 1px solid #ccc; border-radius: 8px;
            font-weight: bold; font-size: 14px; cursor: pointer; transition: 0.2s;
            text-decoration: none; color: black; box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        .signup-header-btn:hover { background-color: #f8fafc; }
<<<<<<< HEAD

        /* ✅ 现代化 Toast 通知样式 */
        #toast-container {
            position: fixed;
            top: 25px;
            right: 25px;
            z-index: 9999;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        .custom-toast {
            min-width: 280px;
            padding: 16px 24px;
            border-radius: 12px;
            background: #ffffff;
            box-shadow: 0 15px 35px rgba(0,0,0,0.15);
            color: #333333;
            font-family: 'Segoe UI', Tahoma, sans-serif;
            font-size: 14px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 12px;
            transform: translateX(120%);
            transition: transform 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55), opacity 0.4s ease;
            opacity: 0;
            border-left: 6px solid transparent;
        }
        .custom-toast.show {
            transform: translateX(0);
            opacity: 1;
        }
        .custom-toast.success { border-left-color: #10b981; } /* 绿 */
        .custom-toast.error { border-left-color: #ef4444; }   /* 红 */
        .toast-icon { font-size: 20px; }
        .custom-toast.success .toast-icon { color: #10b981; }
        .custom-toast.error .toast-icon { color: #ef4444; }
=======
>>>>>>> 4f5123f4f40ff24341cfcb8ebd54648461a4dfed
    </style>
</head>
<body class="bg-background text-foreground">

<<<<<<< HEAD
<div id="toast-container"></div>
=======
>>>>>>> 4f5123f4f40ff24341cfcb8ebd54648461a4dfed
<a href="signup.php" class="signup-header-btn">Sign Up</a>

<div class="min-h-screen grid lg:grid-cols-2 overflow-hidden">
    
    <div class="relative hidden lg:flex flex-col justify-between bg-gradient-to-br from-primary/90 via-primary to-primary/80 p-12 text-primary-foreground">
<<<<<<< HEAD
=======
        
>>>>>>> 4f5123f4f40ff24341cfcb8ebd54648461a4dfed
        <div class="relative z-20">
            <div class="flex items-center gap-2 text-lg font-semibold">
                <div class="w-8 h-8 rounded-lg bg-primary-foreground/10 backdrop-blur-sm flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m12 3-1.912 5.813a2 2 0 0 1-1.275 1.275L3 12l5.813 1.912a2 2 0 0 1 1.275 1.275L12 21l1.912-5.813a2 2 0 0 1 1.275-1.275L21 12l-5.813-1.912a2 2 0 0 1-1.275-1.275L12 3Z"/></svg>
                </div>
                <span>PlayLearn</span>
            </div>
        </div>

        <div class="relative z-20 flex items-end justify-center h-[500px]">
            <div class="relative" style="width: 550px; height: 400px;">
<<<<<<< HEAD
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
=======
                
                <div id="char-purple" class="absolute bottom-0 smooth-transform" style="left: 70px; width: 180px; height: 400px; background-color: #6C3FF5; border-radius: 10px 10px 0 0; z-index: 1; transform-origin: bottom center;">
                    <div id="eyes-purple" class="absolute flex gap-8 smooth-transform" style="left: 45px; top: 40px;">
                        <div class="eye-ball w-[18px] h-[18px] rounded-full bg-white flex items-center justify-center overflow-hidden">
                            <div class="pupil w-[7px] h-[7px] bg-[#2D2D2D] rounded-full transition-transform duration-100 ease-out" data-max="5"></div>
                        </div>
                        <div class="eye-ball w-[18px] h-[18px] rounded-full bg-white flex items-center justify-center overflow-hidden">
                            <div class="pupil w-[7px] h-[7px] bg-[#2D2D2D] rounded-full transition-transform duration-100 ease-out" data-max="5"></div>
                        </div>
                    </div>
                </div>

                <div id="char-black" class="absolute bottom-0 smooth-transform" style="left: 240px; width: 120px; height: 310px; background-color: #2D2D2D; border-radius: 8px 8px 0 0; z-index: 2; transform-origin: bottom center;">
                    <div id="eyes-black" class="absolute flex gap-6 smooth-transform" style="left: 26px; top: 32px;">
                        <div class="eye-ball w-[16px] h-[16px] rounded-full bg-white flex items-center justify-center overflow-hidden">
                            <div class="pupil w-[6px] h-[6px] bg-[#2D2D2D] rounded-full transition-transform duration-100 ease-out" data-max="4"></div>
                        </div>
                        <div class="eye-ball w-[16px] h-[16px] rounded-full bg-white flex items-center justify-center overflow-hidden">
                            <div class="pupil w-[6px] h-[6px] bg-[#2D2D2D] rounded-full transition-transform duration-100 ease-out" data-max="4"></div>
                        </div>
                    </div>
                </div>

>>>>>>> 4f5123f4f40ff24341cfcb8ebd54648461a4dfed
                <div id="char-orange" class="absolute bottom-0 smooth-transform" style="left: 0px; width: 240px; height: 200px; background-color: #FF9B6B; border-radius: 120px 120px 0 0; z-index: 3; transform-origin: bottom center;">
                    <div id="eyes-orange" class="absolute flex gap-8 transition-all duration-200 ease-out" style="left: 82px; top: 90px;">
                        <div class="pupil w-[12px] h-[12px] bg-[#2D2D2D] rounded-full transition-transform duration-100 ease-out" data-max="5"></div>
                        <div class="pupil w-[12px] h-[12px] bg-[#2D2D2D] rounded-full transition-transform duration-100 ease-out" data-max="5"></div>
                    </div>
                </div>
<<<<<<< HEAD
=======

>>>>>>> 4f5123f4f40ff24341cfcb8ebd54648461a4dfed
                <div id="char-yellow" class="absolute bottom-0 smooth-transform" style="left: 310px; width: 140px; height: 230px; background-color: #E8D754; border-radius: 70px 70px 0 0; z-index: 4; transform-origin: bottom center;">
                    <div id="eyes-yellow" class="absolute flex gap-6 transition-all duration-200 ease-out" style="left: 52px; top: 40px;">
                        <div class="pupil w-[12px] h-[12px] bg-[#2D2D2D] rounded-full transition-transform duration-100 ease-out" data-max="5"></div>
                        <div class="pupil w-[12px] h-[12px] bg-[#2D2D2D] rounded-full transition-transform duration-100 ease-out" data-max="5"></div>
                    </div>
                    <div id="mouth-yellow" class="absolute w-20 h-[4px] bg-[#2D2D2D] rounded-full transition-all duration-200 ease-out" style="left: 40px; top: 88px;"></div>
                </div>
            </div>
        </div>

<<<<<<< HEAD
        <div class="relative z-50 flex items-center gap-8 text-sm text-primary-foreground/60">
            <a href="privacy.php" class="hover:text-white transition-colors cursor-pointer">Privacy Policy</a>
            <a href="terms.php" class="hover:text-white transition-colors cursor-pointer">Terms of Service</a>
        </div>
        <div class="absolute inset-0 bg-white/[0.05] pointer-events-none" style="background-image: radial-gradient(#fff 1px, transparent 1px); background-size: 20px 20px; opacity: 0.1;"></div>
=======
        <div class="relative z-20 flex items-center gap-8 text-sm text-primary-foreground/60">
            <a href="#" class="hover:text-primary-foreground transition-colors">Privacy Policy</a>
            <a href="#" class="hover:text-primary-foreground transition-colors">Terms of Service</a>
        </div>
        <div class="absolute inset-0 bg-white/[0.05]" style="background-image: radial-gradient(#fff 1px, transparent 1px); background-size: 20px 20px; opacity: 0.1;"></div>
>>>>>>> 4f5123f4f40ff24341cfcb8ebd54648461a4dfed
    </div>


    <div class="flex items-center justify-center p-8 bg-background relative">
        <div class="w-full max-w-[420px]">
            <div class="text-center mb-10">
                <h1 class="text-3xl font-bold tracking-tight mb-2">Login to PlayLearn</h1>
                <p class="text-muted-foreground text-sm">Welcome back! Please enter your details</p>
            </div>

            <form id="loginForm" class="space-y-5" onsubmit="event.preventDefault(); submitLogin();">
                
                <div class="space-y-2">
                    <label for="loginIdentifier" class="text-sm font-medium">Username / Email</label>
                    <input id="loginIdentifier" name="username" type="text" placeholder="Enter username or email" autocomplete="off" onkeyup="checkLoginValidity()"
                           class="flex w-full h-12 px-3 py-2 text-sm bg-background border border-border/60 rounded-md focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
                </div>

                <div class="space-y-2">
                    <label for="password" class="text-sm font-medium">Password</label>
                    <div class="relative">
                        <input id="password" name="password" type="password" placeholder="Enter your password" onkeyup="checkLoginValidity()"
                               class="flex w-full h-12 px-3 py-2 pr-10 text-sm bg-background border border-border/60 rounded-md focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
                        <button type="button" id="togglePasswordBtn" class="absolute right-3 top-1/2 -translate-y-1/2 text-muted-foreground hover:text-foreground">
                            <svg id="eyeIcon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"/><path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"/><path d="M6.61 6.61A13.526 13.526 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"/><line x1="2" x2="22" y1="2" y2="22"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="flex items-center justify-between mt-2">
                    <div class="flex items-center space-x-2">
                        <input type="checkbox" id="remember" class="w-4 h-4 rounded border-gray-300 text-primary focus:ring-primary">
                        <label for="remember" class="text-sm font-normal cursor-pointer text-muted-foreground">Remember me</label>
                    </div>
                    <a href="javascript:void(0)" onclick="goToRecovery()" class="text-sm text-primary hover:underline font-medium">Forgot Password?</a>
                </div>

                <button type="submit" id="loginBtn" disabled 
                        class="inline-flex items-center justify-center w-full h-12 text-base font-medium bg-primary text-primary-foreground rounded-md hover:bg-primary/90 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                    Log In
                </button>
            </form>

            <div class="text-center text-sm text-muted-foreground mt-8">
                Don't have an account? <a href="signup.php" class="text-foreground font-medium hover:underline">Sign Up</a>
            </div>
        </div>
    </div>
</div>

<script>
<<<<<<< HEAD
    // ✅ Toast 通知核心函数
    function showToast(message, type = 'success') {
        const container = document.getElementById('toast-container');
        const toast = document.createElement('div');
        toast.className = `custom-toast ${type}`;
        
        const icon = type === 'success' 
            ? '<i class="fas fa-check-circle toast-icon"></i>' 
            : '<i class="fas fa-exclamation-circle toast-icon"></i>';
            
        toast.innerHTML = `${icon} <span>${message}</span>`;
        container.appendChild(toast);
        
        setTimeout(() => toast.classList.add('show'), 10);
        setTimeout(() => {
            toast.classList.remove('show');
            setTimeout(() => toast.remove(), 400); 
        }, 3000);
    }

=======
    // === 1. 你原版的表单提交逻辑 ===
>>>>>>> 4f5123f4f40ff24341cfcb8ebd54648461a4dfed
    const loginIdentifierInput = document.getElementById('loginIdentifier');
    const passwordInput = document.getElementById('password');
    const loginBtn = document.getElementById('loginBtn');

<<<<<<< HEAD
=======
    // 检查两个框是否都填了
>>>>>>> 4f5123f4f40ff24341cfcb8ebd54648461a4dfed
    function checkLoginValidity() {
        const user = loginIdentifierInput.value.trim();
        const pass = passwordInput.value;
        if (user !== "" && pass !== "") {
            loginBtn.disabled = false;
        } else {
            loginBtn.disabled = true;
        }
    }

<<<<<<< HEAD
=======
    // 提交数据给 process_login.php
>>>>>>> 4f5123f4f40ff24341cfcb8ebd54648461a4dfed
    function submitLogin() {
        const user = loginIdentifierInput.value.trim();
        const pass = passwordInput.value;

        loginBtn.innerText = "Verifying...";
        loginBtn.disabled = true;

        let formData = new FormData();
        formData.append('username', user);
        formData.append('password', pass);

        fetch('process_login.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === "success") {
                const displayName = data.username ? data.username : user;
<<<<<<< HEAD
                // ✅ 替换了原有的 alert()
                showToast("Welcome back, " + displayName + "! Authenticating...", "success");
                
                // 延迟 1.5 秒再跳转，让用户看清楚绿色的通知
                setTimeout(() => {
                    if (data.role === 'admin') {
                        window.location.href = "admin_dashboard.php";
                    } else if (data.role === 'parent') {
                        window.location.href = "ParentDashboard.php";
                    } else {
                        window.location.href = "homepage.php"; 
                    }
                }, 1500);

            } else {
                // ✅ 替换了原有的 alert()
                showToast(data.message, "error");
                passwordInput.value = ""; 
                checkLoginValidity(); 
=======
                alert("🎉 Welcome back, " + displayName + "! Ready to play?");
                window.location.href = "homepage.php"; // ✅ 改成你们实际的主页名字
            } else {
                alert("❌ Login failed: " + data.message);
                passwordInput.value = ""; // 清空密码框
                checkLoginValidity(); // 重新检查按钮状态
>>>>>>> 4f5123f4f40ff24341cfcb8ebd54648461a4dfed
                loginBtn.innerText = "Log In";
                loginBtn.disabled = true;
            }
        })
        .catch(error => {
            loginBtn.innerText = "Log In";
            loginBtn.disabled = false;
<<<<<<< HEAD
            // ✅ 替换了原有的 alert()
            showToast("Network connection error. Please try again.", "error");
        });
    }

=======
            console.error('Error:', error);
            alert("Network connection error. Please try again.");
        });
    }

    // 忘记密码跳转逻辑
>>>>>>> 4f5123f4f40ff24341cfcb8ebd54648461a4dfed
    function goToRecovery() {
        const currentInput = loginIdentifierInput.value.trim();
        if (currentInput !== "") {
            window.location.href = "recovery.php?user=" + encodeURIComponent(currentInput);
        } else {
            window.location.href = "recovery.php";
        }
    }

<<<<<<< HEAD
    const toggleBtn = document.getElementById('togglePasswordBtn');
    const eyeIcon = document.getElementById('eyeIcon');
=======

    // === 2. 炫酷的卡通人物动画逻辑 ===
    const toggleBtn = document.getElementById('togglePasswordBtn');
    const eyeIcon = document.getElementById('eyeIcon');
    
>>>>>>> 4f5123f4f40ff24341cfcb8ebd54648461a4dfed
    let mouseX = window.innerWidth / 2;
    let mouseY = window.innerHeight / 2;
    let showPassword = false;

    const charPurple = document.getElementById('char-purple');
    const eyesPurple = document.getElementById('eyes-purple');
    const charBlack = document.getElementById('char-black');
    const eyesBlack = document.getElementById('eyes-black');
    const charOrange = document.getElementById('char-orange');
    const eyesOrange = document.getElementById('eyes-orange');
    const charYellow = document.getElementById('char-yellow');
    const eyesYellow = document.getElementById('eyes-yellow');
    const mouthYellow = document.getElementById('mouth-yellow');
    const pupils = document.querySelectorAll('.pupil');

    window.addEventListener('mousemove', (e) => {
        mouseX = e.clientX; mouseY = e.clientY;
        updatePupils(); updateBodyPos();
    });

    function updatePupils() {
        pupils.forEach(pupil => {
            const container = pupil.parentElement;
            const rect = container.getBoundingClientRect();
            const centerX = rect.left + rect.width / 2;
            const centerY = rect.top + rect.height / 2;
<<<<<<< HEAD
            let deltaX = mouseX - centerX; let deltaY = mouseY - centerY;
            
            if (passwordInput.value.length > 0 && showPassword) {
                deltaX = -50; deltaY = -40; 
            }
            const maxDistance = parseFloat(pupil.getAttribute('data-max') || 5);
            const distance = Math.min(Math.sqrt(deltaX ** 2 + deltaY ** 2), maxDistance);
            const angle = Math.atan2(deltaY, deltaX);
=======
            
            let deltaX = mouseX - centerX; let deltaY = mouseY - centerY;
            
            // 如果输入了密码并且显示了明文，大家统一避嫌看地板
            if (passwordInput.value.length > 0 && showPassword) {
                deltaX = -50; deltaY = -40; 
            }

            const maxDistance = parseFloat(pupil.getAttribute('data-max') || 5);
            const distance = Math.min(Math.sqrt(deltaX ** 2 + deltaY ** 2), maxDistance);
            const angle = Math.atan2(deltaY, deltaX);
            
>>>>>>> 4f5123f4f40ff24341cfcb8ebd54648461a4dfed
            pupil.style.transform = `translate(${Math.cos(angle) * distance}px, ${Math.sin(angle) * distance}px)`;
        });
    }

    function calculatePos(element) {
        if(!element) return { faceX: 0, faceY: 0, skew: 0 };
        const rect = element.getBoundingClientRect();
        const deltaX = mouseX - (rect.left + rect.width / 2);
        const deltaY = mouseY - (rect.top + rect.height / 3);
        return {
            faceX: Math.max(-15, Math.min(15, deltaX / 20)),
            faceY: Math.max(-10, Math.min(10, deltaY / 30)),
            skew: Math.max(-6, Math.min(6, -deltaX / 120))
        };
    }

    function updateBodyPos() {
        const pPos = calculatePos(charPurple); const bPos = calculatePos(charBlack);
        const oPos = calculatePos(charOrange); const yPos = calculatePos(charYellow);
        const hasPwd = passwordInput.value.length > 0;

<<<<<<< HEAD
        if (hasPwd && showPassword) {
            charPurple.style.transform = `skewX(0deg)`; charPurple.style.height = '400px';
            eyesPurple.style.left = '20px'; eyesPurple.style.top = '35px';
            charBlack.style.transform = `skewX(0deg)`; 
            eyesBlack.style.left = '10px'; eyesBlack.style.top = '28px';
            charOrange.style.transform = `skewX(0deg)`; 
            eyesOrange.style.left = '50px'; eyesOrange.style.top = '85px';
            charYellow.style.transform = `skewX(0deg)`; 
            eyesYellow.style.left = '20px'; eyesYellow.style.top = '35px';
            mouthYellow.style.left = '10px'; mouthYellow.style.top = '88px';
        } else {
            charPurple.style.transform = `skewX(${pPos.skew}deg)`; charPurple.style.height = '400px';
            eyesPurple.style.left = `${45 + pPos.faceX}px`; eyesPurple.style.top = `${40 + pPos.faceY}px`;
            charBlack.style.transform = `skewX(${bPos.skew}deg)`;
            eyesBlack.style.left = `${26 + bPos.faceX}px`; eyesBlack.style.top = `${32 + bPos.faceY}px`;
            charOrange.style.transform = `skewX(${oPos.skew}deg)`;
            eyesOrange.style.left = `${82 + oPos.faceX}px`; eyesOrange.style.top = `${90 + oPos.faceY}px`;
=======
        // 状态1：正在看明文密码 (大家统一避嫌不看，保持乖巧)
        if (hasPwd && showPassword) {
            charPurple.style.transform = `skewX(0deg)`; charPurple.style.height = '400px';
            eyesPurple.style.left = '20px'; eyesPurple.style.top = '35px';
            
            charBlack.style.transform = `skewX(0deg)`; 
            eyesBlack.style.left = '10px'; eyesBlack.style.top = '28px';
            
            charOrange.style.transform = `skewX(0deg)`; 
            eyesOrange.style.left = '50px'; eyesOrange.style.top = '85px';
            
            charYellow.style.transform = `skewX(0deg)`; 
            eyesYellow.style.left = '20px'; eyesYellow.style.top = '35px';
            mouthYellow.style.left = '10px'; mouthYellow.style.top = '88px';
        } 
        // 状态2：正常跟随鼠标 (去掉了紫色小人突兀的伸脖子和对视，全部统一)
        else {
            charPurple.style.transform = `skewX(${pPos.skew}deg)`; charPurple.style.height = '400px';
            eyesPurple.style.left = `${45 + pPos.faceX}px`; eyesPurple.style.top = `${40 + pPos.faceY}px`;
            
            charBlack.style.transform = `skewX(${bPos.skew}deg)`;
            eyesBlack.style.left = `${26 + bPos.faceX}px`; eyesBlack.style.top = `${32 + bPos.faceY}px`;
            
            charOrange.style.transform = `skewX(${oPos.skew}deg)`;
            eyesOrange.style.left = `${82 + oPos.faceX}px`; eyesOrange.style.top = `${90 + oPos.faceY}px`;
            
>>>>>>> 4f5123f4f40ff24341cfcb8ebd54648461a4dfed
            charYellow.style.transform = `skewX(${yPos.skew}deg)`;
            eyesYellow.style.left = `${52 + yPos.faceX}px`; eyesYellow.style.top = `${40 + yPos.faceY}px`;
            mouthYellow.style.left = `${40 + yPos.faceX}px`; mouthYellow.style.top = `${88 + yPos.faceY}px`;
        }
    }

<<<<<<< HEAD
    passwordInput.addEventListener('input', () => { updateBodyPos(); checkLoginValidity(); });
    
=======
    // 互动事件 (删除了点击输入框时触发特殊动画的代码)
    passwordInput.addEventListener('input', () => { updateBodyPos(); checkLoginValidity(); });
    
    // 密码可见性切换 (SVG无缝替换)
>>>>>>> 4f5123f4f40ff24341cfcb8ebd54648461a4dfed
    toggleBtn.addEventListener('click', () => {
        showPassword = !showPassword;
        passwordInput.type = showPassword ? 'text' : 'password';
        if(showPassword) {
            eyeIcon.innerHTML = `<path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/>`;
        } else {
            eyeIcon.innerHTML = `<path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"/><path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"/><path d="M6.61 6.61A13.526 13.526 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"/><line x1="2" x2="22" y1="2" y2="22"/>`;
        }
        updateBodyPos(); updatePupils();
    });

<<<<<<< HEAD
=======
    // 眨眼动画
>>>>>>> 4f5123f4f40ff24341cfcb8ebd54648461a4dfed
    function blinkLoop(charEyesId) {
        const eyes = document.querySelectorAll(`#${charEyesId} .eye-ball`);
        if(!eyes.length) return;
        setInterval(() => {
            eyes.forEach(eye => eye.classList.add('blink'));
            setTimeout(() => eyes.forEach(eye => eye.classList.remove('blink')), 150);
        }, Math.random() * 4000 + 3000);
    }
    blinkLoop('eyes-purple'); blinkLoop('eyes-black');
<<<<<<< HEAD
=======
    setTimeout(updateBody);

>>>>>>> 4f5123f4f40ff24341cfcb8ebd54648461a4dfed
</script>
</body>
</html>