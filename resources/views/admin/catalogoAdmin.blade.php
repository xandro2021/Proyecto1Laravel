<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>OfficeLend - Catálogo de Equipos</title>
    <script type="text/javascript" src="/js/admin/adminGuard.js"></script>
    <!-- Bootstrap 5 CSS + Icons + Google Fonts -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link
        href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;600;700;800&family=Inter:wght@400;500;600;700&display=swap"
        rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap"
        rel="stylesheet" />
    <!-- Custom CSS to replicate Tailwind design system and overrides -->
    <link rel="stylesheet" href="{{ asset('css/admin/catalogoAdmin.css') }}">
</head>

<body>
    <!-- catalogoAdmin.html -->
    <header class="sticky-top bg-white shadow-sm" style="backdrop-filter: blur(8px)">
        <div class="container py-3">
            <div class="d-flex justify-content-between align-items-center">
                <!-- Logo -->
                <div class="d-flex align-items-center gap-2">
                    <img alt="OfficeLend Logo" class="h-10 w-10 rounded-3" src="../img/logo.png" />
                    <span class="fs-3 fw-bold text-custom-primary font-headline tracking-tight">OfficeLend</span>
                </div>

                <!-- Navegación desktop -->
                <nav class="d-none d-md-flex gap-4">
                    <a class="nav-link text-custom-primary" href="/admin/dashboard">Dashboard</a>
                    <a class="nav-link active text-custom-primary" href="/admin/catalogo">Equipos</a>
                    <a class="nav-link text-custom-primary" href="/admin/prestamos">Préstamos</a>
                    <!-- salir ir a login -->
                    <a class="nav-link text-custom-primary" href="#" onclick="logout()">Salir</a>
                </nav>

                <!-- Acciones usuario -->
                <div class="d-flex align-items-center gap-3">

                    <span id="viewModeLabel" class="small fw-semibold text-custom-primary"> Admin </span>

                    <a href="/user/catalogo" class="btn btn-link p-0 text-custom-primary">
                        <span id="viewModeIcon" class="material-symbols-outlined" style="font-size: 32px;"></span>
                    </a>

                    <button class="btn btn-link text-custom-primary p-2">
                        <span class="material-symbols-outlined">notifications</span>
                    </button>
                    <button type="button" class="btn btn-link text-custom-primary p-2" onclick="openSettings()"
                        title="Abrir configuración">
                        <span class="material-symbols-outlined fs-n3">settings</span>
                    </button>
                    <button id="menuToggleBtn" type="button"
                        class="btn btn-link p-0 rounded-circle overflow-hidden border border-light shadow-sm"
                        style="width: 40px; height: 40px;" onclick="toggleHeaderMenu()" title="Mostrar opciones">
                        <img alt="User profile" class="w-100 h-100 object-fit-cover"
                            src="https://lh3.googleusercontent.com/aida-public/AB6AXuDw7nxzJSdrR71CDJcr7kC74LjSK0FQ4lqpD3NebHzuBF2OKLcobdpMmXLTfXGjKhnwCsGDV2mN9HPKDx-tqzf23jNvhXxFM75S6XKC3LG98PiUY7HxPnK2lIS1M_4GLStSAhndvd9Qn50PRYC5jcBbYgw4BSiKmrhfaCEilKQgy1fH7KuLwKDGqS4BSOc6v4w6xKL722wJvMxtVxo4_cE_vR5qmR6qfuI_PH9r7xPCSIJzDIrCuivh_EQ1OP5_X1nTqItkinNBbVc">
                    </button>
                    <div id="headerMenu" class="bg-white border rounded-3 shadow-sm"
                        style="display:none; position:absolute; right:0; top:56px; min-width: 180px; z-index:1000;">
                        <a class="d-block px-3 py-2 text-decoration-none text-dark"
                            href="/admin/dashboard">Dashboard</a>
                        <a class="d-block px-3 py-2 text-decoration-none text-dark" href="/admin/catalogo">Equipos</a>
                        <a class="d-block px-3 py-2 text-decoration-none text-dark"
                            href="/admin/prestamos">Préstamos</a>
                        <div class="dropdown-divider"></div>
                        <button type="button" class="d-block w-100 text-start px-3 py-2 btn btn-link text-dark"
                            onclick="logout()">Salir</button>
                    </div>
                </div>
            </div>
        </div>
    </header>


    <!-- Main Content -->
    <main class="container py-5">
        <!-- Content Canvas (max-width container) -->
        <div class="space-y-8 d-flex flex-column gap-4">
            <!-- Header Section -->
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end gap-4">
                <div>
                    <h1 class="page-title">Gestión de Inventario</h1>
                    <p class="page-subtitle">
                        Supervisa y organiza los activos tecnológicos de la institución
                        con control total de estados y disponibilidad.
                    </p>
                </div>
                <a th:href="@{/admin/catalogo/nuevo}"
                    class="btn btn-custom-gradient d-inline-flex align-items-center gap-2 px-4 py-3 rounded-xl shadow-lg">
                    <span class="material-symbols-outlined">add_circle</span>
                    Agregar Nuevo Equipo
                </a>
            </div>

            <!-- Filters & Bento Insights row -->
            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="row g-4">
                        <div class="col-md-4">
                            <div class="bg-surface-container-low p-4 rounded-2xl border-bottom border-4 border-primary">
                                <p class="text-primary fw-bold text-uppercase mb-2"
                                    style="font-size: 0.7rem; letter-spacing: 0.05em">
                                    Total Equipos
                                </p>
                                <div class="d-flex align-items-end gap-2">
                                    <span id="totalEquipos" class="display-6 fw-black text-on-surface">0</span>
                                    <span class="text-success small fw-bold mb-1">+12% mes</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div
                                class="bg-surface-container-low p-4 rounded-2xl border-bottom border-4 border-secondary">
                                <p class="text-secondary fw-bold text-uppercase mb-2" style="font-size: 0.7rem">
                                    En Mantenimiento
                                </p>
                                <div class="d-flex align-items-end gap-2">
                                    <span id="enMantenimiento" class="display-6 fw-black text-on-surface">0</span>
                                    <span class="text-slate-400 small fw-medium mb-1">Requiere atención</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="bg-surface-container-low p-4 rounded-2xl border-bottom border-4 border-success">
                                <p class="text-success fw-bold text-uppercase mb-2" style="font-size: 0.7rem">
                                    Disponibilidad
                                </p>
                                <div class="d-flex align-items-end gap-2">
                                    <span id="disponibilidad" class="display-6 fw-black text-on-surface">0%</span>
                                    <span class="text-slate-400 small fw-medium mb-1">De la flota</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="bg-surface-container-lowest p-4 rounded-2xl shadow-sm">
                        <h3 class="fw-bold text-slate-800 fs-1-3rem d-flex align-items-center gap-2 mb-3">
                            <span class="material-symbols-outlined">filter_list</span>
                            Filtros de Vista
                        </h3>
                        <div class="mb-3">
                            <label class="text-uppercase text-slate-400 fw-bold">Categoría</label>
                            <select class="form-select mt-3 py-2 px-3 rounded-lg">
                                <option>Todas las categorías</option>
                                <option>Laptops</option>
                                <option>Periféricos</option>
                                <option>Mobiliario</option>
                            </select>
                        </div>
                        <div class="d-flex flex-wrap gap-2 pt-2">
                            <button class="btn btn-custom-primary rounded-pill px-3 py-1">
                                Todos
                            </button>
                            <button class="btn bg-slate-100 text-slate-600 rounded-pill px-3 py-1">
                                Disponibles
                            </button>
                            <button class="btn bg-slate-100 text-slate-600 rounded-pill px-3 py-1">
                                Ocupados
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Inventory Table Area -->
            <div class="bg-surface-container-lowest rounded-3xl shadow-xl overflow-hidden">
                <div class="table-responsive">
                    <table class="table table-custom align-middle w-100 mb-0">
                        <thead class="bg-slate-50/50">
                            <tr>
                                <th class="text-uppercase text-slate-400 fw-bold">
                                    Equipo e Identificación
                                </th>
                                <th class="text-uppercase text-slate-400 fw-bold">
                                    Categoría
                                </th>
                                <th class="text-uppercase text-slate-400 fw-bold">Estado</th>
                                <th class="text-uppercase text-slate-400 fw-bold text-center">
                                    Stock
                                </th>
                                <th class="text-uppercase text-slate-400 fw-bold text-end">
                                    Acciones
                                </th>
                            </tr>
                        </thead>
                        <tbody id="equipment-tbody" class="divide-y">
                            <!-- Los equipos se cargan dinámicamente -->
                        </tbody>
                    </table>
                </div>
                <!-- Pagination -->
                <div class="px-4 py-3 bg-slate-50 d-flex flex-wrap justify-content-between align-items-center">
                    <p id="pagination-info" class="text-slate-500 small mb-0"></p>
                    <div id="pagination-controls" class="d-flex gap-2">
                        <!-- Se genera dinámicamente -->
                    </div>
                </div>
            </div>

            <!-- Lend-Track custom component (progress) -->
            <div class="shadow-xl bg-surface-container-lowest p-4 p-lg-5 rounded-3xl">
                <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
                    <div>
                        <h3 class="fw-bold text-primary mb-1">
                            Estado de Renovación Anual
                        </h3>
                        <p class="text-slate-500 small mb-0">
                            Progreso de actualización de flota para el próximo ciclo
                            académico.
                        </p>
                    </div>
                    <div class="text-end">
                        <p class="display-6 fw-black text-primary mb-0">64%</p>
                        <p class="text-secondary fw-bold text-uppercase" style="font-size: 0.6rem">
                            Objetivo Q4
                        </p>
                    </div>
                </div>
                <div class="progress-custom w-100 mb-2">
                    <div class="progress-fill"></div>
                </div>
                <div class="d-flex justify-content-between small text-slate-400 fw-bold text-uppercase">
                    <span>Iniciado: Enero</span>
                    <span class="text-primary">Actual: Octubre</span>
                    <span>Meta: Diciembre</span>
                </div>
            </div>
        </div>
    </main>

    <!-- <script type="text/javascript" src="/js/toggleButtonViews.js"></script> -->
    <script src="/js/admin/catalogoAdmin.js"> </script>
    <!-- Bootstrap JS (optional for interactivity but not mandatory) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js">

    </script>
    <script>

        function toggleHeaderMenu() {
            const menu = document.getElementById('headerMenu');
            const button = document.getElementById('menuToggleBtn');
            if (!menu || !button) return;
            menu.style.display = menu.style.display === 'block' ? 'none' : 'block';
        }

        document.addEventListener('click', (event) => {
            const menu = document.getElementById('headerMenu');
            const button = document.getElementById('menuToggleBtn');
            if (!menu || !button) return;
            if (event.target.closest('#headerMenu') || event.target.closest('#menuToggleBtn')) return;
            menu.style.display = 'none';
        });

        fetch("http://localhost:8080/equipment", {
            headers: {
                "Authorization": "Bearer " + localStorage.getItem("jwt")
            }
        })
            .then(res => res.json())
            .then(data => {
                console.log(data);

                const total = data.length;

                const mantenimiento = data.filter(e => e.status === "MANTENIMIENTO").length;

                const disponibles = data.filter(e => e.status === "DISPONIBLE").length;

                const disponibilidad = total > 0
                    ? Math.round((disponibles / total) * 100)
                    : 0;


                document.getElementById("totalEquipos").innerText = total;
                document.getElementById("enMantenimiento").innerText = mantenimiento;
                document.getElementById("disponibilidad").innerText = disponibilidad + "%";
            })
            .catch(err => console.log(err));
    </script>
</body>

</html>
