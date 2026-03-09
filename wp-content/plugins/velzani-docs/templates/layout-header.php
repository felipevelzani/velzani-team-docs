<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo esc_html( $title ); ?> - Documentação</title>
    <style>
        :root {
            --docs-bg: #f8fafc;
            --docs-surface: #ffffff;
            --docs-primary: #2563eb;
            --docs-primary-hover: #1d4ed8;
            --docs-text: #1e293b;
            --docs-muted: #64748b;
            --docs-border: #e2e8f0;
            --docs-radius: 6px;
            --docs-sidebar-width: 280px;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
            background: var(--docs-bg);
            color: var(--docs-text);
            line-height: 1.6;
        }
        
        .docs-layout {
            display: flex;
            min-height: 100vh;
        }
        
        .docs-sidebar {
            width: var(--docs-sidebar-width);
            background: var(--docs-surface);
            border-right: 1px solid var(--docs-border);
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            overflow-y: auto;
            padding: 24px 0;
        }
        
        .docs-logo {
            padding: 0 20px 20px;
            border-bottom: 1px solid var(--docs-border);
            margin-bottom: 16px;
        }
        
        .docs-logo h1 {
            font-size: 18px;
            font-weight: 600;
            color: var(--docs-text);
        }
        
        .docs-nav {
            list-style: none;
        }
        
        .docs-nav-item {
            margin-bottom: 2px;
        }
        
        .docs-nav-link {
            display: block;
            padding: 10px 20px;
            color: var(--docs-text);
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: background 0.15s, color 0.15s;
        }
        
        .docs-nav-link:hover {
            background: var(--docs-bg);
        }
        
        .docs-nav-link.active {
            background: #eff6ff;
            color: var(--docs-primary);
            border-right: 3px solid var(--docs-primary);
        }
        
        .docs-nav-anchors {
            list-style: none;
            display: none;
        }
        
        .docs-nav-item.expanded .docs-nav-anchors {
            display: block;
        }
        
        .docs-nav-anchor {
            display: block;
            padding: 6px 20px 6px 36px;
            color: var(--docs-muted);
            text-decoration: none;
            font-size: 13px;
            transition: color 0.15s;
        }
        
        .docs-nav-anchor:hover {
            color: var(--docs-primary);
        }
        
        .docs-main {
            flex: 1;
            margin-left: var(--docs-sidebar-width);
            padding: 40px;
            max-width: 900px;
        }
        
        .docs-content {
            background: var(--docs-surface);
            border-radius: var(--docs-radius);
            padding: 40px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }
        
        .docs-content h1 {
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 24px;
            padding-bottom: 16px;
            border-bottom: 1px solid var(--docs-border);
        }
        
        .docs-content h2 {
            font-size: 22px;
            font-weight: 600;
            margin-top: 40px;
            margin-bottom: 16px;
            padding-top: 24px;
            border-top: 1px solid var(--docs-border);
        }
        
        .docs-content h2:first-of-type {
            margin-top: 24px;
            padding-top: 0;
            border-top: none;
        }
        
        .docs-content h3 {
            font-size: 18px;
            font-weight: 600;
            margin-top: 24px;
            margin-bottom: 12px;
        }
        
        .docs-content p {
            margin-bottom: 16px;
        }
        
        .docs-content ul, .docs-content ol {
            margin-bottom: 16px;
            padding-left: 24px;
        }
        
        .docs-content li {
            margin-bottom: 8px;
        }
        
        .docs-content code {
            background: var(--docs-bg);
            padding: 2px 6px;
            border-radius: 4px;
            font-family: 'SF Mono', Monaco, 'Courier New', monospace;
            font-size: 14px;
        }
        
        .docs-content pre {
            background: #1e293b;
            color: #e2e8f0;
            padding: 16px;
            border-radius: var(--docs-radius);
            overflow-x: auto;
            margin-bottom: 16px;
        }
        
        .docs-content pre code {
            background: none;
            padding: 0;
            color: inherit;
        }
        
        .docs-content strong {
            font-weight: 600;
        }
        
        .docs-content a {
            color: var(--docs-primary);
            text-decoration: none;
        }
        
        .docs-content a:hover {
            text-decoration: underline;
        }
        
        @media (max-width: 900px) {
            .docs-sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s;
                z-index: 100;
            }
            
            .docs-sidebar.open {
                transform: translateX(0);
            }
            
            .docs-main {
                margin-left: 0;
                padding: 20px;
            }
            
            .docs-content {
                padding: 24px;
            }
            
            .docs-menu-toggle {
                display: flex;
            }
        }
        
        .docs-menu-toggle {
            display: none;
            position: fixed;
            top: 16px;
            left: 16px;
            z-index: 101;
            width: 40px;
            height: 40px;
            background: var(--docs-surface);
            border: 1px solid var(--docs-border);
            border-radius: var(--docs-radius);
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }
        
        .docs-menu-toggle svg {
            width: 20px;
            height: 20px;
            color: var(--docs-text);
        }
        
        html {
            scroll-behavior: smooth;
        }
        
        /* Notes System Styles */
        .docs-note-highlight {
            background: #fef9c3;
            cursor: pointer;
            border-bottom: 2px solid #eab308;
        }
        
        .docs-add-note-btn {
            position: absolute;
            background: var(--docs-primary);
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: var(--docs-radius);
            font-size: 12px;
            cursor: pointer;
            z-index: 1000;
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
        }
        
        .docs-add-note-btn:hover {
            background: var(--docs-primary-hover);
        }
        
        .docs-note-modal {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1001;
        }
        
        .docs-note-modal-content {
            background: var(--docs-surface);
            padding: 24px;
            border-radius: var(--docs-radius);
            width: 100%;
            max-width: 500px;
            margin: 20px;
        }
        
        .docs-note-modal h3 {
            margin-bottom: 16px;
            font-size: 18px;
        }
        
        .docs-note-modal-selected {
            background: #fef9c3;
            padding: 12px;
            border-radius: var(--docs-radius);
            margin-bottom: 16px;
            font-style: italic;
            max-height: 100px;
            overflow-y: auto;
        }
        
        .docs-note-modal textarea {
            width: 100%;
            min-height: 120px;
            padding: 12px;
            border: 1px solid var(--docs-border);
            border-radius: var(--docs-radius);
            font-family: inherit;
            font-size: 14px;
            resize: vertical;
            margin-bottom: 16px;
        }
        
        .docs-note-modal-actions {
            display: flex;
            gap: 12px;
            justify-content: flex-end;
        }
        
        .docs-btn {
            padding: 10px 20px;
            border-radius: var(--docs-radius);
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            border: none;
            transition: background 0.15s;
        }
        
        .docs-btn--primary {
            background: var(--docs-primary);
            color: white;
        }
        
        .docs-btn--primary:hover {
            background: var(--docs-primary-hover);
        }
        
        .docs-btn--secondary {
            background: var(--docs-bg);
            color: var(--docs-text);
            border: 1px solid var(--docs-border);
        }
        
        .docs-btn--secondary:hover {
            background: var(--docs-border);
        }
        
        .docs-btn--small {
            padding: 6px 12px;
            font-size: 12px;
        }
        
        .docs-notes-panel {
            margin-top: 32px;
            padding-top: 24px;
            border-top: 1px solid var(--docs-border);
        }
        
        .docs-notes-panel h3 {
            font-size: 16px;
            margin-bottom: 16px;
            color: var(--docs-muted);
        }
        
        .docs-note-item {
            background: var(--docs-bg);
            padding: 16px;
            border-radius: var(--docs-radius);
            margin-bottom: 12px;
        }
        
        .docs-note-item.resolved {
            opacity: 0.6;
        }
        
        .docs-note-item-selected {
            font-style: italic;
            color: var(--docs-muted);
            margin-bottom: 8px;
            padding: 8px;
            background: #fef9c3;
            border-radius: 4px;
        }
        
        .docs-note-item-content {
            margin-bottom: 8px;
        }
        
        .docs-note-item-meta {
            font-size: 12px;
            color: var(--docs-muted);
        }
        
        .docs-note-item-status {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 500;
            margin-left: 8px;
        }
        
        .docs-note-item-status--pending {
            background: #fef3c7;
            color: #92400e;
        }
        
        .docs-note-item-status--resolved {
            background: #d1fae5;
            color: #065f46;
        }
        
        /* Notes Page Styles */
        .docs-notes-filters {
            display: flex;
            gap: 8px;
            margin-bottom: 24px;
        }
        
        .docs-filter-btn {
            padding: 8px 16px;
            border-radius: var(--docs-radius);
            text-decoration: none;
            color: var(--docs-text);
            background: var(--docs-bg);
            font-size: 14px;
            transition: background 0.15s;
        }
        
        .docs-filter-btn:hover {
            background: var(--docs-border);
        }
        
        .docs-filter-btn.active {
            background: var(--docs-primary);
            color: white;
        }
        
        .docs-notes-empty {
            color: var(--docs-muted);
            text-align: center;
            padding: 40px;
        }
        
        .docs-note-card {
            background: var(--docs-bg);
            padding: 20px;
            border-radius: var(--docs-radius);
            margin-bottom: 16px;
            border-left: 4px solid #eab308;
        }
        
        .docs-note-card.resolved {
            border-left-color: #22c55e;
            opacity: 0.8;
        }
        
        .docs-note-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
        }
        
        .docs-note-doc {
            font-weight: 500;
            color: var(--docs-primary);
            text-decoration: none;
        }
        
        .docs-note-doc:hover {
            text-decoration: underline;
        }
        
        .docs-note-status {
            padding: 4px 10px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 500;
        }
        
        .docs-note-status--pending {
            background: #fef3c7;
            color: #92400e;
        }
        
        .docs-note-status--resolved {
            background: #d1fae5;
            color: #065f46;
        }
        
        .docs-note-selected {
            margin-bottom: 12px;
        }
        
        .docs-note-selected q {
            display: block;
            background: #fef9c3;
            padding: 8px 12px;
            border-radius: 4px;
            margin-top: 4px;
            font-style: italic;
        }
        
        .docs-note-content {
            margin-bottom: 12px;
            line-height: 1.5;
        }
        
        .docs-note-meta {
            font-size: 12px;
            color: var(--docs-muted);
            margin-bottom: 12px;
        }
        
        .docs-note-resolved {
            font-style: italic;
        }
        
        /* Mermaid Diagram Styles */
        .mermaid {
            background: var(--docs-bg);
            padding: 20px;
            border-radius: var(--docs-radius);
            margin-bottom: 16px;
            text-align: center;
            overflow-x: auto;
        }
        
        .mermaid svg {
            max-width: 100%;
            height: auto;
        }
    </style>
    <?php if ( VDOCS_NOTES_ENABLED ) : ?>
    <script>
        var vdocsData = {
            ajaxUrl: '<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>',
            nonce: '<?php echo esc_attr( wp_create_nonce( 'vdocs_notes_nonce' ) ); ?>',
            currentSlug: '<?php echo esc_attr( isset( $current_slug ) ? $current_slug : '' ); ?>',
            isAdmin: <?php echo current_user_can( 'manage_options' ) ? 'true' : 'false'; ?>
        };
    </script>
    <?php endif; ?>
