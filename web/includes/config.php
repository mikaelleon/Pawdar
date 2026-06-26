<?php
/**
 * Pawdar site configuration.
 * Upload the entire web/ folder contents to your InfinityFree htdocs directory.
 */

define('SITE_NAME', 'Pawdar');
define('SITE_TAGLINE', 'Know your dogs. Protect your community.');
define('SITE_DESCRIPTION', 'Community dog registry and incident reporting for barangays, owners, vets, and rescue groups.');

/**
 * Returns the current page filename for active navigation highlighting.
 */
function current_page(): string
{
    return basename($_SERVER['PHP_SELF'] ?? 'index.php');
}

/**
 * Returns true when the given page matches the current request.
 */
function is_active(string $page): bool
{
    return current_page() === $page;
}

/**
 * Builds a CSS class string with optional active state.
 */
function nav_class(string $page, string $base = 'nav-link'): string
{
    return $base . (is_active($page) ? ' is-active' : '');
}
