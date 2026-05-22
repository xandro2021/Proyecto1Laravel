// adminGuard.js

const token = localStorage.getItem("token");
const role = localStorage.getItem("role");

// Debug opcional
console.log("token:", token);
console.log("role:", role);

// ─────────────────────────────────────────────
// Verificar sesión admin
// ─────────────────────────────────────────────
function validateAdminSession() {
    if (!token || role !== "ADMIN") {
        console.warn(
            "Redirigiendo: sesión de administrador inválida."
        );

        clearSession();

        window.location.href = "/";
    }
}

// ─────────────────────────────────────────────
// Limpiar sesión
// ─────────────────────────────────────────────
function clearSession() {
    localStorage.removeItem("token");
    localStorage.removeItem("role");
    localStorage.removeItem("user");
}

// ─────────────────────────────────────────────
// Logout
// ─────────────────────────────────────────────
async function logout() {
    try {
        if (token) {
            await fetch("/api/logout", {
                method: "POST",
                headers: {
                    "Authorization": `Bearer ${token}`,
                    "Accept": "application/json"
                }
            });
        }
    } catch (error) {
        console.error("Error cerrando sesión:", error);
    } finally {
        clearSession();
        window.location.href = "/";
    }
}

// ─────────────────────────────────────────────
// Inicializar protección
// ─────────────────────────────────────────────
validateAdminSession();
