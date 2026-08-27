<?php
// admin/templates/footer.php
// This file is included at the bottom of every admin page
?>
    </main>
</div>

<!-- ============================================================
     SCRIPTS
     ============================================================ -->
<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Admin Scripts -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // ============================================================
    // MOBILE SIDEBAR TOGGLE
    // ============================================================
    const menuToggle = document.getElementById('menuToggle');
    const sidebar = document.getElementById('adminSidebar');
    const overlay = document.getElementById('sidebarOverlay');

    function toggleSidebar() {
        sidebar.classList.toggle('show');
        overlay.classList.toggle('show');
        document.body.style.overflow = sidebar.classList.contains('show') ? 'hidden' : '';
    }

    if (menuToggle) {
        menuToggle.addEventListener('click', toggleSidebar);
    }

    if (overlay) {
        overlay.addEventListener('click', toggleSidebar);
    }

    // Close sidebar on escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && sidebar.classList.contains('show')) {
            toggleSidebar();
        }
    });

    // ============================================================
    // AUTO-CLOSE SIDEBAR ON NAV CLICK (Mobile)
    // ============================================================
    const navItems = document.querySelectorAll('.sidebar-nav .nav-item');
    navItems.forEach(item => {
        item.addEventListener('click', function() {
            if (window.innerWidth <= 992 && sidebar.classList.contains('show')) {
                toggleSidebar();
            }
        });
    });

    // ============================================================
    // CONFIRM DELETE
    // ============================================================
    document.querySelectorAll('.delete-confirm').forEach(btn => {
        btn.addEventListener('click', function(e) {
            if (!confirm('Are you sure you want to delete this item?')) {
                e.preventDefault();
            }
        });
    });

    // ============================================================
    // AUTO-HIDE ALERTS
    // ============================================================
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.transition = 'opacity 0.5s ease';
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 500);
        }, 4000);
    });

    console.log('✅ Kit Group Admin Panel – Ready');
});
</script>

</body>
</html>