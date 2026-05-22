<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>OfficeLend - Gestión de Préstamos</title>

    <script type="text/javascript" src="/js/admin/adminGuard.js"></script>
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />

    <!-- Google Fonts -->
    <link
        href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Inter:wght@400;500;600&display=swap"
        rel="stylesheet" />

    <!-- Material Symbols -->
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap"
        rel="stylesheet" />

    <link rel="stylesheet" href="{{ asset('css/admin/gestionPrestamosAdmin.css') }}">
</head>

<body>
    <!-- gestionPrestamosAdmin.html -->
    <header class="sticky-top bg-white shadow-sm" style="backdrop-filter: blur(8px);">
        <div class="container py-3">
            <div class="d-flex justify-content-between align-items-center">
                <!-- Logo -->
                <div class="d-flex align-items-center gap-2">
                    <img alt="OfficeLend Logo" class="h-10 w-10 rounded-3" src="/img/logo.png">
                    <span class="fs-3 fw-bold text-custom-primary font-headline tracking-tight">OfficeLend</span>
                </div>

                <nav class="d-none d-md-flex gap-4">
                    <a class="nav-link text-custom-primary" href="/admin/dashboard">Dashboard</a>
                    <a class="nav-link text-custom-primary" href="/admin/catalogo">Equipos</a>
                    <a class="nav-link active text-custom-primary" href="/admin/prestamos">Préstamos</a>
                    <!-- salir ir a login -->
                    <a class="nav-link text-custom-primary" href="#" onclick="logout()">Salir</a>
                </nav>

                <!-- Acciones usuario -->
                <div class="d-flex align-items-center gap-3">

                    <span id="viewModeLabel" class="small fw-semibold text-custom-primary"> Admin </span>

                    <a href="/user/catalogo" class="btn btn-link p-0 text-custom-primary">
                        <span id="viewModeIcon" class="material-symbols-outlined" style="font-size: 32px;">swap_horiz</span>
                    </a>

                    <button class="btn btn-link text-custom-primary p-2">
                        <span class="material-symbols-outlined">notifications</span>
                    </button>
                      <button type="button" class="btn btn-link text-custom-primary p-2" onclick="openSettings()" title="Abrir configuración">
                        <span class="material-symbols-outlined fs-n3">settings</span>
                    </button>
                    <button id="menuToggleBtn" type="button" class="btn btn-link p-0 rounded-circle overflow-hidden border border-light shadow-sm" style="width: 40px; height: 40px;" onclick="toggleHeaderMenu()" title="Mostrar opciones">
                        <img alt="User profile" class="w-100 h-100 object-fit-cover"
                            src="https://lh3.googleusercontent.com/aida-public/AB6AXuDw7nxzJSdrR71CDJcr7kC74LjSK0FQ4lqpD3NebHzuBF2OKLcobdpMmXLTfXGjKhnwCsGDV2mN9HPKDx-tqzf23jNvhXxFM75S6XKC3LG98PiUY7HxPnK2lIS1M_4GLStSAhndvd9Qn50PRYC5jcBbYgw4BSiKmrhfaCEilKQgy1fH7KuLwKDGqS4BSOc6v4w6xKL722wJvMxtVxo4_cE_vR5qmR6qfuI_PH9r7xPCSIJzDIrCuivh_EQ1OP5_X1nTqItkinNBbVc">
                    </button>
                     <div id="headerMenu" class="bg-white border rounded-3 shadow-sm" style="display:none; position:absolute; right:0; top:56px; min-width: 180px; z-index:1000;">
                        <a class="d-block px-3 py-2 text-decoration-none text-dark" href="/admin/dashboard">Dashboard</a>
                        <a class="d-block px-3 py-2 text-decoration-none text-dark" href="/admin/catalogo">Equipos</a>
                        <a class="d-block px-3 py-2 text-decoration-none text-dark" href="/admin/prestamos">Préstamos</a>
                        <div class="dropdown-divider"></div>
                        <button type="button" class="d-block w-100 text-start px-3 py-2 btn btn-link text-dark" onclick="logout()">Salir</button>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- ── Main ── -->
    <main class="container py-5">
        <!-- Editorial Header -->
        <div class="mb-5 d-flex flex-column flex-md-row align-items-md-end justify-content-between gap-3">
            <div>
                <h1 class="page-title">Gestión de Préstamos</h1>
                <p class="page-subtitle mb-0">Administre las solicitudes de equipos institucionales y supervise el flujo
                    de activos de OfficeLend de manera eficiente.</p>
            </div>
            <div class="d-flex gap-2 flex-shrink-0">
                <button class="btn-outline-nav">
                    <span class="material-symbols-outlined" style="font-size:1rem;">filter_list</span>
                    Filtros
                </button>
                <button class="btn-primary-action">
                    <span class="material-symbols-outlined" style="font-size:1rem;">add</span>
                    Nueva Solicitud
                </button>
            </div>
        </div>

        <!-- Metric Cards -->
        <div class="row g-4 mb-5">
            <!-- Pendientes -->
            <div class="col-12 col-md-4">
                <div class="metric-card">
                    <div>
                        <p class="metric-label">Total Pendientes</p>
                        <h3 class="metric-value" style="color:var(--color-secondary);">12</h3>
                    </div>
                    <div class="metric-icon"
                        style="background:var(--color-secondary-fixed);color:var(--color-on-secondary-container);">
                        <span class="material-symbols-outlined">pending_actions</span>
                    </div>
                </div>
            </div>
            <!-- Aprobaciones -->
            <div class="col-12 col-md-4">
                <div class="metric-card">
                    <div>
                        <p class="metric-label">Aprobaciones Hoy</p>
                        <h3 class="metric-value" style="color:var(--color-primary);">08</h3>
                    </div>
                    <div class="metric-icon"
                        style="background:var(--color-primary-fixed);color:var(--color-on-primary-fixed);">
                        <span class="material-symbols-outlined">check_circle</span>
                    </div>
                </div>
            </div>
            <!-- Vencidas -->
            <div class="col-12 col-md-4">
                <div class="metric-card">
                    <div>
                        <p class="metric-label">Entregas Vencidas</p>
                        <h3 class="metric-value" style="color:var(--color-error);">03</h3>
                    </div>
                    <div class="metric-icon" style="background:#fee2e2;color:var(--color-error);">
                        <span class="material-symbols-outlined">running_with_errors</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Admin Data Table -->
        <div>

            <!-- Tab row -->
            <div class="d-flex justify-content-between align-items-end section-divider mb-4">
                <div class="d-flex gap-2 flex-wrap">
                    <button class="tab-pill active">Pendientes (12)</button>
                    <button class="tab-pill inactive">Aprobados</button>
                    <button class="tab-pill inactive">Devueltos</button>
                    <button class="tab-pill inactive">Vencidos</button>
                </div>
                <button class="btn-export">Exportar Listado</button>
            </div>

            <!-- Table -->
            <div class="data-table-wrapper mb-4 shadow-xl">
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>USUARIO</th>
                                <th>EQUIPO</th>
                                <th>FECHA SOLICITUD</th>
                                <th>ESTADO</th>
                                <th>ACCIONES</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            <div class="d-flex align-items-center justify-content-between mt-4">
                <p class="pagination-info mb-0">Mostrando 4 de 12 solicitudes pendientes</p>
                <div class="d-flex gap-2">
                    <button class="page-btn page-btn-arrow">
                        <span class="material-symbols-outlined">chevron_left</span>
                    </button>
                    <button class="page-btn page-btn-arrow">
                        <span class="material-symbols-outlined">chevron_right</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Performance Stats Footer -->
        <div class="row g-4 mt-4 mb-5">
            <!-- Tasa Aprobación -->
            <div class="col-12 col-md-6">
                <div class="perf-primary">
                    <div class="deco-circle"></div>
                    <div style="position:relative;z-index:1;">
                        <h2>Eficiencia de Respuesta</h2>
                        <div>
                            <div class="progress-label">
                                <span>Tasa de Aprobación</span>
                                <span>94.2%</span>
                            </div>
                            <div class="progress-track">
                                <div class="progress-fill" style="width:94%;"></div>
                            </div>
                            <div class="trend-label">
                                <span class="material-symbols-outlined" style="font-size:1rem;">trending_up</span>
                                Mejora del 2.4% con respecto al mes pasado
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Tiempo Promedio -->
            <div class="col-12 col-md-6">
                <div class="perf-secondary shadow-xl">
                    <div>
                        <h2>Tiempo Promedio</h2>
                        <p class="mb-0">El tiempo de respuesta ha disminuido significativamente.</p>
                    </div>
                    <div class="mt-4">
                        <div class="perf-time-value">4.5 hrs</div>
                        <p class="perf-goal mb-0">
                            <span class="material-symbols-outlined" style="font-size:1rem;">timer</span>
                            Objetivo Institucional: &lt; 6 hrs
                        </p>
                    </div>
                </div>
            </div>
        </div>

    </main>

    <!-- FAB -->
    <button class="fab" title="Reporte Mensual">
        <span class="material-symbols-outlined">analytics</span>
        <span class="fab-tooltip">Reporte Mensual</span>
    </button>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script src="/js/admin/gestionPrestamosAdmin.js"> </script>
</body>

</html>
