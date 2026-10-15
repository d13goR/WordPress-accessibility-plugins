<?php
/**
 * Vista: widget público de accesibilidad.
 *
 * @var array $o     Ajustes.
 * @var array $media Datos auditivos de la página (lsc_embed, transcript).
 * @package DiegorAccesibilidad
 * @author  Diegor.tech <https://diegor.tech/>
 */

defined( 'ABSPATH' ) || exit;

$da_toggle = static function ( $key, $label ) {
	printf(
		'<button type="button" class="da-btn" data-da-toggle="%1$s" aria-pressed="false">%2$s</button>',
		esc_attr( $key ),
		esc_html( $label )
	);
};
$da_has_lsc        = ! empty( $media['lsc_embed'] );
$da_has_transcript = ! empty( $media['transcript'] );
?>
<?php
$da_pos  = (string) $o['position'];
$da_side = false !== strpos( $da_pos, 'left' ) ? 'left' : 'right';
$da_v    = 0 === strpos( $da_pos, 'middle' ) ? 'middle' : 'bottom';
?>
<div id="da-root" class="da-root da-pos-<?php echo esc_attr( $da_pos ); ?> da-side-<?php echo esc_attr( $da_side ); ?> da-v-<?php echo esc_attr( $da_v ); ?>" data-nosnippet>

	<button type="button" class="da-trigger" aria-expanded="false" aria-controls="da-panel">
		<span class="da-sr">
			<?php
			esc_html_e( 'Abrir menú de accesibilidad', 'diegor-accesibilidad' );
			if ( $o['shortcut'] ) {
				echo ' (Alt + A)';
			}
			?>
		</span>
		<svg aria-hidden="true" focusable="false" width="30" height="30" viewBox="0 0 24 24" fill="currentColor"><circle cx="12" cy="4" r="2.2"/><path d="M20.5 7.6c-2.7.8-5.6 1.2-8.5 1.2s-5.8-.4-8.5-1.2L3 9.5c2.1.6 4.3 1 6.5 1.2V22h2.2v-5.5h.6V22h2.2V10.7c2.2-.2 4.4-.6 6.5-1.2l-.5-1.9z"/></svg>
	</button>

	<div id="da-panel" class="da-panel" role="dialog" aria-modal="false" aria-labelledby="da-title" hidden>
		<div class="da-head">
			<h2 id="da-title" class="da-title"><?php esc_html_e( 'Accesibilidad', 'diegor-accesibilidad' ); ?></h2>
			<button type="button" class="da-close"><span aria-hidden="true">&times;</span><span class="da-sr"><?php esc_html_e( 'Cerrar menú de accesibilidad', 'diegor-accesibilidad' ); ?></span></button>
		</div>

		<div class="da-body">
			<?php if ( $o['visual'] || $o['tts'] ) : ?>
			<section class="da-section" aria-labelledby="da-h-vision">
				<h3 id="da-h-vision" class="da-h"><?php esc_html_e( 'Visión', 'diegor-accesibilidad' ); ?></h3>

				<?php if ( $o['visual'] ) : ?>
				<div class="da-row da-scale" role="group" aria-labelledby="da-scale-label">
					<span id="da-scale-label" class="da-label"><?php esc_html_e( 'Tamaño del texto', 'diegor-accesibilidad' ); ?></span>
					<button type="button" class="da-btn" data-da-action="scale-down"><span aria-hidden="true">A&minus;</span><span class="da-sr"><?php esc_html_e( 'Reducir texto', 'diegor-accesibilidad' ); ?></span></button>
					<output id="da-scale-value" class="da-scale-value">100%</output>
					<button type="button" class="da-btn" data-da-action="scale-up"><span aria-hidden="true">A+</span><span class="da-sr"><?php esc_html_e( 'Aumentar texto', 'diegor-accesibilidad' ); ?></span></button>
				</div>

				<span id="da-contrast-label" class="da-label"><?php esc_html_e( 'Contraste y color', 'diegor-accesibilidad' ); ?></span>
				<div class="da-seg" role="group" aria-labelledby="da-contrast-label">
					<button type="button" class="da-btn" data-da-contrast="none" aria-pressed="true"><?php esc_html_e( 'Normal', 'diegor-accesibilidad' ); ?></button>
					<button type="button" class="da-btn" data-da-contrast="high" aria-pressed="false"><?php esc_html_e( 'Alto contraste', 'diegor-accesibilidad' ); ?></button>
					<button type="button" class="da-btn" data-da-contrast="dark" aria-pressed="false"><?php esc_html_e( 'Modo oscuro', 'diegor-accesibilidad' ); ?></button>
					<button type="button" class="da-btn" data-da-contrast="invert" aria-pressed="false"><?php esc_html_e( 'Invertir', 'diegor-accesibilidad' ); ?></button>
					<button type="button" class="da-btn" data-da-contrast="gray" aria-pressed="false"><?php esc_html_e( 'Escala de grises', 'diegor-accesibilidad' ); ?></button>
				</div>

				<span class="da-label" id="da-tools-label"><?php esc_html_e( 'Lectura', 'diegor-accesibilidad' ); ?></span>
				<div class="da-grid" role="group" aria-labelledby="da-tools-label">
					<?php
					$da_toggle( 'font', __( 'Fuente legible', 'diegor-accesibilidad' ) );
					$da_toggle( 'spacing', __( 'Más espaciado', 'diegor-accesibilidad' ) );
					$da_toggle( 'links', __( 'Resaltar enlaces', 'diegor-accesibilidad' ) );
					$da_toggle( 'focus', __( 'Foco visible', 'diegor-accesibilidad' ) );
					$da_toggle( 'cursor', __( 'Cursor grande', 'diegor-accesibilidad' ) );
					$da_toggle( 'guide', __( 'Guía de lectura', 'diegor-accesibilidad' ) );
					$da_toggle( 'magnifier', __( 'Lupa de texto', 'diegor-accesibilidad' ) );
					$da_toggle( 'motion', __( 'Detener animaciones', 'diegor-accesibilidad' ) );
					?>
				</div>
				<?php endif; ?>

				<?php if ( $o['tts'] ) : ?>
				<span class="da-label" id="da-tts-label"><?php esc_html_e( 'Lector de voz', 'diegor-accesibilidad' ); ?></span>
				<div class="da-tts" role="group" aria-labelledby="da-tts-label" data-da-tts>
					<div class="da-grid">
						<button type="button" class="da-btn" data-da-action="read-page"><?php esc_html_e( 'Leer página', 'diegor-accesibilidad' ); ?></button>
						<?php $da_toggle( 'clickread', __( 'Leer al hacer clic', 'diegor-accesibilidad' ) ); ?>
						<button type="button" class="da-btn" data-da-action="read-pause"><?php esc_html_e( 'Pausar / seguir', 'diegor-accesibilidad' ); ?></button>
						<button type="button" class="da-btn" data-da-action="read-stop"><?php esc_html_e( 'Detener', 'diegor-accesibilidad' ); ?></button>
					</div>
					<label class="da-label" for="da-rate"><?php esc_html_e( 'Velocidad de lectura', 'diegor-accesibilidad' ); ?></label>
					<select id="da-rate" class="da-select">
						<option value="0.75"><?php esc_html_e( 'Lenta', 'diegor-accesibilidad' ); ?></option>
						<option value="1" selected><?php esc_html_e( 'Normal', 'diegor-accesibilidad' ); ?></option>
						<option value="1.25"><?php esc_html_e( 'Rápida', 'diegor-accesibilidad' ); ?></option>
						<option value="1.5"><?php esc_html_e( 'Muy rápida', 'diegor-accesibilidad' ); ?></option>
					</select>
					<p class="da-note"><?php esc_html_e( 'Consejo: selecciona un texto y pulsa «Leer página» para escuchar solo esa parte.', 'diegor-accesibilidad' ); ?></p>
				</div>
				<p class="da-note" id="da-tts-unsupported" hidden><?php esc_html_e( 'Tu navegador no permite la lectura en voz alta.', 'diegor-accesibilidad' ); ?></p>
				<?php endif; ?>
			</section>
			<?php endif; ?>

			<?php if ( $o['hearing'] ) : ?>
			<section class="da-section" aria-labelledby="da-h-hearing">
				<h3 id="da-h-hearing" class="da-h"><?php esc_html_e( 'Audición', 'diegor-accesibilidad' ); ?></h3>
				<div class="da-grid">
					<?php
					$da_toggle( 'captions', __( 'Mostrar subtítulos', 'diegor-accesibilidad' ) );
					$da_toggle( 'bigcaptions', __( 'Subtítulos grandes', 'diegor-accesibilidad' ) );
					?>
					<?php if ( $da_has_lsc ) : ?>
						<button type="button" class="da-btn" data-da-action="lsc-open" aria-expanded="false" aria-controls="da-lsc"><?php esc_html_e( 'Intérprete de señas (LSC)', 'diegor-accesibilidad' ); ?></button>
					<?php endif; ?>
					<?php if ( $da_has_transcript ) : ?>
						<button type="button" class="da-btn" data-da-action="transcript-open" aria-haspopup="dialog"><?php esc_html_e( 'Ver transcripción', 'diegor-accesibilidad' ); ?></button>
					<?php endif; ?>
				</div>
				<?php if ( ! $da_has_lsc && ! $da_has_transcript ) : ?>
					<p class="da-note"><?php esc_html_e( 'Esta página no tiene intérprete de señas ni transcripción.', 'diegor-accesibilidad' ); ?></p>
				<?php endif; ?>
			</section>
			<?php endif; ?>

			<div class="da-foot">
				<button type="button" class="da-btn da-reset" data-da-action="reset"><?php esc_html_e( 'Restablecer todo', 'diegor-accesibilidad' ); ?></button>
				<?php if ( $o['statement_url'] ) : ?>
					<a class="da-link" href="<?php echo esc_url( $o['statement_url'] ); ?>"><?php esc_html_e( 'Declaración de accesibilidad', 'diegor-accesibilidad' ); ?></a>
				<?php endif; ?>
				<?php if ( ! isset( $o['credit'] ) || $o['credit'] ) : ?>
					<p class="da-credit">
						<?php esc_html_e( 'Diseñado por', 'diegor-accesibilidad' ); ?>
						<a href="<?php echo esc_url( DA_AUTHOR_URL ); ?>" target="_blank" rel="noopener">Diegor.tech<span class="da-sr"> <?php esc_html_e( '(se abre en una pestaña nueva)', 'diegor-accesibilidad' ); ?></span></a>
					</p>
				<?php endif; ?>
			</div>
		</div>
	</div>

	<div class="da-live" role="status" aria-live="polite"></div>
	<div class="da-guide" aria-hidden="true"></div>
	<div class="da-magnifier" aria-hidden="true"></div>

	<?php if ( $o['hearing'] && $da_has_lsc ) : ?>
	<div id="da-lsc" class="da-lsc" role="region" aria-labelledby="da-lsc-title" hidden>
		<div class="da-lsc-head">
			<span id="da-lsc-title"><?php esc_html_e( 'Lengua de Señas Colombiana', 'diegor-accesibilidad' ); ?></span>
			<button type="button" class="da-lsc-close"><span aria-hidden="true">&times;</span><span class="da-sr"><?php esc_html_e( 'Cerrar intérprete', 'diegor-accesibilidad' ); ?></span></button>
		</div>
		<div class="da-lsc-body"></div>
		<template id="da-lsc-tpl"><?php echo $media['lsc_embed']; // phpcs:ignore WordPress.Security.EscapeOutput -- Generado por WP_Embed o escapado en lsc_embed(). ?></template>
	</div>
	<?php endif; ?>

	<?php if ( $o['hearing'] && $da_has_transcript ) : ?>
	<div class="da-transcript" role="dialog" aria-modal="true" aria-labelledby="da-tr-title" hidden>
		<div class="da-transcript-box">
			<div class="da-transcript-head">
				<h2 id="da-tr-title" tabindex="-1"><?php esc_html_e( 'Transcripción', 'diegor-accesibilidad' ); ?></h2>
				<button type="button" class="da-btn da-tr-close"><?php esc_html_e( 'Cerrar', 'diegor-accesibilidad' ); ?></button>
			</div>
			<div class="da-transcript-content"><?php echo $media['transcript']; // phpcs:ignore WordPress.Security.EscapeOutput -- Saneado con wp_kses_post(). ?></div>
		</div>
	</div>
	<?php endif; ?>
</div>
