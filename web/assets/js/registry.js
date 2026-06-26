document.addEventListener('DOMContentLoaded', function () {
    var grid = document.querySelector('[data-registry-grid]');
    var search = document.getElementById('registry-search');
    var loadBtn = document.querySelector('[data-registry-load-more]');
    var loadWrap = document.querySelector('[data-registry-load-wrap]');
    var countEl = document.querySelector('[data-registry-count]');
    var offset = grid ? grid.querySelectorAll('.registry-dog-card').length : 0;
    var timer;

    function filters() {
        var activeType = document.querySelector('.registry-type-chip.chip-active');
        return {
            q: search ? search.value.trim() : '',
            type: activeType ? activeType.getAttribute('data-type') : 'all',
            barangay: val('barangay'),
            breed: val('breed'),
            vaccine: val('vaccine')
        };
    }

    function val(name) {
        var el = document.querySelector('.registry-filter[data-filter="' + name + '"]');
        return el ? el.value : 'all';
    }

    function fetchDogs(reset) {
        if (!grid) return;
        if (reset) offset = 0;

        var params = new URLSearchParams(filters());
        params.set('offset', String(offset));

        fetch('ajax/registry_dogs.php?' + params.toString())
            .then(function (res) { return res.json(); })
            .then(function (data) {
                if (!data.success) return;
                if (reset) {
                    grid.innerHTML = data.html || '<div class="feed-empty-state" style="grid-column:1/-1;"><p class="feed-empty-title">No dogs found</p></div>';
                } else if (data.html) {
                    grid.insertAdjacentHTML('beforeend', data.html);
                }
                offset = grid.querySelectorAll('.registry-dog-card').length;
                if (loadWrap) loadWrap.hidden = !data.has_more;
                if (countEl) countEl.textContent = offset + ' of ' + data.total + ' dogs';
                if (window.lucide) lucide.createIcons();
            });
    }

    if (search) {
        search.addEventListener('input', function () {
            clearTimeout(timer);
            timer = setTimeout(function () { fetchDogs(true); }, 300);
        });
    }

    document.querySelectorAll('.registry-type-chip').forEach(function (chip) {
        chip.addEventListener('click', function () {
            document.querySelectorAll('.registry-type-chip').forEach(function (c) {
                c.classList.toggle('chip-active', c === chip);
                c.classList.toggle('chip-outline', c !== chip);
            });
            fetchDogs(true);
        });
    });

    document.querySelectorAll('.registry-filter').forEach(function (sel) {
        sel.addEventListener('change', function () { fetchDogs(true); });
    });

    if (loadBtn) {
        loadBtn.addEventListener('click', function () { fetchDogs(false); });
    }
});
