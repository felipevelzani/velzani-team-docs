<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<article class="docs-content docs-notes-page">
    <h1>Notas e Sugestões</h1>
    
    <div class="docs-notes-filters">
        <?php
        $current_filter = isset( $_GET['filter'] ) ? sanitize_text_field( $_GET['filter'] ) : '';
        $base_url = vdocs_url( 'notes' );
        ?>
        <a href="<?php echo esc_url( $base_url ); ?>" 
           class="docs-filter-btn <?php echo empty( $current_filter ) ? 'active' : ''; ?>">
            Todas
        </a>
        <a href="<?php echo esc_url( add_query_arg( 'filter', 'pending', $base_url ) ); ?>" 
           class="docs-filter-btn <?php echo $current_filter === 'pending' ? 'active' : ''; ?>">
            Pendentes
        </a>
        <a href="<?php echo esc_url( add_query_arg( 'filter', 'resolved', $base_url ) ); ?>" 
           class="docs-filter-btn <?php echo $current_filter === 'resolved' ? 'active' : ''; ?>">
            Resolvidas
        </a>
    </div>
    
    <?php if ( empty( $all_notes ) ) : ?>
        <p class="docs-notes-empty">Nenhuma nota encontrada.</p>
    <?php else : ?>
        <div class="docs-notes-list">
            <?php foreach ( $all_notes as $note ) : ?>
                <div class="docs-note-card <?php echo esc_attr( $note->status ); ?>" data-note-id="<?php echo esc_attr( $note->id ); ?>">
                    <div class="docs-note-header">
                        <a href="<?php echo esc_url( vdocs_url( $note->doc_slug ) ); ?>" class="docs-note-doc">
                            <?php echo esc_html( $note->doc_slug ); ?>
                        </a>
                        <span class="docs-note-status docs-note-status--<?php echo esc_attr( $note->status ); ?>">
                            <?php echo $note->status === 'pending' ? 'Pendente' : 'Resolvida'; ?>
                        </span>
                    </div>
                    
                    <div class="docs-note-selected">
                        <strong>Texto selecionado:</strong>
                        <q><?php echo esc_html( mb_strimwidth( $note->selected_text, 0, 150, '...' ) ); ?></q>
                    </div>
                    
                    <div class="docs-note-content">
                        <?php echo wp_kses_post( $note->note_content ); ?>
                    </div>
                    
                    <div class="docs-note-meta">
                        <span class="docs-note-author">
                            Por <?php echo esc_html( $note->author_name ); ?>
                        </span>
                        <span class="docs-note-date">
                            em <?php echo esc_html( date_i18n( 'd/m/Y H:i', strtotime( $note->created_at ) ) ); ?>
                        </span>
                        <?php if ( $note->status === 'resolved' && $note->resolver_name ) : ?>
                            <span class="docs-note-resolved">
                                — Resolvida por <?php echo esc_html( $note->resolver_name ); ?>
                                em <?php echo esc_html( date_i18n( 'd/m/Y H:i', strtotime( $note->resolved_at ) ) ); ?>
                            </span>
                        <?php endif; ?>
                    </div>
                    
                    <?php if ( $note->status === 'pending' && $is_admin ) : ?>
                        <button type="button" class="docs-btn docs-btn--small docs-resolve-btn" data-note-id="<?php echo esc_attr( $note->id ); ?>">
                            Marcar como resolvida
                        </button>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</article>

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.docs-resolve-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var noteId = this.dataset.noteId;
            var card = this.closest('.docs-note-card');
            
            if (!confirm('Marcar esta nota como resolvida?')) return;
            
            fetch(vdocsData.ajaxUrl, {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams({
                    action: 'vdocs_resolve_note',
                    nonce: vdocsData.nonce,
                    note_id: noteId
                })
            })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                if (data.success) {
                    card.classList.remove('pending');
                    card.classList.add('resolved');
                    card.querySelector('.docs-note-status').textContent = 'Resolvida';
                    card.querySelector('.docs-note-status').className = 'docs-note-status docs-note-status--resolved';
                    btn.remove();
                } else {
                    alert(data.data.message || 'Erro ao resolver nota');
                }
            });
        });
    });
});
</script>
