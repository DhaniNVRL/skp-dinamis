(() => {
    const styleByType = {
        success: 'border-emerald-200 bg-emerald-50 text-emerald-800',
        warning: 'border-amber-200 bg-amber-50 text-amber-800',
        error: 'border-red-200 bg-red-50 text-red-800',
    };

    const iconByType = {
        success: 'fa-circle-check text-emerald-600',
        warning: 'fa-triangle-exclamation text-amber-600',
        error: 'fa-circle-exclamation text-red-600',
    };

    const removeAlert = (alert) => {
        if (!alert || alert.dataset.closing === 'true') return;
        alert.dataset.closing = 'true';
        alert.classList.add('-translate-y-2', 'opacity-0');
        window.setTimeout(() => {
            const container = alert.parentElement;
            alert.remove();
            if (container && !container.querySelector('[data-global-alert]')) container.remove();
        }, 200);
    };

    const placeContainer = (container) => {
        const pageContent = document.getElementById('pageContent');
        const pageWrapper = pageContent?.firstElementChild;

        if (pageWrapper && pageWrapper.children.length) {
            pageWrapper.insertBefore(container, pageWrapper.children[1] ?? null);
        } else if (pageContent) {
            pageContent.prepend(container);
        } else {
            document.body.prepend(container);
        }
    };

    const ensureContainer = () => {
        let container = document.getElementById('globalAlertContainer');
        if (container) return container;

        container = document.createElement('div');
        container.id = 'globalAlertContainer';
        container.className = 'mb-6 flex w-full flex-col gap-3';
        container.setAttribute('role', 'region');
        container.setAttribute('aria-label', 'Notifikasi aplikasi');
        container.setAttribute('aria-live', 'polite');
        placeContainer(container);
        return container;
    };

    const show = (message, type = 'error', title = null) => {
        if (!message) return;
        const normalizedType = styleByType[type] ? type : 'error';
        const alert = document.createElement('div');
        alert.dataset.globalAlert = '';
        alert.dataset.alertType = normalizedType;
        alert.className = `-translate-y-2 rounded-lg border p-4 opacity-0 shadow-sm transition duration-200 ${styleByType[normalizedType]}`;
        alert.setAttribute('role', normalizedType === 'error' ? 'alert' : 'status');

        const row = document.createElement('div');
        row.className = 'flex items-start gap-3';
        const icon = document.createElement('i');
        icon.className = `fa-solid ${iconByType[normalizedType]} mt-0.5 text-lg`;
        const content = document.createElement('div');
        content.className = 'min-w-0 flex-1';
        const heading = document.createElement('p');
        heading.className = 'font-semibold';
        heading.textContent = title ?? (normalizedType === 'success' ? 'Berhasil' : normalizedType === 'warning' ? 'Perhatian' : 'Proses gagal');
        const text = document.createElement('p');
        text.className = 'mt-1 break-words text-sm';
        text.textContent = String(message);
        const close = document.createElement('button');
        close.type = 'button';
        close.dataset.globalAlertClose = '';
        close.className = 'rounded p-1 opacity-70 transition hover:bg-black/5 hover:opacity-100';
        close.setAttribute('aria-label', 'Tutup notifikasi');
        close.innerHTML = '<i class="fa-solid fa-xmark" aria-hidden="true"></i>';
        content.append(heading, text);
        row.append(icon, content, close);
        alert.appendChild(row);
        ensureContainer().appendChild(alert);
        window.requestAnimationFrame(() => alert.classList.remove('-translate-y-2', 'opacity-0'));
        if (normalizedType !== 'error') window.setTimeout(() => removeAlert(alert), 6000);
    };

    window.AppAlert = { show, close: removeAlert };

    const initialize = () => {
        const globalAlerts = [...document.querySelectorAll('[data-global-alert]')];
        const localAlerts = [...document.querySelectorAll('#pageContent [data-alert]')];
        const container = document.getElementById('globalAlertContainer');

        if (globalAlerts.length && localAlerts.length) {
            container?.remove();
            return;
        }

        if (container) placeContainer(container);

        globalAlerts.forEach((alert) => {
            window.requestAnimationFrame(() => alert.classList.remove('-translate-y-2', 'opacity-0'));
            if (alert.dataset.alertType !== 'error') {
                window.setTimeout(() => removeAlert(alert), 6000);
            }
        });
    };

    document.addEventListener('click', (event) => {
        const close = event.target.closest('[data-global-alert-close]');
        if (close) removeAlert(close.closest('[data-global-alert]'));
    });

    document.addEventListener('ajaxError', (event) => {
        const detail = event.detail ?? {};
        show(detail.message ?? 'Permintaan tidak dapat diproses.', 'error');
    });

    if (window.jQuery) {
        window.jQuery(document).ajaxError((_event, xhr) => {
            const errors = xhr.responseJSON?.errors;
            const firstValidationError = errors
                ? Object.values(errors).flat().find(Boolean)
                : null;
            const safeServerMessage = xhr.responseJSON?.safe_to_display === true
                ? xhr.responseJSON?.message
                : null;
            const message = xhr.status >= 500
                ? safeServerMessage ?? 'Terjadi kesalahan pada server. Silakan coba kembali atau hubungi administrator.'
                : firstValidationError ?? safeServerMessage ?? xhr.responseJSON?.message ?? 'Permintaan tidak dapat diproses.';
            show(message, 'error');
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initialize, { once: true });
    } else {
        initialize();
    }
})();
