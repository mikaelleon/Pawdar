<?php

/**
 * Generates breed directory editorial content from DB fields and trait scores.
 */

/**
 * URL slug from breed name.
 */
function breed_slug_from_name(string $name): string
{
    $slug = strtolower(trim($name));
    $slug = preg_replace('/[^a-z0-9]+/', '-', $slug) ?? $slug;
    $slug = trim((string) $slug, '-');

    return $slug !== '' ? $slug : 'breed';
}

/**
 * Ensures slug, breed_group, and local flags exist for directory pages.
 */
function ensure_breed_directory_metadata(PDO $pdo): void
{
    $rows = $pdo->query('SELECT breed_id, breed_name, temperament_notes, slug, breed_group FROM breeds')->fetchAll();

    $updateSlug = $pdo->prepare('UPDATE breeds SET slug = :slug WHERE breed_id = :id');
    $updateGroup = $pdo->prepare('UPDATE breeds SET breed_group = :group WHERE breed_id = :id AND (breed_group IS NULL OR breed_group = \'\')');

    foreach ($rows as $row) {
        $id = (int) $row['breed_id'];
        if (empty($row['slug'])) {
            $base = breed_slug_from_name((string) $row['breed_name']);
            $slug = $base;
            $suffix = 2;
            while (true) {
                $check = $pdo->prepare('SELECT breed_id FROM breeds WHERE slug = :slug AND breed_id != :id LIMIT 1');
                $check->execute([':slug' => $slug, ':id' => $id]);
                if (!$check->fetch()) {
                    break;
                }
                $slug = $base . '-' . $suffix;
                $suffix++;
            }
            $updateSlug->execute([':slug' => $slug, ':id' => $id]);
        }

        if (empty($row['breed_group']) && str_contains((string) $row['temperament_notes'], '—')) {
            $group = trim(explode('—', (string) $row['temperament_notes'], 2)[0]);
            if ($group !== '') {
                $updateGroup->execute([':group' => $group, ':id' => $id]);
            }
        }
    }
}

/**
 * @return array{label: string, class: string}
 */
function breed_score_band(int $score): array
{
    if ($score >= 4) {
        return ['label' => 'High', 'class' => 'trait-high'];
    }
    if ($score <= 2) {
        return ['label' => 'Low', 'class' => 'trait-low'];
    }

    return ['label' => 'Moderate', 'class' => 'trait-moderate'];
}

/**
 * Practical qualifier under trait bars.
 */
function breed_trait_qualifier(string $trait, int $score): string
{
    return match ($trait) {
        'Loyalty' => match (true) {
            $score >= 4 => 'Forms strong bonds; may be protective of household.',
            $score <= 2 => 'More independent; may prefer space from constant handling.',
            default => 'Generally devoted with balanced attachment.',
        },
        'Energy' => match (true) {
            $score >= 4 => 'Needs 60+ minutes of daily exercise and mental stimulation.',
            $score <= 2 => 'Content with short walks and calm indoor time.',
            default => 'Moderate daily activity — regular walks and play sessions.',
        },
        'Friendliness' => match (true) {
            $score >= 4 => 'Usually welcoming to people and other pets with proper introductions.',
            $score <= 2 => 'May be reserved or cautious with strangers; early socialization helps.',
            default => 'Friendly with familiar people; gradual warm-up to newcomers.',
        },
        default => '',
    };
}

/**
 * @param array<string, mixed> $breed
 */
function breed_group_label(array $breed): string
{
    if (!empty($breed['breed_group'])) {
        return (string) $breed['breed_group'];
    }

    $notes = (string) ($breed['temperament_notes'] ?? '');
    if (str_contains($notes, '—')) {
        return trim(explode('—', $notes, 2)[0]);
    }

    return 'Companion Dogs';
}

/**
 * @param array<string, mixed> $breed
 */
