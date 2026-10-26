/* PGS Portal - shared UI helpers (no business logic) */
(function () {
    function openSidebar() { document.body.classList.add('sidebar-open'); }
    function closeSidebar() { document.body.classList.remove('sidebar-open'); }
    function toggleSidebar() { document.body.classList.toggle('sidebar-open'); }

    window.App = { openSidebar: openSidebar, closeSidebar: closeSidebar, toggleSidebar: toggleSidebar };

    // Close mobile sidebar when a menu link is followed
    document.addEventListener('click', function (e) {
        var link = e.target.closest && e.target.closest('.sidebar a');
        if (link && document.body.classList.contains('sidebar-open')) closeSidebar();
    });

    // ESC closes any open modal
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            closeSidebar();
            document.querySelectorAll('.modal').forEach(function (m) { m.style.display = 'none'; });
        }
    });
})();
