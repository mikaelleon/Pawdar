-- Pawdar schema v11: First Aid Guides content and interaction enhancements.
-- Run after schema-v10-dog-profile-demo.sql. Safe to re-run.

ALTER TABLE first_aid_guides ADD COLUMN IF NOT EXISTS facts_section JSON NULL AFTER steps;

-- Close out legacy Trash Disturbance naming/icon everywhere in guides content.
UPDATE first_aid_guides
SET incident_type = 'Disturbance'
WHERE incident_type IN ('Trash Disturbance', 'trash_disturbance');

UPDATE first_aid_guides
SET title = 'Disturbance First Aid'
WHERE title LIKE '%Trash Disturbance%';

UPDATE first_aid_guides
SET icon = 'footprints'
WHERE incident_type = 'Disturbance' AND icon IN ('trash-2', 'trash', 'trash2');

UPDATE first_aid_guides
SET warning_text = 'Stray dogs causing disturbances are often hungry or seeking shelter — avoid feeding them directly as it encourages return visits.'
WHERE incident_type = 'Disturbance'
  AND warning_text LIKE '%Trash-disturbing%';

UPDATE first_aid_guides SET
    steps = '[
        {"summary":"Wash the wound immediately with soap and running water for at least 15 minutes.","icon":"droplets","detail":"Move to safety first, then control any bleeding with firm, direct pressure using a clean cloth or sterile gauze. Elevate the injured area above the heart when possible. Once bleeding slows, wash the wound thoroughly with mild soap and warm, running water for at least 5 to 15 minutes to flush out saliva and bacteria. Avoid harsh chemicals like hydrogen peroxide or iodine, which can damage tissue."},
        {"summary":"Apply an antiseptic such as povidone-iodine or alcohol to the cleaned area.","icon":"bandage","detail":"After cleaning, pat the area dry with a clean towel. Apply a thin layer of over-the-counter antibiotic ointment (such as Neosporin or Bacitracin) if available. Cover the bite with a sterile, dry bandage or dressing to protect it from dirt and further irritation. Change the bandage daily or whenever it becomes wet or dirty."},
        {"summary":"Control bleeding with a clean cloth and gentle pressure; do not close deep wounds.","icon":"hand","detail":"Apply steady, direct pressure without releasing to check too often. If blood soaks through, add more cloth on top rather than removing the first layer. Do not use a tourniquet unless directed by emergency personnel. Deep or gaping wounds should remain open — do not attempt to suture or tightly close them yourself."},
        {"summary":"Note the dog''s description and location, then go to the nearest clinic for anti-rabies evaluation.","icon":"clipboard-list","detail":"Seek medical attention immediately if the bite is deep, gaping, or won''t stop bleeding; if it is on the face, hands, feet, or near a joint; or if it was caused by a wild animal, stray, or unvaccinated pet. Medical care is also necessary if you have not had a tetanus shot in the last 5–10 years, if you notice signs of infection (redness, swelling, pus, fever), or if there is any risk of rabies exposure. Rabies post-exposure prophylaxis is critical for bites from high-risk animals."}
    ]',
    facts_section = '{
        "items": [
            {"heading": "What is rabies?", "body": "Rabies is a viral disease that attacks the nervous system and is almost always fatal once symptoms appear. It is preventable through prompt wound care and post-exposure vaccination."},
            {"heading": "How it spreads", "body": "Rabies spreads through the saliva of infected animals, usually via bites or scratches that break the skin. In Batangas and across the Philippines, unvaccinated stray dogs remain a significant exposure risk — barangay-level vaccination drives are a key prevention strategy."},
            {"heading": "Warning signs", "body": "In dogs: unusual aggression, excessive drooling, difficulty swallowing, paralysis, or sudden behavior changes. In humans after exposure: fever, tingling at the bite site, anxiety, hydrophobia, and muscle spasms — seek care before symptoms develop."}
        ],
        "source": "WHO Rabies Fact Sheet; PH DOH Rabies Prevention and Control Program (2024)"
    }'
