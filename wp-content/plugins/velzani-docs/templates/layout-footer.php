<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
        </main>
    </div>
    <?php if ( VDOCS_NOTES_ENABLED ) : ?>
    <footer style="position: fixed; bottom: 0; right: 0; padding: 12px 20px; font-size: 12px; color: var(--docs-muted);">
        <a href="<?php echo esc_url( vdocs_url( 'notes' ) ); ?>" style="color: var(--docs-muted); text-decoration: none;">
            Ver notas
        </a>
    </footer>
    <?php endif; ?>
    <script src="https://cdn.jsdelivr.net/npm/mermaid@10/dist/mermaid.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            mermaid.initialize({
                startOnLoad: false,
                theme: 'default',
                securityLevel: 'loose',
                flowchart: {
                    useMaxWidth: true,
                    htmlLabels: true
                }
            });

            document.querySelectorAll('pre code.language-mermaid').forEach(function(codeBlock, index) {
                var container = document.createElement('div');
                container.className = 'mermaid';
                container.id = 'mermaid-' + index;
                container.textContent = codeBlock.textContent;
                codeBlock.parentElement.replaceWith(container);
            });

            mermaid.run();
        });
    </script>
</body>
</html>
