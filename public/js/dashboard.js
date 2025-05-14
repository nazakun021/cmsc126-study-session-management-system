// Dashboard functionality
console.log('Dashboard.js loaded successfully');

// Ensure DOM is fully loaded before executing scripts
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Feather icons
    if (typeof feather !== 'undefined') {
        feather.replace();
    } else {
        console.warn('Feather icons library is not loaded.');
    }

    // Load initial data
    loadDashboardData();

    // Add event listeners
    setupEventListeners();
});

async function loadDashboardData() {
    try {
        const response = await fetch('/cmsc126-study-session-management-system/public/api/dashboard/stats');
        if (!response.ok) {
            throw new Error('Failed to fetch dashboard data');
        }
        
        const data = await response.json();
        if (data) {
            updateStats(data);
            renderSessions(data.sessions);
        }
    } catch (error) {
        console.error('Error loading dashboard data:', error);
        Toast.show('Failed to load dashboard data', 'error');
    }
}

function updateStats(data) {
    const totalSessions = document.getElementById('total-sessions');
    const upcomingSessions = document.getElementById('upcoming-sessions');
    const totalSubjects = document.getElementById('total-subjects');
    const avgAttendance = document.getElementById('avg-attendance');

    if (totalSessions) totalSessions.textContent = data.totalSessions;
    if (upcomingSessions) upcomingSessions.textContent = data.upcomingSessions;
    if (totalSubjects) totalSubjects.textContent = data.totalSubjects;
    if (avgAttendance) avgAttendance.textContent = data.avgAttendance + '%';
}

function renderSessions(sessions) {
    const container = document.getElementById('sessions-container');
    const emptyState = document.getElementById('empty-add-btn');

    if (!container || !emptyState) {
        console.error('Session container or empty state element is missing.');
        return;
    }

    if (!sessions || sessions.length === 0) {
        container.style.display = 'none';
        emptyState.style.display = 'flex';
        return;
    }

    container.style.display = 'block';
    emptyState.style.display = 'none';

    const template = document.getElementById('session-card-template');
    if (!template) {
        console.error('Session card template is missing.');
        return;
    }

    container.innerHTML = '';

    sessions.forEach(session => {
        const clone = document.importNode(template.content, true);
        populateSessionCard(clone, session);
        container.appendChild(clone);
    });

    // Reinitialize Feather icons
    feather.replace();
}

function populateSessionCard(clone, session) {
    clone.querySelector('.card-title').textContent = session.reviewTitle;
    clone.querySelector('.card-subject').textContent = session.subjectName;
    clone.querySelector('.card-date').textContent = formatDate(session.reviewDate);
    clone.querySelector('.card-time').textContent = `${session.reviewStartTime} - ${session.reviewEndTime}`;
    clone.querySelector('.card-location').textContent = session.reviewLocation;

    const card = clone.querySelector('.session-card');
    card.dataset.id = session.id;

    setupCardEventListeners(card, session);
}

function setupCardEventListeners(card, session) {
    const deleteBtn = card.querySelector('.delete-session');
    deleteBtn.addEventListener('click', () => handleDeleteSession(session.id));

    const editBtn = card.querySelector('.edit-session');
    editBtn.addEventListener('click', () => handleEditSession(session));
}

async function handleDeleteSession(sessionId) {
    try {
        await Ajax.request('/api/sessions/' + sessionId, {
            method: 'DELETE'
        });
        Toast.show('Session deleted successfully', 'success');
        loadDashboardData();
    } catch (error) {
        ErrorHandler.handle(error);
    }
}

function handleEditSession(session) {
    // Populate and show edit modal
    const modal = document.getElementById('edit-session-modal');
    const form = modal.querySelector('form');

    form.querySelector('[name="sessionId"]').value = session.id;
    form.querySelector('[name="title"]').value = session.title;
    form.querySelector('[name="subject"]').value = session.subject;
    form.querySelector('[name="date"]').value = session.date;
    form.querySelector('[name="startTime"]').value = session.startTime;
    form.querySelector('[name="endTime"]').value = session.endTime;
    form.querySelector('[name="location"]').value = session.location;

    modal.style.display = 'flex';
}

function setupEventListeners() {
    // Add session button
    const addSessionBtn = document.getElementById('add-session-btn');
    addSessionBtn.addEventListener('click', () => {
        document.getElementById('add-session-modal').style.display = 'flex';
    });

    // Form submissions
    const addSessionForm = document.getElementById('addSessionForm');
    addSessionForm.addEventListener('submit', handleAddSession);

    const editSessionForm = document.getElementById('editSessionForm');
    editSessionForm.addEventListener('submit', handleUpdateSession);

    // Modal close buttons
    document.querySelectorAll('.close-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            btn.closest('.modal').style.display = 'none';
        });
    });

    // Add Session Modal Logic
    const emptyAddBtn = document.getElementById('empty-add-btn');
    const addSessionModal = document.getElementById('add-session-modal');
    const closeModalBtn = document.getElementById('close-modal');
    const cancelAddBtn = document.getElementById('cancel-add');

    // Open the Add Session Modal
    const openAddSessionModal = () => {
        addSessionModal.style.display = 'flex';
    };

    addSessionBtn.addEventListener('click', openAddSessionModal);
    emptyAddBtn.addEventListener('click', openAddSessionModal);

    // Close the Add Session Modal
    const closeAddSessionModal = () => {
        addSessionModal.style.display = 'none';
    };

    closeModalBtn.addEventListener('click', closeAddSessionModal);
    cancelAddBtn.addEventListener('click', closeAddSessionModal);

    // Close modal when clicking outside the modal content
    window.addEventListener('click', function (event) {
        if (event.target === addSessionModal) {
            closeAddSessionModal();
        }
    });
}

async function handleAddSession(e) {
    e.preventDefault();

    try {
        const formData = new FormData(e.target);
        
        // Add CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
        formData.append('csrf_token', csrfToken);

        const response = await fetch('/cmsc126-study-session-management-system/public/create-session', {
            method: 'POST',
            body: formData
        });

        if (!response.ok) {
            throw new Error('Failed to create session');
        }

        const result = await response.json();
        
        if (result.success) {
            Toast.show('Session created successfully', 'success');
            e.target.closest('.modal').style.display = 'none';
            e.target.reset();
            loadDashboardData(); // Reload the dashboard data
        } else {
            throw new Error(result.message || 'Failed to create session');
        }
    } catch (error) {
        console.error('Error creating session:', error);
        Toast.show(error.message || 'Failed to create session', 'error');
    }
}

async function handleUpdateSession(e) {
    e.preventDefault();

    try {
        const errors = FormValidator.validate(e.target);
        if (errors.length > 0) {
            const errorDiv = FormValidator.showErrors(errors);
            e.target.prepend(errorDiv);
            return;
        }

        const formData = new FormData(e.target);
        const sessionId = formData.get('sessionId');

        await Ajax.request(`/api/sessions/${sessionId}`, {
            method: 'PUT',
            body: formData
        });

        Toast.show('Session updated successfully', 'success');
        e.target.closest('.modal').style.display = 'none';
        loadDashboardData();
    } catch (error) {
        ErrorHandler.handle(error);
    }
}

function formatDate(dateString) {
    const options = { year: 'numeric', month: 'long', day: 'numeric' };
    return new Date(dateString).toLocaleDateString(undefined, options);
}
