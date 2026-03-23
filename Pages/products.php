<?php
session_start();
include '../Database/connection.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products - AuroraTech</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&display=swap" rel="stylesheet">
    <style>
        :root {
            <?php if (isset($_SESSION['profile_color'])): ?>
            --profile-accent: <?php echo $_SESSION['profile_color']; ?>;
            <?php endif; ?>
        }
        /* Cart success toast */
        .toast {
            position: fixed;
            bottom: 2rem;
            right: 2rem;
            background: linear-gradient(135deg, #10b981, #059669);
            color: #fff;
            padding: 1rem 1.5rem;
            border-radius: 14px;
            font-weight: 600;
            font-size: 0.95rem;
            z-index: 999;
            box-shadow: 0 10px 30px rgba(16,185,129,0.3);
            animation: slideInRight 0.4s ease, fadeOut 0.4s ease 2.5s forwards;
        }

        @keyframes slideInRight {
            from { opacity: 0; transform: translateX(50px); }
            to   { opacity: 1; transform: translateX(0); }
        }
        @keyframes fadeOut {
            from { opacity: 1; }
            to   { opacity: 0; pointer-events: none; }
        }

        /* Cart badge in navbar */
        .cart-link {
            display: flex;
            align-items: center;
            gap: 0.4rem;
            font-size: 0.95rem;
            font-weight: 600;
            color: var(--text-muted);
            padding: 0.5rem 1rem;
            border-radius: 50px;
            border: 1px solid var(--surface-border);
            transition: all 0.25s;
        }
        .cart-link:hover { background: rgba(255,255,255,0.06); color: var(--text-main); }
        .cart-badge {
            background: var(--primary);
            color: #fff;
            font-size: 0.72rem;
            font-weight: 800;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Add to Cart button feedback */
        .add-cart-form { margin-top: 0.5rem; }
        .btn-cart {
            background: linear-gradient(135deg, var(--primary), #e11d48);
            color: #fff;
            border: none;
            padding: 0.85rem;
            border-radius: 50px;
            width: 100%;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.25s;
            box-shadow: 0 4px 15px rgba(244,63,94,0.3);
        }
        .btn-cart:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(244,63,94,0.45);
            filter: brightness(1.1);
        }
        .btn-cart:active { transform: scale(0.97); }
    </style>
</head>
<body>

    <?php
    // Count cart items if logged in
    $cart_count = 0;
    if (isset($_SESSION['id'])) {
        $cc = $conn->prepare("SELECT SUM(quantity) as total FROM CART WHERE user_id = ?");
        $cc->bind_param("i", $_SESSION['id']);
        $cc->execute();
        $cart_count = $cc->get_result()->fetch_assoc()['total'] ?? 0;
    }
    ?>

    <!-- Navbar -->
    <nav class="navbar glass">
        <div class="logo">
            <a href="index.php"><span class="gradient-text">Aurora</span>Tech</a>
        </div>
        
        <ul class="nav-links">
            <li><a href="index.php">Home</a></li>
            <li><a href="products.php" style="color: var(--text-main);">Products</a></li>
            <li><a href="categories.php">Categories</a></li>
            <li><a href="about.php">About</a></li>
        </ul>

        <!-- Auth Section -->
        <div class="auth-buttons" style="display:flex; align-items:center; margin-left: 1rem;">
            <!-- Theme Switcher -->
            <div class="theme-switcher">
                <div class="theme-btn t-dark" id="btn-dark" onclick="setTheme('dark')" title="Dark Mode"></div>
                <div class="theme-btn t-light" id="btn-light" onclick="setTheme('light')" title="Light Mode"></div>
                <div class="theme-btn t-sky" id="btn-sky" onclick="setTheme('sky')" title="Sky Blue Mode"></div>
            </div>
            
            <?php if (isset($_SESSION['id'])): 
                $user_initial = strtoupper(substr($_SESSION['name'], 0, 1));
            ?>
                <a href="dashboard.php" class="cart-link" style="margin-left: 1rem;">
                    🛒 Cart
                    <?php if ($cart_count > 0): ?>
                        <span class="cart-badge"><?php echo $cart_count; ?></span>
                    <?php endif; ?>
                </a>
                <span style="font-size:.9rem; color:var(--text-muted); margin-left: 1rem;">
                    <?php echo htmlspecialchars($_SESSION['name']); ?>
                </span>
                <a href="dashboard.php" style="text-decoration:none; margin-left: 0.5rem;">
                    <div class="avatar-circle"><?php echo $user_initial; ?></div>
                </a>
                <a href="../Controller/logout.php" class="btn btn-logout" style="margin-left:1rem;">Log Out</a>
            <?php else: ?>
                <a href="login.php" class="btn btn-outline">Log In</a>
                <a href="signup.php" class="btn btn-primary">Sign Up</a>
            <?php endif; ?>
        </div>
        
        <div class="hamburger"><span></span><span></span><span></span></div>
    </nav>
    <script src="theme-switcher.js"></script>

    <!-- Success Toast -->
    <?php if (isset($_SESSION['cart_success'])): ?>
        <div class="toast">🛒 <?php echo htmlspecialchars($_SESSION['cart_success']); unset($_SESSION['cart_success']); ?></div>
    <?php endif; ?>

    <!-- Main Content -->
    <main class="page-padding">
        <div class="section-header fade-in-up">
            <h2>All Products</h2>
            <p>Our complete collection of premium devices.</p>
        </div>
        
        <div class="product-grid fade-in-up delay-1">
            <?php
            // Fetch products with optional category filter
            $where_clause = "";
            $params = [];
            $types = "";
            
            if (isset($_GET['category']) && is_numeric($_GET['category'])) {
                $where_clause = " WHERE category_id = ?";
                $params[] = $_GET['category'];
                $types .= "i";
            }
            
            $products_query = "SELECT * FROM PRODUCTS" . $where_clause . " ORDER BY id ASC";
            $stmt = $conn->prepare($products_query);
            if (!empty($params)) {
                $stmt->bind_param($types, ...$params);
            }
            $stmt->execute();
            $products_res = $stmt->get_result();
            
            if ($products_res && mysqli_num_rows($products_res) > 0) {
                while ($prod = mysqli_fetch_assoc($products_res)) {
                    $prod_id = $prod['id'];
                    $name = htmlspecialchars($prod['name']);
                    $price = number_format($prod['price'], 0, '.', ',');
                    $image = $prod['image_path'];
                    $cat_id = $prod['category_id'] ?? 0;
                    
                    // Fallback to stylized placeholder if no image
                    $placeholder_class = 'placeholder-1'; // Default
                    if ($cat_id == 1) $placeholder_class = 'placeholder-3'; // Computers/Tablets
                    if ($cat_id == 3) $placeholder_class = 'placeholder-2'; // Wearables
                    ?>
                    <div class="product-card glass">
                        <div class="product-img-wrapper">
                            <?php if (!empty($image)): ?>
                                <img src="../Images/<?php echo htmlspecialchars($image); ?>" alt="<?php echo $name; ?>" style="width:100%; height:100%; object-fit:cover; border-radius:15px; filter: contrast(1.05) brightness(0.95);">
                            <?php else: ?>
                                <div class="product-img <?php echo $placeholder_class; ?>"><?php echo strtoupper(substr($name, 0, 10)); ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="product-info">
                            <h3><?php echo $name; ?></h3>
                            <p class="price"><?php echo $price; ?> FCFA</p>
                            <form class="add-cart-form" action="../Controller/cartcontroller.php" method="POST">
                                <input type="hidden" name="action" value="add">
                                <input type="hidden" name="product_id" value="<?php echo $prod_id; ?>">
                                <button type="submit" class="btn-cart">
                                    <?php echo isset($_SESSION['id']) ? '🛒 Add to Cart' : '🔒 Login to Add'; ?>
                                </button>
                            </form>
                        </div>
                    </div>
                    <?php
                }
            } else {
                echo "<p style='grid-column: 1 / -1; text-align: center; color: var(--text-muted); padding: 3rem;'>No products available yet.</p>";
            }
            ?>
        </div>
    </main>

    <!-- Footer -->
    <footer class="site-footer">
        <div class="footer-grid">
            <div class="footer-brand">
                <h3><span class="gradient-text">Aurora</span>Tech</h3>
                <p>Elevating everyday life through unparalleled design and technology.</p>
            </div>
            <div class="footer-links">
                <h4>Shop</h4>
                <ul>
                    <li><a href="products.php">Audio</a></li>
                    <li><a href="products.php">Wearables</a></li>
                    <li><a href="products.php">Computers</a></li>
                </ul>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2026 AuroraTech Inc. All rights reserved.</p>
            </div>
        </div>
    </footer>
</body>
</html>
