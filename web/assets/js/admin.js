document.addEventListener('DOMContentLoaded', function () {
    var csrf = document.querySelector('meta[name="csrf-token"]');
    var token = csrf ? csrf.getAttribute('content') : '';

    document.querySelectorAll('[data-admin-approve-user]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var userId = btn.getAttribute('data-user-id');
            if (!userId || !window.PawdarUI || !window.PawdarUI.confirm) {
                submitAdminAction('approve_user', { user_id: userId }, btn);
                return;
            }

            window.PawdarUI.confirm('Approve this account?', function () {
                submitAdminAction('approve_user', { user_id: userId }, btn);
            });
        });
    });

    document.querySelectorAll('[data-admin-approve-dog]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var dogId = btn.getAttribute('data-dog-id');
            if (!window.PawdarUI || !window.PawdarUI.confirm) {
                submitAdminAction('approve_dog', { dog_id: dogId }, btn);
                return;
            }

            window.PawdarUI.confirm('Verify and register this dog?', function () {
                submitAdminAction('approve_dog', { dog_id: dogId }, btn);
            });
        });
    });

    function submitAdminAction(action, fields, btn) {
        var body = new FormData();
        body.append('action', action);
        body.append('csrf_token', token);
        Object.keys(fields).forEach(function (key) {
            body.append(key, fields[key]);
        });

        btn.disabled = true;

        fetch('ajax/admin_action.php', {
            method: 'POST',
            headers: { 'X-CSRF-Token': token },
            body: body
        })
            .then(function (res) { return res.json(); })
            .then(function (data) {
                if (data.success) {
                    if (window.PawdarUI && window.PawdarUI.toast) {
                        window.PawdarUI.toast(data.message || 'Saved', 'success');
                    }
                    var row = btn.closest('.admin-row');
                    if (row) row.remove();
                    return;
                }

                btn.disabled = false;
                if (window.PawdarUI && window.PawdarUI.toast) {
                    window.PawdarUI.toast(data.message || 'Action failed', 'error');
                }
            })
            .catch(function () {
                btn.disabled = false;
            });
    }
});