</head>
<body>
    <button class="docs-menu-toggle" onclick="document.querySelector('.docs-sidebar').classList.toggle('open')">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
        </svg>
    </button>
    
    <div class="docs-layout">
        <aside class="docs-sidebar">
            <div class="docs-logo">
                <h1>Documentação</h1>
            </div>
            <nav>
                <ul class="docs-nav">
                    <?php foreach ( $nav_items as $item ) : ?>
                        <li class="docs-nav-item <?php echo $current_slug === $item['slug'] ? 'expanded' : ''; ?>">
                            <a href="<?php echo esc_url( vdocs_url( $item['slug'] ) ); ?>" 
                               class="docs-nav-link <?php echo $current_slug === $item['slug'] ? 'active' : ''; ?>">
                                <?php echo esc_html( $item['title'] ); ?>
                            </a>
                            <?php if ( ! empty( $item['anchors'] ) && $current_slug === $item['slug'] ) : ?>
                                <ul class="docs-nav-anchors">
                                    <?php foreach ( $item['anchors'] as $anchor ) : ?>
                                        <li>
                                            <a href="#<?php echo esc_attr( $anchor['id'] ); ?>" class="docs-nav-anchor">
                                                <?php echo esc_html( $anchor['title'] ); ?>
                                            </a>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </nav>
        </aside>
        
        <main class="docs-main">
