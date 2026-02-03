// Sidebar functionality
function initializeSidebar() {
    const sidebar = document.getElementById('sidebar');
    const mainContent = document.getElementById('mainContent');
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebarToggleBtn = document.getElementById('sidebarToggleBtn');

    // Set initial state
    if (window.innerWidth <= 768) {
        sidebar.classList.add('collapsed');
        mainContent.classList.add('expanded');
    }

    function toggleSidebar() {
        if (window.innerWidth <= 768) {
            sidebar.classList.toggle('expanded');
        } else {
            sidebar.classList.toggle('collapsed');
            mainContent.classList.toggle('expanded');
        }
    }

    // Remove existing event listeners
    if (sidebarToggle) {
        sidebarToggle.removeEventListener('click', toggleSidebar);
        sidebarToggle.addEventListener('click', toggleSidebar);
    }
    if (sidebarToggleBtn) {
        sidebarToggleBtn.removeEventListener('click', toggleSidebar);
        sidebarToggleBtn.addEventListener('click', toggleSidebar);
    }

    // Handle responsive behavior
    function handleResize() {
        if (window.innerWidth <= 768) {
            sidebar.classList.remove('collapsed');
            sidebar.classList.add('expanded');
            mainContent.classList.add('expanded');
        } else {
            sidebar.classList.remove('expanded');
            sidebar.classList.remove('collapsed');
            mainContent.classList.remove('expanded');
        }
    }

    window.removeEventListener('resize', handleResize);
    window.addEventListener('resize', handleResize);
}

// Initialize sidebar when DOM is loaded
document.addEventListener('DOMContentLoaded', initializeSidebar);

// Initialize sidebar when Turbo navigation occurs (if using Turbo)
if (typeof Turbo !== 'undefined') {
    document.addEventListener('turbo:load', initializeSidebar);
}

// Initialize sidebar when page is shown (for browsers that support it)
document.addEventListener('pageshow', initializeSidebar); 