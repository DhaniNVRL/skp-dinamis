class GlobalTabs {

    constructor() {
        this.init();
    }

    init() {

        document.querySelectorAll('[data-tabs]').forEach(container => {

            const buttons = container.querySelectorAll('[data-tab]');

            const url = new URL(window.location);

            const active =
                url.searchParams.get('tab')
                || buttons[0]?.dataset.tab;

            this.show(container, active);

            buttons.forEach(button => {

                button.addEventListener('click', () => {

                    const tab = button.dataset.tab;

                    url.searchParams.set('tab', tab);

                    history.replaceState({}, '', url);

                    this.show(container, tab);

                });

            });

        });

    }

    show(container, tab) {

        container.querySelectorAll('[data-tab]')
            .forEach(btn => {

                btn.classList.remove(
                    'border-blue-600',
                    'text-blue-600'
                );

                btn.classList.add(
                    'border-transparent',
                    'text-gray-500'
                );

            });


        const activeButton =
            container.querySelector(
                `[data-tab="${tab}"]`
            );

        if (activeButton) {

            activeButton.classList.remove(
                'border-transparent',
                'text-gray-500'
            );

            activeButton.classList.add(
                'border-blue-600',
                'text-blue-600'
            );

        }


        document
            .querySelectorAll('[data-tab-content]')
            .forEach(content => {

                content.classList.add('hidden');

            });


        const activeContent =
            document.querySelector(
                `[data-tab-content="${tab}"]`
            );

        if (activeContent) {

            activeContent.classList.remove('hidden');

        }

    }

}

window.Tabs = new GlobalTabs();
