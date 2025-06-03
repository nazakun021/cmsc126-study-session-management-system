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
    }    function updateEmptyState() {
        EmptyState.update('subjects-container', 'empty-state', subjects);
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
        subjectCard.dataset.id = subject.id;        const deleteBtn = clone.querySelector('.delete-subject');
        deleteBtn.addEventListener('click', () => {
            subjectToDelete = subject.id;
            Modal.open('delete-modal');
        });

        subjectsGrid.appendChild(clone);
    }    Modal.setupCloseButtons('add-subject-modal');
    Modal.setupCloseButtons('delete-modal');

    addSubjectBtn.addEventListener('click', () => {
        Modal.open('add-subject-modal');
    });

    emptyAddBtn.addEventListener('click', () => {
        Modal.open('add-subject-modal');
    });

    addSubjectForm.addEventListener('submit', (e) => {
        e.preventDefault();

        const name = document.getElementById('subject-name').value;
        const code = document.getElementById('subject-code').value;
        const description = document.getElementById('subject-description').value;
        const color = document.getElementById('subject-color').value;

        const newSubject = {
            id: Utils.generateId(),
            name,
            code,
            description,
            color
        };        subjects.push(newSubject);
        updateLocalStorage();
        renderSubjects();

        Modal.close('add-subject-modal');
    });    closeDeleteModalBtn.addEventListener('click', () => {
        Modal.close('delete-modal');
        subjectToDelete = null;
    });

    cancelDeleteBtn.addEventListener('click', () => {
        Modal.close('delete-modal');
        subjectToDelete = null;
    });

    confirmDeleteBtn.addEventListener('click', () => {
        if (subjectToDelete) {
            subjects = subjects.filter(subject => subject.id !== subjectToDelete);
            updateLocalStorage();
            renderSubjects();
            Modal.close('delete-modal');
            subjectToDelete = null;
        }
    });

    MobileMenu.init();

    renderSubjects(); // Load from localStorage on page load
});
