<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>OfficeLend - Mis Préstamos</title>

    <script type="text/javascript" src="/js/usuario/userGuard.js"></script>
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />

    <!-- Google Fonts -->
    <link
        href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Inter:wght@400;500;600&display=swap"
        rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap"
        rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('css/user/historialSolicitudesUser.css') }}" type="text/css" media="screen" >
</head>

<body>
    <!-- historialSolicitudesUser.html -->
    <header class="sticky-top bg-white shadow-sm" style="backdrop-filter: blur(8px);">
        <div class="container py-3">
            <div class="d-flex justify-content-between align-items-center">
                <!-- Logo -->
                <div class="d-flex align-items-center gap-2">
                    <img alt="OfficeLend Logo" class="h-10 w-10 rounded-3" src="../img/logo.png">
                    <span class="fs-3 fw-bold text-custom-primary font-headline tracking-tight">OfficeLend</span>
                </div>

                <!-- Navegación desktop -->
                <nav class="d-none d-md-flex gap-4">
                    <a class="nav-link active text-custom-primary" href="/user/catalogo">Equipos</a>
                    <a class="nav-link text-custom-primary" href="/user/prestamos">Préstamos</a>
                    <!-- salir ir a login -->
                    <a class="nav-link text-custom-primary" href="#" onclick="logout()">Salir</a>
                </nav>

                <!-- Acciones usuario -->
                <div class="d-flex align-items-center gap-3">

                    <span id="viewModeLabel" class="small fw-semibold text-custom-primary">Usuario</span>

                    <a href="/user/catalogo" class="btn btn-link p-0 text-custom-primary">
                        <span id="viewModeIcon" class="material-symbols-outlined" style="font-size: 32px;">Usuario</span>
                    </a>

                    <button class="btn btn-link text-custom-primary p-2">
                        <span class="material-symbols-outlined">notifications</span>
                    </button>
                    <button class="btn btn-link text-custom-primary p-2">
                        <span class="material-symbols-outlined">settings</span>
                    </button>
                    <div class="rounded-circle overflow-hidden border border-light shadow-sm"
                        style="width: 40px; height: 40px;">
                        <img alt="User profile" class="w-100 h-100 object-fit-cover"
                            src="https://lh3.googleusercontent.com/aida-public/AB6AXuDw7nxzJSdrR71CDJcr7kC74LjSK0FQ4lqpD3NebHzuBF2OKLcobdpMmXLTfXGjKhnwCsGDV2mN9HPKDx-tqzf23jNvhXxFM75S6XKC3LG98PiUY7HxPnK2lIS1M_4GLStSAhndvd9Qn50PRYC5jcBbYgw4BSiKmrhfaCEilKQgy1fH7KuLwKDGqS4BSOc6v4w6xKL722wJvMxtVxo4_cE_vR5qmR6qfuI_PH9r7xPCSIJzDIrCuivh_EQ1OP5_X1nTqItkinNBbVc">
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- ── Main ── -->
    <main class="container py-5">

        <!-- Editorial Header -->
        <div class="mb-5">
            <h1 class="page-title">
                Mis Equipos Solicitados
            </h1>
            <p class="page-subtitle">
                Gestiona tus préstamos activos, revisa el historial de solicitudes y realiza el seguimiento de
                tus devoluciones pendientes en un solo lugar.
            </p>
        </div>

        <!-- Stat Cards -->
        <div class="row g-4 mb-5">
            <div class="col-12 col-md-4">
                <div class="stat-card">
                    <div>
                        <p class="stat-label">Solicitudes Pendientes</p>
                        <h3 id="pendientes" class="stat-number text-secondary-color">0</h3>
                    </div>
                    <div class="stat-icon secondary">
                        <span class="material-symbols-outlined">pending_actions</span>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="stat-card">
                    <div>
                        <p class="stat-label">Equipos en Uso</p>
                        <h3 id="aprobados" class="stat-number text-primary-color">0</h3>
                    </div>
                    <div class="stat-icon primary">
                        <span class="material-symbols-outlined">inventory_2</span>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="stat-card">
                    <div>
                        <p class="stat-label">Historial Completo</p>
                        <h3 id="totalPrestamos" class="stat-number text-surface-color">0</h3>
                    </div>
                    <div class="stat-icon surface">
                        <span class="material-symbols-outlined">history</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Loan History Table -->
        <div class="mb-4">
            <!-- Section header -->
            <div class="d-flex justify-content-between align-items-end border-bottom-subtle pb-3 mb-4">
                <h2 class="section-title mb-0">Registro de Préstamos</h2>
                <div class="d-flex gap-2">
                    <button class="btn-filter">Filtrar por fecha</button>
                    <button class="btn-filter">Exportar PDF</button>
                </div>
            </div>

            <!-- Table -->
            <div class="table-card">
                <div class="table-responsive">
                    <table class="loans-table">
                        <thead>
                            <tr>
                                <th>EQUIPO</th>
                                <th>SOLICITUD</th>
                                <th>DEVOLUCIÓN</th>
                                <th>ESTADO</th>
                                <th class="text-end">ACCIONES</th>
                            </tr>
                        </thead>
                        <tbody id="loansTableBody"> </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-between align-items-center mt-4">
                <p class="page-info mb-0">Mostrando 4 de 28 préstamos</p>
                <div class="d-flex gap-2" id="pagination"></div>
            </div>
        </div>

        <!-- Loan Track Banner -->
        <div class="loan-track-banner mt-5">
            <div class="banner-content">
                <h2>Próxima Devolución</h2>
                <div class="d-flex flex-column flex-md-row align-items-md-center gap-4">
                    <div class="flex-grow-1">
                        <div class="d-flex justify-content-between mb-3">
                            <span class="progress-label-equipment">MacBook Pro 14"</span>
                            <span>75% Tiempo Transcurrido</span>
                        </div>
                        <div class="progress-track">
                            <div class="progress-fill"></div>
                        </div>
                        <div class="progress-meta">
                            <span>Iniciado: 12 Oct</span>
                            <span>Entrega: 25 Oct (En 3 días)</span>
                        </div>
                    </div>
                    <button class="btn-extend">Solicitar Extensión</button>
                </div>
            </div>
        </div>

    </main>

    <!-- FAB -->
    <button class="fab" title="Nueva Solicitud">
        <span class="material-symbols-outlined">add</span>
        <span class="fab-tooltip">Nueva Solicitud</span>
    </button>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/js/usuario/historialSolicitudesUser.js"></script>
</body>

</html>
