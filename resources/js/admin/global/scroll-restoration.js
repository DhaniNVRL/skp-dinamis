const STORAGE_KEY = "skp:pending-scroll-restoration";
const MAX_AGE_MS = 30_000;

const currentLocation = () =>
    window.location.pathname + window.location.search;

const saveScrollPosition = () => {
    if (window.scrollY <= 0) return;

    try {
        sessionStorage.setItem(
            STORAGE_KEY,
            JSON.stringify({
                location: currentLocation(),
                scrollY: window.scrollY,
                savedAt: Date.now(),
            })
        );
    } catch (_) {
        // sessionStorage dapat tidak tersedia pada mode browser tertentu.
    }
};

const isMutationForm = (form) => {
    const spoofedMethod = form.querySelector('input[name="_method"]')?.value;
    const method = (spoofedMethod || form.method || "GET").toUpperCase();

    return ["POST", "PUT", "PATCH", "DELETE"].includes(method);
};

const restoreScrollPosition = () => {
    let saved;

    try {
        saved = JSON.parse(sessionStorage.getItem(STORAGE_KEY) || "null");
        sessionStorage.removeItem(STORAGE_KEY);
    } catch (_) {
        sessionStorage.removeItem(STORAGE_KEY);
        return;
    }

    if (
        !saved ||
        saved.location !== currentLocation() ||
        Date.now() - Number(saved.savedAt) > MAX_AGE_MS ||
        !Number.isFinite(Number(saved.scrollY))
    ) {
        return;
    }

    const targetY = Number(saved.scrollY);
    const restore = () => window.scrollTo({ top: targetY, behavior: "instant" });

    requestAnimationFrame(() => requestAnimationFrame(restore));
    window.addEventListener("load", restore, { once: true });
};

if ("scrollRestoration" in history) {
    history.scrollRestoration = "manual";
}

document.addEventListener(
    "submit",
    (event) => {
        if (event.target instanceof HTMLFormElement && isMutationForm(event.target)) {
            saveScrollPosition();
        }
    },
    true
);

document.addEventListener(
    "click",
    (event) => {
        const submitter = event.target.closest(
            'button[type="submit"], input[type="submit"]'
        );
        if (submitter?.form && isMutationForm(submitter.form)) {
            saveScrollPosition();
        }
    },
    true
);

const nativeSubmit = HTMLFormElement.prototype.submit;
HTMLFormElement.prototype.submit = function () {
    if (isMutationForm(this)) saveScrollPosition();
    return nativeSubmit.call(this);
};

if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", restoreScrollPosition, {
        once: true,
    });
} else {
    restoreScrollPosition();
}
