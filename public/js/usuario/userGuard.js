    // userGuard.js
    const token = localStorage.getItem("token");
    const role = localStorage.getItem("role");

    // Validar sesión
    if (!token || !["USER", "ADMIN"].includes(role)) {
        console.warn("Sesión inválida. Redirigiendo al login...");

        localStorage.removeItem("token");
        localStorage.removeItem("role");
        localStorage.removeItem("user");

        window.location.href = "/";
    }

    // Logout
    async function logout() {
        try {
            if (token) {
                await fetch(`http://127.0.0.1:8000/api/logout`, {
                    method: "POST",
                    headers: {
                        Authorization: `Bearer ${token}`,
                        Accept: "application/json"
                    }
                });
            }
        } catch (error) {
            console.error("Error cerrando sesión:", error);
        } finally {
            localStorage.removeItem("token");
            localStorage.removeItem("role");
            localStorage.removeItem("user");

            window.location.href = "/";
        }
    }
