<?php
session_start();
include '../Database/connection.php';

if (!isset($_SESSION['id']) || strtolower($_SESSION['role']) !== 'admin') {
    header("Location: login.php");
    exit();
}

$user_name = htmlspecialchars($_SESSION['name']);
$user_role = strtolower($_SESSION['role']);
$user_initial = strtoupper(substr($user_name, 0, 1));

// Fetch Users
$users_res = mysqli_query($conn, "SELECT id, name, email, role FROM USERS ORDER BY id DESC");
$users = $users_res ? mysqli_fetch_all($users_res, MYSQLI_ASSOC) : [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - AuroraTech</title>
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
        <h2><span class="icon">👥</span> Manage Users</h2>
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $u): ?>
                <tr>
                    <td><?php echo $u['id']; ?></td>
                    <td><?php echo htmlspecialchars($u['name']); ?></td>
                    <td><?php echo htmlspecialchars($u['email']); ?></td>
                    <td><span style="padding: 0.3rem 0.8rem; border-radius: 20px; font-size: 0.8rem; background: rgba(255,255,255,0.1);"><?php echo htmlspecialchars($u['role']); ?></span></td>
                </tr>
                <?php endforeach; ?>
                <?php if(empty($users)) echo "<tr><td colspan='4'>No users found.</td></tr>"; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
