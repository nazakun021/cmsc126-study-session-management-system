document.addEventListener('DOMContentLoaded', function() {
    // Initialize Feather icons
    feather.replace();
    
    // DOM elements
    const addSessionBtn = document.getElementById('add-session-btn');
    const emptyAddBtn = document.getElementById('empty-add-btn');
    const addSessionModal = document.getElementById('add-session-modal');
    const closeModalBtn = document.getElementById('close-modal');
    const cancelAddBtn = document.getElementById('cancel-add');
    const addSessionForm = document.getElementById('add-session-form');
    const sessionsContainer = document.getElementById('sessions-container');
    const sessionsList = document.getElementById('sessions-list');
    const emptyState = document.getElementById('empty-state');
    const deleteModal = document.getElementById('delete-modal');
    const closeDeleteModalBtn = document.getElementById('close-delete-modal');
    const cancelDeleteBtn = document.getElementById('cancel-delete');
    const confirmDeleteBtn = document.getElementById('confirm-delete');
    
    // Session data array (empty initially)
    let sessions = [];
    let sessionToDelete = null;
    
    // Show/hide empty state based on sessions
    function updateEmptyState() {
        if (!emptyState || !sessionsContainer) return;
        if (sessions.length === 0) {
            emptyState.style.display = 'flex';
            sessionsContainer.style.display = 'none';
        } else {
            emptyState.style.display = 'none';
            sessionsContainer.style.display = 'block';
        }
    }
    
    // Render all sessions
    function renderSessions() {
        // Clear existing sessions
        sessionsList.innerHTML = '';
        
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
        const template = document.getElementById('session-template');
        const clone = document.importNode(template.content, true);
        
        // Set session data
        clone.querySelector('.session-title').textContent = session.title;
        clone.querySelector('.session-subject').textContent = session.subject;
        clone.querySelector('.session-date').textContent = formatDate(session.date);
        clone.querySelector('.session-time').textContent = `${session.startTime} - ${session.endTime}`;
        clone.querySelector('.session-location').textContent = session.location;
        
        // Set data attribute for identification
        const sessionItem = clone.querySelector('.session-item');
        sessionItem.dataset.id = session.id;
        
        // Add event listeners for actions
        const deleteBtn = clone.querySelector('.delete-session');
        deleteBtn.addEventListener('click', () => {
            sessionToDelete = session.id;
            deleteModal.style.display = 'flex';
        });
        
        sessionsList.appendChild(clone);
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
    if (addSessionBtn) {
        addSessionBtn.addEventListener('click', () => {
            if (addSessionModal) addSessionModal.style.display = 'flex';
        });
    }
    
    // Open add session modal from empty state
    if (emptyAddBtn) {
        emptyAddBtn.addEventListener('click', () => {
            if (addSessionModal) addSessionModal.style.display = 'flex';
        });
    }
    
    // Close add session modal
    if (closeModalBtn) {
        closeModalBtn.addEventListener('click', () => {
            if (addSessionModal) addSessionModal.style.display = 'none';
            if (addSessionForm) addSessionForm.reset();
        });
    }
    
    // Cancel add session
    if (cancelAddBtn) {
        cancelAddBtn.addEventListener('click', () => {
            if (addSessionModal) addSessionModal.style.display = 'none';
            if (addSessionForm) addSessionForm.reset();
        });
    }
    
    // Submit add session form
    addSessionForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        try {
            // Get form values
            const formData = new FormData(addSessionForm);
            
            // Add creator user ID from session
            const userId = document.querySelector('meta[name="user-id"]')?.content;
            if (!userId) {
                throw new Error('User ID not found. Please log in again.');
            }
            formData.append('creatorUserID', userId);
            
            // Send to backend
            const response = await fetch('/api/sessions', {
                method: 'POST',
                body: formData
            });
            
            const result = await response.json();
            
            if (result.success) {
                // Show success message
                const toast = document.createElement('div');
                toast.className = 'toast success';
                toast.textContent = 'Session created successfully!';
                document.body.appendChild(toast);
                setTimeout(() => toast.remove(), 3000);
                
                // Close modal and reset form
                addSessionModal.style.display = 'none';
                addSessionForm.reset();
                
                // Refresh sessions list
                loadSessions();
            } else {
                // Show error message
                const errorDiv = document.createElement('div');
                errorDiv.className = 'form-error';
                errorDiv.textContent = result.error || 'Failed to create session';
                addSessionForm.prepend(errorDiv);
                setTimeout(() => errorDiv.remove(), 5000);
            }
        } catch (error) {
            console.error('Error creating session:', error);
            const errorDiv = document.createElement('div');
            errorDiv.className = 'form-error';
            errorDiv.textContent = 'An error occurred. Please try again.';
            addSessionForm.prepend(errorDiv);
            setTimeout(() => errorDiv.remove(), 5000);
        }
    });
    
    // Close delete modal
    if (closeDeleteModalBtn) {
        closeDeleteModalBtn.addEventListener('click', () => {
            if (deleteModal) deleteModal.style.display = 'none';
            sessionToDelete = null;
        });
    }
    
    // Cancel delete
    if (cancelDeleteBtn) {
        cancelDeleteBtn.addEventListener('click', () => {
            if (deleteModal) deleteModal.style.display = 'none';
            sessionToDelete = null;
        });
    }
    
    // Confirm delete
    if (confirmDeleteBtn) {
        confirmDeleteBtn.addEventListener('click', () => {
            if (sessionToDelete) {
                sessions = sessions.filter(session => session.id !== sessionToDelete);
                renderSessions();
                if (deleteModal) deleteModal.style.display = 'none';
                sessionToDelete = null;
            }
        });
    }
    
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
    
    // Mobile menu toggle
    const menuToggle = document.getElementById('menu-toggle');
    const sidebar = document.querySelector('.sidebar');
    
    if (menuToggle && sidebar) {
        menuToggle.addEventListener('click', () => {
            sidebar.classList.toggle('active');
        });
    }
    
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
    
    // Load sessions from backend
    async function loadSessions() {
        try {
            const response = await fetch('/api/sessions');
            const result = await response.json();
            
            if (result.success) {
                sessions = result.sessions;
                renderSessions();
            } else {
                console.error('Failed to load sessions:', result.error);
                const errorDiv = document.createElement('div');
                errorDiv.className = 'error-message';
                errorDiv.textContent = 'Failed to load sessions. Please try again.';
                sessionsContainer.prepend(errorDiv);
                setTimeout(() => errorDiv.remove(), 5000);
            }
        } catch (error) {
            console.error('Error loading sessions:', error);
            const errorDiv = document.createElement('div');
            errorDiv.className = 'error-message';
            errorDiv.textContent = 'An error occurred while loading sessions.';
            sessionsContainer.prepend(errorDiv);
            setTimeout(() => errorDiv.remove(), 5000);
        }
    }

    // Initialize the page
    document.addEventListener('DOMContentLoaded', () => {
        loadSessions();
        updateEmptyState();
    });
});