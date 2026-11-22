<?php
// Configuración regional y de zona horaria (UD2 - Aptdo. 1.3.1)
setlocale(LC_ALL, 'es_ES.UTF-8');
date_default_timezone_set('Europe/Madrid');

// Constantes globales de la tienda que puedes usar en tu extensión (UD2 - Aptdo. 1.3)
define('NOMBRE_TIENDA', 'CampusTech Store');
define('IVA', 0.21);
define('MONEDA', '€');

// Variables globales de configuración del catálogo (UD2 - Aptdo. 1.4 y 3.2)
$descuento_global = 0.05;   // 5% de descuento promocional por defecto
$alerta_stock_bajo = 3;     // Umbral para avisar de últimas unidades
$extensiones_cargadas = []; // Array donde debes registrar tu módulo

// Inicialización segura de la sesión para el carrito y registro de actividad
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
}

if (!isset($_SESSION['historial_acciones'])) {
    $_SESSION['historial_acciones'] = [];
}
// No cierres la etiqueta PHP en archivos exclusivos de código (Buena práctica UD2)
