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
    addSessionBtn.addEventListener('click', () => {
        addSessionModal.style.display = 'flex';
    });
    
    // Open add session modal from empty state
    emptyAddBtn.addEventListener('click', () => {
        addSessionModal.style.display = 'flex';
    });
    
    // Close add session modal
    closeModalBtn.addEventListener('click', () => {
        addSessionModal.style.display = 'none';
        addSessionForm.reset();
    });
    
    // Cancel add session
    cancelAddBtn.addEventListener('click', () => {
        addSessionModal.style.display = 'none';
        addSessionForm.reset();
    });
    
    // Submit add session form
    addSessionForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        try {
            const formData = new FormData(e.target);
            
            // Add CSRF token if available (assuming it's stored in a meta tag)
            const csrfTokenMeta = document.querySelector('meta[name="csrf-token"]');
            if (csrfTokenMeta) {
                formData.append('csrf_token', csrfTokenMeta.content);
            }

            // Log form data for debugging
            console.log('Submitting form data from review-sessions.js:', Object.fromEntries(formData));

            const dateInput = e.target.querySelector('[name="reviewDate"]');
            if (dateInput) {
                // Convert DD/MM/YYYY to YYYY-MM-DD if needed for backend consistency
                let value = dateInput.value;
                if (/^\d{2}\/\d{2}\/\d{4}$/.test(value)) {
                    const [day, month, year] = value.split('/');
                    dateInput.value = `${year}-${month}-${day}`;
                    formData.set('reviewDate', `${year}-${month}-${day}`); // Update formData as well
                } else if (/^\d{4}-\d{2}-\d{2}$/.test(value)) {
                    // Ensure the formData has the correct YYYY-MM-DD format if already in that format
                    formData.set('reviewDate', value);
                }
            }

            const response = await fetch('/cmsc126-study-session-management-system/public/create-session', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();
            console.log('Server response from review-sessions.js:', result);
            
            if (result.success) {
                // Assuming Toast is globally available or loaded via utils.js
                if (typeof Toast !== 'undefined') {
                    Toast.show('Session created successfully', 'success');
                } else {
                    alert('Session created successfully'); // Fallback
                }
                addSessionModal.style.display = 'none';
                addSessionForm.reset();
                // Reload sessions or update UI dynamically
                // For simplicity, we'll reload the page. A more sophisticated approach would be to fetch and render just the new session.
                window.location.reload(); 
            } else {
                let errorMessage = 'Failed to create session.';
                if (result.message) {
                    errorMessage = result.message;
                }
                if (result.errors && result.errors.length > 0) {
                    errorMessage = result.errors.join('\n');
                }
                if (typeof Toast !== 'undefined') {
                    Toast.show(errorMessage, 'error');
                } else {
                    alert(errorMessage); // Fallback
                }
            }
        } catch (error) {
            console.error('Error creating session from review-sessions.js:', error);
            if (typeof Toast !== 'undefined') {
                Toast.show('An unexpected error occurred. Please try again.', 'error');
            } else {
                alert('An unexpected error occurred. Please try again.'); // Fallback
            }
        }
    });
    
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
            // Remove from array
            sessions = sessions.filter(session => session.id !== sessionToDelete);
            
            // Render sessions
            renderSessions();
            
            // Close modal
            deleteModal.style.display = 'none';
            sessionToDelete = null;
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
    
    // Sidebar filter toggle
    const filterToggle = document.getElementById('filter-toggle');
    const filterPanel = document.getElementById('sidebar-filter-panel');
    if (filterToggle && filterPanel) {
        filterToggle.addEventListener('click', function() {
            filterPanel.style.display = filterPanel.style.display === 'none' ? 'block' : 'none';
        });
    }
    // Filter form logic (let the form submit to backend)
    // Remove JS filtering and let PHP handle it
    // Optionally, auto-show filter panel if filters are active
    const filterForm = document.getElementById('sidebar-filter-form');
    const clearFilterBtn = document.getElementById('clear-filter');
    if (filterForm) {
        // No JS submit handler needed
    }
    if (clearFilterBtn) {
        // No JS clear handler needed; use <a> link in PHP
    }
    // Auto-show filter panel if filters are active
    const urlParams = new URLSearchParams(window.location.search);
    if ((urlParams.get('subjectID') && urlParams.get('subjectID') !== '') || (urlParams.get('reviewDate') && urlParams.get('reviewDate') !== '')) {
        if (filterPanel) filterPanel.style.display = 'block';
    }
    
    // Initialize the page
    updateEmptyState();
});