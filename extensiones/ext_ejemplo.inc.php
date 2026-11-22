<?php
/**
 * EXTENSIÓN DE EJEMPLO - PROFESOR (Abel Carmona)
 * Archivo guía para el alumnado de 2º DAW - DWES
 * Cada alumno deberá crear su propio fichero: extensiones/ext_nombrealumno.inc.php
 */

// 1. Constante identificativa del proveedor/alumno
define('PROVEEDOR_EJEMPLO', 'Dept. Informática - Prof. Abel');

// 2. Registro de carga usando el array superglobal $GLOBALS
$GLOBALS['extensiones_cargadas'][] = [
    'autor' => 'Abel Carmona (Ejemplo)',
    'proveedor' => PROVEEDOR_EJEMPLO,
    'fecha_actualizacion' => date('d/m/Y H:i')
];

// 3. Array multidimensional asociativo con los productos del módulo
$catalogo_ejemplo = [
    [
        'id' => 'EJ-01',
        'nombre' => 'Servidor Rack Mini Homelab',
        'categoria' => 'Servidores',
        'precio_base' => 450.00,
        'stock' => 2,
        'destacado' => true
    ],
    [
        'id' => 'EJ-02',
        'nombre' => 'Switch Gigabit 8 Puertos Gestionable',
        'categoria' => 'Redes',
        'precio_base' => 49.90,
        'stock' => 12,
        'destacado' => false
    ],
    [
        'id' => 'EJ-03',
        'nombre' => 'Manual PHP 8 & Arquitectura Web',
        'categoria' => 'Libros',
        'precio_base' => 32.50,
        'stock' => 0,
        'destacado' => false
    ],
    [
        'id' => 'EJ-04',
        'nombre' => 'NVMe SSD 1TB PCIe 4.0 Enterprise',
        'categoria' => 'Almacenamiento',
        'precio_base' => 89.00,
        'stock' => 5,
        'destacado' => true
    ]
];

/**
 * Calcula el precio final aplicando IVA y un descuento opcional.
 */
function calcularPrecioFinalEjemplo(float $precioBase, float $descuento = 0): float
{
    $precioConDescuento = $precioBase * (1 - $descuento);
    return round($precioConDescuento * (1 + IVA), 2);
}

/**
 * Aplica una oferta flash modificando directamente el array por referencia (&).
 */
function aplicarOfertaFlashEjemplo(array &$producto, float $porcentaje): void
{
    if ($producto['destacado'] && $producto['stock'] > 0) {
        $producto['precio_base'] = round($producto['precio_base'] * (1 - $porcentaje), 2);
    }
}

/**
 * Filtra los productos por categoría usando bucle foreach.
 */
function filtrarProductosEjemplo(array $productos, string $categoriaFiltro): array
{
    if (empty($categoriaFiltro) || $categoriaFiltro === 'TODAS') {
        return $productos;
    }

    $resultado = [];
    foreach ($productos as $prod) {
        if (strcasecmp($prod['categoria'], $categoriaFiltro) === 0) {
            $resultado[] = $prod;
        }
    }
    return $resultado;
}

/**
 * Genera el HTML de las tarjetas de producto usando sintaxis HEREDOC y estructuras de control.
 */
function renderizarCatalogoEjemplo(array $productos): string
{
    global $alerta_stock_bajo, $descuento_global;
    $html = '';

    foreach ($productos as $item) {
        // Aplicamos oferta flash por referencia si está destacado
        aplicarOfertaFlashEjemplo($item, $descuento_global);
        $precioFinal = calcularPrecioFinalEjemplo($item['precio_base']);
        $precioFormateado = sprintf('%.2f %s', $precioFinal, MONEDA);

        // Control de estado de stock con if / elseif / else
        if ($item['stock'] <= 0) {
            $badgeStock = '<span class="badge badge-agotado">Agotado</span>';
            $botonDisabled = 'disabled';
        } elseif ($item['stock'] <= $alerta_stock_bajo) {
            $badgeStock = "<span class='badge badge-bajo'>¡Últimas {$item['stock']} uds!</span>";
            $botonDisabled = '';
        } else {
            $badgeStock = "<span class='badge badge-ok'>En stock ({$item['stock']})</span>";
            $botonDisabled = '';
        }

        // Etiqueta de categoría con switch
        switch ($item['categoria']) {
            case 'Servidores':
                $iconoCat = '🖥️';
                break;
            case 'Redes':
                $iconoCat = '🌐';
                break;
            case 'Libros':
                $iconoCat = '📚';
                break;
            default:
                $iconoCat = '💾';
                break;
        }

        $claseDestacado = $item['destacado'] ? 'card-destacada' : '';
        $etiquetaOferta = $item['destacado'] ? '<span class="tag-oferta">OFERTA FLASH -5%</span>' : '';
        $actionUrl = htmlspecialchars($_SERVER['PHP_SELF']);

        // Construcción limpia de HTML con HEREDOC
        $html .= <<<HTML
        <article class="card {$claseDestacado}">
            <div class="card-header">
                <span class="categoria">{$iconoCat} {$item['categoria']}</span>
                {$etiquetaOferta}
            </div>
            <h3>{$item['nombre']}</h3>
            <p class="proveedor">Proveedor: <strong>{$GLOBALS['extensiones_cargadas'][0]['proveedor']}</strong></p>
            <div class="card-meta">
                {$badgeStock}
                <span class="precio">{$precioFormateado} <small>(IVA incl.)</small></span>
            </div>
            <form method="POST" action="{$actionUrl}" class="form-carrito">
                <input type="hidden" name="accion" value="agregar">
                <input type="hidden" name="prod_id" value="{$item['id']}">
                <input type="hidden" name="prod_nombre" value="{$item['nombre']}">
                <input type="hidden" name="prod_precio" value="{$precioFinal}">
                <button type="submit" {$botonDisabled}>Añadir al carrito</button>
            </form>
        </article>
HTML;
    }

    return $html;
}
