<?php
$safetyTips = [
    'If bitten, wash the wound with soap and running water for at least 15 minutes.',
    'Never chase an aggressive dog — back away slowly and avoid direct eye contact.',
    'Keep trash bins secured to reduce stray foraging and neighborhood disturbances.',
    'Report injured strays promptly so rescue teams can respond before conditions worsen.',
    'During rabies watch, monitor the dog daily and log symptoms for your LGU case file.',
    'Carry a photo of your registered dog\'s QR tag when traveling — it speeds reunification.',
    'Teach children to ask an owner before petting and to stay calm around unfamiliar dogs.',
    'After a vehicular incident involving a dog, move to safety first, then report exact location.',
    'Vaccination records help vets verify status faster during bite or exposure cases.',
    'Corroborate feed reports you witnessed — it helps officials prioritize real emergencies.',
];

$dayIndex = (int) date('z') % count($safetyTips);
$tip = $safetyTips[$dayIndex];
?>
<details class="bento-card safety-tip-card">
    <summary class="bento-card-header safety-tip-summary">
        <span class="bento-icon" aria-hidden="true">💡</span>
        <span class="bento-label">Safety tip</span>
        <span class="text-xs text-muted">Tap to expand</span>
    </summary>
    <p class="safety-tip-text"><?= htmlspecialchars($tip) ?></p>
    <span class="bento-footer">Rotates daily · Pet trivia moved here to keep utility widgets first</span>
</details>
