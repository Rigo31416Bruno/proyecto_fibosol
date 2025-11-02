<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Usuario</title>
    <link rel="stylesheet" href="../styles/index.css">k
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Crear Cuenta</h1>
            <p>Únete a nuestra comunidad hoy</p>
        </div>

        <div class="form-container">
            <div class="message success-message" id="successMessage">
                ✓ ¡Cuenta creada exitosamente!
            </div>

            <div class="message error-message-general" id="errorMessage">
                Error al registrar. Intenta nuevamente.
            </div>

            <form id="registroForm" novalidate>
                <div class="form-group">
                    <label for="usuario">Usuario</label>
                    <input type="text" id="usuario" name="usuario" placeholder="Elige un usuario" required>
                    <div class="error-message"></div>
                </div>

                <div class="form-group">
                    <label for="email">Correo Electrónico</label>
                    <input type="email" id="email" name="email" placeholder="tu@email.com" required>
                    <div class="error-message"></div>
                </div>

                <div class="form-group">
                    <label for="contrasena">Contraseña</label>
                    <input type="password" id="contrasena" name="contrasena" placeholder="Mínimo 8 caracteres" required>
                    <span class="password-toggle" id="togglePassword">👁️</span>
                    <div class="password-strength">
                        <div class="strength-bar" id="strengthBar"></div>
                    </div>
                    <div class="error-message"></div>
                </div>

                <button type="submit" class="btn-submit" id="btnSubmit">
                    <span class="spinner"></span>
                    <span id="btnText">Crear Cuenta</span>
                </button>

                <div class="login-link">
                    ¿Ya tienes cuenta? <a href="login.php">Inicia sesión aquí</a>
                </div>
            </form>
        </div>
    </div>

    <script>
        const form = document.getElementById('registroForm');
        const btnSubmit = document.getElementById('btnSubmit');
        const btnText = document.getElementById('btnText');
        const successMessage = document.getElementById('successMessage');
        const errorMessage = document.getElementById('errorMessage');
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('contrasena');
        const strengthBar = document.getElementById('strengthBar');

        // Toggle password visibility
        togglePassword.addEventListener('click', () => {
            const type = passwordInput.type === 'password' ? 'text' : 'password';
            passwordInput.type = type;
            togglePassword.textContent = type === 'password' ? '👁️' : '🙈';
        });

        // Password strength indicator
        passwordInput.addEventListener('input', () => {
            const password = passwordInput.value;
            let strength = 'weak';
            
            if (password.length >= 8) {
                strength = 'weak';
                if (/[A-Z]/.test(password) && /[0-9]/.test(password)) {
                    strength = 'medium';
                }
                if (/[^A-Za-z0-9]/.test(password)) {
                    strength = 'strong';
                }
            }

            strengthBar.className = 'strength-bar ' + strength;
        });

        // Validation functions
        function validateField(field) {
            const value = field.value.trim();
            const container = field.parentElement;
            const errorDiv = container.querySelector('.error-message');
            let error = '';

            switch (field.id) {
                case 'usuario':
                    if (!value) error = 'El usuario es requerido';
                    else if (value.length < 3) error = 'Mínimo 3 caracteres';
                    else if (!/^[a-zA-Z0-9_-]+$/.test(value)) error = 'Solo letras, números, guiones y guiones bajos';
                    break;
                case 'email':
                    if (!value) error = 'El email es requerido';
                    else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value)) error = 'Email inválido';
                    break;
                case 'contrasena':
                    if (!value) error = 'La contraseña es requerida';
                    else if (value.length < 8) error = 'Mínimo 8 caracteres';
                    break;
            }

            if (error) {
                container.classList.add('error');
                errorDiv.textContent = error;
                return false;
            } else {
                container.classList.remove('error');
                errorDiv.textContent = '';
                return true;
            }
        }

        // Real-time validation
        form.querySelectorAll('input').forEach(field => {
            field.addEventListener('blur', () => validateField(field));
        });

        // Form submission
        form.addEventListener('submit', async (e) => {
            e.preventDefault();

            // Validate all fields
            const fields = form.querySelectorAll('input');
            let isValid = true;

            fields.forEach(field => {
                if (!validateField(field)) {
                    isValid = false;
                }
            });

            if (!isValid) return;

            // Prepare data
            const formData = new FormData(form);
            const data = Object.fromEntries(formData);

            // Show loading state
            btnSubmit.disabled = true;
            btnSubmit.classList.add('loading');
            errorMessage.classList.remove('show');
            successMessage.classList.remove('show');

            try {
                // AJAX request al backend PHP
                const response = await fetch('../backend/registro.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(data)
                });

                const result = await response.json();

                if (result.success) {
                    successMessage.classList.add('show');
                    form.reset();
                    strengthBar.className = 'strength-bar';

                    // Redirigir después de 2 segundos
                    setTimeout(() => {
                        window.location.href = 'login.php';
                    }, 2000);
                } else {
                    errorMessage.textContent = result.message || 'Error al registrar. Intenta nuevamente.';
                    errorMessage.classList.add('show');
                }

            } catch (error) {
                console.error('Error:', error);
                errorMessage.textContent = 'Error de conexión. Intenta nuevamente.';
                errorMessage.classList.add('show');
            } finally {
                btnSubmit.disabled = false;
                btnSubmit.classList.remove('loading');
            }
        });
    </script>
</body>
</html>