document.addEventListener('DOMContentLoaded', function () {
    initSignupWizard();
});

function initSignupWizard() {
    var form = document.getElementById('signup-form');
    if (!form || form.dataset.signupWizard !== '1') {
        return;
    }

    var step = 1;
    var maxStep = 2;
    var panels = form.querySelectorAll('[data-form-step]');
    var indicators = document.querySelectorAll('[data-signup-step-indicator]');
    var connectors = document.querySelectorAll('[data-signup-connector]');
    var stepper = document.querySelector('[data-signup-stepper]');
    var backBtn = form.querySelector('[data-step-back]');
    var nextBtn = form.querySelector('[data-step-next]');
    var submitBtn = form.querySelector('[data-step-submit]');
    var saveLaterBtn = form.querySelector('[data-save-later]');
    var password = form.querySelector('#password');
    var confirm = form.querySelector('#password_confirm');
    var email = form.querySelector('#email');
    var phoneLocal = form.querySelector('#phone_local');
    var phoneLabel = form.querySelector('[data-phone-label]');
    var phoneHint = form.querySelector('[data-phone-hint]');
    var citySelect = form.querySelector('#city_id');
    var barangaySelect = form.querySelector('#barangay_id');
    var terms = form.querySelector('#terms');
    var matchMessage = form.querySelector('[data-match-message]');
    var weakHint = form.querySelector('[data-password-weak]');
    var strengthFill = form.querySelectorAll('[data-strength-seg]');
    var strengthLabel = form.querySelector('[data-strength-label]');
    var strengthSr = form.querySelector('[data-strength-sr]');
    var approvalNote = form.querySelector('[data-approval-note]');
    var roleCards = Array.prototype.slice.call(document.querySelectorAll('[data-role-card]'));
    var phoneRequiredRoles = JSON.parse(form.dataset.phoneRequiredRoles || '[]');
    var emailTimer;
    var emailExists = false;
    var draftKey = 'pawdar_signup_draft_v2';

    function getPasswordScore(value) {
        var score = 0;
        if ((value || '').length >= 6) score++;
        if (/[A-Z]/.test(value) && /[a-z]/.test(value)) score++;
        if (/\d/.test(value)) score++;
        if (/[^A-Za-z0-9]/.test(value)) score++;
        return score;
    }

    function currentRole() {
        var input = form.querySelector('#role-input');
        return input ? input.value : '';
    }

    function phoneRequiredForRole(role) {
        return phoneRequiredRoles.indexOf(role) !== -1;
    }

    function updatePhoneRequirement() {
        var required = phoneRequiredForRole(currentRole());
        if (phoneLocal) {
            phoneLocal.required = required;
        }
        if (phoneLabel) {
            phoneLabel.textContent = required ? 'Contact number *' : 'Contact number';
        }
        if (phoneHint) {
            phoneHint.textContent = required
                ? 'Required for your role. Used for Call Owner and official contact.'
                : 'Optional for Community Reporters.';
        }
    }

    function updateApprovalNote(roleValue) {
        if (!approvalNote) return;
        var card = roleCards.find(function (item) {
            return item.getAttribute('data-role-value') === roleValue;
        });
        approvalNote.classList.toggle('is-visible', !!(card && card.getAttribute('data-requires-approval') === '1'));
    }

    function selectRoleCard(card) {
        if (!card || card.classList.contains('is-disabled')) return;

        roleCards.forEach(function (c) {
            c.classList.remove('is-selected');
            c.setAttribute('aria-checked', 'false');
            c.setAttribute('tabindex', '-1');
            var existing = c.querySelector('.role-card-check');
            if (existing) existing.remove();
        });

        card.classList.add('is-selected');
        card.setAttribute('aria-checked', 'true');
        card.setAttribute('tabindex', '0');

        var check = document.createElement('span');
        check.className = 'role-card-check';
        check.setAttribute('aria-hidden', 'true');
        check.innerHTML = '<i data-lucide="check"></i>';
        card.appendChild(check);
        if (window.lucide) lucide.createIcons();

        var roleInput = form.querySelector('#role-input');
        var roleValue = card.getAttribute('data-role-value') || '';
        if (roleInput) roleInput.value = roleValue;
        updateApprovalNote(roleValue);
        updatePhoneRequirement();
        validateCurrentStep();
    }

    roleCards.forEach(function (card, index) {
        card.addEventListener('click', function () { selectRoleCard(card); });
        card.addEventListener('keydown', function (event) {
            if (event.key === ' ' || event.key === 'Enter') {
                event.preventDefault();
                selectRoleCard(card);
            } else if (event.key === 'ArrowRight' || event.key === 'ArrowDown') {
                event.preventDefault();
                selectRoleCard(roleCards[(index + 1) % roleCards.length]);
            } else if (event.key === 'ArrowLeft' || event.key === 'ArrowUp') {
                event.preventDefault();
                selectRoleCard(roleCards[(index - 1 + roleCards.length) % roleCards.length]);
            }
        });
    });

    function announceStep(n) {
        if (!stepper) return;
        var labels = ['Account', 'Role and location', 'Verify email'];
        stepper.setAttribute('aria-label', 'Sign up step ' + n + ' of 3: ' + (labels[n - 1] || ''));
    }

    function showStep(n) {
        step = Math.max(1, Math.min(maxStep, n));
        panels.forEach(function (panel) {
            panel.hidden = parseInt(panel.getAttribute('data-form-step'), 10) !== step;
        });

        indicators.forEach(function (indicator) {
            var num = parseInt(indicator.getAttribute('data-signup-step-indicator'), 10);
            var circle = indicator.querySelector('.register-step-circle');
            indicator.classList.remove('is-active', 'is-done');
            if (num < step) {
                indicator.classList.add('is-done');
                circle.textContent = '✓';
            } else if (num === step) {
                indicator.classList.add('is-active');
                circle.textContent = String(num);
            } else {
                circle.textContent = String(num);
            }
        });

        connectors.forEach(function (conn) {
            conn.classList.toggle('is-done', parseInt(conn.getAttribute('data-signup-connector'), 10) < step);
        });

        if (backBtn) backBtn.hidden = step <= 1;
        if (nextBtn) nextBtn.hidden = step >= maxStep;
        if (submitBtn) submitBtn.hidden = step !== maxStep;
        if (saveLaterBtn) saveLaterBtn.hidden = step <= 1;

        announceStep(step);
        validateCurrentStep();
        if (window.lucide) lucide.createIcons();
    }

    function loadBarangays(cityId, selectedId) {
        if (!barangaySelect) return;
        barangaySelect.innerHTML = '<option value="" disabled selected>Select barangay</option>';
        barangaySelect.disabled = true;
        if (!cityId) return;

        fetch('ajax/barangays.php?city_id=' + encodeURIComponent(cityId))
            .then(function (res) { return res.json(); })
            .then(function (data) {
                (data.barangays || []).forEach(function (row) {
                    var opt = document.createElement('option');
                    opt.value = row.barangay_id;
                    opt.textContent = row.name;
                    if (selectedId && String(selectedId) === String(row.barangay_id)) {
                        opt.selected = true;
                    }
                    barangaySelect.appendChild(opt);
                });
                barangaySelect.disabled = false;
                validateCurrentStep();
            });
    }

    if (citySelect) {
        citySelect.addEventListener('change', function () {
            loadBarangays(citySelect.value, null);
            validateCurrentStep();
        });
    }

    if (password) {
        password.addEventListener('input', function () {
            updateStrength(password.value, strengthFill, strengthLabel, strengthSr);
            validateConfirm(confirm, password, false, matchMessage);
            if (weakHint) {
                weakHint.hidden = !password.value || getPasswordScore(password.value) >= 2;
            }
            validateCurrentStep();
        });
    }

    if (confirm && password) {
        confirm.addEventListener('input', function () {
            validateConfirm(confirm, password, false, matchMessage);
            validateCurrentStep();
        });
        confirm.addEventListener('blur', function () {
            validateConfirm(confirm, password, true, matchMessage);
            validateCurrentStep();
        });
    }

    if (email) {
        email.addEventListener('blur', function () {
            if (email.value.trim() === '') {
                PawdarUI.clearFieldError(email);
                emailExists = false;
                validateCurrentStep();
                return;
            }
            if (!email.validity.valid) {
                PawdarUI.showFieldError(email, 'Please enter a valid email address.');
                validateCurrentStep();
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
                            PawdarUI.showFieldErrorHtml(email, 'An account with this email already exists. <a href="login.php">Log in instead</a>');
                        } else {
                            PawdarUI.clearFieldError(email);
                        }
                        validateCurrentStep();
                    });
            }, 400);
        });
    }

    if (phoneLocal) {
        phoneLocal.addEventListener('blur', function () {
            validatePhoneField(true);
            validateCurrentStep();
        });
        phoneLocal.addEventListener('input', function () {
            phoneLocal.value = phoneLocal.value.replace(/\D/g, '').slice(0, 10);
            validateCurrentStep();
        });
    }

    form.addEventListener('input', validateCurrentStep);
    form.addEventListener('change', validateCurrentStep);

    if (terms) terms.addEventListener('change', validateCurrentStep);

    if (backBtn) {
        backBtn.addEventListener('click', function () {
            showStep(step - 1);
        });
    }

    if (nextBtn) {
        nextBtn.addEventListener('click', function () {
            if (!validateStep(step, true)) return;
            showStep(step + 1);
        });
    }

    if (saveLaterBtn) {
        saveLaterBtn.addEventListener('click', function () {
            localStorage.setItem(draftKey, JSON.stringify(collectDraft()));
            if (window.PawdarUI) {
                PawdarUI.showToast('Progress saved. Return to Sign Up to continue.', 'success');
            }
        });
    }

    form.addEventListener('submit', function (event) {
        if (!validateStep(2, true)) {
            event.preventDefault();
            return;
        }
        localStorage.removeItem(draftKey);
        if (submitBtn) PawdarUI.setButtonLoading(submitBtn, true);
    });

    function validatePhoneField(showError) {
        if (!phoneLocal) return true;
        var val = phoneLocal.value.trim();
        var required = phoneRequiredForRole(currentRole());
        if (!required && val === '') {
            PawdarUI.clearFieldError(phoneLocal);
            return true;
        }
        if (required && val === '') {
            if (showError) PawdarUI.showFieldError(phoneLocal, 'This field is required.');
            return false;
        }
        if (!/^9\d{9}$/.test(val)) {
            if (showError) PawdarUI.showFieldError(phoneLocal, 'Enter 10 digits starting with 9 (e.g. 9171234567).');
            return false;
        }
        PawdarUI.clearFieldError(phoneLocal);
        return true;
    }

    function validateStep(stepNum, showErrors) {
        if (stepNum === 1) {
            var ok = true;
            ['last_name', 'first_name', 'email', 'password', 'password_confirm'].forEach(function (id) {
                var field = form.querySelector('#' + id);
                if (!field) return;
                if (!field.value.trim()) {
                    if (showErrors) PawdarUI.showFieldError(field, 'This field is required.');
                    ok = false;
                }
            });
            if (email && email.value.trim() && !email.validity.valid) {
                if (showErrors) PawdarUI.showFieldError(email, 'Please enter a valid email address.');
                ok = false;
            }
            if (emailExists) ok = false;
            if (password && getPasswordScore(password.value) < 2) {
                if (showErrors && weakHint) weakHint.hidden = false;
                ok = false;
            }
            if (password && confirm && password.value !== confirm.value) {
                if (showErrors) validateConfirm(confirm, password, true, matchMessage);
                ok = false;
            }
            form.querySelectorAll('.is-invalid').forEach(function () { ok = false; });
            return ok;
        }

        if (stepNum === 2) {
            var valid = true;
            if (!citySelect || !citySelect.value) {
                if (showErrors && citySelect) PawdarUI.showFieldError(citySelect, 'This field is required.');
                valid = false;
            }
            if (!barangaySelect || !barangaySelect.value) {
                if (showErrors && barangaySelect) PawdarUI.showFieldError(barangaySelect, 'This field is required.');
                valid = false;
            }
            if (!validatePhoneField(showErrors)) valid = false;
            if (terms && !terms.checked) valid = false;
            form.querySelectorAll('.is-invalid').forEach(function () { valid = false; });
            return valid;
        }

        return true;
    }

    function validateCurrentStep() {
        var ok = validateStep(step, false);
        if (step === 1 && nextBtn) nextBtn.disabled = !ok;
        if (step === 2 && submitBtn) submitBtn.disabled = !ok;
    }

    function collectDraft() {
        return {
            step: step,
            last_name: form.querySelector('#last_name')?.value || '',
            first_name: form.querySelector('#first_name')?.value || '',
            middle_name: form.querySelector('#middle_name')?.value || '',
            name_suffix: form.querySelector('#name_suffix')?.value || '',
            email: form.querySelector('#email')?.value || '',
            role: currentRole(),
            city_id: citySelect?.value || '',
            barangay_id: barangaySelect?.value || '',
            phone_local: phoneLocal?.value || '',
        };
    }

    function restoreDraft() {
        var raw = localStorage.getItem(draftKey);
        if (!raw) return;
        try {
            var data = JSON.parse(raw);
            if (data.last_name) form.querySelector('#last_name').value = data.last_name;
            if (data.first_name) form.querySelector('#first_name').value = data.first_name;
            if (data.middle_name) form.querySelector('#middle_name').value = data.middle_name;
            if (data.name_suffix) form.querySelector('#name_suffix').value = data.name_suffix;
            if (data.email) form.querySelector('#email').value = data.email;
            if (data.phone_local && phoneLocal) phoneLocal.value = data.phone_local;
            if (data.role) {
                var card = roleCards.find(function (c) { return c.getAttribute('data-role-value') === data.role; });
                if (card) selectRoleCard(card);
            }
            if (data.city_id && citySelect) {
                citySelect.value = data.city_id;
                loadBarangays(data.city_id, data.barangay_id || null);
            }
            if (data.step) showStep(parseInt(data.step, 10) || 1);
            document.querySelectorAll('.float-field').forEach(function (field) {
                var input = field.querySelector('input');
                if (input) field.classList.toggle('has-value', input.value.trim() !== '');
            });
        } catch (e) {
            localStorage.removeItem(draftKey);
        }
    }

    if (form.dataset.signupError === 'exists' && email) {
        PawdarUI.showFieldErrorHtml(email, 'An account with this email already exists. <a href="login.php">Log in instead</a>');
        emailExists = true;
    }

    updateApprovalNote('Dog Owner');
    updatePhoneRequirement();
    restoreDraft();
    showStep(step);
}
