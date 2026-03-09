<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function vdocs_create_notes_table() {
    global $wpdb;
    
    $table_name = $wpdb->prefix . 'vdocs_notes';
    $charset_collate = $wpdb->get_charset_collate();
    
    $sql = "CREATE TABLE $table_name (
        id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        doc_slug varchar(100) NOT NULL,
        selected_text text NOT NULL,
        note_content text NOT NULL,
        user_id bigint(20) unsigned NOT NULL,
        status varchar(20) NOT NULL DEFAULT 'pending',
        resolved_by bigint(20) unsigned DEFAULT NULL,
        resolved_at datetime DEFAULT NULL,
        created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        KEY doc_slug (doc_slug),
        KEY status (status),
        KEY user_id (user_id)
    ) $charset_collate;";
    
    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta( $sql );
}

function vdocs_get_doc_notes( $slug ) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'vdocs_notes';
    
    $notes = $wpdb->get_results( $wpdb->prepare(
        "SELECT n.*, u.display_name as author_name 
         FROM $table_name n 
         LEFT JOIN {$wpdb->users} u ON n.user_id = u.ID 
         WHERE n.doc_slug = %s 
         ORDER BY n.created_at DESC",
        $slug
    ) );
    
    return $notes ?: array();
}

function vdocs_get_all_notes( $status = '' ) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'vdocs_notes';
    
    $where = '';
    if ( $status && in_array( $status, array( 'pending', 'resolved' ), true ) ) {
        $where = $wpdb->prepare( ' WHERE n.status = %s', $status );
    }
    
    $notes = $wpdb->get_results(
        "SELECT n.*, u.display_name as author_name, r.display_name as resolver_name 
         FROM $table_name n 
         LEFT JOIN {$wpdb->users} u ON n.user_id = u.ID 
         LEFT JOIN {$wpdb->users} r ON n.resolved_by = r.ID 
         $where
         ORDER BY n.created_at DESC"
    );
    
    return $notes ?: array();
}

add_action( 'wp_ajax_vdocs_save_note', 'vdocs_ajax_save_note' );
function vdocs_ajax_save_note() {
    check_ajax_referer( 'vdocs_notes_nonce', 'nonce' );
    
    if ( ! is_user_logged_in() ) {
        wp_send_json_error( array( 'message' => 'Não autenticado' ) );
    }
    
    $slug = isset( $_POST['slug'] ) ? sanitize_text_field( $_POST['slug'] ) : '';
    $selected_text = isset( $_POST['selected_text'] ) ? sanitize_text_field( $_POST['selected_text'] ) : '';
    $note_content = isset( $_POST['note_content'] ) ? wp_kses_post( $_POST['note_content'] ) : '';
    
    if ( empty( $slug ) || empty( $selected_text ) || empty( $note_content ) ) {
        wp_send_json_error( array( 'message' => 'Campos obrigatórios não preenchidos' ) );
    }
    
    global $wpdb;
    $table_name = $wpdb->prefix . 'vdocs_notes';
    
    $result = $wpdb->insert(
        $table_name,
        array(
            'doc_slug'      => $slug,
            'selected_text' => $selected_text,
            'note_content'  => $note_content,
            'user_id'       => get_current_user_id(),
            'status'        => 'pending',
            'created_at'    => current_time( 'mysql' ),
        ),
        array( '%s', '%s', '%s', '%d', '%s', '%s' )
    );
    
    if ( $result ) {
        $user = wp_get_current_user();
        wp_send_json_success( array(
            'message' => 'Nota salva com sucesso',
            'note' => array(
                'id'            => $wpdb->insert_id,
                'selected_text' => $selected_text,
                'note_content'  => $note_content,
                'author_name'   => $user->display_name,
                'status'        => 'pending',
                'created_at'    => current_time( 'mysql' ),
            ),
        ) );
    } else {
        wp_send_json_error( array( 'message' => 'Erro ao salvar nota' ) );
    }
}

add_action( 'wp_ajax_vdocs_resolve_note', 'vdocs_ajax_resolve_note' );
function vdocs_ajax_resolve_note() {
    check_ajax_referer( 'vdocs_notes_nonce', 'nonce' );
    
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_send_json_error( array( 'message' => 'Sem permissão' ) );
    }
    
    $note_id = isset( $_POST['note_id'] ) ? absint( $_POST['note_id'] ) : 0;
    
    if ( ! $note_id ) {
        wp_send_json_error( array( 'message' => 'ID da nota inválido' ) );
    }
    
    global $wpdb;
    $table_name = $wpdb->prefix . 'vdocs_notes';
    
    $result = $wpdb->update(
        $table_name,
        array(
            'status'      => 'resolved',
            'resolved_by' => get_current_user_id(),
            'resolved_at' => current_time( 'mysql' ),
        ),
        array( 'id' => $note_id ),
        array( '%s', '%d', '%s' ),
        array( '%d' )
    );
    
    if ( $result !== false ) {
        wp_send_json_success( array( 'message' => 'Nota marcada como resolvida' ) );
    } else {
        wp_send_json_error( array( 'message' => 'Erro ao atualizar nota' ) );
    }
}

add_action( 'wp_ajax_vdocs_get_notes', 'vdocs_ajax_get_notes' );
function vdocs_ajax_get_notes() {
    check_ajax_referer( 'vdocs_notes_nonce', 'nonce' );
    
    if ( ! is_user_logged_in() ) {
        wp_send_json_error( array( 'message' => 'Não autenticado' ) );
    }
    
    $slug = isset( $_GET['slug'] ) ? sanitize_text_field( $_GET['slug'] ) : '';
    
    if ( empty( $slug ) ) {
        wp_send_json_error( array( 'message' => 'Slug não informado' ) );
    }
    
    $notes = vdocs_get_doc_notes( $slug );
    
    wp_send_json_success( array( 'notes' => $notes ) );
}
