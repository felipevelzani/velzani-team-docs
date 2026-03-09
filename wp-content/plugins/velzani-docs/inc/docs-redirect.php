<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

add_action( 'template_redirect', 'vdocs_handle_request' );

function vdocs_handle_request() {
    if ( ! isset( $_GET['docs'] ) ) {
        return;
    }
    
    $slug = vdocs_get_current_slug();
    
    if ( $slug === 'notes' ) {
        vdocs_render_notes_page();
        exit;
    }
    
    if ( empty( $slug ) ) {
        $first = vdocs_get_first_doc_slug();
        if ( $first ) {
            wp_redirect( vdocs_url( $first ) );
            exit;
        }
    }
    
    $file = vdocs_find_file_by_slug( $slug );
    
    if ( ! $file || ! file_exists( $file ) ) {
        vdocs_render_404();
        exit;
    }
    
    vdocs_render_doc( $file, $slug );
    exit;
}

function vdocs_render_doc( $file, $slug ) {
    $content      = file_get_contents( $file );
    $title        = vdocs_get_title( $content );
    $html_content = vdocs_parse_markdown( $content );
    $html_content = vdocs_add_heading_ids( $html_content );
    $nav_items    = vdocs_get_nav_items();
    $current_slug = $slug;
    $notes        = vdocs_get_doc_notes( $slug );
    
    include VDOCS_PATH . 'templates/layout-header.php';
    include VDOCS_PATH . 'templates/page-doc.php';
    include VDOCS_PATH . 'templates/layout-footer.php';
}

function vdocs_render_notes_page() {
    $title        = 'Notas e Sugestões';
    $nav_items    = vdocs_get_nav_items();
    $current_slug = 'notes';
    $is_admin     = current_user_can( 'manage_options' );
    
    $filter = isset( $_GET['filter'] ) ? sanitize_text_field( $_GET['filter'] ) : '';
    $all_notes = vdocs_get_all_notes( $filter );
    
    include VDOCS_PATH . 'templates/layout-header.php';
    include VDOCS_PATH . 'templates/page-notes.php';
    include VDOCS_PATH . 'templates/layout-footer.php';
}

function vdocs_render_404() {
    $title        = 'Página não encontrada';
    $html_content = '<p>A documentação solicitada não foi encontrada.</p>';
    $nav_items    = vdocs_get_nav_items();
    $current_slug = '';
    $notes        = array();
    
    include VDOCS_PATH . 'templates/layout-header.php';
    include VDOCS_PATH . 'templates/page-doc.php';
    include VDOCS_PATH . 'templates/layout-footer.php';
}
