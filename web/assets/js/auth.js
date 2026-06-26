document.addEventListener('DOMContentLoaded', function () {
    initLoginForm();
    initSignupForm();
});

function initLoginForm() {
    var form = document.getElementById('login-form');
    if (!form) {
        return;
    }

    var email = form.querySelector('#email');
    var password = form.querySelector('#password');
    var submitBtns = document.querySelectorAll('[data-login-submit]');
    var hasError = form.dataset.loginError === '1';

    if (hasError) {
        markLoginError(form, email, password);
    }

    function clearError() {
        if (email) {
            PawdarUI.clearFieldError(email);
        }
        if (password) {
            PawdarUI.clearFieldError(password);
        }
    }

    [email, password].forEach(function (input) {
        if (!input) {
            return;
        }
        input.addEventListener('input', clearError);
    });

    form.addEventListener('submit', function () {
        submitBtns.forEach(function (btn) { PawdarUI.setButtonLoading(btn, true); });
    });
}

function markLoginError(form, email, password) {
    if (email) {
        email.classList.add('is-invalid');
    }
    if (password) {
        password.classList.add('is-invalid');
        PawdarUI.showFieldError(password, 'Incorrect email or password. Please try again.');
    }
}

function initSignupForm() {
    var form = document.getElementById('signup-form');
    if (!form) {
        return;
    }

    var password = form.querySelector('#password');
    var confirm = form.querySelector('#password_confirm');
    var email = form.querySelector('#email');
    var phone = form.querySelector('#phone');
    var submitBtn = form.querySelector('[type="submit"]');
    var strengthFill = document.querySelectorAll('[data-strength-seg]');
    var strengthLabel = document.querySelector('[data-strength-label]');
    var emailTimer;

    document.querySelectorAll('[data-role-card]').forEach(function (card) {
        card.addEventListener('click', function () {
            if (card.classList.contains('is-disabled')) {
                return;
            }
            document.querySelectorAll('[data-role-card]').forEach(function (c) {
                c.classList.remove('is-selected');
                var existing = c.querySelector('.role-card-check');
                if (existing) existing.remove();
            });
            card.classList.add('is-selected');
            var check = document.createElement('span');
            check.className = 'role-card-check';
            check.innerHTML = '<i data-lucide="check"></i>';
            card.appendChild(check);
            if (window.lucide) lucide.createIcons();
            var roleInput = document.getElementById('role-input');
            if (roleInput) {
                roleInput.value = card.getAttribute('data-role-value') || '';
            }
            validateSignupForm();
        });
    });

    if (password) {
        password.addEventListener('input', function () {
            updateStrength(password.value, strengthFill, strengthLabel);
            validateConfirm(confirm, password);
            validateSignupForm();
        });
    }

    if (confirm && password) {
        confirm.addEventListener('input', function () { validateConfirm(confirm, password); validateSignupForm(); });
        confirm.addEventListener('blur', function () { validateConfirm(confirm, password, true); validateSignupForm(); });
    }

    if (email) {
        email.addEventListener('blur', function () {
            if (!email.validity.valid) {
                PawdarUI.showFieldError(email, 'Enter a valid email address.');
                validateSignupForm();
                return;
            }
            PawdarUI.clearFieldError(email);
            clearTimeout(emailTimer);
            emailTimer = setTimeout(function () {
                fetch('ajax/check_email.php?email=' + encodeURIComponent(email.value))
                    .then(function (res) { return res.json(); })
                    .then(function (data) {
                        if (data.exists) {
                            PawdarUI.showFieldError(email, 'This email is already in use.');
                        } else {
                            PawdarUI.clearFieldError(email);
                        }
                        validateSignupForm();
                    });
            }, 400);
        });
    }

    if (phone) {
        phone.addEventListener('blur', function () {
            if (phone.value.trim() === '') {
                PawdarUI.clearFieldError(phone);
                validateSignupForm();
                return;
            }
            if (!/^(\+639|09)\d{9}$/.test(phone.value.replace(/\s/g, ''))) {
                PawdarUI.showFieldError(phone, 'Use format 09XXXXXXXXX or +639XXXXXXXXX.');
            } else {
                PawdarUI.clearFieldError(phone);
            }
            validateSignupForm();
        });
    }

    form.addEventListener('input', validateSignupForm);
    form.addEventListener('submit', function () {
        PawdarUI.setButtonLoading(submitBtn, true);
    });

    validateSignupForm();
}

function updateStrength(value, segments, label) {
    var score = 0;
    if (value.length >= 6) score++;
    if (/[A-Z]/.test(value) && /[a-z]/.test(value)) score++;
    if (/\d/.test(value)) score++;
    if (/[^A-Za-z0-9]/.test(value)) score++;

    var labels = ['', 'Weak', 'Fair', 'Strong', 'Very strong'];
    if (label) {
        label.textContent = value ? labels[score] : '';
    }

    segments.forEach(function (seg, i) {
        seg.className = 'strength-seg';
        if (i < score) {
            if (score === 1) seg.classList.add('is-weak');
            else if (score === 2) seg.classList.add('is-fair');
            else if (score === 3) seg.classList.add('is-good');
            else seg.classList.add('is-strong');
        }
    });
}

function validateConfirm(confirm, password, touched) {
    if (!confirm || !password) {
        return;
    }
    var wrap = confirm.closest('.float-field');
    var icon = wrap ? wrap.querySelector('[data-match-icon]') : null;
    if (confirm.value === '') {
        if (icon) icon.textContent = '';
        if (touched) PawdarUI.clearFieldError(confirm);
        return;
    }
    if (confirm.value === password.value) {
        if (icon) { icon.textContent = '✓'; icon.className = 'match-icon is-match'; }
        PawdarUI.clearFieldError(confirm);
    } else {
        if (icon) { icon.textContent = '✕'; icon.className = 'match-icon is-mismatch'; }
        if (touched) {
            PawdarUI.showFieldError(confirm, 'Passwords do not match.');
        }
    }
}

function validateSignupForm() {
    var form = document.getElementById('signup-form');
    var submitBtn = form ? form.querySelector('[type="submit"]') : null;
    if (!form || !submitBtn) {
        return;
    }

    var valid = form.checkValidity();
    form.querySelectorAll('.is-invalid').forEach(function () { valid = false; });
    var password = form.querySelector('#password');
    var confirm = form.querySelector('#password_confirm');
    if (password && confirm && password.value !== confirm.value) {
        valid = false;
    }
    submitBtn.disabled = !valid;
}
