document.addEventListener('DOMContentLoaded', function () {
    feather.replace();

    const addSubjectBtn = document.getElementById('add-subject-btn');
    const emptyAddBtn = document.getElementById('empty-add-btn');
    const addSubjectModal = document.getElementById('add-subject-modal');
    const closeModalBtn = document.getElementById('close-modal');
    const cancelAddBtn = document.getElementById('cancel-add');
    const addSubjectForm = document.getElementById('add-subject-form');
    const subjectsContainer = document.getElementById('subjects-container');
    const subjectsGrid = document.getElementById('subjects-grid');
    const emptyState = document.getElementById('empty-state');
    const deleteModal = document.getElementById('delete-modal');
    const closeDeleteModalBtn = document.getElementById('close-delete-modal');
    const cancelDeleteBtn = document.getElementById('cancel-delete');
    const confirmDeleteBtn = document.getElementById('confirm-delete');

    let subjects = JSON.parse(localStorage.getItem('subjects')) || [];
    let subjectToDelete = null;

    function updateLocalStorage() {
        localStorage.setItem('subjects', JSON.stringify(subjects));
    }

    function updateEmptyState() {
        if (subjects.length === 0) {
            emptyState.style.display = 'flex';
            subjectsContainer.style.display = 'none';
        } else {
            emptyState.style.display = 'none';
            subjectsContainer.style.display = 'block';
        }
    }

    function renderSubjects() {
        subjectsGrid.innerHTML = '';
        subjects.forEach(subject => addSubjectToDOM(subject));
        feather.replace();
        updateEmptyState();
    }

    function addSubjectToDOM(subject) {
        const template = document.getElementById('subject-template');
        const clone = document.importNode(template.content, true);

        clone.querySelector('.subject-name').textContent = subject.name;
        clone.querySelector('.subject-code').textContent = subject.code;
        clone.querySelector('.subject-description').textContent = subject.description || 'No description provided';
        clone.querySelector('.subject-color').style.backgroundColor = subject.color;

        const subjectCard = clone.querySelector('.subject-card');
        subjectCard.dataset.id = subject.id;

        const deleteBtn = clone.querySelector('.delete-subject');
        deleteBtn.addEventListener('click', () => {
            subjectToDelete = subject.id;
            deleteModal.style.display = 'flex';
        });

        subjectsGrid.appendChild(clone);
    }

    function generateId() {
        return Date.now().toString(36) + Math.random().toString(36).substring(2);
    }

    addSubjectBtn.addEventListener('click', () => {
        addSubjectModal.style.display = 'flex';
    });

    emptyAddBtn.addEventListener('click', () => {
        addSubjectModal.style.display = 'flex';
    });

    closeModalBtn.addEventListener('click', () => {
        addSubjectModal.style.display = 'none';
        addSubjectForm.reset();
    });

    cancelAddBtn.addEventListener('click', () => {
        addSubjectModal.style.display = 'none';
        addSubjectForm.reset();
    });

    addSubjectForm.addEventListener('submit', (e) => {
        e.preventDefault();

        const name = document.getElementById('subject-name').value;
        const code = document.getElementById('subject-code').value;
        const description = document.getElementById('subject-description').value;
        const color = document.getElementById('subject-color').value;

        const newSubject = {
            id: generateId(),
            name,
            code,
            description,
            color
        };

        subjects.push(newSubject);
        updateLocalStorage();
        renderSubjects();

        addSubjectModal.style.display = 'none';
        addSubjectForm.reset();
    });

    closeDeleteModalBtn.addEventListener('click', () => {
        deleteModal.style.display = 'none';
        subjectToDelete = null;
    });

    cancelDeleteBtn.addEventListener('click', () => {
        deleteModal.style.display = 'none';
        subjectToDelete = null;
    });

    confirmDeleteBtn.addEventListener('click', () => {
        if (subjectToDelete) {
            subjects = subjects.filter(subject => subject.id !== subjectToDelete);
            updateLocalStorage();
            renderSubjects();
            deleteModal.style.display = 'none';
            subjectToDelete = null;
        }
    });

    window.addEventListener('click', (e) => {
        if (e.target === addSubjectModal) {
            addSubjectModal.style.display = 'none';
            addSubjectForm.reset();
        }
        if (e.target === deleteModal) {
            deleteModal.style.display = 'none';
            subjectToDelete = null;
        }
    });

    const menuToggle = document.getElementById('menu-toggle');
    const sidebar = document.querySelector('.sidebar');

    menuToggle.addEventListener('click', () => {
        sidebar.classList.toggle('active');
    });

    document.addEventListener('click', function (event) {
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

    renderSubjects(); // Load from localStorage on page load
});
