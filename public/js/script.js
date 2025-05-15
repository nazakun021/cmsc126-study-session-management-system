// Main application script
console.log('Main script.js loaded successfully');

// Add error handling for script loading
window.addEventListener('error', function(e) {
    if (e.target.tagName === 'SCRIPT') {
        console.error('Script loading error:', e.target.src);
        // You could also show a user-friendly error message
        const Toast = window.Toast;
        if (Toast) {
            Toast.show('Failed to load some resources. Please refresh the page.', 'error');
        }
    }
}, true);

document.addEventListener('DOMContentLoaded', function() {
    // Initialize Feather icons
    feather.replace();
    
    // Dropdown functionality
    const dropdownToggle = document.querySelector('.dropdown-toggle');
    const dropdownMenu = document.querySelector('.dropdown-menu');
    
    if (dropdownToggle && dropdownMenu) {
        dropdownToggle.addEventListener('click', function(e) {
            e.stopPropagation();
            dropdownMenu.classList.toggle('show');
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!dropdownToggle.contains(e.target) && !dropdownMenu.contains(e.target)) {
                dropdownMenu.classList.remove('show');
            }
        });
    }
    
    // DOM elements
    const addSessionBtn = document.getElementById('add-session-btn');
    const emptyAddBtn = document.getElementById('empty-add-btn');
    const addSessionModal = document.getElementById('add-session-modal');
    const closeModalBtn = document.getElementById('close-modal');
    const cancelAddBtn = document.getElementById('cancel-add');
    const addSessionForm = document.getElementById('addSessionForm');
    const sessionsContainer = document.getElementById('sessions-container');
    const emptyState = document.getElementById('empty-state');
    const deleteModal = document.getElementById('delete-modal');
    const closeDeleteModalBtn = document.getElementById('close-delete-modal');
    const cancelDeleteBtn = document.getElementById('cancel-delete');
    const confirmDeleteBtn = document.getElementById('confirm-delete');
    
    // Stats elements
    const totalSessionsEl = document.getElementById('total-sessions');
    const totalSubjectsEl = document.getElementById('total-subjects');
    const upcomingSessionsEl = document.getElementById('upcoming-sessions');
    const avgAttendanceEl = document.getElementById('avg-attendance');
    
    // Session data array (empty initially)
    let sessions = [];
    let sessionToDelete = null;
    
    // Show/hide empty state based on sessions
    function updateEmptyState() {
        const sessions = document.querySelectorAll('.session-card');
        if (!emptyState || !sessionsContainer) return;
        if (sessions.length === 0) {
            emptyState.style.display = 'flex';
            sessionsContainer.style.display = 'none';
        } else {
            emptyState.style.display = 'none';
            sessionsContainer.style.display = 'block';
        }
        
        // Update stats
        if (typeof updateStats === 'function') updateStats();
    }
    
    // Update dashboard stats
    function updateStats() {
        // Total sessions
        totalSessionsEl.textContent = sessions.length;
        
        // Get unique subjects
        const subjects = new Set(sessions.map(session => session.subject));
        totalSubjectsEl.textContent = subjects.size;
        
        // Count upcoming sessions (those with dates in the future)
        const today = new Date();
        today.setHours(0, 0, 0, 0);
        const upcoming = sessions.filter(session => {
            const sessionDate = new Date(session.date);
            return sessionDate >= today;
        });
        upcomingSessionsEl.textContent = upcoming.length;
        
        // For demo purposes, set a random attendance percentage
        avgAttendanceEl.textContent = sessions.length > 0 ? Math.floor(Math.random() * 40 + 60) + '%' : '0%';
    }
    
    // Render all sessions
    function renderSessions() {
        // Clear existing sessions
        sessionsContainer.innerHTML = '';
        
        // Add each session
        sessions.forEach(session => {
            addSessionToDOM(session);
        });
        
        // Re-initialize Feather icons for new content
        feather.replace();
        
        // Update empty state
        updateEmptyState();
    }
    
    // Add a single session to the DOM
    function addSessionToDOM(session) {
        const template = document.getElementById('session-card-template');
        const clone = document.importNode(template.content, true);
        
        // Set session data
        clone.querySelector('.card-title').textContent = session.title;
        clone.querySelector('.card-subject').textContent = session.subject;
        clone.querySelector('.card-date').textContent = formatDate(session.date);
        clone.querySelector('.card-time').textContent = `${session.startTime} - ${session.endTime}`;
        clone.querySelector('.card-location').textContent = session.location;
        
        // Set data attribute for identification
        const sessionCard = clone.querySelector('.session-card');
        sessionCard.dataset.id = session.id;
        
        // Add event listeners for actions
        const deleteBtn = clone.querySelector('.delete-session');
        deleteBtn.addEventListener('click', (e) => {
            e.preventDefault();
            sessionToDelete = session.id;
            deleteModal.style.display = 'flex';
        });
        
        sessionsContainer.appendChild(clone);
    }
    
    // Format date for display
    function formatDate(dateString) {
        const options = { year: 'numeric', month: 'long', day: 'numeric' };
        return new Date(dateString).toLocaleDateString(undefined, options);
    }
    
    // Generate a unique ID
    function generateId() {
        return Date.now().toString(36) + Math.random().toString(36).substr(2);
    }
    
    // Format time from 24h to 12h format
    function formatTime(time24) {
        const [hours, minutes] = time24.split(':');
        const hour = parseInt(hours, 10);
        const period = hour >= 12 ? 'PM' : 'AM';
        const hour12 = hour % 12 || 12;
        return `${hour12}:${minutes} ${period}`;
    }
    
    // Event Listeners
    
    // Open add session modal
    if (addSessionBtn && addSessionModal) {
        addSessionBtn.addEventListener('click', () => {
            addSessionModal.style.display = 'flex';
        });
    }
    
    // Open add session modal from empty state
    if (emptyAddBtn && addSessionModal) {
        emptyAddBtn.addEventListener('click', () => {
            addSessionModal.style.display = 'flex';
        });
    }
    
    // Close add session modal
    if (closeModalBtn && addSessionModal && addSessionForm) {
        closeModalBtn.addEventListener('click', () => {
            addSessionModal.style.display = 'none';
            addSessionForm.reset();
        });
    }
    
    // Cancel add session
    if (cancelAddBtn && addSessionModal && addSessionForm) {
        cancelAddBtn.addEventListener('click', () => {
            addSessionModal.style.display = 'none';
            addSessionForm.reset();
        });
    }
    
    // Submit add session form with validation
    if (addSessionForm) {
        addSessionForm.addEventListener('submit', (e) => {
            // Client-side validation
            let valid = true;
            let errorMessages = [];

            // Get form values
            const title = document.getElementById('sessionTitle').value.trim();
            const subject = document.getElementById('sessionSubject').value;
            const topic = document.getElementById('sessionTopic').value.trim();
            const date = document.getElementById('sessionDate').value;
            const startTime = document.getElementById('sessionStartTime').value;
            const endTime = document.getElementById('sessionEndTime').value;
            const location = document.getElementById('sessionLocation').value.trim();
            const description = document.getElementById('sessionDescription').value.trim();

            // Simple validation rules
            if (!title) { valid = false; errorMessages.push('Title is required.'); }
            if (!subject) { valid = false; errorMessages.push('Subject is required.'); }
            if (!topic) { valid = false; errorMessages.push('Topic is required.'); }
            if (!date) { valid = false; errorMessages.push('Date is required.'); }
            if (!startTime) { valid = false; errorMessages.push('Start time is required.'); }
            if (!endTime) { valid = false; errorMessages.push('End time is required.'); }
            if (!location) { valid = false; errorMessages.push('Location is required.'); }
            // Optionally: check if endTime > startTime
            if (startTime && endTime && startTime >= endTime) {
                valid = false;
                errorMessages.push('End time must be after start time.');
            }

            // Remove any previous error message
            let errorDiv = document.getElementById('sessionFormError');
            if (errorDiv) errorDiv.remove();

            if (!valid) {
                e.preventDefault();
                // Show error messages above the form
                errorDiv = document.createElement('div');
                errorDiv.id = 'sessionFormError';
                errorDiv.style.color = 'red';
                errorDiv.style.marginBottom = '10px';
                errorDiv.innerHTML = errorMessages.join('<br>');
                addSessionForm.prepend(errorDiv);
            }
            // If valid, allow form to submit to server
        });
    }
    
    // Close delete modal
    closeDeleteModalBtn.addEventListener('click', () => {
        deleteModal.style.display = 'none';
        sessionToDelete = null;
    });
    
    // Cancel delete
    cancelDeleteBtn.addEventListener('click', () => {
        deleteModal.style.display = 'none';
        sessionToDelete = null;
    });
    
    // Confirm delete
    confirmDeleteBtn.addEventListener('click', () => {
        if (sessionToDelete) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '/cmsc126-study-session-management-system/public/delete-session';
            
            const actionInput = document.createElement('input');
            actionInput.type = 'hidden';
            actionInput.name = 'action';
            actionInput.value = 'delete-session';
            
            const sessionIdInput = document.createElement('input');
            sessionIdInput.type = 'hidden';
            sessionIdInput.name = 'sessionId';
            sessionIdInput.value = sessionToDelete;
            
            form.appendChild(actionInput);
            form.appendChild(sessionIdInput);
            document.body.appendChild(form);
            form.submit();
        }
    });
    
    // Close modals when clicking outside
    window.addEventListener('click', (e) => {
        if (e.target === addSessionModal) {
            addSessionModal.style.display = 'none';
            addSessionForm.reset();
        }
        if (e.target === deleteModal) {
            deleteModal.style.display = 'none';
            sessionToDelete = null;
        }
    });
    
    // Card menu toggle
    document.addEventListener('click', function(event) {
        if (event.target.closest('.card-menu-btn')) {
            const dropdown = event.target.closest('.card-menu').querySelector('.card-menu-dropdown');
            dropdown.classList.toggle('active');
            event.stopPropagation();
        } else {
            document.querySelectorAll('.card-menu-dropdown').forEach(menu => {
                menu.classList.remove('active');
            });
        }
    });
    
    // Mobile menu toggle
    const menuToggle = document.getElementById('menu-toggle');
    const sidebar = document.querySelector('.sidebar');
    
    menuToggle.addEventListener('click', () => {
        sidebar.classList.toggle('active');
    });
    
    // Dropdown toggle
    document.addEventListener('click', function(event) {
        if (event.target.closest('.dropdown-toggle')) {
            const dropdown = event.target.closest('.dropdown').querySelector('.dropdown-menu');
            dropdown.classList.toggle('active');
            event.stopPropagation();
        } else {
            document.querySelectorAll('.dropdown-menu').forEach(menu => {
                menu.classList.remove('active');
            });
        }
    });
    
    // Initialize the page
    updateEmptyState();
});

function logout() {
    // You can add logic here like clearing session/local storage
    alert("You have been logged out.");
    window.location.href = "login.html"; // redirect to login page
}
