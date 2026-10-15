<?php
/**
 * Vista: caja "Accesibilidad auditiva" en el editor.
 *
 * @var string $lsc        URL del video del intérprete.
 * @var string $transcript Transcripción.
 * @package DiegorAccesibilidad
 * @author  Diegor.tech <https://diegor.tech/>
 */

defined( 'ABSPATH' ) || exit;
?>
<p>
	<label for="da_lsc_video"><strong><?php esc_html_e( 'Video del intérprete de Lengua de Señas Colombiana (LSC)', 'diegor-accesibilidad' ); ?></strong></label><br>
	<input type="url" id="da_lsc_video" name="da_lsc_video" class="widefat" value="<?php echo esc_attr( $lsc ); ?>" placeholder="https://www.youtube.com/watch?v=...">
	<span class="description"><?php esc_html_e( 'YouTube, Vimeo o un archivo .mp4 / .webm de la biblioteca de medios. Se mostrará en una ventana flotante.', 'diegor-accesibilidad' ); ?></span>
</p>
<p>
	<label for="da_transcript"><strong><?php esc_html_e( 'Transcripción del audio o video de esta página', 'diegor-accesibilidad' ); ?></strong></label><br>
	<textarea id="da_transcript" name="da_transcript" class="widefat" rows="8"><?php echo esc_textarea( $transcript ); ?></textarea>
	<span class="description"><?php esc_html_e( 'Incluye diálogos y sonidos relevantes, por ejemplo: [música], [aplausos]. Admite HTML básico.', 'diegor-accesibilidad' ); ?></span>
</p>
