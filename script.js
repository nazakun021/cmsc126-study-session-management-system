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
        if (sessions.length === 0) {
            emptyState.style.display = 'flex';
            sessionsContainer.style.display = 'none';
        } else {
            emptyState.style.display = 'none';
            sessionsContainer.style.display = 'grid';
        }
        
        // Update stats
        updateStats();
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
    addSessionForm.addEventListener('submit', (e) => {
        e.preventDefault();
        
        // Get form values
        const title = document.getElementById('session-title').value;
        const subject = document.getElementById('session-subject').value;
        const date = document.getElementById('session-date').value;
        const startTime = document.getElementById('session-start-time').value;
        const endTime = document.getElementById('session-end-time').value;
        const location = document.getElementById('session-location').value;
        
        // Create new session object
        const newSession = {
            id: generateId(),
            title,
            subject,
            date,
            startTime: formatTime(startTime),
            endTime: formatTime(endTime),
            location,
            status: 'scheduled'
        };
        
        // Add to sessions array
        sessions.push(newSession);
        
        // Render sessions
        renderSessions();
        
        // Close modal and reset form
        addSessionModal.style.display = 'none';
        addSessionForm.reset();
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