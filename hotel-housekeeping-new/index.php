<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hotel Housekeeping System</title>
    <link rel="icon" type="image/svg+xml" href='data:image/svg+xml, <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="maroon"><path d="M484-80q-84 0-157.5-32t-128-86.5Q144-253 112-326.5T80-484q0-146 93-257.5T410-880q-18 99 11 193.5T521-521q71 71 165.5 100T880-410q-26 144-138 237T484-80Zm0-80q88 0 163-44t118-121q-86-8-163-43.5T464-465q-61-61-97-138t-43-163q-77 43-120.5 118.5T160-484q0 135 94.5 229.5T484-160Zm-20-305Z"/></svg>'>
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link rel="stylesheet" href="styles/global.css">
    <link rel="stylesheet" href="styles/index.css">
</head>
<body>
    <header><a href="index.php"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 -960 960 960" fill="white"><path d="M484-80q-84 0-157.5-32t-128-86.5Q144-253 112-326.5T80-484q0-146 93-257.5T410-880q-18 99 11 193.5T521-521q71 71 165.5 100T880-410q-26 144-138 237T484-80Zm0-80q88 0 163-44t118-121q-86-8-163-43.5T464-465q-61-61-97-138t-43-163q-77 43-120.5 118.5T160-484q0 135 94.5 229.5T484-160Zm-20-305Z"/></svg><h1>Lunera Hotel</h1></a></header>
    <div class="wrapper">
        <section class="login-area">
            <h1>Hotel Housekeeping System</h1>
            <form id="login" action="includes/login-process.php" method="post" autocomplete="off">
                <h2>Login</h2>
                <p>Enter your credentials to access your account</p>
                <label for="username">Enter Username</label>
                <input type="text" name="username" id="username" placeholder="Username" required>
                <label for="password">Enter Password</label>
                <div style="display: flex; gap: 8px;">
                    <input type="password" name="password" id="password" placeholder="Password" required style="flex: 1;">
                    <button type="button" id="toggle-login-password" style="background: none; border: none; cursor: pointer; padding: 0 14px; min-width: 48px; display: flex; align-items: center; justify-content: center;">
                        <span class="material-icons" style="font-size: 20px; color: #666;">visibility</span>
                    </button>
                </div>
                <button type="submit">Login</button>
                <button type="button" id="create-account-btn" style="margin-top: 12px; background: white; color: maroon; border: 1px solid maroon;">Create Account</button>
            </form>
        </section>
        <section class="mission-vision">
            <h2>Our Mission</h2>
            <p>To provide an unparalleled standard of cleanliness and hospitality, ensuring every guest feels comfortable, valued, and cared for from the moment they arrive. We are committed to using innovative solutions and empowering our staff to deliver excellence in every corner.</p>
            <h2>Our Vision</h2>
            <p>To be the leading name in hotel housekeeping management, recognized for our efficiency, our dedication to sustainable practices, and our passion for creating pristine environments that define the future of hospitality.</p>
        </section>
    </div>

    <!-- Registration Modal -->
    <div id="register-overlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; justify-content: center; align-items: center;">
        <div id="register-modal" style="background: white; border-radius: 12px; width: 90%; max-width: 450px; position: relative; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
            <button id="close-register" style="position: absolute; top: 16px; right: 16px; background: none; border: none; font-size: 24px; cursor: pointer; color: #666; line-height: 1; padding: 0; width: 30px; height: 30px;">&times;</button>
            <div style="padding: 32px;">
                <h2 style="margin: 0 0 8px 0; color: #6a2323; font-size: 1.75rem;">Create Account</h2>
                <p style="color: #666; margin-bottom: 24px; font-size: 0.95rem;">Register with your staff UUID</p>
                <form id="register-form">
                    <div style="margin-bottom: 16px;">
                        <label style="display: block; margin-bottom: 6px; font-weight: 600; color: #333;">Username *</label>
                        <input type="text" name="reg_username" required style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 1rem;">
                    </div>
                    <div style="margin-bottom: 16px;">
                        <label style="display: block; margin-bottom: 6px; font-weight: 600; color: #333;">Password *</label>
                        <div style="display: flex; gap: 8px;">
                            <input type="password" name="reg_password" id="reg_password" required style="flex: 1; padding: 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 1rem;">
                            <button type="button" id="toggle-reg-password" style="background: none; border: none; cursor: pointer; padding: 0 14px; min-width: 52px; display: flex; align-items: center; justify-content: center;">
                                <span class="material-icons" style="font-size: 20px; color: #666;">visibility</span>
                            </button>
                        </div>
                    </div>
                    <div style="margin-bottom: 16px;">
                        <label style="display: block; margin-bottom: 6px; font-weight: 600; color: #333;">Re-type Password *</label>
                        <div style="display: flex; gap: 8px;">
                            <input type="password" name="reg_password_confirm" id="reg_password_confirm" required style="flex: 1; padding: 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 1rem;">
                            <button type="button" id="toggle-reg-confirm" style="background: none; border: none; cursor: pointer; padding: 0 14px; min-width: 52px; display: flex; align-items: center; justify-content: center;">
                                <span class="material-icons" style="font-size: 20px; color: #666;">visibility</span>
                            </button>
                        </div>
                    </div>
                    <div style="margin-bottom: 24px;">
                        <label style="display: block; margin-bottom: 6px; font-weight: 600; color: #333;">Staff UUID *</label>
                        <input type="text" name="reg_uuid" required style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 1rem;" placeholder="Enter your assigned UUID">
                    </div>
                    <button type="submit" style="width: 100%; padding: 12px; border: none; background: maroon; color: white; border-radius: 6px; font-size: 1rem; font-weight: 600; cursor: pointer;">Register</button>
                </form>
            </div>
        </div>
    </div>

    <script>
    // Password toggle functionality
    function togglePasswordVisibility(inputId, buttonId) {
        const input = document.getElementById(inputId);
        const button = document.getElementById(buttonId);
        const icon = button.querySelector('.material-icons');
        
        button.addEventListener('click', function() {
            if (input.type === 'password') {
                input.type = 'text';
                icon.textContent = 'visibility_off';
            } else {
                input.type = 'password';
                icon.textContent = 'visibility';
            }
        });
    }
    
    // Initialize password toggles
    togglePasswordVisibility('password', 'toggle-login-password');
    togglePasswordVisibility('reg_password', 'toggle-reg-password');
    togglePasswordVisibility('reg_password_confirm', 'toggle-reg-confirm');
    
    // Registration modal logic
    const createAccountBtn = document.getElementById('create-account-btn');
    const registerOverlay = document.getElementById('register-overlay');
    const closeRegister = document.getElementById('close-register');
    const registerForm = document.getElementById('register-form');

    createAccountBtn.addEventListener('click', function() {
        registerOverlay.style.display = 'flex';
    });

    closeRegister.addEventListener('click', function() {
        registerOverlay.style.display = 'none';
        registerForm.reset();
    });

    registerOverlay.addEventListener('click', function(e) {
        if (e.target === registerOverlay) {
            registerOverlay.style.display = 'none';
            registerForm.reset();
        }
    });

    registerForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const password = registerForm.querySelector('input[name="reg_password"]').value;
        const confirmPassword = registerForm.querySelector('input[name="reg_password_confirm"]').value;
        
        if (password !== confirmPassword) {
            alert('Passwords do not match!');
            return;
        }
        
        const formData = new FormData(registerForm);
        
        fetch('includes/register-process.php', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                alert('Account created successfully! You can now login.');
                registerOverlay.style.display = 'none';
                registerForm.reset();
            } else {
                alert('Registration failed: ' + (data.error || 'Unknown error'));
            }
        })
        .catch(err => {
            console.error('Registration error:', err);
            alert('An error occurred during registration.');
        });
    });
    </script>
</body>
</html>