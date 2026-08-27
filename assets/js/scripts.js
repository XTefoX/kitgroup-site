// scripts.js – Kit Group
document.addEventListener('DOMContentLoaded', function() {
    
    // ============================================================
    // Auto-close mobile menu after link click
    // ============================================================
    const navLinks = document.querySelectorAll('#mainNav .nav-link');
    const navCollapse = document.getElementById('mainNav');
    
    navLinks.forEach(link => {
        link.addEventListener('click', () => {
            const bsCollapse = bootstrap.Collapse.getInstance(navCollapse);
            if (bsCollapse) {
                bsCollapse.hide();
            }
        });
    });

    // ============================================================
    // Search form enhancement (mobile)
    // ============================================================
    const searchForm = document.querySelector('form[action="/kitgroup/products"]');
    if (searchForm) {
        const searchInput = searchForm.querySelector('input[type="search"]');
        if (searchInput) {
            // Focus search on Ctrl+Shift+F (optional)
            document.addEventListener('keydown', (e) => {
                if (e.ctrlKey && e.shiftKey && e.key === 'F') {
                    e.preventDefault();
                    searchInput.focus();
                }
            });
        }
    }
    
    console.log('Kit Group – Ready!');
});