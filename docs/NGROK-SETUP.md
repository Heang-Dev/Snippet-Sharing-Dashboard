# Ngrok Setup for Local Team Testing

This guide explains how to use Laravel Valet + Ngrok for local team testing, allowing multiple testers to access the application remotely while all data is saved to the local database on the host machine.

## Overview

```
[Remote Testers] → [Ngrok HTTPS Tunnel] → [Laravel Valet] → [Local SQLite DB]
     ↓                    ↓                     ↓                  ↓
  Mobile/PC         abc123.ngrok-free.app    snippet-g11.test    database.sqlite
```

## Prerequisites

1. **Laravel Valet** installed and running
2. **Ngrok** installed (`brew install ngrok` or download from ngrok.com)
3. **Ngrok account** (free tier works, but sessions expire after 2 hours)

## Quick Start

### Step 1: Start Laravel Valet

```bash
# Make sure Valet is running
valet start

# Link your project (if not already done)
cd /path/to/Snippet_Sharing_Dashboard
valet link snippet-g11

# Verify it works locally
open http://snippet-g11.test
```

### Step 2: Build Assets (IMPORTANT!)

```bash
# Stop Vite dev server if running (Ctrl+C)
# Then build production assets
npm run build
```

> **CRITICAL**: Remote users cannot access your local Vite dev server! You must run `npm run build` before sharing via Ngrok.

### Step 3: Configure Environment

Add to your `.env` file:

```env
SESSION_DOMAIN=null
```

Then clear config:

```bash
php artisan config:clear
```

### Step 4: Start Ngrok Tunnel

```bash
# Share your Valet site via Ngrok
valet share

# Or use ngrok directly
ngrok http snippet-g11.test:80
```

### Step 5: Share the Ngrok URL

Ngrok will display a URL like:
```
https://abc123def.ngrok-free.app
```

Share this URL with your team. They can access the full application from any device.

## Configuration Details

### What's Already Configured

The following configurations have been set up to support Ngrok:

#### 1. TrustNgrokProxy Middleware
**File:** `app/Http/Middleware/TrustNgrokProxy.php`

This middleware automatically:
- Detects requests coming through Ngrok
- Forces HTTPS URL generation
- Sets correct request scheme and host

#### 2. Session Configuration
**File:** `config/session.php`

- `SESSION_DOMAIN=null` - Allows sessions to work on any domain
- `SESSION_SECURE_COOKIE=null` - Auto-detects based on request scheme

#### 3. Sanctum Configuration
**File:** `config/sanctum.php`

- Automatically includes `*.ngrok-free.app` and `*.ngrok.io` in stateful domains
- Only applies in `local` environment

#### 4. Filesystems Configuration
**File:** `config/filesystems.php`

- Uses relative URLs (`/storage`) instead of absolute URLs
- Works correctly regardless of the domain

#### 5. CSRF Protection
**File:** `bootstrap/app.php`

- API routes (`api/*`) are excluded from CSRF verification
- Web routes remain protected

## Environment Variables

### Required `.env` Settings for Ngrok

```env
# App Configuration
APP_ENV=local
APP_DEBUG=true
APP_URL=http://snippet-g11.test  # Keep this as your Valet URL

# Session Configuration
SESSION_DRIVER=database
SESSION_DOMAIN=null           # Important: null allows any domain
SESSION_SECURE_COOKIE=        # Empty = auto-detect
```

## Common Issues & Solutions

### Issue 1: White Screen

**Cause:** Vite dev server running - assets point to `127.0.0.1:5173`

**Solution:**
1. Stop `npm run dev` (Ctrl+C)
2. Delete `public/hot` file if it exists
3. Run `npm run build`
4. Refresh the Ngrok URL

### Issue 2: "Page expired" (419 Error)

**Cause:** CSRF token mismatch, usually due to session issues.

**Solution:**
1. Clear config cache: `php artisan config:clear`
2. Verify `SESSION_DOMAIN=null` in `.env`

### Issue 3: Login redirects back to login page

**Cause:** Session cookie not being set correctly for Ngrok domain.

**Solution:**
1. Ensure `SESSION_DOMAIN=null` in `.env`
2. Check that `SESSION_SAME_SITE=lax` (not `strict`)
3. Clear browser cookies for the Ngrok domain

### Issue 4: Images/assets not loading

**Cause:** Absolute URLs pointing to wrong domain.

**Solution:**
1. Verify `filesystems.php` uses relative URLs
2. Run `php artisan storage:link` if not done
3. Clear config cache: `php artisan config:clear`

### Issue 5: Ngrok session expired

**Cause:** Free Ngrok sessions expire after 2 hours.

**Solution:**
1. Restart Ngrok: `valet share` or `ngrok http ...`
2. Share new URL with team
3. Consider Ngrok paid plan for persistent URLs

## After Testing

When done testing, you may want to:

```bash
# Stop Ngrok
Ctrl+C in the ngrok terminal

# Clear caches to reset to normal operation
php artisan config:clear
php artisan cache:clear
php artisan route:clear

# Resume development with Vite
npm run dev
```

## Security Notes

1. **Development Only:** This setup is for development/testing only
2. **Production:** Never use Ngrok in production
3. **Credentials:** Don't commit Ngrok URLs to version control
4. **Environment:** All Ngrok-specific configs only apply when `APP_ENV=local`

## Ngrok Free Plan Limitations

| Feature | Free Plan | Paid Plan |
|---------|-----------|-----------|
| Session Duration | 2 hours | Unlimited |
| Subdomain | Random | Custom |
| Concurrent Tunnels | 1 | Multiple |
| Bandwidth | Limited | Higher |
| Authentication | Basic | Advanced |

## Recommended Workflow

1. **Start of testing session:**
   ```bash
   npm run build              # Build assets first!
   valet share                # or ngrok http snippet-g11.test:80
   ```

2. **Share URL with team** via Slack/Discord/etc.

3. **During testing:**
   - All testers use the same Ngrok URL
   - All data saves to your local database
   - You can see real-time activity in logs

4. **End of session:**
   ```bash
   Ctrl+C  # Stop Ngrok
   npm run dev  # Resume development
   ```

## Debugging

### Check if Ngrok is detected

Add this to any controller temporarily:
```php
dd([
    'is_ngrok' => request()->attributes->get('is_ngrok'),
    'host' => request()->getHost(),
    'scheme' => request()->getScheme(),
    'url' => url('/'),
]);
```

### View Ngrok traffic

Ngrok provides a web interface at `http://127.0.0.1:4040` showing:
- All requests passing through the tunnel
- Request/response details
- Replay requests for debugging

## Related Files

| File | Purpose |
|------|---------|
| `app/Http/Middleware/TrustNgrokProxy.php` | Detects and handles Ngrok requests |
| `config/session.php` | Session cookie configuration |
| `config/sanctum.php` | Sanctum stateful domains |
| `config/filesystems.php` | Storage URL configuration |
| `bootstrap/app.php` | Middleware and CSRF exceptions |
| `.env.ngrok.example` | Example environment settings |
