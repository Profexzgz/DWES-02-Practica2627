<?php
// Carga de configuración y arranque de sesión
require_once 'config.inc.php';

// =========================================================================
// ZONA DE INCLUSIÓN DE TU EXTENSIÓN
// Aquí debes incluir tu archivo de extensión usando require_once o include_once
// Ejemplo: require_once 'extensiones/ext_tunombre.inc.php';
// =========================================================================
require_once 'extensiones/ext_ejemplo.inc.php';


// =========================================================================
// PROCESAMIENTO Y VALIDACIÓN DE FORMULARIOS EN EL SERVIDOR ($_POST / $_GET)
// =========================================================================
$mensaje_sistema = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion'])) {
    if ($_POST['accion'] === 'agregar') {
        $id = isset($_POST['prod_id']) ? trim($_POST['prod_id']) : '';
        $nombre = isset($_POST['prod_nombre']) ? trim($_POST['prod_nombre']) : '';
        $precio = isset($_POST['prod_precio']) ? $_POST['prod_precio'] : 0;

        // Validación en el servidor (UD2 - Aptdo. 5.2)
        if (!empty($id) && !empty($nombre) && is_numeric($precio) && $precio > 0) {
            if (isset($_SESSION['carrito'][$id])) {
                $_SESSION['carrito'][$id]['cantidad']++;
            } else {
                $_SESSION['carrito'][$id] = [
                    'nombre' => htmlspecialchars($nombre),
                    'precio' => (float)$precio,
                    'cantidad' => 1
                ];
            }
            $_SESSION['historial_acciones'][] = date('H:i:s') . " - Añadido: " . htmlspecialchars($nombre);
            $mensaje_sistema = "✅ Producto añadido correctamente al carrito de sesión.";
        } else {
            $mensaje_sistema = "❌ Error de validación: Datos de producto no válidos.";
        }
    } elseif ($_POST['accion'] === 'vaciar') {
        $_SESSION['carrito'] = [];
        $_SESSION['historial_acciones'][] = date('H:i:s') . " - Carrito vaciado";
        $mensaje_sistema = "🗑️ Carrito vaciado correctamente.";
    }
}

// Filtro por categoría mediante GET
$categoria_seleccionada = isset($_GET['categoria']) ? trim($_GET['categoria']) : 'TODAS';

