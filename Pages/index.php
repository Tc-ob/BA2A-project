<?php
include '../Database/connection.php';
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NexGen Commerce</title>
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
            <li><a href="index.php" style="color: var(--text-main);">Home</a></li>
            <li><a href="products.php">Products</a></li>
            <li><a href="categories.php">Categories</a></li>
            <li><a href="about.php">About</a></li>
        </ul>

        <!-- Auth Section -->
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
        <!-- Landing Page View -->
        <div class="landing-view">
            <section class="hero-section">
                <div class="hero-content">
                    <h1 class="fade-in-up">The Future of <span class="gradient-text">Design.</span></h1>
                    <p class="fade-in-up delay-1">Experience technology that blends seamlessly with your life. Minimalist, powerful, and impeccably crafted.</p>
                    <div class="cta-group fade-in-up delay-2">
                        <a href="products.php" class="btn btn-primary btn-large">Shop Collection</a>
                        <a href="about.php" class="btn btn-outline btn-large">Learn More</a>
                    </div>
                </div>
                <div class="hero-visual fade-in-scale delay-3">
                    <div class="abstract-shape shape-1"></div>
                    <div class="abstract-shape shape-2"></div>
                    <div class="product-mockup glass">
                        <div class="mockup-inner">Flagship Model</div>
                    </div>
                </div>
            </section>

            <!-- Products Section -->
            <section id="products" class="products-section">
                <div class="section-header">
                    <h2>Featured Products</h2>
                    <p>Discover our most popular devices, crafted for perfection.</p>
                </div>
                
                <div class="product-grid">
                    <?php
                    $feat_query = "SELECT * FROM PRODUCTS ORDER BY id ASC LIMIT 3";
                    $feat_res = mysqli_query($conn, $feat_query);
                    if ($feat_res && mysqli_num_rows($feat_res) > 0) {
                        while ($prod = mysqli_fetch_assoc($feat_res)) {
                            $prod_id = $prod['id'];
                            $name = htmlspecialchars($prod['name']);
                            $price = number_format($prod['price'], 0, '.', ',');
                            $image = $prod['image_path'];
                            
                            $placeholder_class = 'placeholder-1';
                            if ($prod['category_id'] == 1) $placeholder_class = 'placeholder-3';
                            if ($prod['category_id'] == 3) $placeholder_class = 'placeholder-2';
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
                                    <form class="add-cart-form" action="../Controller/cartcontroller.php" method="POST" style="margin-top:0.5rem;">
                                        <input type="hidden" name="action" value="add">
                                        <input type="hidden" name="product_id" value="<?php echo $prod_id; ?>">
                                        <button type="submit" class="btn btn-primary w-100" style="width:100%;">
                                            <?php echo isset($_SESSION['id']) ? 'Add to Cart' : 'Login to Add'; ?>
                                        </button>
                                    </form>
                                </div>
                            </div>
                            <?php
                        }
                    } else {
                        echo "<p>No featured products available.</p>";
                    }
                    ?>
                </div>
            </section>

            <!-- Revitalized Client Experience -->
            <section class="testimonials-section">
                <div class="section-header">
                    <h2>Professional <span class="gradient-text">Perspectives</span></h2>
                    <p>Strategic insights from global leaders who trust AuroraTech for enterprise-grade excellence.</p>
                </div>

                <div class="testimonial-grid">
                    <!-- Executive 1 -->
                    <div class="testimonial-card glass executive fade-in-up">
                        <div class="executive-badge">Verified CTO</div>
                        <p class="testimonial-quote">"AuroraTech has fundamentally redefined our expectations for infrastructure reliability. Their high-performance systems are essential to our global operations."</p>
                        <div class="client-info">
                            <div class="client-avatar">
                                <img src="https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?auto=format&fit=crop&q=80&w=200&h=200" alt="Aria Vance">
                            </div>
                            <div class="client-details">
                                <h4>Aria Vance</h4>
                                <p>Global CTO @ Nexus Dynamics</p>
                            </div>
                        </div>
                    </div>

                    <!-- Executive 2 -->
                    <div class="testimonial-card glass executive fade-in-up delay-1">
                        <div class="executive-badge">Verified Director</div>
                        <p class="testimonial-quote">"In high-stakes financial logistics, there is no room for error. AuroraTech delivers the precision and rapid consistency that our elite firm requires."</p>
                        <div class="client-info">
                            <div class="client-avatar">
                                <img src="https://images.unsplash.com/photo-1560250097-0b93528c311a?auto=format&fit=crop&q=80&w=200&h=200" alt="Julian Thorne">
                            </div>
                            <div class="client-details">
                                <h4>Julian Thorne</h4>
                                <p>Managing Director @ Blackwood</p>
                            </div>
                        </div>
                    </div>

                    <!-- Executive 3 -->
                    <div class="testimonial-card glass executive fade-in-up delay-2">
                        <div class="executive-badge">Verified CDO</div>
                        <p class="testimonial-quote">"The intersection of aesthetic mastery and engineering excellence is AuroraTech's hallmark. Their attention to detail aligns perfectly with our vision."</p>
                        <div class="client-info">
                            <div class="client-avatar">
                                <img src="https://images.unsplash.com/photo-1580489944761-15a19d654956?auto=format&fit=crop&q=80&w=200&h=200" alt="Seraphina Moon">
                            </div>
                            <div class="client-details">
                                <h4>Seraphina Moon</h4>
                                <p>Chief Design Officer @ Ethereal</p>
                            </div>
                        </div>
                    </div>

                    <!-- Executive 4 -->
                    <div class="testimonial-card glass executive fade-in-up delay-3">
                        <div class="executive-badge">Verified CEO</div>
                        <p class="testimonial-quote">"Beyond the products, it is the enterprise-grade service that stands out. Strategic innovation is at the heart of everything they do."</p>
                        <div class="client-info">
                            <div class="client-avatar">
                                <img src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?auto=format&fit=crop&q=80&w=200&h=200" alt="Marcus Sterling">
                            </div>
                            <div class="client-details">
                                <h4>Marcus Sterling</h4>
                                <p>CEO @ Sterling & Co.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <footer class="site-footer">
                <div class="footer-grid">
                    <div class="footer-brand">
                        <h3><span class="gradient-text">Aurora</span>Tech</h3>
                        <p>Elevating everyday life through unparalleled design and technology.</p>
                    </div>
                    <div class="footer-links">
                        <h4>Shop</h4>
                        <ul>
                            <li><a href="products.php?category=4">Phones</a></li>
                            <li><a href="products.php?category=1">Computers & Tablets</a></li>
                            <li><a href="products.php?category=2">Audio</a></li>
                            <li><a href="products.php?category=3">Wearables</a></li>
                            <li><a href="products.php?category=5">Chargers & Accessories</a></li>
                        </ul>
                    </div>
                    <div class="footer-links">
                        <h4>Company</h4>
                        <ul>
                            <li><a href="about.php">About Us</a></li>
                            <li><a href="#">Careers</a></li>
                            <li><a href="#">Press</a></li>
                            <li><a href="#">Contact</a></li>
                        </ul>
                    </div>
                    <div class="footer-newsletter">
                        <h4>Newsletter</h4>
                        <p>Subscribe for updates and exclusive offers.</p>
                        <form id="newsletter-form" name="newsletter-form" class="newsletter-form">
                            <input type="email" id="newsletter-email" name="newsletter-email" placeholder="Your email address" required>
                            <button type="submit" id="newsletter-submit" name="newsletter-submit" class="btn btn-primary">Subscribe</button>
                        </form>
                    </div>
                </div>
                <div class="footer-bottom">
                    <p>&copy; 2026 AuroraTech Inc. All rights reserved.</p>
                    <div class="legal-links">
                        <a href="#">Privacy Policy</a>
                        <a href="#">Terms of Service</a>
                    </div>
                </div>
            </footer>
        </div>
    </main>
</body>
</html>
