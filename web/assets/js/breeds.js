document.addEventListener('DOMContentLoaded', function () {
    var directory = document.querySelector('[data-breeds-directory]');
    if (!directory) return;

    var form = directory.querySelector('[data-breed-filter-form]');
    var sizeInput = directory.querySelector('[data-size-input]');
    var moodInput = directory.querySelector('[data-mood-input]');
    var localInput = directory.querySelector('[data-local-input]');
    var compareBar = directory.querySelector('[data-compare-bar]');
    var compareOpen = directory.querySelector('[data-compare-open]');
    var compareClear = directory.querySelector('[data-compare-clear]');
    var compareStatus = directory.querySelector('[data-compare-status]');
    var compareHint = directory.querySelector('[data-compare-hint]');
    var compareKey = 'pawdar-breed-compare';
    var maxCompare = 3;
    var focusedIndex = 0;
    var listItems = [];

    function refreshListItems() {
        listItems = Array.prototype.slice.call(directory.querySelectorAll('[data-breed-list-item]'));
        focusedIndex = Math.min(focusedIndex, Math.max(0, listItems.length - 1));
    }

    function getCompareIds() {
        try {
            return JSON.parse(localStorage.getItem(compareKey) || '[]');
        } catch (e) {
            return [];
        }
    }

    function setCompareIds(ids) {
        localStorage.setItem(compareKey, JSON.stringify(ids.slice(0, maxCompare)));
        updateCompareBar();
        syncCompareCheckboxes();
    }

    function showCompareHint(message) {
        if (!compareHint) return;
        compareHint.hidden = false;
        compareHint.textContent = message;
        clearTimeout(showCompareHint._timer);
        showCompareHint._timer = setTimeout(function () {
            compareHint.hidden = true;
        }, 3500);
    }

    function updateCompareBar() {
        var ids = getCompareIds();
        if (compareBar) {
            compareBar.hidden = ids.length === 0;
            compareBar.classList.toggle('is-visible', ids.length > 0);
            directory.classList.toggle('has-compare-bar', ids.length > 0);
        }
        if (compareStatus) {
            if (ids.length === 0) {
                compareStatus.textContent = 'Select breeds to compare (up to 3)';
            } else if (ids.length === 1) {
                compareStatus.textContent = '1 breed selected — pick one more to compare';
            } else {
                compareStatus.textContent = ids.length + ' of ' + maxCompare + ' breeds selected';
            }
        }
        if (compareOpen) {
            compareOpen.hidden = ids.length < 2;
            compareOpen.setAttribute('aria-disabled', ids.length < 2 ? 'true' : 'false');
            compareOpen.href = 'breeds-compare.php?ids=' + ids.join(',');
        }
        if (compareClear) {
            compareClear.hidden = ids.length === 0;
        }
    }

    function syncCompareCheckboxes() {
        var ids = getCompareIds();
        directory.querySelectorAll('[data-compare-breed]').forEach(function (input) {
            input.checked = ids.indexOf(parseInt(input.getAttribute('data-compare-breed'), 10)) !== -1;
        });
    }

    function submitFilters() {
        if (!form) return;
        form.submit();
    }

    directory.querySelectorAll('[data-filter-local]').forEach(function (chip) {
        chip.addEventListener('click', function () {
            if (localInput) {
                localInput.value = chip.getAttribute('data-filter-local') || '';
            }
            var pageInput = form.querySelector('input[name="page"]');
            if (pageInput) pageInput.remove();
            submitFilters();
        });
    });

    directory.querySelectorAll('[data-filter-size]').forEach(function (chip) {
        chip.addEventListener('click', function () {
            var slug = chip.getAttribute('data-filter-size') || 'all';
            if (localInput) localInput.value = '';
            if (sizeInput) sizeInput.value = slug;
            var pageInput = form.querySelector('input[name="page"]');
            if (pageInput) pageInput.remove();
            submitFilters();
        });
    });

    directory.querySelectorAll('[data-filter-mood]').forEach(function (chip) {
        chip.addEventListener('click', function () {
            if (moodInput) moodInput.value = chip.getAttribute('data-filter-mood') || '';
            submitFilters();
        });
    });

    var sortSelect = directory.querySelector('#breed-sort');
    if (sortSelect) {
        sortSelect.addEventListener('change', submitFilters);
    }

    var pageJump = directory.querySelector('[data-page-jump]');
    if (pageJump) {
        pageJump.addEventListener('change', function () {
            if (pageJump.value) {
                window.location.href = pageJump.value;
            }
        });
    }

    var search = directory.querySelector('#breed-search');
    var searchTimer;
    if (search) {
        search.addEventListener('input', function () {
            clearTimeout(searchTimer);
            searchTimer = setTimeout(submitFilters, 400);
        });
    }

    directory.addEventListener('change', function (event) {
        var compareInput = event.target.closest('[data-compare-breed]');
        if (!compareInput) return;

        var id = parseInt(compareInput.getAttribute('data-compare-breed'), 10);
        var ids = getCompareIds();

        if (compareInput.checked) {
            if (ids.indexOf(id) === -1) {
                if (ids.length >= maxCompare) {
                    compareInput.checked = false;
                    showCompareHint('Maximum ' + maxCompare + ' breeds. Uncheck one to add another.');
                    return;
                }
                ids.push(id);
            }
        } else {
            ids = ids.filter(function (x) { return x !== id; });
        }

        setCompareIds(ids);
    });

    if (compareClear) {
        compareClear.addEventListener('click', function () {
            setCompareIds([]);
        });
    }

    directory.addEventListener('keydown', function (event) {
        if (!listItems.length) return;
        if (event.target.closest('[data-compare-breed]')) return;

        if (event.key === 'ArrowDown') {
            event.preventDefault();
            focusedIndex = Math.min(listItems.length - 1, focusedIndex + 1);
            listItems[focusedIndex].focus();
        } else if (event.key === 'ArrowUp') {
            event.preventDefault();
            focusedIndex = Math.max(0, focusedIndex - 1);
            listItems[focusedIndex].focus();
        } else if (event.key === 'Enter' && document.activeElement && document.activeElement.hasAttribute('data-breed-list-item')) {
            var link = document.activeElement.querySelector('.breed-list-link');
            if (link) link.click();
        }
    });

    refreshListItems();
    updateCompareBar();
    syncCompareCheckboxes();
});
