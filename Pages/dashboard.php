<?php
session_start();
include '../Database/connection.php';

// Handle login form submission
if (isset($_POST["submit"])) {
    $email = $_POST["email"];
    $password = $_POST["password"];

    if (empty($email) || empty($password)) {
        $_SESSION['error'] = "Email and password are required.";
        header("Location: login.php");
        exit();
    }

    $stmt = $conn->prepare("SELECT id, name, password, role FROM USERS WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['id'] = $user['id'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['role'] = $user['role'];
        }
        else {
            $_SESSION['error'] = "Wrong credentials entered.";
            header("Location: login.php");
            exit();
        }
    }
    else {
        $_SESSION['error'] = "No account found with that email.";
        header("Location: login.php");
        exit();
    }
}

// Ensure user is authenticated to view this page
if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

$user_name = htmlspecialchars($_SESSION['name']);
$user_role = strtolower($_SESSION['role']);
$user_initial = strtoupper(substr($user_name, 0, 1));
$is_admin = ($user_role === 'admin');

// If admin, fetch categories and stats
$categories = [];
$admin_stats = ['users' => 0, 'orders' => 0, 'revenue' => 0, 'pending' => 0];

if ($is_admin) {
    $cat_res = mysqli_query($conn, "SELECT id, name FROM CATEGORIES ORDER BY name ASC");
    if ($cat_res) {
        $categories = mysqli_fetch_all($cat_res, MYSQLI_ASSOC);
    }
    
    // Stats queries
    $stat_res = mysqli_query($conn, "SELECT COUNT(id) as total FROM USERS");
    if ($stat_res && $row = mysqli_fetch_assoc($stat_res)) {
        $admin_stats['users'] = $row['total'];
    }

    $stat_res = mysqli_query($conn, "SELECT COUNT(id) as total, SUM(total_price) as revenue FROM PAYMENT");
    if ($stat_res && $row = mysqli_fetch_assoc($stat_res)) {
        $admin_stats['orders'] = $row['total'];
        $admin_stats['revenue'] = $row['revenue'] ?? 0;
    }

    $stat_res = mysqli_query($conn, "SELECT COUNT(id) as total FROM PAYMENT WHERE status='Pending'");
    if ($stat_res && $row = mysqli_fetch_assoc($stat_res)) {
        $admin_stats['pending'] = $row['total'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $is_admin ? 'Admin Panel' : 'My Dashboard'; ?> - AuroraTech</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&display=swap" rel="stylesheet">
    <style>
        :root {
            <?php if (isset($_SESSION['profile_color'])): ?>
            --profile-accent: <?php echo $_SESSION['profile_color']; ?>;
            <?php endif; ?>
        }
        /* ======= ROLE DASHBOARD STYLES ======= */
        .dashboard-body { background: var(--bg-main); }

        /* Sidebar layout */
        .dash-layout {
            display: grid;
            grid-template-columns: 260px 1fr;
            min-height: 100vh;
            padding-top: 80px;
        }

        /* --- SIDEBAR --- */
        .dash-sidebar {
            background: #0d0d10;
            border-right: 1px solid var(--surface-border);
            padding: 2.5rem 1.5rem;
            position: sticky;
            top: 80px;
            height: calc(100vh - 80px);
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .sidebar-label {
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: var(--text-muted);
            padding: 1.5rem 1rem 0.5rem;
            margin-top: 0.5rem;
        }

        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 0.85rem;
            padding: 0.9rem 1rem;
            border-radius: 12px;
            font-size: 0.95rem;
            font-weight: 500;
            color: var(--text-muted);
            transition: all var(--transition-fast);
            cursor: pointer;
            border: none;
            background: transparent;
            text-decoration: none;
            width: 100%;
        }

        .sidebar-link:hover {
            background: rgba(255,255,255,0.05);
            color: var(--text-main);
        }

        .sidebar-link.active {
            background: rgba(244, 63, 94, 0.12);
            color: var(--primary);
            border: 1px solid rgba(244, 63, 94, 0.25);
        }

        .sidebar-icon { font-size: 1.2rem; width: 24px; text-align: center; }

        .sidebar-bottom {
            margin-top: auto;
            padding-top: 1.5rem;
            border-top: 1px solid var(--surface-border);
        }

        /* --- MAIN CONTENT --- */
        .dash-main {
            padding: 3rem 4%;
            overflow-y: auto;
        }

        .dash-welcome {
            margin-bottom: 3rem;
    /* animation: slideInDown 1.2s ease; */
        }

        .dash-welcome h1 {
            font-size: 2.2rem;
            margin-bottom: 0.5rem;
        }

        .dash-welcome h1 span { color: var(--primary); }

        .dash-welcome p { color: var(--text-muted); font-size: 1.05rem; }

        .role-badge {
            display: inline-block;
            padding: 0.3rem 1rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-left: 0.75rem;
            vertical-align: middle;
        }

        .role-badge.client { background: rgba(245, 158, 11, 0.15); color: var(--accent); border: 1px solid rgba(245,158,11,0.3); }
        .role-badge.admin  { background: rgba(244, 63, 94, 0.15); color: var(--primary); border: 1px solid rgba(244,63,94,0.3); }

        /* Stat Cards Row */
        .stat-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2.5rem;
        }

        .stat-card {
            padding: 1.75rem;
            border-radius: 20px;
            animation: fadeInUp 0.6s backwards;
        }

        .stat-card:nth-child(2) { animation-delay: 0.1s; }
        .stat-card:nth-child(3) { animation-delay: 0.2s; }
        .stat-card:nth-child(4) { animation-delay: 0.3s; }

        .stat-label {
            font-size: 0.85rem;
            color: var(--text-muted);
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 0.75rem;
        }

        .stat-value {
            font-size: 2.4rem;
            font-weight: 800;
            line-height: 1;
            margin-bottom: 0.5rem;
        }

        /* Profile Customization Modal */
        .profile-modal {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) scale(0.9);
            width: 90%;
            max-width: 450px;
            z-index: 1000;
            padding: 2.5rem;
            animation: modalFadeIn 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        .profile-modal.active {
            display: block;
        }

        .modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            backdrop-filter: blur(8px);
            z-index: 999;
            animation: fadeIn 0.3s ease;
        }

        .modal-overlay.active {
            display: block;
        }

        .modal-close {
            position: absolute;
            top: 1.5rem;
            right: 1.5rem;
            background: rgba(255,255,255,0.07);
            border: 1px solid var(--surface-border);
            color: var(--text-muted);
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s;
        }

        .modal-close:hover {
            background: rgba(244,63,94,0.15);
            color: #f43f5e;
            transform: rotate(90deg);
        }

        @keyframes modalFadeIn {
            from { opacity: 0; transform: translate(-50%, -50%) scale(0.9); }
            to { opacity: 1; transform: translate(-50%, -50%) scale(1); }
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        /* Profile Color Swatches */
        .color-selector {
            display: flex;
            gap: 1rem;
            margin-top: 1rem;
        }

        .color-swatch {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            cursor: pointer;
            border: 2px solid rgba(255,255,255,0.1);
            transition: all 0.3s ease;
            position: relative;
        }

        .color-swatch:hover {
            transform: scale(1.2);
            border-color: #fff;
        }

        .color-swatch.active {
            border-color: #fff;
            box-shadow: 0 0 15px currentColor;
        }

        .color-swatch.active::after {
            content: '✓';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: #fff;
            font-size: 0.8rem;
            font-weight: 800;
        }

        .stat-change {
            font-size: 0.85rem;
            color: #10b981;
        }

        .stat-change.down { color: #f43f5e; }

        /* Content Grid */
        .content-grid {
            display: grid;
            grid-template-columns: 1.5fr 1fr;
            gap: 2rem;
            margin-bottom: 2rem;
        }

        @media (max-width: 1100px) {
            .content-grid { grid-template-columns: 1fr; }
        }

        .panel-card {
            padding: 2rem;
            border-radius: 20px;
            animation: fadeInUp 0.6s ease backwards;
        }

        .panel-card h3 {
            font-size: 1.2rem;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--surface-border);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .panel-card h3 .icon { font-size: 1.1rem; }

        /* Table Styles */
        .data-table { width: 100%; border-collapse: collapse; }
        .data-table th {
            text-align: left;
            padding: 0.75rem 1rem;
            font-size: 0.78rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--text-muted);
            font-weight: 600;
            border-bottom: 1px solid var(--surface-border);
        }

        .data-table td {
            padding: 1rem;
            font-size: 0.95rem;
            border-bottom: 1px solid rgba(255,255,255,0.04);
            color: var(--text-main);
        }

        .data-table tr:last-child td { border-bottom: none; }

        .data-table tr:hover td { background: rgba(255,255,255,0.03); }

        /* Status Pills */
        .pill {
            padding: 0.3rem 0.8rem;
            border-radius: 20px;
            font-size: 0.78rem;
            font-weight: 700;
        }

        .pill-success { background: rgba(16,185,129,0.15); color: #10b981; }
        .pill-warning { background: rgba(245,158,11,0.15); color: #f59e0b; }
        .pill-danger  { background: rgba(244,63,94,0.15);  color: #f43f5e; }
        .pill-info    { background: rgba(99,102,241,0.15); color: #818cf8; }

        /* Quick Action List */
        .quick-list { display: flex; flex-direction: column; gap: 0.75rem; }

        .quick-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem 1.25rem;
            border-radius: 12px;
            background: rgba(255,255,255,0.03);
            border: 1px solid var(--surface-border);
            cursor: pointer;
            transition: all var(--transition-fast);
            font-size: 0.95rem;
            color: var(--text-main);
            text-decoration: none;
        }

        .quick-item:hover {
            background: rgba(255,255,255,0.07);
            border-color: var(--primary);
            transform: translateX(4px);
        }

        .quick-item-icon {
            font-size: 1.2rem;
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background: rgba(244,63,94,0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        /* Admin specific - user count highlight */
        .admin-stat .stat-value { color: var(--primary); }

        /* Progress bar */
        .progress-bar-wrap {
            background: rgba(255,255,255,0.06);
            border-radius: 99px;
            height: 6px;
            margin-top: 0.5rem;
            overflow: hidden;
        }

        .progress-bar-fill {
            height: 100%;
            border-radius: 99px;
            background: linear-gradient(90deg, var(--primary), var(--accent));
        }

        @media (max-width: 900px) {
            .dash-layout { grid-template-columns: 1fr; }
            .dash-sidebar { display: none; }
        }
        body.theme-sky .stat-card { background: rgba(210,238,252,0.9); }
        body.theme-sky .data-table td, body.theme-sky .data-table th { color: #0a2030; }
        body.theme-sky .data-table tr:hover td { background: rgba(0,80,160,0.05); }
        body.theme-sky .quick-item { background: rgba(185,228,248,0.6); border-color: rgba(100,160,200,0.4); color: #0a2030; }
        body.theme-sky .sidebar-label { color: #3d6070; }
    </style>
</head>
<body class="dashboard-body">

    <!-- Navbar -->
    <nav class="navbar glass">
        <div class="logo">
            <a href="index.php"><span class="gradient-text">Aurora</span>Tech</a>
        </div>

        <ul class="nav-links">
            <li><a href="index.php">Home</a></li>
            <li><a href="products.php">Products</a></li>
            <li><a href="categories.php">Categories</a></li>
            <li><a href="about.php">About</a></li>
        </ul>

        <div class="user-profile" style="display:flex; align-items:center; gap:1.2rem;">
            <!-- Theme Switcher -->
            <div class="theme-switcher" title="Switch theme">
                <div class="theme-btn t-dark active" id="btn-dark" onclick="setTheme('dark')" title="Dark"></div>
                <div class="theme-btn t-light" id="btn-light" onclick="setTheme('light')" title="Light"></div>
                <div class="theme-btn t-sky" id="btn-sky" onclick="setTheme('sky')" title="Sky Blue"></div>
            </div>
            <span style="font-size:.9rem; color:var(--text-muted);">
                <?php echo $user_name; ?>
                <span class="role-badge <?php echo $user_role; ?>"><?php echo ucfirst($user_role); ?></span>
            </span>
            <div class="avatar-circle" onclick="toggleProfileModal()" style="cursor:pointer;"><?php echo $user_initial; ?></div>
            <a href="../Controller/logout.php" class="btn btn-logout">Log Out</a>
        </div>

        <div class="hamburger"><span></span><span></span><span></span></div>
    </nav>

    <div class="dash-layout">

        <!-- ===== SIDEBAR ===== -->
        <aside class="dash-sidebar">
            <?php if ($is_admin): ?>
                <span class="sidebar-label">Admin Panel</span>
                <a href="dashboard.php" class="sidebar-link active"><span class="sidebar-icon">📊</span> Overview</a>
                <a href="users.php" class="sidebar-link"><span class="sidebar-icon">👥</span> Users</a>
                <a href="categories.php" class="sidebar-link"><span class="sidebar-icon">🏷️</span> Categories</a>
                <a href="products.php" class="sidebar-link"><span class="sidebar-icon">📦</span> Products</a>
                <a href="orders.php" class="sidebar-link"><span class="sidebar-icon">🛒</span> Orders</a>
                <span class="sidebar-label">Settings</span>
                <a href="#" class="sidebar-link"><span class="sidebar-icon">⚙️</span> Site Settings</a>
                <a href="#" class="sidebar-link"><span class="sidebar-icon">🔔</span> Notifications</a>
            <?php
else: ?>
                <span class="sidebar-label">My Account</span>
                <a href="#" class="sidebar-link active"><span class="sidebar-icon">🏠</span> Dashboard</a>
                <a href="#" class="sidebar-link"><span class="sidebar-icon">🛍️</span> My Orders</a>
                <a href="#" class="sidebar-link"><span class="sidebar-icon">❤️</span> Wishlist</a>
                <a href="javascript:void(0)" onclick="toggleProfileModal()" class="sidebar-link"><span class="sidebar-icon">👤</span> Profile Settings</a>
                <span class="sidebar-label">Support</span>
                <a href="#" class="sidebar-link"><span class="sidebar-icon">💬</span> Help Center</a>
                <a href="#" class="sidebar-link"><span class="sidebar-icon">📋</span> Returns</a>
            <?php
endif; ?>

            <div class="sidebar-bottom">
                <a href="../Controller/logout.php" class="sidebar-link"><span class="sidebar-icon">🚪</span> Log Out</a>
            </div>
        </aside>

        <!-- ===== MAIN CONTENT ===== -->
        <main class="dash-main">

            <!-- Welcome Header -->
            <div class="dash-welcome fade-in-up">
                <?php if ($is_admin): ?>
                    <h1>Admin Panel <span>Overview</span></h1>
                    <p>Here's what's happening across your store today.</p>
                <?php
else: ?>
                    <h1>Welcome back, <span><?php echo $user_name; ?></span>!</h1>
                    <p>Track your orders, manage your wishlist, and explore the latest drops.</p>
                <?php
endif; ?>
            </div>

            <!-- Payment Notifications -->
            <?php if (isset($_SESSION['payment_success'])): ?>
                <div class="alert alert-success fade-in-up" style="background: rgba(16, 185, 129, 0.1); color: #10b981; padding: 1rem; border-radius: 12px; border: 1px solid rgba(16, 185, 129, 0.3); margin-bottom: 2rem;">
                    ✅ <?php echo $_SESSION['payment_success']; unset($_SESSION['payment_success']); ?>
                </div>
            <?php endif; ?>
            <?php if (isset($_SESSION['payment_error'])): ?>
                <div class="alert alert-danger fade-in-up" style="background: rgba(244, 63, 94, 0.1); color: #f43f5e; padding: 1rem; border-radius: 12px; border: 1px solid rgba(244, 63, 94, 0.3); margin-bottom: 2rem;">
                    ❌ <?php echo $_SESSION['payment_error']; unset($_SESSION['payment_error']); ?>
                </div>
            <?php endif; ?>

            <?php if ($is_admin): ?>
            <!-- ============================================ -->
            <!--             ADMIN DASHBOARD                 -->
            <!-- ============================================ -->

            <!-- Stat Cards -->
            <div class="stat-cards">
                <div class="stat-card glass admin-stat">
                    <div class="stat-label">Total Users</div>
                    <div class="stat-value"><?php echo number_format($admin_stats['users']); ?></div>
                    <div class="stat-change">↑ Active this month</div>
                </div>
                <div class="stat-card glass">
                    <div class="stat-label">Total Orders</div>
                    <div class="stat-value"><?php echo number_format($admin_stats['orders']); ?></div>
                    <div class="stat-change">↑ Recent check</div>
                </div>
                <div class="stat-card glass">
                    <div class="stat-label">Revenue</div>
                    <div class="stat-value"><?php echo number_format($admin_stats['revenue'], 0, '.', ','); ?> FCFA</div>
                    <div class="stat-change">↑ Lifetime value</div>
                </div>
                <div class="stat-card glass">
                    <div class="stat-label">Pending Orders</div>
                    <div class="stat-value"><?php echo number_format($admin_stats['pending']); ?></div>
                    <div class="stat-change down">Needs attention</div>
                </div>
            </div>

            <!-- Orders + Quick Actions -->
            <div class="content-grid">
                <div class="panel-card glass">
                    <h3><span class="icon">🛒</span> Recent Orders</h3>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Customer</th>
                                <th>Product</th>
                                <th>Total</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $recent_query = "SELECT p.id as order_id, u.name as customer_name, pr.name as product_name, p.total_price, p.status 
                                             FROM PAYMENT p 
                                             JOIN USERS u ON p.user_id = u.id 
                                             JOIN PRODUCTS pr ON p.product_id = pr.id 
                                             ORDER BY p.payment_date DESC LIMIT 4";
                            $recent_res = mysqli_query($conn, $recent_query);
                            if ($recent_res && mysqli_num_rows($recent_res) > 0) {
                                while ($row = mysqli_fetch_assoc($recent_res)) {
                                    $status_class = 'pill-warning'; // Default mapping (Processing/Pending)
                                    if ($row['status'] == 'Delivered') $status_class = 'pill-success';
                                    if ($row['status'] == 'Shipped') $status_class = 'pill-info';
                                    if ($row['status'] == 'Cancelled') $status_class = 'pill-danger';
                                    
                                    echo "<tr>";
                                    echo "<td>#ORD-" . htmlspecialchars($row['order_id']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['customer_name']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['product_name']) . "</td>";
                                    echo "<td>" . number_format($row['total_price'], 0, '.', ',') . " FCFA</td>";
                                    echo "<td><span class=\"pill $status_class\">" . htmlspecialchars($row['status']) . "</span></td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='5' style='text-align:center; padding:1.5rem;'>No recent orders found.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>

                <div class="panel-card glass">
                    <h3><span class="icon">⚡</span> Quick Actions</h3>
                    <div class="quick-list">
                        <a href="javascript:void(0)" onclick="toggleProductModal()" class="quick-item">
                            <div class="quick-item-icon">➕</div>
                            <div><strong>Add Product</strong><br><small style="color:var(--text-muted)">List a new item in catalog</small></div>
                        </a>
                        <a href="categories.php" class="quick-item">
                            <div class="quick-item-icon">🏷️</div>
                            <div><strong>Manage Categories</strong><br><small style="color:var(--text-muted)">Organize product types</small></div>
                        </a>
                        <a href="users.php" class="quick-item">
                            <div class="quick-item-icon">👥</div>
                            <div><strong>Manage Users</strong><br><small style="color:var(--text-muted)">View and edit accounts</small></div>
                        </a>
                        <a href="orders.php" class="quick-item">
                            <div class="quick-item-icon">📊</div>
                            <div><strong>Manage Orders</strong><br><small style="color:var(--text-muted)">Update order statuses</small></div>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Top Products -->
            <div class="panel-card glass" style="animation-delay:0.3s">
                <h3><span class="icon">🔥</span> Top Selling Products</h3>
                <table class="data-table">
                    <thead>
                        <tr><th>Product</th><th>Category</th><th>Sales</th><th>Revenue</th><th>Inventory</th></tr>
                    </thead>
                    <tbody>
                        <?php
                        $top_prod_query = "SELECT p.name, 'N/A' as cat_name, p.quantity, p.price,
                                           (SELECT COUNT(*) FROM PAYMENT pay WHERE pay.product_id = p.id) as sales
                                           FROM PRODUCTS p
                                           ORDER BY sales DESC LIMIT 5";
                        $top_res = mysqli_query($conn, $top_prod_query);
                        if ($top_res && mysqli_num_rows($top_res) > 0) {
                            while ($row = mysqli_fetch_assoc($top_res)): 
                                $revenue = $row['sales'] * $row['price'];
                            ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['name']); ?></td>
                                <td><?php echo htmlspecialchars($row['cat_name'] ?? 'Uncategorized'); ?></td>
                                <td><?php echo $row['sales']; ?></td>
                                <td><?php echo number_format($revenue, 0, '.', ','); ?> FCFA</td>
                                <td>
                                    <?php if ($row['quantity'] > 10): ?>
                                        <span class="pill pill-success">In Stock (<?php echo $row['quantity']; ?>)</span>
                                    <?php elseif ($row['quantity'] > 0): ?>
                                        <span class="pill pill-warning">Low Stock (<?php echo $row['quantity']; ?>)</span>
                                    <?php else: ?>
                                        <span class="pill pill-danger">Out of Stock</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endwhile;
                        } else {
                            echo "<tr><td colspan='5' style='text-align:center; padding:2rem; color:var(--text-muted);'>No product data available.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>

            <!-- Add Product Modal -->
            <div class="modal-overlay" id="product-overlay" onclick="toggleProductModal()"></div>
            <div class="panel-card glass profile-modal" id="add-product-modal" style="max-width: 500px;">
                <div class="modal-close" onclick="toggleProductModal()">✕</div>
                <h3><span class="icon">📦</span> Add New Product</h3>
                <form action="../Controller/admincontroller.php" method="POST" style="margin-top: 1.5rem;">
                    <input type="hidden" name="add_product" value="1">
                    
                    <div class="input-group" style="margin-bottom: 1.2rem;">
                        <input type="text" name="name" required placeholder=" " style="background: rgba(255,255,255,0.05); border: 1px solid var(--surface-border); color: #fff; padding: 0.8rem; border-radius: 10px; width: 100%;">
                        <label style="position: absolute; top: 10px; left: 10px; font-size: 0.8rem; color: var(--text-muted); pointer-events: none; transition: all 0.2s;">Product Name</label>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.2rem;">
                        <div class="input-group" style="position: relative;">
                            <input type="number" name="price" step="0.01" required placeholder=" " style="background: rgba(255,255,255,0.05); border: 1px solid var(--surface-border); color: #fff; padding: 0.8rem; border-radius: 10px; width: 100%;">
                            <label style="position: absolute; top: 10px; left: 10px; font-size: 0.8rem; color: var(--text-muted); pointer-events: none; transition: all 0.2s;">Price (FCFA)</label>
                        </div>
                        <div class="input-group" style="position: relative;">
                            <input type="number" name="quantity" required placeholder=" " style="background: rgba(255,255,255,0.05); border: 1px solid var(--surface-border); color: #fff; padding: 0.8rem; border-radius: 10px; width: 100%;">
                            <label style="position: absolute; top: 10px; left: 10px; font-size: 0.8rem; color: var(--text-muted); pointer-events: none; transition: all 0.2s;">Initial Stock</label>
                        </div>
                    </div>

                    <div class="input-group" style="margin-bottom: 1.2rem;">
                        <label style="display: block; font-size: 0.8rem; color: var(--text-muted); margin-bottom: 0.5rem;">Category</label>
                        <select name="category_id" style="background: rgba(255,255,255,0.05); border: 1px solid var(--surface-border); color: #fff; padding: 0.8rem; border-radius: 10px; width: 100%; cursor: pointer;">
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="input-group" style="margin-bottom: 1.2rem;">
                        <textarea name="description" rows="3" placeholder="Product Description..." style="background: rgba(255,255,255,0.05); border: 1px solid var(--surface-border); color: #fff; padding: 0.8rem; border-radius: 10px; width: 100%; font-family: inherit; resize: none;"></textarea>
                    </div>

                    <div class="input-group" style="margin-bottom: 1.5rem;">
                        <input type="text" name="image_path" placeholder="Image Filename (e.g. product_watch.png)" style="background: rgba(255,255,255,0.05); border: 1px solid var(--surface-border); color: #fff; padding: 0.8rem; border-radius: 10px; width: 100%;">
                    </div>

                    <button type="submit" class="btn btn-primary" style="width: 100%; padding: 1rem; font-weight: 700;">✨ Add to Catalog</button>
                </form>
            </div>

            <?php
else: ?>
            <!-- ============================================ -->
            <!--             CLIENT DASHBOARD                -->
            <!-- ============================================ -->

            <?php
            // Calculate Client Stats
            $client_stats = ['orders' => 0, 'wishlist' => 0, 'points' => 0, 'spent' => 0];
            $uid = $_SESSION['id'];
            
            $ostmt = $conn->prepare("SELECT COUNT(id) as total, SUM(total_price) as spent FROM PAYMENT WHERE user_id = ?");
            $ostmt->bind_param("i", $uid);
            $ostmt->execute();
            $orow = $ostmt->get_result()->fetch_assoc();
            if ($orow) {
                $client_stats['orders'] = $orow['total'];
                $client_stats['spent'] = $orow['spent'] ?? 0;
            }
            
            // Loyalty points: 10 points per 1000 FCFA spent
            $client_stats['points'] = floor($client_stats['spent'] / 1000) * 10;
            ?>
            <!-- Stat Cards -->
            <div class="stat-cards">
                <div class="stat-card glass">
                    <div class="stat-label">Total Orders</div>
                    <div class="stat-value" style="color:var(--primary);"><?php echo $client_stats['orders']; ?></div>
                    <div class="stat-change">Active account</div>
                </div>
                <div class="stat-card glass">
                    <div class="stat-label">Wishlist Items</div>
                    <div class="stat-value">0</div>
                    <div class="stat-change">Feature coming soon</div>
                </div>
                <div class="stat-card glass">
                    <div class="stat-label">Loyalty Points</div>
                    <div class="stat-value" style="color:var(--accent);"><?php echo number_format($client_stats['points']); ?></div>
                    <div class="stat-change">Based on purchases</div>
                </div>
                <div class="stat-card glass">
                    <div class="stat-label">Total Spent</div>
                    <div class="stat-value"><?php echo number_format($client_stats['spent'], 0, '.', ','); ?> FCFA</div>
                    <div class="stat-change">Lifetime value</div>
                </div>
            </div>

            <!-- My Cart + Quick Actions -->
            <?php
            // Fetch real cart items from DB
            $cart_stmt = $conn->prepare("
                SELECT c.id as cart_id, c.quantity, c.product_id,
                       p.name as product_name, p.price
                FROM CART c
                JOIN PRODUCTS p ON c.product_id = p.id
                WHERE c.user_id = ?
                ORDER BY c.id DESC
            ");
            $cart_stmt->bind_param("i", $_SESSION['id']);
            $cart_stmt->execute();
            $cart_items = $cart_stmt->get_result();
            $cart_total = 0;
            $cart_rows  = $cart_items->fetch_all(MYSQLI_ASSOC);
            foreach ($cart_rows as $row) {
                $cart_total += $row['price'] * $row['quantity'];
            }
            ?>
            <div class="content-grid">
                <div class="panel-card glass">
                    <h3><span class="icon">🛒</span> My Cart
                        <a href="products.php" style="margin-left:auto; font-size:0.85rem; font-weight:500; color:var(--primary);">+ Add More</a>
                    </h3>
                    <?php if (count($cart_rows) === 0): ?>
                        <div style="text-align:center; padding:2rem 0; color:var(--text-muted);">
                            <div style="font-size:2.5rem; margin-bottom:1rem;">🛒</div>
                            <p>Your cart is empty.</p>
                            <a href="products.php" class="btn btn-primary" style="margin-top:1rem; display:inline-flex;">Browse Products</a>
                        </div>
                    <?php else: ?>
                        <table class="data-table">
                            <thead>
                                <tr><th>Product</th><th>Price</th><th>Qty</th><th>Subtotal</th><th></th></tr>
                            </thead>
                            <tbody>
                                <?php foreach ($cart_rows as $item): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                                    <td><?php echo number_format($item['price'], 0, '.', ','); ?> FCFA</td>
                                    <td>
                                        <form action="../Controller/cartcontroller.php" method="POST" style="display:flex; align-items:center; gap:0.4rem;">
                                            <input type="hidden" name="action" value="update">
                                            <input type="hidden" name="cart_id" value="<?php echo $item['cart_id']; ?>">
                                            <input type="number" name="quantity" value="<?php echo $item['quantity']; ?>" min="1" max="99"
                                                style="width:55px; background:rgba(255,255,255,0.07); border:1px solid var(--surface-border); color:var(--text-main); border-radius:8px; padding:0.3rem 0.5rem; font-family:inherit;" onchange="this.form.submit()">
                                        </form>
                                    </td>
                                    <td style="font-weight:700;"><?php echo number_format($item['price'] * $item['quantity'], 0, '.', ','); ?> FCFA</td>
                                    <td>
                                        <form action="../Controller/cartcontroller.php" method="POST">
                                            <input type="hidden" name="action" value="remove">
                                            <input type="hidden" name="cart_id" value="<?php echo $item['cart_id']; ?>">
                                            <button type="submit" style="background:rgba(244,63,94,0.12); border:1px solid rgba(244,63,94,0.3); color:#f43f5e; padding:0.3rem 0.7rem; border-radius:8px; cursor:pointer; font-size:0.85rem;" title="Remove">✕</button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        <div style="display:flex; justify-content:space-between; align-items:center; margin-top:1.5rem; padding-top:1.2rem; border-top:1px solid var(--surface-border);">
                            <span style="font-size:1.1rem; font-weight:700;">Total: <span style="color:var(--primary);"><?php echo number_format($cart_total, 0, '.', ','); ?> FCFA</span></span>
                            <a href="checkout.php" class="btn btn-primary" style="padding:0.75rem 2rem; text-decoration: none;">Checkout →</a>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Profile Customization Modal -->
                <div class="modal-overlay" id="profile-overlay" onclick="toggleProfileModal()"></div>
                <div class="panel-card glass profile-modal" id="profile-settings-modal">
                    <div class="modal-close" onclick="toggleProfileModal()">✕</div>
                    <h3><span class="icon">🎨</span> Profile Identity</h3>
                    <p style="color:var(--text-muted); font-size:0.9rem; margin-bottom:1.5rem;">Customize your signature profile accent color. This changes your identity across the entire site.</p>
                    <div class="color-selector" style="justify-content: center; background: rgba(0,0,0,0.2); padding: 1.5rem; border-radius: 15px; border: 1px solid var(--surface-border);">
                        <div class="color-swatch" style="background: linear-gradient(135deg, #f43f5e, #fb7185); color: #f43f5e;" onclick="updateProfileColor('linear-gradient(135deg, #f43f5e, #fb7185)', this)" title="Neon Rose"></div>
                        <div class="color-swatch" style="background: linear-gradient(135deg, #06b6d4, #22d3ee); color: #06b6d4;" onclick="updateProfileColor('linear-gradient(135deg, #06b6d4, #22d3ee)', this)" title="Electric Blue"></div>
                        <div class="color-swatch" style="background: linear-gradient(135deg, #f59e0b, #fbbf24); color: #f59e0b;" onclick="updateProfileColor('linear-gradient(135deg, #f59e0b, #fbbf24)', this)" title="Amber Gold"></div>
                        <div class="color-swatch" style="background: linear-gradient(135deg, #10b981, #34d399); color: #10b981;" onclick="updateProfileColor('linear-gradient(135deg, #10b981, #34d399)', this)" title="Emerald Green"></div>
                        <div class="color-swatch" style="background: linear-gradient(135deg, #8b5cf6, #a78bfa); color: #8b5cf6;" onclick="updateProfileColor('linear-gradient(135deg, #8b5cf6, #a78bfa)', this)" title="Royal Purple"></div>
                    </div>
                </div>

                <div class="panel-card glass">
                    <h3><span class="icon">⚡</span> Quick Actions</h3>
                    <div class="quick-list">
                        <a href="products.php" class="quick-item">
                            <div class="quick-item-icon">🛍️</div>
                            <div><strong>Browse Products</strong><br><small style="color:var(--text-muted)">Explore our catalog</small></div>
                        </a>
                        <a href="#" class="quick-item">
                            <div class="quick-item-icon">📍</div>
                            <div><strong>Track Order</strong><br><small style="color:var(--text-muted)">See live updates</small></div>
                        </a>
                        <a href="#" class="quick-item">
                            <div class="quick-item-icon">💳</div>
                            <div><strong>Payment Methods</strong><br><small style="color:var(--text-muted)">Manage cards</small></div>
                        </a>
                        <a href="#" class="quick-item">
                            <div class="quick-item-icon">👤</div>
                            <div><strong>Edit Profile</strong><br><small style="color:var(--text-muted)">Update your info</small></div>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Order History -->
            <div class="panel-card glass" style="animation-delay:0.3s; margin-top: 2rem;">
                <h3><span class="icon">📜</span> Order History</h3>
                <?php
                $order_query = "SELECT p.payment_date, pr.name as product_name, p.quantity, p.product_total, p.payment_method 
                                FROM PAYMENT p 
                                JOIN PRODUCTS pr ON p.product_id = pr.id 
                                WHERE p.user_id = ? 
                                ORDER BY p.payment_date DESC";
                $order_stmt = $conn->prepare($order_query);
                $order_stmt->bind_param("i", $_SESSION['id']);
                $order_stmt->execute();
                $orders = $order_stmt->get_result();
                
                if ($orders->num_rows > 0): ?>
                    <table class="data-table">
                        <thead>
                            <tr><th>Date</th><th>Product</th><th>Qty</th><th>Total</th><th>Method</th></tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $orders->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo date('M d, Y', strtotime($row['payment_date'])); ?></td>
                                    <td><?php echo htmlspecialchars($row['product_name']); ?></td>
                                    <td><?php echo $row['quantity']; ?></td>
                                    <td>$<?php echo number_format($row['product_total'], 2); ?></td>
                                    <td><span class="pill pill-info"><?php echo $row['payment_method']; ?></span></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p style="color: var(--text-muted); text-align: center; padding: 2rem;">You haven't placed any orders yet.</p>
                <?php endif; ?>
            </div>

            <!-- Loyalty Progress -->
            <div class="panel-card glass" style="animation-delay:0.4s; margin-top: 2rem;">
                <h3><span class="icon">🏆</span> Aurora Membership</h3>
                <div style="display:flex; align-items:center; gap:2rem; flex-wrap:wrap;">
                    <div>
                        <div class="premium-badge" style="margin-bottom:0.75rem;">Aurora Pro Member</div>
                        <p style="color:var(--text-muted); font-size:0.95rem; max-width:450px;">
                            You have <strong style="color:#fff;">820 loyalty points</strong>. Earn 180 more points to unlock <strong style="color:var(--accent);">Aurora Elite</strong> status and get 15% off all future orders.
                        </p>
                    </div>
                    <div style="flex:1; min-width:200px;">
                        <div style="display:flex; justify-content:space-between; font-size:0.85rem; color:var(--text-muted); margin-bottom:0.5rem;">
                            <span>820 pts</span><span>1,000 pts</span>
                        </div>
                        <div class="progress-bar-wrap">
                            <div class="progress-bar-fill" style="width:82%;"></div>
                        </div>
                    </div>
                </div>
            </div>

            <?php
endif; ?>

        </main>
    </div>

    <!-- Minimal Footer -->
    <footer style="background:#000; padding:2rem 5%; border-top:1px solid var(--surface-border); text-align:center; color:var(--text-muted); font-size:0.9rem;">
        &copy; 2026 <span style="color:var(--primary)">Aurora</span>Tech Inc. All rights reserved.
    </footer>

    <script src="theme-switcher.js"></script>
    <script>
        function toggleProfileModal() {
            const modal = document.getElementById('profile-settings-modal');
            const overlay = document.getElementById('profile-overlay');
            modal.classList.toggle('active');
            overlay.classList.toggle('active');
        }

        function toggleProductModal() {
            const modal = document.getElementById('add-product-modal');
            const overlay = document.getElementById('product-overlay');
            modal.classList.toggle('active');
            overlay.classList.toggle('active');
        }

        function updateProfileColor(color, element) {
            // Update active state in UI
            document.querySelectorAll('.color-swatch').forEach(s => s.classList.remove('active'));
            element.classList.add('active');
            
            // Apply color instantly to CSS variable
            document.documentElement.style.setProperty('--profile-accent', color);
            
            // Persist to database via AJAX
            const formData = new FormData();
            formData.append('action', 'update_color');
            formData.append('color', color);
            
            fetch('../Controller/usercontroller.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (!data.success) {
                    console.error('Failed to save profile color:', data.message);
                }
            })
            .catch(error => console.error('Error:', error));
        }

        // Set active swatch on load
        window.addEventListener('DOMContentLoaded', () => {
            const currentColor = getComputedStyle(document.documentElement).getPropertyValue('--profile-accent').trim();
            document.querySelectorAll('.color-swatch').forEach(swatch => {
                if (swatch.style.background.includes(currentColor)) {
                    swatch.classList.add('active');
                }
            });
        });
    </script>

</body>
</html>