function breed_known_for_text(array $breed): string
{
    if (!empty($breed['known_for'])) {
        return (string) $breed['known_for'];
    }

    $name = (string) $breed['breed_name'];
    $group = breed_group_label($breed);
    $loyalty = (int) ($breed['loyalty_score'] ?? 3);
    $energy = (int) ($breed['energy_score'] ?? 3);
    $friendliness = (int) ($breed['friendliness_score'] ?? 3);
    $size = (string) ($breed['size_category'] ?? 'Medium');
    $variant = abs(crc32($name)) % 6;

    $energyLine = match (true) {
        $energy >= 4 => 'Expect an active companion that thrives on daily runs, play, and mental challenges.',
        $energy <= 2 => 'Often content with shorter walks and relaxed indoor time.',
        default => 'Usually happy with a steady routine of walks and interactive play.',
    };

    $socialLine = match (true) {
        $friendliness >= 4 => 'Many individuals warm up quickly to visitors and household pets when introduced calmly.',
        $friendliness <= 2 => 'Can be watchful or aloof with strangers; early socialization makes a big difference.',
        default => 'Social style varies — some are outgoing, others prefer familiar faces.',
    };

    $bondLine = match (true) {
        $loyalty >= 4 => 'Known for forming strong bonds with their people and staying attentive at home.',
        $loyalty <= 2 => 'Tends toward an independent streak and may enjoy time on their own.',
        default => 'Typically affectionate with family while keeping a balanced temperament.',
    };

    return match ($variant) {
        0 => sprintf(
            '%s belongs to the %s family. %s %s',
            $name,
            $group,
            $energyLine,
            $socialLine
        ),
        1 => sprintf(
            'As a %s %s, %s dogs are often chosen for their personality as much as their looks. %s',
            strtolower($size),
            $group,
            $name,
            $bondLine
        ),
        2 => sprintf(
            '%s is recognized among %s for its characteristic temperament. %s',
            $name,
            $group,
            $energyLine
        ),
        3 => sprintf(
            'Owners often describe %s as a %s breed with a distinct day-to-day rhythm. %s %s',
            $name,
            strtolower($size),
            $bondLine,
            $socialLine
        ),
        4 => str_contains(strtolower($group), 'mixed')
            ? sprintf(
                '%s is a designer or mixed lineage combining traits from parent breeds. %s Individual dogs can vary widely in coat, size, and temperament.',
                $name,
                $energyLine
            )
            : sprintf(
                'Originally associated with the %s group, %s is valued for versatility in family life. %s',
                $group,
                $name,
                $socialLine
            ),
        default => sprintf(
            '%s combines the hallmarks of %s dogs with its own reputation among enthusiasts. %s',
            $name,
            $group,
            $bondLine
        ),
    };
}

/**
 * Formats weight for list/detail — single kg values shown as approximate averages.
 *
 * @param array<string, mixed> $breed
 */
function breed_weight_display(array $breed): string
{
    $raw = trim((string) ($breed['weight_range'] ?? ''));
    if ($raw === '') {
        return '';
    }

    if (preg_match('/^\d+(\.\d+)?\s*kg$/i', $raw) === 1) {
        $kg = preg_replace('/\s*kg/i', '', $raw);

        return '~' . trim((string) $kg) . ' kg avg';
    }

    return $raw;
}

/**
 * Truncates list blurb at word or sentence boundary.
 */
function breed_list_blurb(string $text, int $max = 140): string
{
    if (mb_strlen($text) <= $max) {
        return $text;
    }

    $cut = mb_substr($text, 0, $max);
    $lastPeriod = mb_strrpos($cut, '.');
    if ($lastPeriod !== false && $lastPeriod >= (int) ($max * 0.55)) {
        return mb_substr($cut, 0, $lastPeriod + 1);
    }

    $lastSpace = mb_strrpos($cut, ' ');
    if ($lastSpace !== false && $lastSpace >= (int) ($max * 0.65)) {
        $cut = mb_substr($cut, 0, $lastSpace);
    }

    return rtrim($cut, '.,; ') . '…';
}

/**
 * @param array<string, mixed> $breed
 * @return array{health: string, grooming: string, behavior: string, lifespan: string}
 */
function breed_care_profile(array $breed): array
{
    $health = trim((string) ($breed['common_health_risks'] ?? ''));
    if ($health === '' || $health === 'Average breed health profile') {
        $health = 'Schedule annual vet checkups, keep vaccinations current under RA 9482, and watch for breed-typical joint, skin, and dental issues.';
    }

    $grooming = trim((string) ($breed['grooming_notes'] ?? ''));
    if ($grooming === '') {
        $size = (string) ($breed['size_category'] ?? 'Medium');
        $grooming = match ($size) {
            'Small' => 'Brush 2–3× weekly; moderate shedding; regular nail and dental care.',
            'Large' => 'Brush weekly; heavier shedding seasons; budget for grooming supplies or professional grooming.',
            default => 'Brush weekly; moderate shedding; routine ear and paw checks.',
        };
    }

    $energy = (int) ($breed['energy_score'] ?? 3);
    $friendliness = (int) ($breed['friendliness_score'] ?? 3);
    $behavior = $energy >= 4
        ? 'May need structured exercise to prevent boredom barking or destructive habits.'
        : 'Generally manageable indoors; still needs daily enrichment and walks.';
    if ($friendliness <= 2) {
        $behavior .= ' Early socialization recommended for stranger interactions.';
    }

    $lifespan = trim((string) ($breed['lifespan'] ?? ''));
    if ($lifespan === '') {
        $lifespan = 'Typical lifespan varies by size, nutrition, and preventive vet care.';
    }

    return [
        'health' => $health,
        'grooming' => $grooming,
        'behavior' => $behavior,
        'lifespan' => $lifespan,
    ];
}

