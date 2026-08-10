@extends('layouts.app')

@section('title', 'Punto de Venta')
@section('header', 'Punto de Venta')

@push('styles')
<style>
    .pos-container {
        display: grid;
        grid-template-columns: 1fr 420px;
        gap: 1rem;
        height: calc(100vh - 90px);
    }
    @media (max-width: 1200px) {
        .pos-container { grid-template-columns: 1fr; height: auto; }
    }

    /* === Sección izquierda: Productos === */
    .products-section {
        background: white;
        border-radius: 0.5rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }
    .search-bar {
        padding: 1rem;
        border-bottom: 1px solid #e2e8f0;
    }
    .search-bar input {
        font-size: 1rem;
        padding: 0.75rem 1rem;
    }
    .categories-tabs {
        padding: 0.5rem 1rem;
        border-bottom: 1px solid #e2e8f0;
        display: flex;
        gap: 0.5rem;
        overflow-x: auto;
        flex-shrink: 0;
    }
    .category-tab {
        padding: 0.5rem 1rem;
        background: #f1f5f9;
        border-radius: 2rem;
        cursor: pointer;
        white-space: nowrap;
        border: none;
        font-size: 0.875rem;
        font-weight: 500;
        color: #475569;
        transition: all 0.15s;
    }
    .category-tab:hover { background: #e2e8f0; }
    .category-tab.active {
        background: var(--primary);
        color: white;
    }
    .products-grid {
        flex: 1;
        overflow-y: auto;
        padding: 1rem;
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
        gap: 0.75rem;
        align-content: start;
    }
    .product-card {
        border: 1px solid #e2e8f0;
        border-radius: 0.5rem;
        padding: 0.75rem;
        cursor: pointer;
        transition: all 0.15s;
        background: white;
        text-align: center;
    }
    .product-card:hover {
        border-color: var(--primary);
        box-shadow: 0 4px 12px rgba(37,99,235,0.15);
        transform: translateY(-2px);
    }
    .product-card .product-name {
        font-weight: 500;
        font-size: 0.85rem;
        color: #1e293b;
        margin-bottom: 0.5rem;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        min-height: 2.5rem;
    }
    .product-card .product-code {
        font-size: 0.7rem;
        color: #94a3b8;
        margin-bottom: 0.25rem;
    }
    .product-card .product-price {
        font-weight: 700;
        color: var(--primary);
        font-size: 1.1rem;
    }

    /* === Sección derecha: Carrito === */
    .cart-section {
        background: white;
        border-radius: 0.5rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }
    .cart-header {
        padding: 1rem;
        border-bottom: 1px solid #e2e8f0;
        background: linear-gradient(135deg, #2563eb, #1e40af);
        color: white;
    }
    .cart-items {
        flex: 1;
        overflow-y: auto;
        padding: 0.5rem;
    }
    .cart-item {
        display: grid;
        grid-template-columns: 1fr auto;
        gap: 0.5rem;
        padding: 0.75rem;
        border-bottom: 1px solid #f1f5f9;
        align-items: center;
    }
    .cart-item .item-name {
        font-weight: 500;
        font-size: 0.9rem;
        color: #1e293b;
    }
    .cart-item .item-controls {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .qty-input {
        width: 50px;
        text-align: center;
        border: 1px solid #e2e8f0;
        border-radius: 0.25rem;
        padding: 0.25rem;
    }
    .qty-btn {
        width: 28px;
        height: 28px;
        border-radius: 0.25rem;
        border: 1px solid #e2e8f0;
        background: white;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .qty-btn:hover { background: #f1f5f9; }
    .cart-summary {
        padding: 1rem;
        border-top: 1px solid #e2e8f0;
        background: #f8fafc;
    }
    .summary-row {
        display: flex;
        justify-content: space-between;
        padding: 0.25rem 0;
        font-size: 0.875rem;
    }
    .summary-row.total {
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--primary);
        padding-top: 0.5rem;
        border-top: 1px solid #e2e8f0;
    }
    .empty-cart {
        text-align: center;
        padding: 3rem 1rem;
        color: #94a3b8;
    }
    .empty-cart i { font-size: 3rem; margin-bottom: 1rem; }

    .totals-box {
        background: #f8fafc;
        padding: 0.5rem 1rem;
        border-radius: 0.375rem;
        margin-bottom: 0.5rem;
    }

    /* Modal cliente */
    .modal-cliente-rapido .form-control { margin-bottom: 0.75rem; }

    /* Numpad */
    .numpad {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 0.25rem;
        margin-top: 0.5rem;
    }
    .numpad button {
        padding: 0.75rem;
        border: 1px solid #e2e8f0;
        background: white;
        border-radius: 0.375rem;
        font-size: 1rem;
        font-weight: 500;
        cursor: pointer;
    }
    .numpad button:hover { background: #f1f5f9; }
    .numpad button.action {
        background: var(--primary);
        color: white;
        grid-column: span 2;
    }
</style>
@endpush

@section('content')
<div class="pos-container">
    {{-- Productos --}}
    <div class="products-section">
        <div class="search-bar">
            <div class="input-group">
                <span class="input-group-text bg-white"><i class="fas fa-search text-muted"></i></span>
                <input type="text"
                       id="buscar-producto"
                       class="form-control"
                       placeholder="Buscar por nombre, código o código de barras..."
                       autocomplete="off">
                <button class="btn btn-outline-secondary" type="button" onclick="limpiarBusqueda()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>

        <div class="categories-tabs">
            <button class="category-tab active" data-categoria="">
                <i class="fas fa-th me-1"></i>Todos
            </button>
            @foreach($categorias as $categoria)
                <button class="category-tab"
                        data-categoria="{{ $categoria->id }}"
                        @if($categoria->color) style="--cat-color: {{ $categoria->color }}" @endif>
                    @if($categoria->icono)<i class="{{ $categoria->icono }} me-1"></i>@endif
                    {{ $categoria->nombre }}
                </button>
            @endforeach
        </div>

        <div class="products-grid" id="productos-grid">
            @foreach($productos as $producto)
                <div class="product-card"
                     data-id="{{ $producto->id }}"
                     data-codigo="{{ $producto->codigo }}"
                     data-codigo-barra="{{ $producto->codigo_barra }}"
                     data-nombre="{{ strtolower($producto->nombre) }}"
                     data-categoria="{{ $producto->categoria_id }}"
                     onclick="agregarProducto({{ $producto->id }}, '{{ addslashes($producto->nombre) }}', {{ $producto->precio_venta }}, {{ $producto->incluye_igv ? 'true' : 'false' }}, '{{ $producto->tipo_afectacion_igv }}', '{{ $producto->unidad_medida }}')">
                    <div class="product-code">{{ $producto->codigo }}</div>
                    <div class="product-name">{{ $producto->nombre }}</div>
                    <div class="product-price">S/ {{ number_format($producto->precio_venta, 2) }}</div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Carrito --}}
    <div class="cart-section">
        <div class="cart-header">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-shopping-cart me-2"></i>Carrito</h5>
                <button class="btn btn-sm btn-light" onclick="limpiarCarrito()">
                    <i class="fas fa-trash"></i> Limpiar
                </button>
            </div>
            <div class="mt-2">
                <select id="cliente-select" class="form-select form-select-sm" style="width: 100%;">
                    <option value="{{ $clienteDefecto->id }}" selected>
                        {{ $clienteDefecto->documento_completo }} - {{ $clienteDefecto->nombre_razon_social }}
                    </option>
                </select>
                <button class="btn btn-sm btn-light mt-1 w-100" onclick="abrirModalCliente()">
                    <i class="fas fa-user-plus me-1"></i>Cliente nuevo / Buscar
                </button>
            </div>
        </div>

        <div class="cart-items" id="cart-items">
            <div class="empty-cart">
                <i class="fas fa-shopping-basket"></i>
                <p>El carrito está vacío</p>
                <small>Haz clic en un producto para agregarlo</small>
            </div>
        </div>

        <div class="cart-summary">
            <div class="totals-box">
                <div class="summary-row">
                    <span>Op. Gravadas:</span>
                    <span id="subtotal-gravadas">S/ 0.00</span>
                </div>
                <div class="summary-row">
                    <span>Op. Exoneradas:</span>
                    <span id="subtotal-exoneradas">S/ 0.00</span>
                </div>
                <div class="summary-row">
                    <span>IGV (18%):</span>
                    <span id="igv-total">S/ 0.00</span>
                </div>
                <div class="summary-row total">
                    <span>TOTAL:</span>
                    <span id="total-venta">S/ 0.00</span>
                </div>
            </div>

            <div class="row g-2 mb-2">
                <div class="col-6">
                    <label class="form-label small mb-1">Tipo Comprobante</label>
                    <select id="tipo-comprobante" class="form-select form-select-sm">
                        <option value="03">BOLETA</option>
                        <option value="01">FACTURA</option>
                    </select>
                </div>
                <div class="col-6">
                    <label class="form-label small mb-1">Serie</label>
                    <select id="serie-comprobante" class="form-select form-select-sm">
                        @foreach($seriesBoleta as $serie)
                            <option value="{{ $serie->serie }}" data-tipo="03">
                                {{ $serie->serie }}
                            </option>
                        @endforeach
                        @foreach($seriesFactura as $serie)
                            <option value="{{ $serie->serie }}" data-tipo="01" style="display:none;">
                                {{ $serie->serie }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <button class="btn btn-success w-100 btn-lg" onclick="abrirPago()">
                <i class="fas fa-credit-card me-2"></i>PROCESAR VENTA
            </button>
        </div>
    </div>
</div>

{{-- Modal Cliente --}}
<div class="modal fade" id="modalCliente" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Buscar / Crear Cliente</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body modal-cliente-rapido">
                <div class="mb-3">
                    <label class="form-label">Buscar cliente existente</label>
                    <select id="cliente-buscar" class="form-select" style="width: 100%;"></select>
                </div>

                <hr>

                <h6>Cliente nuevo</h6>
                <div class="row g-2">
                    <div class="col-4">
                        <label class="form-label small">Tipo Doc.</label>
                        <select id="tipo-doc" class="form-select form-select-sm">
                            <option value="DNI">DNI</option>
                            <option value="RUC">RUC</option>
                            <option value="CE">CE</option>
                            <option value="PASAPORTE">PASAPORTE</option>
                        </select>
                    </div>
                    <div class="col-8">
                        <label class="form-label small">Número</label>
                        <div class="input-group input-group-sm">
                            <input type="text" id="numero-doc" class="form-control" maxlength="15">
                            <button type="button" class="btn btn-primary" id="pos-buscar-decolecta" title="Buscar en la API">
                                <i class="fas fa-search me-1"></i>Buscar
                            </button>
                        </div>
                    </div>
                </div>
                <div class="mt-2">
                    <label class="form-label small">Nombre / Razón Social</label>
                    <input type="text" id="nombre-cliente" class="form-control form-control-sm">
                </div>
                <div class="mt-2">
                    <label class="form-label small">Dirección (opcional)</label>
                    <input type="text" id="direccion-cliente" class="form-control form-control-sm">
                </div>
                <div class="mt-2" id="consulta-estado"></div>
                <button class="btn btn-primary w-100 mt-3" onclick="guardarClienteRapido()">
                    <i class="fas fa-save me-1"></i>Guardar Cliente
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Modal Pago --}}
<div class="modal fade" id="modalPago" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title"><i class="fas fa-cash-register me-2"></i>Procesar Pago</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>Total a pagar</h6>
                        <div class="display-5 fw-bold text-success mb-3" id="modal-total">S/ 0.00</div>

                        <div class="mb-3">
                            <label class="form-label">Método de pago</label>
                            <select id="metodo-pago" class="form-select" onchange="cambiarMetodoPago()">
                                <option value="efectivo">Efectivo</option>
                                <option value="tarjeta">Tarjeta</option>
                                <option value="yape">Yape</option>
                                <option value="plin">Plin</option>
                                <option value="transferencia">Transferencia</option>
                            </select>
                        </div>

                        <div id="pago-efectivo">
                            <label class="form-label">Monto recibido</label>
                            <input type="number" id="monto-recibido" class="form-control form-control-lg text-end" step="0.01" oninput="calcularVuelto()">
                            <div class="d-flex gap-2 mt-2 flex-wrap">
                                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="montoRapido(10)">+10</button>
                                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="montoRapido(20)">+20</button>
                                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="montoRapido(50)">+50</button>
                                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="montoRapido(100)">+100</button>
                                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="montoRapido(200)">+200</button>
                                <button type="button" class="btn btn-primary btn-sm" onclick="montoExacto()">Exacto</button>
                            </div>
                            <div class="mt-3 p-3 bg-warning-subtle rounded">
                                <small class="text-muted">Vuelto</small>
                                <div class="h3 mb-0 fw-bold text-warning" id="vuelto-calculado">S/ 0.00</div>
                            </div>
                        </div>

                        <div id="pago-electronico" style="display:none;">
                            <div class="mb-2">
                                <label class="form-label">Monto</label>
                                <input type="number" id="monto-electronico" class="form-control" step="0.01">
                            </div>
                            <div class="mb-2">
                                <label class="form-label">N° Operación / Voucher</label>
                                <input type="text" id="numero-operacion" class="form-control" placeholder="Opcional">
                            </div>
                            <div id="campo-tarjeta" style="display:none;">
                                <label class="form-label">Tipo Tarjeta</label>
                                <select id="tipo-tarjeta" class="form-select">
                                    <option value="debito">Débito</option>
                                    <option value="credito">Crédito</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <h6>Resumen</h6>
                        <div class="bg-light p-3 rounded mb-3" id="modal-resumen-items"></div>

                        <h6>Atajos de teclado</h6>
                        <div class="numpad">
                            <button onclick="tecladoNum('1')">1</button>
                            <button onclick="tecladoNum('2')">2</button>
                            <button onclick="tecladoNum('3')">3</button>
                            <button onclick="tecladoNum('A')" class="action">←</button>
                            <button onclick="tecladoNum('4')">4</button>
                            <button onclick="tecladoNum('5')">5</button>
                            <button onclick="tecladoNum('6')">6</button>
                            <button onclick="tecladoNum('C')" class="action">C</button>
                            <button onclick="tecladoNum('7')">7</button>
                            <button onclick="tecladoNum('8')">8</button>
                            <button onclick="tecladoNum('9')">9</button>
                            <button onclick="tecladoNum('0')" class="action" style="grid-column: span 2;">0</button>
                            <button onclick="tecladoNum('.')">.</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-success btn-lg" onclick="confirmarVenta()">
                    <i class="fas fa-check me-2"></i>CONFIRMAR VENTA
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const igvPorcentaje = {{ (float) ($empresa?->igv ?? 18) }};
let carrito = [];
let totalVenta = 0;
let categoriaSeleccionada = '';

const formatMoney = (v) => 'S/ ' + parseFloat(v).toFixed(2);

// === Búsqueda y filtrado de productos ===
document.getElementById('buscar-producto').addEventListener('input', filtrarProductos);
document.querySelectorAll('.category-tab').forEach(tab => {
    tab.addEventListener('click', function() {
        document.querySelectorAll('.category-tab').forEach(t => t.classList.remove('active'));
        this.classList.add('active');
        categoriaSeleccionada = this.dataset.categoria;
        filtrarProductos();
    });
});

function filtrarProductos() {
    const termino = document.getElementById('buscar-producto').value.toLowerCase();
    document.querySelectorAll('.product-card').forEach(card => {
        const nombre = card.dataset.nombre;
        const codigo = card.dataset.codigo.toLowerCase();
        const codigoBarra = (card.dataset.codigoBarra || '').toLowerCase();
        const categoria = card.dataset.categoria;

        const matchTexto = !termino || nombre.includes(termino) || codigo.includes(termino) || codigoBarra.includes(termino);
        const matchCategoria = !categoriaSeleccionada || categoria === categoriaSeleccionada;

        card.style.display = (matchTexto && matchCategoria) ? '' : 'none';
    });
}

function limpiarBusqueda() {
    document.getElementById('buscar-producto').value = '';
    filtrarProductos();
}

// === Carrito ===
function agregarProducto(id, nombre, precio, incluyeIgv, tipoAfectacion, unidadMedida) {
    const existente = carrito.find(item => item.producto_id === id);
    if (existente) {
        existente.cantidad += 1;
        existente.subtotal = existente.cantidad * existente.precio_unitario;
        existente.igv_item = existente.cantidad * (existente.precio_unitario_con_igv - existente.precio_unitario);
        existente.total_item = existente.cantidad * existente.precio_unitario_con_igv;
    } else {
        const precioSinIgv = incluyeIgv ? (precio / (1 + igvPorcentaje/100)) : precio;
        const igvUnitario = incluyeIgv ? (precio - precioSinIgv) : 0;
        carrito.push({
            producto_id: id,
            nombre: nombre,
            cantidad: 1,
            precio_unitario: precioSinIgv,
            precio_unitario_con_igv: precio,
            descuento: 0,
            subtotal: precioSinIgv,
            igv_item: igvUnitario,
            total_item: precio,
            tipo_afectacion_igv: tipoAfectacion,
            unidad_medida: unidadMedida,
            incluye_igv: incluyeIgv,
        });
    }
    renderCarrito();
}

function cambiarCantidad(index, nuevaCantidad) {
    nuevaCantidad = parseFloat(nuevaCantidad);
    if (nuevaCantidad <= 0) {
        carrito.splice(index, 1);
    } else {
        carrito[index].cantidad = nuevaCantidad;
        carrito[index].subtotal = nuevaCantidad * carrito[index].precio_unitario;
        carrito[index].igv_item = nuevaCantidad * (carrito[index].precio_unitario_con_igv - carrito[index].precio_unitario);
        carrito[index].total_item = nuevaCantidad * carrito[index].precio_unitario_con_igv;
    }
    renderCarrito();
}

function eliminarItem(index) {
    carrito.splice(index, 1);
    renderCarrito();
}

function limpiarCarrito() {
    if (carrito.length === 0) return;
    Swal.fire({
        title: '¿Limpiar carrito?',
        text: 'Se eliminarán todos los productos',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, limpiar',
        cancelButtonText: 'Cancelar'
    }).then(r => {
        if (r.isConfirmed) {
            carrito = [];
            renderCarrito();
        }
    });
}

function renderCarrito() {
    const container = document.getElementById('cart-items');
    if (carrito.length === 0) {
        container.innerHTML = `
            <div class="empty-cart">
                <i class="fas fa-shopping-basket"></i>
                <p>El carrito está vacío</p>
                <small>Haz clic en un producto para agregarlo</small>
            </div>`;
        document.getElementById('subtotal-gravadas').textContent = formatMoney(0);
        document.getElementById('subtotal-exoneradas').textContent = formatMoney(0);
        document.getElementById('igv-total').textContent = formatMoney(0);
        document.getElementById('total-venta').textContent = formatMoney(0);
        totalVenta = 0;
        return;
    }

    let html = '';
    carrito.forEach((item, index) => {
        html += `
            <div class="cart-item">
                <div>
                    <div class="item-name">${item.nombre}</div>
                    <small class="text-muted">${formatMoney(item.precio_unitario_con_igv)} c/u</small>
                </div>
                <div class="item-controls">
                    <button class="qty-btn" onclick="cambiarCantidad(${index}, ${item.cantidad - 1})">-</button>
                    <input type="number" class="qty-input" value="${item.cantidad}" min="0.001" step="0.001"
                           onchange="cambiarCantidad(${index}, this.value)">
                    <button class="qty-btn" onclick="cambiarCantidad(${index}, ${item.cantidad + 1})">+</button>
                    <button class="qty-btn text-danger" onclick="eliminarItem(${index})"><i class="fas fa-trash"></i></button>
                </div>
            </div>`;
    });
    container.innerHTML = html;

    // Totales
    const gravadas = carrito.filter(i => i.tipo_afectacion_igv === '10').reduce((s, i) => s + i.subtotal, 0);
    const exoneradas = carrito.filter(i => i.tipo_afectacion_igv === '20').reduce((s, i) => s + i.subtotal, 0);
    const igv = carrito.reduce((s, i) => s + i.igv_item, 0);
    const total = carrito.reduce((s, i) => s + i.total_item, 0);

    document.getElementById('subtotal-gravadas').textContent = formatMoney(gravadas);
    document.getElementById('subtotal-exoneradas').textContent = formatMoney(exoneradas);
    document.getElementById('igv-total').textContent = formatMoney(igv);
    document.getElementById('total-venta').textContent = formatMoney(total);
    totalVenta = total;
}

// === Tipo de comprobante / serie ===
document.getElementById('tipo-comprobante').addEventListener('change', function() {
    const tipo = this.value;
    document.querySelectorAll('#serie-comprobante option').forEach(opt => {
        opt.style.display = (opt.dataset.tipo === tipo) ? '' : 'none';
        if (opt.dataset.tipo === tipo && !document.getElementById('serie-comprobante').value) {
            opt.selected = true;
        }
    });
    // Seleccionar primera opción visible
    const primeraVisible = document.querySelector('#serie-comprobante option[data-tipo="'+tipo+'"]');
    if (primeraVisible) {
        document.getElementById('serie-comprobante').value = primeraVisible.value;
    }
});

// === Cliente Select2 ===
$('#cliente-select').select2({
    ajax: {
        url: '{{ route('pos.buscar.clientes') }}',
        dataType: 'json',
        delay: 250,
        data: params => ({ q: params.term }),
        processResults: data => ({ results: data.results }),
        cache: true
    },
    placeholder: 'Buscar cliente...',
    minimumInputLength: 0,
    width: '100%'
});

$('#cliente-buscar').select2({
    ajax: {
        url: '{{ route('pos.buscar.clientes') }}',
        dataType: 'json',
        delay: 250,
        data: params => ({ q: params.term }),
        processResults: data => ({ results: data.results }),
        cache: true
    },
    placeholder: 'Buscar cliente...',
    minimumInputLength: 2,
    width: '100%',
    dropdownParent: $('#modalCliente')
});

$('#cliente-buscar').on('select2:select', function(e) {
    const data = e.params.data;
    $('#cliente-select').html(`<option value="${data.id}" selected>${data.text}</option>`);
    $('#cliente-select').val(data.id).trigger('change');
    bootstrap.Modal.getInstance(document.getElementById('modalCliente')).hide();
});

function abrirModalCliente() {
    new bootstrap.Modal(document.getElementById('modalCliente')).show();
}

// === Consulta DNI/RUC en Decolecta (botón Buscar) ===
const consultaUrl = '{{ route('consulta.documento') }}';
const csrftoken = '{{ csrf_token() }}';

function setEstadoConsulta(html) {
    document.getElementById('consulta-estado').innerHTML = html || '';
}

function buscarEnDecolectaPos() {
    const tipoDoc = document.getElementById('tipo-doc').value;
    const numeroDoc = document.getElementById('numero-doc').value.trim();
    const requerido = tipoDoc === 'DNI' ? 8 : tipoDoc === 'RUC' ? 11 : 0;

    if (!numeroDoc) {
        setEstadoConsulta('<div class="alert alert-warning py-1 small mb-0">Ingresa el número de documento.</div>');
        return;
    }
    if (requerido && numeroDoc.length !== requerido) {
        setEstadoConsulta(`<div class="alert alert-warning py-1 small mb-0">El ${tipoDoc} debe tener ${requerido} dígitos.</div>`);
        return;
    }

    setEstadoConsulta('<div class="alert alert-info py-1 small mb-0">Buscando datos en la API...</div>');

    fetch(consultaUrl, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrftoken,
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
        },
        body: JSON.stringify({ tipo: tipoDoc, numero: numeroDoc }),
    })
    .then(r => r.json().then(j => ({ status: r.status, body: j })))
    .then(({ status, body }) => {
        if (body && body.success) {
            const c = body.cliente;
            document.getElementById('nombre-cliente').value = c.nombre_razon_social || '';
            document.getElementById('direccion-cliente').value = c.direccion || '';
            setEstadoConsulta(`<div class="alert alert-success py-1 small mb-0"><i class="fas fa-check-circle"></i> Datos cargados: <strong>${c.nombre_razon_social}</strong></div>`);
        } else {
            setEstadoConsulta(`<div class="alert alert-warning py-1 small mb-0">${body.message || 'No se encontraron datos. Ingréselos manualmente.'}</div>`);
        }
    })
    .catch(err => {
        setEstadoConsulta('<div class="alert alert-danger py-1 small mb-0">No se pudo consultar el servicio. Ingréselos manualmente.</div>');
    });
}

