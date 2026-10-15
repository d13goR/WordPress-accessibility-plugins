<?php
/**
 * Caja "Accesibilidad auditiva" en páginas y entradas:
 * video del intérprete LSC y transcripción.
 *
 * @package DiegorAccesibilidad
 * @author  Diegor.tech <https://diegor.tech/>
 */

defined( 'ABSPATH' ) || exit;

class DA_Metabox {

	const META_LSC        = '_da_lsc_video';
	const META_TRANSCRIPT = '_da_transcript';
	const NONCE           = 'da_meta_nonce';

	public static function init() {
		add_action( 'add_meta_boxes', array( __CLASS__, 'add' ) );
		add_action( 'save_post', array( __CLASS__, 'save' ), 10, 1 );
		add_shortcode( 'da_transcripcion', array( __CLASS__, 'shortcode' ) );
	}

	public static function add() {
		foreach ( get_post_types( array( 'public' => true ) ) as $post_type ) {
			if ( 'attachment' === $post_type ) {
				continue;
			}
			add_meta_box(
				'da_hearing',
				__( 'Accesibilidad auditiva', 'diegor-accesibilidad' ),
				array( __CLASS__, 'render' ),
				$post_type,
				'normal',
				'default'
			);
		}
	}

	public static function render( $post ) {
		wp_nonce_field( 'da_save_meta', self::NONCE );
		$lsc        = get_post_meta( $post->ID, self::META_LSC, true );
		$transcript = get_post_meta( $post->ID, self::META_TRANSCRIPT, true );
		include DA_PATH . 'views/admin-metabox.php';
	}

	public static function save( $post_id ) {
		if ( ! isset( $_POST[ self::NONCE ] ) ) {
			return;
		}
		if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST[ self::NONCE ] ) ), 'da_save_meta' ) ) {
			return;
		}
		if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || wp_is_post_revision( $post_id ) ) {
			return;
		}
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		$lsc        = isset( $_POST['da_lsc_video'] ) ? esc_url_raw( wp_unslash( $_POST['da_lsc_video'] ) ) : '';
		$transcript = isset( $_POST['da_transcript'] ) ? wp_kses_post( wp_unslash( $_POST['da_transcript'] ) ) : '';

		self::store( $post_id, self::META_LSC, $lsc );
		self::store( $post_id, self::META_TRANSCRIPT, $transcript );
	}

	private static function store( $post_id, $key, $value ) {
		if ( '' === trim( (string) $value ) ) {
			delete_post_meta( $post_id, $key );
		} else {
			update_post_meta( $post_id, $key, $value );
		}
	}

	/**
	 * Shortcode [da_transcripcion]: muestra la transcripción dentro del contenido.
	 */
	public static function shortcode( $atts ) {
		$atts       = shortcode_atts( array( 'titulo' => __( 'Transcripción', 'diegor-accesibilidad' ) ), $atts, 'da_transcripcion' );
		$post_id    = get_the_ID();
		$transcript = $post_id ? get_post_meta( $post_id, self::META_TRANSCRIPT, true ) : '';
		if ( ! $transcript ) {
			return '';
		}
		$id = 'da-tr-inline-' . $post_id;
		return sprintf(
			'<section class="da-transcript-inline" aria-labelledby="%1$s"><h2 id="%1$s">%2$s</h2>%3$s</section>',
			esc_attr( $id ),
			esc_html( $atts['titulo'] ),
			wpautop( wp_kses_post( $transcript ) )
		);
	}
}
