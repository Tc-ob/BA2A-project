<?php
session_start();
include "../Database/connection.php";

// Ensure user is logged in
if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['id'];
$user_name = htmlspecialchars($_SESSION['name']);
$user_initial = strtoupper(substr($user_name, 0, 1));

// Fetch cart items to display
$cart_query = "SELECT c.id as cart_id, c.quantity, p.name as product_name, p.price 
               FROM CART c 
               JOIN PRODUCTS p ON c.product_id = p.id 
               WHERE c.user_id = ?";
$stmt = $conn->prepare($cart_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$cart_items = $result->fetch_all(MYSQLI_ASSOC);

if (empty($cart_items)) {
    header("Location: dashboard.php");
    exit();
}

$total_price = 0;
foreach ($cart_items as $item) {
    $total_price += $item['price'] * $item['quantity'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - AuroraTech</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&display=swap" rel="stylesheet">
    <style>
        :root {
            <?php if (isset($_SESSION['profile_color'])): ?>
            --profile-accent: <?php echo $_SESSION['profile_color']; ?>;
            <?php endif; ?>
        }
        .checkout-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 2rem;
        }

        .summary-card {
            padding: 2.5rem;
            border-radius: 20px;
            margin-bottom: 2rem;
        }

        .order-item {
            display: flex;
            justify-content: space-between;
            padding: 1rem 0;
            border-bottom: 1px solid var(--surface-border);
        }

        .order-item:last-of-type { border-bottom: none; }

        .total-row {
            display: flex;
            justify-content: space-between;
            padding-top: 1.5rem;
            margin-top: 1rem;
            border-top: 2px solid var(--surface-border);
            font-size: 1.5rem;
            font-weight: 800;
        }

        .payment-methods {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin: 2rem 0;
        }

        .method-card {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid var(--surface-border);
            border-radius: 15px;
            padding: 1.5rem;
            cursor: pointer;
            text-align: center;
            transition: all 0.3s ease;
            position: relative;
        }

        .method-card:hover {
            background: rgba(255, 255, 255, 0.08);
            border-color: var(--primary);
        }

        .method-card input {
            position: absolute;
            opacity: 0;
        }

        .method-card.active {
            background: rgba(244, 63, 94, 0.1);
            border-color: var(--primary);
            box-shadow: 0 0 20px rgba(244, 63, 94, 0.2);
        }

        .method-icon {
            font-size: 2rem;
            margin-bottom: 0.75rem;
            display: block;
        }

        .method-name {
            font-weight: 700;
            font-size: 1rem;
        }

        .btn-confirm {
            width: 100%;
            padding: 1.2rem;
            font-size: 1.2rem;
            font-weight: 800;
            margin-top: 1rem;
        }

        .error-msg {
            background: rgba(244, 63, 94, 0.1);
            color: #f43f5e;
            padding: 1rem;
            border-radius: 10px;
            margin-bottom: 1.5rem;
            border: 1px solid rgba(244, 63, 94, 0.3);
            text-align: center;
        }
    </style>
</head>
<body class="dashboard-body">

    <!-- Navbar -->
    <nav class="navbar glass">
        <div class="logo">
            <a href="index.php"><span class="gradient-text">Aurora</span>Tech</a>
        </div>
        <div class="user-profile" style="display:flex; align-items:center; gap:1.2rem;">
            <!-- Theme Switcher -->
            <div class="theme-switcher">
                <div class="theme-btn t-dark" id="btn-dark" onclick="setTheme('dark')" title="Dark Mode"></div>
                <div class="theme-btn t-light" id="btn-light" onclick="setTheme('light')" title="Light Mode"></div>
                <div class="theme-btn t-sky" id="btn-sky" onclick="setTheme('sky')" title="Sky Blue Mode"></div>
            </div>
            <div class="avatar-circle"><?php echo $user_initial; ?></div>
            <a href="dashboard.php" class="btn btn-logout">Cancel</a>
        </div>
    </nav>

    <main class="page-padding">
        <div class="checkout-container">
            <h1 class="fade-in-up" style="margin-bottom: 2rem;">Secure <span>Checkout</span></h1>

            <?php if (isset($_SESSION['payment_error'])): ?>
                <div class="error-msg fade-in-up">
                    <?php echo $_SESSION['payment_error']; unset($_SESSION['payment_error']); ?>
                </div>
            <?php endif; ?>

            <div class="summary-card glass fade-in-up delay-1">
                <h3 style="margin-bottom: 1.5rem;">Order Summary</h3>
                <div class="order-list">
                    <?php foreach ($cart_items as $item): ?>
                        <div class="order-item">
                            <div>
                                <strong><?php echo htmlspecialchars($item['product_name']); ?></strong>
                                <br><small style="color: var(--text-muted);">Quantity: <?php echo $item['quantity']; ?></small>
                            </div>
                            <span><?php echo number_format($item['price'] * $item['quantity'], 0, '.', ','); ?> FCFA</span>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="total-row">
                    <span>Total Amount</span>
                    <span class="gradient-text"><?php echo number_format($total_price, 0, '.', ','); ?> FCFA</span>
                </div>
            </div>

            <form action="../Controller/paymentcontroller.php" method="POST" class="fade-in-up delay-2">
                <h3 style="margin-bottom: 1rem;">Select Payment Method</h3>
                <div class="payment-methods">
                    <label class="method-card" id="card-momo">
                        <input type="radio" name="payment_method" value="Mobile Money" required onclick="updateActive(this)">
                        <span class="method-icon">📱</span>
                        <span class="method-name">Mobile Money</span>
                    </label>

                    <label class="method-card" id="card-orange">
                        <input type="radio" name="payment_method" value="Orange Money" required onclick="updateActive(this)">
                        <span class="method-icon">🍊</span>
                        <span class="method-name">Orange Money</span>
                    </label>

                    <label class="method-card" id="card-bank">
                        <input type="radio" name="payment_method" value="Bank Account" required onclick="updateActive(this)">
                        <span class="method-icon">🏦</span>
                        <span class="method-name">Bank Account</span>
                    </label>
                </div>

                <button type="submit" name="place_order" class="btn btn-primary btn-confirm">Confirm Purchase & Pay</button>
            </form>
        </div>
    </main>

    <script>
        function updateActive(input) {
            document.querySelectorAll('.method-card').forEach(card => card.classList.remove('active'));
            input.closest('.method-card').classList.add('active');
        }
    </script>
    <script src="theme-switcher.js"></script>

</body>
</html>
