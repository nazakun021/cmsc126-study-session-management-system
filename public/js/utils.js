// Utility functions
console.log('Utils.js loaded successfully');

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

// Common utility functions
const Utils = {
    // Format date for display
    formatDate(dateString) {
        const options = { year: 'numeric', month: 'long', day: 'numeric' };
        return new Date(dateString).toLocaleDateString(undefined, options);
    },
    
    // Generate a unique ID
    generateId() {
        return Date.now().toString(36) + Math.random().toString(36).substring(2);
    },
    
    // Format time from 24h to 12h format
    formatTime(time24) {
        const [hours, minutes] = time24.split(':');
        const hour12 = hours % 12 || 12;
        const ampm = hours >= 12 ? 'PM' : 'AM';
        return `${hour12}:${minutes} ${ampm}`;
    }
};

// Error handling
const ErrorHandler = {
    handle(error) {
        console.error(error);
        Toast.show(error.message || 'An error occurred. Please try again.', 'error');
    }
};

// Notification utilities - centralized message handling
const Notification = {
    success(message) {
        Toast.show(message, 'success');
    },
    
    error(message) {
        Toast.show(message, 'error');
    },
    
    info(message) {
        Toast.show(message, 'info');
    },
    
    warn(message) {
        Toast.show(message, 'warning');
    }
};

// AJAX helper
const Ajax = {
    async request(url, options = {}) {
        try {
            Loading.show(document.querySelector('#content'));            const response = await fetch(url, {
                ...options,
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-Token': CSRF.getToken(),
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

// CSRF token utilities
const CSRF = {
    getToken() {
        const meta = document.querySelector('meta[name="csrf-token"]');
        return meta ? meta.content : null;
    },
    
    addToFormData(formData) {
        const token = this.getToken();
        if (token) {
            formData.append('csrf_token', token);
            return true;
        }
        return false;
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

// Modal management utilities
const Modal = {
    open(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.style.display = 'flex';
        }
    },
    
    close(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.style.display = 'none';
            // Reset form if it exists
            const form = modal.querySelector('form');
            if (form) {
                form.reset();
            }
        }
    },
    
    setupCloseButtons(modalId) {
        const modal = document.getElementById(modalId);
        if (!modal) return;
        
        // Close button
        const closeBtn = modal.querySelector('.close-btn, #close-modal');
        if (closeBtn) {
            closeBtn.addEventListener('click', () => this.close(modalId));
        }
        
        // Cancel button
        const cancelBtn = modal.querySelector('[id*="cancel"]');
        if (cancelBtn) {
            cancelBtn.addEventListener('click', () => this.close(modalId));
        }
        
        // Click outside to close
        window.addEventListener('click', (e) => {
            if (e.target === modal) {
                this.close(modalId);
            }
        });
    }
};

// Empty state management utilities
const EmptyState = {
    update(containerId, emptyStateId, items) {
        const container = document.getElementById(containerId);
        const emptyState = document.getElementById(emptyStateId);
        
        if (!container || !emptyState) return;
        
        if (items.length === 0) {
            emptyState.style.display = 'flex';
            container.style.display = 'none';
        } else {
            emptyState.style.display = 'none';
            container.style.display = 'block';
        }
    }
};

// Mobile menu utilities
const MobileMenu = {
    init() {
        const menuToggle = document.getElementById('menu-toggle');
        const sidebar = document.querySelector('.sidebar');
        
        if (menuToggle && sidebar) {
            menuToggle.addEventListener('click', () => {
                sidebar.classList.toggle('active');
            });
        }
    }
};

// Initialize accessibility features
document.addEventListener('DOMContentLoaded', () => {
    Accessibility.addKeyboardNavigation();
    Accessibility.addARIALabels();
});