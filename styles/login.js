// login.js - Versión mejorada
document.addEventListener('DOMContentLoaded', function() {
    // Elementos del DOM
    const modal = document.getElementById('loginModal');
    const loginBtnNav = document.getElementById('loginBtnNav');
    const loginBtnHero = document.getElementById('loginBtnHero');
    const closeModal = document.querySelector('.close-modal');
    const loginForm = document.getElementById('loginForm');
    const forgotPassword = document.getElementById('forgotPassword');
    const usernameInput = document.getElementById('username');
    const passwordInput = document.getElementById('password');

    // Estados
    let loginAttempts = 0;
    const MAX_ATTEMPTS = 3;

    // Mostrar modal con animación
    function openModal() {
        modal.style.display = 'block';
        setTimeout(() => {
            modal.style.opacity = '1';
            modal.querySelector('.modal-content').style.transform = 'translateY(0)';
        }, 10);
        usernameInput.focus();
    }

    // Cerrar modal con animación
    function closeModalFunc() {
        modal.style.opacity = '0';
        modal.querySelector('.modal-content').style.transform = 'translateY(-20px)';
        setTimeout(() => {
            modal.style.display = 'none';
            loginForm.reset();
        }, 300);
    }

    // Event listeners
    loginBtnNav.addEventListener('click', function(e) {
        e.preventDefault();
        openModal();
    });

    loginBtnHero.addEventListener('click', function(e) {
        e.preventDefault();
        openModal();
    });

    closeModal.addEventListener('click', closeModalFunc);

    window.addEventListener('click', function(e) {
        if (e.target === modal) {
            closeModalFunc();
        }
    });

    // Validación en tiempo real
    usernameInput.addEventListener('input', validateFields);
    passwordInput.addEventListener('input', validateFields);

    function validateFields() {
        const usernameValid = usernameInput.value.trim().length > 0;
        const passwordValid = passwordInput.value.length > 0;
        
        if (usernameValid && passwordValid) {
            document.querySelector('.btn-submit').disabled = false;
        } else {
            document.querySelector('.btn-submit').disabled = true;
        }
    }

    // Manejar el envío del formulario
    loginForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const username = usernameInput.value.trim();
        const password = passwordInput.value;
        
        // Deshabilitar el botón durante el proceso
        const submitBtn = document.querySelector('.btn-submit');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Verificando...';
        
        try {
            // Simulación de petición a servidor con retraso
            const success = await simulateLoginRequest(username, password);
            
            if (success) {
                // Login exitoso
                showFeedback('success', 'Inicio de sesión exitoso');
                setTimeout(() => {
                    closeModalFunc();
                    updateUIAfterLogin(username);
                }, 1500);
            } else {
                // Login fallido
                loginAttempts++;
                showFeedback('error', `Credenciales incorrectas. Intentos restantes: ${MAX_ATTEMPTS - loginAttempts}`);
                
                if (loginAttempts >= MAX_ATTEMPTS) {
                    disableLoginTemporarily();
                }
            }
        } catch (error) {
            showFeedback('error', 'Error de conexión. Intente nuevamente.');
        } finally {
            submitBtn.disabled = false;
            submitBtn.textContent = 'Ingresar';
        }
    });

    // Simulación de petición al servidor
    function simulateLoginRequest(username, password) {
        return new Promise((resolve) => {
            setTimeout(() => {
                // Simulación: acepta cualquier usuario con contraseña "password123"
                resolve(password === 'password123');
            }, 1200);
        });
    }

    // Mostrar feedback al usuario
    function showFeedback(type, message) {
        const feedbackDiv = document.createElement('div');
        feedbackDiv.className = `feedback ${type}`;
        feedbackDiv.textContent = message;
        
        const existingFeedback = document.querySelector('.feedback');
        if (existingFeedback) {
            existingFeedback.remove();
        }
        
        loginForm.insertBefore(feedbackDiv, loginForm.firstChild);
        
        setTimeout(() => {
            feedbackDiv.style.opacity = '1';
        }, 10);
        
        // Eliminar el mensaje después de 5 segundos
        setTimeout(() => {
            if (feedbackDiv.parentNode === loginForm) {
                feedbackDiv.style.opacity = '0';
                setTimeout(() => {
                    feedbackDiv.remove();
                }, 300);
            }
        }, 5000);
    }

    // Deshabilitar login temporalmente después de muchos intentos
    function disableLoginTemporarily() {
        const submitBtn = document.querySelector('.btn-submit');
        submitBtn.disabled = true;
        
        let secondsLeft = 30;
        submitBtn.textContent = `Espere ${secondsLeft} segundos`;
        
        const countdown = setInterval(() => {
            secondsLeft--;
            submitBtn.textContent = `Espere ${secondsLeft} segundos`;
            
            if (secondsLeft <= 0) {
                clearInterval(countdown);
                submitBtn.disabled = false;
                submitBtn.textContent = 'Ingresar';
                loginAttempts = 0;
            }
        }, 1000);
    }

    // Actualizar UI después de login exitoso
    function updateUIAfterLogin(username) {
        // Cambiar botón de login por información de usuario
        loginBtnNav.innerHTML = `<i class="fas fa-user-circle"></i> ${username}`;
        loginBtnNav.classList.add('logged-in');
        
        // Ocultar botón de login en hero section
        loginBtnHero.style.display = 'none';
        
        // Mostrar botón de logout (podrías añadirlo al DOM)
        // Aquí podrías también actualizar otros elementos de la UI
    }

    // Manejar "olvidé mi contraseña"
    forgotPassword.addEventListener('click', function(e) {
        e.preventDefault();
        showFeedback('info', 'Por favor contacte al administrador del sistema.');
    });

    // Inicialización
    validateFields();
});