document.addEventListener('DOMContentLoaded', function () {
    initFloatingLabels();
    initPasswordToggles();
    initDemoDisclosure();
});

function initFloatingLabels() {
    document.querySelectorAll('.float-field').forEach(function (field) {
        var input = field.querySelector('input, textarea, select');
        if (!input) {
            return;
        }

        function sync() {
            field.classList.toggle('has-value', input.value.trim() !== '');
        }

        input.addEventListener('focus', function () { field.classList.add('is-focused'); });
        input.addEventListener('blur', function () { field.classList.remove('is-focused'); sync(); });
        input.addEventListener('input', sync);
        sync();
    });
}

function initPasswordToggles() {
    document.querySelectorAll('[data-toggle-password]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var targetId = btn.getAttribute('data-toggle-password');
            var input = document.getElementById(targetId);
            if (!input) {
                return;
            }

            var show = input.type === 'password';
            input.type = show ? 'text' : 'password';
            btn.setAttribute('aria-label', show ? 'Hide password' : 'Show password');
            btn.setAttribute('aria-pressed', show ? 'true' : 'false');

            var icon = btn.querySelector('[data-lucide]');
            if (icon && window.lucide) {
                icon.setAttribute('data-lucide', show ? 'eye-off' : 'eye');
                lucide.createIcons();
            }
        });
    });
}

function initDemoDisclosure() {
    document.querySelectorAll('[data-demo-toggle]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var panel = btn.nextElementSibling;
            if (!panel) {
                return;
            }
            var open = !panel.hasAttribute('hidden');
            if (open) {
                panel.setAttribute('hidden', '');
                btn.setAttribute('aria-expanded', 'false');
            } else {
                panel.removeAttribute('hidden');
                btn.setAttribute('aria-expanded', 'true');
            }
        });
    });
}

function setButtonLoading(btn, loading) {
    if (!btn) {
        return;
    }

    if (loading) {
        btn.dataset.originalText = btn.textContent;
        btn.classList.add('is-loading');
        btn.disabled = true;
        btn.innerHTML = '<span class="btn-dots"><span></span><span></span><span></span></span>';
    } else {
        btn.classList.remove('is-loading');
        btn.disabled = false;
        btn.textContent = btn.dataset.originalText || btn.textContent;
    }
}

function showToast(message, durationMs) {
    durationMs = durationMs || 4000;
    var container = document.querySelector('[data-toast-container]');
    if (!container) {
        container = document.createElement('div');
        container.className = 'toast-container';
        container.setAttribute('data-toast-container', '');
        container.setAttribute('aria-live', 'polite');
        document.body.appendChild(container);
    }

    var toast = document.createElement('div');
    toast.className = 'toast-message';
    toast.innerHTML = '<span class="toast-text"></span><span class="toast-progress"></span>';
    toast.querySelector('.toast-text').textContent = message;
    container.appendChild(toast);

    requestAnimationFrame(function () {
        toast.classList.add('is-visible');
        var bar = toast.querySelector('.toast-progress');
        bar.style.width = '100%';
        requestAnimationFrame(function () {
            bar.style.transition = 'width ' + durationMs + 'ms linear';
            bar.style.width = '0%';
        });
    });

    setTimeout(function () {
        toast.classList.remove('is-visible');
        setTimeout(function () { toast.remove(); }, 300);
    }, durationMs);
}

function showConfirmModal(options) {
    return new Promise(function (resolve) {
        var overlay = document.createElement('div');
        overlay.className = 'modal-overlay';
        overlay.innerHTML =
            '<div class="modal-card" role="dialog" aria-modal="true">' +
            '<h3 class="modal-title">' + escapeHtml(options.title || 'Confirm') + '</h3>' +
            '<p class="modal-body">' + escapeHtml(options.body || '') + '</p>' +
            '<div class="modal-actions">' +
            '<button type="button" class="btn-outline btn-sm" data-modal-cancel>Cancel</button>' +
            '<button type="button" class="btn-primary btn-sm" data-modal-confirm>' + escapeHtml(options.confirmLabel || 'Confirm') + '</button>' +
            '</div></div>';
        document.body.appendChild(overlay);
        document.body.classList.add('modal-open');

        function close(result) {
            overlay.remove();
            document.body.classList.remove('modal-open');
            resolve(result);
        }

        overlay.querySelector('[data-modal-cancel]').addEventListener('click', function () { close(false); });
        overlay.querySelector('[data-modal-confirm]').addEventListener('click', function () { close(true); });
        overlay.addEventListener('click', function (e) {
            if (e.target === overlay) {
                close(false);
            }
        });
    });
}

function showFieldError(input, message) {
    clearFieldError(input);
    input.classList.add('is-invalid');
    var err = document.createElement('p');
    err.className = 'field-error';
    err.setAttribute('role', 'alert');
    err.innerHTML = escapeHtml(message);
    input.closest('.float-field, .form-group')?.appendChild(err);
}

function clearFieldError(input) {
    input.classList.remove('is-invalid');
    var wrap = input.closest('.float-field, .form-group');
    if (wrap) {
        wrap.querySelectorAll('.field-error').forEach(function (el) { el.remove(); });
    }
}

function escapeHtml(text) {
    var div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

window.PawdarUI = {
    setButtonLoading: setButtonLoading,
    showToast: showToast,
    showConfirmModal: showConfirmModal,
    showFieldError: showFieldError,
    clearFieldError: clearFieldError
};
