(function () {
    "use strict";

    window.addEventListener("load", function () {

        var form = document.querySelector(".hero__form");
        if (!form) return;

        var msg = document.getElementById("msg");
        var btn = form.querySelector("#btn");
        var phoneInput = form.querySelector("#phone");
        
        function showMessage(text, type) {
    msg.textContent = text;

    msg.classList.remove("is-loading", "is-success", "is-error");

    if (type) {
        msg.classList.add("is-" + type);
    }

    msg.classList.add("show");
}

        //  UI enhancements
        var ctaPill = form.querySelector(".hero__cta-pill");
        if (ctaPill) {
            form.addEventListener("focusin", function () {
                ctaPill.classList.add("is-focused");
            });
            form.addEventListener("focusout", function () {
                setTimeout(function () {
                    if (!form.contains(document.activeElement)) {
                        ctaPill.classList.remove("is-focused");
                    }
                }, 0);
            });
        }

        // Submit handler
        form.addEventListener("submit", function (e) {
            e.preventDefault();

            var phone = phoneInput.value.trim();

            // Basic validation
            if (!/^[6-9]\d{9}$/.test(phone)) {
    showMessage("Enter a valid mobile number", "error");
    return;
}

            // UI state
            showMessage("Connecting your call...", "loading");
            btn.disabled = true;

            fetch('/demo/callback', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ phone: phone })
            })
            .then(function () {
                // Always show success
                setTimeout(function () {
                    showMessage("You’ll receive a call shortly", "success");
                }, 800);
            })
            .catch(function () {
                // Do NOT expose errors to user
                showMessage("You’ll receive a call shortly", "success");
            })
            .finally(function () {
                btn.disabled = false;
            });
        });
        phoneInput.addEventListener("input", function () {
    msg.textContent = "";
    msg.classList.remove("show");
});

    });

})();