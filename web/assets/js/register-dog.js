document.addEventListener('DOMContentLoaded', function () {
    var form = document.getElementById('register-dog-form');
    if (!form) return;

    var step = parseInt(form.getAttribute('data-step') || '1', 10);
    var steps = form.querySelectorAll('[data-form-step]');
    var backBtn = form.querySelector('[data-step-back]');
    var nextBtn = form.querySelector('[data-step-next]');
    var submitBtn = form.querySelector('[data-step-submit]');

    function showStep(n) {
        step = n;
        steps.forEach(function (el) {
            el.hidden = parseInt(el.getAttribute('data-form-step'), 10) !== step;
        });
        if (backBtn) backBtn.hidden = step <= 1;
        if (nextBtn) nextBtn.hidden = step >= 3;
        if (submitBtn) submitBtn.hidden = step !== 3;
        if (step === 3) renderReview();
    }

    function renderReview() {
        var box = document.getElementById('register-review');
        if (!box) return;
        var fd = new FormData(form);
        box.innerHTML =
            '<p><strong>Name:</strong> ' + esc(fd.get('dog_name')) + '</p>' +
            '<p><strong>Breed:</strong> ' + esc(fd.get('breed_search')) + '</p>' +
            '<p><strong>Sex:</strong> ' + esc(fd.get('gender')) + '</p>' +
            '<p><strong>Type:</strong> ' + esc(fd.get('dog_type')) + '</p>' +
            '<p><strong>Vaccine:</strong> ' + esc(fd.get('vaccine_name') || 'None') + '</p>';
    }

    function esc(v) {
        var d = document.createElement('div');
        d.textContent = v || '';
        return d.innerHTML;
    }

    if (backBtn) backBtn.addEventListener('click', function () { showStep(step - 1); });
    if (nextBtn) nextBtn.addEventListener('click', function () { showStep(step + 1); });
    showStep(step);
});
