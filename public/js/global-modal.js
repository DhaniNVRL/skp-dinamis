class ModalManager {
    constructor() {
        document.addEventListener('click', (event) => this.handleClick(event));
        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') {
                const modal = document.querySelector('[data-modal]:not(.hidden)');
                if (modal) this.close(modal.id);
            }
        });
    }

    open(id) {
        const modal = document.getElementById(id);
        if (!modal) return;

        document.querySelectorAll('[data-modal]:not(.hidden)').forEach((item) => {
            if (item !== modal) this.close(item.id);
        });

        modal.classList.remove('hidden');
        modal.classList.add('flex');
        modal.setAttribute('aria-hidden', 'false');
        document.body.classList.add('overflow-hidden');
        modal.querySelector('button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])')?.focus();
        document.dispatchEvent(new CustomEvent('modal:opened', { detail: { id, modal } }));
    }

    close(id) {
        const modal = document.getElementById(id);
        if (!modal) return;

        modal.classList.add('hidden');
        modal.classList.remove('flex');
        modal.setAttribute('aria-hidden', 'true');
        if (!document.querySelector('[data-modal]:not(.hidden)')) {
            document.body.classList.remove('overflow-hidden');
        }
        document.dispatchEvent(new CustomEvent('modal:closed', { detail: { id, modal } }));
    }

    handleClick(event) {
        const openButton = event.target.closest('[data-modal-open]');
        if (openButton) {
            event.preventDefault();
            this.open(openButton.dataset.modalOpen);
            return;
        }

        const closeButton = event.target.closest('[data-modal-close]');
        if (closeButton) {
            event.preventDefault();
            this.close(closeButton.dataset.modalClose);
            return;
        }

        const backdrop = event.target.closest('[data-modal]');
        if (backdrop && event.target === backdrop) this.close(backdrop.id);
    }
}

window.Modal = new ModalManager();

const legacyModal = document.getElementById('globalModal');
if (legacyModal) {
    legacyModal.dataset.modal = '';

    document.querySelectorAll('.openModal').forEach((button) => {
        button.addEventListener('click', () => {
            const title = document.getElementById('modalTitle');
            const manualForm = document.getElementById('manualForm');
            const excelForm = document.getElementById('excelForm');
            const groupInput = document.getElementById('groupId');

            if (title) title.textContent = button.dataset.title ?? '';
            if (manualForm) manualForm.action = button.dataset.manual ?? '';
            if (excelForm) excelForm.action = button.dataset.excel ?? '';
            if (groupInput) groupInput.value = button.dataset.group ?? '';
            window.Modal.open('globalModal');
        });
    });

    legacyModal.querySelectorAll('[data-close]').forEach((button) => {
        button.addEventListener('click', () => window.Modal.close('globalModal'));
    });
}

document.addEventListener('click', (event) => {
    const tab = event.target.closest('.tab-btn');
    if (!tab) return;

    const scope = tab.closest('[data-modal], #globalModal') ?? document;
    scope.querySelectorAll('.tab-btn').forEach((item) => {
        item.classList.remove('border-blue-600', 'text-blue-600');
    });
    scope.querySelectorAll('[data-content]').forEach((item) => item.classList.add('hidden'));
    tab.classList.add('border-blue-600', 'text-blue-600');
    scope.querySelector(`[data-content="${CSS.escape(tab.dataset.tab ?? '')}"]`)?.classList.remove('hidden');
});

document.getElementById('addRow')?.addEventListener('click', () => {
    const templateRow = document.querySelector('#rows .row');
    if (!templateRow) return;

    const row = templateRow.cloneNode(true);
    row.querySelectorAll('input, textarea, select').forEach((input) => {
        input.value = '';
    });
    document.getElementById('rows')?.appendChild(row);
});

document.addEventListener('click', (event) => {
    const removeButton = event.target.closest('.remove');
    if (removeButton) removeButton.closest('.row')?.remove();
});
