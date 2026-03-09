<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function vdocs_url( $slug = '' ) {
    $args = array( 'docs' => $slug ?: '' );
    return add_query_arg( $args, home_url( '/' ) );
}

function vdocs_get_nav_items() {
    $items = array();
    $files = glob( VDOCS_DOCS_PATH . '*.md' );
    
    if ( empty( $files ) ) {
        return $items;
    }
    
    foreach ( $files as $file ) {
        $filename = basename( $file );
        $parsed   = vdocs_parse_filename( $filename );
        $content  = file_get_contents( $file );
        $title    = vdocs_get_title( $content );
        $anchors  = vdocs_get_anchors( $content );
        
        if ( empty( $title ) ) {
            $title = ucfirst( str_replace( '-', ' ', $parsed['slug'] ) );
        }
        
        $items[] = array(
            'slug'    => $parsed['slug'],
            'title'   => $title,
            'order'   => $parsed['order'],
            'file'    => $filename,
            'anchors' => $anchors,
        );
    }
    
    usort( $items, function( $a, $b ) {
        return $a['order'] - $b['order'];
    } );
    
    return $items;
}

function vdocs_get_first_doc_slug() {
    $items = vdocs_get_nav_items();
    
    if ( ! empty( $items ) ) {
        return $items[0]['slug'];
    }
    
    return '';
}

function vdocs_get_current_slug() {
    return isset( $_GET['docs'] ) ? sanitize_text_field( $_GET['docs'] ) : '';
}
