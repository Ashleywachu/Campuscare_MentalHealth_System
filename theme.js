function applyStoredTheme() {
    if (localStorage.getItem('theme') === 'dark') {
        document.body.classList.add('dark');
    }
    document.querySelectorAll('.theme-toggle-icon').forEach(function (icon) {
        icon.className = document.body.classList.contains('dark')
            ? 'fa-solid fa-sun'
            : 'fa-solid fa-moon';
    });
}

function toggleTheme() {
    document.body.classList.toggle('dark');
    localStorage.setItem('theme', document.body.classList.contains('dark') ? 'dark' : 'light');
    document.querySelectorAll('.theme-toggle-icon').forEach(function (icon) {
        icon.className = document.body.classList.contains('dark')
            ? 'fa-solid fa-sun'
            : 'fa-solid fa-moon';
    });
}

document.addEventListener('DOMContentLoaded', applyStoredTheme);
