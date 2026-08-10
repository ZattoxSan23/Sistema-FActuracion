<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Sistema') - {{ config('app.name') }}</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.1/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">

    <style>
        :root {
            --sidebar-width: 250px;
            --sidebar-collapsed-width: 70px;
            --header-height: 60px;
            --primary: #2563eb;
            --sidebar-bg: #1e293b;
            --sidebar-color: #cbd5e1;
            --sidebar-active: #3b82f6;
        }

        body {
            font-family: 'Figtree', system-ui, -apple-system, sans-serif;
            background: #f1f5f9;
            margin: 0;
        }

        /* ===== Sidebar ===== */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: var(--sidebar-width);
            height: 100vh;
            background: var(--sidebar-bg);
            color: var(--sidebar-color);
            overflow-y: auto;
            transition: all 0.3s;
            z-index: 1040;
        }
        .sidebar-brand {
            padding: 1rem 1.25rem;
            border-bottom: 1px solid #334155;
            color: white;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-weight: 600;
            font-size: 1.1rem;
        }
        .sidebar-brand i { font-size: 1.5rem; color: var(--sidebar-active); }
        .sidebar-section {
            padding: 0.75rem 1rem 0.25rem;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            color: #64748b;
            letter-spacing: 0.05em;
        }
        .sidebar-menu { list-style: none; padding: 0; margin: 0; }
        .sidebar-menu a {
            display: flex;
            align-items: center;
            gap: 0.85rem;
            padding: 0.7rem 1.25rem;
            color: var(--sidebar-color);
            text-decoration: none;
            transition: all 0.15s;
            border-left: 3px solid transparent;
        }
        .sidebar-menu a:hover {
            background: #334155;
            color: white;
        }
        .sidebar-menu a.active {
            background: #334155;
            color: white;
            border-left-color: var(--sidebar-active);
        }
        .sidebar-menu i { width: 20px; text-align: center; }

        /* ===== Main wrapper ===== */
        .main-wrapper {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
        }

        /* ===== Header ===== */
        .header {
            background: white;
            height: var(--header-height);
            display: flex;
            align-items: center;
            padding: 0 1.5rem;
            border-bottom: 1px solid #e2e8f0;
            position: sticky;
            top: 0;
            z-index: 1030;
        }
        .header h1 {
            font-size: 1.25rem;
            margin: 0;
            font-weight: 600;
        }

        /* ===== Content ===== */
        .content {
            padding: 1.5rem;
        }
        .page-title {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }
        .page-title h2 {
            margin: 0;
            font-size: 1.5rem;
            font-weight: 700;
            color: #1e293b;
        }
        .breadcrumb {
            background: transparent;
            padding: 0;
            margin: 0;
        }

        /* ===== Cards ===== */
        .card {
            border: none;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            border-radius: 0.5rem;
        }
        .card-header {
            background: white;
            border-bottom: 1px solid #e2e8f0;
            padding: 1rem 1.25rem;
            font-weight: 600;
        }

        /* ===== Stat cards ===== */
        .stat-card {
            background: white;
            border-radius: 0.5rem;
            padding: 1.25rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }
        .stat-card .icon {
            width: 48px;
            height: 48px;
            border-radius: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.25rem;
        }
        .stat-card h6 {
            margin: 0.5rem 0 0.25rem;
            color: #64748b;
            font-size: 0.8rem;
            text-transform: uppercase;
            font-weight: 500;
        }
        .stat-card .value {
            font-size: 1.5rem;
            font-weight: 700;
            color: #1e293b;
        }

        /* ===== Avatar ===== */
        .user-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: var(--primary);
            color: white;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 0.85rem;
        }

        /* ===== Alerts ===== */
        .alert-dismissible { padding-right: 3rem; }

        /* ===== DataTables ===== */
        .dataTables_wrapper .dataTables_filter input { border-radius: 0.375rem; }

        /* ===== Print ===== */
        @media print {
            .sidebar, .header, .no-print { display: none !important; }
            .main-wrapper { margin-left: 0; }
            .content { padding: 0; }
        }
    </style>

    @stack('styles')
</head>
<body>
    @include('layouts.partials.sidebar')

    <div class="main-wrapper">
        @include('layouts.partials.header')

        <main class="content">
            @include('layouts.partials.alerts')

            @yield('content')
        </main>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.1/dist/sweetalert2.all.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

    @stack('scripts')
</body>
</html>
