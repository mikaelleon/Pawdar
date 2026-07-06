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
 * Fetches and caches a breed image URL from dog.ceo.
 */
function resolve_breed_image_url(PDO $pdo, int $breedId, string $breedName): ?string
{
    $stmt = $pdo->prepare('SELECT image_url FROM breeds WHERE breed_id = :id LIMIT 1');
    $stmt->execute([':id' => $breedId]);
    $existing = $stmt->fetchColumn();

    if (is_string($existing) && $existing !== '') {
        return $existing;
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

    if (!is_string($imageUrl) || $imageUrl === '') {
        return null;
    }

    $update = $pdo->prepare('UPDATE breeds SET image_url = :url WHERE breed_id = :id');
    $update->execute([':url' => $imageUrl, ':id' => $breedId]);

    return $imageUrl;
}

/**
 * Returns URL for a dog profile photo or breed fallback image.
 *
 * @param array<string, mixed> $dog
 * @param array<string, mixed>|null $breed
 */
function dog_profile_image_url(array $dog, ?array $breed = null): ?string
{
    if (!empty($dog['photo_path'])) {
        return (string) $dog['photo_path'];
    }

    if ($breed === null) {
        return null;
    }

    if (!empty($breed['image_url'])) {
        return (string) $breed['image_url'];
    }

    if (!empty($breed['breed_id'])) {
        return 'ajax/breed-image.php?id=' . (int) $breed['breed_id'];
    }

    return null;
}

/**
 * Returns URL for breed directory cards.
 *
 * @param array<string, mixed> $breed
 */
function breed_card_image_url(array $breed): ?string
{
    if (!empty($breed['image_url'])) {
        return (string) $breed['image_url'];
    }

    if (!empty($breed['breed_id'])) {
        return 'ajax/breed-image.php?id=' . (int) $breed['breed_id'];
    }

    return null;
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
