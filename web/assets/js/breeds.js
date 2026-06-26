document.addEventListener('DOMContentLoaded', function () {
    var search = document.getElementById('breed-search');
    var timer;

    if (search) {
        search.addEventListener('input', function () {
            clearTimeout(timer);
            timer = setTimeout(function () {
                var q = search.value.trim();
                var size = document.querySelector('[data-breeds-page]')?.getAttribute('data-size-filter') || 'all';
                fetch('ajax/search_breeds.php?q=' + encodeURIComponent(q) + '&size=' + encodeURIComponent(size))
                    .then(function (res) { return res.json(); })
                    .then(function (data) {
                        if (!data.success) return;
                        var grid = document.querySelector('[data-breed-grid]');
                        if (!grid) return;
                        if (!data.breeds.length) {
                            grid.innerHTML = '<div class="feed-empty-state"><p class="feed-empty-title">No breeds found</p><button type="button" class="btn-outline btn-sm" data-clear-breed-search>Clear search</button></div>';
                            bindClearSearch();
                            return;
                        }
                        grid.innerHTML = data.breeds.map(function (breed) {
                            return '<button type="button" class="breed-card card-hoverable" data-breed-card data-breed-id="' + breed.breed_id + '" data-size="' + breed.size_category + '">' +
                                '<div class="breed-card-image pastel-color-0"><i data-lucide="dog"></i></div>' +
                                '<div class="card-body"><div style="font-weight:500;">' + escapeHtml(breed.breed_name) + '</div>' +
                                '<span class="badge badge-owned">' + escapeHtml(breed.size_category) + '</span></div></button>';
                        }).join('');
                        bindBreedCards();
                        if (window.lucide) lucide.createIcons();
                    });
            }, 300);
        });
    }

    document.querySelectorAll('.breed-size-chip').forEach(function (chip) {
        chip.addEventListener('click', function () {
            var size = chip.getAttribute('data-size') || 'all';
            document.querySelectorAll('[data-breed-card]').forEach(function (card) {
                var cardSize = card.getAttribute('data-size');
                card.hidden = size !== 'all' && cardSize !== size;
            });
            document.querySelectorAll('.breed-size-chip').forEach(function (c) {
                c.classList.toggle('chip-active', c === chip);
                c.classList.toggle('chip-outline', c !== chip);
            });
            var page = document.querySelector('[data-breeds-page]');
            if (page) page.setAttribute('data-size-filter', size);
        });
    });

    bindBreedCards();
    bindClearSearch();
});

function bindBreedCards() {
    document.querySelectorAll('[data-breed-card]').forEach(function (card) {
        card.addEventListener('click', function () {
            var breedId = card.getAttribute('data-breed-id');
            if (!breedId) return;

            document.querySelectorAll('[data-breed-card]').forEach(function (c) {
                c.classList.remove('is-selected');
            });
            card.classList.add('is-selected');

            var page = document.querySelector('[data-breeds-page]');
            if (page) page.setAttribute('data-selected-breed', breedId);

            fetch('ajax/breed_detail.php?breed_id=' + encodeURIComponent(breedId))
                .then(function (res) { return res.json(); })
                .then(function (data) {
                    if (data.success && data.breed) {
                        renderBreedDetail(data.breed);
                    }
                });

            fetch('ajax/breed_dogs.php?breed_id=' + encodeURIComponent(breedId))
                .then(function (res) { return res.json(); })
                .then(function (data) {
                    if (!data.success) return;
                    renderBreedDogs(data.dogs);
                });

            var url = new URL(window.location.href);
            url.searchParams.set('breed', breedId);
            window.history.replaceState({}, '', url.toString());
        });
    });
}

function renderBreedDetail(breed) {
    var nameEl = document.querySelector('[data-breed-name]');
    var metaEl = document.querySelector('[data-breed-meta]');
    var temperamentEl = document.querySelector('[data-breed-temperament]');
    var healthEl = document.querySelector('[data-breed-health]');

    if (nameEl) nameEl.textContent = breed.breed_name || '';
    if (metaEl) {
        metaEl.textContent = [breed.size_category, breed.weight_range, breed.lifespan]
            .filter(Boolean)
            .join(' · ');
    }
    if (temperamentEl) temperamentEl.textContent = breed.temperament_notes || '';
    if (healthEl) healthEl.textContent = breed.common_health_risks || '';

    document.querySelectorAll('[data-breed-detail] .rating-dots').forEach(function (dotsEl, index) {
        var scores = [
            parseInt(breed.loyalty_score, 10) || 3,
            parseInt(breed.energy_score, 10) || 3,
            parseInt(breed.friendliness_score, 10) || 3
        ];
        var filled = scores[index] || 3;
        dotsEl.querySelectorAll('.rating-dot').forEach(function (dot, i) {
            dot.classList.toggle('empty', i >= filled);
        });
    });
}

function renderBreedDogs(dogs) {
    var container = document.querySelector('[data-breed-dogs]');
    if (!container) return;

    if (!dogs.length) {
        container.innerHTML = '<div class="text-sm text-muted text-center" style="padding:12px;"><i data-lucide="paw-print"></i><div>No dogs of this breed registered yet</div></div>';
        if (window.lucide) lucide.createIcons();
        return;
    }

    container.innerHTML = dogs.map(function (dog) {
        var status = dog.Status || 'Registered';
        var badge = status === 'Registered' ? 'badge-verified' : 'badge-investigating';
        return '<a href="dog-profile.php?id=' + dog.dog_id + '" class="dog-breed-row">' +
            '<div class="icon-box icon-box-sm" style="background:var(--muted-teal);color:#fff;width:32px;height:32px;"><i data-lucide="dog"></i></div>' +
            '<div class="flex-1"><div class="text-sm" style="font-weight:500;">' + escapeHtml(dog.DogName) + '</div>' +
            '<div class="text-xs text-muted">' + escapeHtml(dog.owner_name) + '</div></div>' +
            '<span class="badge ' + badge + '">' + escapeHtml(status) + '</span>' +
            '<i data-lucide="chevron-right"></i></a>';
    }).join('');

    if (window.lucide) lucide.createIcons();
}

function bindClearSearch() {
    document.querySelectorAll('[data-clear-breed-search]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var search = document.getElementById('breed-search');
            if (search) {
                search.value = '';
                search.dispatchEvent(new Event('input'));
            }
        });
    });
}

function escapeHtml(text) {
    var div = document.createElement('div');
    div.textContent = text || '';
    return div.innerHTML;
}