WHERE incident_type = 'Animal Bite';

UPDATE first_aid_guides SET
    steps = '[
        {"summary":"Ensure traffic is clear before approaching the dog.","icon":"shield-alert","detail":"Stop at a safe distance and assess traffic flow. Turn on hazard lights, use a reflective vest if available, and ask a second person to warn oncoming vehicles. Never step into active traffic lanes without confirming drivers have slowed or stopped."},
        {"summary":"Do not move the dog unless it is in immediate danger.","icon":"ban","detail":"Sudden movement can worsen spinal, pelvic, or internal injuries. If the dog must be moved from the roadway, use a firm board or blanket as a stretcher with at least two people supporting the head, torso, and hips in a straight line."},
        {"summary":"Check for breathing and visible bleeding; apply gentle pressure to wounds.","icon":"heart-pulse","detail":"Observe chest rise and listen for breathing. If the dog is unconscious but breathing, keep the airway clear and head slightly extended. Apply light pressure to bleeding sites with clean cloth — avoid pressing on obvious fractures or swollen areas."},
        {"summary":"Cover the dog with a blanket to prevent shock and call a vet or rescue immediately.","icon":"thermometer","detail":"Shock can develop quickly after trauma. Keep the dog warm, calm, and still while waiting for professional help. Note the location, time, and vehicle description if applicable, and transport only when a vet or trained rescuer advises."}
    ]',
    facts_section = '{
        "items": [
            {"heading": "Common injuries", "body": "Stray and owned dogs hit by vehicles often suffer fractures, internal bleeding, head trauma, and shock. External wounds may be less severe than hidden injuries."},
            {"heading": "Local road risk", "body": "Free-roaming dogs near markets, schools, and barangay roads in Batangas face higher collision risk, especially at dawn and dusk when visibility is low."},
            {"heading": "Why not to move them", "body": "Improper lifting is a leading cause of worsened spinal injury in animal trauma cases. Wait for trained responders when the scene is safe."}
        ],
        "source": "AVMA Emergency Care Guidelines (2023); MMDA Animal Rescue Field Protocol"
    }'
WHERE incident_type = 'Vehicular Accident';

UPDATE first_aid_guides SET
    steps = '[
        {"summary":"Approach slowly from the side; avoid direct eye contact.","icon":"eye-off","detail":"Injured animals are often frightened and may bite defensively. Speak softly, crouch to appear less threatening, and never approach from directly in front. Watch for growling, stiff posture, or attempts to flee."},
        {"summary":"Use a blanket or towel to gently restrain the dog if needed.","icon":"layers","detail":"If you must handle the dog, toss a blanket over the head and body to limit vision and movement. Support the body evenly — avoid grabbing limbs or pulling on injured areas. Wear gloves when possible."},
        {"summary":"Check for visible wounds and apply light pressure to stop bleeding.","icon":"bandage","detail":"Look for cuts, swelling, limping, or open fractures. Apply gentle pressure with a clean cloth to slow bleeding. Do not remove embedded objects or attempt to set bones."},
        {"summary":"Contact a local rescue organization or vet — do not attempt complex treatment alone.","icon":"phone","detail":"Call your barangay animal control officer, a licensed veterinarian, or a rescue group such as PAWS. Provide the exact location and a brief description of injuries. Stay nearby at a safe distance until help arrives."}
    ]',
    facts_section = '{
        "items": [
            {"heading": "Why strays get injured", "body": "Common causes include vehicle strikes, fights with other animals, wire traps, malnutrition, and untreated infections. Early reporting improves survival chances."},
            {"heading": "Approach safely", "body": "Even friendly dogs may bite when in pain. Use barriers, avoid sudden movements, and never corner an injured animal against a wall or fence."},
            {"heading": "Barangay role", "body": "Reporting injured strays through Pawdar helps barangay health workers coordinate vaccination, rescue, and humane population management."}
        ],
        "source": "PAWS Animal Rescue Field Guide (2024); PH Animal Welfare Act (RA 8485)"
    }'