// Cálculo de totales del carrito en $_SESSION
$total_articulos = 0;
$total_importe = 0.0;
foreach ($_SESSION['carrito'] as $item_carrito) {
    $total_articulos += $item_carrito['cantidad'];
    $total_importe += $item_carrito['precio'] * $item_carrito['cantidad'];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo NOMBRE_TIENDA; ?> - DWES Práctica 02</title>
    <style>
        :root {
            --azul-oscuro: #16243e;
            --naranja-fp: #d97706;
            --gris-fondo: #f4f6f9;
            --blanco: #ffffff;
        }
        body { font-family: 'Segoe UI', system-ui, sans-serif; background: var(--gris-fondo); color: #1f2937; margin: 0; }
        header { background: var(--azul-oscuro); color: var(--blanco); padding: 1.5rem 2rem; border-bottom: 4px solid var(--naranja-fp); }
        header h1 { margin: 0; font-size: 1.6rem; }
        header p { margin: 0.3rem 0 0; color: #cbd5e1; font-size: 0.95rem; }
        .container { max-width: 1200px; margin: 1.5rem auto; padding: 0 1.5rem; }
        .panel-superior { display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem; }
        .caja { background: var(--blanco); padding: 1.2rem; border-radius: 8px; box-shadow: 0 2px 6px rgba(0,0,0,0.06); }
        .alerta { background: #ecfdf5; border-left: 4px solid #10b981; padding: 0.8rem 1rem; margin-bottom: 1rem; border-radius: 4px; font-weight: 500; }
        .grid-productos { display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 1.2rem; }
        .card { background: var(--blanco); border-radius: 8px; padding: 1.2rem; box-shadow: 0 2px 6px rgba(0,0,0,0.06); display: flex; flex-direction: column; justify-content: space-between; border-top: 3px solid #cbd5e1; }
        .card-destacada { border-top-color: var(--naranja-fp); background: #fffbeb; }
        .card-header { display: flex; justify-content: space-between; align-items: center; font-size: 0.85rem; margin-bottom: 0.5rem; }
        .tag-oferta { background: var(--naranja-fp); color: white; padding: 2px 6px; border-radius: 4px; font-weight: bold; font-size: 0.75rem; }
        .card h3 { margin: 0.4rem 0; font-size: 1.1rem; color: var(--azul-oscuro); }
        .proveedor { font-size: 0.85rem; color: #64748b; margin: 0.2rem 0 1rem; }
        .card-meta { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; }
        .precio { font-size: 1.15rem; font-weight: bold; color: var(--azul-oscuro); }
        .badge { padding: 3px 8px; border-radius: 12px; font-size: 0.78rem; font-weight: 600; }
        .badge-ok { background: #dcfce7; color: #166534; }
        .badge-bajo { background: #fef3c7; color: #92400e; }
        .badge-agotado { background: #fee2e2; color: #991b1b; }
        button { width: 100%; background: var(--azul-oscuro); color: white; border: none; padding: 0.6rem; border-radius: 6px; cursor: pointer; font-weight: 600; }
        button:hover:not(:disabled) { background: #243b66; }
        button:disabled { background: #94a3b8; cursor: not-allowed; }
        .btn-vaciar { background: #dc2626; margin-top: 0.8rem; }
        .btn-vaciar:hover { background: #b91c1c; }
        .lista-ext { font-size: 0.85rem; padding-left: 1.2rem; color: #475569; }
    </style>
</head>
<body>

<header>
    <h1>🛒 <?php echo NOMBRE_TIENDA; ?> — Catálogo Colaborativo 2º DAW</h1>
    <p>Práctica DWES-02 | Servidor: <?php echo $_SERVER['SERVER_NAME']; ?> | Fecha: <?php echo date('d/m/Y H:i'); ?></p>
</header>

<div class="container">

    <?php if (!empty($mensaje_sistema)): ?>
        <div class="alerta"><?php echo $mensaje_sistema; ?></div>
    <?php endif; ?>

    <div class="panel-superior">
        <!-- Filtro y Extensiones Activas -->
        <div class="caja">
            <h3>🔍 Filtrar Catálogo (Método GET)</h3>
            <form method="GET" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" style="display:flex; gap:0.5rem;">
                <select name="categoria" style="padding:0.5rem; flex:1;">
                    <option value="TODAS" <?php echo $categoria_seleccionada === 'TODAS' ? 'selected' : ''; ?>>Todas las categorías</option>
                    <option value="Servidores" <?php echo $categoria_seleccionada === 'Servidores' ? 'selected' : ''; ?>>Servidores</option>
                    <option value="Redes" <?php echo $categoria_seleccionada === 'Redes' ? 'selected' : ''; ?>>Redes</option>
                    <option value="Libros" <?php echo $categoria_seleccionada === 'Libros' ? 'selected' : ''; ?>>Libros</option>
                    <option value="Almacenamiento" <?php echo $categoria_seleccionada === 'Almacenamiento' ? 'selected' : ''; ?>>Almacenamiento</option>
                </select>
                <button type="submit" style="width:auto; padding:0.5rem 1.2rem;">Filtrar</button>
            </form>

            <h4 style="margin-top:1.2rem; margin-bottom:0.4rem;">🧩 Extensiones cargadas en el proyecto (<?php echo count($extensiones_cargadas); ?>):</h4>
            <ul class="lista-ext">
                <?php foreach ($extensiones_cargadas as $ext): ?>
                    <li><strong><?php echo $ext['autor']; ?></strong> — <?php echo $ext['proveedor']; ?> (Act: <?php echo $ext['fecha_actualizacion']; ?>)</li>
                <?php endforeach; ?>
            </ul>
        </div>

        <!-- Carrito persistente en $_SESSION -->
        <div class="caja">
            <h3>🛍️ Tu Carrito (<code>$_SESSION</code>)</h3>
            <p>Artículos seleccionados: <strong><?php echo $total_articulos; ?></strong></p>
            <p>Total acumulado: <strong><?php echo sprintf('%.2f %s', $total_importe, MONEDA); ?></strong></p>

            <?php if ($total_articulos > 0): ?>
                <ul style="font-size:0.85rem; padding-left:1.2rem;">
                    <?php foreach ($_SESSION['carrito'] as $cItem): ?>
                        <li><?php echo "{$cItem['nombre']} (x{$cItem['cantidad']})"; ?></li>
                    <?php endforeach; ?>
                </ul>
                <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                    <input type="hidden" name="accion" value="vaciar">
                    <button type="submit" class="btn-vaciar">Vaciar carrito</button>
                </form>
            <?php else: ?>
                <p style="font-size:0.85rem; color:#64748b;">El carrito está vacío. Añade productos desde las tarjetas.</p>
            <?php endif; ?>
        </div>
    </div>

    <h2>📦 Productos disponibles</h2>
    <section class="grid-productos">
        <?php
        // Renderizado de la extensión de ejemplo del profesor
        $productos_filtrados_ejemplo = filtrarProductosEjemplo($catalogo_ejemplo, $categoria_seleccionada);
        echo renderizarCatalogoEjemplo($productos_filtrados_ejemplo);

        // =================================================================
        // ZONA DE RENDERIZADO DE TU EXTENSIÓN
        // Aquí debes llamar a tus funciones para filtrar y mostrar tus productos
        // =================================================================
        ?>
    </section>

</div>
<details style="margin: 2rem; padding: 1rem; background: #1e293b; color: #f8fafc; border-radius: 8px; font-family: monospace;">
    <summary style="cursor: pointer; font-weight: bold;">🛠️ Panel de Depuración en Vivo (Variables del Servidor)</summary>
    <h4>1. Contenido de $_SESSION (Carrito e Historial):</h4>
    <pre><?php var_dump($_SESSION); ?></pre>
    <h4>2. Extensiones registradas en $GLOBALS:</h4>
    <pre><?php print_r($GLOBALS['extensiones_cargadas']); ?></pre>
    <h4>3. Datos recibidos por POST / GET:</h4>
    <pre><?php var_dump(['GET' => $_GET, 'POST' => $_POST]); ?></pre>
</details>
</body>
</html>
