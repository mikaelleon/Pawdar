<?php

/**
 * Maps breed display names to dog.ceo API path segments.
 *
 * @return array<string, string>
 */
function dog_ceo_breed_slug_map(): array
{
    return [
        'Golden Retriever' => 'retriever/golden',
        'Labrador Retriever' => 'labrador',
        'German Shepherd' => 'germanshepherd',
        'French Bulldog' => 'bulldog/french',
        'English Bulldog' => 'bulldog/english',
        'American Bulldog' => 'bulldog/american',
        'Siberian Husky' => 'husky',
        'Alaskan Husky' => 'husky',
        'Shih Tzu' => 'shihtzu',
        'Chow Chow' => 'chow',
        'Border Collie' => 'collie/border',
        'Bearded Collie' => 'collie/bearded',
        'Cocker Spaniel' => 'spaniel/cocker',
        'English Cocker Spaniel' => 'spaniel/cocker',
        'American Cocker Spaniel' => 'spaniel/american',
        'Brittany Spaniel' => 'spaniel/brittany',
        'Welsh Springer Spaniel' => 'spaniel/welsh',
        'Irish Water Spaniel' => 'spaniel/irish',
        'English Springer Spaniel' => 'spaniel/springer',
        'Australian Shepherd' => 'australian/shepherd',
        'Australian Cattle Dog' => 'australian/cattledog',
        'Australian Terrier' => 'australian/terrier',
        'Boston Terrier' => 'terrier/boston',
        'Scottish Terrier' => 'terrier/scottish',
        'Yorkshire Terrier' => 'terrier/yorkshire',
        'West Highland White Terrier' => 'terrier/westhighland',
        'Airedale Terrier' => 'terrier/airedale',
        'Bull Terrier' => 'terrier/bull',
        'Staffordshire Bull Terrier' => 'terrier/staffordshire',
        'American Staffordshire Terrier' => 'terrier/american',
        'Irish Terrier' => 'terrier/irish',
        'Norwich Terrier' => 'terrier/norwich',
        'Norfolk Terrier' => 'terrier/norfolk',
        'Border Terrier' => 'terrier/border',
        'Cairn Terrier' => 'terrier/cairn',
        'Dandie Dinmont Terrier' => 'terrier/dandie',
        'Fox Terrier' => 'terrier/fox',
        'Kerry Blue Terrier' => 'terrier/kerryblue',
        'Lakeland Terrier' => 'terrier/lakeland',
        'Silky Terrier' => 'terrier/silky',
        'Skye Terrier' => 'terrier/skye',
        'Tibetan Terrier' => 'terrier/tibetan',
        'Welsh Terrier' => 'terrier/welsh',
        'Aspin' => 'mix',
        'Mixed Breed' => 'mix',
    ];
}

/**
 * Resolves a dog.ceo API breed path from a breed name.
 */
function breed_to_dog_ceo_slug(string $breedName): string
{
    $trimmed = trim($breedName);
    $map = dog_ceo_breed_slug_map();

    if (isset($map[$trimmed])) {
        return $map[$trimmed];
    }

    $lower = strtolower($trimmed);
    if (isset($map[$lower])) {
        return $map[$lower];
    }

    $slug = preg_replace('/[^a-z0-9]/', '', $lower);

    return $slug !== '' ? $slug : 'mix';
}

/**
 * Absolute filesystem path for a breed image stored under web/uploads/breeds/.
 */
function breed_local_image_path(string $relativePath): ?string
{
    $relativePath = ltrim(str_replace('\\', '/', $relativePath), '/');
    if ($relativePath === '' || str_contains($relativePath, '..')) {
        return null;
    }

    $fullPath = dirname(__DIR__) . '/' . $relativePath;

    return is_file($fullPath) ? $fullPath : null;
}

/**
 * Returns a public relative path when a local breed image exists for the slug.
 */
function breed_local_image_public_path(string $slug): ?string
{
    $slug = trim($slug);
    if ($slug === '') {
        return null;
    }

    foreach (['jpg', 'jpeg', 'png', 'webp', 'gif'] as $ext) {
        $relative = 'uploads/breeds/' . $slug . '.' . $ext;
        if (breed_local_image_path($relative) !== null) {
            return $relative;
        }
    }

    return null;
}

/**
 * Validates remote dog.ceo URLs and local uploads/breeds paths.
 */
function breed_image_url_is_valid(string $url): bool
{
    if ($url === '') {
        return false;
    }

    if (str_starts_with($url, 'uploads/')) {
        return breed_local_image_path($url) !== null;
    }

    if (filter_var($url, FILTER_VALIDATE_URL) === false) {
        return false;
    }

    $context = stream_context_create([
        'http' => [
            'method' => 'HEAD',
            'timeout' => 3,
            'user_agent' => 'Pawdar/1.0',
            'ignore_errors' => true,
        ],
        'ssl' => [
            'verify_peer' => true,
            'verify_peer_name' => true,
        ],
    ]);

    $headers = @get_headers($url, true, $context);
    if ($headers === false || !isset($headers[0])) {
        return false;
    }

    return str_contains((string) $headers[0], '200');
}

/**
 * Fetches and caches a breed image URL from dog.ceo.
 */
