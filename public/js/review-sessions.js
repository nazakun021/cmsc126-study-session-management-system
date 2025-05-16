document.addEventListener('DOMContentLoaded', function() {
    console.log('review-sessions.js loaded and DOMContentLoaded.');

    if (typeof feather !== 'undefined') {
        feather.replace();
    } else {
        console.warn('Feather icons library is not loaded.');
    }

    // --- Add Session Modal Elements ---
    const addSessionModal = document.getElementById('add-session-modal');
    const addSessionForm = document.getElementById('add-session-form');
    const addSessionBtn = document.getElementById('add-session-btn');
    const emptyAddBtn = document.getElementById('empty-add-btn'); // For when the list is empty
    const closeModalButton = document.getElementById('close-modal'); // 'X' button in add-session-modal
    const cancelAddButton = document.getElementById('cancel-add');   // 'Cancel' button in add-session-modal

    // --- Delete Modal Elements ---
    const deleteModal = document.getElementById('delete-modal');
    const closeDeleteModalButton = document.getElementById('close-delete-modal');
    const cancelDeleteButton = document.getElementById('cancel-delete');
    const confirmDeleteButton = document.getElementById('confirm-delete');
    let sessionToDeleteId = null;

    // --- Logging to verify elements ---
    console.log({
        addSessionModal,
        addSessionForm,
        addSessionBtn,
        emptyAddBtn,
        closeModalButton,
        cancelAddButton,
        deleteModal,
        closeDeleteModalButton,
        cancelDeleteButton,
        confirmDeleteButton
    });

    // --- Function to open Add Session Modal ---
    const openAddModal = () => {
        if (addSessionModal) {
            console.log('Opening add session modal');
            addSessionModal.style.display = 'flex';
        } else {
            console.error('Add Session Modal (add-session-modal) not found when trying to open.');
        }
    };

    // --- Function to close Add Session Modal ---
    const closeAddModal = () => {
        if (addSessionModal) {
            console.log('Closing add session modal');
            addSessionModal.style.display = 'none';
            if (addSessionForm) {
                addSessionForm.reset();
                console.log('Add session form reset.');
            }
        } else {
            console.error('Add Session Modal (add-session-modal) not found when trying to close.');
        }
    };

    // --- Event Listeners for Add Session Modal ---
    if (addSessionBtn) {
        addSessionBtn.addEventListener('click', openAddModal);
    } else {
        console.warn('Add Session Button (add-session-btn) not found.');
    }

    if (emptyAddBtn) { // This button appears when the list of sessions is empty
        emptyAddBtn.addEventListener('click', openAddModal);
    } else {
        console.warn('Empty Add Button (empty-add-btn) not found. This might be normal if sessions exist.');
    }

    if (closeModalButton) {
        closeModalButton.addEventListener('click', closeAddModal);
    } else {
        console.error('Close Modal Button (close-modal) for Add Modal not found!');
    }

    if (cancelAddButton) {
        cancelAddButton.addEventListener('click', closeAddModal);
    } else {
        console.error('Cancel Add Button (cancel-add) for Add Modal not found!');
    }

    // --- Add Session Form Submission ---
    if (addSessionForm) {
        addSessionForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            console.log('Add session form submitted.');
            try {
                const formData = new FormData(e.target);
                const csrfTokenMeta = document.querySelector('meta[name="csrf-token"]');
                if (csrfTokenMeta) {
                    formData.append('csrf_token', csrfTokenMeta.content);
                } else {
                    console.warn('CSRF token meta tag not found.');
                }

                console.log('Submitting form data from review-sessions.js:', Object.fromEntries(formData));

                // Date formatting (ensure YYYY-MM-DD)
                const dateInput = e.target.querySelector('[name="reviewDate"]');
                if (dateInput) {
                    let value = dateInput.value;
                    const dateRegexDDMMYYYY = /^\d{2}\/\d{2}\/\d{4}$/; // DD/MM/YYYY
                    const dateRegexYYYYMMDD = /^\d{4}-\d{2}-\d{2}$/; // YYYY-MM-DD (standard HTML5 date input)

                    if (dateRegexDDMMYYYY.test(value)) {
                        const [day, month, year] = value.split('/');
                        formData.set('reviewDate', `${year}-${month}-${day}`);
                        console.log('Converted date from DD/MM/YYYY to YYYY-MM-DD');
                    } else if (dateRegexYYYYMMDD.test(value)) {
                        // Already in correct format, no change needed, but good to log
                        console.log('Date is already in YYYY-MM-DD format.');
                    } else if (value) { // If value exists but doesn't match known formats
                        console.warn('Date format is not recognized for reviewDate:', value);
                    }
                }

                const response = await fetch('/cmsc126-study-session-management-system/public/create-session', {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();
                console.log('Server response from review-sessions.js:', result);

                if (result.success) {
                    (typeof Toast !== 'undefined' ? Toast.show('Session created successfully', 'success') : alert('Session created successfully'));
                    closeAddModal();
                    window.location.reload();
                } else {
                    let errorMessage = result.message || (result.errors && result.errors.length > 0 ? result.errors.join('\n') : 'Failed to create session.');
                    (typeof Toast !== 'undefined' ? Toast.show(errorMessage, 'error') : alert(errorMessage));
                }
            } catch (error) {
                console.error('Error creating session from review-sessions.js:', error);
                (typeof Toast !== 'undefined' ? Toast.show('An unexpected error occurred. Please try again.', 'error') : alert('An unexpected error occurred. Please try again.'));
            }
        });
    } else {
        console.error('Add Session Form (add-session-form) not found!');
    }

    // --- Delete Confirmation Modal Logic ---
    const openDeleteConfirmationModal = (sessionId) => {
        if (deleteModal) {
            console.log(`Opening delete confirmation modal for session ID: ${sessionId}`);
            sessionToDeleteId = sessionId;
            deleteModal.style.display = 'flex';
        } else {
            console.error('Delete Modal (delete-modal) not found when trying to open.');
        }
    };

    const closeDeleteConfirmationModal = () => {
        if (deleteModal) {
            console.log('Closing delete confirmation modal');
            deleteModal.style.display = 'none';
        }
        sessionToDeleteId = null;
    };

    if (closeDeleteModalButton) {
        closeDeleteModalButton.addEventListener('click', closeDeleteConfirmationModal);
    } else {
        console.error('Close Delete Modal Button (close-delete-modal) not found!');
    }

    if (cancelDeleteButton) {
        cancelDeleteButton.addEventListener('click', closeDeleteConfirmationModal);
    } else {
        console.error('Cancel Delete Button (cancel-delete) not found!');
    }

    if (confirmDeleteButton) {
        confirmDeleteButton.addEventListener('click', () => {
            if (sessionToDeleteId) {
                console.log('Confirmed delete for session ID:', sessionToDeleteId);
                // Placeholder for actual delete logic
                (typeof Toast !== 'undefined' ? Toast.show('Delete functionality for ' + sessionToDeleteId + ' to be implemented.', 'info') : alert('Delete functionality to be implemented'));
                // Example: await deleteSession(sessionToDeleteId);
                closeDeleteConfirmationModal();
                // Potentially reload or update UI: window.location.reload();
            } else {
                console.warn('Confirm delete clicked, but no sessionToDeleteId was set.');
            }
        });
    } else {
        console.error('Confirm Delete Button (confirm-delete) not found!');
    }

    document.querySelectorAll('.delete-session').forEach(button => {
        button.addEventListener('click', function() {
            const sessionId = this.dataset.sessionId;
            if (sessionId) {
                openDeleteConfirmationModal(sessionId);
            } else {
                console.warn('Delete button clicked, but data-session-id attribute is missing.', this);
            }
        });
    });

    // --- Global Modal Closing Mechanisms (Click outside, Escape key) ---
    window.addEventListener('click', (event) => {
        // Close Add Session Modal if clicked outside
        if (addSessionModal && event.target === addSessionModal) {
            console.log('Clicked outside add session modal.');
            closeAddModal();
        }
        // Close Delete Modal if clicked outside
        if (deleteModal && event.target === deleteModal) {
            console.log('Clicked outside delete modal.');
            closeDeleteConfirmationModal();
        }
    });

    window.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            // Close Add Session Modal if open
            if (addSessionModal && addSessionModal.style.display !== 'none') {
                console.log('Escape key pressed, closing add session modal.');
                closeAddModal();
            }
            // Close Delete Modal if open
            if (deleteModal && deleteModal.style.display !== 'none') {
                console.log('Escape key pressed, closing delete modal.');
                closeDeleteConfirmationModal();
            }
        }
    });

    // --- Sidebar and Filter UI Logic (Copied from previous working version) ---
    const menuToggle = document.getElementById('menu-toggle');
    const sidebar = document.querySelector('.sidebar');
    if (menuToggle && sidebar) {
        menuToggle.addEventListener('click', () => sidebar.classList.toggle('active'));
    } else {
        if (!menuToggle) console.warn('Menu Toggle button (menu-toggle) not found.');
        if (!sidebar) console.warn('Sidebar element (.sidebar) not found.');
    }

    const filterToggle = document.getElementById('filter-toggle');
    const filterPanel = document.getElementById('sidebar-filter-panel');
    if (filterToggle && filterPanel) {
        filterToggle.addEventListener('click', () => {
            filterPanel.style.display = filterPanel.style.display === 'none' ? 'block' : 'none';
        });
        // Check URL params to decide if filter panel should be open on load
        const urlParams = new URLSearchParams(window.location.search);
        if ((urlParams.get('subjectID') && urlParams.get('subjectID') !== '') || (urlParams.get('reviewDate') && urlParams.get('reviewDate') !== '')) {
            filterPanel.style.display = 'block';
        }
    } else {
        if (!filterToggle) console.warn('Filter Toggle button (filter-toggle) not found.');
        if (!filterPanel) console.warn('Filter Panel element (sidebar-filter-panel) not found.');
    }

    console.log('review-sessions.js event listeners attached and setup complete.');
});

// Ensure Feather icons are replaced after dynamic content changes if any (e.g., after AJAX calls that add new icons)
// This might be called from other functions if they modify the DOM with new icons.
function refreshFeatherIcons() {
    if (typeof feather !== 'undefined') {
        feather.replace();
    }
}