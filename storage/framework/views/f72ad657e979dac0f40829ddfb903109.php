<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo $__env->yieldContent('title', 'Iniciar Sesión'); ?> - Sistema de Facturación</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.5.1/css/all.min.css">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <style>
        :root {
            --primary: #2563eb;
            --primary-dark: #1e40af;
            --bg-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        * { font-family: 'Figtree', sans-serif; }
        body {
            min-height: 100vh;
            background: var(--bg-gradient);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }
        .login-card {
            width: 100%;
            max-width: 420px;
            border: none;
            border-radius: 1rem;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            overflow: hidden;
        }
        .login-header {
            background: white;
            padding: 2.5rem 2rem 1rem;
            text-align: center;
        }
        .login-header .logo {
            width: 80px;
            height: 80px;
            background: var(--bg-gradient);
            border-radius: 20px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 2rem;
            margin-bottom: 1rem;
        }
        .login-body {
            padding: 1.5rem 2rem 2.5rem;
        }
        .form-control {
            padding: 0.75rem 1rem;
            border-radius: 0.5rem;
        }
        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 0.2rem rgba(37, 99, 235, 0.15);
        }
        .btn-primary {
            background-color: var(--primary);
            border-color: var(--primary);
            padding: 0.75rem;
            border-radius: 0.5rem;
            font-weight: 600;
        }
        .btn-primary:hover {
            background-color: var(--primary-dark);
            border-color: var(--primary-dark);
        }
        .input-group-text {
            background: #f8f9fa;
            border-right: none;
        }
        .input-group .form-control { border-left: none; }
        .input-group .form-control:focus { border-color: #ced4da; }
        .input-group:focus-within .input-group-text { border-color: var(--primary); }
    </style>
</head>
<body>
    <div class="card login-card">
        <div class="login-header">
            <div class="logo">
                <i class="fas fa-file-invoice-dollar"></i>
            </div>
            <h4 class="fw-bold mb-1">Sistema de Facturación</h4>
            <p class="text-muted mb-0">Iniciar sesión en su cuenta</p>
        </div>
        <div class="login-body">
            <?php echo $__env->yieldContent('content'); ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php /**PATH /var/www/html/resources/views/layouts/auth.blade.php ENDPATH**/ ?>