function resolve_breed_image_url(PDO $pdo, int $breedId, string $breedName): ?string
{
    $stmt = $pdo->prepare('SELECT image_url, slug FROM breeds WHERE breed_id = :id LIMIT 1');
    $stmt->execute([':id' => $breedId]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $existing = is_array($row) ? ($row['image_url'] ?? null) : null;
    $slug = is_array($row) && !empty($row['slug'])
        ? (string) $row['slug']
        : breed_slug_from_name($breedName);

    if (is_string($existing) && $existing !== '') {
        if (breed_image_url_is_valid($existing)) {
            return $existing;
        }

        $clear = $pdo->prepare('UPDATE breeds SET image_url = NULL WHERE breed_id = :id');
        $clear->execute([':id' => $breedId]);
    }

    $localPath = breed_local_image_public_path($slug);
    if ($localPath !== null) {
        $update = $pdo->prepare('UPDATE breeds SET image_url = :url WHERE breed_id = :id');
        $update->execute([':url' => $localPath, ':id' => $breedId]);

        return $localPath;
    }

    $slug = breed_to_dog_ceo_slug($breedName);
    $apiUrl = 'https://dog.ceo/api/breed/' . rawurlencode($slug) . '/images/random';

    $context = stream_context_create([
        'http' => [
            'timeout' => 4,
            'user_agent' => 'Pawdar/1.0',
        ],
    ]);

    $response = @file_get_contents($apiUrl, false, $context);
    if ($response === false) {
        return null;
    }

    $data = json_decode($response, true);
    $imageUrl = is_array($data) ? ($data['message'] ?? null) : null;

    if (!is_string($imageUrl) || $imageUrl === '' || !breed_image_url_is_valid($imageUrl)) {
        return null;
    }

    $update = $pdo->prepare('UPDATE breeds SET image_url = :url WHERE breed_id = :id');
    $update->execute([':url' => $imageUrl, ':id' => $breedId]);

    return $imageUrl;
}

/**
 * Returns the dog's own uploaded photo URL, or null when none is set.
 *
 * @param array<string, mixed> $dog
 * @param array<string, mixed>|null $breed Unused; kept for call-site compatibility.
 */
function dog_profile_image_url(array $dog, ?array $breed = null): ?string
{
    if (!empty($dog['photo_path'])) {
        return (string) $dog['photo_path'];
    }

    return null;
}

/**
 * Returns a local breed photo for directory UI, or null when none is stored.
 *
 * @param array<string, mixed> $breed
 */
function breed_directory_photo_url(array $breed): ?string
{
    if (!empty($breed['image_url'])) {
        $url = (string) $breed['image_url'];
        if (breed_image_url_is_valid($url) && str_starts_with($url, 'uploads/')) {
            return $url;
        }
    }

    $slug = (string) ($breed['slug'] ?? breed_slug_from_name((string) ($breed['breed_name'] ?? '')));
    return breed_local_image_public_path($slug);
}

/**
 * Returns URL for breed directory cards.
 *
 * @param array<string, mixed> $breed
 */
function breed_card_image_url(array $breed): ?string
{
    $local = breed_directory_photo_url($breed);
    if ($local !== null) {
        return $local;
    }

    if (!empty($breed['breed_id'])) {
        return 'ajax/breed-image.php?id=' . (int) $breed['breed_id'];
    }

    return null;
}

/**
 * Silhouette fallback tinted by dominant trait.
 *
 * @deprecated Directory UI uses Lucide dog icon instead.
 * @param array<string, mixed> $breed
 */
function breed_silhouette_url(array $breed): string
{
    return 'ajax/breed-silhouette.php?id=' . (int) ($breed['breed_id'] ?? 0);
}

/**
 * List row thumbnail — local photo only; null when the breed has no stored image.
 *
 * @param array<string, mixed> $breed
 */
function breed_list_thumbnail_url(array $breed): ?string
{
    return breed_directory_photo_url($breed);
}

/**
 * Detail/compare thumbnail — local photo only.
 *
 * @param array<string, mixed> $breed
 */
function breed_thumbnail_url(array $breed): ?string
{
    return breed_directory_photo_url($breed);
}

/**
 * Dominant trait color for silhouette fallback.
 *
 * @param array<string, mixed> $breed
 */
function breed_trait_accent_color(array $breed): string
{
    $loyalty = (int) ($breed['loyalty_score'] ?? 3);
    $energy = (int) ($breed['energy_score'] ?? 3);
    $friendliness = (int) ($breed['friendliness_score'] ?? 3);
    $dominant = max($loyalty, $energy, $friendliness);

    if ($dominant === $energy && $energy >= 4) {
        return '#F8BC72';
    }
    if ($dominant === $loyalty && $loyalty >= 4) {
        return '#87AFAE';
    }
    if ($dominant === $friendliness && $friendliness >= 4) {
        return '#C0DAB5';
    }

    return '#6C8B9F';
}

/**
 * Common coat color options for register/edit forms.
 *
 * @return list<string>
 */
function dog_coat_color_options(): array
{
    return [
        'Black',
        'White',
        'Brown',
        'Tan',
        'Golden',
        'Cream',
        'Gray',
        'Brindle',
        'Spotted',
        'Mixed',
        'Other',
    ];
}
