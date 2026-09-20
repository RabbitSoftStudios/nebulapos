/**
 * NebulaPOS - App JS Handler
 */

document.addEventListener('DOMContentLoaded', () => {
    // Manejador del Formulario de Registro
    const registerForm = document.getElementById('register-form');
    if (registerForm) {
        registerForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const alertBox = document.getElementById('register-alert');
            alertBox.classList.add('d-none');

            const formData = new FormData(registerForm);
            const data = Object.fromEntries(formData.entries());
            data.action = 'register';

            try {
                const response = await fetch('../backend/auth.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(data)
                });
                const result = await response.json();

                if (result.status === 'success') {
                    alertBox.className = 'alert alert-success';
                    alertBox.textContent = result.message + ' Redirigiendo al login...';
                    alertBox.classList.remove('d-none');
                    setTimeout(() => window.location.href = 'login.html', 1500);
                } else {
                    alertBox.className = 'alert alert-danger';
                    alertBox.textContent = result.message || 'Error al registrar usuario.';
                    alertBox.classList.remove('d-none');
                }
            } catch (err) {
                alertBox.className = 'alert alert-danger';
                alertBox.textContent = 'Error de conexión con el servidor.';
                alertBox.classList.remove('d-none');
            }
        });
    }

    // Manejador del Formulario de Login
    const loginForm = document.getElementById('login-form');
    if (loginForm) {
        loginForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const alertBox = document.getElementById('login-alert');
            alertBox.classList.add('d-none');

            const formData = new FormData(loginForm);
            const data = Object.fromEntries(formData.entries());
            data.action = 'login';

            try {
                const response = await fetch('../backend/auth.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(data)
                });
                const result = await response.json();

                if (result.status === 'success') {
                    alertBox.className = 'alert alert-success';
                    alertBox.textContent = result.message + ' ¡Bienvenido, ' + result.user.name + '!';
                    alertBox.classList.remove('d-none');
                    localStorage.setItem('nebulapos_user', JSON.stringify(result.user));
                    setTimeout(() => window.location.href = 'index.html', 1500);
                } else {
                    alertBox.className = 'alert alert-danger';
                    alertBox.textContent = result.message || 'Credenciales no válidas.';
                    alertBox.classList.remove('d-none');
                }
            } catch (err) {
                alertBox.className = 'alert alert-danger';
                alertBox.textContent = 'Error de conexión con el servidor.';
                alertBox.classList.remove('d-none');
            }
        });
    }

    // Manejador del Formulario de Pago Wompi
    const paymentForm = document.getElementById('payment-form');
    if (paymentForm) {
        paymentForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const alertBox = document.getElementById('payment-alert');
            alertBox.classList.add('d-none');

            const formData = new FormData(paymentForm);
            const data = Object.fromEntries(formData.entries());
            data.action = 'pay';

            try {
                const response = await fetch('../backend/wompi.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(data)
                });
                const result = await response.json();

                if (result.status === 'success') {
                    alertBox.className = 'alert alert-success';
                    alertBox.innerHTML = `<strong>¡Pago Exitoso!</strong><br>
                        Ref: ${result.transaction.reference}<br>
                        Monto: $${result.transaction.amount} ${result.transaction.currency}<br>
                        Estado: ${result.transaction.status}`;
                    alertBox.classList.remove('d-none');
                    paymentForm.reset();
                } else {
                    alertBox.className = 'alert alert-danger';
                    alertBox.textContent = result.message || 'Error al procesar el pago.';
                    alertBox.classList.remove('d-none');
                }
            } catch (err) {
                alertBox.className = 'alert alert-danger';
                alertBox.textContent = 'Error al comunicarse con la pasarela de pago.';
                alertBox.classList.remove('d-none');
            }
        });
    }
});
