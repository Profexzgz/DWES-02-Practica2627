# 🛒 CampusTech Store — Catálogo Colaborativo (Práctica DWES-02)

![PHP](https://img.shields.io/badge/PHP-8.0%2B-777BB4?style=flat&logo=php&logoColor=white)
![XAMPP](https://img.shields.io/badge/Servidor-XAMPP%20%2F%20Apache-FB7A24?style=flat&logo=xampp&logoColor=white)
![CFGS DAW](https://img.shields.io/badge/CFGS-2%C2%BA%20DAW%20%C2%B7%20DWES-16243e?style=flat)
![Campus Digital FP](https://img.shields.io/badge/Centro-Campus%20Digital%20FP-d97706?style=flat)

Aplicación web modular desarrollada en **PHP puro** para el módulo **Desarrollo Web en Entorno Servidor (0613)** del **CFGS Desarrollo de Aplicaciones Web (Campus Digital FP)**.

El objetivo de esta práctica es aplicar los fundamentos de la **Unidad 2 (Características del lenguaje PHP)** mediante un flujo de trabajo colaborativo real en GitHub (**Fork + Branch + Pull Request**), donde cada estudiante desarrolla e integra su propia extensión de catálogo sin alterar el núcleo de la tienda.

---

## 🎯 Objetivos Didácticos (UD2)

* **Modularización e inclusión de ficheros (`Aptdo. 3.1`):** Uso de `require_once` y convención de archivos auxiliares `.inc.php` sin etiqueta de cierre `?>`.
* **Variables, constantes y ámbitos (`Aptdos. 1, 1.3 y 1.4`):** Definición de constantes (`define`), importación de ámbito global (`global`) y uso de variables superglobales (`$_SERVER`, `$_GET`, `$_POST`, `$_SESSION`, `$GLOBALS`).
* **Estructuras de datos compuestas (`Aptdo. 4`):** Manipulación y recorrido (`foreach`) de arrays indexados, asociativos y multidimensionales.
* **Funciones avanzadas (`Aptdos. 3.2 y 3.3`):** Tipado estricto de parámetros y retorno (`: float`, `: string`, `: void`), parámetros con valores por defecto y **paso por referencia (`&`)**.
* **Flujo colaborativo Git/GitHub:** Sincronización de repositorios mediante *Fork*, creación de ramas de desarrollo y envío de *Pull Requests*.

---

## 📂 Estructura del Proyecto

```text
DWES-02-Practica2627/
├── config.inc.php                  # Configuración global, constantes (IVA) e inicio de $_SESSION
├── index.php                       # Controlador principal, gestión POST/GET, carrito y vista HTML
└── extensiones/
    ├── ext_ejemplo.inc.php         # Extensión base de referencia del profesor
    └── ext_<tu_usuario>.inc.php    # Archivo modular que debes crear y desarrollar
```

> ⚠️ **Importante:** No debes modificar ni eliminar `config.inc.php` ni `extensiones/ext_ejemplo.inc.php`.

---

## 🚀 Instalación y Puesta en Marcha Local (XAMPP + VS Code)

1. **Haz un Fork** de este repositorio (`Profexzgz/DWES-02-Practica2627`) en tu cuenta personal de GitHub pulsando el botón **Fork** arriba a la derecha.
2. **Clona tu Fork** dentro de la carpeta pública de tu servidor web local (`C:\xampp\htdocs\` en Windows o `/var/www/html/` en Linux):
   ```bash
   git clone https://github.com/<TU_USUARIO>/DWES-02-Practica2627.git
   ```
3. **Crea tu rama de trabajo** antes de tocar ningún archivo:
   ```bash
   git checkout -b practica-dwes-02
   ```
4. **Arranca Apache** desde el Panel de Control de XAMPP y abre en tu navegador:
   ```text
   http://localhost/DWES-02-Practica2627/index.php
   ```

---

## 🛠️ Requisitos de Desarrollo para el Alumno

1. **Crear tu módulo de extensión:**
   * Crea un nuevo fichero dentro de `extensiones/` siguiendo el formato `ext_<tu_alias>.inc.php` (por ejemplo, `ext_anagarcia.inc.php`).
   * **Registra tus metadatos** añadiendo un array asociativo a `$GLOBALS['extensiones_cargadas'][]` con las claves `autor`, `proveedor` y `fecha_actualizacion`.
   * **Define tu catálogo** con al menos **4 productos** en un array multidimensional asociativo (`id`, `nombre`, `categoria`, `precio_base`, `stock`, `destacado`).
2. **Implementar las funciones obligatorias (con prefijo único para evitar colisiones):**
   * `calcularPrecioFinal<Alias>(float $precioBase, float $descuento = 0): float` — Cálculo de descuento e `IVA` con devolución mediante `return`.
   * `aplicarOfertaFlash<Alias>(array &$producto, float $porcentaje): void` — Modificación directa por **referencia (`&`)** del precio base en productos destacados con stock disponible.
   * `renderizarCatalogo<Alias>(array $productos, string $categoriaFiltro = ''): string` — Generación de tarjetas HTML usando sintaxis `HEREDOC` (`<<<HTML`) y envío de formularios `POST` hacia `$_SERVER['PHP_SELF']`.
3. **Conectar tu extensión en `index.php`:**
   * Incluye tu archivo mediante `require_once` en la **Zona de Inclusión** de `index.php`.
   * Invoca tu función de renderizado en la **Zona de Renderizado** de `index.php` respetando el filtro activo (`$categoria_seleccionada`).

---

## 📤 Entrega mediante Pull Request

Una vez verificadas tus tarjetas de productos, el filtrado por categoría y el funcionamiento del carrito en `$_SESSION`:

1. Guarda tus cambios y realiza un commit en tu rama `practica-dwes-02`:
   ```bash
   git add extensiones/ext_<tu_alias>.inc.php index.php
   git commit -m "Añade extensión de catálogo de <Tu Nombre>"
   git push -u origin practica-dwes-02
   ```
2. Accede a tu repositorio en GitHub y pulsa **Compare & pull request**.
3. Asegúrate de que la rama base sea **`Profexzgz/DWES-02-Practica2627` (`main`)** y abre la Pull Request indicando tu nombre completo en la descripción.

---

## 🛠️ Depuración en el Aula

* **Panel integrado en vivo:** Al pie de `index.php` dispones de un bloque desplegable (`<details>`) que muestra el estado en tiempo real de `$_SESSION`, `$GLOBALS['extensiones_cargadas']`, `$_GET` y `$_POST`.
* **Xdebug + VS Code:** El proyecto es compatible con depuración paso a paso en el puerto `9003` mediante la extensión *PHP Debug*.

---

<p align="center">
  Desarrollado para el módulo <strong>DWES (0613)</strong> · <strong>Campus Digital FP</strong>
</p>