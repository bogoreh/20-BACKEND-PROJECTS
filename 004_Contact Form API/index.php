<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - Get in Touch</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Roboto:wght@300;400;500&display=swap" rel="stylesheet">
</head>
<body>
    <div class="container">
        <div class="contact-wrapper">
            <div class="contact-info">
                <h1><i class="fas fa-envelope-open-text"></i> Get in Touch</h1>
                <p class="subtitle">We'd love to hear from you. Send us a message and we'll respond as soon as possible.</p>
                
                <div class="info-section">
                    <div class="info-item">
                        <i class="fas fa-map-marker-alt"></i>
                        <div>
                            <h3>Our Location</h3>
                            <p>123 Business Street, Suite 100<br>New York, NY 10001</p>
                        </div>
                    </div>
                    
                    <div class="info-item">
                        <i class="fas fa-phone"></i>
                        <div>
                            <h3>Phone Number</h3>
                            <p>+1 (555) 123-4567</p>
                        </div>
                    </div>
                    
                    <div class="info-item">
                        <i class="fas fa-envelope"></i>
                        <div>
                            <h3>Email Address</h3>
                            <p>info@yourcompany.com</p>
                        </div>
                    </div>
                    
                    <div class="info-item">
                        <i class="fas fa-clock"></i>
                        <div>
                            <h3>Working Hours</h3>
                            <p>Monday - Friday: 9:00 AM - 6:00 PM</p>
                        </div>
                    </div>
                </div>
                
                <div class="social-links">
                    <a href="#"><i class="fab fa-facebook-f"></i></a>
                    <a href="#"><i class="fab fa-twitter"></i></a>
                    <a href="#"><i class="fab fa-linkedin-in"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                </div>
            </div>
            
            <div class="contact-form">
                <div class="form-header">
                    <h2><i class="fas fa-paper-plane"></i> Send Message</h2>
                    <p>Fill out the form below and we'll get back to you shortly.</p>
                </div>
                
                <form id="contactForm" method="POST">
                    <div id="formMessage" class="message"></div>
                    
                    <div class="form-group">
                        <label for="name"><i class="fas fa-user"></i> Full Name *</label>
                        <input type="text" id="name" name="name" placeholder="Enter your full name" required>
                        <div class="error-message" id="nameError"></div>
                    </div>
                    
                    <div class="form-group">
                        <label for="email"><i class="fas fa-envelope"></i> Email Address *</label>
                        <input type="email" id="email" name="email" placeholder="Enter your email address" required>
                        <div class="error-message" id="emailError"></div>
                    </div>
                    
                    <div class="form-group">
                        <label for="subject"><i class="fas fa-tag"></i> Subject *</label>
                        <input type="text" id="subject" name="subject" placeholder="What is this regarding?" required>
                        <div class="error-message" id="subjectError"></div>
                    </div>
                    
                    <div class="form-group">
                        <label for="message"><i class="fas fa-comment-dots"></i> Your Message *</label>
                        <textarea id="message" name="message" rows="5" placeholder="Please type your message here..." required></textarea>
                        <div class="error-message" id="messageError"></div>
                    </div>
                    
                    <button type="submit" id="submitBtn" class="submit-btn">
                        <i class="fas fa-paper-plane"></i> Send Message
                        <div class="spinner" id="spinner"></div>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script src="assets/js/script.js"></script>
</body>
</html>