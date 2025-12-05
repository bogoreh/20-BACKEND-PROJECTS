<?php
// Start session for session-based auth
if (AUTH_METHOD === 'session') {
    require_once 'auth_session.php';
} else {
    require_once 'auth_jwt.php';
}

// Check login status based on auth method
function isUserLoggedIn() {
    if (AUTH_METHOD === 'session') {
        return isLoggedIn();
    } else {
        return isLoggedInJWT();
    }
}

function getCurrentUserData() {
    if (AUTH_METHOD === 'session') {
        return getCurrentUser();
    } else {
        return getCurrentUserJWT();
    }
}

function requireAuthentication() {
    if (AUTH_METHOD === 'session') {
        requireAuth();
    } else {
        requireAuthJWT();
    }
}

function login($user) {
    if (AUTH_METHOD === 'session') {
        return loginUser($user);
    } else {
        return loginUserJWT($user);
    }
}

function logout() {
    if (AUTH_METHOD === 'session') {
        return logoutUser();
    } else {
        return logoutUserJWT();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Authentication App</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <a href="index.php" class="logo">AuthApp</a>
            <div class="nav-links">
                <?php if (isUserLoggedIn()): ?>
                    <?php $user = getCurrentUserData(); ?>
                    <span class="welcome">Welcome, <?php echo htmlspecialchars($user['username']); ?>!</span>
                    <a href="dashboard.php" class="nav-link"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                    <a href="logout.php" class="nav-link logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
                <?php else: ?>
                    <a href="index.php" class="nav-link"><i class="fas fa-home"></i> Home</a>
                    <a href="login.php" class="nav-link"><i class="fas fa-sign-in-alt"></i> Login</a>
                    <a href="signup.php" class="nav-link"><i class="fas fa-user-plus"></i> Sign Up</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>
    <main class="container">