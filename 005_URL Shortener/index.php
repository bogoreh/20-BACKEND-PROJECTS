<?php
require_once 'includes/functions.php';

$shortUrl = '';
$error = '';
$success = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $originalUrl = trim($_POST['url']);
    
    if (empty($originalUrl)) {
        $error = 'Please enter a URL';
    } elseif (!isValidUrl($originalUrl)) {
        $error = 'Please enter a valid URL (include http:// or https://)';
    } else {
        $shortCode = createShortUrl($originalUrl);
        
        if ($shortCode) {
            $shortUrl = BASE_URL . $shortCode;
            $success = 'URL shortened successfully!';
        } else {
            $error = 'Failed to create short URL. Please try again.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>URL Shortener</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="container">
        <header>
            <h1><i class="fas fa-link"></i> URL Shortener</h1>
            <p class="tagline">Shorten your long URLs instantly</p>
        </header>
        
        <main>
            <div class="card">
                <h2>Paste your URL to shorten</h2>
                
                <?php if ($error): ?>
                    <div class="alert alert-error">
                        <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>
                
                <?php if ($success): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($success); ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="">
                    <div class="form-group">
                        <div class="input-group">
                            <i class="fas fa-link input-icon"></i>
                            <input 
                                type="url" 
                                name="url" 
                                placeholder="Enter your long URL (e.g., https://example.com/very-long-url)" 
                                required
                                value="<?php echo isset($_POST['url']) ? htmlspecialchars($_POST['url']) : ''; ?>"
                            >
                        </div>
                    </div>
                    
                    <button type="submit" class="btn-shorten">
                        <i class="fas fa-magic"></i> Shorten URL
                    </button>
                </form>
                
                <?php if ($shortUrl): ?>
                    <div class="result">
                        <h3>Your Short URL:</h3>
                        <div class="short-url-container">
                            <input type="text" id="short-url" value="<?php echo htmlspecialchars($shortUrl); ?>" readonly>
                            <button id="copy-btn" class="btn-copy" title="Copy to clipboard">
                                <i class="far fa-copy"></i> Copy
                            </button>
                        </div>
                        <p class="info">This URL will redirect to your original long URL.</p>
                        
                        <?php 
                        $shortCode = str_replace(BASE_URL, '', $shortUrl);
                        $stats = getUrlStats($shortCode);
                        if ($stats): 
                        ?>
                            <div class="stats">
                                <h4><i class="fas fa-chart-bar"></i> URL Stats</h4>
                                <div class="stats-grid">
                                    <div class="stat-item">
                                        <span class="stat-label">Original URL:</span>
                                        <span class="stat-value"><?php echo htmlspecialchars(substr($stats['original_url'], 0, 50)) . (strlen($stats['original_url']) > 50 ? '...' : ''); ?></span>
                                    </div>
                                    <div class="stat-item">
                                        <span class="stat-label">Created:</span>
                                        <span class="stat-value"><?php echo date('M d, Y', strtotime($stats['created_at'])); ?></span>
                                    </div>
                                    <div class="stat-item">
                                        <span class="stat-label">Clicks:</span>
                                        <span class="stat-value"><?php echo $stats['click_count']; ?></span>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="features">
                <h2>Why Use Our URL Shortener?</h2>
                <div class="features-grid">
                    <div class="feature">
                        <i class="fas fa-bolt"></i>
                        <h3>Fast & Simple</h3>
                        <p>Shorten URLs in seconds with our easy-to-use tool.</p>
                    </div>
                    <div class="feature">
                        <i class="fas fa-shield-alt"></i>
                        <h3>Reliable</h3>
                        <p>Our links never expire and are always accessible.</p>
                    </div>
                    <div class="feature">
                        <i class="fas fa-chart-line"></i>
                        <h3>Track Clicks</h3>
                        <p>See how many times your shortened URL has been clicked.</p>
                    </div>
                </div>
            </div>
        </main>
        
        <footer>
            <p>URL Shortener &copy; <?php echo date('Y'); ?> | Simple PHP Application</p>
        </footer>
    </div>
    
    <script src="assets/js/script.js"></script>
</body>
</html>