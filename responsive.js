/* ============================================================================
   RESPONSIVE MENU HANDLER - JOURNEYMERAPI
   Instruksi: Tambahkan <script src="responsive.js"></script> sebelum </body>
   di SEMUA file HTML (user pages & admin pages)
   ============================================================================ */

(function() {
    'use strict';
    
    // ========================================================================
    // UTILITY FUNCTIONS
    // ========================================================================
    
    function isMobile() {
        return window.innerWidth <= 1023;
    }
    
    function preventBodyScroll(prevent) {
        if (prevent) {
            document.body.style.overflow = 'hidden';
            document.body.classList.add('modal-open');
        } else {
            document.body.style.overflow = '';
            document.body.classList.remove('modal-open');
        }
    }
    
    // ========================================================================
    // ADMIN SIDEBAR HANDLER
    // ========================================================================
    
    function initAdminSidebar() {
        const sidebar = document.querySelector('.sidebar');
        if (!sidebar) return;
        
        // Create hamburger button
        if (!document.querySelector('.hamburger-menu')) {
            const hamburger = document.createElement('button');
            hamburger.className = 'hamburger-menu';
            hamburger.innerHTML = '<span></span><span></span><span></span>';
            hamburger.style.display = 'none'; // Hidden by default, CSS shows on mobile
            document.body.appendChild(hamburger);
            
            // Create backdrop
            const backdrop = document.createElement('div');
            backdrop.className = 'sidebar-backdrop';
            document.body.appendChild(backdrop);
            
            // Toggle handler
            hamburger.addEventListener('click', function() {
                sidebar.classList.toggle('active');
                backdrop.classList.toggle('active');
                hamburger.classList.toggle('active');
                preventBodyScroll(sidebar.classList.contains('active'));
            });
            
            // Close on backdrop click
            backdrop.addEventListener('click', function() {
                sidebar.classList.remove('active');
                backdrop.classList.remove('active');
                hamburger.classList.remove('active');
                preventBodyScroll(false);
            });
            
            // Close on menu item click (mobile)
            const menuLinks = sidebar.querySelectorAll('.menu a');
            menuLinks.forEach(link => {
                link.addEventListener('click', function() {
                    if (isMobile()) {
                        setTimeout(() => {
                            sidebar.classList.remove('active');
                            backdrop.classList.remove('active');
                            hamburger.classList.remove('active');
                            preventBodyScroll(false);
                        }, 200);
                    }
                });
            });
            
            // Reset pada resize
            window.addEventListener('resize', function() {
                if (window.innerWidth > 1023) {
                    sidebar.classList.remove('active');
                    backdrop.classList.remove('active');
                    hamburger.classList.remove('active');
                    preventBodyScroll(false);
                }
            });
        }
    }
    
    // ========================================================================
    // USER PAGES MOBILE MENU HANDLER
    // ========================================================================
    
    function initUserMenu() {
        const menus = [
            '.menu', '.menu-about', '.menu-contact', 
            '.menu-order', '.menu-status', '.menu-tour'
        ];
        
        menus.forEach(selector => {
            const menu = document.querySelector(selector);
            if (!menu) return;
            
            const navbar = menu.closest('.navbar');
            if (!navbar) return;
            
            // Cek apakah sudah ada toggle button
            let toggle = navbar.querySelector('.mobile-menu-toggle');
            
            if (!toggle) {
                // Create toggle button
                toggle = document.createElement('button');
                toggle.className = 'mobile-menu-toggle';
                toggle.innerHTML = '<span></span><span></span><span></span>';
                toggle.style.display = 'none'; // CSS will show on mobile
                
                // Determine color based on page
                const isIndexPage = selector === '.menu';
                toggle.style.color = isIndexPage ? '#eee' : '#adaf9c';
                
                // Insert after logo
                const logo = navbar.querySelector('[class^="logo"]');
                if (logo) {
                    logo.after(toggle);
                }
                
                // Toggle handler
                toggle.addEventListener('click', function() {
                    menu.classList.toggle('active');
                    toggle.classList.toggle('active');
                    preventBodyScroll(menu.classList.contains('active'));
                });
                
                // Close on menu item click
                const links = menu.querySelectorAll('a');
                links.forEach(link => {
                    link.addEventListener('click', function() {
                        if (isMobile()) {
                            setTimeout(() => {
                                menu.classList.remove('active');
                                toggle.classList.remove('active');
                                preventBodyScroll(false);
                            }, 200);
                        }
                    });
                });
                
                // Close on outside click
                document.addEventListener('click', function(e) {
                    if (isMobile() && 
                        menu.classList.contains('active') && 
                        !menu.contains(e.target) && 
                        !toggle.contains(e.target)) {
                        menu.classList.remove('active');
                        toggle.classList.remove('active');
                        preventBodyScroll(false);
                    }
                });
                
                // Reset on resize
                window.addEventListener('resize', function() {
                    if (window.innerWidth > 1023) {
                        menu.classList.remove('active');
                        toggle.classList.remove('active');
                        preventBodyScroll(false);
                    }
                });
            }
        });
    }
    
    // ========================================================================
    // TABLE SCROLL SHADOW INDICATOR
    // ========================================================================
    
    function initTableScrollIndicator() {
        const tableWrappers = document.querySelectorAll('.table-wrapper');
        
        tableWrappers.forEach(wrapper => {
            if (!isMobile()) return;
            
            wrapper.addEventListener('scroll', function() {
                const scrollLeft = this.scrollLeft;
                const scrollWidth = this.scrollWidth;
                const clientWidth = this.clientWidth;
                
                // Toggle shadows based on scroll position
                if (scrollLeft <= 10) {
                    this.style.setProperty('--show-left-shadow', '0');
                } else {
                    this.style.setProperty('--show-left-shadow', '1');
                }
                
                if (scrollLeft + clientWidth >= scrollWidth - 10) {
                    this.style.setProperty('--show-right-shadow', '0');
                } else {
                    this.style.setProperty('--show-right-shadow', '1');
                }
            });
            
            // Trigger initial check
            wrapper.dispatchEvent(new Event('scroll'));
        });
    }
    
    // ========================================================================
    // MODAL ESC KEY HANDLER (semua modal)
    // ========================================================================
    
    function initModalEscHandler() {
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                // Close all modals
                document.querySelectorAll('.modal.active').forEach(modal => {
                    modal.classList.remove('active');
                });
                
                // Close mobile menus
                document.querySelectorAll('.menu.active, .menu-about.active, .menu-contact.active, .menu-order.active, .menu-status.active, .menu-tour.active').forEach(menu => {
                    menu.classList.remove('active');
                });
                
                document.querySelectorAll('.mobile-menu-toggle.active').forEach(toggle => {
                    toggle.classList.remove('active');
                });
                
                // Close admin sidebar
                const sidebar = document.querySelector('.sidebar.active');
                if (sidebar) {
                    sidebar.classList.remove('active');
                    document.querySelector('.sidebar-backdrop')?.classList.remove('active');
                    document.querySelector('.hamburger-menu')?.classList.remove('active');
                }
                
                preventBodyScroll(false);
            }
        });
    }
    
    // ========================================================================
    // TEXTAREA AUTO-RESIZE (untuk special request, etc)
    // ========================================================================
    
    function initTextareaAutoResize() {
        const textareas = document.querySelectorAll('textarea');
        
        textareas.forEach(textarea => {
            textarea.addEventListener('input', function() {
                this.style.height = 'auto';
                this.style.height = (this.scrollHeight) + 'px';
            });
        });
    }
    
    // ========================================================================
    // INPUT ZOOM PREVENTION (iOS)
    // ========================================================================
    
    function preventInputZoom() {
        if (!isMobile()) return;
        
        const inputs = document.querySelectorAll('input, select, textarea');
        
        inputs.forEach(input => {
            const currentSize = window.getComputedStyle(input).fontSize;
            const sizeValue = parseFloat(currentSize);
            
            // iOS zooms if font-size < 16px
            if (sizeValue < 16) {
                input.style.fontSize = '16px';
            }
        });
    }
    
    // ========================================================================
    // SMOOTH SCROLL FOR ANCHOR LINKS
    // ========================================================================
    
    function initSmoothScroll() {
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                const href = this.getAttribute('href');
                if (href === '#') return;
                
                const target = document.querySelector(href);
                if (target) {
                    e.preventDefault();
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    }
    
    // ========================================================================
    // VIEWPORT HEIGHT FIX (untuk mobile browser dengan address bar)
    // ========================================================================
    
    function fixMobileViewportHeight() {
        if (!isMobile()) return;
        
        // Set CSS variable for real viewport height
        const setVh = () => {
            const vh = window.innerHeight * 0.01;
            document.documentElement.style.setProperty('--vh', `${vh}px`);
        };
        
        setVh();
        window.addEventListener('resize', setVh);
        window.addEventListener('orientationchange', setVh);
    }
    
    // ========================================================================
    // LAZY LOAD IMAGES (optional performance boost)
    // ========================================================================
    
    function initLazyLoad() {
        if ('IntersectionObserver' in window) {
            const images = document.querySelectorAll('img[data-src]');
            
            const imageObserver = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        img.src = img.dataset.src;
                        img.removeAttribute('data-src');
                        imageObserver.unobserve(img);
                    }
                });
            });
            
            images.forEach(img => imageObserver.observe(img));
        }
    }
    
    // ========================================================================
    // ORIENTATION CHANGE HANDLER
    // ========================================================================
    
    function handleOrientationChange() {
        window.addEventListener('orientationchange', function() {
            // Close all menus on orientation change
            document.querySelectorAll('.menu.active, .sidebar.active').forEach(el => {
                el.classList.remove('active');
            });
            
            document.querySelectorAll('.mobile-menu-toggle.active, .hamburger-menu.active').forEach(el => {
                el.classList.remove('active');
            });
            
            document.querySelector('.sidebar-backdrop')?.classList.remove('active');
            
            preventBodyScroll(false);
        });
    }
    
    // ========================================================================
    // INIT ALL ON DOM READY
    // ========================================================================
    
    function init() {
        // Wait for DOM
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', init);
            return;
        }
        
        // Initialize all features
        initAdminSidebar();
        initUserMenu();
        initTableScrollIndicator();
        initModalEscHandler();
        initTextareaAutoResize();
        preventInputZoom();
        initSmoothScroll();
        fixMobileViewportHeight();
        initLazyLoad();
        handleOrientationChange();
        
        console.log('✅ JourneyMerapi Responsive System Initialized');
    }
    
    // Start initialization
    init();
    
})();