document.getElementById('pos-buscar-decolecta').addEventListener('click', buscarEnDecolectaPos);
document.getElementById('numero-doc').addEventListener('keydown', (e) => {
    if (e.key === 'Enter') { e.preventDefault(); buscarEnDecolectaPos(); }
});

function guardarClienteRapido() {
    const data = {
        tipo_documento: document.getElementById('tipo-doc').value,
        numero_documento: document.getElementById('numero-doc').value,
        nombre_razon_social: document.getElementById('nombre-cliente').value,
        direccion: document.getElementById('direccion-cliente').value,
    };
    if (!data.numero_documento || !data.nombre_razon_social) {
        Swal.fire('Error', 'Documento y nombre son requeridos', 'error');
        return;
    }
    fetch('{{ route('pos.cliente.rapido') }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: JSON.stringify(data)
    })
    .then(r => r.json())
    .then(resp => {
        if (resp.success) {
            const option = new Option(resp.cliente.text, resp.cliente.id, true, true);
            $('#cliente-select').html(option).trigger('change');
            bootstrap.Modal.getInstance(document.getElementById('modalCliente')).hide();
            Swal.fire({ icon: 'success', title: 'Cliente guardado', timer: 1500, showConfirmButton: false });
            document.getElementById('numero-doc').value = '';
            document.getElementById('nombre-cliente').value = '';
            document.getElementById('direccion-cliente').value = '';
        }
    });
}

