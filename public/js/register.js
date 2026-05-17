document.getElementById("registerForm").addEventListener("submit", function (e) {
    e.preventDefault();

    const name = document.getElementById("name").value.trim();
    const email = document.getElementById("email").value.trim();
    const username = document.getElementById("username").value.trim();
    const password = document.getElementById("password").value;
    const confirmPassword = document.getElementById("confirmPassword").value;
    const errorMsg = document.getElementById("errorMsg");

    errorMsg.classList.add("d-none");

    if (!name || !email || !username || !password) {
        errorMsg.textContent = "Todos los campos son obligatorios";
        errorMsg.classList.remove("d-none");
        return;
    }

    if (password !== confirmPassword) {
        errorMsg.textContent = "Las contraseñas no coinciden";
        errorMsg.classList.remove("d-none");
        return;
    }

    fetch("/auth/register", {
        method: "POST",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify({
            fullName: name,   
            email: email,
            username: username,
            password: password
        })
    })
    .then(res => {
        if (!res.ok) {
            return res.text().then(text => { throw new Error(text); });
        }
        return res.text();
    })
    .then(data => {
        alert(data);
        window.location.href = "/login";
    })
    .catch(err => {
        errorMsg.textContent = err.message || "Error al registrar";
        errorMsg.classList.remove("d-none");
        console.error(err);
    });
});