// Copy short URL to clipboard
document.addEventListener('DOMContentLoaded', function() {
    const copyBtn = document.getElementById('copy-btn');
    
    if (copyBtn) {
        copyBtn.addEventListener('click', function() {
            const shortUrlInput = document.getElementById('short-url');
            
            // Select the text
            shortUrlInput.select();
            shortUrlInput.setSelectionRange(0, 99999); // For mobile devices
            
            // Copy to clipboard
            navigator.clipboard.writeText(shortUrlInput.value)
                .then(() => {
                    // Change button text temporarily
                    const originalHTML = copyBtn.innerHTML;
                    copyBtn.innerHTML = '<i class="fas fa-check"></i> Copied!';
                    copyBtn.style.background = '#00b894';
                    
                    // Revert after 2 seconds
                    setTimeout(() => {
                        copyBtn.innerHTML = originalHTML;
                        copyBtn.style.background = '';
                    }, 2000);
                })
                .catch(err => {
                    console.error('Failed to copy: ', err);
                    alert('Failed to copy URL to clipboard. Please copy manually.');
                });
        });
    }
    
    // Auto-select URL when input is clicked
    const urlInput = document.querySelector('input[name="url"]');
    if (urlInput) {
        urlInput.addEventListener('click', function() {
            this.select();
        });
    }
});