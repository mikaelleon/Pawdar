(function () {
    var currentStep = 1;
    var csrfToken = '';
    var onSuccessCallback = null;
    var drawerInitialized = false;

    function drawer() { return document.querySelector('[data-report-drawer]'); }
    function overlay() { return document.querySelector('[data-report-drawer-overlay]'); }
    function form() { return document.querySelector('[data-report-form]'); }

    function openReportDrawer() {
        openReportDrawerPrefill(null);
    }

    function resetReportPrefill() {
        var f = form();
        if (!f) return;
        var dogIdInput = f.querySelector('#report-dog-id');
        var dogSearch = f.querySelector('#report-dog-search');
        if (dogIdInput) dogIdInput.value = '';
        if (dogSearch) {
            dogSearch.value = '';
            dogSearch.readOnly = false;
        }
    }

    function openReportDrawerPrefill(options) {
        var d = drawer();
        var o = overlay();
        if (!d || !o) return;

        resetReportPrefill();
        currentStep = 1;
        showReportStep(1);
        d.classList.add('is-open');
        o.classList.add('is-open');
        d.setAttribute('aria-hidden', 'false');
        o.setAttribute('aria-hidden', 'false');
        document.body.classList.add('drawer-open');

        if (!options) return;

        var f = form();
        if (!f) return;

        var dogIdInput = f.querySelector('#report-dog-id');
        var dogSearch = f.querySelector('#report-dog-search');
        if (options.dogId && dogIdInput) {
            dogIdInput.value = String(options.dogId);
        }
        if (dogSearch) {
            var label = options.dogName || '';
            if (options.registryId) {
                label = label ? label + ' · ' + options.registryId : options.registryId;
            }
            dogSearch.value = label;
            if (options.dogId) {
                dogSearch.readOnly = true;
            }
        }
        if (options.incidentType) {
            var typeInput = f.querySelector('input[name="incident_type"][value="' + options.incidentType + '"]');
            if (typeInput) {
                typeInput.checked = true;
            }
        }
    }

    function closeReportDrawer() {
        var d = drawer();
        var o = overlay();
        if (!d || !o) return;

        d.classList.remove('is-open');
        o.classList.remove('is-open');
        d.setAttribute('aria-hidden', 'true');
        o.setAttribute('aria-hidden', 'true');
        document.body.classList.remove('drawer-open');
        currentStep = 1;
        resetReportPrefill();
    }

    function showReportStep(step) {
        currentStep = Math.max(1, Math.min(3, step));
        var f = form();
        if (!f) return;

        f.querySelectorAll('[data-report-step]').forEach(function (panel) {
            panel.hidden = parseInt(panel.getAttribute('data-report-step'), 10) !== currentStep;
        });

        document.querySelectorAll('[data-step-indicator]').forEach(function (dot) {
            var n = parseInt(dot.getAttribute('data-step-indicator'), 10);
            dot.classList.remove('is-active', 'is-done');
            dot.textContent = String(n);
            if (n < currentStep) {
                dot.classList.add('is-done');
                dot.innerHTML = '<i data-lucide="check" style="width:14px;height:14px;"></i>';
            } else if (n === currentStep) {
                dot.classList.add('is-active');
            }
        });

        document.querySelectorAll('[data-progress-line]').forEach(function (line, i) {
            line.classList.toggle('is-done', (i + 1) < currentStep);
        });

        if (window.lucide) lucide.createIcons();
    }

    function initReportDrawer(token, onSuccess) {
        csrfToken = token || getCsrfToken();
        if (typeof onSuccess === 'function') {
            onSuccessCallback = onSuccess;
        }
        var f = form();
        if (!f) return;
        if (drawerInitialized) {
            return;
        }
        drawerInitialized = true;

        var submitBtn = f.querySelector('[data-report-submit]');

        document.querySelectorAll('[data-report-next]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                if (currentStep === 1) {
                    var typeChecked = f.querySelector('input[name="incident_type"]:checked');
                    if (!typeChecked) {
                        if (window.PawdarUI && PawdarUI.showToast) {
                            PawdarUI.showToast('Please select an incident type.', 'error');
                        }
                        return;
                    }
                }
                if (currentStep === 2) {
                    var location = f.querySelector('[name="location"]');
                    if (location && !location.value.trim()) {
                        location.focus();
                        if (window.PawdarUI && PawdarUI.showToast) {
                            PawdarUI.showToast('Location is required.', 'error');
                        }
                        return;
                    }
                    var desc = f.querySelector('#report-description');
                    if (desc && charCount) {
                        charCount.textContent = desc.value.length + ' / 280';
                    }
                }
                showReportStep(currentStep + 1);
            });
        });

        document.querySelectorAll('[data-report-back]').forEach(function (btn) {
            btn.addEventListener('click', function () { showReportStep(currentStep - 1); });
        });

        var geoBtn = f.querySelector('[data-use-location]');
        var geoStatus = f.querySelector('[data-geo-status]');
        if (geoBtn) {
            geoBtn.addEventListener('click', function () {
                if (!navigator.geolocation) {
                    if (geoStatus) geoStatus.textContent = 'Geolocation is not supported on this device.';
                    if (window.PawdarUI && PawdarUI.showToast) {
                        PawdarUI.showToast('Geolocation is not supported on this device.', 'error');
                    }
                    return;
                }
                geoBtn.disabled = true;
                if (geoStatus) geoStatus.textContent = 'Getting your location…';
                navigator.geolocation.getCurrentPosition(function (pos) {
                    var latInput = f.querySelector('#report-latitude');
                    var lngInput = f.querySelector('#report-longitude');
                    if (latInput) latInput.value = pos.coords.latitude.toFixed(7);
                    if (lngInput) lngInput.value = pos.coords.longitude.toFixed(7);
                    f.querySelector('#report-location').value =
                        pos.coords.latitude.toFixed(5) + ', ' + pos.coords.longitude.toFixed(5);
                    geoBtn.disabled = false;
                    if (geoStatus) geoStatus.textContent = 'Location captured.';
                }, function () {
                    geoBtn.disabled = false;
                    if (geoStatus) geoStatus.textContent = 'Could not get location. Enter it manually.';
                    if (window.PawdarUI && PawdarUI.showToast) {
                        PawdarUI.showToast('Could not get your location. Please enter it manually.', 'error');
                    }
                }, { enableHighAccuracy: true, timeout: 10000 });
            });
        }

        var desc = f.querySelector('#report-description');
        var charCount = f.querySelector('[data-char-count]');
        if (desc && charCount) {
            desc.addEventListener('input', function () {
                charCount.textContent = desc.value.length + ' / 280';
            });
        }

        var dogSearch = f.querySelector('#report-dog-search');
        var dogResults = f.querySelector('[data-dog-search-results]');
        var dogTimer;
        if (dogSearch && dogResults) {
            dogSearch.addEventListener('input', function () {
                clearTimeout(dogTimer);
                dogTimer = setTimeout(function () {
                    fetch('ajax/search_dogs.php?q=' + encodeURIComponent(dogSearch.value))
                        .then(function (res) { return res.json(); })
                        .then(function (data) {
                            if (!data.dogs || !data.dogs.length) {
                                dogResults.hidden = true;
                                return;
                            }
                            dogResults.hidden = false;
                            dogResults.innerHTML = data.dogs.map(function (dog) {
                                return '<button type="button" class="dog-search-item" data-dog-id="' + dog.id + '" data-dog-name="' + dog.name + '">' + dog.name + '</button>';
                            }).join('');
                        });
                }, 300);
            });
            dogResults.addEventListener('click', function (e) {
                var item = e.target.closest('[data-dog-id]');
                if (!item) return;
                f.querySelector('#report-dog-id').value = item.getAttribute('data-dog-id');
                dogSearch.value = item.getAttribute('data-dog-name');
                dogResults.hidden = true;
            });
        }

        var dropzone = f.querySelector('[data-photo-dropzone]');
        var photoInput = f.querySelector('#report-photo');
        var preview = f.querySelector('[data-photo-preview]');
        if (dropzone && photoInput) {
            dropzone.addEventListener('click', function () { photoInput.click(); });
            photoInput.addEventListener('change', function () {
                if (!photoInput.files[0]) return;
                var file = photoInput.files[0];
                if (file.size > 5 * 1024 * 1024) return;
                var reader = new FileReader();
                reader.onload = function () {
                    if (preview) {
                        preview.src = reader.result;
                        preview.hidden = false;
                    }
                };
                reader.readAsDataURL(file);
            });
        }

        f.addEventListener('submit', function (event) {
            event.preventDefault();
            if (window.PawdarUI) PawdarUI.setButtonLoading(submitBtn, true);
            var formData = new FormData(f);
            formData.set('csrf_token', csrfToken);

            fetch('ajax/submit-report.php', {
                method: 'POST',
                headers: { 'X-CSRF-Token': csrfToken },
                body: formData
            })
                .then(function (res) { return res.json(); })
                .then(function (data) {
                    if (window.PawdarUI) PawdarUI.setButtonLoading(submitBtn, false);
                    if (data.success) {
                        closeReportDrawer();
                        f.reset();
                        if (preview) preview.hidden = true;
                        showReportStep(1);
                        if (window.PawdarUI && PawdarUI.showToast) {
                            PawdarUI.showToast('Your report has been submitted', 'success');
                        }
                        if (typeof onSuccessCallback === 'function') onSuccessCallback();
                    } else if (window.PawdarUI && PawdarUI.showToast) {
                        PawdarUI.showToast(data.message || 'Failed to submit report', 'error');
                    }
                })
                .catch(function () {
                    if (window.PawdarUI) PawdarUI.setButtonLoading(submitBtn, false);
                    if (window.PawdarUI && PawdarUI.showToast) {
                        PawdarUI.showToast('Network error. Please check your connection.', 'error');
                    }
                });
        });
    }

    function getCsrfToken() {
        var meta = document.querySelector('meta[name="csrf-token"]');
        if (meta) return meta.getAttribute('content') || '';
        var feed = document.querySelector('[data-feed-page]');
        return feed ? feed.getAttribute('data-csrf') || '' : '';
    }

    document.addEventListener('click', function (e) {
        if (e.target.closest('[data-open-report-drawer], [data-action="open-report"]')) {
            e.preventDefault();
            openReportDrawer();
            return;
        }
        if (e.target.closest('[data-close-report-drawer]')) {
            e.preventDefault();
            e.stopPropagation();
            closeReportDrawer();
            return;
        }
        if (e.target.closest('[data-report-drawer-overlay]')) {
            closeReportDrawer();
        }
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && drawer() && drawer().classList.contains('is-open')) {
            closeReportDrawer();
        }
    });

    window.openReportDrawer = openReportDrawer;
    window.openReportDrawerPrefill = openReportDrawerPrefill;
    window.closeReportDrawer = closeReportDrawer;
    window.initReportDrawer = initReportDrawer;

    document.addEventListener('DOMContentLoaded', function () {
        if (form()) {
            initReportDrawer(getCsrfToken(), null);
        }
    });
})();
