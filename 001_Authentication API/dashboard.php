<?php
require_once 'includes/header.php';
requireAuthentication(); // This will redirect to login if not authenticated

$user = getCurrentUserData();
?>

<div class="dashboard">
    <div class="dashboard-header">
        <h1>Dashboard</h1>
        <p>Welcome to your personal dashboard</p>
    </div>
    
    <div class="user-info">
        <div class="info-card">
            <div class="info-icon">
                <i class="fas fa-user-circle"></i>
            </div>
            <div class="info-content">
                <h3>Your Profile</h3>
                <p><strong>Username:</strong> <?php echo htmlspecialchars($user['username']); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
                <p><strong>Authentication Method:</strong> <?php echo AUTH_METHOD; ?></p>
            </div>
        </div>
        
        <div class="info-card">
            <div class="info-icon">
                <i class="fas fa-shield-alt"></i>
            </div>
            <div class="info-content">
                <h3>Account Security</h3>
                <p>Your account is protected with <?php echo AUTH_METHOD === 'session' ? 'PHP Sessions' : 'JWT Tokens'; ?></p>
                <p>Last login: <?php echo date('F j, Y, g:i a'); ?></p>
            </div>
        </div>
        
        <div class="info-card">
            <div class="info-icon">
                <i class="fas fa-cog"></i>
            </div>
            <div class="info-content">
                <h3>Quick Actions</h3>
                <div class="action-buttons">
                    <a href="#" class="btn btn-small"><i class="fas fa-edit"></i> Edit Profile</a>
                    <a href="#" class="btn btn-small"><i class="fas fa-key"></i> Change Password</a>
                    <a href="logout.php" class="btn btn-small btn-danger"><i class="fas fa-sign-out-alt"></i> Logout</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>