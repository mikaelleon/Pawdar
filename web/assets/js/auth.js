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
    var submitBtn = form.querySelector('[data-login-submit]');
    var alertBox = document.querySelector('[data-login-alert]');
    var hasError = form.dataset.loginError === '1';
    var errorType = form.dataset.loginErrorType || 'invalid';
    var isLocked = form.dataset.locked === '1';

    if (hasError && !isLocked) {
        if (errorType === 'missing') {
            if (!email.value.trim()) {
                PawdarUI.showFieldError(email, 'This field is required.');
            }
            if (!password.value) {
                PawdarUI.showFieldError(password, 'This field is required.');
            }
        } else {
            markLoginError(email, password);
        }
    }

    function clearErrors() {
        PawdarUI.clearFieldError(email);
        PawdarUI.clearFieldError(password);
        if (alertBox) {
            alertBox.hidden = true;
            alertBox.innerHTML = '';
        }
    }

    [email, password].forEach(function (input) {
        if (!input) {
            return;
        }
        input.addEventListener('input', clearErrors);
    });

    form.addEventListener('submit', function (event) {
        event.preventDefault();

        if (isLocked || !submitBtn) {
            return;
        }

        clearErrors();

        var valid = true;
        if (!email.value.trim()) {
            PawdarUI.showFieldError(email, 'This field is required.');
            valid = false;
        } else if (!email.validity.valid) {
            PawdarUI.showFieldError(email, 'Please enter a valid email address.');
            valid = false;
        }

        if (!password.value) {
            PawdarUI.showFieldError(password, 'This field is required.');
            valid = false;
        }

        if (!valid) {
            (email.value.trim() ? password : email).focus();
            return;
        }

        setLoginButtonLoading(submitBtn, true);

        fetch(form.action, {
            method: 'POST',
            headers: {
                Accept: 'application/json',
            },
            body: new FormData(form),
        })
            .then(function (res) { return res.json().then(function (data) { return { ok: res.ok, data: data }; }); })
            .then(function (result) {
                if (result.data.success) {
                    showLoginSuccess(submitBtn, function () {
                        window.location.href = result.data.redirect || 'feed.php';
                    });
                    return;
                }

                setLoginButtonLoading(submitBtn, false);

                if (result.data.error === 'locked') {
                    if (alertBox) {
                        alertBox.hidden = false;
                        alertBox.innerHTML = '<p class="field-error">' + escapeHtml(result.data.message || 'Too many attempts.') + '</p>';
                    }
                    isLocked = true;
                    submitBtn.disabled = true;
                    return;
                }

                if (result.data.error === 'missing') {
                    if (!email.value.trim()) {
                        PawdarUI.showFieldError(email, 'This field is required.');
                    }
                    if (!password.value) {
                        PawdarUI.showFieldError(password, 'This field is required.');
                    }
                    return;
                }

                markLoginError(email, password);
            })
            .catch(function () {
                setLoginButtonLoading(submitBtn, false);
                if (alertBox) {
                    alertBox.hidden = false;
                    alertBox.innerHTML = '<p class="field-error">Network error. Please try again.</p>';
                }
            });
    });
}

function markLoginError(email, password) {
    if (email) {
        email.classList.add('is-invalid');
    }
    if (password) {
        PawdarUI.showFieldError(password, 'Incorrect email or password.');
    }
}

function setLoginButtonLoading(btn, loading) {
    if (!btn) {
        return;
    }

    if (loading) {
        btn.dataset.originalText = btn.textContent.trim();
        btn.classList.add('is-loading');
        btn.disabled = true;
        btn.style.opacity = '0.6';
        btn.innerHTML = '<span class="btn-spinner" aria-hidden="true"></span> Logging in…';
        return;
    }

    btn.classList.remove('is-loading', 'is-success');
    btn.disabled = false;
    btn.style.opacity = '';
    btn.textContent = btn.dataset.originalText || 'Log In';
}

function showLoginSuccess(btn, callback) {
    btn.classList.remove('is-loading');
    btn.classList.add('is-success');
    btn.disabled = true;
    btn.style.opacity = '1';
    btn.innerHTML = '<i data-lucide="check" aria-hidden="true"></i> Success';
    if (window.lucide) {
        lucide.createIcons();
    }
    setTimeout(callback, 250);
}

