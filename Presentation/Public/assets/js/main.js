function toggleSidebar() {
    const sidebar = document.querySelector('.sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    const toggleBtn = document.querySelector('.sidebar-toggle');
    if (!sidebar) return;
    if (window.innerWidth < 769) {
        sidebar.classList.toggle('active');
        if (overlay) overlay.classList.toggle('active');
        if (toggleBtn) toggleBtn.classList.toggle('active');
    } else {
        sidebar.classList.toggle('collapsed');
        if (toggleBtn) toggleBtn.classList.toggle('active');
    }
}

document.addEventListener('click', function(event) {
    const sidebar = document.querySelector('.sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    const toggleBtn = document.querySelector('.sidebar-toggle');
    if (window.innerWidth < 769) {
        if (sidebar && overlay && sidebar.classList.contains('active')) {
            if (!sidebar.contains(event.target) && toggleBtn && !toggleBtn.contains(event.target)) {
                sidebar.classList.remove('active');
                overlay.classList.remove('active');
                if (toggleBtn) toggleBtn.classList.remove('active');
            }
        }
    }
});

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        const sidebar = document.querySelector('.sidebar');
        const overlay = document.getElementById('sidebarOverlay');
        const toggleBtn = document.querySelector('.sidebar-toggle');
        if (sidebar && sidebar.classList.contains('active')) {
            sidebar.classList.remove('active');
            if (overlay) overlay.classList.remove('active');
            if (toggleBtn) toggleBtn.classList.remove('active');
        }
    }
});

let resizeTimer;
window.addEventListener('resize', function() {
    clearTimeout(resizeTimer);
    resizeTimer = setTimeout(function() {
        const sidebar = document.querySelector('.sidebar');
        const overlay = document.getElementById('sidebarOverlay');
        if (sidebar) {
            if (window.innerWidth >= 769) {
                sidebar.classList.remove('active');
                if (overlay) overlay.classList.remove('active');
            } else {
                sidebar.classList.remove('collapsed');
            }
        }
    }, 250);
});

function toggleDropdown(event) {
    event.stopPropagation();
    const profile = event.currentTarget;
    const menu = profile.querySelector('.dropdown-menu');
    document.querySelectorAll('.user-profile .dropdown-menu.show').forEach(function(el) {
        if (el !== menu) el.classList.remove('show');
    });
    menu.classList.toggle('show');
}

document.addEventListener('click', function(event) {
    const profile = document.querySelector('.user-profile');
    if (profile && !profile.contains(event.target)) {
        const menu = profile.querySelector('.dropdown-menu');
        if (menu) menu.classList.remove('show');
    }
});

function showNotifications() { alert('📬 You have new notifications'); }