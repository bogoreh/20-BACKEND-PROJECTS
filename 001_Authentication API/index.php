<?php require_once 'includes/header.php'; ?>
<div class="hero">
    <div class="hero-content">
        <h1>Welcome to AuthApp</h1>
        <p>A simple and secure authentication system</p>
        <div class="hero-buttons">
            <?php if (isUserLoggedIn()): ?>
                <a href="dashboard.php" class="btn btn-primary">Go to Dashboard</a>
                <a href="logout.php" class="btn btn-secondary">Logout</a>
            <?php else: ?>
                <a href="login.php" class="btn btn-primary">Login</a>
                <a href="signup.php" class="btn btn-secondary">Sign Up</a>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="features">
    <div class="feature">
        <i class="fas fa-shield-alt"></i>
        <h3>Secure</h3>
        <p>Your data is protected with industry-standard security practices</p>
    </div>
    <div class="feature">
        <i class="fas fa-bolt"></i>
        <h3>Fast</h3>
        <p>Lightning-fast authentication with minimal overhead</p>
    </div>
    <div class="feature">
        <i class="fas fa-mobile-alt"></i>
        <h3>Responsive</h3>
        <p>Works perfectly on all devices and screen sizes</p>
    </div>
</div>
<?php require_once 'includes/footer.php'; ?>