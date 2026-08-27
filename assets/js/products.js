// assets/js/products.js
document.addEventListener('DOMContentLoaded', function() {
    const minSlider = document.getElementById('minPriceSlider');
    const maxSlider = document.getElementById('maxPriceSlider');
    const minDisplay = document.getElementById('minPriceDisplay');
    const maxDisplay = document.getElementById('maxPriceDisplay');
    
    if (minSlider && maxSlider) {
        // Update display on change
        minSlider.addEventListener('input', function() {
            minDisplay.textContent = this.value;
        });
        
        maxSlider.addEventListener('input', function() {
            maxDisplay.textContent = this.value;
        });
        
        // Ensure min <= max
        minSlider.addEventListener('change', function() {
            if (parseInt(this.value) > parseInt(maxSlider.value)) {
                this.value = maxSlider.value;
                minDisplay.textContent = this.value;
            }
        });
        
        maxSlider.addEventListener('change', function() {
            if (parseInt(this.value) < parseInt(minSlider.value)) {
                this.value = minSlider.value;
                maxDisplay.textContent = this.value;
            }
        });
    }
});