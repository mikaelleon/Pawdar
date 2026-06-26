document.addEventListener('DOMContentLoaded', function () {
    var form = document.querySelector('[data-registry-search]');
    var input = form ? form.querySelector('input[name="q"]') : null;
    var timer;

    if (input) {
        input.addEventListener('input', function () {
            clearTimeout(timer);
            timer = setTimeout(function () {
                if (form) form.submit();
            }, 400);
        });
    }
});
