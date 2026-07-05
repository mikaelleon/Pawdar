document.addEventListener('DOMContentLoaded', function () {
    var form = document.getElementById('register-dog-form');
    if (!form) {
        return;
    }

    var step = 1;
    var steps = form.querySelectorAll('[data-form-step]');
    var stepIndicators = document.querySelectorAll('[data-register-step-indicator]');
    var stepConnectors = document.querySelectorAll('.register-step-connector');
    var backBtn = form.querySelector('[data-step-back]');
    var nextBtn = form.querySelector('[data-step-next]');
    var submitBtn = form.querySelector('[data-step-submit]');
    var nameInput = form.querySelector('[name="dog_name"]');
    var requirementsHint = form.querySelector('[data-step-requirements]');
    var nameError = form.querySelector('[data-field-error="dog-name"]');
    var breedError = form.querySelector('[data-field-error="breed"]');

    function showStep(n) {
        step = Math.max(1, Math.min(3, n));
        steps.forEach(function (el) {
            el.hidden = parseInt(el.getAttribute('data-form-step'), 10) !== step;
        });

        stepIndicators.forEach(function (indicator) {
            var num = parseInt(indicator.getAttribute('data-register-step-indicator'), 10);
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

        stepConnectors.forEach(function (conn, i) {
            conn.classList.toggle('is-done', (i + 1) < step);
        });

        if (backBtn) backBtn.hidden = step <= 1;
        if (nextBtn) nextBtn.hidden = step >= 3;
        if (submitBtn) submitBtn.hidden = step !== 3;

        if (step === 3) renderReview();
        checkFormReady();
        updateRequirementsHint();
        form.querySelector('.register-form-panel:not([hidden])')?.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        if (window.lucide) lucide.createIcons();
    }

    function setInlineError(el, messageEl, show, message) {
        if (el) el.classList.toggle('is-invalid', show);
        if (!messageEl) return;
        if (message) messageEl.textContent = message;
        messageEl.hidden = !show;
    }

    function getStep1Missing() {
        var missing = [];
        if (!nameInput.value.trim()) missing.push('Dog name');
        if (!breedReady()) missing.push('Breed selection');
        return missing;
    }

    function updateRequirementsHint() {
        if (!requirementsHint || step !== 1) {
            if (requirementsHint) requirementsHint.hidden = true;
            return;
        }

        var missing = getStep1Missing();
        if (!missing.length) {
            requirementsHint.hidden = true;
            return;
        }

        requirementsHint.hidden = false;
        requirementsHint.innerHTML = '<strong>Before you continue:</strong> ' + missing.join(' · ');
    }

    function validateStep1() {
        var missing = getStep1Missing();
        var nameMissing = !nameInput.value.trim();
        var breedMissing = !breedReady();

        setInlineError(nameInput, nameError, nameMissing);
        setInlineError(breedInput, breedError, breedMissing);

        if (nameMissing || breedMissing) {
            updateRequirementsHint();
            (nameMissing ? nameInput : breedInput).focus();
            if (breedMissing && breedInput.value.trim().length >= 2 && !breedDropdown.hidden) {
                showBreedDropdown();
            }
            return false;
        }

        return true;
    }

    var vaccineNameInput = form.querySelector('[name="vaccine_name"]');
    var vaccineDateInput = form.querySelector('[name="vaccine_date"]');
    var vaccineDueInput = form.querySelector('[name="vaccine_due"]');
    var vetNameInput = form.querySelector('[name="vet_name"]');

    function clearFieldError(input) {
        if (!input) return;
        input.classList.remove('is-invalid');
        var msg = input.closest('.form-field')?.querySelector('[data-field-error]:not([data-field-error="dog-name"]):not([data-field-error="breed"])');
        if (msg) msg.hidden = true;
    }

    function showFieldError(input, message) {
        if (!input) return;
        input.classList.add('is-invalid');
        var field = input.closest('.form-field');
        if (!field) return;
        var existing = field.querySelector('[data-field-error]');
        if (existing) {
            existing.textContent = message;
            existing.hidden = false;
            return;
        }
        var el = document.createElement('p');
        el.className = 'field-error';
        el.setAttribute('data-field-error', '');
        el.textContent = message;
        field.appendChild(el);
    }

    function validateStep2() {
        var vaccineName = vaccineNameInput ? vaccineNameInput.value.trim() : '';
        var dateGiven = vaccineDateInput ? vaccineDateInput.value : '';
        var nextDue = vaccineDueInput ? vaccineDueInput.value : '';
        var vetName = vetNameInput ? vetNameInput.value.trim() : '';
        var hasPartialData = vaccineName !== '' || vetName !== '';

        clearFieldError(vaccineDateInput);
        clearFieldError(vaccineDueInput);

        if (!hasPartialData) {
            return true;
        }

        var valid = true;

        if (!dateGiven) {
            showFieldError(vaccineDateInput, 'Date given is required when adding vaccination details.');
            valid = false;
        }

        if (!nextDue) {
            showFieldError(vaccineDueInput, 'Next due date is required when adding vaccination details.');
            valid = false;
        }

        if (dateGiven && nextDue && new Date(nextDue) <= new Date(dateGiven)) {
            showFieldError(vaccineDueInput, 'Next due date must be after the date given.');
            valid = false;
        }

        if (!valid && vaccineDateInput && !dateGiven) {
            vaccineDateInput.focus();
        } else if (!valid && vaccineDueInput) {
            vaccineDueInput.focus();
        }

        return valid;
    }

    function isEmptyReviewValue(value) {
        if (!value) return true;
        var normalized = String(value).trim().toLowerCase();
        return normalized === 'none' || normalized === '—' || normalized === 'not specified' || normalized === 'not added';
    }

    function reviewRow(label, value, emptyLabel) {
        var display = value || emptyLabel || '—';
        var empty = isEmptyReviewValue(display);
        var rowClass = 'register-review-row' + (empty ? ' register-review-row--empty' : '');
        return '<div class="' + rowClass + '"><dt>' + esc(label) + '</dt><dd>' + esc(display) + '</dd></div>';
    }

    function reviewSection(title, stepNum, rowsHtml) {
        return '<section class="register-review-section">' +
            '<div class="register-review-section-head">' +
            '<h3>' + esc(title) + '</h3>' +
            '<button type="button" class="register-review-edit" data-edit-step="' + stepNum + '">Edit</button>' +
            '</div>' +
            '<dl class="register-review-list">' + rowsHtml + '</dl>' +
            '</section>';
    }

    function renderReview() {
        var box = document.getElementById('register-review');
        if (!box) return;
        var fd = new FormData(form);
        var photoFile = photoInput && photoInput.files[0];
        var photoValue = photoFile ? photoFile.name : 'Not added';

        box.innerHTML =
            reviewSection('Basic info', 1,
                reviewRow('Dog name', fd.get('dog_name')) +
                reviewRow('Breed', fd.get('breed_search')) +
                reviewRow('Sex', fd.get('gender')) +
                reviewRow('Age', fd.get('age') ? fd.get('age') + ' years' : 'Not specified', 'Not specified') +
                reviewRow('Dog type', fd.get('dog_type')) +
                reviewRow('Photo', photoValue, 'Not added')
            ) +
            reviewSection('Health records', 2,
                reviewRow('Vaccination', fd.get('vaccine_name'), 'Not added') +
                reviewRow('Date given', fd.get('vaccine_date'), 'Not added') +
                reviewRow('Next due', fd.get('vaccine_due'), 'Not added') +
                reviewRow('Veterinarian', fd.get('vet_name'), 'Not added') +
                reviewRow('Health notes', fd.get('health_notes'), 'Not added')
            );

        box.querySelectorAll('[data-edit-step]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                showStep(parseInt(btn.getAttribute('data-edit-step'), 10));
            });
        });
    }

    function esc(value) {
        var node = document.createElement('div');
        node.textContent = value || '';
        return node.innerHTML;
    }

    var breedWrapper = form.querySelector('[data-breed-wrapper]');
    var breedInput = form.querySelector('[data-breed-input]');
    var breedDropdown = form.querySelector('[data-breed-dropdown]');
    var breedIdInput = form.querySelector('[data-breed-id]');
    var breedClear = form.querySelector('[data-breed-clear]');
    var breedNoMatch = form.querySelector('[data-breed-no-match]');
    var breedCustomLabel = form.querySelector('[data-breed-custom-label]');
    var breedUseCustom = form.querySelector('[data-breed-use-custom]');
    var breedSelected = form.querySelector('[data-breed-selected]');
    var breedSize = form.querySelector('[data-breed-size]');
    var breedTemperament = form.querySelector('[data-breed-temperament]');

    var breedTimer = null;
    var breedResults = [];
    var breedActive = -1;
    var breedConfirmed = false;

    function breedReady() {
        return breedConfirmed && breedInput.value.trim().length > 0;
    }

    function fetchBreeds(query) {
        clearTimeout(breedTimer);
        breedTimer = setTimeout(function () {
            if (query.length < 2) {
                hideBreedDropdown();
                if (query.length > 0) {
                    breedDropdown.innerHTML = '<li class="breed-dropdown-hint">Type at least 2 letters to search breeds.</li>';
                    showBreedDropdown();
                }
                return;
            }
            breedDropdown.innerHTML = '<li class="breed-dropdown-loading">Searching…</li>';
            showBreedDropdown();
            fetch('ajax/search_breeds.php?q=' + encodeURIComponent(query))
                .then(function (res) { return res.json(); })
                .then(function (data) {
                    breedResults = (data && data.breeds) ? data.breeds : [];
                    renderBreeds(query);
                })
                .catch(function () {
                    breedDropdown.innerHTML = '<li class="breed-dropdown-hint">Could not load breeds. Try again.</li>';
                    showBreedDropdown();
                });
        }, 250);
    }

    function renderBreeds(query) {
        breedActive = -1;
        if (!breedResults.length) {
            hideBreedDropdown();
            breedCustomLabel.textContent = query;
            breedNoMatch.hidden = false;
            breedSelected.hidden = true;
            return;
        }
        breedNoMatch.hidden = true;
        breedDropdown.innerHTML = breedResults.map(function (breed, i) {
            var size = breed.size_category
                ? '<span class="dditem-size">' + esc(breed.size_category) + '</span>'
                : '';
            return '<li class="breed-dropdown-item" role="option" data-index="' + i + '" id="breed-option-' + i + '">' +
                '<span class="dditem-name">' + highlight(breed.breed_name, query) + '</span>' + size + '</li>';
        }).join('');
        breedDropdown.querySelectorAll('.breed-dropdown-item').forEach(function (item) {
            item.addEventListener('mousedown', function (e) {
                e.preventDefault();
                selectBreed(breedResults[parseInt(item.dataset.index, 10)]);
            });
            item.addEventListener('mouseenter', function () {
                setBreedActive(parseInt(item.dataset.index, 10));
            });
        });
        showBreedDropdown();
    }

    function highlight(text, query) {
        var q = query.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
        return esc(text).replace(new RegExp('(' + q + ')', 'gi'), '<mark>$1</mark>');
    }

    function selectBreed(breed) {
        if (!breed) return;
        breedInput.value = breed.breed_name;
        breedIdInput.value = breed.breed_id;
        breedConfirmed = true;
        hideBreedDropdown();
        breedNoMatch.hidden = true;
        breedClear.hidden = false;
        breedInput.classList.remove('is-invalid', 'breed-input--custom');
        breedInput.classList.add('breed-input--selected');
        setInlineError(breedInput, breedError, false);
        breedSize.textContent = breed.size_category || '';
        breedSize.hidden = !breed.size_category;
        breedTemperament.textContent = breed.temperament_notes || '';
        breedSelected.hidden = !(breed.size_category || breed.temperament_notes);
        checkFormReady();
    }

    function setBreedActive(index) {
        var items = breedDropdown.querySelectorAll('.breed-dropdown-item');
        items.forEach(function (item, i) {
            item.classList.toggle('active', i === index);
            item.setAttribute('aria-selected', i === index ? 'true' : 'false');
        });
        if (items[index]) items[index].scrollIntoView({ block: 'nearest' });
        breedActive = index;
        if (items[index]) breedInput.setAttribute('aria-activedescendant', items[index].id);
    }

    function showBreedDropdown() {
        breedDropdown.hidden = false;
        breedWrapper.classList.add('is-open');
        breedInput.setAttribute('aria-expanded', 'true');
    }

    function hideBreedDropdown() {
        breedDropdown.hidden = true;
        breedWrapper.classList.remove('is-open');
        breedInput.setAttribute('aria-expanded', 'false');
        breedInput.removeAttribute('aria-activedescendant');
        breedActive = -1;
    }

    if (breedInput) {
        breedInput.addEventListener('input', function () {
            var q = breedInput.value.trim();
            breedIdInput.value = '';
            breedConfirmed = false;
            breedSelected.hidden = true;
            breedInput.classList.remove('breed-input--selected', 'breed-input--custom');
            breedClear.hidden = q.length === 0;
            setInlineError(breedInput, breedError, false);
            fetchBreeds(q);
            checkFormReady();
        });

        breedInput.addEventListener('focus', function () {
            var q = breedInput.value.trim();
            if (q.length >= 2 && breedResults.length) {
                showBreedDropdown();
            } else if (q.length > 0 && q.length < 2) {
                breedDropdown.innerHTML = '<li class="breed-dropdown-hint">Type at least 2 letters to search breeds.</li>';
                showBreedDropdown();
            }
        });

        breedInput.addEventListener('keydown', function (e) {
            var items = breedDropdown.querySelectorAll('.breed-dropdown-item');
            if (!items.length) return;
            if (e.key === 'ArrowDown') {
                e.preventDefault();
                setBreedActive(Math.min(breedActive + 1, items.length - 1));
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                setBreedActive(Math.max(breedActive - 1, 0));
            } else if (e.key === 'Enter') {
                if (breedActive >= 0) {
                    e.preventDefault();
                    selectBreed(breedResults[breedActive]);
                }
            } else if (e.key === 'Escape') {
                hideBreedDropdown();
            }
        });

        breedClear.addEventListener('click', function () {
            breedInput.value = '';
            breedIdInput.value = '';
            breedConfirmed = false;
            breedClear.hidden = true;
            breedNoMatch.hidden = true;
            breedSelected.hidden = true;
            breedInput.classList.remove('breed-input--selected', 'breed-input--custom', 'is-invalid');
            setInlineError(breedInput, breedError, false);
            hideBreedDropdown();
            breedInput.focus();
            checkFormReady();
        });

        breedUseCustom.addEventListener('click', function () {
            breedConfirmed = true;
            breedIdInput.value = '';
            breedNoMatch.hidden = true;
            breedClear.hidden = false;
            breedInput.classList.remove('is-invalid', 'breed-input--selected');
            breedInput.classList.add('breed-input--custom');
            setInlineError(breedInput, breedError, false);
            hideBreedDropdown();
            checkFormReady();
        });

        document.addEventListener('click', function (e) {
            if (!e.target.closest('[data-breed-wrapper]')) hideBreedDropdown();
        });
    }

    function checkFormReady() {
        if (!nextBtn) return;
        if (step === 1) {
            var ready = nameInput.value.trim().length > 0 && breedReady();
            nextBtn.disabled = !ready;
            nextBtn.classList.toggle('btn--ready', ready);
            nextBtn.setAttribute('aria-disabled', ready ? 'false' : 'true');
            updateRequirementsHint();
            return;
        }
        nextBtn.disabled = false;
        nextBtn.classList.add('btn--ready');
        nextBtn.setAttribute('aria-disabled', 'false');
        if (requirementsHint) requirementsHint.hidden = true;
    }

    nameInput.addEventListener('input', function () {
        setInlineError(nameInput, nameError, false);
        checkFormReady();
    });

    nameInput.addEventListener('blur', function () {
        if (step === 1 && !nameInput.value.trim()) {
            setInlineError(nameInput, nameError, true);
        }
    });

    [vaccineNameInput, vetNameInput, vaccineDateInput, vaccineDueInput].forEach(function (input) {
        if (!input) return;
        input.addEventListener('input', function () {
            clearFieldError(vaccineDateInput);
            clearFieldError(vaccineDueInput);
        });
    });

    var photoInput = form.querySelector('[data-photo-input]');
    var photoPreview = form.querySelector('[data-photo-preview]');
    var photoLabel = form.querySelector('[data-photo-label]');
    var photoUpload = form.querySelector('[data-photo-upload]');
    var photoIcon = form.querySelector('[data-photo-icon]');
    var photoRemove = form.querySelector('[data-photo-remove]');
    var photoError = form.querySelector('[data-photo-error]');

    function clearPhotoError() {
        if (!photoError) return;
        photoError.hidden = true;
        photoError.textContent = '';
        photoUpload.classList.remove('is-invalid');
    }

    function showPhotoError(message) {
        if (!photoError) return;
        photoError.textContent = message;
        photoError.hidden = false;
        photoUpload.classList.add('is-invalid');
    }

    function resetPhoto() {
        if (photoInput) photoInput.value = '';
        if (photoPreview) {
            photoPreview.src = '';
            photoPreview.hidden = true;
        }
        if (photoLabel) photoLabel.textContent = 'Tap to add a photo (JPG or PNG, max 5MB)';
        if (photoIcon) photoIcon.hidden = false;
        if (photoRemove) photoRemove.hidden = true;
        if (photoUpload) photoUpload.classList.remove('has-preview');
        clearPhotoError();
    }

    if (photoInput && photoUpload) {
        photoUpload.addEventListener('click', function (e) {
            if (e.target === photoInput || e.target.closest('[data-photo-remove]')) return;
            photoInput.click();
        });
        photoUpload.addEventListener('dragover', function (e) {
            e.preventDefault();
            photoUpload.classList.add('is-dragover');
        });
        photoUpload.addEventListener('dragleave', function () {
            photoUpload.classList.remove('is-dragover');
        });
        photoUpload.addEventListener('drop', function (e) {
            e.preventDefault();
            photoUpload.classList.remove('is-dragover');
            if (e.dataTransfer.files[0]) handlePhoto(e.dataTransfer.files[0]);
        });
        photoInput.addEventListener('change', function () {
            if (photoInput.files[0]) handlePhoto(photoInput.files[0]);
        });
    }

    if (photoRemove) {
        photoRemove.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            resetPhoto();
        });
    }

    function handlePhoto(file) {
        clearPhotoError();
        if (!/^image\/(jpeg|png)$/.test(file.type)) {
            showPhotoError('Only JPG and PNG files are allowed.');
            photoInput.value = '';
            return;
        }
        if (file.size > 5 * 1024 * 1024) {
            showPhotoError('Photo must be under 5 MB. Choose a smaller file.');
            photoInput.value = '';
            return;
        }

        if (photoLabel) photoLabel.textContent = file.name;
        if (photoIcon) photoIcon.hidden = true;
        if (photoRemove) photoRemove.hidden = false;
        if (photoPreview) {
            var reader = new FileReader();
            reader.onload = function () {
                photoPreview.src = reader.result;
                photoPreview.hidden = false;
                photoUpload.classList.add('has-preview');
            };
            reader.readAsDataURL(file);
        }
        if (file && photoInput.files.length === 0) {
            var dt = new DataTransfer();
            dt.items.add(file);
            photoInput.files = dt.files;
        }
    }

    if (backBtn) backBtn.addEventListener('click', function () { showStep(step - 1); });
    if (nextBtn) nextBtn.addEventListener('click', function () {
        if (step === 1 && !validateStep1()) return;
        if (step === 2 && !validateStep2()) return;
        showStep(step + 1);
    });
    form.addEventListener('submit', function (e) {
        if (step !== 3) e.preventDefault();
    });

    checkFormReady();
    showStep(1);
});
