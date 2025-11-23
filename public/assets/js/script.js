'use strict';

document.addEventListener('DOMContentLoaded', () => {
    /* ==========================================
       1. SELECTORS
       ========================================== */
    const sidebar = document.querySelector('.sidebar');
    const sidebarToggle = document.getElementById('sidebarToggle');
    
    /* ==========================================
       2. SIDEBAR TOGGLE (Mobile & Desktop)
       ========================================== */
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function () {
            if (window.innerWidth > 992) {
                // Desktop: Collapse sidebar
                sidebar.classList.toggle('collapsed');
            } else {
                // Mobile: Show/Hide sidebar
                sidebar.classList.toggle('active');
            }
            
            // Ganti icon burger/close
            const icon = this.querySelector('i');
            if(icon) {
                if (sidebar.classList.contains('collapsed') || !sidebar.classList.contains('active')) {
                     icon.classList.remove('fa-times');
                     icon.classList.add('fa-bars');
                } else {
                     icon.classList.remove('fa-bars');
                     icon.classList.add('fa-times');
                }
            }
        });
    }

    /* ==========================================
       3. DROPDOWN / SUBMENU LOGIC
       ========================================== */
    const menuItems = document.querySelectorAll('.menu-item.has-submenu');

    menuItems.forEach(item => {
        item.addEventListener('click', (e) => {
            // PENTING: PreventDefault hanya untuk menu yang punya anak (Dropdown)
            // Agar tidak reload saat klik induk menu
            e.preventDefault(); 

            const submenu = item.nextElementSibling;
            if (!submenu) return;

            // Logika Accordion: Tutup submenu lain yang sedang terbuka
            document.querySelectorAll('.submenu.active').forEach(s => {
                if (s !== submenu) {
                    s.style.maxHeight = '0';
                    s.classList.remove('active');
                    s.previousElementSibling.classList.remove('active');
                    s.previousElementSibling.parentElement.classList.remove('active');
                }
            });

            // Toggle submenu yang diklik
            item.classList.toggle('active'); // Highlight induknya
            submenu.classList.toggle('active');
            item.parentElement.classList.toggle('active'); // Highlight li wrapper

            // Animasi slide
            if (submenu.classList.contains('active')) {
                submenu.style.maxHeight = submenu.scrollHeight + 'px';
            } else {
                submenu.style.maxHeight = '0';
            }
        });
    });

    // Pastikan submenu yang aktif (dari Laravel) terbuka saat halaman dimuat
    const activeSubmenu = document.querySelector('.submenu[style*="display:block"]');
    if (activeSubmenu) {
        activeSubmenu.classList.add('active');
        activeSubmenu.style.maxHeight = activeSubmenu.scrollHeight + 'px';
        // Highlight parentnya
        const parentLink = activeSubmenu.previousElementSibling;
        if(parentLink) parentLink.classList.add('active');
    }

    /* ==========================================
       4. TUTUP SIDEBAR SAAT KLIK DI LUAR (Mobile)
       ========================================== */
    document.addEventListener('click', (e) => {
        if (window.innerWidth <= 992 && 
            sidebar.classList.contains('active') && 
            !sidebar.contains(e.target) && 
            !sidebarToggle.contains(e.target)) {
            sidebar.classList.remove('active');
        }
    });
});