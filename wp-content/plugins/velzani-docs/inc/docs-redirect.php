<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'VDOCS_AUTH_COOKIE', 'vdocs_auth' );

add_action( 'template_redirect', 'vdocs_handle_request' );

function vdocs_is_authenticated() {
    if ( isset( $_COOKIE[ VDOCS_AUTH_COOKIE ] ) ) {
        return hash_equals( wp_hash( 'VDOCS_419CAR' ), $_COOKIE[ VDOCS_AUTH_COOKIE ] );
    }
    return false;
}

function vdocs_handle_auth() {
    if ( vdocs_is_authenticated() ) {
        return;
    }

    $error = false;

    if ( isset( $_POST['vdocs_password'] ) ) {
        if ( $_POST['vdocs_password'] === '419CAR' ) {
            $token = wp_hash( 'VDOCS_419CAR' );
            setcookie( VDOCS_AUTH_COOKIE, $token, time() + 60 * 60 * 24 * 30, COOKIEPATH, COOKIE_DOMAIN, is_ssl(), true );
            wp_redirect( esc_url_raw( $_SERVER['REQUEST_URI'] ) );
            exit;
        }
        $error = true;
    }

    vdocs_render_password_form( $error );
    exit;
}

function vdocs_render_password_form( $error = false ) {
    ?><!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Documentação — Acesso Restrito</title>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #f8fafc;
            color: #1e293b;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .gate {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 40px 36px;
            width: 100%;
            max-width: 360px;
            box-shadow: 0 1px 4px rgba(0,0,0,.06);
        }
        .gate-logo {
            font-size: 18px;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 6px;
        }
        .gate-sub {
            font-size: 13px;
            color: #64748b;
            margin-bottom: 28px;
        }
        label {
            display: block;
            font-size: 13px;
            font-weight: 500;
            color: #1e293b;
            margin-bottom: 6px;
        }
        input[type="password"] {
            width: 100%;
            padding: 9px 12px;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            font-size: 14px;
            color: #1e293b;
            background: #f8fafc;
            outline: none;
            transition: border-color .15s;
        }
        input[type="password"]:focus { border-color: #2563eb; background: #fff; }
        .gate-error {
            font-size: 13px;
            color: #dc2626;
            margin-top: 6px;
        }
        button {
            margin-top: 20px;
            width: 100%;
            padding: 10px;
            background: #2563eb;
            color: #fff;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: background .15s;
        }
        button:hover { background: #1d4ed8; }
    </style>
</head>
<body>
    <div class="gate">
        <div class="gate-logo">Velzani Docs</div>
        <div class="gate-sub">Esta área é restrita à equipe interna.</div>
        <form method="post">
            <label for="vdocs_pw">Senha</label>
            <input type="password" id="vdocs_pw" name="vdocs_password" autofocus autocomplete="current-password">
            <?php if ( $error ) : ?>
                <div class="gate-error">Senha incorreta. Tente novamente.</div>
            <?php endif; ?>
            <button type="submit">Entrar</button>
        </form>
    </div>
</body>
</html><?php
}

function vdocs_handle_request() {
    if ( ! isset( $_GET['docs'] ) ) {
        return;
    }

    vdocs_handle_auth();

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