// === Modal Pago ===
function abrirPago() {
    if (carrito.length === 0) {
        Swal.fire('Carrito vacío', 'Agrega productos antes de procesar el pago', 'warning');
        return;
    }
    document.getElementById('modal-total').textContent = formatMoney(totalVenta);
    document.getElementById('monto-recibido').value = totalVenta.toFixed(2);
    document.getElementById('vuelto-calculado').textContent = formatMoney(0);

    let resumen = '<ul class="list-unstyled mb-0">';
    carrito.forEach(item => {
        resumen += `<li class="d-flex justify-content-between"><span>${item.cantidad} x ${item.nombre}</span><strong>${formatMoney(item.total_item)}</strong></li>`;
    });
    resumen += '</ul>';
    document.getElementById('modal-resumen-items').innerHTML = resumen;

    new bootstrap.Modal(document.getElementById('modalPago')).show();
}

function cambiarMetodoPago() {
    const metodo = document.getElementById('metodo-pago').value;
    document.getElementById('pago-efectivo').style.display = metodo === 'efectivo' ? '' : 'none';
    document.getElementById('pago-electronico').style.display = metodo !== 'efectivo' ? '' : 'none';
    document.getElementById('campo-tarjeta').style.display = metodo === 'tarjeta' ? '' : 'none';

    if (metodo !== 'efectivo') {
        document.getElementById('monto-electronico').value = totalVenta.toFixed(2);
    }
}

