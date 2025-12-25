document.addEventListener('DOMContentLoaded', function() {
    const contactForm = document.getElementById('contactForm');
    const submitBtn = document.getElementById('submitBtn');
    const spinner = document.getElementById('spinner');
    const formMessage = document.getElementById('formMessage');
    
    // Clear error messages on input
    const inputs = contactForm.querySelectorAll('input, textarea');
    inputs.forEach(input => {
        input.addEventListener('input', function() {
            clearError(this.id);
        });
    });
    
    // Form submission
    contactForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Validate form
        if (validateForm()) {
            submitForm();
        }
    });
    
    function validateForm() {
        let isValid = true;
        
        // Clear all errors first
        clearAllErrors();
        
        // Validate name
        const name = document.getElementById('name').value.trim();
        if (name.length < 2) {
            showError('nameError', 'Name must be at least 2 characters');
            isValid = false;
        }
        
        // Validate email
        const email = document.getElementById('email').value.trim();
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            showError('emailError', 'Please enter a valid email address');
            isValid = false;
        }
        
        // Validate subject
        const subject = document.getElementById('subject').value.trim();
        if (subject.length < 3) {
            showError('subjectError', 'Subject must be at least 3 characters');
            isValid = false;
        }
        
        // Validate message
        const message = document.getElementById('message').value.trim();
        if (message.length < 10) {
            showError('messageError', 'Message must be at least 10 characters');
            isValid = false;
        }
        
        return isValid;
    }
    
    function submitForm() {
        // Show loading state
        submitBtn.disabled = true;
        spinner.style.display = 'block';
        submitBtn.querySelector('i').style.display = 'none';
        
        // Create FormData object
        const formData = new FormData(contactForm);
        
        // Send data via Fetch API
        fetch('api/submit_contact.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            // Hide loading state
            submitBtn.disabled = false;
            spinner.style.display = 'none';
            submitBtn.querySelector('i').style.display = 'inline-block';
            
            if (data.success) {
                // Show success message
                showMessage('success', data.message);
                
                // Clear form
                contactForm.reset();
                clearAllErrors();
                
                // Redirect to success page after 2 seconds
                setTimeout(() => {
                    window.location.href = 'success.php';
                }, 2000);
            } else {
                // Show error message
                showMessage('error', data.message);
                
                // Display field-specific errors
                if (data.errors) {
                    for (const field in data.errors) {
                        showError(field + 'Error', data.errors[field]);
                    }
                }
            }
        })
        .catch(error => {
            // Hide loading state
            submitBtn.disabled = false;
            spinner.style.display = 'none';
            submitBtn.querySelector('i').style.display = 'inline-block';
            
            showMessage('error', 'Network error. Please check your connection and try again.');
            console.error('Error:', error);
        });
    }
    
    function showError(elementId, message) {
        const errorElement = document.getElementById(elementId);
        if (errorElement) {
            errorElement.textContent = message;
            errorElement.style.display = 'block';
            
            // Highlight the corresponding input
            const fieldId = elementId.replace('Error', '');
            const inputElement = document.getElementById(fieldId);
            if (inputElement) {
                inputElement.style.borderColor = '#e74c3c';
            }
        }
    }
    
    function clearError(fieldId) {
        const errorElement = document.getElementById(fieldId + 'Error');
        if (errorElement) {
            errorElement.textContent = '';
            errorElement.style.display = 'none';
            
            const inputElement = document.getElementById(fieldId);
            if (inputElement) {
                inputElement.style.borderColor = '#e0e0e0';
            }
        }
    }
    
    function clearAllErrors() {
        const errorElements = document.querySelectorAll('.error-message');
        errorElements.forEach(element => {
            element.textContent = '';
            element.style.display = 'none';
        });
        
        inputs.forEach(input => {
            input.style.borderColor = '#e0e0e0';
        });
        
        formMessage.style.display = 'none';
    }
    
    function showMessage(type, message) {
        formMessage.textContent = message;
        formMessage.className = `message ${type}`;
        formMessage.style.display = 'block';
        
        // Scroll to message
        formMessage.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }
});