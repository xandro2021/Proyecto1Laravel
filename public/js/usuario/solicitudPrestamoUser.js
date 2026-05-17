// solicitudPrestamoUser.js
const API_URL = "/loans";

document.addEventListener("DOMContentLoaded", () => {

  cargarEquipo();

  const form = document.querySelector("form");
  const today = new Date().toISOString().split("T")[0];
  document.getElementById("fecha-devolucion").min = today;

  form.addEventListener("submit", async (e) => {
    e.preventDefault();

    try {
      const token = localStorage.getItem("jwt");

      // 1. obtener id del equipo desde la URL
      const pathParts = window.location.pathname.split("/");

      const equipmentId = parseInt(pathParts[pathParts.length - 1]);

      if (isNaN(equipmentId)) {
        alert("ID de equipo inválido");
        return;
      }

      // 2. obtener datos del formulario
      const justification = document.getElementById("justificacion").value;
      const requestDate = today;
      const estimatedEndDate = document.getElementById("fecha-devolucion").value;
      const compliance = document.getElementById("compliance").checked;

      if (!compliance) {
        alert("Debe aceptar los términos");
        return;
      }

      if (!justification || !estimatedEndDate) {
        alert("Complete todos los campos");
        return;
      }

      // 3. construir objeto
      const loanData = {
        requestDate,
        estimatedEndDate,
        justification,
        equipment: { id: equipmentId }
      };

      // 4. request
      const res = await fetch(API_URL, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          "Authorization": "Bearer " + token
        },
        body: JSON.stringify(loanData)
      });

      if (!res.ok) {
        throw new Error("Error al crear solicitud");
      }

      // 5. éxito
      alert("Solicitud enviada correctamente");

      window.location.href = "/user/prestamos";

    } catch (err) {
      console.error(err);
      alert("Error al enviar la solicitud");
    }
  });
});

async function cargarEquipo() {
  // Inicio de cargar datos del equipo
  const token = localStorage.getItem("jwt");
  const pathParts = window.location.pathname.split("/");
  const equipmentId = parseInt(pathParts[pathParts.length - 1]);

  if (isNaN(equipmentId)) {
    alert("ID de equipo inválido");
    return;
  }

  // FETCH DEL EQUIPO
  try {
    const res = await fetch(`/equipment/${equipmentId}`, {
      headers: {
        "Authorization": "Bearer " + token
      }
    });

    if (!res.ok) {
      throw new Error("No se pudo cargar el equipo");
    }

    const equipment = await res.json();

    // actualizar HTML dinámicamente
    document.getElementById("equipment-name").textContent = equipment.name;
    document.getElementById("equipment-id").textContent = "ID: " + equipment.id;
    document.getElementById("equipment-type").textContent = equipment.type;

    if (equipment.imageFilename) {
      document.getElementById("equipment-image").src = `/uploads/equipment/${equipment.imageFilename}`;
    }

  } catch (err) {
    console.error(err);
    alert("Error cargando equipo");
  }
  // Fin de cargar datos del equipo
}
