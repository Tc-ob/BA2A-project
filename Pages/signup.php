<?php
session_start();
include '../Database/connection.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - AuroraTech</title>
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
            <h2>Create Account</h2>
            <p>Join AuroraTech to experience the pinnacle of design.</p>
            
            <?php
            if(isset($_SESSION['error'])){
                echo '<p class="error-msg" style="color: #ff4d4d; margin-bottom: 10px;">'.$_SESSION['error'].'</p>';
                unset($_SESSION['error']);
            }
            ?>
            
            <form id="signup-form" name="signup-form" method="POST" class="auth-form" action="../Controller/signupcontroller.php">
                <div class="input-group">
                    <input name="name" type="text" id="signup-name" required placeholder=" ">
                    <label for="signup-name">Full Name</label>
                    <div class="input-glow"></div>
                </div>
                <div class="input-group">
                    <input name="email" type="email" id="signup-email" required placeholder=" ">
                    <label for="signup-email">Email Address</label>
                    <div class="input-glow"></div>
                </div>
                <div class="input-group">
                    <input name="password" type="password" id="signup-password" required placeholder=" ">
                    <label for="signup-password">Password</label>
                    <div class="input-glow"></div>
                </div>
                
                <div class="form-options">
                    <label  class="checkbox-container">
                        <input name="terms" type="checkbox" id="signup-terms" required>
                        <span class="checkmark"></span>
                        I agree to the <a href="#" class="text-link">Terms & Conditions</a>
                    </label>
                </div>

                <!-- Submit Button -->
                <button type="submit" id="signup-submit" name="signup-submit" class="btn btn-primary submit-btn">Sign Up Now</button>
            </form>
            
            <p class="switch-auth">Already a member? <a href="login.php" class="text-link text-bold">Log In</a></p>
        </div>
    </div>
    <script src="theme-switcher.js"></script>
</body>
</html>
