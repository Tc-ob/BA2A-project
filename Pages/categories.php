<?php
session_start();
include '../Database/connection.php';
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Categories - AuroraTech</title>
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
            <li><a href="categories.php" style="color: var(--text-main);">Categories</a></li>
            <li><a href="about.php">About</a></li>
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
    <main class="page-padding">
        <div class="section-header fade-in-up">
            <h2>Categories</h2>
            <p>Browse our curated collections of premium tech.</p>
        </div>
        
        <div class="category-grid fade-in-up delay-1">
            <?php
            $cat_query = "SELECT * FROM CATEGORIES ORDER BY id ASC";
            $cats = mysqli_query($conn, $cat_query);
            $bg_idx = 1;
            
            if ($cats && mysqli_num_rows($cats) > 0) {
                while ($c = mysqli_fetch_assoc($cats)) {
                    // Cycle through default placeholder backgrounds
                    $bg_class = 'cat-bg-' . (($bg_idx % 3) == 0 ? 3 : ($bg_idx % 3));
                    ?>
                    <a href="products.php?category=<?php echo $c['id']; ?>" class="category-card glass <?php echo $bg_class; ?>">
                        <div class="cat-content">
                            <h3><?php echo htmlspecialchars($c['name']); ?></h3>
                            <p><?php echo htmlspecialchars($c['description']); ?></p>
                        </div>
                    </a>
                    <?php
                    $bg_idx++;
                }
            } else {
                echo "<p style='color:var(--text-muted);'>No categories available.</p>";
            }
            ?>
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
