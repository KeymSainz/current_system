<?php
/**
 * Fix&Go — Google Maps API Configuration
 * Returns the Maps API key securely to authenticated pages.
 *
 * GET /backend/maps-config.php → { success: true, key: "YOUR_API_KEY" }
 *
 * ── HOW TO FIX "This page can't load Google Maps correctly" ───────────────
 *
 * The error appears when the API key has HTTP referrer restrictions.
 * For LOCAL DEVELOPMENT (localhost), do this:
 *
 * 1. Go to: https://console.cloud.google.com/apis/credentials
 * 2. Click your API key
 * 3. Under "Application restrictions" → select "NONE"
 * 4. Under "API restrictions" → select "Don't restrict key"
 *    OR add: Maps JavaScript API + Directions API
 * 5. Click SAVE — wait 2-5 minutes for changes to take effect
 * 6. Hard refresh the page (Ctrl+Shift+R)
 *
 * ── REQUIRED APIs (enable all in Google Cloud Console) ───────────────────
 *      • Maps JavaScript API  ← renders the map
 *      • Directions API       ← road route (truck follows roads)
 *
 * ── BILLING ──────────────────────────────────────────────────────────────
 *      Google Maps requires a billing account even for free-tier usage.
 *      Enable billing at: https://console.cloud.google.com/billing
 *      (You get $200 free credit/month — more than enough for development)
 *
 * ─────────────────────────────────────────────────────────────────────────
 */

require_once __DIR__ . '/helpers.php';
startSecureSession();
header('Content-Type: application/json');
header('Cache-Control: no-store, no-cache');

// ── PASTE YOUR GOOGLE MAPS API KEY HERE ───────────────────────────────────
define('AIzaSyAi2az-pJFXj7ogUB0ZmLm4Vw7z6bgSG2c', 'AIzaSyAi2az-pJFXj7ogUB0ZmLm4Vw7z6bgSG2c');
// ──────────────────────────────────────────────────────────────────────────

// Only serve to logged-in users (prevents key scraping)
if (empty($_SESSION['user_id'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Login required.']);
    exit;
}

$key = GOOGLE_MAPS_API_KEY;

if (empty($key) || $key === 'AIzaSyAi2az-pJFXj7ogUB0ZmLm4Vw7z6bgSG2c') {
    echo json_encode([
        'success' => false,
        'message' => 'Google Maps API key not configured. See backend/maps-config.php for instructions.',
        'key'     => '',
    ]);
    exit;
}

echo json_encode([
    'success' => true,
    'key'     => $key,
]);