WHERE incident_type = 'Injured Stray';

UPDATE first_aid_guides SET
    steps = '[
        {"summary":"Do not run — stand still or back away slowly without turning your back.","icon":"hand","detail":"Running triggers a chase response in many dogs. Stand sideways, keep your hands low and still, and avoid screaming or making sudden gestures. Back away slowly to increase distance without provoking pursuit."},
        {"summary":"Put an object (bag, jacket, bike) between you and the dog.","icon":"shield","detail":"Use any available barrier — a backpack, umbrella, bicycle, or stick — to create space. If the dog charges, hold the object forward to redirect the bite away from your body. Do not swing or strike unless absolutely necessary for self-defense."},
        {"summary":"If bitten, follow animal bite first aid steps immediately.","icon":"droplets","detail":"Treat any broken skin as a potential rabies exposure. Wash the wound with soap and running water for at least 15 minutes, apply antiseptic, control bleeding, and seek medical evaluation the same day."},
        {"summary":"Report the incident on Pawdar so your barangay can respond.","icon":"megaphone","detail":"Document the location, time, dog description (size, color, collar), and behavior. Barangay officials use reports to identify repeat offenders, plan vaccination drives, and protect the community."}
    ]',
    facts_section = '{
        "items": [
            {"heading": "Warning body language", "body": "Raised hackles, stiff tail, hard stare, bared teeth, low growling, and lunging without contact are common pre-bite signals. Recognizing these early can prevent escalation."},
            {"heading": "Common triggers", "body": "Territorial guarding, fear, maternal protection, pain, and competition over food are frequent causes of aggressive displays toward people."},
            {"heading": "Prevention", "body": "Avoid approaching unfamiliar dogs, especially those tied up or with puppies. Teach children not to disturb sleeping or eating animals."}
        ],
        "source": "CDC Dog Bite Prevention Guidelines (2024); WHO Community Rabies Prevention"
    }'
WHERE incident_type = 'Aggressive Behavior';

UPDATE first_aid_guides SET
    steps = '[
        {"summary":"Stay calm and keep a safe distance from the dog.","icon":"shield","detail":"Loud reactions or chasing can escalate the situation. Give the dog space to move away on its own. Keep children and pets indoors until the area is clear."},
        {"summary":"Avoid chasing or cornering the animal.","icon":"move-diagonal","detail":"Cornered dogs are more likely to bark, lunge, or bite. Block access to food sources calmly rather than pursuing the animal through yards or alleys."},
        {"summary":"If the dog appears sick or injured, report it as an Injured Stray instead.","icon":"stethoscope","detail":"Limping, visible wounds, lethargy, or unusual aggression may indicate injury or illness rather than simple nuisance behavior. Use the Injured Stray guide and report type for proper barangay response."},
        {"summary":"Contact the barangay if stray dogs are regularly causing disturbances in the area.","icon":"building-2","detail":"Recurring disturbances — barking at night, litter scattering, blocking pathways — should be reported so officials can coordinate vaccination, responsible feeding policies, and humane population management under local ordinances."}
    ]',
    facts_section = '{
        "items": [
            {"heading": "Community impact", "body": "Unmanaged stray populations can increase noise complaints, waste scattering, traffic hazards, and rabies exposure risk — issues barangays address through coordinated animal health programs."},
            {"heading": "Local ordinances", "body": "Many Batangas barangays enforce RA 9482 (Anti-Rabies Act) requirements for dog registration, vaccination, and responsible ownership. Reporting helps target education and vaccination efforts."},
            {"heading": "Do not feed strays directly", "body": "Hand-feeding encourages dogs to return and gather in groups. Work with barangay officials on structured, humane population management instead."}
        ],
        "source": "Local Barangay Animal Control Advisory (2024); RA 9482 Anti-Rabies Act"
    }'
WHERE incident_type = 'Disturbance';
