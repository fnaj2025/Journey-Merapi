/* ================== PREVENT DOUBLE LOADING ================== */
if (window.authJsLoaded) {
    console.warn('auth.js already loaded, skipping...');
} else {
    window.authJsLoaded = true;

/* ================== DARK MODE ================== */
const darkBtn = document.getElementById("darkModeToggle");

// Load dark mode preference
if (localStorage.getItem("darkMode") === "enabled") {
    document.body.classList.add("dark-mode");
    if (darkBtn) {
        darkBtn.textContent = "Light Mode";  
        darkBtn.classList.add('light-mode-btn');
    }
}

// button text 
if (darkBtn) {
    darkBtn.addEventListener("click", () => {
        document.body.classList.toggle("dark-mode");
        const isEnabled = document.body.classList.contains("dark-mode");
        localStorage.setItem("darkMode", isEnabled ? "enabled" : "disabled");
        
        // Update button 
        darkBtn.textContent = isEnabled ? "Light Mode" : "Dark Mode";
        
        if (isEnabled) {
            darkBtn.classList.add('light-mode-btn');
        } else {
            darkBtn.classList.remove('light-mode-btn');
        }
    });
}

/* ================== PAGE TRANSITION ================== */
document.body.classList.add("page-transition");

window.addEventListener("load", () => {
    setTimeout(() => document.body.classList.add("page-loaded"), 50);
});

document.querySelectorAll("a").forEach(link => {
    if (!link.href || link.target === "_blank" || link.href.includes("#")) return;

    link.addEventListener("click", function (e) {
        // Skip untuk logout dan submit form
        if (this.classList.contains('logout') || this.closest('form')) return;
        
        e.preventDefault();
        const url = this.href;
        document.body.classList.remove("page-loaded");
        setTimeout(() => window.location.href = url, 300);
    });
});

/* ================== MODAL SYSTEM ================== */
(function() {
    const loginModal = document.getElementById("loginModal");
    const registerModal = document.getElementById("registerModal");
    const openLogin = document.getElementById("openLogin");

    /* OPEN LOGIN */
    if (openLogin) {
        openLogin.addEventListener("click", () => {
            if (loginModal) loginModal.classList.add("active");
        });
    }

    /* CLOSE ALL */
    document.querySelectorAll(".close-btn").forEach(btn => {
        btn.addEventListener("click", () => {
            if (loginModal) loginModal.classList.remove("active");
            if (registerModal) registerModal.classList.remove("active");
        });
    });

    /* SWITCH LOGIN → REGISTER */
    document.querySelectorAll("[data-switch='register']").forEach(btn => {
        btn.addEventListener("click", e => {
            e.preventDefault();
            if (loginModal) loginModal.classList.remove("active");
            if (registerModal) registerModal.classList.add("active");
        });
    });

    /* SWITCH REGISTER → LOGIN */
    document.querySelectorAll("[data-switch='login']").forEach(btn => {
        btn.addEventListener("click", e => {
            e.preventDefault();
            if (registerModal) registerModal.classList.remove("active");
            if (loginModal) loginModal.classList.add("active");
        });
    });

    /* CLOSE IF CLICK OUTSIDE */
    window.addEventListener("click", e => {
        if (e.target === loginModal) loginModal.classList.remove("active");
        if (e.target === registerModal) registerModal.classList.remove("active");
    });
})();

/* ================== TOGGLE PASSWORD VISIBILITY ================== */
document.querySelectorAll(".toggle-password").forEach(toggle => {
    toggle.addEventListener("click", function() {
        const targetId = this.getAttribute("data-target");
        const passwordInput = document.getElementById(targetId);
        
        if (passwordInput && passwordInput.type === "password") {
            passwordInput.type = "text";
            this.classList.add("hide");
        } else if (passwordInput) {
            passwordInput.type = "password";
            this.classList.remove("hide");
        }
    });
});

/* ================== LOGIN REQUIRED NOTIFICATION ================== */
function showLoginRequiredNotification(message = 'Silakan login terlebih dahulu untuk melanjutkan.') {
    // Add animation styles if not exist
    if (!document.querySelector('style[data-login-notification]')) {
        const style = document.createElement('style');
        style.setAttribute('data-login-notification', 'true');
        style.textContent = `
            @keyframes slideDown {
                from { 
                    transform: translateX(-50%) translateY(-120px) scale(0.8); 
                    opacity: 0; 
                }
                to { 
                    transform: translateX(-50%) translateY(0) scale(1); 
                    opacity: 1; 
                }
            }
            @keyframes slideUp {
                from { 
                    transform: translateX(-50%) translateY(0) scale(1); 
                    opacity: 1; 
                }
                to { 
                    transform: translateX(-50%) translateY(-120px) scale(0.8); 
                    opacity: 0; 
                }
            }
        `;
        document.head.appendChild(style);
    }
    
    const notification = document.createElement('div');
    notification.style.cssText = `
        position: fixed;
        top: 80px;
        left: 50%;
        transform: translateX(-50%);
        background: linear-gradient(135deg, #ff6b35 0%, #ff8c42 100%);
        color: white;
        padding: 16px 32px;
        border-radius: 12px;
        box-shadow: 0 8px 24px rgba(255, 107, 53, 0.4);
        z-index: 9999;
        font-family: 'Poppins', sans-serif;
        font-size: 15px;
        font-weight: 600;
        text-align: center;
        min-width: 320px;
        max-width: 500px;
        animation: slideDown 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
        border: 2px solid rgba(255, 255, 255, 0.3);
    `;
    notification.innerHTML = `
        <div style="display: flex; align-items: center; gap: 12px; justify-content: center;">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"></circle>
                <line x1="12" y1="16" x2="12" y2="12"></line>
                <line x1="12" y1="8" x2="12.01" y2="8"></line>
            </svg>
            <span>${message}</span>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.animation = 'slideUp 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55)';
        setTimeout(() => notification.remove(), 400);
    }, 4000);
}

window.showLoginRequiredNotification = showLoginRequiredNotification;

/* ================== FORM STATE MANAGEMENT ================== */
// Simpan state form sebelum redirect ke login
function saveFormState(formId, formData) {
    sessionStorage.setItem(`form_state_${formId}`, JSON.stringify({
        data: formData,
        timestamp: Date.now(),
        page: window.location.pathname
    }));
}

// Restore form state setelah login
function restoreFormState(formId) {
    const saved = sessionStorage.getItem(`form_state_${formId}`);
    if (!saved) return null;
    
    try {
        const state = JSON.parse(saved);
        // Validasi: hanya restore jika < 30 menit
        if (Date.now() - state.timestamp < 30 * 60 * 1000) {
            return state.data;
        }
    } catch (e) {
        console.error('Error restoring form state:', e);
    }
    
    // Hapus data expired
    sessionStorage.removeItem(`form_state_${formId}`);
    return null;
}

// Clear form state setelah berhasil submit
function clearFormState(formId) {
    sessionStorage.removeItem(`form_state_${formId}`);
}

// Export functions
window.saveFormState = saveFormState;
window.restoreFormState = restoreFormState;
window.clearFormState = clearFormState;

/* ================== HANDLE TOUR ORDER (untuk tour.php) ================== */
function handleTourOrder(tourId, tourName) {
    console.log('handleTourOrder called:', tourId, tourName);
    
    // Simpan tour yang dipilih ke sessionStorage
    sessionStorage.setItem('pendingTourOrder', JSON.stringify({
        id: tourId,
        name: tourName,
        timestamp: Date.now()
    }));
    
    // Tampilkan notifikasi
    showLoginRequiredNotification('Silakan login terlebih dahulu untuk memesan tour.');
    
    // Buka modal login setelah delay kecil
    setTimeout(() => {
        const loginModal = document.getElementById('loginModal');
        if (loginModal) {
            loginModal.classList.add('active');
            console.log('Login modal opened');
        } else {
            console.error('Login modal not found!');
        }
    }, 600);
}

// Export function
window.handleTourOrder = handleTourOrder;

/* ================== AUTO OPEN LOGIN MODAL (dari register) ================== */
window.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    
    if (urlParams.get('action') === 'openLogin') {
        setTimeout(() => {
            const loginModal = document.getElementById('loginModal');
            if (loginModal) {
                loginModal.classList.add('active');
            }
        }, 500);
        
        // Bersihkan URL parameter
        window.history.replaceState({}, '', window.location.pathname);
    }
});

console.log('auth.js loaded successfully');
console.log('Available functions:', {
    handleTourOrder: typeof window.handleTourOrder,
    showLoginRequiredNotification: typeof window.showLoginRequiredNotification,
    saveFormState: typeof window.saveFormState,
    restoreFormState: typeof window.restoreFormState,
    clearFormState: typeof window.clearFormState
});

} // End of double-loading check