<?php
session_start();
include '../Database/connection.php';

if (!isset($_SESSION['id']) || strtolower($_SESSION['role']) !== 'admin') {
    header("Location: login.php");
    exit();
}

$user_name = htmlspecialchars($_SESSION['name']);
$user_role = strtolower($_SESSION['role']);

// Fetch Orders
$orders_query = "SELECT p.id as order_id, u.name as customer, pr.name as product, p.total_price, p.status, p.payment_date
                 FROM PAYMENT p 
                 JOIN USERS u ON p.user_id = u.id 
                 JOIN PRODUCTS pr ON p.product_id = pr.id 
                 ORDER BY p.payment_date DESC";
$orders_res = mysqli_query($conn, $orders_query);
$orders = $orders_res ? mysqli_fetch_all($orders_res, MYSQLI_ASSOC) : [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Orders - AuroraTech</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&display=swap" rel="stylesheet">
    <style>
        :root {
            <?php if (isset($_SESSION['profile_color'])): ?>
            --profile-accent: <?php echo $_SESSION['profile_color']; ?>;
            <?php endif; ?>
        }
        .dashboard-body { background: var(--bg-main); padding: 3rem 4%; }
        .data-table { width: 100%; border-collapse: collapse; margin-top: 2rem; }
        .data-table th { text-align: left; padding: 1rem; color: var(--text-muted); border-bottom: 1px solid var(--surface-border); }
        .data-table td { padding: 1rem; border-bottom: 1px solid rgba(255,255,255,0.04); color: var(--text-main); }
        .panel-card { padding: 2rem; border-radius: 20px; background: rgba(255,255,255,0.03); border: 1px solid var(--surface-border); }
    </style>
</head>
<body class="dashboard-body">
    <div style="margin-bottom: 2rem;">
        <a href="dashboard.php" class="btn btn-primary" style="padding: 0.5rem 1rem; text-decoration: none;">← Back to Dashboard</a>
    </div>
    
    <div class="panel-card glass">
        <h2><span class="icon">📊</span> Manage Orders</h2>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Customer</th>
                    <th>Product</th>
                    <th>Total</th>
                    <th>Date</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $o): ?>
                <tr>
                    <td>#ORD-<?php echo $o['order_id']; ?></td>
                    <td><?php echo htmlspecialchars($o['customer']); ?></td>
                    <td><?php echo htmlspecialchars($o['product']); ?></td>
                    <td><?php echo number_format($o['total_price'], 0, '.', ','); ?> FCFA</td>
                    <td><?php echo date('M d, Y', strtotime($o['payment_date'])); ?></td>
                    <td><span style="padding: 0.3rem 0.8rem; border-radius: 20px; font-size: 0.8rem; background: rgba(255,255,255,0.1);"><?php echo htmlspecialchars($o['status']); ?></span></td>
                </tr>
                <?php endforeach; ?>
                <?php if(empty($orders)) echo "<tr><td colspan='6'>No orders found.</td></tr>"; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
