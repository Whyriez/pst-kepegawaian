// Enhanced sidebar functionality with mobile improvements
document.addEventListener('DOMContentLoaded', function() {
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.querySelector('.sidebar');
    const mainContent = document.querySelector('.main-content');
    
    // Create overlay for mobile
    const overlay = document.createElement('div');
    overlay.className = 'sidebar-overlay';
    document.body.appendChild(overlay);
    
    // Initialize sidebar state from session storage
    initializeSidebarState();
    
    // Toggle submenus dengan state persistence
    const submenuToggles = document.querySelectorAll('.menu-item.has-submenu');
    
    submenuToggles.forEach(toggle => {
        toggle.addEventListener('click', function(e) {
            // Prevent default only for submenu toggles without href
            if (this.getAttribute('href') === 'javascript:void(0)') {
                e.preventDefault();
            }
            
            const parentLi = this.closest('li');
            const submenu = this.nextElementSibling;
            
            // Toggle current submenu
            toggleSubmenu(parentLi, submenu, this);
        });
    });
    
    // Set active states based on current route
    setActiveStates();
    
    // Mobile sidebar toggle dengan overlay
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function(e) {
            e.stopPropagation();
            toggleMobileSidebar();
        });
    }
    
    // Close sidebar when clicking overlay
    overlay.addEventListener('click', function() {
        closeMobileSidebar();
    });
    
    // Close sidebar when clicking outside on mobile
    document.addEventListener('click', function(e) {
        if (window.innerWidth <= 768 && 
            sidebar.classList.contains('active') &&
            !sidebar.contains(e.target) && 
            !sidebarToggle.contains(e.target)) {
            closeMobileSidebar();
        }
    });
    
    // Swipe to close functionality
    let touchStartX = 0;
    let touchEndX = 0;
    
    sidebar.addEventListener('touchstart', function(e) {
        touchStartX = e.changedTouches[0].screenX;
    }, { passive: true });
    
    sidebar.addEventListener('touchend', function(e) {
        touchEndX = e.changedTouches[0].screenX;
        handleSwipe();
    }, { passive: true });
    
    // Handle window resize
    window.addEventListener('resize', function() {
        if (window.innerWidth > 768) {
            closeMobileSidebar();
        }
    });
    
    // Keyboard escape key to close sidebar
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && sidebar.classList.contains('active')) {
            closeMobileSidebar();
        }
    });
    
    // Fungsi untuk mobile sidebar
    function toggleMobileSidebar() {
        sidebar.classList.toggle('active');
        overlay.classList.toggle('active');
        sidebarToggle.classList.toggle('active');
        document.body.style.overflow = sidebar.classList.contains('active') ? 'hidden' : '';
    }
    
    function closeMobileSidebar() {
        sidebar.classList.remove('active');
        overlay.classList.remove('active');
        sidebarToggle.classList.remove('active');
        document.body.style.overflow = '';
    }
    
    function handleSwipe() {
        const swipeThreshold = 50;
        const swipeDistance = touchEndX - touchStartX;
        
        if (swipeDistance < -swipeThreshold && sidebar.classList.contains('active')) {
            closeMobileSidebar();
        }
    }
    
    // ... (rest of the functions remain the same as previous version)
    function toggleSubmenu(parentLi, submenu, trigger) {
        const isOpening = !submenu.classList.contains('open');
        
        // Close other open submenus at the same level
        if (isOpening) {
            const siblingSubmenus = parentLi.parentElement.querySelectorAll('.submenu.open');
            siblingSubmenus.forEach(menu => {
                if (menu !== submenu) {
                    closeSubmenu(menu);
                }
            });
        }
        
        // Toggle current submenu
        if (isOpening) {
            openSubmenu(submenu, trigger);
        } else {
            closeSubmenu(submenu);
        }
        
        // Save state to session storage
        saveSidebarState();
    }
    
    function openSubmenu(submenu, trigger) {
        submenu.classList.add('open');
        submenu.style.maxHeight = submenu.scrollHeight + 'px';
        if (trigger) {
            trigger.classList.add('active');
            trigger.closest('li').classList.add('active');
        }
    }
    
    function closeSubmenu(submenu) {
        submenu.classList.remove('open');
        submenu.style.maxHeight = null;
        const trigger = submenu.previousElementSibling;
        if (trigger && trigger.classList.contains('has-submenu')) {
            trigger.classList.remove('active');
            trigger.closest('li').classList.remove('active');
        }
    }
    
    function initializeSidebarState() {
        const savedState = sessionStorage.getItem('sidebarState');
        if (savedState) {
            const state = JSON.parse(savedState);
            
            state.openSubmenus.forEach(menuIndex => {
                const menuItems = document.querySelectorAll('.menu-item.has-submenu');
                if (menuItems[menuIndex]) {
                    const parentLi = menuItems[menuIndex].closest('li');
                    const submenu = menuItems[menuIndex].nextElementSibling;
                    openSubmenu(submenu, menuItems[menuIndex]);
                }
            });
        }
    }
    
    function saveSidebarState() {
        const openSubmenus = [];
        const menuItems = document.querySelectorAll('.menu-item.has-submenu');
        
        menuItems.forEach((item, index) => {
            if (item.classList.contains('active')) {
                openSubmenus.push(index);
            }
        });
        
        const state = {
            openSubmenus: openSubmenus
        };
        
        sessionStorage.setItem('sidebarState', JSON.stringify(state));
    }
    
    function setActiveStates() {
        const currentPath = window.location.pathname;
        
        // Remove all active states first
        document.querySelectorAll('.menu-item, .submenu-item').forEach(item => {
            item.classList.remove('active');
        });
        document.querySelectorAll('.menu li').forEach(li => {
            li.classList.remove('active', 'open');
        });
        document.querySelectorAll('.submenu').forEach(submenu => {
            submenu.classList.remove('open');
            submenu.style.maxHeight = null;
        });
        
        // Find and set active menu item based on current route
        const menuItems = document.querySelectorAll('.menu-item, .submenu-item');
        let activeItem = null;
        
        menuItems.forEach(item => {
            const href = item.getAttribute('href');
            if (href && href !== 'javascript:void(0)' && href !== '#') {
                try {
                    const itemPath = new URL(href, window.location.origin).pathname;
                    if (currentPath === itemPath || currentPath.startsWith(itemPath + '/')) {
                        activeItem = item;
                    }
                } catch (e) {
                    if (currentPath === href || currentPath.startsWith(href + '/')) {
                        activeItem = item;
                    }
                }
            }
        });
        
        if (activeItem) {
            activeItem.classList.add('active');
            
            if (activeItem.classList.contains('submenu-item')) {
                const submenu = activeItem.closest('.submenu');
                const parentMenuItem = submenu.previousElementSibling;
                const parentLi = parentMenuItem.closest('li');
                
                if (submenu && parentMenuItem) {
                    openSubmenu(submenu, parentMenuItem);
                    parentLi.classList.add('active', 'open');
                }
            }
            
            if (activeItem.classList.contains('has-submenu')) {
                const submenu = activeItem.nextElementSibling;
                const parentLi = activeItem.closest('li');
                
                if (submenu) {
                    openSubmenu(submenu, activeItem);
                    parentLi.classList.add('active', 'open');
                }
            }
            
            saveSidebarState();
        }
    }
});