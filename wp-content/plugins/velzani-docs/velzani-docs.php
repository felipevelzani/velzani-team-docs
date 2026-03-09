<?php
/**
 * Plugin Name: Velzani Docs
 * Description: Markdown-based documentation system with auto-generated navigation.
 * Version: 1.0.0
 * Author: Velzani
 * Text Domain: velzani-docs
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'VDOCS_VERSION', '1.0.0' );
define( 'VDOCS_PATH', plugin_dir_path( __FILE__ ) );
define( 'VDOCS_URL', plugin_dir_url( __FILE__ ) );
define( 'VDOCS_DOCS_PATH', VDOCS_PATH . 'docs/' );
define( 'VDOCS_NOTES_ENABLED', false );

require_once VDOCS_PATH . 'inc/Parsedown.php';
require_once VDOCS_PATH . 'inc/docs-parser.php';
require_once VDOCS_PATH . 'inc/docs-load.php';
if ( VDOCS_NOTES_ENABLED ) {
    require_once VDOCS_PATH . 'inc/docs-notes.php';
}
require_once VDOCS_PATH . 'inc/docs-redirect.php';

if ( VDOCS_NOTES_ENABLED ) {
    register_activation_hook( __FILE__, 'vdocs_create_notes_table' );
}
