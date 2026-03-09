<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function vdocs_parse_filename( $filename ) {
    $basename = basename( $filename, '.md' );
    
    if ( preg_match( '/^(\d+)-(.+)$/', $basename, $matches ) ) {
        return array(
            'order' => (int) $matches[1],
            'slug'  => $matches[2],
        );
    }
    
    return array(
        'order' => 999,
        'slug'  => $basename,
    );
}

function vdocs_find_file_by_slug( $slug ) {
    $files = glob( VDOCS_DOCS_PATH . '*-' . $slug . '.md' );
    
    if ( ! empty( $files ) ) {
        return $files[0];
    }
    
    $files = glob( VDOCS_DOCS_PATH . $slug . '.md' );
    
    if ( ! empty( $files ) ) {
        return $files[0];
    }
    
    return false;
}

function vdocs_parse_markdown( $content ) {
    static $parsedown = null;
    
    if ( $parsedown === null ) {
        $parsedown = new Parsedown();
        $parsedown->setSafeMode( true );
    }
    
    return $parsedown->text( $content );
}

function vdocs_get_title( $content ) {
    if ( preg_match( '/^#\s+(.+)$/m', $content, $matches ) ) {
        return trim( $matches[1] );
    }
    return '';
}

function vdocs_get_anchors( $content ) {
    $anchors = array();
    
    if ( preg_match_all( '/^##\s+(.+)$/m', $content, $matches ) ) {
        foreach ( $matches[1] as $heading ) {
            $heading = trim( $heading );
            $anchors[] = array(
                'title' => $heading,
                'id'    => vdocs_slugify( $heading ),
            );
        }
    }
    
    return $anchors;
}

function vdocs_slugify( $text ) {
    $text = mb_strtolower( $text, 'UTF-8' );
    $text = preg_replace( '/[áàãâä]/u', 'a', $text );
    $text = preg_replace( '/[éèêë]/u', 'e', $text );
    $text = preg_replace( '/[íìîï]/u', 'i', $text );
    $text = preg_replace( '/[óòõôö]/u', 'o', $text );
    $text = preg_replace( '/[úùûü]/u', 'u', $text );
    $text = preg_replace( '/[ç]/u', 'c', $text );
    $text = preg_replace( '/[ñ]/u', 'n', $text );
    $text = preg_replace( '/[^a-z0-9\s-]/', '', $text );
    $text = preg_replace( '/[\s_]+/', '-', $text );
    $text = preg_replace( '/-+/', '-', $text );
    $text = trim( $text, '-' );
    
    return $text;
}

function vdocs_add_heading_ids( $html ) {
    return preg_replace_callback(
        '/<h2>(.+?)<\/h2>/i',
        function( $matches ) {
            $text = strip_tags( $matches[1] );
            $id = vdocs_slugify( $text );
            return '<h2 id="' . esc_attr( $id ) . '">' . $matches[1] . '</h2>';
        },
        $html
    );
}
