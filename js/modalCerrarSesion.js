function openLogoutModal() {
    document.getElementById('logoutModal').style.display = 'flex';
}

function closeLogoutModal() {
    document.getElementById('logoutModal').style.display = 'none';
}

window.onclick = function(event) {
    const modal = document.getElementById('logoutModal');
    if (event.target === modal) {
        modal.style.display = 'none';
    }
}