   document.addEventListener('DOMContentLoaded', () => {
            const actionButtons = document.querySelectorAll('.class-action-btn');

            actionButtons.forEach(button => {
                button.addEventListener('click', (event) => {
                    event.stopPropagation();

                    const menu = button.nextElementSibling;
                    const isOpen = !menu.classList.contains('hidden');

                    // Fermer tous les autres menus
                    document.querySelectorAll('.class-action-menu').forEach(otherMenu => {
                        otherMenu.classList.add('hidden');
                    });

                    document.querySelectorAll('.class-action-btn').forEach(otherButton => {
                        otherButton.setAttribute('aria-expanded', 'false');
                    });

                    // Ouvrir celui qui vient d'être cliqué
                    if (!isOpen) {
                        menu.classList.remove('hidden');
                        button.setAttribute('aria-expanded', 'true');
                    }
                });
            });

            // Fermer lorsqu'on clique ailleurs
            document.addEventListener('click', () => {
                document.querySelectorAll('.class-action-menu').forEach(menu => {
                    menu.classList.add('hidden');
                });

                document.querySelectorAll('.class-action-btn').forEach(button => {
                    button.setAttribute('aria-expanded', 'false');
                });
            });

            // Empêcher le clic dans le menu de le fermer immédiatement
            document.querySelectorAll('.class-action-menu').forEach(menu => {
                menu.addEventListener('click', event => {
                    event.stopPropagation();
                });
            });
        });
