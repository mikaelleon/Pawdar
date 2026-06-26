<?php
require_once __DIR__ . '/includes/app-layout.php';
require_once __DIR__ . '/includes/dogs.php';

$pdo = db();
$userId = (int) $_SESSION['user_id'];
$userRole = current_user_role();
$message = '';

$stmt = $pdo->prepare('SELECT * FROM user WHERE UserID = :id LIMIT 1');
$stmt->execute([':id' => $userId]);
$user = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && validate_csrf((string) ($_POST['csrf_token'] ?? ''))) {
    $action = (string) ($_POST['action'] ?? 'profile');
    if ($action === 'profile') {
        $pdo->prepare('UPDATE user SET Name = :name, Email = :email, Phone = :phone, Barangay = :barangay WHERE UserID = :id')
            ->execute([
                ':name' => trim((string) $_POST['name']),
                ':email' => trim((string) $_POST['email']),
                ':phone' => trim((string) $_POST['phone']),
                ':barangay' => trim((string) $_POST['barangay']),
                ':id' => $userId,
            ]);
        $_SESSION['user_name'] = trim((string) $_POST['name']);
        $_SESSION['user_barangay'] = trim((string) $_POST['barangay']);
        $_SESSION['user_initials'] = user_initials_from_name(trim((string) $_POST['name']));
        $message = 'Profile updated.';
    }
    if ($action === 'password') {
        $current = (string) ($_POST['current_password'] ?? '');
        $new = (string) ($_POST['new_password'] ?? '');
        if (password_verify($current, (string) $user['Password']) && strlen($new) >= 8) {
            $pdo->prepare('UPDATE user SET Password = :pass WHERE UserID = :id')
                ->execute([':pass' => password_hash($new, PASSWORD_DEFAULT), ':id' => $userId]);
            $message = 'Password updated.';
        } else {
            $message = 'Could not update password.';
        }
    }
    $stmt->execute([':id' => $userId]);
    $user = $stmt->fetch();
}

$myDogs = $userRole === 'Dog Owner' ? fetch_registry_dogs($pdo, 'Dog Owner', $userId, (string) $user['Barangay']) : [];

app_layout_start('feed', 'Profile', [
    'showSearch' => false,
    'topbarTitle' => 'Profile & Settings',
    'scripts' => ['assets/js/profile.js'],
    'breadcrumbs' => [['label' => 'My Profile']],
]);
?>

<div class="feed-header"><h1 class="feed-title">Profile & settings</h1></div>
<?php if ($message !== ''): ?><p class="text-sm" style="color:var(--tea-green);font-weight:700;"><?= htmlspecialchars($message) ?></p><?php endif; ?>

<div class="card card-bordered card-body mb-md flex items-center gap-md">
    <div class="avatar avatar-lg <?= htmlspecialchars(avatar_color_class($userId)) ?>"><?= htmlspecialchars(user_initials_from_name((string) $user['Name'])) ?></div>
    <div>
        <div style="font-weight:700;font-size:20px;"><?= htmlspecialchars((string) $user['Name']) ?></div>
        <span class="badge badge-owned"><?= htmlspecialchars((string) $user['Role']) ?></span>
        <p class="text-xs text-muted mt-sm">Member since <?= date('M Y', strtotime((string) ($user['created_at'] ?? 'now'))) ?></p>
    </div>
</div>

<form method="post" class="card card-bordered card-body mb-md">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars((string) $_SESSION['csrf_token']) ?>">
    <input type="hidden" name="action" value="profile">
    <div class="label-upper mb-md">Personal information</div>
    <label class="field-label">Full name</label>
    <input class="field-input mb-md" name="name" value="<?= htmlspecialchars((string) $user['Name']) ?>">
    <label class="field-label">Email</label>
    <input class="field-input mb-md" type="email" name="email" value="<?= htmlspecialchars((string) $user['Email']) ?>">
    <label class="field-label">Contact number</label>
    <input class="field-input mb-md" name="phone" value="<?= htmlspecialchars((string) ($user['Phone'] ?? '')) ?>">
    <label class="field-label">Barangay</label>
    <input class="field-input mb-md" name="barangay" value="<?= htmlspecialchars((string) $user['Barangay']) ?>">
    <button type="submit" class="btn-primary btn-sm">Save changes</button>
</form>

<form method="post" class="card card-bordered card-body mb-md">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars((string) $_SESSION['csrf_token']) ?>">
    <input type="hidden" name="action" value="password">
    <div class="label-upper mb-md">Password</div>
    <input class="field-input mb-md" type="password" name="current_password" placeholder="Current password">
    <input class="field-input mb-md" type="password" name="new_password" placeholder="New password">
    <input class="field-input mb-md" type="password" name="confirm_password" placeholder="Confirm new password">
    <button type="submit" class="btn-outline btn-sm">Update password</button>
</form>

<div class="card card-bordered card-body mb-md">
    <div class="label-upper mb-md">Notification preferences</div>
    <?php foreach ([
        'notify_incidents' => 'New incidents in my barangay',
        'notify_dog_match' => 'Incidents matching my dogs',
        'notify_case_updates' => 'Case status updates on my reports',
        'notify_vaccine' => 'Vaccination record updates',
    ] as $field => $label): ?>
        <label class="flex justify-between items-center mb-md text-sm">
            <span><?= htmlspecialchars($label) ?></span>
            <input type="checkbox" data-notify-pref="<?= htmlspecialchars($field) ?>" <?= (int) ($user[$field] ?? 1) ? 'checked' : '' ?>>
        </label>
    <?php endforeach; ?>
</div>

<?php if ($userRole === 'Dog Owner'): ?>
<div class="card card-bordered card-body">
    <div class="label-upper mb-md">My dogs</div>
    <?php foreach ($myDogs as $dog): ?>
        <a href="dog-profile.php?id=<?= (int) $dog['dog_id'] ?>" class="text-sm block mb-sm"><?= htmlspecialchars((string) $dog['DogName']) ?></a>
    <?php endforeach; ?>
    <a href="register_dog.php" class="link-hover text-sm">Register another dog</a>
</div>
<?php endif; ?>

<?php app_layout_end([]); ?>
