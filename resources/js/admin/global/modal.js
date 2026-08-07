class GlobalModal {
    constructor() {
        this.registerOpen();
        this.registerClose();
        this.registerBackdrop();
    }

    open(id) {

        const modal = document.getElementById(id);

        if (!modal) return;


        // tutup semua modal yang sedang terbuka
        document
            .querySelectorAll("[data-modal]")
            .forEach(item => {

                if(item.id !== id){

                    item.classList.add("hidden");
                    item.classList.remove("flex");

                }

            });



        // buka modal tujuan
        modal.classList.remove("hidden");
        modal.classList.add("flex");


        document.dispatchEvent(
            new CustomEvent("modal:opened", {
                detail:{
                    id,
                    modal
                }
            })
        );

    }

   close(id) {

        const modal =
            document.getElementById(id);


        if(!modal)
            return;


        modal.classList.add("hidden");
        modal.classList.remove("flex");


        document.dispatchEvent(
            new CustomEvent("modal:closed", {
                detail:{
                    id,
                    modal
                }
            })
        );

    }

    registerOpen() {
        document.addEventListener("click", (e) => {
            const button = e.target.closest("[data-modal-open]");
            if (!button) return;
            this.open(button.dataset.modalOpen);
        });
    }

    registerClose() {

        document.addEventListener("click", (e) => {

            const button = e.target.closest("[data-modal-close]");

            if (!button) return;

            this.close(button.dataset.modalClose);

        });

    }

    registerBackdrop() {
        document.addEventListener("click", (e) => {
            const modal = e.target.closest("[data-modal]");
            if (!modal) return;
            if (e.target === modal) {
                this.close(modal.id);
            }
        });
    }
}

window.Modal = new GlobalModal();
