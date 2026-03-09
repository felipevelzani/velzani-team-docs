<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<article class="docs-content" id="docs-content">
    <?php echo $html_content; ?>
    
    <?php if ( ! empty( $notes ) ) : ?>
    <div class="docs-notes-panel">
        <h3>Notas desta página (<?php echo count( $notes ); ?>)</h3>
        <?php foreach ( $notes as $note ) : ?>
            <div class="docs-note-item <?php echo esc_attr( $note->status ); ?>" data-note-id="<?php echo esc_attr( $note->id ); ?>">
                <div class="docs-note-item-selected">
                    "<?php echo esc_html( mb_strimwidth( $note->selected_text, 0, 100, '...' ) ); ?>"
                </div>
                <div class="docs-note-item-content">
                    <?php echo wp_kses_post( $note->note_content ); ?>
                </div>
                <div class="docs-note-item-meta">
                    <?php echo esc_html( $note->author_name ); ?> — 
                    <?php echo esc_html( date_i18n( 'd/m/Y H:i', strtotime( $note->created_at ) ) ); ?>
                    <span class="docs-note-item-status docs-note-item-status--<?php echo esc_attr( $note->status ); ?>">
                        <?php echo $note->status === 'pending' ? 'Pendente' : 'Resolvida'; ?>
                    </span>
                </div>
                <?php if ( $note->status === 'pending' && current_user_can( 'manage_options' ) ) : ?>
                    <button type="button" class="docs-btn docs-btn--small docs-resolve-note-btn" data-note-id="<?php echo esc_attr( $note->id ); ?>">
                        Marcar como resolvida
                    </button>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</article>

<script>
(function() {
    var addNoteBtn = null;
    var selectedText = '';
    var selectionRange = null;
    
    function removeAddNoteBtn() {
        if (addNoteBtn) {
            addNoteBtn.remove();
            addNoteBtn = null;
        }
    }
    
    function showAddNoteBtn(x, y) {
        removeAddNoteBtn();
        addNoteBtn = document.createElement('button');
        addNoteBtn.className = 'docs-add-note-btn';
        addNoteBtn.textContent = 'Adicionar nota';
        addNoteBtn.style.left = x + 'px';
        addNoteBtn.style.top = y + 'px';
        document.body.appendChild(addNoteBtn);
        
        addNoteBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            showNoteModal();
        });
    }
    
    function showNoteModal() {
        var modal = document.createElement('div');
        modal.className = 'docs-note-modal';
        modal.innerHTML = 
            '<div class="docs-note-modal-content">' +
                '<h3>Adicionar Nota</h3>' +
                '<div class="docs-note-modal-selected">' + escapeHtml(selectedText) + '</div>' +
                '<textarea id="note-content" placeholder="Escreva sua nota ou sugestão..."></textarea>' +
                '<div class="docs-note-modal-actions">' +
                    '<button type="button" class="docs-btn docs-btn--secondary" id="cancel-note">Cancelar</button>' +
                    '<button type="button" class="docs-btn docs-btn--primary" id="save-note">Salvar</button>' +
                '</div>' +
            '</div>';
        
        document.body.appendChild(modal);
        document.getElementById('note-content').focus();
        
        document.getElementById('cancel-note').addEventListener('click', function() {
            modal.remove();
            removeAddNoteBtn();
        });
        
        document.getElementById('save-note').addEventListener('click', function() {
            var noteContent = document.getElementById('note-content').value.trim();
            if (!noteContent) {
                alert('Por favor, escreva uma nota.');
                return;
            }
            saveNote(noteContent, modal);
        });
        
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                modal.remove();
                removeAddNoteBtn();
            }
        });
    }
    
    function saveNote(noteContent, modal) {
        var saveBtn = document.getElementById('save-note');
        saveBtn.disabled = true;
        saveBtn.textContent = 'Salvando...';
        
        fetch(vdocsData.ajaxUrl, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({
                action: 'vdocs_save_note',
                nonce: vdocsData.nonce,
                slug: vdocsData.currentSlug,
                selected_text: selectedText,
                note_content: noteContent
            })
        })
        .then(function(response) { return response.json(); })
        .then(function(data) {
            if (data.success) {
                modal.remove();
                removeAddNoteBtn();
                location.reload();
            } else {
                alert(data.data.message || 'Erro ao salvar nota');
                saveBtn.disabled = false;
                saveBtn.textContent = 'Salvar';
            }
        })
        .catch(function() {
            alert('Erro de conexão');
            saveBtn.disabled = false;
            saveBtn.textContent = 'Salvar';
        });
    }
    
    function escapeHtml(text) {
        var div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    
    document.addEventListener('mouseup', function(e) {
        setTimeout(function() {
            var selection = window.getSelection();
            var text = selection.toString().trim();
            
            if (text.length > 3 && text.length < 500) {
                var content = document.getElementById('docs-content');
                if (content && content.contains(selection.anchorNode)) {
                    selectedText = text;
                    selectionRange = selection.getRangeAt(0).cloneRange();
                    
                    var rect = selection.getRangeAt(0).getBoundingClientRect();
                    showAddNoteBtn(rect.left + window.scrollX, rect.bottom + window.scrollY + 8);
                } else {
                    removeAddNoteBtn();
                }
            } else {
                removeAddNoteBtn();
            }
        }, 10);
    });
    
    document.addEventListener('mousedown', function(e) {
        if (addNoteBtn && !addNoteBtn.contains(e.target)) {
            removeAddNoteBtn();
        }
    });
    
    document.querySelectorAll('.docs-resolve-note-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var noteId = this.dataset.noteId;
            var item = this.closest('.docs-note-item');
            
            if (!confirm('Marcar esta nota como resolvida?')) return;
            
            this.disabled = true;
            this.textContent = 'Salvando...';
            
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
                    item.classList.add('resolved');
                    item.querySelector('.docs-note-item-status').textContent = 'Resolvida';
                    item.querySelector('.docs-note-item-status').className = 'docs-note-item-status docs-note-item-status--resolved';
                    btn.remove();
                } else {
                    alert(data.data.message || 'Erro ao resolver nota');
                    btn.disabled = false;
                    btn.textContent = 'Marcar como resolvida';
                }
            });
        });
    });
})();
</script>
