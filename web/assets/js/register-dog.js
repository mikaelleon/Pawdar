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

    // ── Step navigation ───────────────────────────────────────────────────
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
        form.querySelector('.register-form-panel:not([hidden])')?.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }

    function validateStep1() {
        var valid = true;
        nameInput.classList.remove('is-invalid');
        if (!nameInput.value.trim()) {
            nameInput.classList.add('is-invalid');
            valid = false;
        }
        if (!breedReady()) {
            breedInput.classList.add('is-invalid');
            valid = false;
        }
        if (!valid) {
            (nameInput.value.trim() ? breedInput : nameInput).focus();
        }
        return valid;
    }

    var vaccineNameInput = form.querySelector('[name="vaccine_name"]');
    var vaccineDateInput = form.querySelector('[name="vaccine_date"]');
    var vaccineDueInput = form.querySelector('[name="vaccine_due"]');
    var vetNameInput = form.querySelector('[name="vet_name"]');

    function clearFieldError(input) {
        if (!input) {
            return;
        }
        input.classList.remove('is-invalid');
        var msg = input.closest('.form-field')?.querySelector('[data-field-error]');
        if (msg) {
            msg.remove();
        }
    }

    function showFieldError(input, message) {
        if (!input) {
            return;
        }
        input.classList.add('is-invalid');
        var field = input.closest('.form-field');
        if (!field) {
            return;
        }
        var existing = field.querySelector('[data-field-error]');
        if (existing) {
            existing.textContent = message;
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
            showFieldError(vaccineDateInput, 'Date given is required if adding a vaccine record.');
            valid = false;
        }

        if (!nextDue) {
            showFieldError(vaccineDueInput, 'Next due date is required if adding a vaccine record.');
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

    function renderReview() {
        var box = document.getElementById('register-review');
        if (!box) return;
        var fd = new FormData(form);
        var photoName = photoInput && photoInput.files[0] ? photoInput.files[0].name : 'None';
        box.innerHTML =
            '<dl class="register-review-list">' +
            reviewRow('Dog name', fd.get('dog_name')) +
            reviewRow('Breed', fd.get('breed_search')) +
            reviewRow('Sex', fd.get('gender')) +
            reviewRow('Age', fd.get('age') ? fd.get('age') + ' years' : 'Not specified') +
            reviewRow('Dog type', fd.get('dog_type')) +
            reviewRow('Photo', photoName) +
            reviewRow('Vaccination', fd.get('vaccine_name') || 'None') +
            reviewRow('Date given', fd.get('vaccine_date') || '—') +
            reviewRow('Next due', fd.get('vaccine_due') || '—') +
            reviewRow('Veterinarian', fd.get('vet_name') || '—') +
            '</dl>';
    }

    function reviewRow(label, value) {
        return '<div class="register-review-row"><dt>' + esc(label) + '</dt><dd>' + esc(value || '—') + '</dd></div>';
    }

    function esc(value) {
        var node = document.createElement('div');
        node.textContent = value || '';
        return node.innerHTML;
    }

    // ── Breed autocomplete ────────────────────────────────────────────────
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
                .catch(hideBreedDropdown);
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
            return '<li class="breed-dropdown-item" role="option" data-index="' + i + '">' +
                '<span class="dditem-name">' + highlight(breed.breed_name, query) + '</span>' + size + '</li>';
        }).join('');
        breedDropdown.querySelectorAll('.breed-dropdown-item').forEach(function (item) {
            item.addEventListener('click', function () {
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
        breedSize.textContent = breed.size_category || '';
        breedSize.hidden = !breed.size_category;
        breedTemperament.textContent = breed.temperament_notes || '';
        breedSelected.hidden = !(breed.size_category || breed.temperament_notes);
        checkFormReady();
    }

    function setBreedActive(index) {
        var items = breedDropdown.querySelectorAll('.breed-dropdown-item');
        items.forEach(function (item, i) { item.classList.toggle('active', i === index); });
        if (items[index]) items[index].scrollIntoView({ block: 'nearest' });
        breedActive = index;
    }

    function showBreedDropdown() {
        breedDropdown.hidden = false;
        breedInput.setAttribute('aria-expanded', 'true');
    }

    function hideBreedDropdown() {
        breedDropdown.hidden = true;
        breedInput.setAttribute('aria-expanded', 'false');
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
            fetchBreeds(q);
            checkFormReady();
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
            checkFormReady();
        });

        document.addEventListener('click', function (e) {
            if (!e.target.closest('[data-breed-wrapper]')) hideBreedDropdown();
        });
    }

    // ── Continue ready state ──────────────────────────────────────────────
    function checkFormReady() {
        if (!nextBtn) {
            return;
        }
        if (step === 1) {
            var ready = nameInput.value.trim().length > 0 && breedReady();
            nextBtn.disabled = !ready;
            nextBtn.classList.toggle('btn--ready', ready);
            return;
        }
        nextBtn.disabled = false;
        nextBtn.classList.add('btn--ready');
    }

    nameInput.addEventListener('input', function () {
        nameInput.classList.remove('is-invalid');
        checkFormReady();
    });

    // ── Photo upload ──────────────────────────────────────────────────────
    var photoInput = form.querySelector('[data-photo-input]');
    var photoPreview = form.querySelector('[data-photo-preview]');
    var photoLabel = form.querySelector('[data-photo-label]');
    var photoUpload = form.querySelector('[data-photo-upload]');

    if (photoInput && photoUpload) {
        photoUpload.addEventListener('click', function (e) {
            if (e.target === photoInput) return;
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

    function handlePhoto(file) {
        if (!/^image\/(jpeg|png)$/.test(file.type)) {
            alert('Only JPG and PNG files are allowed.');
            photoInput.value = '';
            return;
        }
        if (file.size > 5 * 1024 * 1024) {
            alert('Photo must be under 5 MB.');
            photoInput.value = '';
            return;
        }
        if (photoLabel) photoLabel.textContent = file.name;
        if (photoPreview) {
            var reader = new FileReader();
            reader.onload = function () {
                photoPreview.src = reader.result;
                photoPreview.hidden = false;
                photoUpload.classList.add('has-preview');
            };
            reader.readAsDataURL(file);
        }
        // keep dropped file in the input for submission
        if (file && photoInput.files.length === 0) {
            var dt = new DataTransfer();
            dt.items.add(file);
            photoInput.files = dt.files;
        }
    }

    // ── Buttons ───────────────────────────────────────────────────────────
    if (backBtn) backBtn.addEventListener('click', function () { showStep(step - 1); });
    if (nextBtn) nextBtn.addEventListener('click', function () {
        if (step === 1 && !validateStep1()) {
            return;
        }
        if (step === 2 && !validateStep2()) {
            return;
        }
        showStep(step + 1);
    });
    form.addEventListener('submit', function (e) {
        if (step !== 3) e.preventDefault();
    });

    checkFormReady();
    showStep(1);
});
