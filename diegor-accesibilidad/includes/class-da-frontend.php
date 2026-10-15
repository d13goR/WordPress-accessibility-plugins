<?php
/**
 * Parte pública: carga de recursos, render del widget y mejoras de medios.
 *
 * @package DiegorAccesibilidad
 * @author  Diegor.tech <https://diegor.tech/>
 */

defined( 'ABSPATH' ) || exit;

class DA_Frontend {

	public static function init() {
		if ( ! DA_Settings::get( 'enabled' ) ) {
			return;
		}
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'assets' ) );
		add_action( 'wp_footer', array( __CLASS__, 'render' ), 99 );
		add_filter( 'the_content', array( __CLASS__, 'iframe_titles' ), 20 );

		if ( DA_Settings::get( 'hearing' ) && DA_Settings::get( 'force_captions' ) ) {
			add_filter( 'embed_oembed_html', array( __CLASS__, 'youtube_captions' ), 10, 2 );
		}
	}

	public static function assets() {
		$o = DA_Settings::get();

		wp_enqueue_style( 'da-toolbar', DA_URL . 'assets/css/da-toolbar.css', array(), DA_VERSION );
		wp_add_inline_style( 'da-toolbar', '.da-root{--da-accent:' . esc_attr( $o['color'] ) . ';}' );

		wp_enqueue_script(
			'da-toolbar',
			DA_URL . 'assets/js/da-toolbar.js',
			array(),
			DA_VERSION,
			array(
				'in_footer' => true,
				'strategy'  => 'defer',
			)
		);

		wp_localize_script(
			'da-toolbar',
			'DA_CONFIG',
			array(
				'shortcut' => (bool) $o['shortcut'],
				'i18n'     => array(
					'on'          => __( 'activado', 'diegor-accesibilidad' ),
					'off'         => __( 'desactivado', 'diegor-accesibilidad' ),
					'scale'       => __( 'Tamaño del texto: %s', 'diegor-accesibilidad' ),
					'contrast'    => __( 'Contraste: %s', 'diegor-accesibilidad' ),
					'reading'     => __( 'Leyendo en voz alta.', 'diegor-accesibilidad' ),
					'paused'      => __( 'Lectura en pausa.', 'diegor-accesibilidad' ),
					'resumed'     => __( 'Lectura reanudada.', 'diegor-accesibilidad' ),
					'stopped'     => __( 'Lectura detenida.', 'diegor-accesibilidad' ),
					'nothing'     => __( 'No se encontró texto para leer.', 'diegor-accesibilidad' ),
					'noTTS'       => __( 'Tu navegador no permite la lectura en voz alta.', 'diegor-accesibilidad' ),
					'captionsOn'  => __( 'Subtítulos activados en %s video(s).', 'diegor-accesibilidad' ),
					'captionsOff' => __( 'Subtítulos forzados desactivados.', 'diegor-accesibilidad' ),
					'noCaptions'  => __( 'Esta página no tiene videos con subtítulos disponibles.', 'diegor-accesibilidad' ),
					'reset'       => __( 'Ajustes de accesibilidad restablecidos.', 'diegor-accesibilidad' ),
				),
			)
		);
	}

	/**
	 * Datos auditivos de la página actual (intérprete LSC y transcripción).
	 */
	private static function page_media() {
		$media = array(
			'lsc_embed'  => '',
			'transcript' => '',
		);
		if ( ! is_singular() ) {
			return $media;
		}
		$post_id = get_queried_object_id();

		if ( DA_Settings::get( 'lsc' ) ) {
			$url = get_post_meta( $post_id, DA_Metabox::META_LSC, true );
			if ( $url ) {
				$media['lsc_embed'] = self::lsc_embed( $url );
			}
		}

		$transcript = get_post_meta( $post_id, DA_Metabox::META_TRANSCRIPT, true );
		if ( $transcript ) {
			$media['transcript'] = wpautop( wp_kses_post( $transcript ) );
		}
		return $media;
	}

	/**
	 * Genera el reproductor del intérprete: archivo de video propio u oEmbed (YouTube, Vimeo).
	 */
	private static function lsc_embed( $url ) {
		$title = __( 'Intérprete de Lengua de Señas Colombiana', 'diegor-accesibilidad' );

		if ( preg_match( '/\.(mp4|webm|ogv)(\?.*)?$/i', $url ) ) {
			return sprintf(
				'<video src="%1$s" controls playsinline muted autoplay loop title="%2$s"></video>',
				esc_url( $url ),
				esc_attr( $title )
			);
		}

		// WP_Embed::shortcode guarda el resultado en caché (post meta), evitando peticiones remotas en cada visita.
		global $wp_embed;
		$html = ( $wp_embed instanceof WP_Embed ) ? $wp_embed->shortcode( array(), $url ) : '';
		if ( false === stripos( (string) $html, '<iframe' ) ) {
			return '';
		}
		if ( false === stripos( $html, ' title=' ) ) {
			$html = preg_replace( '/<iframe/i', '<iframe title="' . esc_attr( $title ) . '"', $html, 1 );
		}
		return $html;
	}

	public static function render() {
		$o     = DA_Settings::get();
		$media = self::page_media();
		include DA_PATH . 'views/toolbar.php';
	}

	/**
	 * Fuerza los subtítulos (en español) en los videos de YouTube incrustados con oEmbed.
	 */
	public static function youtube_captions( $html, $url ) {
		if ( ! preg_match( '#youtube(-nocookie)?\.com|youtu\.be#i', (string) $url . $html ) ) {
			return $html;
		}
		return preg_replace_callback(
			'/\ssrc="([^"]+)"/i',
			static function ( $m ) {
				$src = add_query_arg(
					array(
						'cc_load_policy' => 1,
						'cc_lang_pref'   => 'es',
						'hl'             => 'es',
					),
					html_entity_decode( $m[1] )
				);
				return ' src="' . esc_url( $src ) . '"';
			},
			$html,
			1
		);
	}

	/**
	 * Añade un título accesible a los iframes que no lo tienen (WCAG 4.1.2).
	 */
	public static function iframe_titles( $content ) {
		if ( false === stripos( $content, '<iframe' ) ) {
			return $content;
		}
		return preg_replace(
			'/<iframe(?![^>]*\stitle=)/i',
			'<iframe title="' . esc_attr__( 'Contenido multimedia incrustado', 'diegor-accesibilidad' ) . '"',
			$content
		);
	}
}
