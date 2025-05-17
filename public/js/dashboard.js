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

    // Sidebar filter toggle
    const filterToggleBtn = document.getElementById('filter-toggle');
    const sidebarFilterPanel = document.getElementById('sidebar-filter-panel');

    if (filterToggleBtn && sidebarFilterPanel) {
        filterToggleBtn.addEventListener('click', () => {
            const isPanelVisible = sidebarFilterPanel.style.display === 'block';
            sidebarFilterPanel.style.display = isPanelVisible ? 'none' : 'block';
            filterToggleBtn.classList.toggle('active', !isPanelVisible);
            console.log(`Dashboard filter panel toggled. Visible: ${!isPanelVisible}`);
        });
    }

    // Add Session Modal (if it exists on this page, similar to review-sessions.js)
    const addSessionBtn = document.getElementById('add-session-btn');
    const emptyAddBtn = document.getElementById('empty-add-btn'); // If dashboard has its own empty state add button
    const addSessionModal = document.getElementById('add-session-modal'); // Assuming a shared or similar modal structure
    const closeModalButton = addSessionModal ? addSessionModal.querySelector('.close-btn') : null; // General close button
    const cancelAddButton = addSessionModal ? addSessionModal.querySelector('#cancel-add') : null; // Specific cancel button
    const addSessionForm = document.getElementById('add-session-form'); // Assuming shared form ID

    const openAddModal = () => {
        if (addSessionModal) {
            addSessionModal.style.display = 'flex';
        }
    };

    const closeAddModal = () => {
        if (addSessionModal) {
            addSessionModal.style.display = 'none';
            if (addSessionForm) addSessionForm.reset();
        }
    };

    if (addSessionBtn) {
        addSessionBtn.addEventListener('click', openAddModal);
    }
    if (emptyAddBtn && emptyAddBtn.tagName === 'BUTTON') { // Ensure it's the button, not the div
        const actualButton = emptyAddBtn.querySelector('.btn-primary') || emptyAddBtn;
        actualButton.addEventListener('click', openAddModal);
    }

    if (closeModalButton) {
        closeModalButton.addEventListener('click', closeAddModal);
    }
    if (cancelAddButton) {
        cancelAddButton.addEventListener('click', closeAddModal);
    }

    if (addSessionForm) {
        addSessionForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(e.target);
            const csrfTokenMeta = document.querySelector('meta[name="csrf-token"]');
            if (csrfTokenMeta) {
                formData.append('csrf_token', csrfTokenMeta.content);
            }
            
            // Add any specific dashboard related logic if needed before submission
            console.log('Submitting ADD form data from dashboard.js:', Object.fromEntries(formData));

            try {
                const response = await fetch('/cmsc126-study-session-management-system/public/create-session', {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();
                if (result.success) {
                    (typeof Toast !== 'undefined' ? Toast.show('Session created successfully', 'success') : alert('Session created successfully'));
                    closeAddModal();
                    window.location.reload(); // Reload to see the new session
                } else {
                    let errorMessage = result.message || (result.errors && result.errors.length > 0 ? result.errors.join('\n') : 'Failed to create session.');
                    (typeof Toast !== 'undefined' ? Toast.show(errorMessage, 'error') : alert(errorMessage));
                }
            } catch (error) {
                console.error('Error creating session from dashboard:', error);
                (typeof Toast !== 'undefined' ? Toast.show('An unexpected error occurred.', 'error') : alert('An unexpected error occurred.'));
            }
        });
    }

    // Placeholder for client-side search on dashboard (if desired in addition to server-side filtering)
    const sessionSearchInput = document.getElementById('session-search');
    if (sessionSearchInput) {
        sessionSearchInput.addEventListener('input', (e) => {
            const searchTerm = e.target.value.toLowerCase();
            const sessionCards = document.querySelectorAll('#dashboard-session-list .session-card'); // Target cards in dashboard
            sessionCards.forEach(card => {
                const titleElement = card.querySelector('.card-title');
                const title = titleElement ? titleElement.textContent.toLowerCase() : '';
                // Add more fields to search if needed (e.g., subject, topic from card content)
                const subjectElement = card.querySelector('.card-info-item span'); // Example, adjust selector
                const subject = subjectElement ? subjectElement.textContent.toLowerCase() : '';

                if (title.includes(searchTerm) || subject.includes(searchTerm)) {
                    card.style.display = ''; // Or 'block', 'flex' etc. depending on card styling
                } else {
                    card.style.display = 'none';
                }
            });
        });
    }

    // --- Delete Modal Elements ---
    const deleteModal = document.getElementById('delete-modal');
    const closeDeleteModalButton = deleteModal ? deleteModal.querySelector('#close-delete-modal') : null;
    const cancelDeleteButton = deleteModal ? deleteModal.querySelector('#cancel-delete') : null;
    const confirmDeleteButton = deleteModal ? deleteModal.querySelector('#confirm-delete') : null;
    let sessionToDeleteId = null;

    // --- Function to open Delete Confirmation Modal ---
    const openDeleteConfirmationModal = (sessionId) => {
        if (deleteModal) {
            console.log(`Dashboard: Opening delete confirmation modal for session ID: ${sessionId}`);
            sessionToDeleteId = sessionId;
            deleteModal.style.display = 'flex';
        } else {
            console.error('Dashboard: Delete Modal (delete-modal) not found when trying to open.');
        }
    };

    // --- Function to close Delete Confirmation Modal ---
    const closeDeleteConfirmationModal = () => {
        if (deleteModal) {
            console.log('Dashboard: Closing delete confirmation modal');
            deleteModal.style.display = 'none';
        }
        sessionToDeleteId = null;
    };

    // --- Event Listeners for Delete Modal ---
    if (closeDeleteModalButton) {
        closeDeleteModalButton.addEventListener('click', closeDeleteConfirmationModal);
    }
    if (cancelDeleteButton) {
        cancelDeleteButton.addEventListener('click', closeDeleteConfirmationModal);
    }

    if (confirmDeleteButton) {
        confirmDeleteButton.addEventListener('click', async (event) => {
            event.preventDefault();
            event.stopImmediatePropagation();
            console.log('Dashboard: Confirm delete button clicked.');

            if (sessionToDeleteId) {
                const idToDelete = sessionToDeleteId;
                console.log('Dashboard: Attempting to delete session ID:', idToDelete);
                try {
                    const csrfTokenMeta = document.querySelector('meta[name="csrf-token"]');
                    const formData = new FormData();
                    formData.append('reviewSessionID', idToDelete);

                    if (csrfTokenMeta && csrfTokenMeta.content) {
                        formData.append('csrf_token', csrfTokenMeta.content);
                    } else {
                        console.warn('Dashboard: CSRF token meta tag not found or empty for delete action.');
                        (typeof Toast !== 'undefined' ? Toast.show('CSRF token missing. Cannot submit delete.', 'error') : alert('CSRF token missing. Cannot submit delete.'));
                        return;
                    }

                    const response = await fetch('/cmsc126-study-session-management-system/public/delete-session.php', {
                        method: 'POST',
                        body: formData
                    });

                    const result = await response.json();
                    console.log('Dashboard: Server JSON response from DELETE action:', result);

                    if (result.success) {
                        (typeof Toast !== 'undefined' ? Toast.show(result.message || 'Session deleted successfully!', 'success') : alert(result.message || 'Session deleted successfully!'));
                        closeDeleteConfirmationModal();
                        // Reload the page to reflect changes, or dynamically remove the card
                        window.location.reload(); 
                    } else {
                        let errorMessage = result.message || 'Failed to delete session.';
                        (typeof Toast !== 'undefined' ? Toast.show(errorMessage, 'error') : alert(errorMessage));
                    }
                } catch (error) {
                    console.error(`Dashboard: Error deleting session ${idToDelete}:`, error);
                    (typeof Toast !== 'undefined' ? Toast.show('An unexpected error occurred while deleting.', 'error') : alert('An unexpected error occurred.'));
                }
            } else {
                console.warn('Dashboard: sessionToDeleteId is null when confirm delete was clicked.');
            }
        });
    }

    // Attach event listeners to delete buttons on session cards
    document.querySelectorAll('#dashboard-session-list .delete-session-btn').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation(); // Stop propagation to prevent card menu from closing if event bubbles up
            const sessionId = this.dataset.sessionId;
            console.log('Dashboard: Delete button clicked for session ID:', sessionId);
            if (sessionId) {
                openDeleteConfirmationModal(sessionId);
            } else {
                console.error('Dashboard: Session ID not found on delete button', this);
            }
        });
    });

    // --- Edit Session Modal Elements (assuming a shared or similar modal structure to review-sessions.php) ---
    const editSessionModal = document.getElementById('edit-session-modal'); // Ensure this modal exists in dashboard.php
    const editSessionForm = document.getElementById('edit-session-form'); // Ensure this form ID is used in the modal
    const closeEditModalButton = editSessionModal ? editSessionModal.querySelector('.close-btn') : null; // General close button for edit
    const cancelEditButton = editSessionModal ? editSessionModal.querySelector('#cancel-edit-session') : null; // Specific cancel button for edit

    // --- Function to open Edit Session Modal ---
    const openEditModal = (sessionData) => {
        if (editSessionModal && editSessionForm) {
            console.log('Dashboard: Opening edit session modal with data:', sessionData);
            // Populate form fields
            editSessionForm.querySelector('#edit-session-id').value = sessionData.reviewSessionID;
            editSessionForm.querySelector('#edit-session-title').value = sessionData.title;
            editSessionForm.querySelector('#edit-session-subject').value = sessionData.subjectId;
            editSessionForm.querySelector('#edit-session-topic').value = sessionData.topic;
            editSessionForm.querySelector('#edit-session-date').value = sessionData.date;
            editSessionForm.querySelector('#edit-session-start-time').value = sessionData.startTime;
            editSessionForm.querySelector('#edit-session-end-time').value = sessionData.endTime;
            editSessionForm.querySelector('#edit-session-location').value = sessionData.location;
            editSessionForm.querySelector('#edit-session-description').value = sessionData.description;

            editSessionModal.style.display = 'flex';
        } else {
            if (!editSessionModal) console.error('Dashboard: Edit Session Modal (edit-session-modal) not found.');
            if (!editSessionForm) console.error('Dashboard: Edit Session Form (edit-session-form) not found.');
            alert('Edit modal or form is not available. Please check the page structure.');
        }
    };

    // --- Function to close Edit Session Modal ---
    const closeEditModal = () => {
        if (editSessionModal) {
            console.log('Dashboard: Closing edit session modal');
            editSessionModal.style.display = 'none';
            if (editSessionForm) {
                editSessionForm.reset();
            }
        }
    };

    // --- Event Listeners for Edit Modal ---
    if (closeEditModalButton) {
        closeEditModalButton.addEventListener('click', closeEditModal);
    }
    if (cancelEditButton) {
        cancelEditButton.addEventListener('click', closeEditModal);
    }

    // Attach event listeners to edit buttons on session cards
    document.querySelectorAll('#dashboard-session-list .edit-session-btn').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            const card = this.closest('.session-card');
            if (card) {
                const sessionData = {
                    reviewSessionID: card.dataset.sessionId,
                    title: card.dataset.title,
                    subjectId: card.dataset.subjectId,
                    topic: card.dataset.topic,
                    date: card.dataset.date,
                    startTime: card.dataset.startTime,
                    endTime: card.dataset.endTime,
                    location: card.dataset.location,
                    description: card.dataset.description
                };
                console.log('Dashboard: Edit button clicked for session ID:', sessionData.reviewSessionID, 'Data:', sessionData);
                openEditModal(sessionData);
            } else {
                console.error('Dashboard: Could not find parent .session-card for edit button', this);
            }
        });
    });

    // --- Edit Session Form Submission ---
    if (editSessionForm) {
        editSessionForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            e.stopImmediatePropagation();
            console.log('Dashboard: Edit session form submission initiated.');

            const sessionIdField = editSessionForm.querySelector('#edit-session-id');
            if (!sessionIdField || !sessionIdField.value) {
                console.error('Dashboard: Session ID field missing or empty in edit form.');
                (typeof Toast !== 'undefined' ? Toast.show('Critical error: Session ID missing.', 'error') : alert('Critical error: Session ID missing.'));
                return;
            }
            const sessionId = sessionIdField.value;

            try {
                const formData = new FormData(e.target);
                const csrfTokenMeta = document.querySelector('meta[name="csrf-token"]');

                if (csrfTokenMeta && csrfTokenMeta.content) {
                    formData.append('csrf_token', csrfTokenMeta.content);
                } else {
                    console.warn('Dashboard: CSRF token meta tag not found or empty for edit form.');
                    (typeof Toast !== 'undefined' ? Toast.show('CSRF token missing. Cannot submit.', 'error') : alert('CSRF token missing. Cannot submit.'));
                    return;
                }
                
                // Ensure reviewSessionID is on formData if not already from a hidden input with that name
                if (!formData.has('reviewSessionID')) {
                    formData.append('reviewSessionID', sessionId);
                }

                console.log(`Dashboard: Submitting EDIT form data for session ID ${sessionId}:`, Object.fromEntries(formData));

                const response = await fetch('/cmsc126-study-session-management-system/public/update-session.php', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();
                console.log('Dashboard: Server JSON response from EDIT form submission:', result);

                if (result.success) {
                    (typeof Toast !== 'undefined' ? Toast.show(result.message || 'Session updated successfully!', 'success') : alert(result.message || 'Session updated successfully!'));
                    closeEditModal();
                    // Reload the page to reflect changes. Dynamic update is complex with server-side filtering.
                    window.location.reload(); 
                } else {
                    let errorMessage = result.message || (result.errors && result.errors.length > 0 ? result.errors.join('\\n') : 'Failed to update session.');
                    (typeof Toast !== 'undefined' ? Toast.show(errorMessage, 'error') : alert(errorMessage));
                }
            } catch (error) {
                console.error(`Dashboard: Error updating session ${sessionId}:`, error);
                (typeof Toast !== 'undefined' ? Toast.show('An unexpected error occurred while updating. Check console.', 'error') : alert('An unexpected error occurred. Check console.'));
            }
        });
    } else {
        // This might be normal if the edit modal is not part of dashboard.php or has different IDs.
        // console.warn('Dashboard: Edit Session Form (edit-session-form) not found. Edit functionality might not be available on this page or uses different IDs.');
    }

}); // End of DOMContentLoaded

// Removed loadDashboardData, updateStats, renderSessions, populateSessionCard, setupCardEventListeners,
// handleDeleteSession, handleEditSession, setupEventListeners, handleAddSession, handleUpdateSession
// as the primary data loading and filtering is now server-side with page reloads.
// The remaining JS focuses on client-side interactions like modal toggles and potential live search.
