document.addEventListener('open-modal', () => {
    const modal = document.getElementById('editAddModal');
    modal.style.display = 'block';
});

function closeModal() {
    const modal = document.getElementById('editAddModal');
    modal.style.display = 'none';
}

document.addEventListener('close-add-modal', () => {
    closeModal();
});


document.addEventListener('open-delete-modal', () => {
const modal = document.getElementById('deleteConfirmationModal');
modal.style.display = 'block';
});

function closeDeleteModal() {
const modal = document.getElementById('deleteConfirmationModal');
modal.style.display = 'none';
}

document.addEventListener('close-delete-modal', () => {
closeDeleteModal();
});


