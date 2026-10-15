<?php
/**
 * Ajustes del plugin (modelo + controlador de la pantalla de ajustes).
 *
 * @package DiegorAccesibilidad
 * @author  Diegor.tech <https://diegor.tech/>
 */

defined( 'ABSPATH' ) || exit;

class DA_Settings {

	const OPTION = 'da_settings';
	const PAGE   = 'diegor-accesibilidad';

	/**
	 * Valores por defecto.
	 */
	public static function defaults() {
		return array(
			'enabled'        => 1,
			'position'       => 'right',
			'color'          => '#1a56db',
			'visual'         => 1,
			'tts'            => 1,
			'hearing'        => 1,
			'force_captions' => 1,
			'lsc'            => 1,
			'shortcut'       => 1,
			'statement_url'  => '',
			'credit'         => 1,
		);
	}

	/**
	 * Devuelve todos los ajustes o uno concreto.
	 *
	 * @param string|null $key Clave opcional.
	 * @return mixed
	 */
	public static function get( $key = null ) {
		$opts = wp_parse_args( (array) get_option( self::OPTION, array() ), self::defaults() );
		if ( null === $key ) {
			return $opts;
		}
		return isset( $opts[ $key ] ) ? $opts[ $key ] : null;
	}

	public static function activate() {
		if ( false === get_option( self::OPTION ) ) {
			add_option( self::OPTION, self::defaults() );
		}
	}

	public static function init() {
		add_action( 'admin_menu', array( __CLASS__, 'menu' ) );
		add_action( 'admin_init', array( __CLASS__, 'register' ) );
		add_filter( 'plugin_action_links_' . plugin_basename( DA_FILE ), array( __CLASS__, 'action_links' ) );
	}

	public static function menu() {
		$hook = add_options_page(
			__( 'Accesibilidad', 'diegor-accesibilidad' ),
			__( 'Accesibilidad', 'diegor-accesibilidad' ),
			'manage_options',
			self::PAGE,
			array( __CLASS__, 'render' )
		);
		add_action(
			'load-' . $hook,
			static function () {
				add_filter( 'admin_footer_text', array( __CLASS__, 'footer_text' ) );
			}
		);
	}

	public static function register() {
		register_setting(
			'da_settings_group',
			self::OPTION,
			array(
				'type'              => 'array',
				'sanitize_callback' => array( __CLASS__, 'sanitize' ),
				'default'           => self::defaults(),
			)
		);
	}

	/**
	 * Sanea los valores enviados desde el formulario.
	 *
	 * @param mixed $in Datos crudos.
	 * @return array
	 */
	public static function sanitize( $in ) {
		$in  = is_array( $in ) ? $in : array();
		$out = array();

		foreach ( array( 'enabled', 'visual', 'tts', 'hearing', 'force_captions', 'lsc', 'shortcut', 'credit' ) as $key ) {
			$out[ $key ] = empty( $in[ $key ] ) ? 0 : 1;
		}

		$position        = isset( $in['position'] ) ? sanitize_key( $in['position'] ) : 'right';
		$out['position'] = in_array( $position, array( 'right', 'left', 'middle-right', 'middle-left' ), true ) ? $position : 'right';

		$color        = isset( $in['color'] ) ? sanitize_hex_color( $in['color'] ) : '';
		$out['color'] = $color ? $color : '#1a56db';

		$out['statement_url'] = isset( $in['statement_url'] ) ? esc_url_raw( $in['statement_url'] ) : '';

		return $out;
	}

	public static function action_links( $links ) {
		$url = admin_url( 'options-general.php?page=' . self::PAGE );
		array_unshift( $links, '<a href="' . esc_url( $url ) . '">' . esc_html__( 'Ajustes', 'diegor-accesibilidad' ) . '</a>' );
		return $links;
	}

	public static function render() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		$o     = self::get();
		$ratio = self::contrast_ratio( $o['color'], '#ffffff' );
		include DA_PATH . 'views/admin-settings.php';
	}

	/**
	 * Firma en el pie del panel, solo en la pantalla del plugin.
	 */
	public static function footer_text() {
		return sprintf(
			/* translators: 1: versión, 2: enlace a Diegor.tech */
			esc_html__( 'Diegor Accesibilidad v%1$s · Diseñado por %2$s', 'diegor-accesibilidad' ),
			esc_html( DA_VERSION ),
			'<a href="' . esc_url( DA_AUTHOR_URL ) . '" target="_blank" rel="noopener">' . esc_html( DA_AUTHOR ) . '</a>'
		);
	}

	/**
	 * Relación de contraste WCAG entre dos colores hex.
	 */
	public static function contrast_ratio( $a, $b ) {
		$la = self::luminance( $a );
		$lb = self::luminance( $b );
		return ( max( $la, $lb ) + 0.05 ) / ( min( $la, $lb ) + 0.05 );
	}

	private static function luminance( $hex ) {
		$hex = ltrim( (string) $hex, '#' );
		if ( 3 === strlen( $hex ) ) {
			$hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
		}
		if ( 6 !== strlen( $hex ) ) {
			return 0;
		}
		$c = array();
		foreach ( array( 0, 2, 4 ) as $i ) {
			$v   = hexdec( substr( $hex, $i, 2 ) ) / 255;
			$c[] = $v <= 0.03928 ? $v / 12.92 : pow( ( $v + 0.055 ) / 1.055, 2.4 );
		}
		return 0.2126 * $c[0] + 0.7152 * $c[1] + 0.0722 * $c[2];
	}
}
