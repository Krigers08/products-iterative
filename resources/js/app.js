/**
 * Product quantity adjustments (delivery and purchase) submitted in the background.
 */
function initQuantityForms() {
    document.querySelectorAll('[data-quantity-form]').forEach((form) => {
        form.addEventListener('submit', async (event) => {
            event.preventDefault();

            if (form.getAttribute('aria-busy') === 'true') {
                return;
            }

            const direction = event.submitter?.dataset.quantityDirection === 'decrease'
                ? 'decrease'
                : 'increase';
            const url = direction === 'decrease'
                ? form.dataset.decreaseUrl
                : form.dataset.increaseUrl;
            const amountInput = form.querySelector('input[name="amount"]');
            const token = form.querySelector('input[name="_token"]')?.value;

            if (!url || !amountInput) {
                return;
            }

            setSubmitting(form, true);
            hideFeedback(form);

            try {
                const response = await fetch(url, {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {
                        Accept: 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token,
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify({ amount: amountInput.value }),
                });

                const data = await response.json().catch(() => ({}));

                if (!response.ok) {
                    showFeedback(form, collectErrors(data), 'error');

                    return;
                }

                updateQuantity(form, data.quantity);
                showFeedback(form, data.message, 'success');

                amountInput.value = '1';
                amountInput.focus();
            } catch {
                showFeedback(
                    form,
                    'Neizdevās sazināties ar serveri. Lūdzu, mēģiniet vēlreiz.',
                    'error',
                );
            } finally {
                setSubmitting(form, false);
            }
        });
    });
}

function setSubmitting(form, isSubmitting) {
    form.setAttribute('aria-busy', isSubmitting ? 'true' : 'false');

    form.querySelectorAll('button[type="submit"]').forEach((button) => {
        button.disabled = isSubmitting || (isDecreaseButton(button) && quantityOf(form) === 0);
    });
}

function updateQuantity(form, quantity) {
    form.dataset.quantity = String(quantity);

    document.querySelectorAll('[data-quantity-value]').forEach((element) => {
        element.textContent = String(quantity);
    });
}

function showFeedback(form, message, type) {
    const feedback = form.querySelector('[data-quantity-feedback]');

    if (!feedback || !message) {
        return;
    }

    feedback.textContent = message;
    feedback.classList.remove('flash-success', 'flash-errors');
    feedback.classList.add(type === 'error' ? 'flash-errors' : 'flash-success');
    feedback.hidden = false;
}

function hideFeedback(form) {
    const feedback = form.querySelector('[data-quantity-feedback]');

    if (!feedback) {
        return;
    }

    feedback.hidden = true;
    feedback.textContent = '';
}

function collectErrors(data) {
    if (data.errors) {
        return Object.values(data.errors).flat().join(' ');
    }

    return data.message ?? 'Darbību neizdevās veikt.';
}

function isDecreaseButton(button) {
    return button.dataset.quantityDirection === 'decrease';
}

function quantityOf(form) {
    return Number(form.dataset.quantity ?? 0);
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initQuantityForms);
} else {
    initQuantityForms();
}
