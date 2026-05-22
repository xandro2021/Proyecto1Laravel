<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>OfficeLend Admin Dashboard</title>
    <script type="text/javascript" src="/js/admin/adminGuard.js"></script>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com" rel="preconnect" />
    <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;600;700;800&amp;family=Inter:wght@300;400;500;600&amp;display=swap"
        rel="stylesheet" />
    <link
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap"
        rel="stylesheet" />

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />

    <link rel="stylesheet" href="{{ asset('css/admin/dashboardAdmin.css') }}">
</head>

<body>
    <!-- dashboardAdmin.blade.php -->
    <header class="sticky-top bg-white shadow-sm" style="backdrop-filter: blur(8px)">
        <div class="container py-3">
            <div class="d-flex justify-content-between align-items-center">
                <!-- Logo -->
                <div class="d-flex align-items-center gap-2">
                    <img alt="OfficeLend logo" class="logo rounded-3" src="{{ asset('img/logo.png') }}" />
                    <span class="logo-title fw-bold text-custom-primary font-headline tracking-tight">OfficeLend</span>
                </div>

                <!-- Navegación desktop -->
                <nav class="d-none d-md-flex gap-4">
                    <a class="nav-link active text-custom-primary" href="/admin/dashboard">Dashboard</a>
                    <a class="nav-link text-custom-primary" href="/admin/catalogo">Equipos</a>
                    <a class="nav-link text-custom-primary" href="/admin/prestamos">Préstamos</a>
                    <!-- salir ir a login -->
                    <a class="nav-link text-custom-primary" href="#" onclick="logout()">Salir</a>
                </nav>

                <!-- Acciones usuario -->
                <div class="d-flex align-items-center gap-3 position-relative">

                    <span id="viewModeLabel" class="small fw-semibold text-custom-primary"> Admin </span>

                    <a href="/user/catalogo" class="btn btn-link p-0 text-custom-primary">
                        <span id="viewModeIcon" class="material-symbols-outlined" style="font-size: 32px;">swap_horiz</span>
                    </a>

                    <button type="button" class="btn btn-link text-custom-primary p-2" onclick="showNotifications()"
                        title="Ver notificaciones">
                        <span class="material-symbols-outlined fs-n3">notifications</span>
                    </button>
                    <button type="button" class="btn btn-link text-custom-primary p-2" onclick="openSettings()"
                        title="Abrir configuración">
                        <span class="material-symbols-outlined fs-n3">settings</span>
                    </button>
                    <button id="menuToggleBtn" type="button"
                        class="btn btn-link p-0 rounded-circle overflow-hidden border border-light shadow-sm"
                        style="width: 40px; height: 40px" onclick="toggleHeaderMenu()" title="Mostrar opciones">
                        <img alt="User profile" class="w-100 h-100 object-fit-cover"
                            src="https://lh3.googleusercontent.com/aida-public/AB6AXuDw7nxzJSdrR71CDJcr7kC74LjSK0FQ4lqpD3NebHzuBF2OKLcobdpMmXLTfXGjKhnwCsGDV2mN9HPKDx-tqzf23jNvhXxFM75S6XKC3LG98PiUY7HxPnK2lIS1M_4GLStSAhndvd9Qn50PRYC5jcBbYgw4BSiKmrhfaCEilKQgy1fH7KuLwKDGqS4BSOc6v4w6xKL722wJvMxtVxo4_cE_vR5qmR6qfuI_PH9r7xPCSIJzDIrCuivh_EQ1OP5_X1nTqItkinNBbVc" />
                    </button>
                    <div id="headerMenu" class="bg-white border rounded-3 shadow-sm" style="
                display: none;
                position: absolute;
                right: 0;
                top: 56px;
                min-width: 180px;
                z-index: 1000;
              ">
                        <a class="d-block px-3 py-2 text-decoration-none text-dark"
                            href="/admin/dashboard">Dashboard</a>
                        <a class="d-block px-3 py-2 text-decoration-none text-dark" href="/admin/catalogo">Equipos</a>
                        <a class="d-block px-3 py-2 text-decoration-none text-dark"
                            href="/admin/prestamos">Préstamos</a>
                        <button type="button" class="d-block w-100 text-start px-3 py-2 btn btn-link text-dark"
                            onclick="logout()">
                            Salir
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="container py-5">
        <div class="d-flex justify-content-between align-items-end mb-5">
            <div>
                <h1 class="page-title">Panel de Control</h1>
                <p class="page-subtitle">
                    Bienvenido, Admin. Aquí tienes un resumen de la gestión hoy.
                </p>
                <div class="card bg-light border-0 shadow-sm mt-4" style="max-width: 540px">
                    <div class="card-body p-4 d-flex align-items-center gap-4">
                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center"
                            style="width: 64px; height: 64px; font-size: 1.5rem">
                            MP
                        </div>
                        <div>
                            <h5 class="mb-1 fw-bold" id="userName">Cargando...</h5>
                            <p class="mb-1 text-muted" id="userRole">...</p>
                            <p class="mb-0 small text-muted" id="userEmail">...</p>
                        </div>
                    </div>
                </div>
                <div class="d-flex flex-wrap gap-3 mt-3">
                    <div class="card border-0 shadow-sm p-3 flex-fill" style="min-width: 170px">
                        <div class="text-muted small">Solicitudes en revisión</div>
                        <div class="fw-bold fs-3">8</div>
                    </div>
                    <div class="card border-0 shadow-sm p-3 flex-fill" style="min-width: 170px">
                        <div class="text-muted small">Reservas programadas</div>
                        <div class="fw-bold fs-3">14</div>
                    </div>
                    <div class="card border-0 shadow-sm p-3 flex-fill" style="min-width: 170px">
                        <div class="text-muted small">Alertas críticas</div>
                        <div class="fw-bold fs-3 text-danger">3</div>
                    </div>
                </div>
            </div>
            <div class="text-end">
                <div id="currentDate" class="text-muted fs-5 text-1-4rem"></div>
                <div class="fs-4 fw-bold text-custom-primary text-1-8rem">
                    Sede Central UCR
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="row g-4 mb-5">
            <div class="col-lg-3 col-md-6">
                <div class="card h-100 border-0 shadow-sm card-stat" style="border-left: 4px solid var(--primary)">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between mb-3">
                            <div class="p-3 bg-light rounded-3">
                                <span class="material-symbols-outlined fs-1 text-custom-primary">desktop_windows</span>
                            </div>
                            <span class="badge bg-success text-white align-self-start">+5%</span>
                        </div>
                        <h6 class="text-muted">Equipos Disponibles</h6>
                        <p id="equiposDisponibles" class="display-5 fw-bold mb-0">0</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="card h-100 border-0 shadow-sm card-stat" style="border-left: 4px solid #0d6efd">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between mb-3">
                            <div class="p-3 bg-light rounded-3">
                                <span
                                    class="material-symbols-outlined fs-1 text-custom-primary">assignment_turned_in</span>
                            </div>
                            <span class="badge bg-primary text-white align-self-start">Activos hoy</span>
                        </div>
                        <h6 class="text-muted">Préstamos Activos</h6>
                        <p id="prestamosActivos" class="display-5 fw-bold mb-0">0</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="card h-100 border-0 shadow-sm card-stat" style="
              border-left: 4px solid var(--secondary);
              background-color: rgba(254, 166, 25, 0.08);
            ">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between mb-3">
                            <div class="p-3 bg-light rounded-3">
                                <span class="material-symbols-outlined fs-1"
                                    style="color: var(--secondary)">pending_actions</span>
                            </div>
                            <span class="badge bg-warning text-dark align-self-start">URGENTE</span>
                        </div>
                        <h6 class="text-warning-emphasis">Solicitudes Pendientes</h6>
                        <p id="solicitudesPendientes" class="display-5 fw-bold mb-0" style="color: var(--secondary)">
                            0
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="card h-100 border-0 shadow-sm card-stat" style="
              border-left: 4px solid var(--error);
              background-color: rgba(186, 26, 26, 0.08);
            ">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between mb-3">
                            <div class="p-3 bg-light rounded-3">
                                <span class="material-symbols-outlined fs-1 text-danger">warning</span>
                            </div>
                            <span class="badge bg-danger text-white align-self-start">Acción Requerida</span>
                        </div>
                        <h6 class="text-danger-emphasis">Préstamos Devueltos</h6>
                        <p id="prestamosDevueltos" class="display-5 fw-bold mb-0 text-danger">0</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Admin Overview -->
        <div class="row g-4 mb-5">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <div
                            class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
                            <div>
                                <h4 class="fw-bold text-custom-primary mb-1">
                                    Resumen rápido del administrador
                                </h4>
                                <p class="text-muted mb-0">
                                    Un vistazo a prioridades del día y acciones recomendadas
                                    para mantener el inventario y las solicitudes bajo control.
                                </p>
                            </div>
                            <button class="btn btn-primary btn-sm">
                                Ver solicitudes pendientes
                            </button>
                        </div>
                        <div class="row text-center mt-4">
                            <div class="col-md-4 mb-3 mb-md-0">
                                <div class="p-3 rounded-4 bg-light">
                                    <div class="fw-bold fs-2 text-custom-primary">+12%</div>
                                    <div class="text-muted small">Crecimiento de préstamos</div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3 mb-md-0">
                                <div class="p-3 rounded-4 bg-light">
                                    <div class="fw-bold fs-2">5</div>
                                    <div class="text-muted small">Equipos en mantenimiento</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="p-3 rounded-4 bg-light">
                                    <div class="fw-bold fs-2 text-warning">2</div>
                                    <div class="text-muted small">Solicitudes urgentes</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts and Inventory -->
        <div class="row g-4 mb-5">
            <!-- Demand Trend -->
            <div class="col-12 col-lg-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4">

                        <h4 class="fw-bold text-custom-primary mb-4">
                            🏆 Top 3 equipos más prestados
                        </h4>

                        <div id="topEquipos"></div>

                    </div>
                </div>
            </div>

            <!-- Inventory Status -->
            <div class="col-12 col-lg-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4">
                        <h4 class="fw-bold text-custom-primary mb-4">
                            📦 Estado de Inventario Real
                        </h4>

                        <div class="mb-4">
                            <div class="d-flex justify-content-between mb-2">
                                <span>Equipos disponibles</span>
                                <span id="inv_disponibles" class="fw-bold">0</span>
                            </div>
                            <div class="progress" style="height: 8px">
                                <div id="bar_disponibles" class="progress-bar bg-primary"></div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <div class="d-flex justify-content-between mb-2">
                                <span>Préstamos activos</span>
                                <span id="inv_activos" class="fw-bold">0</span>
                            </div>
                            <div class="progress" style="height: 8px">
                                <div id="bar_activos" class="progress-bar bg-warning"></div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <div class="d-flex justify-content-between mb-2">
                                <span>Solicitudes pendientes</span>
                                <span id="inv_pendientes" class="fw-bold">0</span>
                            </div>
                            <div class="progress" style="height: 8px">
                                <div id="bar_pendientes" class="progress-bar bg-secondary"></div>
                            </div>
                        </div>

                        <div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Préstamos devueltos</span>
                                <span id="inv_devueltos" class="fw-bold">0</span>
                            </div>
                            <div class="progress" style="height: 8px">
                                <div id="bar_devueltos" class="progress-bar bg-success"></div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <button onclick="window.location.href = '/admin/catalogo'"
                class="btn btn-outline-primary w-100 py-3 text-1-4rem">
                Ver inventario completo
            </button>
        </div>
        </div>
        </div>
        </div>

        </div>



        <div class="row g-4 mb-5">

            <div class="col-md-4">
                <div class="card p-4 shadow-sm h-100">
                    <h5 class="fw-bold">📊 Actividad del día</h5>
                    <p class="text-muted">Hoy se han registrado solicitudes y movimientos en el sistema.</p>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card p-4 shadow-sm h-100">
                    <h5 class="fw-bold">⚠️ Alertas</h5>
                    <p class="text-muted">Revisa equipos en mantenimiento.</p>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card p-4 shadow-sm h-100">
                    <h5 class="fw-bold">📦 Inventario</h5>
                    <p class="text-muted">Estado general del inventario actualizado en tiempo real.</p>
                </div>
            </div>

        </div>

        <!-- Floating Action Button -->
        <button onclick="window.location.href = '/admin/prestamos'"
            class="btn btn-warning position-fixed bottom-0 end-0 m-4 rounded-circle shadow-lg d-flex align-items-center justify-content-center"
            style="width: 68px; height: 68px; z-index: 50">
            <span class="material-symbols-outlined fs-1 text-white">add</span>
        </button>

        <!-- Bootstrap JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
        <script
            src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.28/jspdf.plugin.autotable.min.js"></script>

        <script src="/js/admin/dashboardAdmin.js"></script>
</body>

</html>
