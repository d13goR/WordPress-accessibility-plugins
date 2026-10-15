<?php
/**
 * Vista: pantalla de ajustes.
 *
 * @var array $o     Ajustes actuales.
 * @var float $ratio Contraste del color con blanco.
 * @package DiegorAccesibilidad
 * @author  Diegor.tech <https://diegor.tech/>
 */

defined( 'ABSPATH' ) || exit;

$da_name = DA_Settings::OPTION;
$da_cb   = static function ( $key, $label, $help = '' ) use ( $o, $da_name ) {
	printf(
		'<label><input type="checkbox" name="%1$s[%2$s]" value="1" %3$s> %4$s</label>%5$s<br>',
		esc_attr( $da_name ),
		esc_attr( $key ),
		checked( 1, (int) $o[ $key ], false ),
		esc_html( $label ),
		$help ? ' <span class="description">' . esc_html( $help ) . '</span>' : ''
	);
};
?>
<div class="wrap">
	<h1><?php esc_html_e( 'Accesibilidad', 'diegor-accesibilidad' ); ?></h1>
	<p class="description">
		<?php
		printf(
			/* translators: 1: versión, 2: enlace al autor */
			esc_html__( 'Diegor Accesibilidad v%1$s · Diseñado y desarrollado por %2$s', 'diegor-accesibilidad' ),
			esc_html( DA_VERSION ),
			'<a href="' . esc_url( DA_AUTHOR_URL ) . '" target="_blank" rel="noopener"><strong>' . esc_html( DA_AUTHOR ) . '</strong></a>'
		);
		?>
	</p>

	<?php if ( $ratio < 4.5 ) : ?>
		<div class="notice notice-warning">
			<p>
				<?php
				printf(
					/* translators: %s: relación de contraste */
					esc_html__( 'El color elegido tiene un contraste de %s:1 con el ícono blanco. WCAG AA recomienda al menos 4.5:1; elige un color más oscuro.', 'diegor-accesibilidad' ),
					esc_html( number_format_i18n( $ratio, 2 ) )
				);
				?>
			</p>
		</div>
	<?php endif; ?>

	<form method="post" action="options.php">
		<?php settings_fields( 'da_settings_group' ); ?>
		<table class="form-table" role="presentation">
			<tr>
				<th scope="row"><?php esc_html_e( 'Estado', 'diegor-accesibilidad' ); ?></th>
				<td><?php $da_cb( 'enabled', __( 'Mostrar la barra de accesibilidad en el sitio', 'diegor-accesibilidad' ) ); ?></td>
			</tr>
			<tr>
				<th scope="row"><label for="da-position"><?php esc_html_e( 'Posición del botón', 'diegor-accesibilidad' ); ?></label></th>
				<td>
					<select id="da-position" name="<?php echo esc_attr( $da_name ); ?>[position]">
						<option value="right" <?php selected( $o['position'], 'right' ); ?>><?php esc_html_e( 'Abajo a la derecha', 'diegor-accesibilidad' ); ?></option>
						<option value="left" <?php selected( $o['position'], 'left' ); ?>><?php esc_html_e( 'Abajo a la izquierda', 'diegor-accesibilidad' ); ?></option>
						<option value="middle-right" <?php selected( $o['position'], 'middle-right' ); ?>><?php esc_html_e( 'Centro a la derecha', 'diegor-accesibilidad' ); ?></option>
						<option value="middle-left" <?php selected( $o['position'], 'middle-left' ); ?>><?php esc_html_e( 'Centro a la izquierda', 'diegor-accesibilidad' ); ?></option>
					</select>
					<p class="description"><?php esc_html_e( 'Si usas el botón de WhatsApp abajo a la derecha, elige «Centro a la derecha» o una posición a la izquierda para que no se tapen.', 'diegor-accesibilidad' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="da-color"><?php esc_html_e( 'Color principal', 'diegor-accesibilidad' ); ?></label></th>
				<td>
					<input type="color" id="da-color" name="<?php echo esc_attr( $da_name ); ?>[color]" value="<?php echo esc_attr( $o['color'] ); ?>">
					<span class="description">
						<?php
						printf(
							/* translators: %s: relación de contraste */
							esc_html__( 'Contraste actual con blanco: %s:1', 'diegor-accesibilidad' ),
							esc_html( number_format_i18n( $ratio, 2 ) )
						);
						?>
					</span>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Herramientas visuales', 'diegor-accesibilidad' ); ?></th>
				<td>
					<?php
					$da_cb( 'visual', __( 'Texto, contrastes, fuente legible, espaciado, cursor, guía y lupa', 'diegor-accesibilidad' ) );
					$da_cb( 'tts', __( 'Lector de voz (texto a voz del navegador)', 'diegor-accesibilidad' ) );
					?>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Herramientas auditivas', 'diegor-accesibilidad' ); ?></th>
				<td>
					<?php
					$da_cb( 'hearing', __( 'Sección de audición (subtítulos y subtítulos grandes)', 'diegor-accesibilidad' ) );
					$da_cb( 'force_captions', __( 'Activar subtítulos en español por defecto en videos de YouTube incrustados', 'diegor-accesibilidad' ) );
					$da_cb( 'lsc', __( 'Intérprete de Lengua de Señas Colombiana (por página)', 'diegor-accesibilidad' ) );
					?>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Teclado', 'diegor-accesibilidad' ); ?></th>
				<td><?php $da_cb( 'shortcut', __( 'Abrir y cerrar con Alt + A', 'diegor-accesibilidad' ) ); ?></td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Créditos', 'diegor-accesibilidad' ); ?></th>
				<td><?php $da_cb( 'credit', __( 'Mostrar «Diseñado por Diegor.tech» al pie de la barra', 'diegor-accesibilidad' ) ); ?></td>
			</tr>
			<tr>
				<th scope="row"><label for="da-statement"><?php esc_html_e( 'Declaración de accesibilidad', 'diegor-accesibilidad' ); ?></label></th>
				<td>
					<input type="url" id="da-statement" class="regular-text" name="<?php echo esc_attr( $da_name ); ?>[statement_url]" value="<?php echo esc_attr( $o['statement_url'] ); ?>" placeholder="https://diegor.tech/accesibilidad/">
					<p class="description"><?php esc_html_e( 'Página opcional donde explicas tu compromiso de accesibilidad y un canal para reportar barreras.', 'diegor-accesibilidad' ); ?></p>
				</td>
			</tr>
		</table>
		<?php submit_button(); ?>
	</form>

	<hr>
	<h2><?php esc_html_e( 'Intérprete LSC y transcripciones', 'diegor-accesibilidad' ); ?></h2>
	<p><?php esc_html_e( 'Al editar cualquier página o entrada encontrarás la caja «Accesibilidad auditiva». Ahí puedes pegar el enlace del video del intérprete (YouTube, Vimeo o .mp4) y la transcripción del audio. Los visitantes los verán como opciones en la barra.', 'diegor-accesibilidad' ); ?></p>
	<p><?php esc_html_e( 'Para mostrar la transcripción dentro del contenido usa el shortcode:', 'diegor-accesibilidad' ); ?> <code>[da_transcripcion]</code></p>
</div>
