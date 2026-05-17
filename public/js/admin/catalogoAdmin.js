// catalogoAdmin.js
const API_BASE = 'http://localhost:8080';
const ITEMS_PER_PAGE = 4;

let allEquipment = [];
let currentPage = 1;

function getStatusBadge(status) {
  const badges = {
    DISPONIBLE: `
            <div class="d-flex align-items-center gap-2">
                <span class="status-dot status-dot-green animate-pulse"></span>
                <span class="small fw-bold text-green-700">Disponible</span>
            </div>`,
    OCUPADO: `
            <div class="d-flex align-items-center gap-2">
                <span class="status-dot status-dot-secondary"></span>
                <span class="small fw-bold text-secondary">Ocupado</span>
            </div>`,
    MANTENIMIENTO: `
            <div class="d-flex align-items-center gap-2">
                <span class="status-dot status-dot-slate"></span>
                <span class="small fw-bold text-slate-500">Mantenimiento</span>
            </div>`
  };

  return badges[status] ?? badges['DISPONIBLE'];
}

function renderTable(equipmentList) {
  const tbody = document.getElementById('equipment-tbody');
  tbody.innerHTML = '';

  if (equipmentList.length === 0) {
    tbody.innerHTML = `
            <tr>
                <td colspan="5" class="text-center text-slate-400 py-4">No hay equipos registrados.</td>
            </tr>`;
    return;
  }

  equipmentList.forEach(eq => {
    // ← Construir URL de imagen
    const imageUrl = eq.imageFilename
      ? `/uploads/equipment/${eq.imageFilename}`
      : 'https://via.placeholder.com/80?text=Sin+imagen';

    tbody.innerHTML += `
            <tr>
                <td class="py-4 px-4">
                    <div class="d-flex align-items-center gap-3">
                        <img class="table-img"
                             src="${imageUrl}"
                             alt="${eq.name}"
                             style="width:80px; height:80px; object-fit:cover; border-radius:8px; background:#f0f0f0;">
                        <div>
                            <p class="fw-bold text-slate-900 mb-0">${eq.name}</p>
                            <p class="text-slate-400 font-monospace small mb-0">ID: ${eq.id}</p>
                        </div>
                    </div>
                </td>
                <td><span class="badge-laptop">${eq.type}</span></td>
                <td>${getStatusBadge(eq.status)}</td>
                <td class="text-center">
                    <span class="fw-bold text-slate-900">${eq.stock}</span><br>
                    <span class="text-slate-400" style="font-size:0.6rem;">Unidades</span>
                </td>
                <td class="text-end">
                    <div class="d-flex justify-content-end gap-2">
                        <button class="btn p-1 rounded" onclick="editEquipment(${eq.id})">
                            <span class="material-symbols-outlined">edit</span>
                        </button>
                        <button class="btn p-1 rounded" onclick="deleteEquipment(${eq.id})">
                            <span class="material-symbols-outlined">delete</span>
                        </button>
                    </div>
                </td>
            </tr>`;
  });
}

// PAGINACION
function renderPagination(totalItems) {
  const totalPages = Math.ceil(totalItems / ITEMS_PER_PAGE);
  const container = document.getElementById('pagination-controls');
  const info = document.getElementById('pagination-info');

  const start = (currentPage - 1) * ITEMS_PER_PAGE + 1;
  const end = Math.min(currentPage * ITEMS_PER_PAGE, totalItems);
  info.innerHTML = `Mostrando <strong>${start} - ${end}</strong> de <strong>${totalItems}</strong> equipos`;

  container.innerHTML = '';

  // Botón anterior
  container.innerHTML += `
        <button class="btn btn-light border rounded ${currentPage === 1 ? 'disabled opacity-50' : ''}"
            onclick="changePage(${currentPage - 1})">
            <span class="material-symbols-outlined">chevron_left</span>
        </button>`;

  // Botones de página
  for (let i = 1; i <= totalPages; i++) {
    container.innerHTML += `
            <button class="btn ${i === currentPage ? 'btn-custom-primary fw-bold' : 'btn-light border'} rounded px-3 py-1"
                onclick="changePage(${i})">${i}</button>`;
  }

  // Botón siguiente
  container.innerHTML += `
        <button class="btn btn-light border rounded ${currentPage === totalPages ? 'disabled opacity-50' : ''}"
            onclick="changePage(${currentPage + 1})">
            <span class="material-symbols-outlined">chevron_right</span>
        </button>`;
}

function changePage(page) {
  const totalPages = Math.ceil(allEquipment.length / ITEMS_PER_PAGE);
  if (page < 1 || page > totalPages) return;
  currentPage = page;
  const start = (currentPage - 1) * ITEMS_PER_PAGE;
  renderTable(allEquipment.slice(start, start + ITEMS_PER_PAGE));
  renderPagination(allEquipment.length);
}

async function loadEquipment() {
  const token = localStorage.getItem('jwt');

  try {
    const response = await fetch(`${API_BASE}/equipment`, {
      headers: { 'Authorization': `Bearer ${token}` }
    });

    if (response.status === 401 || response.status === 403) {
      window.location.href = '/';
      return;
    }

    allEquipment = await response.json();
    allEquipment.reverse();
    changePage(1);

  } catch (error) {
    console.error('Error al cargar equipos:', error);
    document.getElementById('equipment-tbody').innerHTML = `
            <tr>
                <td colspan="5" class="text-center text-danger py-4">Error al conectar con el servidor.</td>
            </tr>`;
  }
}

function editEquipment(id) {
  window.location.href = `/admin/catalogo/editar/${id}`;
}

async function deleteEquipment(id) {
  if (!confirm('¿Estás seguro de que deseas eliminar este equipo?')) return;
  const token = localStorage.getItem('jwt');

  try {
    const response = await fetch(`${API_BASE}/equipment/${id}`, {
      method: 'DELETE',
      headers: { 'Authorization': `Bearer ${token}` }
    });

    if (response.ok) {
      allEquipment = allEquipment.filter(eq => eq.id !== id);
      changePage(currentPage);
      window.location.href = "/admin/catalogo";
    } else {
      alert('No se pudo eliminar el equipo.');
    }
  } catch (error) {
    console.error('Error al eliminar:', error);
  }
}

// inicio
document.addEventListener('DOMContentLoaded', loadEquipment);
