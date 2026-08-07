(function () {
    "use strict";

    const validTab = (value) => /^[a-zA-Z0-9_-]{1,50}$/.test(value || "");

    const setUrlTab = (tab) => {
        if (!validTab(tab)) return;
        const url = new URL(window.location.href);
        url.searchParams.set("tab", tab);
        window.history.replaceState({}, "", url.toString());
    };

    const activateGroupTab = (tab) => {
        const button = document.querySelector(`.tab-button[data-tab="${CSS.escape(tab)}"]`);
        const content = document.getElementById(`tab-${tab}`);
        if (!button || !content) return false;

        document.querySelectorAll(".tab-button[data-tab]").forEach((item) => {
            const active = item === button;
            item.classList.toggle("border-blue-600", active);
            item.classList.toggle("text-blue-600", active);
            item.classList.toggle("bg-white", active);
            item.classList.toggle("border-transparent", !active);
            item.classList.toggle("text-gray-500", !active);
            item.setAttribute("aria-selected", active ? "true" : "false");
        });

        document.querySelectorAll('[id^="tab-"]').forEach((item) => {
            if (item.parentElement === content.parentElement) {
                item.classList.toggle("hidden", item !== content);
            }
        });

        return true;
    };

    const currentTab = () => {
        const fromUrl = new URL(window.location.href).searchParams.get("tab");
        if (validTab(fromUrl)) return fromUrl;

        const selected = document.querySelector(
            '[data-unit-tab][aria-selected="true"], .tab-button[data-tab][aria-selected="true"], [data-tab][aria-selected="true"]'
        );
        return selected?.dataset.unitTab || selected?.dataset.tab || "";
    };

    document.addEventListener("click", (event) => {
        const button = event.target.closest("[data-unit-tab], .tab-button[data-tab], [data-tabs] [data-tab]");
        if (!button) return;

        const tab = button.dataset.unitTab || button.dataset.tab;
        if (!validTab(tab)) return;

        setUrlTab(tab);
        if (button.matches(".tab-button[data-tab]")) activateGroupTab(tab);
    }, true);

    document.addEventListener("submit", (event) => {
        const form = event.target;
        if (!(form instanceof HTMLFormElement) || form.method.toLowerCase() === "get") return;

        const tab = currentTab();
        if (!validTab(tab)) return;

        let input = form.querySelector('input[name="_active_tab"]');
        if (!input) {
            input = document.createElement("input");
            input.type = "hidden";
            input.name = "_active_tab";
            form.appendChild(input);
        }
        input.value = tab;
    }, true);

    const requestedTab = new URL(window.location.href).searchParams.get("tab");
    if (validTab(requestedTab)) activateGroupTab(requestedTab);
})();