function calcularVuelto() {
    const recibido = parseFloat(document.getElementById('monto-recibido').value) || 0;
    const vuelto = Math.max(0, recibido - totalVenta);
    document.getElementById('vuelto-calculado').textContent = formatMoney(vuelto);
}

function montoRapido(monto) {
    const actual = parseFloat(document.getElementById('monto-recibido').value) || 0;
    document.getElementById('monto-recibido').value = (actual + monto).toFixed(2);
    calcularVuelto();
}

function montoExacto() {
    document.getElementById('monto-recibido').value = totalVenta.toFixed(2);
    calcularVuelto();
}

function tecladoNum(char) {
    const input = document.getElementById('monto-recibido');
    if (char === 'C') {
        input.value = '';
    } else if (char === 'A') {
        input.value = input.value.slice(0, -1);
    } else {
        input.value += char;
    }
    calcularVuelto();
}

function confirmarVenta() {
    const metodoPago = document.getElementById('metodo-pago').value;
    let pagos = [];
    let vuelto = 0;
    let montoRecibido = 0;

    if (metodoPago === 'efectivo') {
        const recibido = parseFloat(document.getElementById('monto-recibido').value) || 0;
        if (recibido < totalVenta) {
            Swal.fire('Error', 'El monto recibido es menor al total', 'error');
            return;
        }
        vuelto = recibido - totalVenta;
        montoRecibido = recibido;
        pagos.push({
            metodo_pago: 'efectivo',
            monto: totalVenta,
            monto_recibido: recibido,
            vuelto: vuelto,
        });
    } else {
        pagos.push({
            metodo_pago: metodoPago,
            monto: totalVenta,
            numero_operacion: document.getElementById('numero-operacion').value,
            tipo_tarjeta: metodoPago === 'tarjeta' ? document.getElementById('tipo-tarjeta').value : null,
        });
    }

    const data = {
        cliente_id: document.getElementById('cliente-select').value,
        tipo_comprobante: document.getElementById('tipo-comprobante').value,
        serie: document.getElementById('serie-comprobante').value,
        items: carrito.map(item => ({
            producto_id: item.producto_id,
            cantidad: item.cantidad,
            precio_unitario: item.precio_unitario_con_igv,
            descuento: item.descuento || 0,
        })),
        pagos: pagos,
    };

    fetch('{{ route('pos.venta.store') }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: JSON.stringify(data)
    })
    .then(r => r.json())
    .then(resp => {
        if (resp.success) {
            bootstrap.Modal.getInstance(document.getElementById('modalPago')).hide();
            Swal.fire({
                icon: 'success',
                title: '¡Venta registrada!',
                html: `<p>Comprobante: <strong>${resp.venta.correlativo}</strong></p><p>Total: <strong>${formatMoney(resp.venta.total)}</strong></p>`,
                showCancelButton: true,
                confirmButtonText: '<i class="fas fa-print"></i> Imprimir',
                cancelButtonText: 'Cerrar',
            }).then(r => {
                if (r.isConfirmed) {
                    window.open(`{{ url('pos/ticket') }}/${resp.venta.id}`, '_blank');
                }
                carrito = [];
                renderCarrito();
            });
        } else {
            Swal.fire('Error', resp.error || 'No se pudo registrar la venta', 'error');
        }
    })
    .catch(err => Swal.fire('Error', err.message, 'error'));
}

// === Atajos de teclado ===
document.addEventListener('keydown', function(e) {
    if (e.key === 'F2' && carrito.length > 0) {
        e.preventDefault();
        abrirPago();
    }
    if (e.key === 'F4') {
        e.preventDefault();
        document.getElementById('buscar-producto').focus();
    }
    if (e.key === 'Escape') {
        limpiarBusqueda();
    }
});

// === Escaneo de código de barras ===
document.getElementById('buscar-producto').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        const termino = this.value.trim();
        if (termino) {
            const producto = Array.from(document.querySelectorAll('.product-card')).find(card => {
                const codigo = card.dataset.codigo.toLowerCase();
                const codigoBarra = (card.dataset.codigoBarra || '').toLowerCase();
                return codigo === termino.toLowerCase() || codigoBarra === termino.toLowerCase();
            });
            if (producto) {
                producto.click();
                this.value = '';
                filtrarProductos();
            }
        }
    }
});
</script>
@endpush
