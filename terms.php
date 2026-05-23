<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PlayLearn - Terms of Service</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: { DEFAULT: '#0f172a', foreground: '#f8fafc' },
                        background: '#ffffff',
                        foreground: '#020817',
                        border: '#e2e8f0'
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
        .content-scroll::-webkit-scrollbar { width: 6px; }
        .content-scroll::-webkit-scrollbar-track { background: #f1f5f9; }
        .content-scroll::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        
        .header-back-btn {
            position: absolute; top: 20px; right: 30px; z-index: 50;
            height: 38px; padding: 0 16px; display: flex; justify-content: center; align-items: center; gap: 6px;
            background-color: white; border: 1px solid #e2e8f0; border-radius: 8px;
            font-weight: 600; font-size: 14px; cursor: pointer; transition: 0.2s;
            color: #0f172a; box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        .header-back-btn:hover { background-color: #f8fafc; border-color: #cbd5e1; }
    </style>
</head>
<body class="h-screen w-full flex bg-background font-sans text-foreground overflow-hidden relative">

<button onclick="window.history.back()" class="header-back-btn">
    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
    Back
</button>

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
        <div class="relative z-20 flex items-center gap-8 text-sm text-primary-foreground/60"><span>Privacy Policy</span><span>Terms of Service</span></div>
    </div>

    <div class="flex flex-col pt-16 px-8 pb-8 lg:px-16 lg:pb-16 bg-background h-full relative">
        <div class="flex-1 overflow-hidden flex flex-col">
            <h1 class="text-3xl font-bold mb-2">Terms of Service</h1>
            <p class="text-muted-foreground text-sm mb-6">Effective Date: April 27, 2026</p>

            <div class="flex-1 overflow-y-auto pr-4 content-scroll space-y-6 text-sm leading-relaxed text-slate-600">
                <section>
                    <h2 class="text-lg font-bold text-primary mb-2">1. Acceptance of Terms</h2>
                    <p>By creating an account on PlayLearn, you agree to comply with these Terms of Service. If you are under 18, please review these terms with your parent or guardian.</p>
                </section>
                <section>
                    <h2 class="text-lg font-bold text-primary mb-2">2. User Conduct</h2>
                    <p>PlayLearn is a community for learning and fun. Users agree NOT to:
                        <ul class="list-disc ml-5 mt-2 space-y-1">
                            <li>Attempt to hack, exploit bugs, or manipulate high scores.</li>
                            <li>Use offensive or inappropriate language in usernames.</li>
                            <li>Harass or disrupt the experience of other players.</li>
                            <li>Share account passwords with anyone.</li>
                        </ul>
                    </p>
                </section>
                <section>
                    <h2 class="text-lg font-bold text-primary mb-2">3. Account Responsibility</h2>
                    <p>You are responsible for maintaining the confidentiality of your account credentials. Any activity under your username is your responsibility.</p>
                </section>
                <section>
                    <h2 class="text-lg font-bold text-primary mb-2">4. Modification of Service</h2>
                    <p>We reserve the right to add new games, update existing levels, or change site features at any time to improve the learning experience.</p>
                </section>
                <section>
                    <h2 class="text-lg font-bold text-primary mb-2">5. Account Termination</h2>
                    <p>PlayLearn administrators reserve the right to suspend or ban accounts that violate these terms, specifically targeting cheating or harmful behavior.</p>
                </section>
            </div>

            <div class="mt-8 pt-6 border-t">
                <button onclick="window.history.back()" class="w-full h-12 bg-primary text-primary-foreground rounded-md font-bold hover:bg-primary/90 transition-colors">
                    Back to Registration
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    let mouseX = window.innerWidth / 2; let mouseY = window.innerHeight / 2;
    const charPurple = document.getElementById('char-purple'); const eyesPurple = document.getElementById('eyes-purple');
    const charBlack = document.getElementById('char-black'); const eyesBlack = document.getElementById('eyes-black');
    const charOrange = document.getElementById('char-orange'); const eyesOrange = document.getElementById('eyes-orange');
    const charYellow = document.getElementById('char-yellow'); const eyesYellow = document.getElementById('eyes-yellow');
    const mouthYellow = document.getElementById('mouth-yellow'); const pupils = document.querySelectorAll('.pupil');
    window.addEventListener('mousemove', (e) => { mouseX = e.clientX; mouseY = e.clientY; updatePupils(); updateBodyPos(); });
    function updatePupils() { pupils.forEach(pupil => { const container = pupil.parentElement; const rect = container.getBoundingClientRect(); const centerX = rect.left + rect.width / 2; const centerY = rect.top + rect.height / 2; let deltaX = mouseX - centerX; let deltaY = mouseY - centerY; const maxDistance = parseFloat(pupil.getAttribute('data-max') || 5); const distance = Math.min(Math.sqrt(deltaX ** 2 + deltaY ** 2), maxDistance); const angle = Math.atan2(deltaY, deltaX); pupil.style.transform = `translate(${Math.cos(angle) * distance}px, ${Math.sin(angle) * distance}px)`; }); }
    function calculatePos(element) { if(!element) return { faceX: 0, faceY: 0, skew: 0 }; const rect = element.getBoundingClientRect(); const deltaX = mouseX - (rect.left + rect.width / 2); const deltaY = mouseY - (rect.top + rect.height / 3); return { faceX: Math.max(-15, Math.min(15, deltaX / 20)), faceY: Math.max(-10, Math.min(10, deltaY / 30)), skew: Math.max(-6, Math.min(6, -deltaX / 120)) }; }
    function updateBodyPos() { 
        const pPos = calculatePos(charPurple); const bPos = calculatePos(charBlack); 
        const oPos = calculatePos(charOrange); const yPos = calculatePos(charYellow); 
        charPurple.style.transform = `skewX(${pPos.skew}deg)`; 
        eyesPurple.style.left = `${45 + pPos.faceX}px`; eyesPurple.style.top = `${40 + pPos.faceY}px`; 
        charBlack.style.transform = `skewX(${bPos.skew}deg)`; 
        eyesBlack.style.left = `${26 + bPos.faceX}px`; eyesBlack.style.top = `${32 + bPos.faceY}px`; 
        charOrange.style.transform = `skewX(${oPos.skew}deg)`; 
        eyesOrange.style.left = `${82 + oPos.faceX}px`; eyesOrange.style.top = `${90 + oPos.faceY}px`; 
        charYellow.style.transform = `skewX(${yPos.skew}deg)`; 
        eyesYellow.style.left = `${52 + yPos.faceX}px`; eyesYellow.style.top = `${40 + yPos.faceY}px`; 
        mouthYellow.style.left = `${40 + yPos.faceX}px`; mouthYellow.style.top = `${88 + yPos.faceY}px`; 
    }
    function blinkLoop(charEyesId) { const eyes = document.querySelectorAll(`#${charEyesId} .eye-ball`); if(!eyes.length) return; setInterval(() => { eyes.forEach(eye => eye.classList.add('blink')); setTimeout(() => eyes.forEach(eye => eye.classList.remove('blink')), 150); }, Math.random() * 4000 + 3000); }
    blinkLoop('eyes-purple'); blinkLoop('eyes-black');
</script>
</body>
</html>