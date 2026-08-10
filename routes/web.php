<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - Sistema de Facturación Electrónica
|--------------------------------------------------------------------------
*/

// Rutas públicas (sin autenticación)
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.attempt');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Rutas protegidas (requieren autenticación)
Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/', fn () => redirect()->route('dashboard'));
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ==========================
    // MÓDULO CAJA (todos los roles)
    // ==========================
    Route::prefix('caja')->name('caja.')->middleware(['role:administrador,cajera'])->group(function () {
        Route::get('/', [\App\Http\Controllers\CajaController::class, 'index'])->name('index');
        Route::get('apertura', [\App\Http\Controllers\CajaController::class, 'apertura'])->name('apertura');
        Route::post('apertura', [\App\Http\Controllers\CajaController::class, 'storeApertura'])->name('apertura.store');
        Route::get('cierre/{caja}', [\App\Http\Controllers\CajaController::class, 'cierre'])->name('cierre');
        Route::post('cierre/{caja}', [\App\Http\Controllers\CajaController::class, 'storeCierre'])->name('cierre.store');
        Route::get('movimientos/{caja}', [\App\Http\Controllers\CajaController::class, 'movimientos'])->name('movimientos');
        Route::post('movimiento-rapido', [\App\Http\Controllers\CajaController::class, 'movimientoRapido'])->name('movimiento.rapido');
        Route::get('reporte/{caja}/pdf', [\App\Http\Controllers\CajaController::class, 'reportePdf'])->name('reporte.pdf');
        Route::get('{caja}/excel', [\App\Http\Controllers\CajaController::class, 'exportExcel'])->name('excel');
    });

    // ==========================
    // MÓDULO ARQUEO DE CAJA
    // ==========================
    Route::middleware(['role:administrador,cajera'])->prefix('arqueo')->name('arqueo.')->group(function () {
        Route::get('caja/{caja}', [\App\Http\Controllers\ArqueoController::class, 'create'])->name('create');
        Route::post('caja/{caja}', [\App\Http\Controllers\ArqueoController::class, 'store'])->name('store');
        Route::get('{caja}/pdf', [\App\Http\Controllers\ArqueoController::class, 'imprimir'])->name('pdf');
    });

    // ==========================
    // PUNTO DE VENTA (cajera y admin)
    // ==========================
    Route::middleware(['role:administrador,cajera', 'caja.abierta'])->prefix('pos')->name('pos.')->group(function () {
        Route::get('/', [\App\Http\Controllers\PosController::class, 'index'])->name('index');
        Route::get('buscar-productos', [\App\Http\Controllers\PosController::class, 'buscarProductos'])->name('buscar.productos');
        Route::get('buscar-clientes', [\App\Http\Controllers\PosController::class, 'buscarClientes'])->name('buscar.clientes');
        Route::post('cliente-rapido', [\App\Http\Controllers\PosController::class, 'clienteRapido'])->name('cliente.rapido');
        Route::post('venta', [\App\Http\Controllers\PosController::class, 'storeVenta'])->name('venta.store');
        Route::get('ticket/{venta}', [\App\Http\Controllers\PosController::class, 'imprimirTicket'])->name('ticket');
        Route::get('pdf/{venta}', [\App\Http\Controllers\PosController::class, 'imprimirPdf'])->name('pdf');
    });

    // ==========================
    // VENTAS - Listado (todos pueden ver)
    // ==========================
    Route::prefix('ventas')->name('ventas.')->group(function () {
        Route::get('/', [\App\Http\Controllers\VentaController::class, 'index'])->name('index');
        Route::get('{venta}', [\App\Http\Controllers\VentaController::class, 'show'])->name('show');
        Route::post('{venta}/anular', [\App\Http\Controllers\VentaController::class, 'anular'])->name('anular')->middleware('role:administrador');
        Route::post('{venta}/reenviar-sunat', [\App\Http\Controllers\VentaController::class, 'reenviarSunat'])->name('reenviar.sunat');
        Route::get('{venta}/pdf', [\App\Http\Controllers\VentaController::class, 'pdf'])->name('pdf');
        Route::get('{venta}/xml', [\App\Http\Controllers\VentaController::class, 'xml'])->name('xml');
    });

    // ==========================
    // PRODUCTOS
    // ==========================
    Route::prefix('productos')->name('productos.')->group(function () {
        Route::get('/', [\App\Http\Controllers\ProductoController::class, 'index'])->name('index');
        Route::get('crear', [\App\Http\Controllers\ProductoController::class, 'create'])->name('create')->middleware('role:administrador');
        Route::post('/', [\App\Http\Controllers\ProductoController::class, 'store'])->name('store')->middleware('role:administrador');
        Route::get('{producto}/editar', [\App\Http\Controllers\ProductoController::class, 'edit'])->name('edit')->middleware('role:administrador');
        Route::put('{producto}', [\App\Http\Controllers\ProductoController::class, 'update'])->name('update')->middleware('role:administrador');
        Route::delete('{producto}', [\App\Http\Controllers\ProductoController::class, 'destroy'])->name('destroy')->middleware('role:administrador');
        Route::get('buscar', [\App\Http\Controllers\ProductoController::class, 'buscar'])->name('buscar');
    });

    // ==========================
    // CATEGORÍAS
    // ==========================
    Route::prefix('categorias')->name('categorias.')->middleware('role:administrador')->group(function () {
        Route::get('/', [\App\Http\Controllers\CategoriaController::class, 'index'])->name('index');
        Route::post('/', [\App\Http\Controllers\CategoriaController::class, 'store'])->name('store');
        Route::put('{categoria}', [\App\Http\Controllers\CategoriaController::class, 'update'])->name('update');
        Route::delete('{categoria}', [\App\Http\Controllers\CategoriaController::class, 'destroy'])->name('destroy');
    });

    // ==========================
    // CLIENTES
    // ==========================
    Route::prefix('clientes')->name('clientes.')->group(function () {
        Route::get('/', [\App\Http\Controllers\ClienteController::class, 'index'])->name('index');
        Route::get('crear', [\App\Http\Controllers\ClienteController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\ClienteController::class, 'store'])->name('store');
        Route::get('{cliente}/editar', [\App\Http\Controllers\ClienteController::class, 'edit'])->name('edit');
        Route::put('{cliente}', [\App\Http\Controllers\ClienteController::class, 'update'])->name('update');
        Route::delete('{cliente}', [\App\Http\Controllers\ClienteController::class, 'destroy'])->name('destroy');
        Route::get('buscar', [\App\Http\Controllers\ClienteController::class, 'buscar'])->name('buscar');
    });

    // Consulta automática de DNI/RUC (Decolecta)
    Route::post('consulta-documento', [\App\Http\Controllers\ConsultaDocumentoController::class, 'show'])
        ->name('consulta.documento');

    // ==========================
    // REPORTES (admin y contador)
    // ==========================
    Route::middleware(['role:administrador,contador'])->prefix('reportes')->name('reportes.')->group(function () {
        Route::get('/', [\App\Http\Controllers\ReporteController::class, 'index'])->name('index');
        Route::get('ventas', [\App\Http\Controllers\ReporteController::class, 'ventas'])->name('ventas');
        Route::get('ventas/excel', [\App\Http\Controllers\ReporteController::class, 'ventasExcel'])->name('ventas.excel');
        Route::get('ventas/pdf', [\App\Http\Controllers\ReporteController::class, 'ventasPdf'])->name('ventas.pdf');
        Route::get('libro-ventas', [\App\Http\Controllers\ReporteController::class, 'libroVentas'])->name('libro.ventas');
        Route::get('libro-ventas/pdf', [\App\Http\Controllers\ReporteController::class, 'libroVentasPdf'])->name('libro.ventas.pdf');
        Route::get('productos-mas-vendidos', [\App\Http\Controllers\ReporteController::class, 'productosMasVendidos'])->name('productos.mas.vendidos');
        Route::get('por-categoria', [\App\Http\Controllers\ReporteController::class, 'porCategoria'])->name('por.categoria');
        Route::get('por-vendedor', [\App\Http\Controllers\ReporteController::class, 'porVendedor'])->name('por.vendedor');
        Route::get('por-metodo-pago', [\App\Http\Controllers\ReporteController::class, 'porMetodoPago'])->name('por.metodo.pago');
        Route::get('diario', [\App\Http\Controllers\ReporteController::class, 'diario'])->name('diario');
        Route::get('mensual', [\App\Http\Controllers\ReporteController::class, 'mensual'])->name('mensual');
        Route::get('igv', [\App\Http\Controllers\ReporteController::class, 'igv'])->name('igv');

        // Reportes nuevos
        Route::get('flujo-caja', [\App\Http\Controllers\ReporteController::class, 'flujoCaja'])->name('flujo.caja');
        Route::get('flujo-caja/excel', [\App\Http\Controllers\ReporteController::class, 'flujoCajaExcel'])->name('flujo.caja.excel');
        Route::get('flujo-caja/pdf', [\App\Http\Controllers\ReporteController::class, 'flujoCajaPdf'])->name('flujo.caja.pdf');
        Route::get('resumen-diario', [\App\Http\Controllers\ReporteController::class, 'resumenDiario'])->name('resumen.diario');
        Route::get('resumen-diario/excel', [\App\Http\Controllers\ReporteController::class, 'resumenDiarioExcel'])->name('resumen.diario.excel');
        Route::get('resumen-diario/pdf', [\App\Http\Controllers\ReporteController::class, 'resumenDiarioPdf'])->name('resumen.diario.pdf');
        Route::get('cliente/{cliente}/cuenta', [\App\Http\Controllers\ReporteController::class, 'estadoCuentaCliente'])->name('cliente.cuenta');
        Route::get('cliente/{cliente}/cuenta/pdf', [\App\Http\Controllers\ReporteController::class, 'estadoCuentaClientePdf'])->name('cliente.cuenta.pdf');
        Route::get('cliente/{cliente}/cuenta/excel', [\App\Http\Controllers\ReporteController::class, 'estadoCuentaClienteExcel'])->name('cliente.cuenta.excel');

        Route::get('stock-critico', [\App\Http\Controllers\ReporteController::class, 'stockCritico'])->name('stock.critico');
        Route::get('top-clientes', [\App\Http\Controllers\ReporteController::class, 'topClientes'])->name('top.clientes');
        Route::get('margen-ganancia', [\App\Http\Controllers\ReporteController::class, 'margenGanancia'])->name('margen.ganancia');
    });

    // ==========================
    // SUNAT
    // ==========================
    Route::middleware(['role:administrador'])->prefix('sunat')->name('sunat.')->group(function () {
        Route::get('configuracion', [\App\Http\Controllers\SunatController::class, 'configuracion'])->name('configuracion');
        Route::post('configuracion', [\App\Http\Controllers\SunatController::class, 'guardarConfiguracion'])->name('configuracion.guardar');
        Route::post('probar-conexion', [\App\Http\Controllers\SunatController::class, 'probarConexion'])->name('probar.conexion');
        Route::get('comprobantes', [\App\Http\Controllers\SunatController::class, 'comprobantes'])->name('comprobantes');
        Route::get('comprobantes/{comprobante}', [\App\Http\Controllers\SunatController::class, 'verComprobante'])->name('comprobante');
        Route::post('reenviar/{comprobante}', [\App\Http\Controllers\SunatController::class, 'reenviar'])->name('reenviar');
        Route::get('respuestas', [\App\Http\Controllers\SunatController::class, 'respuestas'])->name('respuestas');
    });

    // ==========================
    // CONFIGURACIÓN (admin)
    // ==========================
    Route::middleware(['role:administrador'])->prefix('configuracion')->name('configuracion.')->group(function () {
        Route::get('/', [\App\Http\Controllers\ConfiguracionController::class, 'index'])->name('index');
        Route::put('empresa', [\App\Http\Controllers\ConfiguracionController::class, 'updateEmpresa'])->name('empresa.update');
        Route::post('series', [\App\Http\Controllers\ConfiguracionController::class, 'storeSerie'])->name('series.store');
        Route::put('series/{serie}', [\App\Http\Controllers\ConfiguracionController::class, 'updateSerie'])->name('series.update');
        Route::delete('series/{serie}', [\App\Http\Controllers\ConfiguracionController::class, 'destroySerie'])->name('series.destroy');
        Route::put('impresoras', [\App\Http\Controllers\ConfiguracionController::class, 'updateImpresoras'])->name('impresoras.update');
    });

    // ==========================
    // USUARIOS (admin)
    // ==========================
    Route::middleware(['role:administrador'])->prefix('usuarios')->name('usuarios.')->group(function () {
        Route::get('/', [\App\Http\Controllers\UsuarioController::class, 'index'])->name('index');
        Route::get('crear', [\App\Http\Controllers\UsuarioController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\UsuarioController::class, 'store'])->name('store');
        Route::get('{user}/editar', [\App\Http\Controllers\UsuarioController::class, 'edit'])->name('edit');
        Route::put('{user}', [\App\Http\Controllers\UsuarioController::class, 'update'])->name('update');
        Route::delete('{user}', [\App\Http\Controllers\UsuarioController::class, 'destroy'])->name('destroy');
        Route::post('{user}/toggle', [\App\Http\Controllers\UsuarioController::class, 'toggle'])->name('toggle');
    });

    // ==========================
    // MI PERFIL (todos)
    // ==========================
    Route::prefix('perfil')->name('perfil.')->group(function () {
        Route::get('/', [\App\Http\Controllers\PerfilController::class, 'edit'])->name('edit');
        Route::put('/', [\App\Http\Controllers\PerfilController::class, 'update'])->name('update');
        Route::put('password', [\App\Http\Controllers\PerfilController::class, 'updatePassword'])->name('password');
    });
});
