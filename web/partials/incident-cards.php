<?php
/**
 * Reusable incident feed cards from the design system.
 */
function render_incident_cards(): void
{
    $incidents = [
        [
            'type' => 'Animal Bite',
            'badge' => 'badge-bite',
            'accent' => 'accent-bite',
            'icon' => 'dog',
            'title' => 'Loose dog bit a jogger near the creek',
            'location' => 'Riverside Park, Brgy. San Roque',
            'time' => '12m ago',
            'corroborate' => 4,
            'status' => 'Investigating',
            'status_class' => 'badge-investigating',
            'distance' => '0.4 km',
        ],
        [
            'type' => 'Injured Stray',
            'badge' => 'badge-injured',
            'accent' => 'accent-injured',
            'icon' => 'paw-print',
            'title' => 'Limping stray near the wet market',
            'location' => 'Market St., Brgy. Poblacion',
            'time' => '38m ago',
            'corroborate' => 1,
            'status' => 'Received',
            'status_class' => 'badge-received',
            'distance' => '1.1 km',
        ],
        [
            'type' => 'Aggressive',
            'badge' => 'badge-aggressive',
            'accent' => 'accent-aggressive',
            'icon' => 'dog',
            'title' => 'Dog lunging at passersby on Acacia Ave',
            'location' => 'Acacia Ave, Brgy. Maligaya',
            'time' => '2h ago',
            'corroborate' => 9,
            'status' => 'Resolved',
            'status_class' => 'badge-resolved',
            'distance' => '2.3 km',
        ],
    ];

    foreach ($incidents as $item): ?>
        <article class="incident-card card-bordered">
            <div class="accent <?= $item['accent'] ?>"></div>
            <div class="card-body" style="flex:1;">
                <div class="flex justify-between items-center mb-md" style="margin-bottom:10px;">
                    <span class="badge <?= $item['badge'] ?>"><?= htmlspecialchars($item['type']) ?></span>
                    <span class="text-xs text-muted"><?= htmlspecialchars($item['time']) ?></span>
                </div>
                <div class="flex gap-md" style="gap:11px;">
                    <div class="icon-box icon-box-md"><i data-lucide="<?= $item['icon'] ?>"></i></div>
                    <div class="flex-1" style="min-width:0;">
                        <div style="font-weight:800;font-size:15px;line-height:1.25;"><?= htmlspecialchars($item['title']) ?></div>
                        <div class="text-xs text-muted mt-sm flex items-center gap-sm" style="margin-top:3px;">
                            <i data-lucide="map-pin" style="width:13px;height:13px;"></i>
                            <?= htmlspecialchars($item['location']) ?>
                        </div>
                    </div>
                </div>
                <div class="flex justify-between items-center" style="margin-top:13px;padding-top:12px;border-top:1px solid var(--border);">
                    <div class="chip chip-outline" style="padding:6px 12px;font-size:12px;">
                        <i data-lucide="thumbs-up" style="width:14px;height:14px;color:var(--air-force);"></i>
                        Corroborate · <?= (int) $item['corroborate'] ?>
                    </div>
                    <div class="flex items-center gap-sm">
                        <span class="badge <?= $item['status_class'] ?>"><?= htmlspecialchars($item['status']) ?></span>
                        <span class="text-xs text-muted" style="font-weight:700;"><?= htmlspecialchars($item['distance']) ?></span>
                    </div>
                </div>
            </div>
        </article>
    <?php endforeach;
}
