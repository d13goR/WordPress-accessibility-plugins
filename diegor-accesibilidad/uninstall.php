<?php
/**
 * Limpieza al desinstalar el plugin.
 *
 * @package DiegorAccesibilidad
 * @author  Diegor.tech <https://diegor.tech/>
 */

defined( 'WP_UNINSTALL_PLUGIN' ) || exit;

delete_option( 'da_settings' );
delete_post_meta_by_key( '_da_lsc_video' );
delete_post_meta_by_key( '_da_transcript' );
