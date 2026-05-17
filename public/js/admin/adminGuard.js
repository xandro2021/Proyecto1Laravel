// adminGuard.js
console.log('token:', localStorage.getItem('token'));
console.log('role:', localStorage.getItem('role'));

const token = localStorage.getItem('token');

const role = localStorage.getItem('role');

if (!token || role !== 'ADMIN') {

  console.log('Redirecting to login — no valid admin session');

  window.location.href = '/';
}

async function logout() {

  const token = localStorage.getItem('token');

  try {

    await fetch('/api/logout', {
      method: 'POST',

      headers: {
        'Authorization': 'Bearer ' + token,
        'Accept': 'application/json'
      }
    });

  } finally {

    localStorage.removeItem('token');

    localStorage.removeItem('role');

    localStorage.removeItem('user');

    window.location.href = '/';
  }
}
