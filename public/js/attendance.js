document.addEventListener('DOMContentLoaded', function() {
    // Initialize Feather icons
    feather.replace();
    
    // DOM elements
    const addRecordBtn = document.getElementById('add-record-btn');
    const emptyAddBtn = document.getElementById('empty-add-btn');
    const addRecordModal = document.getElementById('add-record-modal');
    const closeModalBtn = document.getElementById('close-modal');
    const cancelAddBtn = document.getElementById('cancel-add');
    const addRecordForm = document.getElementById('add-record-form');
    const attendanceContainer = document.getElementById('attendance-container');
    const attendanceList = document.getElementById('attendance-list');
    const emptyState = document.getElementById('empty-state');
    const deleteModal = document.getElementById('delete-modal');
    const closeDeleteModalBtn = document.getElementById('close-delete-modal');
    const cancelDeleteBtn = document.getElementById('cancel-delete');
    const confirmDeleteBtn = document.getElementById('confirm-delete');
    
    // Attendance data array (empty initially)
    let records = [];
    let recordToDelete = null;
      // Show/hide empty state based on records
    function updateEmptyState() {
        EmptyState.update('attendance-container', 'empty-state', records);
    }
    
    // Render all attendance records
    function renderRecords() {
        // Clear existing records
        attendanceList.innerHTML = '';
        
        // Add each record
        records.forEach(record => {
            addRecordToDOM(record);
        });
        
        // Re-initialize Feather icons for new content
        feather.replace();
        
        // Update empty state
        updateEmptyState();
    }
      // Add a single record to the DOM
    function addRecordToDOM(record) {
        const template = document.getElementById('record-template');
        const clone = document.importNode(template.content, true);
        
        // Set record data
        clone.querySelector('.record-session').textContent = record.session;
        clone.querySelector('.record-date').textContent = Utils.formatDate(record.date);
        clone.querySelector('.record-attendees').textContent = `${record.attendees} attendees`;
        clone.querySelector('.record-notes').textContent = record.notes || 'No notes provided';
        
        // Set data attribute for identification
        const recordItem = clone.querySelector('.attendance-item');
        recordItem.dataset.id = record.id;
          // Add event listeners for actions
        const deleteBtn = clone.querySelector('.delete-record');
        deleteBtn.addEventListener('click', () => {
            recordToDelete = record.id;
            Modal.open('delete-modal');
        });
        
        attendanceList.appendChild(clone);
    }    
    // Event Listeners
    Modal.setupCloseButtons('add-record-modal');
    Modal.setupCloseButtons('delete-modal');
    
    // Open add record modal
    addRecordBtn.addEventListener('click', () => {
        Modal.open('add-record-modal');
    });
    
    // Open add record modal from empty state
    emptyAddBtn.addEventListener('click', () => {
        Modal.open('add-record-modal');
    });
    
    // Submit add record form
    addRecordForm.addEventListener('submit', (e) => {
        e.preventDefault();
        
        // Get form values
        const session = document.getElementById('record-session').value;
        const date = document.getElementById('record-date').value;
        const attendees = document.getElementById('record-attendees').value;
        const notes = document.getElementById('record-notes').value;
        
        // Create new record object
        const newRecord = {
            id: Utils.generateId(),
            session,
            date,
            attendees,
            notes
        };
        
        // Add to records array
        records.push(newRecord);
          // Render records
        renderRecords();
        
        // Close modal and reset form
        Modal.close('add-record-modal');
    });
      // Close delete modal
    closeDeleteModalBtn.addEventListener('click', () => {
        Modal.close('delete-modal');
        recordToDelete = null;
    });
    
    // Cancel delete
    cancelDeleteBtn.addEventListener('click', () => {
        Modal.close('delete-modal');
        recordToDelete = null;
    });
    
    // Confirm delete
    confirmDeleteBtn.addEventListener('click', () => {
        if (recordToDelete) {
            // Remove from array
            records = records.filter(record => record.id !== recordToDelete);
            
            // Render records
            renderRecords();
            
            // Close modal
            Modal.close('delete-modal');
            recordToDelete = null;
        }
    });
    
    MobileMenu.init();
    
    // Initialize the page
    updateEmptyState();
});