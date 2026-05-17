// Forzar recarga si viene del cache (botón atrás)
window.addEventListener("pageshow", function (event) {
  if (event.persisted) {
    window.location.reload();
  }
});

document.addEventListener("DOMContentLoaded", () => {
  const label = document.getElementById("viewModeLabel");
  const icon = document.getElementById("viewModeIcon");
  const toggleBtn = document.getElementById("toggleViewBtn");

  if (!label || !icon || !toggleBtn) return;

  // 1. Obtener modo desde localStorage
  let mode = localStorage.getItem("viewMode");

  // 2. Default si no existe
  if (!mode) {
    mode = "ADMIN";
    localStorage.setItem("viewMode", mode);
  }

  // 3. Renderizar UI
  function render(mode) {
    label.textContent = mode;
    icon.textContent = mode === "ADMIN" ? "toggle_on" : "toggle_off";
  }

  render(mode);

  // 4. Interceptar click del toggle
  toggleBtn.addEventListener("click", () => {
    let newMode = mode === "ADMIN" ? "USER" : "ADMIN";

    // Guardar en frontend
    localStorage.setItem("viewMode", newMode);

  });
});
