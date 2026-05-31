<?php
// 获取 URL 中的状态参数
$status = $_GET['status'] ?? '';

// 获取家长当前的邮箱
$parent_id = $_SESSION['user_id'];
$res_parent = $conn->query("SELECT email, username FROM users WHERE id = $parent_id");
$parent_data = $res_parent->fetch_assoc();
$current_email = $parent_data['email'] ?? '';
$current_username = $parent_data['username'] ?? '';
?>

<div class="container-fluid px-0">
    <div class="mb-4">
        <h3 class="fw-bold text-dark mb-1">Account Settings</h3>
        <p class="text-muted small">Manage your personal profile and linked child accounts.</p>
    </div>

    <?php if ($status === 'link_success'): ?>
        <div class="alert alert-success border-0 shadow-sm rounded-4 mb-4 d-flex align-items-center">
            <i class="fas fa-check-circle fs-4 me-3"></i> 
            <div><strong>Success!</strong> Account linked successfully.</div>
        </div>
    <?php elseif ($status === 'already_linked'): ?>
        <div class="alert alert-info border-0 shadow-sm rounded-4 mb-4 d-flex align-items-center">
            <i class="fas fa-info-circle fs-4 me-3"></i> 
            <div>This account is already linked to your dashboard.</div>
        </div>
    <?php elseif ($status === 'invalid_code'): ?>
        <div class="alert alert-danger border-0 shadow-sm rounded-4 mb-4 d-flex align-items-center">
            <i class="fas fa-times-circle fs-4 me-3"></i> 
            <div><strong>Invalid Code!</strong> Please check the code and try again.</div>
        </div>
    <?php elseif ($status === 'history_cleared'): ?>
        <div class="alert alert-success border-0 shadow-sm rounded-4 mb-4 d-flex align-items-center" style="background-color: #d1e7dd; color: #0f5132;">
            <i class="fas fa-trash-check fs-4 me-3"></i> 
            <div><strong>History Cleared!</strong> All game records have been permanently deleted.</div>
        </div>
    <?php elseif ($status === 'child_created'): ?>
        <div class="alert alert-success border-0 shadow-sm rounded-4 mb-4 d-flex align-items-center">
            <i class="fas fa-check-circle fs-4 me-3"></i>
            <div><strong>Child account created!</strong> The account is already linked to your dashboard.</div>
        </div>
    <?php elseif ($status === 'child_age_invalid'): ?>
        <div class="alert alert-danger border-0 shadow-sm rounded-4 mb-4 d-flex align-items-center">
            <i class="fas fa-times-circle fs-4 me-3"></i>
            <div>Child accounts are limited to ages 4 to 12.</div>
        </div>
    <?php elseif ($status === 'child_password_invalid'): ?>
        <div class="alert alert-danger border-0 shadow-sm rounded-4 mb-4 d-flex align-items-center">
            <i class="fas fa-times-circle fs-4 me-3"></i>
            <div>Password must be 8 to 200 characters and include an uppercase letter plus a number.</div>
        </div>
    <?php elseif ($status === 'child_duplicate'): ?>
        <div class="alert alert-danger border-0 shadow-sm rounded-4 mb-4 d-flex align-items-center">
            <i class="fas fa-times-circle fs-4 me-3"></i>
            <div>This child username is already taken.</div>
        </div>
    <?php elseif ($status === 'child_missing_fields'): ?>
        <div class="alert alert-danger border-0 shadow-sm rounded-4 mb-4 d-flex align-items-center">
            <i class="fas fa-times-circle fs-4 me-3"></i>
            <div>Please fill in the child username, IC, password, and birthday.</div>
        </div>
    <?php endif; ?>

    <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
        <div class="card-header bg-white border-0 pt-4 pb-0 px-4">
            <h5 class="fw-bold text-dark mb-0"><i class="fas fa-user-circle text-primary me-2"></i> Profile Details</h5>
        </div>
        <div class="card-body p-4">
            <div class="row g-4">
                <div class="col-md-6">
                    <label class="form-label small fw-bold text-secondary text-uppercase tracking-wider">Parent Username</label>
                    <div class="input-group shadow-sm" style="border-radius: 12px; overflow: hidden;">
                        <span class="input-group-text bg-light border-0"><i class="fas fa-user text-muted"></i></span>
                        <input type="text" class="form-control border-0 bg-light py-2" value="<?php echo htmlspecialchars($current_username); ?>" readonly>
                    </div>
                    <small class="text-muted mt-2 d-block"><i class="fas fa-lock me-1" style="font-size: 10px;"></i> Username cannot be changed.</small>
                </div>

                <div class="col-md-6">
                    <label class="form-label small fw-bold text-secondary text-uppercase tracking-wider">Email Address</label>
                    <div class="input-group shadow-sm" style="border-radius: 12px; overflow: hidden; border: 1px solid #e0e0e0;">
                        <span class="input-group-text bg-white border-0"><i class="fas fa-envelope text-muted"></i></span>
                        <input type="email" id="newEmail" class="form-control border-0 py-2 shadow-none" value="<?php echo htmlspecialchars($current_email); ?>">
                        <button class="btn btn-primary fw-bold px-4" type="button" id="sendOtpBtn" onclick="requestEmailOtp()">
                            Send OTP
                        </button>
                    </div>
                    
                    <div id="otpSection" class="mt-3 p-3 bg-light rounded-4 border" style="display: none;">
                        <label class="form-label small fw-bold text-dark">Enter Verification Code</label>
                        <div class="input-group shadow-sm" style="border-radius: 10px; overflow: hidden;">
                            <input type="text" id="emailOtp" class="form-control border-0 text-center tracking-widest fs-5 py-2 shadow-none" placeholder="· · · · · ·" maxlength="6">
                            <button class="btn btn-success fw-bold px-3" type="button" onclick="verifyAndSaveEmail()">
                                <i class="fas fa-check me-1"></i> Verify
                            </button>
                        </div>
                    </div>
                    <small id="emailStatusMsg" class="mt-2 d-block fw-bold"></small>
                </div>
            </div>
        </div>
    </div>
    
    <div class="card border-0 shadow-sm rounded-4 mb-4" style="background: linear-gradient(145deg, #fff7ed 0%, #ffffff 100%); border-left: 5px solid var(--blob-orange, #FF9B6B) !important;">
        <div class="card-body p-4">
            <div class="d-flex flex-column flex-lg-row justify-content-between gap-4">
                <div class="flex-grow-1">
                    <h5 class="fw-bold text-dark mb-2">
                        <i class="fas fa-child me-2" style="color: var(--blob-orange, #FF9B6B);"></i> Create Child Account
                    </h5>
                    <p class="small text-muted mb-0">Create a child login from the parent dashboard. The new account is linked automatically and is limited to ages 4 to 12.</p>
                </div>
                <form action="process_create_child.php" method="POST" class="flex-grow-1" style="max-width: 560px;">
                    <div class="row g-2">
                        <div class="col-md-6">
                            <input type="text" name="child_username" class="form-control fw-bold" placeholder="Child username" required>
                        </div>
                        <div class="col-md-6">
                            <input type="password" name="child_password" class="form-control fw-bold" placeholder="Password" required>
                        </div>
                        <div class="col-md-12">
                            <input type="text" name="child_ic" class="form-control fw-bold" placeholder="Child IC / ID number" required>
                        </div>
                        <div class="col-md-3">
                            <input type="number" name="child_month" min="1" max="12" class="form-control" placeholder="Month" required>
                        </div>
                        <div class="col-md-3">
                            <input type="number" name="child_day" min="1" max="31" class="form-control" placeholder="Day" required>
                        </div>
                        <div class="col-md-3">
                            <input type="number" name="child_year" min="<?php echo date('Y') - 12; ?>" max="<?php echo date('Y') - 4; ?>" class="form-control" placeholder="Year" required>
                        </div>
                        <div class="col-md-3">
                            <select name="child_gender" class="form-select">
                                <option value="">Gender</option>
                                <option value="girl">Girl</option>
                                <option value="boy">Boy</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary w-100 fw-bold">Create and Link Child</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="card border-0 shadow-sm rounded-4 mb-4" style="background: linear-gradient(145deg, #f0f7ff 0%, #ffffff 100%); border-left: 5px solid #4A90E2 !important;">
        <div class="card-body p-4 d-flex flex-column flex-md-row align-items-md-center justify-content-between">
            <div class="mb-3 mb-md-0 me-md-4">
                <h5 class="fw-bold text-primary mb-2">
                    <i class="fas fa-link me-2"></i> Link Child's Account
                </h5>
                <p class="small text-muted mb-0">Enter the 6-digit linking code shown on your child's "My Profile" page to sync their learning data to your dashboard.</p>
            </div>
            
            <form action="process_link.php" method="POST" class="d-flex align-items-center m-0">
                <div class="input-group shadow-sm" style="border-radius: 12px; overflow: hidden; width: auto;">
                    <span class="input-group-text bg-white border-0"><i class="fas fa-key text-muted"></i></span>
                    <input type="text" name="child_code" class="form-control border-0 fw-bold text-center shadow-none" 
                           placeholder="E95104" maxlength="6" style="width: 130px; letter-spacing: 2px; height: 48px;" required>
                    <button type="submit" class="btn btn-primary px-4 fw-bold shadow-none" style="height: 48px;">
                        Confirm
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm rounded-4" style="background: #fffafb; border: 1px solid #ffccd5;">
        <div class="card-body p-4 d-flex flex-column flex-md-row align-items-md-center justify-content-between">
            <div class="mb-3 mb-md-0">
                <h5 class="fw-bold text-danger mb-1"><i class="fas fa-exclamation-triangle me-2"></i> Danger Zone</h5>
                <p class="small text-muted mb-0">Resetting all game data will wipe the history for the currently selected child. <b class="text-danger">This cannot be undone.</b></p>
            </div>
            <button type="button" class="btn btn-outline-danger px-4 py-2 fw-bold shadow-sm" style="border-radius: 12px; white-space: nowrap;" data-bs-toggle="modal" data-bs-target="#deleteConfirmModal">
                <i class="fas fa-trash-alt me-2"></i> Reset Game Scores
            </button>
        </div>
    </div>
</div>

<div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
      <div class="modal-header bg-danger text-white border-0 p-4">
        <h5 class="modal-title fw-bold">
            <i class="fas fa-exclamation-triangle me-2"></i> Confirm Data Reset
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-4 text-center">
        <div class="mb-4 mt-2">
            <div class="bg-danger-subtle text-danger rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                <i class="fas fa-trash-alt fa-3x"></i>
            </div>
        </div>
        <h4 class="fw-bold text-dark mb-3">Are you absolutely sure?</h4>
        <p class="text-muted mb-0">This action will permanently delete <b>all game records, scores, and history</b> for this child. This data cannot be recovered.</p>
      </div>
      <div class="modal-footer border-0 bg-light p-3 justify-content-center gap-3">
        <button type="button" class="btn btn-light px-4 fw-bold border shadow-sm" data-bs-dismiss="modal" style="border-radius: 12px;">Cancel</button>
        <a href="process_clear_history.php?child_id=<?php echo $target_user_id ?? 0; ?>&redirect=settings" class="btn btn-danger px-4 fw-bold shadow-sm" style="border-radius: 12px;">Yes, Delete Everything</a>
      </div>
    </div>
  </div>
</div>

<script>
function requestEmailOtp() {
    const newEmail = document.getElementById('newEmail').value.trim();
    const btn = document.getElementById('sendOtpBtn');
    const msgBox = document.getElementById('emailStatusMsg');

    if (!newEmail.includes('@')) {
        msgBox.innerHTML = "<span class='text-danger'><i class='fas fa-exclamation-circle'></i> Please enter a valid email address.</span>";
        return;
    }

    btn.disabled = true;
    btn.innerText = "Sending...";

    let formData = new FormData();
    formData.append('new_email', newEmail);

    fetch('ajax_send_setting_otp.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'success') {
            msgBox.innerHTML = "<span class='text-success'><i class='fas fa-paper-plane'></i> OTP sent to your new email!</span>";
            
            // 💡 加入了一点展开动画效果
            const otpSection = document.getElementById('otpSection');
            otpSection.style.display = 'block';
            otpSection.animate([{ opacity: 0, transform: 'translateY(-10px)' }, { opacity: 1, transform: 'translateY(0)' }], { duration: 300, fill: 'forwards' });
            
            let seconds = 60;
            let countdown = setInterval(() => {
                seconds--;
                btn.innerText = `Retry (${seconds}s)`;
                if (seconds <= 0) {
                    clearInterval(countdown);
                    btn.disabled = false;
                    btn.innerText = "Send OTP";
                }
            }, 1000);
        } else {
            msgBox.innerHTML = `<span class='text-danger'><i class='fas fa-exclamation-circle'></i> ${data.message}</span>`;
            btn.disabled = false;
            btn.innerText = "Send OTP";
        }
    });
}

function verifyAndSaveEmail() {
    const otp = document.getElementById('emailOtp').value.trim();
    const msgBox = document.getElementById('emailStatusMsg');

    if (otp.length !== 6) {
        msgBox.innerHTML = "<span class='text-danger'><i class='fas fa-exclamation-circle'></i> Please enter a 6-digit code.</span>";
        return;
    }

    let formData = new FormData();
    formData.append('otp', otp);

    fetch('ajax_update_email.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'success') {
            msgBox.innerHTML = "<span class='text-success'><i class='fas fa-check-circle'></i> Email updated successfully!</span>";
            document.getElementById('otpSection').style.display = 'none';
            document.getElementById('sendOtpBtn').style.display = 'none'; 
        } else {
            msgBox.innerHTML = `<span class='text-danger'><i class='fas fa-times-circle'></i> ${data.message}</span>`;
        }
    });
}
</script>
