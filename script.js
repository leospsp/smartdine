function navigate(screenId) {
    document.querySelectorAll('.screen').forEach(screen => {
        screen.classList.remove('active');
    });
    document.getElementById(screenId).classList.add('active');
}

function toggleMenu() {
    document.getElementById('sidebar').classList.toggle('open');
}
