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

    // --- Edit Session Modal Elements ---
    const editSessionModal = document.getElementById('edit-session-modal');
    const editSessionForm = document.getElementById('edit-session-form');
    const closeEditModalButton = document.getElementById('close-edit-modal');
    const cancelEditButton = document.getElementById('cancel-edit-session');

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
        editSessionModal, // Added for logging
        editSessionForm,  // Added for logging
        closeEditModalButton, // Added for logging
        cancelEditButton, // Added for logging
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
                    console.warn('CSRF token meta tag not found for add form.');
                }

                console.log('Submitting ADD form data from review-sessions.js:', Object.fromEntries(formData));

                // Date formatting (ensure YYYY-MM-DD)
                const dateInput = e.target.querySelector('[name="reviewDate"]');
                if (dateInput) {
                    let value = dateInput.value;
                    const dateRegexDDMMYYYY = /^\d{2}\/\d{2}\/\d{4}$/; // DD/MM/YYYY
                    const dateRegexYYYYMMDD = /^\d{4}-\d{2}-\d{2}$/; // YYYY-MM-DD (standard HTML5 date input)

                    if (dateRegexDDMMYYYY.test(value)) {
                        const [day, month, year] = value.split('/');
                        formData.set('reviewDate', `${year}-${month}-${day}`);
                        console.log('Converted date from DD/MM/YYYY to YYYY-MM-DD for add form');
                    } else if (dateRegexYYYYMMDD.test(value)) {
                        console.log('Date is already in YYYY-MM-DD format for add form.');
                    } else if (value) {
                        console.warn('Date format is not recognized for reviewDate in add form:', value);
                    }
                }

                const response = await fetch('/cmsc126-study-session-management-system/public/create-session', {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();
                console.log('Server response from ADD form submission:', result);

                if (result.success) {
                    (typeof Toast !== 'undefined' ? Toast.show('Session created successfully', 'success') : alert('Session created successfully'));
                    closeAddModal();
                    window.location.reload();
                } else {
                    let errorMessage = result.message || (result.errors && result.errors.length > 0 ? result.errors.join('\n') : 'Failed to create session.');
                    (typeof Toast !== 'undefined' ? Toast.show(errorMessage, 'error') : alert(errorMessage));
                }
            } catch (error) {
                console.error('Error creating session:', error);
                (typeof Toast !== 'undefined' ? Toast.show('An unexpected error occurred. Please try again.', 'error') : alert('An unexpected error occurred. Please try again.'));
            }
        });
    } else {
        console.error('Add Session Form (add-session-form) not found!');
    }

    // --- Edit Session Modal Logic ---
    const openEditModal = (sessionData) => {
        if (editSessionModal && editSessionForm) {
            console.log('Opening edit session modal with data:', sessionData);
            // Populate form fields
            editSessionForm.querySelector('#edit-session-id').value = sessionData.reviewSessionID;
            editSessionForm.querySelector('#edit-session-title').value = sessionData.title;
            editSessionForm.querySelector('#edit-session-subject').value = sessionData.subjectId;
            editSessionForm.querySelector('#edit-session-topic').value = sessionData.topic;
            editSessionForm.querySelector('#edit-session-date').value = sessionData.date; // Ensure YYYY-MM-DD
            editSessionForm.querySelector('#edit-session-start-time').value = sessionData.startTime;
            editSessionForm.querySelector('#edit-session-end-time').value = sessionData.endTime;
            editSessionForm.querySelector('#edit-session-location').value = sessionData.location;
            editSessionForm.querySelector('#edit-session-description').value = sessionData.description;

            editSessionModal.style.display = 'flex';
        } else {
            if (!editSessionModal) console.error('Edit Session Modal (edit-session-modal) not found when trying to open.');
            if (!editSessionForm) console.error('Edit Session Form (edit-session-form) not found when trying to open.');
        }
    };

    const closeEditModal = () => {
        if (editSessionModal) {
            console.log('Closing edit session modal');
            editSessionModal.style.display = 'none';
            if (editSessionForm) {
                editSessionForm.reset();
            }
        } else {
            console.error('Edit Session Modal (edit-session-modal) not found when trying to close.');
        }
    };

    if (closeEditModalButton) {
        closeEditModalButton.addEventListener('click', closeEditModal);
    } else {
        console.warn('Close Edit Modal Button (close-edit-modal) not found. This might be an issue if you have an X button.');
    }

    if (cancelEditButton) {
        cancelEditButton.addEventListener('click', closeEditModal);
    } else {
        console.error('Cancel Edit Button (cancel-edit-session) not found!');
    }

    document.querySelectorAll('.edit-session').forEach(button => {
        button.addEventListener('click', function() {
            const sessionItem = this.closest('.session-item');
            if (sessionItem) {
                const sessionData = {
                    reviewSessionID: sessionItem.dataset.sessionId,
                    title: sessionItem.dataset.title,
                    subjectId: sessionItem.dataset.subjectId,
                    topic: sessionItem.dataset.topic,
                    date: sessionItem.dataset.date, // Should be YYYY-MM-DD
                    startTime: sessionItem.dataset.startTime,
                    endTime: sessionItem.dataset.endTime,
                    location: sessionItem.dataset.location,
                    description: sessionItem.dataset.description
                };
                openEditModal(sessionData);
            } else {
                console.error('Could not find parent .session-item for edit button', this);
            }
        });
    });

    if (editSessionForm) {
        editSessionForm.addEventListener('submit', async (e) => {
            console.log('EDIT FORM SUBMIT EVENT TRIGGERED'); 
            e.preventDefault(); 
            e.stopImmediatePropagation(); 
            console.log('Edit session form submission initiated. Default prevented.');

            const sessionIdField = editSessionForm.querySelector('#edit-session-id');
            if (!sessionIdField || !sessionIdField.value) { // Check if field exists and has a value
                console.error('Session ID field (#edit-session-id) not found or empty in edit form.');
                (typeof Toast !== 'undefined' ? Toast.show('Critical error: Session ID field missing or empty.', 'error') : alert('Critical error: Session ID field missing or empty.'));
                return;
            }
            const sessionId = sessionIdField.value;
            console.log('Attempting to update session ID:', sessionId);

            try {
                const formData = new FormData(e.target);
                const csrfTokenMeta = document.querySelector('meta[name="csrf-token"]');

                if (csrfTokenMeta && csrfTokenMeta.content) {
                    formData.append('csrf_token', csrfTokenMeta.content);
                    console.log('CSRF token appended to edit form data:', csrfTokenMeta.content);
                } else {
                    console.warn('CSRF token meta tag not found or empty for edit form.');
                    (typeof Toast !== 'undefined' ? Toast.show('CSRF token missing. Cannot submit.', 'error') : alert('CSRF token missing. Cannot submit.'));
                    return; 
                }

                // Ensure reviewSessionID is on formData if not already from a hidden input with that name
                // The hidden input is <input type="hidden" name="reviewSessionID" id="edit-session-id">
                // So, formData.get('reviewSessionID') should already have the value.
                // No need to add it again if the input name is 'reviewSessionID'

                console.log(`Submitting EDIT form data for session ID ${sessionId}:`, Object.fromEntries(formData));

                const response = await fetch('/cmsc126-study-session-management-system/public/update-session.php', { // Corrected endpoint
                    method: 'POST',
                    body: formData
                });

                console.log('Raw response from server (edit):', response);
                const result = await response.json();
                console.log('Server JSON response from EDIT form submission:', result);

                if (result.success) {
                    (typeof Toast !== 'undefined' ? Toast.show(result.message || 'Session updated successfully!', 'success') : alert(result.message || 'Session updated successfully!'));
                    closeEditModal();
                    
                    // Option 1: Reload the page (simplest for server-side filtered lists)
                    window.location.reload();

                    // Option 2: Dynamic UI update (more complex, especially if sorting/filtering changes)
                    // This was attempted before, but can lead to inconsistencies if not perfectly synced.
                    // For now, reload is safer. If dynamic update is preferred, it needs careful implementation.
                    // const cardToUpdate = document.querySelector(`.session-item[data-session-id="\${sessionId}"]`);
                    // if (cardToUpdate) {
                    //     console.log('Found card to update in DOM:', cardToUpdate);
                    //     const titleElement = cardToUpdate.querySelector('.session-title');
                    //     if (titleElement) titleElement.textContent = formData.get('reviewTitle');
                    //     cardToUpdate.dataset.title = formData.get('reviewTitle');
                    //     cardToUpdate.dataset.subjectId = formData.get('subjectID');
                    //     cardToUpdate.dataset.topic = formData.get('reviewTopic');
                    //     cardToUpdate.dataset.date = formData.get('reviewDate');
                    //     cardToUpdate.dataset.startTime = formData.get('reviewStartTime');
                    //     cardToUpdate.dataset.endTime = formData.get('reviewEndTime');
                    //     cardToUpdate.dataset.location = formData.get('reviewLocation');
                    //     cardToUpdate.dataset.description = formData.get('reviewDescription');
                        
                    //     // Update displayed subject name, date format, time format etc.
                    //     // This requires looking up subject name from a map and formatting dates/times.
                    //     // Example for subject (assuming subjectMap is available globally or passed):
                    //     // const subjectSpan = cardToUpdate.querySelector('.session-subject');
                    //     // if (subjectSpan && typeof subjectMap !== 'undefined' && subjectMap[formData.get('subjectID')]) {
                    //     //     subjectSpan.textContent = subjectMap[formData.get('subjectID')];
                    //     // }
                    //     console.log('Card UI update attempted for session ID:', sessionId);
                    // } else {
                    //     console.warn('Could not find card to update in DOM for session ID:', sessionId, '. Page will reload.');
                    //     window.location.reload();
                    // }

                } else {
                    let errorMessage = result.message || (result.errors && result.errors.length > 0 ? result.errors.join('\\\\n') : 'Failed to update session.');
                    (typeof Toast !== 'undefined' ? Toast.show(errorMessage, 'error') : alert(errorMessage));
                }
            } catch (error) {
                console.error(`Error updating session ${sessionId}:`, error);
                (typeof Toast !== 'undefined' ? Toast.show('An unexpected error occurred while updating. Check console.', 'error') : alert('An unexpected error occurred. Check console.'));
            }
        });
    } else {
        console.error('Edit Session Form (edit-session-form) not found!');
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
        confirmDeleteButton.addEventListener('click', async (event) => { // Added event parameter
            console.log('CONFIRM DELETE BUTTON CLICKED'); // New log
            event.preventDefault(); // Added to be absolutely sure
            event.stopImmediatePropagation(); // Added to be absolutely sure
            console.log('Confirm delete button click. Default prevented.');

            if (sessionToDeleteId) {
                const idToDelete = sessionToDeleteId; // Capture the ID before any async operations or modal closing
                console.log('Attempting to delete session ID:', idToDelete);
                try {
                    const csrfTokenMeta = document.querySelector('meta[name="csrf-token"]');
                    const formData = new FormData();
                    formData.append('reviewSessionID', idToDelete); // Use captured idToDelete

                    if (csrfTokenMeta && csrfTokenMeta.content) {
                        formData.append('csrf_token', csrfTokenMeta.content);
                        console.log('CSRF token appended to delete form data:', csrfTokenMeta.content);
                    } else {
                        console.warn('CSRF token meta tag not found or empty for delete action.');
                        (typeof Toast !== 'undefined' ? Toast.show('CSRF token missing. Cannot submit delete.', 'error') : alert('CSRF token missing. Cannot submit delete.'));
                        return; // Stop if CSRF is missing
                    }

                    console.log('Submitting DELETE request for session ID:', idToDelete, 'with FormData:', Object.fromEntries(formData));

                    const response = await fetch('/cmsc126-study-session-management-system/public/delete-session', {
                        method: 'POST',
                        body: formData
                    });

                    console.log('Raw response from server (delete):', response);
                    const result = await response.json();
                    console.log('Server JSON response from DELETE action:', result);

                    if (result.success) {
                        (typeof Toast !== 'undefined' ? Toast.show(result.message || 'Session deleted successfully!', 'success') : alert(result.message || 'Session deleted successfully!'));
                        
                        const cardToRemove = document.querySelector(`.session-item[data-session-id="${idToDelete}"]`); // Use captured idToDelete
                        if (cardToRemove) {
                            cardToRemove.remove();
                            console.log(`Removed session card ${idToDelete} from DOM.`);
                        } else {
                            console.warn(`Could not find session card ${idToDelete} to remove from DOM. Page may need manual refresh.`);
                        }
                        closeDeleteConfirmationModal(); // Close modal AFTER successful operation and DOM update
                    } else {
                        let errorMessage = result.message || 'Failed to delete session.';
                        (typeof Toast !== 'undefined' ? Toast.show(errorMessage, 'error') : alert(errorMessage));
                        // Modal remains open on failure for user to see context or retry.
                    }
                } catch (error) {
                    console.error(`Error deleting session ${idToDelete}:`, error); // Use captured idToDelete
                    (typeof Toast !== 'undefined' ? Toast.show('An unexpected error occurred while deleting. Check console.', 'error') : alert('An unexpected error occurred. Check console.'));
                    // Modal remains open on error.
                }
            } else {
                console.warn('sessionToDeleteId is null when confirm delete was clicked. This should not happen.');
            }
        });
    } else {
        console.error('Confirm Delete Button (confirm-delete) not found!');
    }

    document.querySelectorAll('.delete-session').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault(); // Prevent any default action
            e.stopImmediatePropagation(); // Stop other listeners
            const sessionId = this.dataset.sessionId;
            console.log('Delete button clicked for session ID:', sessionId);
            if (sessionId) {
                openDeleteConfirmationModal(sessionId);
            } else {
                console.error('Session ID not found on delete button', this);
            }
        });
    });

    // --- Filter Panel Toggle ---
    const filterToggleBtn = document.getElementById('filter-toggle');
    const sidebarFilterPanel = document.getElementById('sidebar-filter-panel');

    if (filterToggleBtn && sidebarFilterPanel) {
        filterToggleBtn.addEventListener('click', () => {
            const isPanelVisible = sidebarFilterPanel.style.display === 'block';
            sidebarFilterPanel.style.display = isPanelVisible ? 'none' : 'block';
            // Optionally, update the toggle button's appearance (e.g., active state)
            filterToggleBtn.classList.toggle('active', !isPanelVisible);
            console.log(`Filter panel toggled. Visible: ${!isPanelVisible}`);
        });
    } else {
        if (!filterToggleBtn) console.warn('Filter toggle button (filter-toggle) not found.');
        if (!sidebarFilterPanel) console.warn('Sidebar filter panel (sidebar-filter-panel) not found.');
    }

    // --- Clear Filter Button ---
    // The clear filter is an <a> tag navigating to review-sessions.php without query params.
    // This should work by default. If AJAX filtering were implemented, this would need JS handling.
    const clearFilterBtn = document.getElementById('clear-filter');
    if (clearFilterBtn) {
        clearFilterBtn.addEventListener('click', (e) => {
            // Optional: if we wanted to do something before navigating, like show a spinner
            console.log('Clear filter button clicked. Navigating to base page.');
        });
    }

    // --- Search Functionality (Placeholder/Example) ---
    const sessionSearchInput = document.getElementById('session-search');
    if (sessionSearchInput) {
        sessionSearchInput.addEventListener('input', (e) => {
            const searchTerm = e.target.value.toLowerCase();
            const sessionItems = document.querySelectorAll('.session-item');
            sessionItems.forEach(item => {
                const title = item.dataset.title ? item.dataset.title.toLowerCase() : '';
                const topic = item.dataset.topic ? item.dataset.topic.toLowerCase() : '';
                const subjectNameElement = item.querySelector('.session-subject');
                const subject = subjectNameElement ? subjectNameElement.textContent.toLowerCase() : '';

                if (title.includes(searchTerm) || topic.includes(searchTerm) || subject.includes(searchTerm)) {
                    item.style.display = ''; // Or 'flex', 'grid' depending on your layout
                } else {
                    item.style.display = 'none';
                }
            });
        });
    } else {
        console.warn('Session search input (session-search) not found.');
    }

}); // End of DOMContentLoaded