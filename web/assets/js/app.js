document.addEventListener('DOMContentLoaded', function () {
    if (window.lucide) {
        lucide.createIcons();
    }

    var menuBtn = document.querySelector('[data-mobile-menu]');
    var mobileNav = document.querySelector('[data-mobile-nav]');

    if (menuBtn && mobileNav) {
        menuBtn.addEventListener('click', function () {
            var isOpen = mobileNav.hasAttribute('hidden');
            if (isOpen) {
                mobileNav.removeAttribute('hidden');
                menuBtn.setAttribute('aria-expanded', 'true');
            } else {
                mobileNav.setAttribute('hidden', '');
                menuBtn.setAttribute('aria-expanded', 'false');
            }
        });

        mobileNav.querySelectorAll('a').forEach(function (link) {
            link.addEventListener('click', function () {
                mobileNav.setAttribute('hidden', '');
                menuBtn.setAttribute('aria-expanded', 'false');
            });
        });
    }

    document.querySelectorAll('[data-role-chip]').forEach(function (chip) {
        chip.addEventListener('click', function () {
            if (chip.classList.contains('is-disabled')) {
                return;
            }

            document.querySelectorAll('[data-role-chip]').forEach(function (c) {
                c.classList.remove('is-selected');
            });
            chip.classList.add('is-selected');

            var roleInput = document.getElementById('role-input');
            var roleValue = chip.getAttribute('data-role-value');
            if (roleInput && roleValue) {
                roleInput.value = roleValue;
            }
        });
    });

    initNotificationBell();
    initAvatarMenu();
});

function getCsrfToken() {
    var meta = document.querySelector('meta[name="csrf-token"]');
    return meta ? meta.getAttribute('content') : '';
}

function initNotificationBell() {
    var bells = document.querySelectorAll('[data-notification-bell]');
    if (!bells.length) {
        return;
    }

    bells.forEach(function (bell) {
        bell.addEventListener('click', function (event) {
            event.preventDefault();
            event.stopPropagation();

            var wrap = bell.closest('.notification-wrap');
            var dropdown = wrap ? wrap.querySelector('[data-notification-dropdown]') : null;
            if (!dropdown) {
                return;
            }

            var isOpen = !dropdown.hasAttribute('hidden');
            if (isOpen) {
                closeNotificationDropdown(wrap);
                return;
            }

            closeAllNotificationDropdowns();
            dropdown.removeAttribute('hidden');
            bell.setAttribute('aria-expanded', 'true');
            loadNotifications(dropdown, true);
        });
    });

    document.querySelectorAll('[data-notification-dropdown]').forEach(function (dropdown) {
        dropdown.addEventListener('click', function (event) {
            event.stopPropagation();
        });
    });

    document.addEventListener('click', function () {
        closeAllNotificationDropdowns();
    });

    pollNotifications();
    setInterval(pollNotifications, 30000);
}

function closeNotificationDropdown(wrap) {
    if (!wrap) {
        return;
    }

    var dropdown = wrap.querySelector('[data-notification-dropdown]');
    var bell = wrap.querySelector('[data-notification-bell]');
    if (dropdown) {
        dropdown.setAttribute('hidden', '');
    }
    if (bell) {
        bell.setAttribute('aria-expanded', 'false');
    }
}

function closeAllNotificationDropdowns() {
    document.querySelectorAll('[data-notification-dropdown]').forEach(function (dropdown) {
        dropdown.setAttribute('hidden', '');
    });
    document.querySelectorAll('[data-notification-bell]').forEach(function (bell) {
        bell.setAttribute('aria-expanded', 'false');
    });
}

function loadNotifications(dropdown, markRead) {
    var list = dropdown.querySelector('[data-notification-list]');
    if (!list) {
        return;
    }

    fetch('ajax/notifications.php')
        .then(function (res) { return res.json(); })
        .then(function (data) {
            if (!data.success) {
                return;
            }

            updateNotificationBadges(data.count);

            if (!data.items.length) {
                list.innerHTML = '<p class="notification-empty text-sm text-muted">No notifications yet.</p>';
            } else {
                list.innerHTML = data.items.map(function (item) {
                    return '<div class="notification-item' + (item.is_read ? '' : ' is-unread') + '">' +
                        '<span class="notification-item-message">' + escapeHtml(item.message) + '</span>' +
                        '<span class="notification-item-time text-xs text-muted">' + escapeHtml(item.time) + '</span>' +
                        '</div>';
                }).join('');
            }

            if (markRead && data.count > 0) {
                fetch('ajax/notifications.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-Token': getCsrfToken()
                    },
                    body: JSON.stringify({ csrf_token: getCsrfToken() })
                }).then(function (res) { return res.json(); })
                    .then(function (result) {
                        if (result.success) {
                            updateNotificationBadges(result.count);
                        }
                    });
            }
        });
}

function pollNotifications() {
    fetch('ajax/notifications.php')
        .then(function (res) { return res.json(); })
        .then(function (data) {
            if (!data.success) {
                return;
            }

            var prev = parseInt(document.querySelector('[data-notification-count]')?.textContent || '0', 10);
            updateNotificationBadges(data.count);

            if (data.count > prev) {
                document.querySelectorAll('[data-notification-bell]').forEach(function (bell) {
                    bell.classList.add('is-ringing');
                    setTimeout(function () { bell.classList.remove('is-ringing'); }, 400);
                });
            }
        });
}

function updateNotificationBadges(count) {
    document.querySelectorAll('[data-notification-count]').forEach(function (badge) {
        var display = count > 99 ? '99+' : String(count);
        badge.textContent = display;
        badge.classList.toggle('is-hidden', count <= 0);
        if (count > 0) {
            badge.setAttribute('aria-label', count + ' unread');
            badge.removeAttribute('aria-hidden');
        } else {
            badge.removeAttribute('aria-label');
            badge.setAttribute('aria-hidden', 'true');
        }
    });
}

function initAvatarMenu() {
    var triggers = document.querySelectorAll('[data-avatar-menu]');
    if (!triggers.length) {
        return;
    }

    triggers.forEach(function (trigger) {
        trigger.addEventListener('click', function (event) {
            event.preventDefault();
            event.stopPropagation();

            var wrap = trigger.closest('.avatar-menu-wrap');
            var dropdown = wrap ? wrap.querySelector('[data-avatar-dropdown]') : null;
            if (!dropdown) {
                return;
            }

            var isOpen = !dropdown.hasAttribute('hidden');
            closeAllAvatarMenus();
            if (isOpen) {
                return;
            }

            dropdown.removeAttribute('hidden');
            trigger.setAttribute('aria-expanded', 'true');
            if (window.lucide) {
                lucide.createIcons();
            }
        });
    });

    document.querySelectorAll('[data-avatar-dropdown]').forEach(function (dropdown) {
        dropdown.addEventListener('click', function (event) {
            event.stopPropagation();
        });
    });

    document.addEventListener('click', function () {
        closeAllAvatarMenus();
    });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            closeAllAvatarMenus();
        }
    });
}

function closeAllAvatarMenus() {
    document.querySelectorAll('[data-avatar-dropdown]').forEach(function (dropdown) {
        dropdown.setAttribute('hidden', '');
    });
    document.querySelectorAll('[data-avatar-menu]').forEach(function (trigger) {
        trigger.setAttribute('aria-expanded', 'false');
    });
}

function escapeHtml(text) {
    var div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}
