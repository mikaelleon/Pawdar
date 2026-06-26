<?php
$funFacts = [
    "A dog's nose print is as unique as a human fingerprint.",
    "Dogs have three eyelids. The third one helps keep their eyes moist.",
    "A dog's sense of smell is 10,000 to 100,000 times stronger than a human's.",
    'Dalmatians are born completely white and develop their spots as they grow older.',
    'Dogs can recognize over 150 words and count up to four or five.',
    "The Basenji is the only dog breed that doesn't bark. It yodels instead.",
    "A dog's heart beats 60 to 140 times per minute.",
    'Greyhounds can reach speeds of up to 70 km/h, making them the fastest dog breed.',
    'Dogs curl up when sleeping to protect their organs, a leftover survival instinct.',
    'Puppies are born blind, deaf, and without teeth.',
    'The oldest dog on record, Bluey, lived to 29 years old.',
    'Dogs have 18 muscles in each ear to help them locate sound.',
    "A dog's average body temperature is 38.5°C.",
    'Chow Chows and Shar-Peis have blue-black tongues, unlike most other dog breeds.',
    'Dogs dream. They experience REM sleep just like humans do.',
    'The Labrador Retriever has been the most popular dog breed in the Philippines for over a decade.',
    'Dogs sweat only through their paw pads. They cool down mainly by panting.',
    'Aspin (Asong Pinoy) dogs are believed to be one of the oldest dog types in Southeast Asia.',
    'A study found that dogs can smell cancer, low blood sugar, and seizures in humans.',
    'Dogs have been domesticated for at least 15,000 years.',
];

$dayIndex = (int) date('z') % count($funFacts);
$fact = $funFacts[$dayIndex];
?>
<div class="bento-card funfact-card">
    <div class="bento-card-header">
        <span class="bento-icon" aria-hidden="true">🐾</span>
        <span class="bento-label">Dog fact of the day</span>
    </div>
    <p class="funfact-text"><?= htmlspecialchars($fact) ?></p>
    <span class="bento-footer">Refreshes daily</span>
</div>
