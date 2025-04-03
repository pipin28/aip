// Get the modals
var modal = document.getElementById('id01');
var deleteModal = document.getElementById('delete-modal');

// When the user clicks anywhere outside of either modal, close it
window.onclick = function(event) {
    if (event.target == modal) {
        modal.style.display = "none";
    }
    if (event.target == deleteModal) {
        deleteModal.style.display = "none";
    }
}