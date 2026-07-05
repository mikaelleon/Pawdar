<?php

/**
 * Transactional email via Resend API (https://resend.com).
 *
 * Configure in repo-root `.env` — see `.env.example` and docs/EMAIL_SETUP.md.
 */

/**
 * Returns true when a Resend API key is configured.
 */
function pawdar_resend_configured(): bool
{
    return (getenv('RESEND_API_KEY') ?: '') !== '';
}

/**
 * Sends an email through Resend. Returns true on HTTP 2xx.
 */
function pawdar_send_email(string $to, string $subject, string $html, ?string $text = null): bool
{
    $apiKey = getenv('RESEND_API_KEY') ?: '';
    if ($apiKey === '') {
        error_log('pawdar_send_email: RESEND_API_KEY is not set');
        return false;
    }

    $from = getenv('RESEND_FROM') ?: 'Pawdar <onboarding@resend.dev>';
    $payload = [
        'from' => $from,
        'to' => [$to],
        'subject' => $subject,
        'html' => $html,
    ];
    if ($text !== null && $text !== '') {
        $payload['text'] = $text;
    }

    if (!function_exists('curl_init')) {
        error_log('pawdar_send_email: PHP cURL extension is required');
        return false;
    }

    $ch = curl_init('https://api.resend.com/emails');
    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            'Authorization: Bearer ' . $apiKey,
            'Content-Type: application/json',
        ],
        CURLOPT_POSTFIELDS => json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
        CURLOPT_TIMEOUT => 15,
    ]);

    $response = curl_exec($ch);
    $status = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);

    if ($response === false) {
        error_log('pawdar_send_email curl error: ' . $curlError);
        return false;
    }

    if ($status < 200 || $status >= 300) {
        error_log('pawdar_send_email HTTP ' . $status . ': ' . $response);
        return false;
    }

    return true;
}

/**
 * Builds an absolute URL to a path under the web app root.
 */
function pawdar_app_url(string $path = ''): string
{
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = (string) ($_SERVER['HTTP_HOST'] ?? 'localhost');
    $basePath = rtrim(dirname((string) ($_SERVER['SCRIPT_NAME'] ?? '/web')), '/\\');
    if (str_ends_with($basePath, '/auth')) {
        $basePath = dirname($basePath);
    }

    $path = ltrim(str_replace('\\', '/', $path), '/');
    $url = $scheme . '://' . $host . $basePath;

    return $path === '' ? $url : $url . '/' . $path;
}

/**
 * @return array{html: string, text: string}
 */
function pawdar_verification_email_content(string $name, string $verifyUrl): array
{
    $safeName = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
    $safeUrl = htmlspecialchars($verifyUrl, ENT_QUOTES, 'UTF-8');

    $html = '<div style="font-family:Nunito,Arial,sans-serif;color:#3d3d3d;max-width:520px;">'
        . '<h1 style="font-size:22px;color:#4a6741;">Confirm your Pawdar account</h1>'
        . '<p>Hi ' . $safeName . ',</p>'
        . '<p>Thanks for signing up. Confirm your email to finish setting up your account.</p>'
        . '<p style="margin:24px 0;">'
        . '<a href="' . $safeUrl . '" style="background:#c96442;color:#fff;padding:12px 20px;border-radius:8px;text-decoration:none;font-weight:700;">Verify my email</a>'
        . '</p>'
        . '<p style="font-size:13px;color:#6c8b9f;">This link expires in 24 hours. If you did not sign up, you can ignore this email.</p>'
        . '<p style="font-size:12px;color:#6c8b9f;">Pawdar processes your data in compliance with the Philippine Data Privacy Act (RA 10173).</p>'
        . '</div>';

    $text = "Hi {$name},\n\nConfirm your Pawdar account (valid 24 hours):\n{$verifyUrl}\n\n"
        . "If you did not sign up, ignore this email.";

    return ['html' => $html, 'text' => $text];
}

/**
 * @return array{html: string, text: string}
 */
function pawdar_password_reset_email_content(string $name, string $resetUrl): array
{
    $safeName = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
    $safeUrl = htmlspecialchars($resetUrl, ENT_QUOTES, 'UTF-8');

    $html = '<div style="font-family:Nunito,Arial,sans-serif;color:#3d3d3d;max-width:520px;">'
        . '<h1 style="font-size:22px;color:#4a6741;">Reset your Pawdar password</h1>'
        . '<p>Hi ' . $safeName . ',</p>'
        . '<p>We received a request to reset your password. Use the button below within 1 hour.</p>'
        . '<p style="margin:24px 0;">'
        . '<a href="' . $safeUrl . '" style="background:#c96442;color:#fff;padding:12px 20px;border-radius:8px;text-decoration:none;font-weight:700;">Reset password</a>'
        . '</p>'
        . '<p style="font-size:13px;color:#6c8b9f;">If you did not request this, you can safely ignore this email.</p>'
        . '</div>';

    $text = "Hi {$name},\n\nReset your Pawdar password (valid 1 hour):\n{$resetUrl}\n\n"
        . "If you did not request this, ignore this email.";

    return ['html' => $html, 'text' => $text];
}