function escapeHtml(text) {
    var div = document.createElement('div');
    div.textContent = text || '';
    return div.innerHTML;
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
    var name = form.querySelector('#name');
    var barangay = form.querySelector('#barangay');
    var terms = form.querySelector('#terms');
    var submitBtn = form.querySelector('[type="submit"]');
    var strengthFill = form.querySelectorAll('[data-strength-seg]');
    var strengthLabel = form.querySelector('[data-strength-label]');
    var strengthSr = form.querySelector('[data-strength-sr]');
    var weakHint = form.querySelector('[data-password-weak]');
    var approvalNote = form.querySelector('[data-approval-note]');
    var matchMessage = form.querySelector('[data-match-message]');
    var roleCards = Array.prototype.slice.call(document.querySelectorAll('[data-role-card]'));
    var emailTimer;
    var emailExists = false;

    function getPasswordScore(value) {
        var score = 0;
        if (value.length >= 6) {
            score++;
        }
        if (/[A-Z]/.test(value) && /[a-z]/.test(value)) {
            score++;
        }
        if (/\d/.test(value)) {
            score++;
        }
        if (/[^A-Za-z0-9]/.test(value)) {
            score++;
        }
        return score;
    }

    function normalizePhone(value) {
        return (value || '').replace(/\s/g, '');
    }

    function isValidPhone(value) {
        var normalized = normalizePhone(value);
        return normalized === '' || /^(\+639|09)\d{9}$/.test(normalized);
    }

    function updateApprovalNote(roleValue) {
        if (!approvalNote) {
            return;
        }
        var card = roleCards.find(function (item) {
            return item.getAttribute('data-role-value') === roleValue;
        });
        var needsApproval = card && card.getAttribute('data-requires-approval') === '1';
        approvalNote.classList.toggle('is-visible', !!needsApproval);
    }

    function selectRoleCard(card) {
        if (!card || card.classList.contains('is-disabled')) {
            return;
        }

        roleCards.forEach(function (c) {
            c.classList.remove('is-selected');
            c.setAttribute('aria-checked', 'false');
            c.setAttribute('tabindex', '-1');
            var existing = c.querySelector('.role-card-check');
            if (existing) {
                existing.remove();
            }
        });

        card.classList.add('is-selected');
        card.setAttribute('aria-checked', 'true');
        card.setAttribute('tabindex', '0');
        card.focus();

        var check = document.createElement('span');
        check.className = 'role-card-check';
        check.setAttribute('aria-hidden', 'true');
        check.innerHTML = '<i data-lucide="check"></i>';
        card.appendChild(check);
        if (window.lucide) {
            lucide.createIcons();
        }

        var roleInput = document.getElementById('role-input');
        var roleValue = card.getAttribute('data-role-value') || '';
        if (roleInput) {
            roleInput.value = roleValue;
        }
        updateApprovalNote(roleValue);
        validateSignupForm();
    }

    roleCards.forEach(function (card, index) {
        card.addEventListener('click', function () {
            selectRoleCard(card);
        });

        card.addEventListener('keydown', function (event) {
            if (event.key === ' ' || event.key === 'Enter') {
                event.preventDefault();
                selectRoleCard(card);
                return;
            }

            if (event.key === 'ArrowRight' || event.key === 'ArrowDown') {
                event.preventDefault();
                var next = roleCards[(index + 1) % roleCards.length];
                selectRoleCard(next);
            }

            if (event.key === 'ArrowLeft' || event.key === 'ArrowUp') {
                event.preventDefault();
                var prev = roleCards[(index - 1 + roleCards.length) % roleCards.length];
                selectRoleCard(prev);
            }
        });
    });

    updateApprovalNote('Dog Owner');

    if (password) {
        password.addEventListener('input', function () {
            updateStrength(password.value, strengthFill, strengthLabel, strengthSr);
            validateConfirm(confirm, password, false, matchMessage);
            validateSignupForm();
        });
        password.addEventListener('blur', function () {
            if (password.value && getPasswordScore(password.value) < 2) {
                if (weakHint) {
                    weakHint.hidden = false;
                }
            }
        });
    }

    if (confirm && password) {
        confirm.addEventListener('input', function () {
            validateConfirm(confirm, password, false, matchMessage);
            validateSignupForm();
        });
        confirm.addEventListener('blur', function () {
            validateConfirm(confirm, password, true, matchMessage);
            validateSignupForm();
        });
    }

    if (email) {
        email.addEventListener('blur', function () {
            if (email.value.trim() === '') {
                PawdarUI.clearFieldError(email);
                emailExists = false;
                validateSignupForm();
                return;
            }
            if (!email.validity.valid) {
                PawdarUI.showFieldError(email, 'Please enter a valid email address.');
                validateSignupForm();
                return;
            }
            PawdarUI.clearFieldError(email);
            clearTimeout(emailTimer);
            emailTimer = setTimeout(function () {
                fetch('ajax/check_email.php?email=' + encodeURIComponent(email.value))
                    .then(function (res) { return res.json(); })
                    .then(function (data) {
                        emailExists = !!data.exists;
                        if (data.exists) {
                            PawdarUI.showFieldErrorHtml(
                                email,
                                'An account with this email already exists. <a href="login.php">Log in instead</a>'
                            );
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
            if (!isValidPhone(phone.value)) {
                PawdarUI.showFieldError(phone, 'Use Philippine mobile format: 09XX XXX XXXX.');
            } else {
                PawdarUI.clearFieldError(phone);
            }
            validateSignupForm();
        });
    }

    if (barangay) {
        barangay.addEventListener('change', validateSignupForm);
        barangay.addEventListener('blur', function () {
            if (!barangay.value) {
                PawdarUI.showFieldError(barangay, 'This field is required.');
            } else {
                PawdarUI.clearFieldError(barangay);
            }
            validateSignupForm();
        });
    }

    if (terms) {
        terms.addEventListener('change', validateSignupForm);
    }

    form.addEventListener('input', validateSignupForm);
    form.addEventListener('change', validateSignupForm);

    form.addEventListener('submit', function (event) {
        var valid = validateSignupSubmit(true);
        if (!valid) {
            event.preventDefault();
            return;
        }
        PawdarUI.setButtonLoading(submitBtn, true);
    });

    if (form.dataset.signupError === 'exists' && email) {
        PawdarUI.showFieldErrorHtml(
            email,
            'An account with this email already exists. <a href="login.php">Log in instead</a>'
        );
        emailExists = true;
    }

    validateSignupForm();

    function validateSignupSubmit(showErrors) {
        var valid = true;

        [name, email, password, confirm, barangay].forEach(function (field) {
            if (!field) {
                return;
            }
            if (!field.value || (field === barangay && !barangay.value)) {
                if (showErrors) {
                    PawdarUI.showFieldError(field, 'This field is required.');
                }
                valid = false;
            }
        });

        if (email && email.value.trim() && !email.validity.valid) {
            if (showErrors) {
                PawdarUI.showFieldError(email, 'Please enter a valid email address.');
            }
            valid = false;
        }

        if (emailExists) {
            valid = false;
        }

        if (phone && phone.value.trim() && !isValidPhone(phone.value)) {
            if (showErrors) {
                PawdarUI.showFieldError(phone, 'Use Philippine mobile format: 09XX XXX XXXX.');
            }
            valid = false;
        }

        if (password && password.value && getPasswordScore(password.value) < 2) {
            if (showErrors && weakHint) {
                weakHint.hidden = false;
            }
            valid = false;
        }

        if (password && confirm && password.value !== confirm.value) {
            if (showErrors) {
                validateConfirm(confirm, password, true, matchMessage);
            }
            valid = false;
        }

        if (terms && !terms.checked) {
            valid = false;
        }

        form.querySelectorAll('.is-invalid').forEach(function () {
            valid = false;
        });

        return valid;
    }

    function validateSignupForm() {
        if (!submitBtn) {
            return;
        }

        var valid = form.checkValidity();
        form.querySelectorAll('.is-invalid').forEach(function () { valid = false; });

        if (password && confirm && confirm.value && password.value !== confirm.value) {
            valid = false;
        }

        if (password && password.value && getPasswordScore(password.value) < 2) {
            valid = false;
            if (weakHint) {
                weakHint.hidden = getPasswordScore(password.value) >= 2 || password.value === '';
            }
        } else if (weakHint) {
            weakHint.hidden = true;
        }

        if (emailExists) {
            valid = false;
        }

        if (phone && phone.value.trim() && !isValidPhone(phone.value)) {
            valid = false;
        }

        if (terms && !terms.checked) {
            valid = false;
        }

        submitBtn.disabled = !valid;
    }
}

function updateStrength(value, segments, label, srLabel) {
    var score = 0;
    if (value.length >= 6) score++;
    if (/[A-Z]/.test(value) && /[a-z]/.test(value)) score++;
    if (/\d/.test(value)) score++;
    if (/[^A-Za-z0-9]/.test(value)) score++;

    var labels = ['', 'Weak', 'Fair', 'Strong', 'Very strong'];
    var text = value ? labels[score] : '';
    if (label) {
        label.textContent = text;
    }
    if (srLabel) {
        srLabel.textContent = value ? 'Password strength: ' + text : '';
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

    return score;
}

function validateConfirm(confirm, password, touched, matchMessage) {
    if (!confirm || !password) {
        return;
    }
    var wrap = confirm.closest('.float-field');
    var icon = wrap ? wrap.querySelector('[data-match-icon]') : null;
    var feedback = matchMessage || document.querySelector('[data-match-message]');

    if (feedback) {
        feedback.textContent = '';
        feedback.className = 'confirm-match-feedback';
        feedback.hidden = true;
    }

    if (confirm.value === '') {
        if (icon) {
            icon.textContent = '';
            icon.className = 'match-icon';
        }
        if (touched) {
            PawdarUI.clearFieldError(confirm);
        }
        return;
    }

    if (confirm.value === password.value) {
        if (icon) {
            icon.textContent = '✓';
            icon.className = 'match-icon is-match';
        }
        PawdarUI.clearFieldError(confirm);
        if (feedback) {
            feedback.textContent = 'Passwords match.';
            feedback.className = 'confirm-match-feedback is-match';
            feedback.hidden = false;
        }
        return;
    }

    if (icon) {
        icon.textContent = '✕';
        icon.className = 'match-icon is-mismatch';
    }
    if (touched) {
        PawdarUI.showFieldError(confirm, 'Passwords don\'t match.');
    }
}
