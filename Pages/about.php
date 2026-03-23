<?php
session_start();
include '../Database/connection.php';
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - AuroraTech</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&display=swap" rel="stylesheet">
    <style>
        :root {
            <?php if (isset($_SESSION['profile_color'])): ?>
            --profile-accent: <?php echo $_SESSION['profile_color']; ?>;
            <?php endif; ?>
        }
    </style>
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar glass">
        <div class="logo">
            <a href="index.php"><span class="gradient-text">Aurora</span>Tech</a>
        </div>
        
        <ul class="nav-links">
            <li><a href="index.php">Home</a></li>
            <li><a href="products.php">Products</a></li>
            <li><a href="categories.php">Categories</a></li>
            <li><a href="about.php" style="color: var(--text-main);">About</a></li>
        </ul>

        <div class="auth-buttons" style="display:flex; align-items:center;">
            <!-- Theme Switcher -->
            <div class="theme-switcher">
                <div class="theme-btn t-dark" id="btn-dark" onclick="setTheme('dark')" title="Dark Mode"></div>
                <div class="theme-btn t-light" id="btn-light" onclick="setTheme('light')" title="Light Mode"></div>
                <div class="theme-btn t-sky" id="btn-sky" onclick="setTheme('sky')" title="Sky Blue Mode"></div>
            </div>
            
            <?php if (isset($_SESSION['id'])): 
                $user_initial = strtoupper(substr($_SESSION['name'], 0, 1));
            ?>
                <span style="font-size:.9rem; color:var(--text-muted); margin-right: 1rem;">
                    <?php echo htmlspecialchars($_SESSION['name']); ?>
                </span>
                <a href="dashboard.php" style="text-decoration:none;">
                    <div class="avatar-circle"><?php echo $user_initial; ?></div>
                </a>
                <a href="../Controller/logout.php" class="btn btn-logout" style="margin-left:1rem;">Log Out</a>
            <?php else: ?>
                <a href="login.php" class="btn btn-outline">Log In</a>
                <a href="signup.php" class="btn btn-primary">Sign Up</a>
            <?php endif; ?>
        </div>
        
        <!-- Mobile Menu Toggle -->
        <div class="hamburger">
            <span></span><span></span><span></span>
        </div>
    </nav>
    <script src="theme-switcher.js"></script>

    <!-- Main Content Area -->
    <main class="page-padding about-page-wrapper">
        <div class="about-hero fade-in-up">
            <h1>We craft <span class="gradient-text">Experiences.</span></h1>
            <p>At AuroraTech, we believe technology should be beautiful, intuitive, and seamlessly integrated into your life. Every product is a masterpiece of design and engineering.</p>
        </div>
        
        <div class="about-grid fade-in-scale delay-1">
            <div class="about-card glass">
                <h3>Our Mission</h3>
                <p>To deliver uncompromising quality and aesthetic perfection in every device we create, pushing the boundaries of what is possible.</p>
            </div>
            <div class="about-card glass">
                <h3>Our Vision</h3>
                <p>A future where technology is invisible, yet omnipresent—enhancing human capability without introducing friction.</p>
            </div>
        </div>
    </main>

    <!-- Footer Section -->
    <footer class="site-footer">
        <div class="footer-grid">
            <div class="footer-brand">
                <h3><span class="gradient-text">Aurora</span>Tech</h3>
                <p>Elevating everyday life through unparalleled design and technology.</p>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2026 AuroraTech Inc. All rights reserved.</p>
            </div>
        </div>
    </footer>
</body>
</html>