/**
 * @param array<string, mixed> $breed
 */
function breed_adoption_guidance(array $breed): array
{
    $size = (string) ($breed['size_category'] ?? 'Medium');
    $energy = (int) ($breed['energy_score'] ?? 3);
    $friendliness = (int) ($breed['friendliness_score'] ?? 3);

    $space = $size === 'Large'
        ? 'Best with yard space or access to daily outdoor exercise areas.'
        : ($size === 'Small' ? 'Can adapt to apartments with regular walks.' : 'Fits homes with moderate space and daily outdoor time.');

    $children = $friendliness >= 4
        ? 'Often good with children when supervised and properly introduced.'
        : 'Supervise interactions with children; temperament varies by individual.';

    $experience = ($energy >= 4 || $friendliness <= 2)
        ? 'Experienced owners may handle training and socialization needs more confidently.'
        : 'Often suitable for first-time owners willing to learn basic training.';

    $commitment = $energy >= 4
        ? 'Expect significant daily time for exercise, training, and enrichment.'
        : 'Plan for daily walks, grooming, and routine vet costs.';

    $summary = !empty($breed['adoption_notes'])
        ? (string) $breed['adoption_notes']
        : 'Review living space, household members, and time for exercise before adopting or purchasing this breed.';

    return [
        'summary' => $summary,
        'space' => $space,
        'children' => $children,
        'experience' => $experience,
        'commitment' => $commitment,
    ];
}

/**
 * @param array<string, mixed> $breed
 */
function breed_legal_global_text(array $breed): string
{
    if (!empty($breed['legal_global'])) {
        return (string) $breed['legal_global'];
    }

    $name = (string) $breed['breed_name'];
    $restricted = preg_match('/pit\s*bull|staffordshire|rottweiler|doberman|dogo|bandog/i', $name) === 1;

    if ($restricted) {
        return $name . ' and closely related breeds may face breed-specific legislation (BSL) or housing restrictions in some countries and municipalities abroad. Requirements vary by jurisdiction — verify local rules if relocating internationally.';
    }

    return 'No widespread international breed ban applies to ' . $name . ' specifically, but airline travel, housing, and insurance policies may still impose pet restrictions. Check destination rules before travel.';
}

/**
 * @param array<string, mixed> $breed
 */
function breed_legal_philippines_text(array $breed): string
{
    if (!empty($breed['legal_philippines'])) {
        return (string) $breed['legal_philippines'];
    }

    return 'Under the Anti-Rabies Act of 2007 (RA 9482), all dogs in the Philippines — including '
        . (string) $breed['breed_name']
        . ' — must be registered and vaccinated against rabies. No specific national breed ban currently applies beyond standard registration, vaccination, and responsible ownership requirements. Contact your barangay LGU official for local leash, registration, and ordinance details.';
}

/**
 * Legal disclaimer block.
 */
function breed_legal_disclaimer(): string
{
    return 'Laws and local ordinances may change. Confirm current requirements with your barangay or city LGU office.';
}

/**
 * @return list<string>
 */
function breed_local_names(): array
{
    return ['Aspin (Asong Pinoy)', 'Aspin', 'Asong Pinoy', 'Mixed Breed'];
}

/**
 * @param array<string, mixed> $breed
 */
function breed_is_local(array $breed): bool
{
    if ((int) ($breed['is_local_breed'] ?? 0) === 1) {
        return true;
    }

    return in_array((string) ($breed['breed_name'] ?? ''), breed_local_names(), true);
}

/**
 * @return list<array{slug: string, label: string, sql: string}>
 */
function breed_mood_filters(): array
{
    return [
        ['slug' => 'energetic', 'label' => 'Energetic', 'sql' => 'b.energy_score >= 4'],
        ['slug' => 'chill', 'label' => 'Chill', 'sql' => 'b.energy_score <= 2'],
        ['slug' => 'kids', 'label' => 'Great with kids', 'sql' => 'b.friendliness_score >= 4'],
        ['slug' => 'independent', 'label' => 'Independent', 'sql' => '(b.friendliness_score <= 2 OR b.loyalty_score <= 2)'],
    ];
}

/**
 * @return list<array{slug: string, label: string}>
 */
function breed_sort_options(): array
{
    return [
        ['slug' => 'name_asc', 'label' => 'A–Z'],
        ['slug' => 'name_desc', 'label' => 'Z–A'],
        ['slug' => 'registered', 'label' => 'Most registered on Pawdar'],
    ];
}
