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
        if (records.length === 0) {
            emptyState.style.display = 'flex';
            attendanceContainer.style.display = 'none';
        } else {
            emptyState.style.display = 'none';
            attendanceContainer.style.display = 'block';
        }
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
        clone.querySelector('.record-date').textContent = formatDate(record.date);
        clone.querySelector('.record-attendees').textContent = `${record.attendees} attendees`;
        clone.querySelector('.record-notes').textContent = record.notes || 'No notes provided';
        
        // Set data attribute for identification
        const recordItem = clone.querySelector('.attendance-item');
        recordItem.dataset.id = record.id;
        
        // Add event listeners for actions
        const deleteBtn = clone.querySelector('.delete-record');
        deleteBtn.addEventListener('click', () => {
            recordToDelete = record.id;
            deleteModal.style.display = 'flex';
        });
        
        attendanceList.appendChild(clone);
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
    
    // Event Listeners
    
    // Open add record modal
    addRecordBtn.addEventListener('click', () => {
        addRecordModal.style.display = 'flex';
    });
    
    // Open add record modal from empty state
    emptyAddBtn.addEventListener('click', () => {
        addRecordModal.style.display = 'flex';
    });
    
    // Close add record modal
    closeModalBtn.addEventListener('click', () => {
        addRecordModal.style.display = 'none';
        addRecordForm.reset();
    });
    
    // Cancel add record
    cancelAddBtn.addEventListener('click', () => {
        addRecordModal.style.display = 'none';
        addRecordForm.reset();
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
            id: generateId(),
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
        addRecordModal.style.display = 'none';
        addRecordForm.reset();
    });
    
    // Close delete modal
    closeDeleteModalBtn.addEventListener('click', () => {
        deleteModal.style.display = 'none';
        recordToDelete = null;
    });
    
    // Cancel delete
    cancelDeleteBtn.addEventListener('click', () => {
        deleteModal.style.display = 'none';
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
            deleteModal.style.display = 'none';
            recordToDelete = null;
        }
    });
    
    // Close modals when clicking outside
    window.addEventListener('click', (e) => {
        if (e.target === addRecordModal) {
            addRecordModal.style.display = 'none';
            addRecordForm.reset();
        }
        if (e.target === deleteModal) {
            deleteModal.style.display = 'none';
            recordToDelete = null;
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