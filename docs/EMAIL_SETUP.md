# Pawdar — Email (Resend) Setup

Pawdar sends transactional email through [Resend](https://resend.com) for:

- **Email verification** after sign up (`verify.php`, resend button)
- **Password reset** (`forgot_password.php`)

## 1. Configure API key

Copy `.env.example` to `.env` at the **repository root** and set:

```env
RESEND_API_KEY=re_your_api_key_here
RESEND_FROM="Pawdar <onboarding@resend.dev>"
```

Never commit `.env` (listed in root `.gitignore`).

PHP loads `.env` automatically on each request via `web/includes/env.php`.

## 2. Testing vs production

| Mode | `RESEND_FROM` | Recipients |
|------|---------------|------------|
| **Testing** (no domain verified) | `Pawdar <onboarding@resend.dev>` | Only the email on your Resend account |
| **Production** | `Pawdar <noreply@yourdomain.com>` | Any user, after domain verification in Resend |

## 3. Requirements

- PHP **cURL** extension enabled (default in XAMPP)
- Outbound HTTPS to `api.resend.com`

## 4. Verification flow

1. User completes sign up → `auth/signup-handler.php` creates account and sends verification email
2. User lands on **`verify.php`** — check inbox, resend if needed
3. User clicks link → **`auth/verify-email.php?token=...`**
4. Success → **`email_verified.php`** → continue to feed or pending approval screen

## 5. Troubleshooting

- Check Resend dashboard → **Emails** for delivery logs and errors
- Check PHP `error_log` for `pawdar_send_email` messages
- On localhost without Resend key, `verify.php` shows a configuration warning
