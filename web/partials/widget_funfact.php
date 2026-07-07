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
<div class="bento-card safety-tip-card">
    <div class="bento-card-header">
        <span class="bento-icon" aria-hidden="true"><i data-lucide="lightbulb"></i></span>
        <span class="bento-label">Safety tip</span>
        <span class="safety-tip-caption text-xs">Rotates daily</span>
    </div>
    <p class="safety-tip-text"><?= htmlspecialchars($tip) ?></p>
</div>
