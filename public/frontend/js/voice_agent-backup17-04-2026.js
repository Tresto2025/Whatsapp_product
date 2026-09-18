(function () {
    "use strict";

    var form = document.querySelector(".hero__form");
    if (form) {
        var ctaPill = form.querySelector(".hero__cta-pill");
        var countryWrap = form.querySelector(".hero__country-wrap");
        var countrySelect = form.querySelector("#country-code");

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

        if (countryWrap && countrySelect) {
            countrySelect.addEventListener("focus", function () {
                countryWrap.classList.add("is-open");
            });
            countrySelect.addEventListener("blur", function () {
                countryWrap.classList.remove("is-open");
            });
            countrySelect.addEventListener("change", function () {
                countryWrap.classList.add("is-open");
                setTimeout(function () {
                    countryWrap.classList.remove("is-open");
                }, 220);
            });
        }

        form.addEventListener("submit", function (e) {
            e.preventDefault();
        });
    }

    var el = document.getElementById("typed-text");
    if (el) {
        var WORDS = [
            "Turn every call into a confirmed booking",
            "Handles follow-ups and reminders automatically",
            "Appointments booked without manual work",
            "Handles every patient call 24/7"
        ];

        var TYPE_SPEED_MS = 85;
        var DELETE_SPEED_MS = 45;
        var PAUSE_AFTER_WORD_MS = 2000;
        var PAUSE_BEFORE_NEXT_MS = 400;

        var wordIndex = 0;
        var charIndex = 0;
        var deleting = false;

        function tick() {
            var word = WORDS[wordIndex];

            if (!deleting) {
                charIndex++;
                el.textContent = word.slice(0, charIndex);
                if (charIndex === word.length) {
                    deleting = true;
                    setTimeout(tick, PAUSE_AFTER_WORD_MS);
                    return;
                }
                setTimeout(tick, TYPE_SPEED_MS);
            } else {
                charIndex--;
                el.textContent = word.slice(0, Math.max(0, charIndex));
                if (charIndex <= 0) {
                    deleting = false;
                    wordIndex = (wordIndex + 1) % WORDS.length;
                    setTimeout(tick, PAUSE_BEFORE_NEXT_MS);
                    return;
                }
                setTimeout(tick, DELETE_SPEED_MS);
            }
        }

        tick();
    }

    var faqList = document.querySelector(".faq__list");
    if (faqList) {
        faqList.addEventListener("click", function (e) {
            var trigger = e.target.closest(".faq-item__trigger");
            if (!trigger || !faqList.contains(trigger)) return;

            var item = trigger.closest(".faq-item");
            if (!item) return;

            var wasOpen = item.classList.contains("is-open");

            var items = faqList.querySelectorAll(".faq-item");
            for (var i = 0; i < items.length; i++) {
                items[i].classList.remove("is-open");
                var t = items[i].querySelector(".faq-item__trigger");
                if (t) t.setAttribute("aria-expanded", "false");
            }

            if (!wasOpen) {
                item.classList.add("is-open");
                trigger.setAttribute("aria-expanded", "true");
            }
        });
    }
})();
