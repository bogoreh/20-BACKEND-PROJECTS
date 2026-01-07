document.addEventListener('DOMContentLoaded', function() {
    // Temperature unit toggle
    const unitRadios = document.querySelectorAll('input[name="units"]');
    const tempInputs = document.querySelectorAll('.temp-input');
    
    unitRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            const labels = document.querySelectorAll('.temp-label');
            const unit = this.value === 'metric' ? '°C' : '°F';
            
            labels.forEach(label => {
                label.textContent = label.textContent.replace(/°[CF]/, unit);
            });
        });
    });
    
    // Form validation
    const form = document.querySelector('form');
    form.addEventListener('submit', function(e) {
        const city = document.getElementById('city').value.trim();
        const minTemp = document.getElementById('min_temp').value;
        const maxTemp = document.getElementById('max_temp').value;
        
        if (!city) {
            e.preventDefault();
            alert('Please enter a city name');
            return;
        }
        
        if (minTemp && maxTemp && parseFloat(minTemp) > parseFloat(maxTemp)) {
            e.preventDefault();
            alert('Minimum temperature cannot be greater than maximum temperature');
            return;
        }
    });
    
    // Show loading on submit
    form.addEventListener('submit', function() {
        const submitBtn = this.querySelector('button[type="submit"]');
        submitBtn.innerHTML = '<div class="spinner"></div>';
        submitBtn.disabled = true;
    });
    
    // Auto-detect location button
    const detectBtn = document.querySelector('#detect-location');
    if (detectBtn) {
        detectBtn.addEventListener('click', function() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    function(position) {
                        // Reverse geocoding would be implemented here
                        alert('Location detected! City would be auto-filled with reverse geocoding API.');
                    },
                    function(error) {
                        alert('Unable to detect location. Please enter manually.');
                    }
                );
            } else {
                alert('Geolocation is not supported by your browser');
            }
        });
    }
    
    // Animate temperature changes
    const tempElements = document.querySelectorAll('.temperature, .day-temp');
    tempElements.forEach(element => {
        const temp = parseFloat(element.textContent);
        if (!isNaN(temp)) {
            if (temp > 30) {
                element.style.color = '#e74c3c'; // Hot
            } else if (temp < 10) {
                element.style.color = '#3498db'; // Cold
            }
        }
    });
});