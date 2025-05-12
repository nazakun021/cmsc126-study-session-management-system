// Toast notification system
const Toast = {
    show(message, type = 'info') {
        const toast = document.createElement('div');
        toast.className = `toast toast-${type}`;
        toast.textContent = message;
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 3000);
    }
};

// Loading state management
const Loading = {
    show(element) {
        element.classList.add('loading');
        element.innerHTML = '<div class="spinner"></div>';
    },
    hide(element) {
        element.classList.remove('loading');
    }
};

// Form validation
const FormValidator = {
    validate(form) {
        const errors = [];
        const fields = form.querySelectorAll('[required]');
        
        fields.forEach(field => {
            if (!field.value.trim()) {
                errors.push(`${field.name} is required`);
                field.classList.add('error');
            } else {
                field.classList.remove('error');
            }
        });
        
        return errors;
    },
    
    showErrors(errors) {
        const errorDiv = document.createElement('div');
        errorDiv.className = 'form-errors';
        errorDiv.innerHTML = errors.map(error => `<p>${error}</p>`).join('');
        return errorDiv;
    }
};

// Rate limiting
const RateLimiter = {
    limits: new Map(),
    
    check(formId, maxAttempts = 5, timeWindow = 60000) {
        const now = Date.now();
        const count = this.limits.get(formId) || 0;
        
        if (count > maxAttempts && now - this.limits.get('time') < timeWindow) {
            throw new Error('Too many submissions. Please wait a minute.');
        }
        
        this.limits.set(formId, count + 1);
        this.limits.set('time', now);
    }
};

// Debouncing utility
const debounce = (func, wait) => {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
};

// Error handling
const ErrorHandler = {
    handle(error) {
        console.error(error);
        Toast.show(error.message || 'An error occurred. Please try again.', 'error');
    }
};

// AJAX helper
const Ajax = {
    async request(url, options = {}) {
        try {
            Loading.show(document.querySelector('#content'));
            const response = await fetch(url, {
                ...options,
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]').content,
                    ...options.headers
                }
            });
            
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            
            return await response.json();
        } catch (error) {
            ErrorHandler.handle(error);
            throw error;
        } finally {
            Loading.hide(document.querySelector('#content'));
        }
    }
};

// Accessibility helpers
const Accessibility = {
    addKeyboardNavigation() {
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                const modals = document.querySelectorAll('.modal');
                modals.forEach(modal => modal.style.display = 'none');
            }
        });
    },
    
    addARIALabels() {
        const buttons = document.querySelectorAll('button');
        buttons.forEach(button => {
            if (!button.getAttribute('aria-label')) {
                button.setAttribute('aria-label', button.textContent.trim());
            }
        });
    }
};

// Initialize accessibility features
document.addEventListener('DOMContentLoaded', () => {
    Accessibility.addKeyboardNavigation();
    Accessibility.addARIALabels();
}); 