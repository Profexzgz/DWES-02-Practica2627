<?php
/**
 * EXTENSIÓN DE EJEMPLO - PROFESOR (Abel Carmona)
 * Este archivo te sirve como plantilla guía para tu práctica de 2º DAW - DWES.
 * 
 * INSTRUCCIONES PARA TI:
 * 1. Crea tu propio archivo dentro de esta carpeta: extensiones/ext_tunombre.inc.php
 * 2. Renombra tus constantes, variables y funciones añadiendo tu nombre o iniciales
 *    (por ejemplo: PROVEEDOR_JUAN, $catalogo_juan, renderizarCatalogoJuan...)
 *    para que no colisionen con las de tus compañeros al hacer la Pull Request.
 */

// 1. Define aquí una constante identificativa para tu proveedor/tienda
define('PROVEEDOR_EJEMPLO', 'Dept. Informática - Prof. Abel');

// 2. Registra tu módulo en el array superglobal $GLOBALS
$GLOBALS['extensiones_cargadas'][] = [
    'autor' => 'Abel Carmona (Ejemplo)',
    'proveedor' => PROVEEDOR_EJEMPLO,
    'fecha_actualizacion' => date('d/m/Y H:i')
];

// 3. Crea tu array multidimensional asociativo con al menos 4 productos propios
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
 * Función 1: Calcula el precio final aplicando la constante IVA y un descuento opcional.
 * Debes implementarla usando parámetros con valor por defecto y return.
 */
function calcularPrecioFinalEjemplo(float $precioBase, float $descuento = 0): float
{
    $precioConDescuento = $precioBase * (1 - $descuento);
    return round($precioConDescuento * (1 + IVA), 2);
}

/**
 * Función 2: Aplica una oferta flash modificando directamente tu array por referencia (&).
 */
function aplicarOfertaFlashEjemplo(array &$producto, float $porcentaje): void
{
    if ($producto['destacado'] && $producto['stock'] > 0) {
        $producto['precio_base'] = round($producto['precio_base'] * (1 - $porcentaje), 2);
    }
}

/**
 * Función 3: Filtra tus productos por categoría recorriendo el array con foreach.
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
 * Función 4: Genera el HTML de tus tarjetas de producto usando HEREDOC y estructuras de control.
 */
function renderizarCatalogoEjemplo(array $productos): string
{
    // Accede aquí a las variables globales definidas en config.inc.php
    global $alerta_stock_bajo, $descuento_global;
    $html = '';

    foreach ($productos as $item) {
        // Aplica la oferta flash por referencia si el producto está destacado
        aplicarOfertaFlashEjemplo($item, $descuento_global);
        $precioFinal = calcularPrecioFinalEjemplo($item['precio_base']);
        $precioFormateado = sprintf('%.2f %s', $precioFinal, MONEDA);

        // Controla el estado del stock usando if / elseif / else
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

        // Asigna un icono según la categoría utilizando switch
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
        $nombreProveedor = PROVEEDOR_EJEMPLO;

        // Construye el bloque HTML con sintaxis HEREDOC
        $html .= <<<HTML
        <article class="card {$claseDestacado}">
            <div class="card-header">
                <span class="categoria">{$iconoCat} {$item['categoria']}</span>
                {$etiquetaOferta}
            </div>
            <h3>{$item['nombre']}</h3>
            <p class="proveedor">Proveedor: <strong>{$nombreProveedor}</strong></p>
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
