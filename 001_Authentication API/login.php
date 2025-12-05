<?php
require_once 'config/database.php';
require_once 'includes/header.php';

// Redirect if already logged in
if (isUserLoggedIn()) {
    header('Location: dashboard.php');
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    
    if (empty($username) || empty($password)) {
        $error = 'Please enter username and password';
    } else {
        $conn = getDBConnection();
        
        $query = "SELECT id, username, email, password FROM users WHERE username = ? OR email = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "ss", $username, $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if ($row = mysqli_fetch_assoc($result)) {
            if (password_verify($password, $row['password'])) {
                // Login successful
                $user = [
                    'id' => $row['id'],
                    'username' => $row['username'],
                    'email' => $row['email']
                ];
                
                login($user);
                header('Location: dashboard.php');
                exit();
            } else {
                $error = 'Invalid password';
            }
        } else {
            $error = 'User not found';
        }
        
        mysqli_close($conn);
    }
}
?>

<div class="form-container">
    <h2>Login to Your Account</h2>
    
    <?php if ($error): ?>
        <div class="alert alert-error">
            <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
        </div>
    <?php endif; ?>
    
    <form method="POST" action="" class="auth-form">
        <div class="form-group">
            <label for="username"><i class="fas fa-user"></i> Username or Email</label>
            <input type="text" id="username" name="username" required 
                   value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">
        </div>
        
        <div class="form-group">
            <label for="password"><i class="fas fa-lock"></i> Password</label>
            <input type="password" id="password" name="password" required>
        </div>
        
        <button type="submit" class="btn btn-primary btn-block">Login</button>
        
        <div class="form-footer">
            Don't have an account? <a href="signup.php">Sign up here</a>
        </div>
    </form>
</div>

<?php require_once 'includes/footer.php'; ?>