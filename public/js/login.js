// login.js
localStorage.clear()

document.getElementById('togglePassword').addEventListener('click', () => {

  const pwd = document.getElementById('password');

  pwd.type = pwd.type === 'password'
    ? 'text'
    : 'password';
});

document.getElementById('loginForm').addEventListener('submit', async (e) => {

  e.preventDefault();

  const username = document.getElementById('username').value;

  const password = document.getElementById('password').value;

  const errorMsg = document.getElementById('errorMsg');

  errorMsg.classList.add('d-none');

  try {

    const response = await fetch('/api/login', {
      method: 'POST',

      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      },

      body: JSON.stringify({
        username,
        password
      })
    });

    if (!response.ok) {

      errorMsg.classList.remove('d-none');

      return;
    }

    // ← volver a usar json
    const data = await response.json();

    console.log('Login response:', data);

    // Guardar token
    localStorage.setItem('token', data.token);

    // Guardar usuario
    localStorage.setItem('user', JSON.stringify(data.user));

    // Guardar rol
    localStorage.setItem('role', data.user.role);

    // Redirect según rol
    if (data.user.role === 'ADMIN') {

      window.location.href = '/admin/dashboard';

    } else {

      window.location.href = '/user/catalogo';
    }

  } catch (err) {

    console.error(err);

    errorMsg.classList.remove('d-none');
  }
});
