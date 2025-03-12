import './bootstrap';
import Alpine from 'alpinejs';

window.Alpine = Alpine;
Alpine.start();

// Confirmation dialogs
window.confirmAction = function(message, callback) {
    if (confirm(message)) {
        callback();
    }
};

// Toggle mobile menu
document.addEventListener('DOMContentLoaded', function() {
    const mobileMenuButton = document.getElementById('mobile-menu-button');
    const mobileMenu = document.getElementById('mobile-menu');

    if (mobileMenuButton && mobileMenu) {
        mobileMenuButton.addEventListener('click', function() {
            mobileMenu.classList.toggle('hidden');
        });
    }

    // Auto-hide alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.opacity = '0';
            alert.style.transition = 'opacity 1s';
            setTimeout(() => {
                alert.style.display = 'none';
            }, 1000);
        }, 5000);
    });

    // Initialize any datepickers
    const datepickers = document.querySelectorAll('.datepicker');
    if (datepickers.length > 0) {
        // If you want to use a datepicker library, you would initialize it here
    }

    // Initialize any select2 dropdowns
    const select2Dropdowns = document.querySelectorAll('.select2');
    if (select2Dropdowns.length > 0) {
        // If you want to use select2, you would initialize it here
    }
});
