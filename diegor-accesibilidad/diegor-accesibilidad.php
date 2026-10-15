<?php
/**
 * Plugin Name:       Diegor Accesibilidad
 * Plugin URI:        https://diegor.tech/
 * Description:       Barra de accesibilidad para personas con discapacidad visual y auditiva: tamaño de texto, contrastes, lector de voz, lupa, guía de lectura, subtítulos, intérprete de Lengua de Señas Colombiana (LSC) y transcripciones.
 * Version:           1.0.2
 * Requires at least: 6.3
 * Requires PHP:      7.4
 * Author:            Diegor.tech · Diego Rodríguez
 * Author URI:        https://diegor.tech/
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       diegor-accesibilidad
 * Domain Path:       /languages
 *
 * Diseñado y desarrollado por Diegor.tech — https://diegor.tech/
 * Copyright (c) 2026 Diegor.tech. Todos los derechos reservados sobre la marca y el diseño.
 * El código se distribuye bajo licencia GPL-2.0-or-later.
 */

defined( 'ABSPATH' ) || exit;

define( 'DA_VERSION', '1.0.2' );
define( 'DA_AUTHOR', 'Diegor.tech' );
define( 'DA_AUTHOR_URL', 'https://diegor.tech/' );
define( 'DA_FILE', __FILE__ );
define( 'DA_PATH', plugin_dir_path( __FILE__ ) );
define( 'DA_URL', plugin_dir_url( __FILE__ ) );

require_once DA_PATH . 'includes/class-da-settings.php';
require_once DA_PATH . 'includes/class-da-metabox.php';
require_once DA_PATH . 'includes/class-da-frontend.php';

register_activation_hook( __FILE__, array( 'DA_Settings', 'activate' ) );

add_action(
	'plugins_loaded',
	static function () {
		load_plugin_textdomain( 'diegor-accesibilidad', false, dirname( plugin_basename( DA_FILE ) ) . '/languages' );
		DA_Settings::init();
		DA_Metabox::init();
		DA_Frontend::init();
	}
);
