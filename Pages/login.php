<?php
session_start();
include '../Database/connection.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log In - AuroraTech</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&display=swap" rel="stylesheet">
</head>
<body class="auth-page">
    <div class="theme-switcher" style="position: absolute; top: 2rem; right: 2rem; z-index: 100;">
        <div class="theme-btn t-dark" id="btn-dark" onclick="setTheme('dark')" title="Dark Mode"></div>
        <div class="theme-btn t-light" id="btn-light" onclick="setTheme('light')" title="Light Mode"></div>
        <div class="theme-btn t-sky" id="btn-sky" onclick="setTheme('sky')" title="Sky Blue Mode"></div>
    </div>

    <div class="auth-container fade-in-scale">
        <a href="index.php" class="auth-logo">
            <span class="gradient-text">Aurora</span>Tech
        </a>
        
        <div class="auth-card glass">
            <h2>Welcome Back</h2>
            <p>Log in to access your dashboard and exclusive offers.</p>

            <?php
if (isset($_SESSION['success'])) {
    echo '<p class="success-msg" style="color: #4CAF50; margin-bottom: 10px;">' . $_SESSION['success'] . '</p>';
    unset($_SESSION['success']);
}
if (isset($_SESSION['error'])) {
    echo '<p class="error-msg" style="color: #ff4d4d; margin-bottom: 10px;">' . $_SESSION['error'] . '</p>';
    unset($_SESSION['error']);
}
?>
            
            <form id="login-form" name="login-form" class="auth-form" action="./dashboard.php" method="POST">
                <div class="input-group">
                    <input type="email" id="login-email" name="email" required placeholder=" ">
                    <label for="login-email">Email Address</label>
                    <div class="input-glow"></div>
                </div>
                <div class="input-group">
                    <input type="password" id="login-password" name="password" required placeholder=" ">
                    <label for="login-password">Password</label>
                    <div class="input-glow"></div>
                </div>
                
                <div class="form-options">
                    <label class="checkbox-container">
                        <input type="checkbox" id="login-remember" name="remember">
                        <span class="checkmark"></span>
                        Remember me
                    </label>
                    <a href="#" class="text-link text-small">Forgot Password?</a>
                </div>

                <!-- Submit Button -->
                <button type="submit" id="login-submit" name="submit" class="btn btn-primary submit-btn">Log In to Account</button>
            </form>
            
            <p class="switch-auth">New to AuroraTech? <a href="signup.php" class="text-link text-bold">Create Account</a></p>
        </div>
    </div>
    <script src="theme-switcher.js"></script>
</body>
</html>
