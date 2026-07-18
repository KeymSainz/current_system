<!DOCTYPE html>
<!-- FG_BACKEND set first so all inline scripts can use it -->
<script>window.FG_BACKEND = 'api/session/user';</script>

<html lang="en" data-theme="dark">

<head>

  <meta charset="UTF-8" />

  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
  <meta http-equiv="Pragma" content="no-cache" />
  <meta http-equiv="Expires" content="0" />
  <!-- FG_BACKEND must be defined FIRST before any inline scripts use it -->
  <script>window.FG_BACKEND = 'api/session/user';</script>
  <!-- Force SW unregister + cache clear on every load -->
  <script>
    if ('serviceWorker' in navigator) {
      navigator.serviceWorker.getRegistrations().then(function(regs) {
        regs.forEach(function(reg) { reg.unregister(); });
      });
      caches.keys().then(function(keys) {
        keys.forEach(function(key) { caches.delete(key); });
      });
    }
  </script>
  <!-- PWA -->
  <meta name="theme-color" content="#e6a800">
  <meta name="mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
  <meta name="apple-mobile-web-app-title" content="Fix&amp;Go">
  <link rel="manifest" href="fixandgo/manifest.json">
  <link rel="apple-touch-icon" href="fixandgo/assets/images/icons/icon-192.png">
  <link rel="stylesheet" href="fixandgo/assets/css/mobile.css">
  <meta name="description" content="Fix&amp;Go — Book phone repairs, order replacement parts, and track your device. Fast, trusted, and fully online.">
  <meta name="keywords" content="phone repair, mobile repair booking, Fix and Go, phone parts, technician">
  <meta name="author" content="Fix&amp;Go">

  <title>Fix&amp;Go &ndash; Your Trusted Phone Repair &amp; Shop</title>

  <!-- Bootstrap 5 CDN -->

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />

  <!-- Font Awesome 6 CDN -->

  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet" />

  <style>

    /* =====================

       CSS VARIABLES / THEME

    ===================== */

    :root {

      --orange: #e6a800;

      --orange-hover: #c98f00;

      --bg: #0d0d0d;

      --bg-secondary: #161616;

      --bg-card: #1a1a1a;

      --text-primary: #ffffff;

      --text-secondary: #aaaaaa;

      --border-color: #2a2a2a;

      --navbar-bg: rgba(13, 13, 13, 0.95);

      --section-bg: #111111;

    }

    [data-theme="light"] {

      --bg: #f5f5f5;

      --bg-secondary: #ebebeb;

      --bg-card: #ffffff;

      --text-primary: #111111;

      --text-secondary: #555555;

      --border-color: #dddddd;

      --navbar-bg: rgba(245, 245, 245, 0.97);

      --section-bg: #f0f0f0;

    }

    /* =====================

       GLOBAL

    ===================== */

    *, *::before, *::after {

      box-sizing: border-box;

      margin: 0;

      padding: 0;

    }

    html {

      scroll-behavior: smooth;

      /* Compensate for fixed navbar (≈60px) + ticker bar (≈36px) on desktop.
         On mobile the navbar is ≈54px. Using 70px as safe value. */
      scroll-padding-top: 70px;

    }

    /* Anchor targets need extra space below fixed navbar */
    section[id], div[id="home"], div[id="shop"], div[id="services"],
    div[id="technicians"], div[id="about"] {
      scroll-margin-top: 70px;
    }

    @media (max-width: 991px) {
      html { scroll-padding-top: 60px; }
      section[id], div[id="home"], div[id="shop"], div[id="services"],
      div[id="technicians"], div[id="about"] {
        scroll-margin-top: 60px;
      }
    }

    body {

      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;

      background-color: var(--bg);

      color: var(--text-primary);

      transition: background-color 0.3s ease, color 0.3s ease;

    }

    a {

      text-decoration: none;

      color: inherit;

    }

    /* =====================

       NAVBAR

    ===================== */

    .navbar {

      background-color: var(--navbar-bg);

      backdrop-filter: blur(10px);

      -webkit-backdrop-filter: blur(10px);

      border-bottom: 1px solid var(--border-color);

      padding: 0.10rem 0;

      position: fixed;

      top: 0;

      left: 0;

      width: 100%;

      z-index: 1000;

      transition: background-color 0.3s ease;

    }

    .navbar-brand {

      display: flex;

      align-items: center;

      gap: 0.5rem;

      font-size: 1.4rem;

      font-weight: 700;

      color: var(--text-primary) !important;

      letter-spacing: 0.5px;

    }

    .navbar-brand .brand-icon {

      color: var(--orange);

      font-size: 1.5rem;

    }

    .brand-logo-img {

      height: 52px;

      width: auto;

      object-fit: contain;

    }

    .navbar-brand span {

      color: var(--orange);

    }

    .nav-link {

      color: var(--text-secondary) !important;

      font-weight: 500;

      font-size: 0.95rem;

      padding: 0.4rem 0.85rem !important;

      border-radius: 6px;

      transition: color 0.2s ease, background-color 0.2s ease;

    }

    .nav-link:hover,

    .nav-link.active {

      color: var(--text-primary) !important;

      background-color: rgba(255, 107, 43, 0.1);

    }

    .theme-toggle-btn {

      background: none;

      border: 1px solid var(--border-color);

      color: var(--text-primary);

      width: 38px;

      height: 38px;

      border-radius: 50%;

      display: flex;

      align-items: center;

      justify-content: center;

      cursor: pointer;

      font-size: 1rem;

      transition: border-color 0.2s ease, color 0.2s ease, background-color 0.2s ease;

    }

    .theme-toggle-btn:hover {

      border-color: var(--orange);

      color: var(--orange);

      background-color: rgba(255, 107, 43, 0.08);

    }

    .btn-navbar-login {

      background-color: var(--orange);

      color: #ffffff !important;

      border: none;

      padding: 0.45rem 1.2rem;

      border-radius: 50px;

      font-weight: 600;

      font-size: 0.9rem;

      transition: background-color 0.2s ease, transform 0.15s ease;

    }

    .btn-navbar-login:hover {

      background-color: var(--orange-hover);

      transform: translateY(-1px);

    }

    .navbar-toggler {

      border-color: var(--border-color);

      color: var(--text-primary);

    }

    .navbar-toggler-icon {

      filter: invert(1);

    }

    [data-theme="light"] .navbar-toggler-icon {

      filter: invert(0);

    }

    /* =====================

       HERO SECTION — Marketplace Style

    ===================== */

    .hero-section {

      position: relative;

      width: 100%;

      min-height: 100vh;

      background: linear-gradient(135deg, #0a0a0a 0%, #111111 40%, #1a1200 100%);

      display: flex;

      flex-direction: column;

      align-items: stretch;

      overflow: hidden;

      isolation: isolate;

      z-index: 0;

    }

    /* Animated background grid */

    .hero-section::before {

      content: '';

      position: absolute;

      inset: 0;

      z-index: -1;

      background-image:

        linear-gradient(rgba(230,168,0,0.04) 1px, transparent 1px),

        linear-gradient(90deg, rgba(230,168,0,0.04) 1px, transparent 1px);

      background-size: 60px 60px;

      will-change: auto;

      animation: gridMove 20s linear infinite;

    }

    @keyframes gridMove {

      0% { background-position: 0 0; }

      100% { background-position: 0 60px; }

    }

    /* Glow orbs */

    .hero-section::after {

      content: '';

      position: absolute;

      top: -20%;

      right: -10%;

      width: 600px;

      height: 600px;

      z-index: -1;

      background: radial-gradient(circle, rgba(230,168,0,0.12) 0%, transparent 70%);

      pointer-events: none;

    }

    .hero-overlay {

      display: none; /* replaced by CSS background */

    }

    .hero-content {

      position: relative;

      z-index: 2;

      max-width: 680px;

    }

    .hero-label {

      display: inline-flex;

      align-items: center;

      gap: 0.5rem;

      font-size: 0.78rem;

      font-weight: 700;

      letter-spacing: 2px;

      text-transform: uppercase;

      color: var(--orange);

      background-color: rgba(230,168,0,0.1);

      border: 1px solid rgba(230,168,0,0.3);

      padding: 0.35rem 0.9rem;

      border-radius: 50px;

      margin-bottom: 1.4rem;

    }

    .hero-headline {

      font-size: clamp(2.2rem, 5vw, 3.6rem);

      font-weight: 900;

      line-height: 1.15;

      color: #ffffff;

      margin-bottom: 1.4rem;

      letter-spacing: -0.5px;

    }

    .hero-headline span {

      color: var(--orange);

      position: relative;

    }

    .hero-subtext {

      font-size: 1.05rem;

      color: rgba(255, 255, 255, 0.7);

      margin-bottom: 2rem;

      line-height: 1.75;

    }

    .hero-buttons {

      display: flex;

      gap: 1rem;

      flex-wrap: wrap;

      margin-bottom: 2.5rem;

    }

    .btn-hero-login {

      background: linear-gradient(135deg, var(--orange), var(--orange-hover));

      color: #ffffff;

      border: none;

      padding: 0.8rem 2.2rem;

      border-radius: 50px;

      font-weight: 700;

      font-size: 1rem;

      transition: all 0.25s ease;

      box-shadow: 0 4px 24px rgba(230,168,0,0.4);

      display: inline-flex;

      align-items: center;

      gap: 0.5rem;

    }

    .btn-hero-login:hover {

      transform: translateY(-3px);

      box-shadow: 0 8px 32px rgba(230,168,0,0.55);

      color: #ffffff;

    }

    .btn-hero-register {

      background-color: transparent;

      color: #ffffff;

      border: 2px solid rgba(255, 255, 255, 0.5);

      padding: 0.8rem 2.2rem;

      border-radius: 50px;

      font-weight: 700;

      font-size: 1rem;

      transition: all 0.25s ease;

      display: inline-flex;

      align-items: center;

      gap: 0.5rem;

    }

    .btn-hero-register:hover {

      border-color: #ffffff;

      background-color: rgba(255, 255, 255, 0.1);

      transform: translateY(-3px);

      color: #ffffff;

    }

    /* Hero stats row */

    .hero-stats {

      display: flex;

      gap: 2rem;

      flex-wrap: wrap;

    }

    .hero-stat {

      display: flex;

      flex-direction: column;

    }

    .hero-stat-val {

      font-size: 1.6rem;

      font-weight: 900;

      color: var(--orange);

      line-height: 1;

    }

    .hero-stat-lbl {

      font-size: 0.72rem;

      color: rgba(255,255,255,0.5);

      font-weight: 600;

      text-transform: uppercase;

      letter-spacing: 0.5px;

      margin-top: 0.2rem;

    }

    /* Hero right side — floating cards */

    .hero-right {

      position: absolute;

      right: 0;

      top: 50%;

      transform: translateY(-50%);

      width: 42%;

      display: flex;

      flex-direction: column;

      gap: 1rem;

      padding-right: 2rem;

      z-index: 2;

    }

    .hero-float-card {

      background: rgba(255,255,255,0.04);

      border: 1px solid rgba(230,168,0,0.2);

      border-radius: 16px;

      padding: 1.1rem 1.4rem;

      backdrop-filter: blur(12px);

      display: flex;

      align-items: center;

      gap: 1rem;

      transition: transform 0.3s ease, border-color 0.3s ease;

    }

    .hero-float-card:hover {

      transform: translateX(-6px);

      border-color: rgba(230,168,0,0.5);

    }

    .hero-float-card:nth-child(2) { margin-left: 2rem; }

    .hero-float-card:nth-child(3) { margin-left: 1rem; }

    .hero-float-icon {

      width: 48px;

      height: 48px;

      border-radius: 12px;

      background: linear-gradient(135deg, rgba(230,168,0,0.2), rgba(230,168,0,0.05));

      border: 1px solid rgba(230,168,0,0.3);

      display: flex;

      align-items: center;

      justify-content: center;

      font-size: 1.3rem;

      color: var(--orange);

      flex-shrink: 0;

    }

    .hero-float-title {

      font-size: 0.88rem;

      font-weight: 700;

      color: #fff;

      margin-bottom: 0.15rem;

    }

    .hero-float-sub {

      font-size: 0.72rem;

      color: rgba(255,255,255,0.5);

    }

    @media (max-width: 991px) {

      .hero-right { display: none; }

      .hero-content { max-width: 100%; text-align: center; }

      .hero-buttons { justify-content: center; }

      .hero-stats { justify-content: center; }

    }

    /* Hero animations */
    @keyframes heroPulse {
      0%, 100% { opacity: 0.4; }
      50%       { opacity: 0.9; }
    }
    @keyframes heroFloat {
      0%, 100% { transform: translateY(0) translateZ(0); }
      50%       { transform: translateY(-6px) translateZ(0); }
    }
    @keyframes tickerScroll {
      0%   { transform: translateX(0) translateZ(0); }
      100% { transform: translateX(-50%) translateZ(0); }
    }

    /* ── Trust ticker bar ─────────────────────── */
    .trust-ticker {
      background: linear-gradient(90deg, var(--orange), #c98f00);
      padding: 0.65rem 0;
      overflow: hidden;
      position: relative;
      z-index: 1;
      isolation: isolate;
    }
    .trust-ticker::before,
    .trust-ticker::after {
      content: '';
      position: absolute;
      top: 0; bottom: 0;
      width: 60px;
      z-index: 2;
      pointer-events: none;
    }
    .trust-ticker::before { left: 0;  background: linear-gradient(90deg, var(--orange), transparent); }
    .trust-ticker::after  { right: 0; background: linear-gradient(-90deg, #c98f00, transparent); }
    .ticker-track {
      display: flex;
      gap: 0;
      white-space: nowrap;
      animation: tickerScroll 24s linear infinite;
      will-change: transform;
    }
    .ticker-item {
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
      padding: 0 2.5rem;
      font-size: 0.78rem;
      font-weight: 700;
      color: #000;
      letter-spacing: 0.3px;
    }
    .ticker-dot {
      width: 5px; height: 5px;
      border-radius: 50%;
      background: rgba(0,0,0,0.35);
      flex-shrink: 0;
    }

    /* ── How it Works section ─────────────────── */
    .how-section {
      background: var(--bg-secondary);
      padding: 80px 0;
    }
    .step-card {
      text-align: center;
      padding: 2rem 1.5rem;
      position: relative;
    }
    .step-number {
      width: 56px; height: 56px;
      border-radius: 50%;
      background: linear-gradient(135deg, var(--orange), var(--orange-hover));
      color: #000;
      font-size: 1.3rem;
      font-weight: 900;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 1.25rem;
      box-shadow: 0 4px 20px rgba(230,168,0,0.35);
    }
    .step-icon {
      font-size: 2rem;
      margin-bottom: 0.85rem;
      display: block;
    }
    .step-card h5 {
      font-size: 1rem;
      font-weight: 800;
      color: var(--text-primary);
      margin-bottom: 0.5rem;
    }
    .step-card p {
      font-size: 0.85rem;
      color: var(--text-secondary);
      line-height: 1.7;
    }
    .step-connector {
      position: absolute;
      top: 3.1rem;
      right: -18%;
      width: 36%;
      height: 2px;
      background: linear-gradient(90deg, var(--orange), transparent);
      opacity: 0.3;
    }

    /* ── Promo / CTA banner ───────────────────── */
    .promo-banner {
      background: linear-gradient(135deg, #0a0a0a 0%, #1a0a00 50%, #0a0a0a 100%);
      border-top: 1px solid rgba(230,168,0,0.12);
      border-bottom: 1px solid rgba(230,168,0,0.12);
      padding: 64px 0;
      position: relative;
      overflow: hidden;
    }
    .promo-banner::before {
      content: '';
      position: absolute;
      top: -80px; left: 50%; transform: translateX(-50%);
      width: 600px; height: 300px;
      border-radius: 50%;
      background: radial-gradient(ellipse, rgba(230,168,0,0.07), transparent 70%);
      pointer-events: none;
    }

    /* ── Testimonials strip ───────────────────── */
    .testimonials-section {
      background: var(--bg);
      padding: 80px 0;
    }
    .testimonial-card {
      background: var(--bg-card);
      border: 1px solid var(--border-color);
      border-radius: 16px;
      padding: 1.75rem;
      height: 100%;
      transition: transform 0.25s, border-color 0.25s;
    }
    .testimonial-card:hover {
      transform: translateY(-4px);
      border-color: rgba(230,168,0,0.3);
    }
    .testimonial-stars { color: var(--orange); font-size: 0.85rem; margin-bottom: 0.85rem; }
    .testimonial-text { font-size: 0.9rem; color: var(--text-secondary); line-height: 1.75; margin-bottom: 1.25rem; font-style: italic; }
    .testimonial-author { display: flex; align-items: center; gap: 0.75rem; }
    .t-avatar { width: 40px; height: 40px; border-radius: 50%; background: linear-gradient(135deg,rgba(230,168,0,0.2),rgba(230,168,0,0.05)); border: 2px solid rgba(230,168,0,0.3); display: flex; align-items: center; justify-content: center; font-size: 1rem; font-weight: 800; color: var(--orange); flex-shrink: 0; }
    .t-name { font-size: 0.85rem; font-weight: 700; color: var(--text-primary); }
    .t-sub  { font-size: 0.72rem; color: var(--text-secondary); }

    /* =====================

       INFO SECTION

    ===================== */

    .info-section {

      background-color: var(--section-bg);

      padding: 90px 0;

      transition: background-color 0.3s ease;

    }

    .section-label {

      display: inline-block;

      font-size: 0.75rem;

      font-weight: 700;

      letter-spacing: 2.5px;

      text-transform: uppercase;

      color: var(--orange);

      margin-bottom: 1rem;

    }

    .section-heading {

      font-size: clamp(1.6rem, 3.5vw, 2.4rem);

      font-weight: 800;

      color: var(--text-primary);

      margin-bottom: 1.2rem;

      line-height: 1.25;

    }

    .section-paragraph {

      font-size: 1rem;

      color: var(--text-secondary);

      line-height: 1.8;

      margin-bottom: 2rem;

    }

    .video-wrapper {

      position: relative;

      width: 100%;

      padding-bottom: 56.25%;

      border-radius: 14px;

      overflow: hidden;

      box-shadow: 0 8px 40px rgba(0, 0, 0, 0.4);

    }

    .video-wrapper iframe {

      position: absolute;

      top: 0;

      left: 0;

      width: 100%;

      height: 100%;

      border: none;

    }

    /* =====================

       SERVICES SECTION

    ===================== */

    .services-section {

      background-color: var(--bg);

      padding: 90px 0;

      transition: background-color 0.3s ease;

    }

    .services-section .section-heading,

    .services-section .section-label {

      text-align: center;

    }

    .services-section .section-heading {

      margin-bottom: 0.5rem;

    }

    .services-subtitle {

      text-align: center;

      color: var(--text-secondary);

      font-size: 1rem;

      margin-bottom: 3rem;

    }

    .service-card {

      background-color: var(--bg-card);

      border: 1px solid var(--border-color);

      border-radius: 16px;

      padding: 2.2rem 1.8rem;

      text-align: center;

      transition: transform 0.25s ease, box-shadow 0.25s ease, border-color 0.25s ease;

      height: 100%;

    }

    .service-card:hover {

      transform: translateY(-6px);

      box-shadow: 0 12px 40px rgba(255, 107, 43, 0.15);

      border-color: var(--orange);

    }

    .service-icon-wrap {

      width: 72px;

      height: 72px;

      background: linear-gradient(135deg, rgba(255, 107, 43, 0.15), rgba(255, 107, 43, 0.05));

      border: 1px solid rgba(255, 107, 43, 0.25);

      border-radius: 50%;

      display: flex;

      align-items: center;

      justify-content: center;

      margin: 0 auto 1.4rem;

      font-size: 1.7rem;

      color: var(--orange);

      transition: background 0.25s ease;

    }

    .service-card:hover .service-icon-wrap {

      background: linear-gradient(135deg, rgba(255, 107, 43, 0.25), rgba(255, 107, 43, 0.1));

    }

    .service-card h5 {

      font-size: 1.15rem;

      font-weight: 700;

      color: var(--text-primary);

      margin-bottom: 0.75rem;

    }

    .service-card p {

      font-size: 0.92rem;

      color: var(--text-secondary);

      line-height: 1.7;

      margin-bottom: 1.4rem;

    }

    .service-badge {

      display: inline-block;

      font-size: 0.75rem;

      font-weight: 600;

      color: var(--orange);

      background-color: rgba(255, 107, 43, 0.1);

      border: 1px solid rgba(255, 107, 43, 0.25);

      padding: 0.3rem 0.85rem;

      border-radius: 50px;

    }

    /* =====================

       SHOP SECTION

    ===================== */

    .shop-section {

      background-color: var(--section-bg);

      padding: 90px 0;

      transition: background-color 0.3s ease;

    }

    .shop-section .section-heading,

    .shop-section .section-label {

      text-align: center;

    }

    .shop-section .section-heading { margin-bottom: 0.5rem; }

    .shop-subtitle {

      text-align: center;

      color: var(--text-secondary);

      font-size: 1rem;

      margin-bottom: 2.5rem;

    }

    /* Shop info banner */

    .shop-banner {

      background: var(--bg-card);

      border: 1px solid var(--border-color);

      border-radius: 16px;

      padding: 1.5rem 2rem;

      display: flex;

      align-items: center;

      gap: 1.25rem;

      margin-bottom: 2rem;

      flex-wrap: wrap;

    }

    .shop-banner-icon {

      width: 56px; height: 56px;

      border-radius: 14px;

      background: linear-gradient(135deg, rgba(230,168,0,0.2), rgba(230,168,0,0.07));

      border: 1px solid rgba(230,168,0,0.3);

      display: flex; align-items: center; justify-content: center;

      font-size: 1.6rem; color: var(--orange); flex-shrink: 0;

    }

    .shop-banner-info h6 {

      font-size: 1.05rem; font-weight: 800;

      color: var(--text-primary); margin: 0 0 0.2rem;

    }

    .shop-banner-info p {

      font-size: 0.82rem; color: var(--text-secondary); margin: 0;

    }

    .shop-banner-meta {

      margin-left: auto;

      display: flex; gap: 1.25rem; flex-wrap: wrap;

    }

    .shop-meta-item {

      display: flex; align-items: center; gap: 0.4rem;

      font-size: 0.82rem; color: var(--text-secondary);

    }

    .shop-meta-item i { color: var(--orange); }

    /* Category filter tabs */

    .category-tabs {

      display: flex; gap: 0.5rem; flex-wrap: wrap;

      margin-bottom: 1.75rem;

    }

    .cat-tab {

      padding: 0.4rem 1rem;

      border-radius: 50px;

      border: 1.5px solid var(--border-color);

      background: transparent;

      color: var(--text-secondary);

      font-size: 0.8rem; font-weight: 600;

      cursor: pointer; transition: all 0.2s;

    }

    .cat-tab:hover, .cat-tab.active {

      border-color: var(--orange);

      color: var(--orange);

      background: rgba(230,168,0,0.08);

    }

    /* Product grid */

    .product-grid {

      display: grid;

      grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));

      gap: 1.25rem;

    }

    .product-card {

      background: var(--bg-card);

      border: 1px solid var(--border-color);

      border-radius: 14px;

      overflow: hidden;

      transition: transform 0.25s ease, box-shadow 0.25s ease, border-color 0.25s ease;

      display: flex; flex-direction: column;

    }

    .product-card:hover {

      transform: translateY(-5px);

      box-shadow: 0 12px 36px rgba(230,168,0,0.14);

      border-color: var(--orange);

    }

    .product-card-img {

      width: 100%; aspect-ratio: 1/1;

      object-fit: cover;

      background: var(--bg);

    }

    .product-card-img-placeholder {

      width: 100%; aspect-ratio: 1/1;

      background: linear-gradient(135deg, #1a1a1a, #141414);

      display: flex; flex-direction: column; align-items: center; justify-content: center;

      gap: 0.5rem;

      position: relative; overflow: hidden;

    }

    .product-card-img-placeholder::before {

      content: '';

      position: absolute; inset: 0;

      background: repeating-linear-gradient(

        45deg,

        transparent, transparent 12px,

        rgba(230,168,0,0.03) 12px, rgba(230,168,0,0.03) 13px

      );

    }

    .product-card-img-placeholder i {

      font-size: 2.2rem;

      color: rgba(230,168,0,0.35);

      position: relative; z-index: 1;

    }

    .product-card-img-placeholder::after {

      content: 'No Image';

      font-size: 0.6rem;

      font-weight: 700;

      letter-spacing: 1px;

      text-transform: uppercase;

      color: rgba(255,255,255,0.15);

      position: relative; z-index: 1;

    }

    .product-card-body {

      padding: 0.9rem 1rem 1rem;

      flex: 1; display: flex; flex-direction: column;

    }

    .product-cat-badge {

      display: inline-block;

      font-size: 0.68rem; font-weight: 700;

      color: var(--orange);

      background: rgba(230,168,0,0.1);

      border: 1px solid rgba(230,168,0,0.2);

      padding: 0.15rem 0.55rem;

      border-radius: 50px;

      margin-bottom: 0.45rem;

    }

    .product-card-title {

      font-size: 0.82rem; font-weight: 700;

      color: var(--text-primary);

      line-height: 1.35;

      margin-bottom: 0.3rem;

      flex: 1;

    }

    .product-card-brand {

      font-size: 0.75rem; color: var(--text-secondary);

      margin-bottom: 0.6rem;

    }

    .product-card-footer {

      display: flex; align-items: center; justify-content: space-between;

      margin-top: auto;

    }

    .product-price {

      font-size: 1rem; font-weight: 800; color: var(--orange);

    }

    .product-qty {

      font-size: 0.72rem; color: var(--text-secondary);

      background: var(--bg);

      border: 1px solid var(--border-color);

      padding: 0.15rem 0.5rem; border-radius: 6px;

    }

    /* Order Now CTA on product card */

    .product-card-cta {

      margin-top: 0.75rem;

      padding: 0.45rem 1rem;

      border-radius: 8px;

      background: rgba(230,168,0,0.1);

      border: 1.5px solid rgba(230,168,0,0.3);

      color: var(--orange);

      font-size: 0.78rem; font-weight: 700;

      display: flex; align-items: center; justify-content: center; gap: 0.4rem;

      transition: all 0.2s;

    }

    .product-card:hover .product-card-cta {

      background: var(--orange);

      border-color: var(--orange);

      color: #fff;

    }

    /* Shop empty / loading states */

    .shop-empty {

      text-align: center; padding: 3.5rem 1rem;

      color: var(--text-secondary);

    }

    .shop-empty i { font-size: 2.8rem; display: block; margin-bottom: 0.75rem; opacity: 0.4; }

    .shop-empty p { font-size: 0.9rem; margin: 0; }

    .shop-loading {

      text-align: center; padding: 3rem 1rem;

      color: var(--text-secondary); font-size: 0.9rem;

    }

    .shop-loading .spinner {

      width: 32px; height: 32px;

      border: 3px solid var(--border-color);

      border-top-color: var(--orange);

      border-radius: 50%;

      animation: spin 0.7s linear infinite;

      margin: 0 auto 0.75rem;

    }

    @keyframes spin { to { transform: rotate(360deg); } }

    /* Search bar */

    .shop-search {

      padding: 0.5rem 1rem;

      border-radius: 50px;

      border: 1.5px solid var(--border-color);

      background: var(--bg);

      color: var(--text-primary);

      font-size: 0.85rem;

      outline: none;

      transition: border-color 0.2s;

      min-width: 220px;

    }

    .shop-search:focus { border-color: var(--orange); }

    .shop-search::placeholder { color: var(--text-secondary); }

    /* =====================

       TECHNICIANS SECTION

    ===================== */

    .tech-section {

      background-color: var(--bg);

      padding: 90px 0;

      transition: background-color 0.3s ease;

    }

    .tech-section .section-heading,

    .tech-section .section-label { text-align: center; }

    .tech-section .section-heading { margin-bottom: 0.5rem; }

    .tech-subtitle {

      text-align: center;

      color: var(--text-secondary);

      font-size: 1rem;

      margin-bottom: 0.75rem;

    }

    /* Counter badge */

    .tech-count-badge {

      display: inline-flex; align-items: center; gap: 0.4rem;

      background: rgba(230,168,0,0.1);

      border: 1px solid rgba(230,168,0,0.25);

      color: var(--orange);

      font-size: 0.8rem; font-weight: 700;

      padding: 0.3rem 0.9rem; border-radius: 50px;

      margin-bottom: 2.5rem;

    }

    /* Technician card grid */

    .tech-grid {

      display: grid;

      grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));

      gap: 1.5rem;

    }

    .tech-card {

      background: var(--bg-card);

      border: 1px solid var(--border-color);

      border-radius: 18px;

      padding: 2rem 1.5rem 1.5rem;

      text-align: center;

      transition: transform 0.25s ease, box-shadow 0.25s ease, border-color 0.25s ease;

      position: relative;

      overflow: hidden;

      display: flex;

      flex-direction: column;

    }

    .tech-card::before {

      content: '';

      position: absolute; top: 0; left: 0; right: 0;

      height: 4px;

      background: linear-gradient(90deg, var(--orange), var(--orange-hover));

      opacity: 0; transition: opacity 0.25s;

    }

    .tech-card:hover {

      transform: translateY(-6px);

      box-shadow: 0 14px 40px rgba(230,168,0,0.14);

      border-color: var(--orange);

    }

    .tech-card:hover::before { opacity: 1; }

    /* Avatar */

    .tech-avatar {

      width: 80px; height: 80px;

      border-radius: 50%;

      margin: 0 auto 1rem;

      border: 3px solid var(--border-color);

      object-fit: cover;

      transition: border-color 0.25s;

      display: block;

    }

    .tech-card:hover .tech-avatar { border-color: var(--orange); }

    .tech-avatar-placeholder {

      width: 80px; height: 80px;

      border-radius: 50%;

      margin: 0 auto 1rem;

      border: 3px solid var(--border-color);

      background: linear-gradient(135deg, rgba(230,168,0,0.18), rgba(230,168,0,0.06));

      display: flex; align-items: center; justify-content: center;

      font-size: 2rem; color: var(--orange);

      transition: border-color 0.25s;

    }

    .tech-card:hover .tech-avatar-placeholder { border-color: var(--orange); }

    .tech-name {

      font-size: 1rem; font-weight: 800;

      color: var(--text-primary); margin-bottom: 0.25rem;

    }

    .tech-role-badge {

      display: inline-block;

      font-size: 0.7rem; font-weight: 700;

      color: var(--orange);

      background: rgba(230,168,0,0.1);

      border: 1px solid rgba(230,168,0,0.2);

      padding: 0.15rem 0.6rem; border-radius: 50px;

      margin-bottom: 0.75rem;

    }

    .tech-shop {

      font-size: 0.78rem; color: var(--text-secondary);

      display: flex; align-items: center; justify-content: center; gap: 0.3rem;

      margin-bottom: 1rem;

    }

    .tech-shop i { color: var(--orange); font-size: 0.72rem; }

    /* Availability dot */

    .tech-avail-wrap {

      position: absolute; top: 1rem; right: 1rem;

    }

    .tech-avail-dot {

      display: inline-block;

      width: 10px; height: 10px;

      border-radius: 50%;

      border: 2px solid var(--bg-card);

      box-shadow: 0 0 0 1px rgba(0,0,0,0.15);

    }

    /* Rating row */

    .tech-rating {

      display: flex; align-items: center; justify-content: center; gap: 2px;

      margin-bottom: 0.6rem;

    }

    /* Specialization pills */

    .tech-specs {

      display: flex; flex-wrap: wrap; justify-content: center; gap: 0.35rem;

      margin-bottom: 0.65rem;

    }

    .tech-spec-pill {

      font-size: 0.68rem; font-weight: 600;

      color: var(--text-secondary);

      background: var(--bg);

      border: 1px solid var(--border-color);

      padding: 0.15rem 0.55rem; border-radius: 50px;

    }

    /* Bio */

    .tech-bio {

      font-size: 0.76rem; color: var(--text-secondary);

      line-height: 1.5; margin-bottom: 0.65rem;

      display: -webkit-box; -webkit-line-clamp: 2; line-clamp: 2;

      -webkit-box-orient: vertical; overflow: hidden;

    }

    /* Stats row */

    .tech-stats {

      display: flex; justify-content: center; gap: 1.25rem;

      border-top: 1px solid var(--border-color);

      padding-top: 1rem; margin-top: 0.25rem;

    }

    .tech-stat { text-align: center; }

    .tech-stat-val {

      font-size: 1.1rem; font-weight: 800; color: var(--text-primary);

      line-height: 1;

    }

    .tech-stat-lbl {

      font-size: 0.65rem; color: var(--text-secondary);

      font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;

      margin-top: 0.2rem;

    }

    /* Book Now CTA on card */

    .tech-card-cta {

      margin-top: auto;

      padding: 0.5rem 1rem;

      border-radius: 8px;

      background: rgba(230,168,0,0.1);

      border: 1.5px solid rgba(230,168,0,0.3);

      color: var(--orange);

      font-size: 0.8rem; font-weight: 700;

      display: flex; align-items: center; justify-content: center; gap: 0.4rem;

      transition: all 0.2s;

    }

    .tech-card:hover .tech-card-cta {

      background: var(--orange);

      border-color: var(--orange);

      color: #fff;

    }

    /* Loading / empty */

    .tech-loading {

      text-align: center; padding: 3rem 1rem;

      color: var(--text-secondary); font-size: 0.9rem;

    }

    .tech-loading .spinner {

      width: 32px; height: 32px;

      border: 3px solid var(--border-color);

      border-top-color: var(--orange);

      border-radius: 50%;

      animation: spin 0.7s linear infinite;

      margin: 0 auto 0.75rem;

    }

    .tech-empty {

      text-align: center; padding: 3.5rem 1rem;

      color: var(--text-secondary);

    }

    .tech-empty i { font-size: 2.8rem; display: block; margin-bottom: 0.75rem; opacity: 0.4; }

    .tech-empty p { font-size: 0.9rem; margin: 0; }

    /* =====================

       FOOTER

    ===================== */

    .footer {

      background-color: var(--bg-secondary);

      border-top: 1px solid var(--border-color);

      padding: 2rem 0;

      text-align: center;

      transition: background-color 0.3s ease;

    }

    .footer p {

      font-size: 0.9rem;

      margin: 0;

    }

    .footer span {

      color: var(--orange);

      font-weight: 600;

    }

    /* =====================

       RESPONSIVE TWEAKS

    ===================== */

    @media (max-width: 768px) {

      .hero-content {

        text-align: center;

      }

      .hero-buttons {

        justify-content: center;

      }

      .info-section .row {

        gap: 2.5rem;

      }

    }

    /* =====================

       NAVBAR SEARCH

    ===================== */

    .navbar-search-wrap {

      position: relative;

    }

    .navbar-search-box {

      display: flex; align-items: center;

      background: var(--bg-card);

      border: 1.5px solid var(--border-color);

      border-radius: 50px;

      padding: 0.3rem 0.75rem;

      gap: 0.5rem;

      transition: border-color 0.2s, box-shadow 0.2s;

      min-width: 240px;

    }

    .navbar-search-box:focus-within {

      border-color: var(--orange);

      box-shadow: 0 0 0 3px rgba(230,168,0,0.15);

    }

    .navbar-search-icon {

      color: var(--text-secondary);

      font-size: 0.85rem;

      flex-shrink: 0;

    }

    .navbar-search-input {

      background: transparent;

      border: none;

      outline: none;

      color: var(--text-primary);

      font-size: 0.85rem;

      width: 100%;

      font-family: inherit;

    }

    .navbar-search-input::placeholder { color: var(--text-secondary); }

    .navbar-search-clear {

      background: none; border: none; cursor: pointer;

      color: var(--text-secondary); font-size: 0.8rem;

      padding: 0; line-height: 1; flex-shrink: 0;

      transition: color 0.2s;

    }

    .navbar-search-clear:hover { color: var(--text-primary); }

    /* =====================

       NOTIFICATION BADGE & DROPDOWN

    ===================== */

    .notification-badge {

      position: absolute;

      top: -4px;

      right: -8px;

      background: #dc3545;

      color: #fff;

      font-size: 0.65rem;

      font-weight: 700;

      padding: 0.15rem 0.4rem;

      border-radius: 50px;

      min-width: 18px;

      text-align: center;

      line-height: 1;

    }

    .notification-dropdown {

      position: fixed;

      top: 68px;

      right: 20px;

      width: 380px;

      max-width: calc(100vw - 40px);

      background: var(--bg-card);

      border: 1px solid var(--border-color);

      border-radius: 14px;

      box-shadow: 0 12px 40px rgba(0,0,0,0.25);

      z-index: 2000;

      animation: dropIn 0.18s ease;

      max-height: 500px;

      display: flex;

      flex-direction: column;

    }

    @media (max-width: 768px) {
      .notification-dropdown {
        position: fixed;
        top: 60px;
        left: 8px;
        right: 8px;
        width: auto;
        max-width: none;
        max-height: 70vh;
        border-radius: 14px;
        z-index: 2001;
      }
    }

    .notification-header {

      padding: 1rem 1.25rem;

      border-bottom: 1px solid var(--border-color);

      display: flex;

      align-items: center;

      justify-content: space-between;

    }

    .notification-header h6 {

      font-size: 1rem;

      font-weight: 700;

      color: var(--text-primary);

      margin: 0;

    }

    .notification-header-actions {

      display: flex;

      gap: 0.5rem;

    }

    .notification-header-btn {

      background: none;

      border: none;

      color: var(--orange);

      font-size: 0.75rem;

      font-weight: 600;

      cursor: pointer;

      padding: 0.25rem 0.5rem;

      border-radius: 4px;

      transition: background 0.2s;

    }

    .notification-header-btn:hover {

      background: rgba(230,168,0,0.1);

    }

    .notification-list {

      overflow-y: auto;

      flex: 1;

    }

    .notification-item {

      padding: 1rem 1.25rem;

      border-bottom: 1px solid var(--border-color);

      cursor: pointer;

      transition: background 0.15s;

      display: flex;

      gap: 0.75rem;

      position: relative;

    }

    .notification-item:hover {

      background: rgba(230,168,0,0.05);

    }

    .notification-item.unread {

      background: rgba(230,168,0,0.08);

    }

    .notification-item.unread::before {

      content: '';

      position: absolute;

      left: 0;

      top: 50%;

      transform: translateY(-50%);

      width: 4px;

      height: 60%;

      background: var(--orange);

      border-radius: 0 4px 4px 0;

    }

    .notification-icon {

      width: 40px;

      height: 40px;

      border-radius: 50%;

      background: linear-gradient(135deg, rgba(230,168,0,0.2), rgba(230,168,0,0.06));

      border: 2px solid rgba(230,168,0,0.25);

      display: flex;

      align-items: center;

      justify-content: center;

      font-size: 1rem;

      color: var(--orange);

      flex-shrink: 0;

    }

    .notification-content {

      flex: 1;

      min-width: 0;

    }

    .notification-title {

      font-size: 0.85rem;

      font-weight: 700;

      color: var(--text-primary);

      margin-bottom: 0.25rem;

      line-height: 1.3;

    }

    .notification-body {

      font-size: 0.78rem;

      color: var(--text-secondary);

      line-height: 1.4;

      margin-bottom: 0.35rem;

      display: -webkit-box;

      -webkit-line-clamp: 2; line-clamp: 2;

      -webkit-box-orient: vertical;

      overflow: hidden;

    }

    .notification-time {

      font-size: 0.7rem;

      color: var(--text-secondary);

      font-weight: 500;

    }

    .notification-empty {

      padding: 3rem 1.5rem;

      text-align: center;

      color: var(--text-secondary);

    }

    .notification-empty i {

      font-size: 2.5rem;

      display: block;

      margin-bottom: 0.75rem;

      opacity: 0.4;

    }

    .notification-empty p {

      font-size: 0.85rem;

      margin: 0;

    }

    .notification-loading {

      padding: 2rem 1.5rem;

      text-align: center;

      color: var(--text-secondary);

      font-size: 0.85rem;

    }

    .notification-loading .spinner {

      width: 28px;

      height: 28px;

      border: 3px solid var(--border-color);

      border-top-color: var(--orange);

      border-radius: 50%;

      animation: spin 0.7s linear infinite;

      margin: 0 auto 0.75rem;

    }

    /* Search results dropdown */

    .navbar-search-results {

      position: absolute; top: calc(100% + 8px); left: 0; right: 0;

      background: var(--bg-card);

      border: 1px solid var(--border-color);

      border-radius: 14px;

      box-shadow: 0 12px 40px rgba(0,0,0,0.25);

      z-index: 2000;

      max-height: 420px; overflow-y: auto;

      animation: dropIn 0.18s ease;

    }

    @keyframes dropIn {

      from { opacity: 0; transform: translateY(-6px); }

      to   { opacity: 1; transform: translateY(0); }

    }

    .nsr-section-label {

      font-size: 0.68rem; font-weight: 700; text-transform: uppercase;

      letter-spacing: 1px; color: var(--text-secondary);

      padding: 0.75rem 1rem 0.35rem;

    }

    .nsr-item {

      display: flex; align-items: center; gap: 0.75rem;

      padding: 0.6rem 1rem; cursor: pointer;

      transition: background 0.15s;

      text-decoration: none; color: var(--text-primary);

    }

    .nsr-item:hover { background: rgba(230,168,0,0.07); }

    .nsr-item-img {

      width: 40px; height: 40px; border-radius: 8px;

      object-fit: cover; flex-shrink: 0;

      background: var(--bg); border: 1px solid var(--border-color);

    }

    .nsr-item-img-ph {

      width: 40px; height: 40px; border-radius: 8px;

      background: var(--bg); border: 1px solid var(--border-color);

      display: flex; align-items: center; justify-content: center;

      font-size: 1.1rem; color: var(--text-secondary); flex-shrink: 0;

    }

    .nsr-item-avatar {

      width: 40px; height: 40px; border-radius: 50%;

      background: linear-gradient(135deg,rgba(230,168,0,0.2),rgba(230,168,0,0.06));

      border: 2px solid rgba(230,168,0,0.25);

      display: flex; align-items: center; justify-content: center;

      font-size: 1.1rem; color: var(--orange); flex-shrink: 0;

    }

    .nsr-item-body { flex: 1; min-width: 0; }

    .nsr-item-name {

      font-size: 0.85rem; font-weight: 700; color: var(--text-primary);

      white-space: nowrap; overflow: hidden; text-overflow: ellipsis;

    }

    .nsr-item-sub {

      font-size: 0.72rem; color: var(--text-secondary); margin-top: 0.1rem;

    }

    .nsr-item-price {

      font-size: 0.85rem; font-weight: 800; color: var(--orange);

      flex-shrink: 0;

    }

    .nsr-empty {

      padding: 1.5rem 1rem; text-align: center;

      color: var(--text-secondary); font-size: 0.85rem;

    }

    .nsr-divider {

      height: 1px; background: var(--border-color); margin: 0.25rem 0;

    }

    /* =====================

       SUPPLIER NAV PROFILE

    ===================== */

    .supplier-nav-profile {

      display: flex; align-items: center; gap: 0.6rem;

      background: var(--bg-card);

      border: 1.5px solid var(--border-color);

      border-radius: 50px;

      padding: 0.3rem 0.85rem 0.3rem 0.4rem;

      cursor: default;

      transition: border-color 0.2s;

    }

    .supplier-nav-profile:hover { border-color: var(--orange); }

    .snp-avatar {

      width: 32px; height: 32px; border-radius: 50%;

      background: linear-gradient(135deg, rgba(230,168,0,0.25), rgba(230,168,0,0.08));

      border: 2px solid rgba(230,168,0,0.35);

      display: flex; align-items: center; justify-content: center;

      font-size: 0.9rem; color: var(--orange); flex-shrink: 0;

      overflow: hidden;

    }

    .snp-avatar img { width: 100%; height: 100%; object-fit: cover; border-radius: 50%; }

    .snp-info { display: flex; flex-direction: column; line-height: 1.2; }

    .snp-name {

      font-size: 0.82rem; font-weight: 700; color: var(--text-primary);

      max-width: 110px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;

    }

    .snp-role { font-size: 0.68rem; color: var(--text-secondary); }

    @media (max-width: 992px) {

      .navbar-search-box { min-width: 180px; }

    }

    @media (max-width: 768px) {

      .navbar-search-wrap { order: 3; width: 100%; margin-top: 0.5rem; }

      .navbar-search-box  { min-width: unset; width: 100%; border-radius: 10px; }

      .navbar-search-results { border-radius: 10px; }

      .snp-info { display: none; }

      .hero-content { text-align: center; }

      .hero-buttons { justify-content: center; }

      .info-section .row { gap: 2.5rem; }

    }

    /* =====================================================================
       COMPREHENSIVE MOBILE RESPONSIVE FIXES
       Covers all screen sizes down to 320px (smallest common phone)
    ===================================================================== */

    /* Prevent any horizontal scroll on the whole page */
    html, body { overflow-x: hidden; max-width: 100%; }

    /* ── Navbar ──────────────────────────────────────────────── */
    @media (max-width: 991px) {
      /* Collapse the action buttons area so it doesn't overflow */
      #navActions {
        flex-wrap: wrap;
        gap: 0.4rem !important;
        justify-content: flex-end;
      }
      .snp-info { display: none; }
      /* Make the hamburger visible */
      .navbar-toggler { display: flex !important; }
      /* Nav links collapse properly */
      .navbar-collapse { width: 100%; }
      #mainNav .navbar-nav { padding: 0.5rem 0; gap: 0 !important; }
      #mainNav .nav-link { padding: 0.5rem 1rem !important; border-radius: 0; }
    }

    @media (max-width: 575px) {
      /* On very small screens, hide label text next to icons in navbar buttons */
      .btn-navbar-login .login-label { display: none; }
      /* Keep mob-btn-label visible — this is the mobile-only button label */
      .mob-btn-label { display: inline !important; }
      /* Shrink avatar in navbar */
      .snp-avatar { width: 30px !important; height: 30px !important; font-size: 0.75rem !important; }
      /* Compact notification/msg badges */
      #customerCartBtn, #navMsgWrap a {
        width: 32px !important; height: 32px !important; font-size: 0.85rem !important;
      }
    }

    /* ── Hero (logged-out) ────────────────────────────────────── */
    @media (max-width: 991px) {
      /* Hero section — consolidated mobile overrides */
      .hero-section {
        display: block !important;
        background: linear-gradient(135deg, #0a0a0a 0%, #111111 40%, #1a1200 100%) !important;
        min-height: unset !important;
        padding-top: 68px !important;
        padding-bottom: 2rem !important;
        overflow: visible !important;
        scroll-margin-top: 68px !important;
      }
      #heroLoggedOut {
        display: block !important;
        min-height: unset !important;
        padding: 1.5rem 0 2rem !important;
        background: transparent !important;
        position: relative !important;
        z-index: 1 !important;
        isolation: isolate !important;
        width: 100% !important;
      }
      /* Override inline max-width:560px — make content full width and centered */
      #heroLoggedOut .hero-content {
        max-width: 100% !important;
        flex: unset !important;
        text-align: center !important;
        padding: 0 0.75rem !important;
        display: block !important;
        visibility: visible !important;
        opacity: 1 !important;
      }
      .hero-right { display: none !important; }
      .hero-headline { font-size: clamp(1.8rem, 6vw, 3rem) !important; }
      .hero-subtext  { font-size: 0.95rem !important; }
      .hero-stats    { justify-content: center; gap: 1.25rem !important; flex-wrap: wrap; }
      .hero-buttons  { justify-content: center; flex-wrap: wrap; gap: 0.75rem !important; }
      .btn-hero-login, .btn-hero-register { padding: 0.7rem 1.6rem !important; font-size: 0.9rem !important; }
    }

    @media (max-width: 480px) {
      .hero-headline { font-size: clamp(1.6rem, 7vw, 2.2rem) !important; }
      .hero-stat-val { font-size: 1.25rem !important; }
      .hero-stat-lbl { font-size: 0.65rem !important; }
      .btn-hero-login, .btn-hero-register { width: 100%; justify-content: center; }
      .hero-buttons { flex-direction: column; align-items: center; }
    }

    /* ── Hero (logged-in marketplace) ────────────────────────── */
    @media (max-width: 768px) {
      #heroMarketplace { padding: 0.5rem 0 0 !important; background: var(--bg, #0d0d0d) !important; }
      #mktBannerRow    { height: 160px !important; }
      /* 3-col ad strip → 2 col on mobile */
      #mktAdStrip { grid-template-columns: repeat(2, 1fr) !important; gap: 0.5rem !important; }
      /* Customer ad section promo cards → 2 col */
      #custAdCards { grid-template-columns: repeat(2, 1fr) !important; gap: 0.5rem !important; }
      /* Fix card heights so they don't overflow */
      #custAdSection [style*="height:100px"],
      #mktAdStrip [style*="height:110px"] { height: auto !important; min-height: 90px !important; }
      /* Wide promo strip — stack vertically */
      #custAdSection > div:last-of-type {
        flex-direction: column !important;
        align-items: flex-start !important;
        gap: 0.85rem !important;
      }
      /* Technician spotlight */
      #heroMarketplace > div > div[style*="promo"] {
        flex-direction: column !important;
        align-items: flex-start !important;
      }
      /* Product grid — 2 columns on mobile like standard app design */
      .product-grid {
        grid-template-columns: repeat(2, 1fr) !important;
        gap: 0.75rem !important;
      }
    }

    @media (max-width: 575px) {
      /* Promo strip badges row — wrap */
      #custAdSection > div:last-of-type > div:last-child {
        flex-wrap: wrap !important;
        gap: 0.5rem !important;
      }
      /* Category row pills shrink */
      .mkt-cat-pill { min-width: 58px !important; }
      .mkt-cat-pill-icon { width: 50px !important; height: 50px !important; font-size: 1.3rem !important; }
      /* Product cards — slightly smaller text on tiny screens */
      .product-card-title { font-size: 0.75rem !important; }
      .product-price { font-size: 0.9rem !important; }
      .product-card-body { padding: 0.6rem 0.7rem 0.75rem !important; }
    }

    /* ── Section isolation — prevents content bleed-through on mobile ── */
    .services-section, .how-section, .shop-section,
    .tech-section, .info-section, .promo-banner,
    .testimonials-section, .about-section, footer {
      position: relative;
      z-index: 1;
      isolation: isolate;
    }
    /* Ensure sections have opaque backgrounds on mobile */
    @media (max-width: 991px) {
      .services-section, .how-section, .shop-section,
      .tech-section, .info-section, .testimonials-section,
      .about-section {
        background-color: var(--bg, #0d0d0d) !important;
        position: relative !important;
        z-index: 2 !important;
      }
      /* Stop hero grid animation on mobile — saves battery + prevents bleed */
      .hero-section::before {
        animation: none !important;
        background-position: 0 0 !important;
      }
      /* Stop heroPulse and heroFloat on mobile too */
      .hero-glow, .hero-badge-float, [style*="heroPulse"],
      [style*="heroFloat"] {
        animation: none !important;
      }
      .section-heading { font-size: clamp(1.4rem, 4vw, 2rem) !important; }

      /* ── FIX: Hero marketplace sections must not overlap ── */
      #heroMarketplace {
        position: relative !important;
        z-index: 1 !important;
        isolation: isolate !important;
        width: 100% !important;
        flex-shrink: 0 !important;
      }
      /* Mobile search bar — show on mobile only */
      #mobileSearchBar { display: block !important; }
      html { scroll-padding-top: 105px !important; }
      /* Marketplace: full background to cover hero gradient */
      #heroMarketplace {
        background: var(--bg, #0d0d0d) !important;
        padding-top: 0.5rem !important;
        padding-bottom: 1rem !important;
      }
      /* The marketplace banner row on mobile */
      #mktBannerRow {
        height: 160px !important;
        border-radius: 10px !important;
        margin-bottom: 1rem !important;
        overflow: hidden !important;
      }
      #mktBannerSlides {
        overflow: hidden !important;
        border-radius: 10px !important;
      }
      /* Ad strip — single column on mobile */
      #mktAdStrip {
        grid-template-columns: 1fr !important;
        gap: 0.5rem !important;
      }
      #custAdCards {
        grid-template-columns: 1fr 1fr !important;
        gap: 0.5rem !important;
      }
      /* Ad section cards — fix height for mobile */
      #custAdSection [style*="height:100px"],
      #custAdSection [style*="height:120px"] {
        height: auto !important;
        min-height: 70px !important;
      }
      /* Welcome strip on mobile */
      #heroMarketplace > div:first-child {
        padding: 0.75rem 1rem !important;
        border-radius: 10px !important;
        margin-bottom: 0.75rem !important;
      }
      /* Prevent any absolute-positioned children from escaping */
      #mktBannerSlides,
      #mktBannerSlides > * {
        border-radius: 10px !important;
        overflow: hidden !important;
      }
    }

    /* ── Services — swipeable carousel on mobile ─────────────── */
    /* Desktop: 3-column grid */
    .svc-grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 1.5rem;
    }
    /* How It Works — desktop: 4-column grid */
    .how-grid {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 1.5rem;
    }
    /* Shared swipe dots */
    .swipe-dots { display: none; justify-content: center; gap: 6px; margin-top: 1rem; }
    .swipe-dot  { width: 8px; height: 8px; border-radius: 50%; background: rgba(255,255,255,0.25); display: inline-block; transition: background 0.3s; }
    .swipe-dot.active { background: var(--orange); }

    @media (max-width: 991px) {
      .services-section { padding: 48px 0 !important; }
      /* Services: horizontal swipe */
      .svc-grid {
        display: flex !important;
        flex-direction: row !important;
        overflow-x: auto !important;
        scroll-snap-type: x mandatory !important;
        -webkit-overflow-scrolling: touch !important;
        gap: 0.75rem !important;
        padding: 0.25rem 1rem 1rem !important;
        scrollbar-width: none !important;
        -ms-overflow-style: none !important;
      }
      .svc-grid::-webkit-scrollbar { display: none; }
      .svc-item {
        flex: 0 0 80vw !important;
        min-width: 0 !important;
        scroll-snap-align: center !important;
      }
      .svc-item .service-card { height: 100% !important; }
      #svcDots { display: flex !important; }
    }

    @media (max-width: 768px) {
      .how-section { padding: 48px 0 32px !important; }
      /* How It Works: horizontal swipe */
      .how-grid {
        display: flex !important;
        flex-direction: row !important;
        overflow-x: auto !important;
        scroll-snap-type: x mandatory !important;
        -webkit-overflow-scrolling: touch !important;
        gap: 0.75rem !important;
        padding: 0.25rem 1rem 1rem !important;
        scrollbar-width: none !important;
        -ms-overflow-style: none !important;
      }
      .how-grid::-webkit-scrollbar { display: none; }
      .how-item {
        flex: 0 0 80vw !important;
        min-width: 0 !important;
        scroll-snap-align: center !important;
      }
      .how-item .step-card {
        background: var(--bg-card, #1a1a1a) !important;
        border: 1px solid var(--border-color, #2a2a2a) !important;
        border-radius: 16px !important;
        padding: 1.5rem 1rem !important;
        height: 100% !important;
      }
      .step-number    { width: 44px !important; height: 44px !important; font-size: 1.1rem !important; }
      .step-icon      { font-size: 1.6rem !important; }
      .step-connector { display: none !important; }
      #howDots { display: flex !important; }
    }

    /* ── Promo banner ─────────────────────────────────────────── */
    @media (max-width: 768px) {
      .promo-banner { padding: 36px 0 28px !important; }
      .promo-banner .row { flex-direction: column !important; gap: 0.75rem !important; }
      .promo-banner .col-lg-4 { display: none !important; }
      .promo-banner .col-lg-8 { padding-left: 1rem !important; padding-right: 1rem !important; }
      .promo-banner h2 { font-size: 1.15rem !important; margin-bottom: 0.4rem !important; }
      .promo-banner p  { font-size: 0.8rem !important; line-height: 1.5 !important; margin-bottom: 0.85rem !important; max-width: 100% !important; }
      .promo-banner a  { width: 100% !important; justify-content: center !important; text-align: center; padding: 0.6rem 1rem !important; font-size: 0.88rem !important; }
      /* Compress the badge tags below the button */
      .promo-banner span[style*="border-radius:50px"] { font-size: 0.7rem !important; padding: 0.28rem 0.7rem !important; }
      /* Compress tech section top padding to remove the dead space */
      .tech-section { padding: 32px 0 40px !important; }
      /* Compress shop section top space */
      .shop-section { padding-top: 36px !important; padding-bottom: 32px !important; }
      .shop-section .section-label   { font-size: 0.65rem !important; margin-bottom: 0.4rem !important; }
      .shop-section .section-heading { font-size: 1.3rem !important; margin-bottom: 0.35rem !important; }
      .shop-subtitle { font-size: 0.82rem !important; margin-bottom: 1rem !important; }
    }

    /* ── Testimonials ─────────────────────────────────────────── */
    @media (max-width: 768px) {
      .testimonial-card { padding: 1.25rem !important; }
      .testimonial-text { font-size: 0.83rem !important; }
    }

    /* ── CTA join banner ──────────────────────────────────────── */
    @media (max-width: 575px) {
      /* Stack buttons vertically */
      #guestOnlyContent2 div[style*="display:flex"] > a,
      #guestOnlyContent2 div[style*="justify-content:center"] > a {
        width: 100% !important; justify-content: center !important;
      }
    }

    /* ── Trust ticker ─────────────────────────────────────────── */
    @media (max-width: 575px) {
      .ticker-item { font-size: 0.7rem !important; padding: 0 1.5rem !important; }
    }

    /* ── Technician profile modal ─────────────────────────────── */
    @media (max-width: 575px) {
      #techProfileModal > div { max-width: 100% !important; margin: 0.5rem !important; border-radius: 16px !important; }
    }
    /* ── Technician profile modal — mobile scroll fix ───────── */
    @media (max-width: 991px) {
      #techProfileModal {
        align-items: flex-start !important;
        padding: 0 !important;
        overflow-y: auto !important;
        -webkit-overflow-scrolling: touch !important;
      }
      #techProfileModal > div {
        margin: 0 !important;
        border-radius: 0 !important;
        min-height: 100% !important;
        max-height: none !important;
        width: 100% !important;
        max-width: 100% !important;
      }
    }
    @media (min-width: 769px) and (max-width: 1024px) {
      /* Surface Pro 7 — modal scrollable, comfortable width */
      #techProfileModal {
        align-items: flex-start !important;
        padding: 1rem !important;
        overflow-y: auto !important;
      }
      #techProfileModal > div {
        margin: 1rem auto !important;
        border-radius: 16px !important;
        max-width: 720px !important;
      }
    }

    /* ── Booking/Agreement modals ─────────────────────────────── */
    @media (max-width: 575px) {
      #bfAgreementModal > div,
      #agreementModal   > div { max-width: 100% !important; margin: 0.5rem !important; border-radius: 16px !important; }
      /* Booking form service type selector → stack vertically */
      #tpmBookingForm .hero-content > div[style*="grid-template-columns:1fr 1fr"],
      .bfSvcGrid { grid-template-columns: 1fr !important; }
    }

    /* ── Shop & technician product grids ─────────────────────── */
    @media (max-width: 575px) {
      /* Force product grid to 2 columns on small phones */
      #shopProductsContainer .row { --bs-gutter-x: 0.5rem !important; }
      #shopProductsContainer .col-6 { padding: 0.25rem !important; }
      .mkt-feat-body { padding: 0.6rem 0.7rem 0.7rem !important; }
      .mkt-feat-name { font-size: 0.75rem !important; }
      .mkt-feat-price { font-size: 0.88rem !important; }
      /* Tech grid — 1 col on tiny screens */
      #techGridContainer .col-md-4 { flex: 0 0 100% !important; max-width: 100% !important; }
    }

    @media (max-width: 400px) {
      /* Ultra-small — 1 column product grid */
      #shopProductsContainer .col-6 { flex: 0 0 100% !important; max-width: 100% !important; }
    }

    /* ── Info/About section ───────────────────────────────────── */
    @media (max-width: 768px) {
      .info-section .video-wrapper { margin-top: 1.5rem; }
    }

    /* ── Footer ───────────────────────────────────────────────── */
    @media (max-width: 575px) {
      .footer { padding: 1.25rem 0 !important; text-align: center; }
      .footer p { font-size: 0.8rem !important; }
    }


    /* ── Mobile navbar: hide desktop controls on small screens ── */
    @media (max-width: 991px) {
      #navDesktopControls { display: none !important; }
      #navMobileIcons     { display: flex !important; }
      .navbar             { flex-wrap: nowrap !important; padding: 0.35rem 0.85rem !important; }
      .navbar .container  { flex-wrap: nowrap !important; }
      .brand-logo-img     { height: 36px !important; }
      /* Hide Bootstrap collapse on mobile — we use the right drawer instead */
      .navbar-collapse    { display: none !important; }
      /* Add bottom padding so content isn't hidden behind bottom nav */
      body                { padding-bottom: 60px; }
    }

    /* ── Surface Pro 7 (912px) — show full desktop nav ─────── */
    @media (min-width: 769px) and (max-width: 1024px) {
      /* Show desktop nav links, hide mobile icons and drawer trigger */
      .navbar-collapse    { display: flex !important; }
      #navMobileIcons     { display: none !important; }
      #navDesktopControls { display: flex !important; }
      /* Smaller but visible nav */
      .navbar   { padding: 0.35rem 1rem !important; }
      .nav-link { font-size: 0.85rem !important; padding: 0.35rem 0.65rem !important; }
      .brand-logo-img { height: 38px !important; }
      /* No bottom padding needed — no bottom nav */
      body { padding-bottom: 0 !important; }
    }
    /* ── Surface Pro 7 (912px) — shop/landing page layout ──── */
    @media (min-width: 769px) and (max-width: 1024px) {
      /* Hero: side by side, compact */
      .hero-section { min-height: 70vh !important; padding-top: 70px !important; }
      .hero-headline { font-size: clamp(1.8rem, 3.5vw, 2.8rem) !important; }
      .hero-subtext  { font-size: 0.92rem !important; }
      .hero-right    { display: flex !important; }

      /* Section padding — comfortable for tablet */
      .services-section, .how-section, .shop-section,
      .tech-section, .info-section, .testimonials-section {
        padding: 60px 0 !important;
      }

      /* Service cards — 3 per row */
      .services-section .col-md-4 { flex: 0 0 33.33% !important; max-width: 33.33% !important; }

      /* Product grid — 3 col on tablet, 2 col on phone (overridden below) */
      .product-grid {
        grid-template-columns: repeat(3, 1fr) !important;
        gap: 1rem !important;
      }

      /* Category tabs — swipeable single row on mobile */
      .category-tabs {
        flex-wrap: nowrap !important;
        overflow-x: auto !important;
        scrollbar-width: none !important;
        -ms-overflow-style: none !important;
        padding-bottom: 0.25rem;
        gap: 0.4rem;
        -webkit-overflow-scrolling: touch;
      }
      .category-tabs::-webkit-scrollbar { display: none; }
      .cat-tab { font-size: 0.78rem !important; padding: 0.35rem 0.85rem !important; flex-shrink: 0 !important; white-space: nowrap !important; }
      .shop-search { width: 100% !important; min-width: unset !important; }

      /* How-it-works steps — 4 per row */
      .step-card { padding: 1.5rem 1rem !important; }

      /* Technicians grid — 2 or 3 col */
      .tech-grid { grid-template-columns: repeat(2, 1fr) !important; }

      /* Hide mobile-only bottom nav */
      #shopBottomNav { display: none !important; }
    }

    /* Bottom nav — always opaque regardless of theme */
    #shopBottomNav {
      background: #0d0d0d !important;
      isolation: isolate;
    }
    [data-theme="light"] #shopBottomNav {
      background: #f5f5f5 !important;
    }

    /* ── Phone screens — 2-col product grid ─────────────────── */
    @media (max-width: 600px) {
      .product-grid {
        grid-template-columns: repeat(2, 1fr) !important;
        gap: 0.6rem !important;
      }
      /* Tighten card text on small screens */
      .product-card-title  { font-size: 0.75rem !important; line-height: 1.3 !important; }
      .product-price       { font-size: 0.88rem !important; }
      .product-qty         { font-size: 0.65rem !important; }
      .product-card-body   { padding: 0.6rem 0.65rem 0.75rem !important; }
      .product-cat-badge   { font-size: 0.6rem !important; }
      .product-card-cta    { font-size: 0.72rem !important; padding: 0.4rem 0.6rem !important; }
    }

    @media (max-width: 575px) {
      .brand-logo-img  { height: 30px !important; }
      .navbar-brand    { font-size: 1rem !important; }
      /* Hide theme toggle on mobile — saves navbar space, accessible via drawer */
      #themeToggleMobile { display: none !important; }
    }

    /* When logged in on mobile: hide Dashboard pill (Me tab in bottom nav handles it) */
    @media (max-width: 991px) {
      body.fg-logged-in #navLoginBtnMob { display: none !important; }
    }
    /* Drawer link hover */
    #mobileDrawer nav a:hover {
      background: rgba(230,168,0,0.08);
      color: var(--orange, #e6a800) !important;
    }
    #mobileDrawer nav a:hover i {
      transform: scale(1.1);
    }

  </style>

  <!-- Pre-hide heroMarketplace before any render — prevents overlap flash -->
  <style id="heroPreHide">
    #heroMarketplace { display: none !important; }
  </style>

  <!-- Early hero swap: ONLY swaps if server confirms session is active -->
  <script>
    (function() {
      try {
        var u = JSON.parse(sessionStorage.getItem('fg_user') || 'null');
        if (u && u.role !== 'supervisor') {
          // Verify with server synchronously before doing anything
          var xhr = new XMLHttpRequest();
          xhr.open('GET', 'api/session/user', false);
          try { xhr.send(); } catch(e) { sessionStorage.removeItem('fg_user'); u = null; }
          if (xhr.status === 200) {
            var resp = {};
            try { resp = JSON.parse(xhr.responseText); } catch(e) {}
            if (!resp.loggedIn) {
              // Server says not logged in — clear stale cache, show hero
              sessionStorage.removeItem('fg_user');
              u = null;
            }
          } else {
            sessionStorage.removeItem('fg_user');
            u = null;
          }
        }
        if (u && u.role !== 'supervisor') {
          // Server-confirmed logged-in user — swap hero
          var preHide = document.getElementById('heroPreHide');
          if (preHide) preHide.parentNode.removeChild(preHide);
          document.write('<style id="heroSwapStyle">#heroLoggedOut{display:none!important}#heroMarketplace{display:block!important}</style>');
          document.write('<style>#guestOnlyContent{display:none!important}</style>');
          // Mark body as logged-in for CSS targeting
          document.write('<script>document.addEventListener("DOMContentLoaded",function(){document.body.classList.add("fg-logged-in");});<\/script>');
        }
      } catch(e) { sessionStorage.removeItem('fg_user'); }
      document.write('<style>#guestOnlyContent2{display:none!important}</style>');
    })();
  </script>

</head>

<body>

  <!-- =====================
       APP INTRO OVERLAY
  ===================== -->
  <div id="fgIntroOverlay" style="display:none;position:fixed;inset:0;z-index:99999;background:rgba(0,0,0,0.82);backdrop-filter:blur(8px);-webkit-backdrop-filter:blur(8px);align-items:center;justify-content:center;padding:1rem;">
    <div style="background:#141414;border:1px solid rgba(230,168,0,0.25);border-radius:24px;width:100%;max-width:480px;overflow:hidden;box-shadow:0 40px 100px rgba(0,0,0,0.7);animation:introSlideUp 0.45s cubic-bezier(0.34,1.56,0.64,1) both;">

      <!-- Header gradient -->
      <div style="background:linear-gradient(135deg,#1a0f00 0%,#0d0d0d 100%);padding:2rem 2rem 1.25rem;text-align:center;position:relative;overflow:hidden;">
        <div style="position:absolute;top:-40px;left:50%;transform:translateX(-50%);width:240px;height:140px;border-radius:50%;background:radial-gradient(ellipse,rgba(230,168,0,0.18),transparent 70%);pointer-events:none;"></div>
        <!-- Logo -->
        <div style="width:64px;height:64px;border-radius:18px;background:linear-gradient(135deg,rgba(230,168,0,0.2),rgba(230,168,0,0.06));border:1.5px solid rgba(230,168,0,0.35);display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;font-size:1.8rem;">🔧</div>
        <div style="font-size:1.6rem;font-weight:900;color:#fff;letter-spacing:-0.5px;line-height:1.2;">Fix<span style="color:#e6a800;">&amp;Go</span></div>
        <div style="font-size:0.72rem;font-weight:700;letter-spacing:2.5px;text-transform:uppercase;color:rgba(230,168,0,0.75);margin-top:0.3rem;">Phone Repair &amp; Shop</div>
      </div>

      <!-- Slides wrapper -->
      <div style="padding:1.75rem 2rem 0;">

        <!-- Slide 1 -->
        <div class="fg-intro-slide" data-slide="0" style="text-align:center;">
          <div style="font-size:2.6rem;margin-bottom:0.85rem;line-height:1;">📱</div>
          <div style="font-size:1.1rem;font-weight:800;color:#fff;margin-bottom:0.6rem;">Welcome to Fix&amp;Go</div>
          <div style="font-size:0.88rem;color:rgba(255,255,255,0.6);line-height:1.7;">Your all-in-one platform for <strong style="color:#e6a800;">phone repairs</strong>, <strong style="color:#e6a800;">spare parts</strong>, and certified technicians — all in one place.</div>
        </div>

        <!-- Slide 2 -->
        <div class="fg-intro-slide" data-slide="1" style="display:none;text-align:center;">
          <div style="font-size:2.6rem;margin-bottom:0.85rem;line-height:1;">🔧</div>
          <div style="font-size:1.1rem;font-weight:800;color:#fff;margin-bottom:0.6rem;">Book a Repair Instantly</div>
          <div style="font-size:0.88rem;color:rgba(255,255,255,0.6);line-height:1.7;">Find a nearby certified technician, describe your issue, and schedule a repair — at your home or in-shop.</div>
        </div>

        <!-- Slide 3 -->
        <div class="fg-intro-slide" data-slide="2" style="display:none;text-align:center;">
          <div style="font-size:2.6rem;margin-bottom:0.85rem;line-height:1;">🛒</div>
          <div style="font-size:1.1rem;font-weight:800;color:#fff;margin-bottom:0.6rem;">Shop Genuine Parts</div>
          <div style="font-size:0.88rem;color:rgba(255,255,255,0.6);line-height:1.7;">Browse our marketplace for screen replacements, batteries, cameras, and more — sourced from verified suppliers.</div>
        </div>

        <!-- Slide 4 -->
        <div class="fg-intro-slide" data-slide="3" style="display:none;text-align:center;">
          <div style="font-size:2.6rem;margin-bottom:0.85rem;line-height:1;">⭐</div>
          <div style="font-size:1.1rem;font-weight:800;color:#fff;margin-bottom:0.6rem;">Trusted by 500+ Customers</div>
          <div style="font-size:0.88rem;color:rgba(255,255,255,0.6);line-height:1.7;">4.9★ average rating. Every repair is backed by a <strong style="color:#e6a800;">90-day warranty</strong> and professional service guarantee.</div>
        </div>

      </div>

      <!-- Dot indicators -->
      <div style="display:flex;justify-content:center;gap:0.5rem;padding:1.25rem 0 0.5rem;">
        <span class="fg-intro-dot active" data-dot="0" style="width:22px;height:5px;border-radius:3px;background:#e6a800;cursor:pointer;transition:all 0.3s;"></span>
        <span class="fg-intro-dot" data-dot="1" style="width:8px;height:5px;border-radius:3px;background:rgba(255,255,255,0.2);cursor:pointer;transition:all 0.3s;"></span>
        <span class="fg-intro-dot" data-dot="2" style="width:8px;height:5px;border-radius:3px;background:rgba(255,255,255,0.2);cursor:pointer;transition:all 0.3s;"></span>
        <span class="fg-intro-dot" data-dot="3" style="width:8px;height:5px;border-radius:3px;background:rgba(255,255,255,0.2);cursor:pointer;transition:all 0.3s;"></span>
      </div>

      <!-- Actions -->
      <div style="padding:1rem 2rem 2rem;display:flex;flex-direction:column;gap:0.75rem;">
        <button id="fgIntroNextBtn" onclick="fgIntroNext()"
          style="width:100%;padding:0.85rem;border-radius:50px;border:none;background:linear-gradient(135deg,#e6a800,#c98f00);color:#000;font-weight:800;font-size:0.95rem;cursor:pointer;letter-spacing:0.3px;transition:all 0.2s;box-shadow:0 4px 20px rgba(230,168,0,0.35);"
          onmouseenter="this.style.transform='translateY(-2px)';this.style.boxShadow='0 8px 28px rgba(230,168,0,0.5)'"
          onmouseleave="this.style.transform='';this.style.boxShadow='0 4px 20px rgba(230,168,0,0.35)'">
          Next →
        </button>
        <button onclick="fgIntroSkip()"
          style="width:100%;padding:0.6rem;border-radius:50px;border:1.5px solid rgba(255,255,255,0.12);background:transparent;color:rgba(255,255,255,0.45);font-size:0.82rem;font-weight:600;cursor:pointer;transition:all 0.2s;"
          onmouseenter="this.style.borderColor='rgba(255,255,255,0.3)';this.style.color='rgba(255,255,255,0.7)'"
          onmouseleave="this.style.borderColor='rgba(255,255,255,0.12)';this.style.color='rgba(255,255,255,0.45)'">
          Skip intro
        </button>
      </div>

    </div>
  </div>

  <style>
    @keyframes introSlideUp {
      from { opacity:0; transform:translateY(40px) scale(0.96); }
      to   { opacity:1; transform:translateY(0)    scale(1);    }
    }
  </style>
  <script>
    (function(){
      const INTRO_KEY = 'fgIntroSeen_v2';
      let _slide = 0;
      const TOTAL = 4;

      function show() {
        var el = document.getElementById('fgIntroOverlay');
        if (el) el.style.display = 'flex';
      }
      function hide() {
        var el = document.getElementById('fgIntroOverlay');
        if (el) { el.style.opacity='0'; el.style.transition='opacity 0.35s'; setTimeout(function(){ el.style.display='none'; el.style.opacity=''; el.style.transition=''; }, 350); }
      }
      function goTo(n) {
        document.querySelectorAll('.fg-intro-slide').forEach(function(s){ s.style.display='none'; });
        document.querySelectorAll('.fg-intro-dot').forEach(function(d,i){
          d.style.width = i===n ? '22px' : '8px';
          d.style.background = i===n ? '#e6a800' : 'rgba(255,255,255,0.2)';
        });
        var t = document.querySelector('.fg-intro-slide[data-slide="'+n+'"]');
        if (t) t.style.display = 'block';
        var btn = document.getElementById('fgIntroNextBtn');
        if (btn) btn.textContent = n === TOTAL-1 ? 'Get Started →' : 'Next →';
        _slide = n;
      }
      window.fgIntroNext = function() {
        if (_slide < TOTAL-1) { goTo(_slide+1); }
        else { fgIntroSkip(); }
      };
      window.fgIntroSkip = function() {
        try { localStorage.setItem(INTRO_KEY, '1'); } catch(e){}
        hide();
      };
      // Dot click
      document.addEventListener('click', function(e){
        var d = e.target.closest('.fg-intro-dot');
        if (d) goTo(parseInt(d.dataset.dot)||0);
      });
      // Show only if not seen before
      try {
        if (!localStorage.getItem(INTRO_KEY)) show();
      } catch(e) { show(); }
    })();
  </script>

  <nav class="navbar navbar-expand-lg">

    <div class="container">

      <!-- Brand -->

      <a class="navbar-brand" href="#">

        <img src="assets/images/logo.png" alt="Fix&Go Logo" class="brand-logo-img"

             onerror="this.style.display='none'; document.getElementById('fallback-icon').style.display='inline-block';">

        <i class="fa-solid fa-screwdriver-wrench brand-icon" id="fallback-icon" style="display:none;"></i>

        Fix<span>&amp;Go</span>

      </a>

      <!-- Mobile toggler → opens right drawer -->
      <button class="navbar-toggler" type="button" id="mobileMenuBtn"
        aria-label="Toggle navigation" aria-expanded="false"
        onclick="openMobileDrawer()">
        <span class="navbar-toggler-icon"></span>
      </button>

      <!-- ── MOBILE-ONLY icons: always visible beside hamburger ── -->
      <div class="d-flex d-lg-none align-items-center gap-2 ms-auto me-2" id="navMobileIcons">
        <!-- Messages button — mobile (shown when logged in, replaces notification bell) -->
        <div id="navNotifWrapMob" style="display:none;position:relative;">
          <a id="navMobMsgBtn" href="views/user/customer/messages.php" aria-label="Messages"
            style="background:transparent;border:1.5px solid var(--border-color);border-radius:50%;width:34px;height:34px;display:flex;align-items:center;justify-content:center;cursor:pointer;color:var(--text-primary);font-size:0.88rem;position:relative;text-decoration:none;">
            <i class="fa-solid fa-comment-dots"></i>
            <span id="notifBadgeMob" style="display:none;position:absolute;top:-3px;right:-3px;background:#dc3545;color:#fff;font-size:0.5rem;font-weight:800;padding:0.1rem 0.3rem;border-radius:10px;min-width:14px;text-align:center;line-height:1.4;">0</span>
          </a>
        </div>
        <button class="theme-toggle-btn" id="themeToggleMobile" aria-label="Toggle theme" style="width:34px;height:34px;border-radius:50%;padding:0;font-size:0.85rem;" onclick="if(document.getElementById('themeToggle'))document.getElementById('themeToggle').click()">
          <i class="fa-solid fa-moon" id="themeIconMobile"></i>
        </button>
        <div id="customerCartWrapMob" style="display:none;position:relative;">
          <button onclick="openCartDrawer()" aria-label="Cart" style="background:transparent;border:1.5px solid var(--border-color);border-radius:50%;width:34px;height:34px;display:flex;align-items:center;justify-content:center;cursor:pointer;color:var(--text-primary);font-size:0.88rem;">
            <i class="fa-solid fa-cart-shopping"></i>
            <span id="cartBadgeMob" style="display:none;position:absolute;top:-4px;right:-4px;background:#dc3545;color:#fff;font-size:0.55rem;font-weight:800;padding:0.1rem 0.3rem;border-radius:10px;min-width:14px;text-align:center;line-height:1.4;">0</span>
          </button>
        </div>
        <a href="login.html" id="navLoginBtnMob" style="background:linear-gradient(135deg,var(--orange),var(--orange-hover));color:#000;border:none;padding:0.45rem 1rem;border-radius:50px;font-weight:700;font-size:0.82rem;text-decoration:none;display:inline-flex;align-items:center;gap:0.35rem;white-space:nowrap;">
          <i class="fa-solid fa-right-to-bracket"></i><span class="mob-btn-label">Login</span>
        </a>
      </div>

      <!-- Desktop collapse (unchanged) -->
      <div class="collapse navbar-collapse" id="mainNav">

        <ul class="navbar-nav mx-auto mb-2 mb-lg-0 gap-1">

          <li class="nav-item">

            <a class="nav-link active" href="#">Home</a>

          </li>

          <li class="nav-item" id="navItemServices">

            <a class="nav-link" href="#services" id="navServices">Services</a>

          </li>

          <li class="nav-item">

            <a class="nav-link" href="#shop">Shop</a>

          </li>

          <li class="nav-item">

            <a class="nav-link" href="#technicians">Technicians</a>

          </li>

          <li class="nav-item" id="navItemNotifications" style="display:none;">

            <a class="nav-link" href="#" id="navNotifications" style="position:relative;">

              <i class="fa-solid fa-bell"></i>

              <span id="notificationBadge" class="notification-badge" style="display:none;">0</span>

            </a>

          </li>

          <li class="nav-item" id="navItemAbout">

            <a class="nav-link" href="#about" id="navAbout">About Us</a>

          </li>

        </ul>

        <!-- Right side controls -->

        <div class="d-flex align-items-center gap-3" id="navDesktopControls">

          <!-- â”€â”€ Global Search â”€â”€ -->

          <div class="navbar-search-wrap" id="navSearchWrap">

            <div class="navbar-search-box" id="navSearchBox">

              <i class="fa-solid fa-magnifying-glass navbar-search-icon"></i>

              <input type="text" id="navSearchInput"

                     class="navbar-search-input"

                     placeholder="Search accessories or technicians…"

                     autocomplete="off"

                     aria-label="Search accessories or technicians">

              <button class="navbar-search-clear" id="navSearchClear" title="Clear" style="display:none;">

                <i class="fa-solid fa-xmark"></i>

              </button>

            </div>

            <!-- Dropdown results -->

            <div class="navbar-search-results" id="navSearchResults" style="display:none;"></div>

          </div>

          <button class="theme-toggle-btn" id="themeToggle" aria-label="Toggle theme" title="Toggle dark/light mode">

            <i class="fa-solid fa-moon" id="themeIcon"></i>

          </button>

          <!-- â”€â”€ Supplier profile (shown only when supplier/owner/sales_person is logged in) â”€â”€ -->

          <div class="supplier-nav-profile" id="supplierNavProfile" style="display:none;">

            <div class="snp-avatar" id="snpAvatar">

              <i class="fa-solid fa-user"></i>

            </div>

            <div class="snp-info">

              <span class="snp-name" id="snpName">Supplier</span>

              <span class="snp-role">📦 Supplier</span>

            </div>

          </div>

          <!-- â”€â”€ Customer Cart Icon (shown only when customer is logged in) â”€â”€ -->

          <div id="customerCartWrap" style="display:none;position:relative;">

            <button id="customerCartBtn" onclick="openCartDrawer()" aria-label="Shopping Cart"

              style="background:transparent;border:1.5px solid var(--border-color);border-radius:50%;width:38px;height:38px;display:flex;align-items:center;justify-content:center;cursor:pointer;color:var(--text-primary);font-size:1rem;transition:all 0.2s;position:relative;">

              <i class="fa-solid fa-cart-shopping"></i>

              <span id="customerCartBadge" style="display:none;position:absolute;top:-5px;right:-5px;background:#dc3545;color:#fff;font-size:0.6rem;font-weight:800;padding:0.1rem 0.35rem;border-radius:10px;min-width:16px;text-align:center;line-height:1.4;">0</span>

            </button>

          </div>

          <!-- Messages Icon (shown for customer and sales_person) -->

          <div id="navMsgWrap" style="display:none;position:relative;">

            <a id="navMsgLink" href="#" aria-label="Messages" title="Messages"

              style="background:transparent;border:1.5px solid var(--border-color);border-radius:50%;width:38px;height:38px;display:flex;align-items:center;justify-content:center;cursor:pointer;color:var(--text-primary);font-size:1rem;transition:all 0.2s;text-decoration:none;"

              onmouseenter="this.style.borderColor='var(--orange)';this.style.color='var(--orange)'"

              onmouseleave="this.style.borderColor='var(--border-color)';this.style.color='var(--text-primary)'">

              <i class="fa-solid fa-comment-dots"></i>

            </a>

            <span id="navMsgBadgeIndex" style="display:none;position:absolute;top:-5px;right:-5px;background:var(--orange);color:#fff;font-size:0.6rem;font-weight:800;padding:0.1rem 0.35rem;border-radius:10px;min-width:16px;text-align:center;line-height:1.4;pointer-events:none;"></span>

          </div>

          <!--  (shown only when customer is logged in) â”€â”€ -->

          <div class="supplier-nav-profile" id="customerNavProfile" style="display:none;cursor:pointer;"

               onclick="window.location.href='views/user/customer/dashboard.php'">

            <div class="snp-avatar" id="customerNavAvatar">

              <i class="fa-solid fa-user"></i>

            </div>

            <div class="snp-info">

              <span class="snp-name" id="customerNavName">Customer</span>

              <span class="snp-role">👤 Customer</span>

            </div>

          </div>

          <a href="login.html" class="btn-navbar-login" id="navLoginBtn">

            <i class="fa-solid fa-right-to-bracket me-1"></i> Dashboard

          </a>

        </div>

      </div>

    </div>

  </nav>

  <!-- ── MOBILE SEARCH BAR — sticky below navbar, hidden on desktop ── -->
  <div id="mobileSearchBar"
    style="display:none;position:sticky;top:58px;z-index:999;
           background:var(--navbar-bg,rgba(13,13,13,0.97));
           border-bottom:1px solid var(--border-color,#2a2a2a);
           padding:0.5rem 1rem;backdrop-filter:blur(10px);-webkit-backdrop-filter:blur(10px);">
    <div style="position:relative;display:flex;align-items:center;">
      <i class="fa-solid fa-magnifying-glass"
         style="position:absolute;left:0.75rem;color:var(--text-secondary);font-size:0.85rem;pointer-events:none;"></i>
      <input type="text" id="mobileSearchInput"
             placeholder="Search accessories or technicians…"
             autocomplete="off"
             style="width:100%;background:var(--bg,#111);border:1.5px solid var(--border-color,#2a2a2a);
                    border-radius:50px;padding:0.5rem 2.5rem 0.5rem 2.25rem;
                    color:var(--text-primary,#fff);font-size:0.85rem;outline:none;
                    transition:border-color 0.2s;">
      <button id="mobileSearchClear"
              style="display:none;position:absolute;right:0.75rem;background:none;border:none;
                     color:var(--text-secondary);cursor:pointer;font-size:0.85rem;padding:0;">
        <i class="fa-solid fa-xmark"></i>
      </button>
    </div>
    <!-- Dropdown results reuse desktop results panel -->
    <div id="mobileSearchResults"
         style="display:none;position:absolute;left:1rem;right:1rem;top:calc(100% - 0.5rem);
                background:var(--bg-card,#1a1a1a);border:1px solid var(--border-color,#2a2a2a);
                border-radius:12px;box-shadow:0 8px 32px rgba(0,0,0,0.4);
                max-height:60vh;overflow-y:auto;z-index:1000;"></div>
  </div>

  <!-- =====================

       NOTIFICATION DROPDOWN

  ===================== -->
  <div class="notification-dropdown" id="notificationDropdown" style="display:none;">

    <div class="notification-header">

      <h6><i class="fa-solid fa-bell me-2"></i>Notifications</h6>

      <div class="notification-header-actions">

        <button class="notification-header-btn" id="markAllReadBtn">Mark all read</button>

        <button class="notification-header-btn" id="closeNotificationsBtn">

          <i class="fa-solid fa-xmark"></i>

        </button>

      </div>

    </div>

    <div class="notification-list" id="notificationList">

      <div class="notification-loading">

        <div class="spinner"></div>

        Loading notifications…

      </div>

    </div>

  </div>

  <!-- ═══ MOBILE RIGHT DRAWER ══════════════════════════════════ -->
  <!-- Overlay -->
  <div id="mobileDrawerOverlay" onclick="closeMobileDrawer()"
    style="display:none;position:fixed;inset:0;z-index:1099;background:rgba(0,0,0,0.55);backdrop-filter:blur(3px);transition:opacity 0.25s;"></div>

  <!-- Drawer panel — slides in from right -->
  <div id="mobileDrawer"
    style="position:fixed;top:0;right:0;z-index:1100;height:100%;width:72vw;max-width:300px;
           background:var(--navbar-bg,#111);border-left:1px solid var(--border-color,#2a2a2a);
           display:flex;flex-direction:column;transform:translateX(100%);
           transition:transform 0.3s cubic-bezier(0.4,0,0.2,1);
           box-shadow:-8px 0 32px rgba(0,0,0,0.4);">

    <!-- Drawer header -->
    <div style="display:flex;align-items:center;justify-content:space-between;
                padding:1rem 1.25rem;border-bottom:1px solid var(--border-color,#2a2a2a);">
      <span style="font-size:1rem;font-weight:800;color:var(--text-primary,#fff);">
        Fix<span style="color:var(--orange,#e6a800);">&amp;Go</span>
      </span>
      <button onclick="closeMobileDrawer()"
        style="background:none;border:1px solid var(--border-color,#2a2a2a);color:var(--text-primary,#fff);
               width:32px;height:32px;border-radius:8px;font-size:1rem;cursor:pointer;
               display:flex;align-items:center;justify-content:center;">✕</button>
    </div>

    <!-- Drawer nav links -->
    <nav style="flex:1;overflow-y:auto;padding:0.5rem 0;">
      <a href="#home" onclick="closeMobileDrawer()"
        style="display:flex;align-items:center;gap:0.85rem;padding:0.9rem 1.5rem;
               color:var(--text-primary,#fff);text-decoration:none;font-weight:600;font-size:1rem;
               border-bottom:1px solid rgba(255,255,255,0.05);transition:background 0.15s;">
        <i class="fa-solid fa-house" style="width:18px;color:var(--orange,#e6a800);"></i>Home
      </a>
      <a href="#services" onclick="closeMobileDrawer()"
        style="display:flex;align-items:center;gap:0.85rem;padding:0.9rem 1.5rem;
               color:var(--text-primary,#fff);text-decoration:none;font-weight:600;font-size:1rem;
               border-bottom:1px solid rgba(255,255,255,0.05);transition:background 0.15s;">
        <i class="fa-solid fa-wrench" style="width:18px;color:var(--orange,#e6a800);"></i>Services
      </a>
      <a href="#shop" onclick="closeMobileDrawer()"
        style="display:flex;align-items:center;gap:0.85rem;padding:0.9rem 1.5rem;
               color:var(--text-primary,#fff);text-decoration:none;font-weight:600;font-size:1rem;
               border-bottom:1px solid rgba(255,255,255,0.05);transition:background 0.15s;">
        <i class="fa-solid fa-shop" style="width:18px;color:var(--orange,#e6a800);"></i>Shop
      </a>
      <a href="#technicians" onclick="closeMobileDrawer()"
        style="display:flex;align-items:center;gap:0.85rem;padding:0.9rem 1.5rem;
               color:var(--text-primary,#fff);text-decoration:none;font-weight:600;font-size:1rem;
               border-bottom:1px solid rgba(255,255,255,0.05);transition:background 0.15s;">
        <i class="fa-solid fa-screwdriver-wrench" style="width:18px;color:var(--orange,#e6a800);"></i>Technicians
      </a>
      <a href="#about" onclick="closeMobileDrawer()"
        style="display:flex;align-items:center;gap:0.85rem;padding:0.9rem 1.5rem;
               color:var(--text-primary,#fff);text-decoration:none;font-weight:600;font-size:1rem;
               transition:background 0.15s;" id="drawerAbout">
        <i class="fa-solid fa-circle-info" style="width:18px;color:var(--orange,#e6a800);"></i>About Us
      </a>

      <!-- Notification row — always in drawer for logged-in users -->
      <div id="drawerNotifRow" style="display:none;border-top:1px solid rgba(255,255,255,0.05);">
        <a href="#" id="drawerNotifLink" onclick="closeMobileDrawer(); if(document.getElementById('navNotifications'))document.getElementById('navNotifications').click(); return false;"
          style="display:flex;align-items:center;gap:0.85rem;padding:0.9rem 1.5rem;
                 color:var(--text-primary,#fff);text-decoration:none;font-weight:600;font-size:1rem;">
          <i class="fa-solid fa-bell" style="width:18px;color:var(--orange,#e6a800);"></i>
          Notifications
          <span id="drawerNotifBadge" style="display:none;margin-left:auto;background:#dc3545;
                color:#fff;font-size:0.65rem;font-weight:800;padding:0.1rem 0.45rem;
                border-radius:10px;min-width:18px;text-align:center;">0</span>
        </a>
      </div>
    </nav>

    <!-- Drawer footer — intentionally empty, login is in the nav links -->
    <div style="padding:0.5rem 1.25rem;border-top:1px solid var(--border-color,#2a2a2a);"></div>
  </div>

  <!-- ═══ MOBILE BOTTOM NAV (shop/landing only) ════════════════ -->
  <nav id="shopBottomNav"
    style="display:none;position:fixed;bottom:0;left:0;right:0;z-index:900;
           background:var(--navbar-bg,rgba(13,13,13,0.98));
           backdrop-filter:blur(12px);-webkit-backdrop-filter:blur(12px);
           border-top:1px solid var(--border-color,#2a2a2a);
           padding:0.35rem 0 calc(0.35rem + env(safe-area-inset-bottom,0px));
           box-shadow:0 -4px 20px rgba(0,0,0,0.5);">
    <ul style="list-style:none;margin:0;padding:0;display:flex;justify-content:space-around;align-items:center;">
      <li>
        <a href="#home"
          style="display:flex;flex-direction:column;align-items:center;gap:0.15rem;
                 padding:0.3rem 0.75rem;color:var(--orange,#e6a800);text-decoration:none;
                 font-size:0.62rem;font-weight:700;">
          <i class="fa-solid fa-house" style="font-size:1.2rem;"></i>Home
        </a>
      </li>
      <li>
        <a href="#shop"
          style="display:flex;flex-direction:column;align-items:center;gap:0.15rem;
                 padding:0.3rem 0.75rem;color:var(--text-secondary,#888);text-decoration:none;
                 font-size:0.62rem;font-weight:700;">
          <i class="fa-solid fa-shop" style="font-size:1.2rem;"></i>Shop
        </a>
      </li>
      <li>
        <a href="#technicians"
          style="display:flex;flex-direction:column;align-items:center;gap:0.15rem;
                 padding:0.3rem 0.75rem;color:var(--text-secondary,#888);text-decoration:none;
                 font-size:0.62rem;font-weight:700;">
          <i class="fa-solid fa-wrench" style="font-size:1.2rem;"></i>Technicians
        </a>
      </li>
      <li id="shopBnNotif" style="display:none;position:relative;">
        <a href="views/user/customer/notifications.php" id="shopBnNotifBtn"
          style="display:flex;flex-direction:column;align-items:center;gap:0.15rem;
                 padding:0.3rem 0.75rem;color:var(--text-secondary,#888);text-decoration:none;
                 font-size:0.62rem;font-weight:700;">
          <span style="position:relative;display:inline-block;">
            <i class="fa-solid fa-inbox" style="font-size:1.2rem;"></i>
            <span id="shopBnBadge" style="display:none;position:absolute;top:-5px;right:-6px;
                  background:#dc3545;color:#fff;font-size:0.5rem;font-weight:800;
                  padding:0.05rem 0.3rem;border-radius:10px;min-width:14px;text-align:center;">0</span>
          </span>Inbox
        </a>
      </li>
      <li>
        <a href="login.html" id="shopBnLogin"
          style="display:flex;flex-direction:column;align-items:center;gap:0.15rem;
                 padding:0.3rem 0.75rem;color:var(--text-secondary,#888);text-decoration:none;
                 font-size:0.62rem;font-weight:700;">
          <i class="fa-solid fa-user" style="font-size:1.2rem;"></i>Me
        </a>
      </li>
    </ul>
  </nav>

  <!-- ═══ DRAWER + BOTTOM NAV SCRIPTS ══════════════════════════ -->
  <script>
  // ── Right drawer ────────────────────────────────────────────
  function openMobileDrawer() {
    document.getElementById('mobileDrawer').style.transform = 'translateX(0)';
    document.getElementById('mobileDrawerOverlay').style.display = 'block';
    document.getElementById('mobileMenuBtn').setAttribute('aria-expanded','true');
    document.body.style.overflow = 'hidden';
  }
  function closeMobileDrawer() {
    document.getElementById('mobileDrawer').style.transform = 'translateX(100%)';
    document.getElementById('mobileDrawerOverlay').style.display = 'none';
    document.getElementById('mobileMenuBtn').setAttribute('aria-expanded','false');
    document.body.style.overflow = '';
  }
  // Close on Escape
  document.addEventListener('keydown', function(e){
    if (e.key === 'Escape') closeMobileDrawer();
  });

  // ── Show bottom nav only on mobile ──────────────────────────
  (function initShopBottomNav(){
    var nav = document.getElementById('shopBottomNav');
    function check() {
      var isMob = window.innerWidth <= 991;
      nav.style.display = isMob ? 'block' : 'none';
      // Add bottom padding so last content isn't hidden behind the nav
      document.body.style.paddingBottom = isMob ? '70px' : '';
    }
    check();
    window.addEventListener('resize', check);

    // Highlight active section via IntersectionObserver
    var sections = ['home','shop','technicians','about'];
    var links = {
      home:        document.querySelector('#shopBottomNav a[href="#home"]'),
      shop:        document.querySelector('#shopBottomNav a[href="#shop"]'),
      technicians: document.querySelector('#shopBottomNav a[href="#technicians"]'),
    };
    var orange = 'var(--orange,#e6a800)';
    var muted  = 'var(--text-secondary,#888)';

    if ('IntersectionObserver' in window) {
      var io = new IntersectionObserver(function(entries){
        entries.forEach(function(en){
          if (en.isIntersecting) {
            Object.values(links).forEach(function(l){ if(l) l.style.color = muted; });
            var id = en.target.id;
            if (links[id]) links[id].style.color = orange;
          }
        });
      }, { threshold: 0.4 });
      sections.forEach(function(id){
        var el = document.getElementById(id);
        if (el) io.observe(el);
      });
    }
  })();

  // ── Sync notifications badge to drawer + bottom nav ─────────
  // (handled by syncMobileNav in the main script block)
  </script>

  <!-- =====================

       HERO SECTION

  ===================== -->

  <section class="hero-section" id="home" style="min-height:auto;padding-top:80px;padding-bottom:2rem;background:var(--bg);overflow:visible;display:block;">

    <!-- ═══ ALWAYS-VISIBLE MOBILE HERO CARD ═══════════════════════
         This block has NO id referenced by JS — it always shows.
         Hidden on desktop (≥992px) where the full layout takes over.
    ═══════════════════════════════════════════════════════════════ -->
    <div class="container" style="padding-top:0;padding-bottom:0;">
      <div class="fg-mobile-hero-static">
        <!-- glow orb -->
        <div style="position:absolute;top:-50px;right:-30px;width:200px;height:200px;border-radius:50%;background:radial-gradient(circle,rgba(230,168,0,0.13),transparent 70%);pointer-events:none;z-index:0;"></div>
        <div style="position:relative;z-index:1;">
          <span class="fg-mhs-badge">Fast &bull; Reliable &bull; Affordable</span>
          <h1 class="fg-mhs-title">Fix<span>&amp;Go</span> &ndash;<br>Your Trusted<br><em>Phone Repair</em> &amp; Shop</h1>
          <p class="fg-mhs-sub">Professional phone repairs done right the first time. From cracked screens to battery issues, our certified technicians get your device back in perfect shape &mdash; fast.</p>
          <div class="fg-mhs-btns">
            <a href="login.html" class="fg-mhs-btn-primary"><i class="fa-solid fa-right-to-bracket"></i> Login</a>
            <a href="register.php" class="fg-mhs-btn-outline"><i class="fa-solid fa-user-plus"></i> Get Started</a>
          </div>
          <div class="fg-mhs-stats">
            <div><strong>500+</strong><span>Repairs Done</span></div>
            <div><strong>4.9★</strong><span>Avg. Rating</span></div>
            <div><strong>24h</strong><span>Turnaround</span></div>
            <div><strong>90d</strong><span>Warranty</span></div>
          </div>
        </div>
      </div>
    </div>
    <style>
      /* Mobile-only static hero — always renders regardless of JS/login state */
      .fg-mobile-hero-static {
        display: none; /* hidden by default — shown only on mobile */
        background: linear-gradient(135deg, #0a0a0a 0%, #111 50%, #1a1200 100%);
        border-radius: 16px;
        padding: 1.75rem 1.25rem 1.5rem;
        margin-bottom: 1rem;
        position: relative;
        overflow: hidden;
        border: 1px solid rgba(230,168,0,0.15);
      }
      .fg-mhs-badge {
        display: inline-flex;
        align-items: center;
        font-size: 0.65rem;
        font-weight: 800;
        letter-spacing: 2px;
        text-transform: uppercase;
        color: #e6a800;
        background: rgba(230,168,0,0.1);
        border: 1px solid rgba(230,168,0,0.25);
        padding: 0.25rem 0.75rem;
        border-radius: 50px;
        margin-bottom: 0.9rem;
      }
      .fg-mhs-title {
        font-size: clamp(1.55rem, 6vw, 2.2rem);
        font-weight: 900;
        color: #fff;
        line-height: 1.2;
        margin: 0 0 0.75rem;
        letter-spacing: -0.5px;
      }
      .fg-mhs-title span { color: #e6a800; }
      .fg-mhs-title em   { color: #e6a800; font-style: normal; }
      .fg-mhs-sub {
        font-size: 0.85rem;
        color: rgba(255,255,255,0.6);
        line-height: 1.65;
        margin: 0 0 1.1rem;
      }
      .fg-mhs-btns {
        display: flex;
        gap: 0.65rem;
        flex-wrap: wrap;
        margin-bottom: 1.1rem;
      }
      .fg-mhs-btn-primary {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        padding: 0.65rem 1.4rem;
        border-radius: 50px;
        background: linear-gradient(135deg, #e6a800, #c98f00);
        color: #000;
        font-size: 0.88rem;
        font-weight: 800;
        text-decoration: none;
        box-shadow: 0 4px 18px rgba(230,168,0,0.38);
      }
      .fg-mhs-btn-outline {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        padding: 0.65rem 1.4rem;
        border-radius: 50px;
        background: transparent;
        color: #fff;
        font-size: 0.88rem;
        font-weight: 700;
        text-decoration: none;
        border: 1.5px solid rgba(255,255,255,0.35);
      }
      .fg-mhs-stats {
        display: flex;
        gap: 1.1rem;
        flex-wrap: wrap;
        padding-top: 1rem;
        border-top: 1px solid rgba(255,255,255,0.08);
      }
      .fg-mhs-stats div { display: flex; flex-direction: column; }
      .fg-mhs-stats strong {
        font-size: 1.15rem;
        font-weight: 900;
        color: #e6a800;
        line-height: 1;
      }
      .fg-mhs-stats span {
        font-size: 0.6rem;
        color: rgba(255,255,255,0.38);
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-top: 0.15rem;
      }
      /* Show ONLY on mobile (≤991px) AND only when NOT logged in */
      @media (max-width: 991px) {
        .fg-mobile-hero-static { display: block !important; }
        /* Hide the JS-controlled hero divs on mobile to avoid duplication */
        #heroLoggedOut { display: none !important; }
      }
      /* Hide for logged-in users — JS adds fg-logged-in to body */
      body.fg-logged-in .fg-mobile-hero-static { display: none !important; }
      /* On desktop, hide this and let the normal hero show */
      @media (min-width: 992px) {
        .fg-mobile-hero-static { display: none !important; }
      }
    </style>

    <div class="container" style="padding-top:0;padding-bottom:0;">

      <!-- Logged-out state (shown by default; JS hides it if logged in) -->

      <div id="heroLoggedOut" style="display:block !important;position:relative;background:transparent;isolation:isolate;padding-top:0.5rem;padding-bottom:1.5rem;">

        <!-- LEFT: Text content -->
        <div class="hero-content" style="position:relative;z-index:2;width:100%;max-width:560px;">

          <div class="hero-label">Fast &bull; Reliable &bull; Affordable</div>

          <h1 class="hero-headline">Fix&amp;Go &ndash; Your Trusted<br /><span>Phone Repair</span> &amp; Shop</h1>

          <p class="hero-subtext">Professional phone repairs done right the first time. From cracked screens to battery issues, our certified technicians get your device back in perfect shape &mdash; fast.</p>

          <div class="hero-buttons">
            <a href="login.html" id="heroLoginBtn" class="btn-hero-login"><i class="fa-solid fa-right-to-bracket"></i> Login</a>
            <a href="register.php" id="heroRegisterBtn" class="btn-hero-register"><i class="fa-solid fa-user-plus"></i> Get Started</a>
          </div>

          <!-- Stats row -->
          <div class="hero-stats">
            <div class="hero-stat">
              <span class="hero-stat-val">500+</span>
              <span class="hero-stat-lbl">Repairs Done</span>
            </div>
            <div class="hero-stat">
              <span class="hero-stat-val">4.9★</span>
              <span class="hero-stat-lbl">Avg. Rating</span>
            </div>
            <div class="hero-stat">
              <span class="hero-stat-val">24h</span>
              <span class="hero-stat-lbl">Turnaround</span>
            </div>
            <div class="hero-stat">
              <span class="hero-stat-val">90d</span>
              <span class="hero-stat-lbl">Warranty</span>
            </div>
          </div>
        </div>

        <!-- RIGHT: Visual design panel -->
        <div class="hero-right" style="right:0;top:50%;transform:translateY(-50%);display:flex;flex-direction:column;gap:1rem;align-items:flex-end;">

          <!-- Main visual card -->
          <div style="background:rgba(230,168,0,0.06);border:1px solid rgba(230,168,0,0.18);border-radius:24px;padding:2rem 1.75rem;width:320px;position:relative;overflow:hidden;backdrop-filter:blur(12px);">
            <!-- Glow blob -->
            <div style="position:absolute;top:-40px;right:-40px;width:160px;height:160px;border-radius:50%;background:radial-gradient(circle,rgba(230,168,0,0.18),transparent 70%);pointer-events:none;"></div>

            <!-- Phone icon ring -->
            <div style="width:80px;height:80px;border-radius:50%;background:linear-gradient(135deg,rgba(230,168,0,0.2),rgba(230,168,0,0.05));border:2px solid rgba(230,168,0,0.3);display:flex;align-items:center;justify-content:center;font-size:2.2rem;margin-bottom:1.25rem;position:relative;">
              📱
              <!-- Pulse ring -->
              <div style="position:absolute;inset:-8px;border-radius:50%;border:2px solid rgba(230,168,0,0.15);animation:heroPulse 2s ease-in-out infinite;"></div>
            </div>

            <div style="font-size:1.1rem;font-weight:800;color:var(--text-primary);margin-bottom:0.3rem;">Expert Repairs</div>
            <div style="font-size:0.8rem;color:var(--text-secondary);line-height:1.6;margin-bottom:1.25rem;">All major brands · Genuine parts · Same-day service</div>

            <!-- Mini service pills -->
            <div style="display:flex;flex-wrap:wrap;gap:0.4rem;margin-bottom:1.25rem;">
              <span style="font-size:0.65rem;font-weight:700;padding:0.25rem 0.65rem;border-radius:50px;background:rgba(230,168,0,0.12);border:1px solid rgba(230,168,0,0.25);color:var(--orange);">🔨 Screen Fix</span>
              <span style="font-size:0.65rem;font-weight:700;padding:0.25rem 0.65rem;border-radius:50px;background:rgba(230,168,0,0.12);border:1px solid rgba(230,168,0,0.25);color:var(--orange);">🔋 Battery</span>
              <span style="font-size:0.65rem;font-weight:700;padding:0.25rem 0.65rem;border-radius:50px;background:rgba(230,168,0,0.12);border:1px solid rgba(230,168,0,0.25);color:var(--orange);">💧 Water Damage</span>
              <span style="font-size:0.65rem;font-weight:700;padding:0.25rem 0.65rem;border-radius:50px;background:rgba(230,168,0,0.12);border:1px solid rgba(230,168,0,0.25);color:var(--orange);">📷 Camera</span>
              <span style="font-size:0.65rem;font-weight:700;padding:0.25rem 0.65rem;border-radius:50px;background:rgba(74,222,128,0.1);border:1px solid rgba(74,222,128,0.2);color:#4ade80;">⚡ Fast</span>
            </div>

            <!-- Progress bars showing brand coverage -->
            <div style="font-size:0.68rem;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:var(--text-secondary);margin-bottom:0.6rem;">Brand Coverage</div>
            <div style="display:flex;flex-direction:column;gap:0.45rem;">
              <div style="display:flex;align-items:center;gap:0.6rem;">
                <span style="font-size:0.7rem;color:var(--text-secondary);width:60px;flex-shrink:0;">Apple</span>
                <div style="flex:1;height:5px;border-radius:3px;background:rgba(255,255,255,0.08);overflow:hidden;">
                  <div style="width:95%;height:100%;border-radius:3px;background:linear-gradient(90deg,var(--orange),#ffcc44);"></div>
                </div>
                <span style="font-size:0.65rem;color:var(--orange);font-weight:700;">95%</span>
              </div>
              <div style="display:flex;align-items:center;gap:0.6rem;">
                <span style="font-size:0.7rem;color:var(--text-secondary);width:60px;flex-shrink:0;">Samsung</span>
                <div style="flex:1;height:5px;border-radius:3px;background:rgba(255,255,255,0.08);overflow:hidden;">
                  <div style="width:90%;height:100%;border-radius:3px;background:linear-gradient(90deg,#3b82f6,#60a5fa);"></div>
                </div>
                <span style="font-size:0.65rem;color:#60a5fa;font-weight:700;">90%</span>
              </div>
              <div style="display:flex;align-items:center;gap:0.6rem;">
                <span style="font-size:0.7rem;color:var(--text-secondary);width:60px;flex-shrink:0;">Others</span>
                <div style="flex:1;height:5px;border-radius:3px;background:rgba(255,255,255,0.08);overflow:hidden;">
                  <div style="width:80%;height:100%;border-radius:3px;background:linear-gradient(90deg,#8b5cf6,#a78bfa);"></div>
                </div>
                <span style="font-size:0.65rem;color:#a78bfa;font-weight:700;">80%</span>
              </div>
            </div>
          </div>

          <!-- Floating review badge -->
          <div style="background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.1);border-radius:14px;padding:0.75rem 1rem;width:280px;display:flex;align-items:center;gap:0.75rem;animation:heroFloat 3s ease-in-out infinite;">
            <div style="width:40px;height:40px;border-radius:50%;background:linear-gradient(135deg,rgba(74,222,128,0.2),rgba(74,222,128,0.05));border:2px solid rgba(74,222,128,0.3);display:flex;align-items:center;justify-content:center;font-size:1.1rem;flex-shrink:0;">⭐</div>
            <div>
              <div style="font-size:0.78rem;font-weight:700;color:var(--text-primary);">4.9 / 5 Customer Rating</div>
              <div style="font-size:0.65rem;color:var(--text-secondary);margin-top:0.1rem;">Based on 200+ verified reviews</div>
            </div>
          </div>

          <!-- Floating warranty badge -->
          <div style="background:rgba(230,168,0,0.08);border:1px solid rgba(230,168,0,0.2);border-radius:14px;padding:0.65rem 1rem;width:240px;display:flex;align-items:center;gap:0.65rem;animation:heroFloat 3s ease-in-out 1s infinite;margin-right:1rem;">
            <div style="width:34px;height:34px;border-radius:10px;background:rgba(230,168,0,0.15);display:flex;align-items:center;justify-content:center;font-size:0.95rem;flex-shrink:0;">🛡️</div>
            <div>
              <div style="font-size:0.73rem;font-weight:800;color:var(--orange);">90-Day Warranty</div>
              <div style="font-size:0.62rem;color:var(--text-secondary);">On all repairs — guaranteed</div>
            </div>
          </div>

        </div><!-- /hero-right -->

      </div>

      <!-- Logged-in marketplace home -->

      <div id="heroMarketplace" style="display:none;background:var(--bg,#0d0d0d);position:relative;z-index:1;isolation:isolate;transform:translateZ(0);min-height:280px;">

        <!-- Welcome strip -->
        <div style="margin-bottom:1rem;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:0.5rem;">
          <div>
            <h2 id="mktWelcomeTitle" style="font-size:1.05rem;font-weight:800;color:var(--text-primary);margin:0 0 0.1rem;">Welcome back! 👋</h2>
            <p style="font-size:0.78rem;color:var(--text-secondary);margin:0;">Browse accessories, book repairs, and manage your orders.</p>
          </div>
          <div style="display:flex;gap:0.4rem;flex-shrink:0;">
            <span style="font-size:0.62rem;font-weight:700;color:#e6a800;background:rgba(230,168,0,0.1);border:1px solid rgba(230,168,0,0.2);padding:0.18rem 0.55rem;border-radius:50px;">🔥 Hot Items</span>
            <span style="font-size:0.62rem;font-weight:700;color:#4ade80;background:rgba(74,222,128,0.1);border:1px solid rgba(74,222,128,0.2);padding:0.18rem 0.55rem;border-radius:50px;">🛡️ 90-Day Warranty</span>
          </div>
        </div>

        <!-- SHOP BANNER STRIP above carousel -->
        <div style="display:flex;align-items:center;justify-content:space-between;gap:0.75rem;margin-bottom:0.75rem;flex-wrap:wrap;">
          <div style="display:flex;align-items:center;gap:0.6rem;">
            <div style="width:8px;height:8px;border-radius:50%;background:#e6a800;box-shadow:0 0 8px rgba(230,168,0,0.6);"></div>
            <span style="font-size:0.7rem;font-weight:800;letter-spacing:2px;text-transform:uppercase;color:var(--text-secondary);">Today's Deals</span>
          </div>
          <span style="font-size:0.65rem;font-weight:700;color:#60a5fa;background:rgba(96,165,250,0.1);border:1px solid rgba(96,165,250,0.2);padding:0.2rem 0.6rem;border-radius:50px;">🚚 Free Delivery</span>
        </div>

        <div id="mktBannerRow" style="position:relative;border-radius:16px;overflow:hidden;margin-bottom:1.5rem;height:240px;min-height:200px;background:linear-gradient(135deg,#0a0020,#1a0040);isolation:isolate;">
          <!-- Loading placeholder shown until initBanners() fills the slides -->
          <div id="mktBannerLoader" style="position:absolute;inset:0;display:flex;flex-direction:column;align-items:center;justify-content:center;z-index:2;background:linear-gradient(135deg,#0a0020,#1a0040,#2d0060);">
            <div style="font-size:2.5rem;margin-bottom:0.75rem;">🛠️</div>
            <div style="font-size:1rem;font-weight:800;color:#fff;margin-bottom:0.25rem;">Fix<span style="color:#e6a800;">&amp;Go</span> Shop</div>
            <div style="font-size:0.75rem;color:rgba(255,255,255,0.5);">Repairs · Parts · Fast Service</div>
            <a href="#shop" style="margin-top:1rem;padding:0.45rem 1.4rem;border-radius:50px;background:#e6a800;color:#000;font-size:0.78rem;font-weight:800;text-decoration:none;">Browse Shop →</a>
          </div>
          <div id="mktBannerSlides" style="width:100%;height:100%;min-height:200px;position:relative;overflow:hidden;border-radius:16px;"></div>
          <button onclick="bannerSlide(-1)" style="position:absolute;left:0.75rem;top:50%;transform:translateY(-50%);width:38px;height:38px;border-radius:50%;background:rgba(0,0,0,0.55);border:none;color:#fff;cursor:pointer;font-size:1.2rem;display:flex;align-items:center;justify-content:center;z-index:5;transition:background 0.2s;" onmouseenter="this.style.background='rgba(0,0,0,0.8)'" onmouseleave="this.style.background='rgba(0,0,0,0.55)'">&#8249;</button>
          <button onclick="bannerSlide(1)"  style="position:absolute;right:0.75rem;top:50%;transform:translateY(-50%);width:38px;height:38px;border-radius:50%;background:rgba(0,0,0,0.55);border:none;color:#fff;cursor:pointer;font-size:1.2rem;display:flex;align-items:center;justify-content:center;z-index:5;transition:background 0.2s;" onmouseenter="this.style.background='rgba(0,0,0,0.8)'" onmouseleave="this.style.background='rgba(0,0,0,0.55)'">&#8250;</button>
          <div id="mktBannerDots" style="position:absolute;bottom:0.75rem;left:50%;transform:translateX(-50%);display:flex;gap:6px;z-index:5;"></div>
        </div>

        <!-- CATEGORY SHORTCUTS -->
        <div style="margin-bottom:1.5rem;">
          <div id="mktCategoryRow" style="display:flex;gap:1rem;overflow-x:auto;padding-bottom:0.5rem;scrollbar-width:none;-ms-overflow-style:none;"></div>
        </div>

        <!-- TECHNICIAN SPOTLIGHT BANNER — CTA strip to book a technician -->
        <div style="border-radius:16px;overflow:hidden;margin-bottom:1.25rem;background:linear-gradient(135deg,#0d1a0d,#0a2010,#0d2d1a);border:1px solid rgba(74,222,128,0.15);padding:1.25rem 1.75rem;display:flex;align-items:center;justify-content:space-between;gap:1rem;flex-wrap:wrap;">
          <div style="display:flex;align-items:center;gap:1rem;">
            <div style="width:52px;height:52px;border-radius:14px;background:rgba(74,222,128,0.12);border:1.5px solid rgba(74,222,128,0.25);display:flex;align-items:center;justify-content:center;font-size:1.6rem;flex-shrink:0;">🔧</div>
            <div>
              <div style="font-size:0.6rem;font-weight:800;letter-spacing:2px;text-transform:uppercase;color:#4ade80;margin-bottom:0.2rem;">CERTIFIED EXPERTS</div>
              <div style="font-size:1.05rem;font-weight:900;color:#fff;line-height:1.2;">Book a Repair Technician</div>
              <div style="font-size:0.75rem;color:rgba(255,255,255,0.5);margin-top:0.15rem;">Fast · Reliable · Affordable service at your doorstep</div>
            </div>
          </div>
          <div style="display:flex;gap:0.6rem;align-items:center;flex-wrap:wrap;">
            <div style="display:flex;align-items:center;gap:0.4rem;font-size:0.7rem;color:rgba(255,255,255,0.5);background:rgba(255,255,255,0.05);border:1px solid rgba(74,222,128,0.15);padding:0.3rem 0.7rem;border-radius:50px;">⭐ Top Rated</div>
            <div style="display:flex;align-items:center;gap:0.4rem;font-size:0.7rem;color:rgba(255,255,255,0.5);background:rgba(255,255,255,0.05);border:1px solid rgba(74,222,128,0.15);padding:0.3rem 0.7rem;border-radius:50px;">🛡️ Verified</div>
            <a href="#technicians" style="display:inline-flex;align-items:center;gap:0.4rem;padding:0.5rem 1.2rem;border-radius:50px;background:#4ade80;color:#000;font-size:0.78rem;font-weight:800;text-decoration:none;transition:all 0.2s;white-space:nowrap;" onmouseenter="this.style.background='#22c55e';this.style.transform='translateY(-1px)'" onmouseleave="this.style.background='#4ade80';this.style.transform=''">Book Now →</a>
          </div>
        </div>

        <!-- AD BANNER STRIP -->
        <div id="mktAdStrip" style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:0.75rem;margin-bottom:2rem;">

          <!-- Banner 1: New Tech / Purple neon style -->
          <a href="#shop" style="text-decoration:none;display:block;border-radius:14px;overflow:hidden;position:relative;height:110px;background:linear-gradient(135deg,#1a0030,#3d0070,#6600cc);box-shadow:0 4px 20px rgba(102,0,204,0.4);transition:transform 0.2s,box-shadow 0.2s;" onmouseenter="this.style.transform='translateY(-3px)';this.style.boxShadow='0 8px 28px rgba(102,0,204,0.6)'" onmouseleave="this.style.transform='';this.style.boxShadow='0 4px 20px rgba(102,0,204,0.4)'">
            <!-- Neon glow lines -->
            <div style="position:absolute;top:0;left:0;right:0;bottom:0;background:repeating-linear-gradient(45deg,transparent,transparent 20px,rgba(192,132,252,0.04) 20px,rgba(192,132,252,0.04) 21px);pointer-events:none;"></div>
            <!-- Triangle decorations -->
            <div style="position:absolute;top:8px;right:60px;width:0;height:0;border-left:12px solid transparent;border-right:12px solid transparent;border-bottom:20px solid rgba(192,132,252,0.3);"></div>
            <div style="position:absolute;bottom:10px;right:20px;width:0;height:0;border-left:8px solid transparent;border-right:8px solid transparent;border-top:14px solid rgba(192,132,252,0.2);"></div>
            <!-- Left icon -->
            <div style="position:absolute;left:-8px;top:50%;transform:translateY(-50%);font-size:3.5rem;opacity:0.25;pointer-events:none;">🎧</div>
            <!-- Right icon -->
            <div style="position:absolute;right:4px;top:50%;transform:translateY(-50%);font-size:2.8rem;opacity:0.3;pointer-events:none;">⌚</div>
            <!-- Text -->
            <div style="position:relative;z-index:2;padding:0.9rem 1rem 0.9rem 3.5rem;">
              <div style="font-size:0.55rem;font-weight:800;letter-spacing:2px;text-transform:uppercase;color:#c084fc;margin-bottom:0.2rem;">NEW ARRIVALS</div>
              <div style="font-size:1rem;font-weight:900;color:#fff;line-height:1.1;text-shadow:0 0 12px rgba(192,132,252,0.6);">NEW TECH</div>
              <div style="font-size:0.6rem;color:rgba(255,255,255,0.55);margin-top:0.2rem;">Latest accessories &amp; gadgets</div>
              <div style="margin-top:0.5rem;display:flex;gap:0.4rem;">
                <span style="font-size:0.5rem;background:rgba(255,255,255,0.1);border:1px solid rgba(192,132,252,0.3);color:rgba(255,255,255,0.7);padding:0.15rem 0.4rem;border-radius:4px;">Free Delivery</span>
                <span style="font-size:0.5rem;background:rgba(255,255,255,0.1);border:1px solid rgba(192,132,252,0.3);color:rgba(255,255,255,0.7);padding:0.15rem 0.4rem;border-radius:4px;">2-Year Warranty</span>
              </div>
            </div>
          </a>

          <!-- Banner 2: Super Sale / Pink pastel style -->
          <a href="#shop" style="text-decoration:none;display:block;border-radius:14px;overflow:hidden;position:relative;height:110px;background:linear-gradient(135deg,#ffe0e6,#ffd6e8,#e8f4ff);box-shadow:0 4px 20px rgba(255,100,150,0.2);transition:transform 0.2s,box-shadow 0.2s;" onmouseenter="this.style.transform='translateY(-3px)';this.style.boxShadow='0 8px 28px rgba(255,100,150,0.35)'" onmouseleave="this.style.transform='';this.style.boxShadow='0 4px 20px rgba(255,100,150,0.2)'">
            <!-- Floating circles -->
            <div style="position:absolute;top:-15px;right:30px;width:60px;height:60px;border-radius:50%;background:rgba(100,200,255,0.25);pointer-events:none;"></div>
            <div style="position:absolute;bottom:-10px;right:80px;width:40px;height:40px;border-radius:50%;background:rgba(100,220,180,0.3);pointer-events:none;"></div>
            <div style="position:absolute;top:20px;right:10px;width:25px;height:25px;border-radius:50%;background:rgba(255,150,200,0.4);pointer-events:none;"></div>
            <!-- Phone icon -->
            <div style="position:absolute;right:12px;top:50%;transform:translateY(-50%);font-size:2.8rem;opacity:0.35;pointer-events:none;">📱</div>
            <!-- Text -->
            <div style="position:relative;z-index:2;padding:0.9rem 1rem;">
              <div style="font-size:0.55rem;font-weight:800;letter-spacing:2px;text-transform:uppercase;color:#e05080;margin-bottom:0.1rem;">LIMITED TIME</div>
              <div style="font-size:1.1rem;font-weight:900;color:#c0304a;line-height:1;">SUPER</div>
              <div style="font-size:1.4rem;font-weight:900;color:#c0304a;line-height:1;margin-bottom:0.2rem;">SALE</div>
              <div style="font-size:0.6rem;color:#888;margin-bottom:0.4rem;">Shop Now</div>
              <div style="display:inline-block;font-size:0.55rem;font-weight:800;background:#c0304a;color:#fff;padding:0.2rem 0.6rem;border-radius:4px;">UP TO 30% OFF</div>
            </div>
          </a>

          <!-- Banner 3: Devices / Yellow + Blue style -->
          <a href="#shop" style="text-decoration:none;display:block;border-radius:14px;overflow:hidden;position:relative;height:110px;background:linear-gradient(135deg,#1a3a6b,#0d2a55,#0a1f40);box-shadow:0 4px 20px rgba(30,80,180,0.35);transition:transform 0.2s,box-shadow 0.2s;" onmouseenter="this.style.transform='translateY(-3px)';this.style.boxShadow='0 8px 28px rgba(30,80,180,0.5)'" onmouseleave="this.style.transform='';this.style.boxShadow='0 4px 20px rgba(30,80,180,0.35)'">
            <!-- Grid pattern -->
            <div style="position:absolute;inset:0;background-image:linear-gradient(rgba(96,165,250,0.06) 1px,transparent 1px),linear-gradient(90deg,rgba(96,165,250,0.06) 1px,transparent 1px);background-size:20px 20px;pointer-events:none;"></div>
            <!-- Icons -->
            <div style="position:absolute;right:8px;top:8px;font-size:2rem;opacity:0.3;pointer-events:none;">💻</div>
            <div style="position:absolute;right:8px;bottom:8px;font-size:1.8rem;opacity:0.25;pointer-events:none;">🎮</div>
            <!-- Text -->
            <div style="position:relative;z-index:2;padding:0.9rem 1rem;">
              <div style="font-size:0.55rem;font-weight:800;letter-spacing:2px;text-transform:uppercase;color:#60a5fa;margin-bottom:0.2rem;">ELECTRONICS</div>
              <div style="font-size:0.85rem;font-weight:900;color:#fff;line-height:1.15;margin-bottom:0.3rem;">SALE<br><span style="color:#60a5fa;">UP TO 20% OFF</span></div>
              <div style="display:inline-flex;align-items:center;gap:0.3rem;font-size:0.6rem;font-weight:700;color:#60a5fa;border:1px solid rgba(96,165,250,0.4);padding:0.2rem 0.5rem;border-radius:4px;">Shop Now →</div>
            </div>
          </a>

        </div>


        <!-- ── CUSTOMER AD BANNERS (logged-in only) ───────────── -->
        <div id="custAdSection">

          <!-- Row 1: 3 quick-promo cards -->
          <div id="custAdCards" style="display:grid;grid-template-columns:repeat(3,1fr);gap:0.75rem;margin-bottom:0.85rem;">

            <!-- Card 1: Flash Sale -->
            <div style="border-radius:14px;overflow:hidden;position:relative;height:100px;background:linear-gradient(135deg,#1a0030,#3d0070,#6600cc);box-shadow:0 4px 18px rgba(102,0,204,0.4);cursor:pointer;transition:transform 0.2s;" onmouseenter="this.style.transform='translateY(-3px)'" onmouseleave="this.style.transform=''">
              <div style="position:absolute;inset:0;background:repeating-linear-gradient(45deg,transparent,transparent 20px,rgba(192,132,252,0.04) 20px,rgba(192,132,252,0.04) 21px);pointer-events:none;"></div>
              <div style="position:absolute;right:6px;top:50%;transform:translateY(-50%);font-size:2.8rem;opacity:0.22;pointer-events:none;">&#9201;</div>
              <div style="position:relative;z-index:2;padding:0.9rem 1rem;">
                <div style="font-size:0.52rem;font-weight:800;letter-spacing:2px;text-transform:uppercase;color:#c084fc;margin-bottom:0.15rem;">FLASH SALE</div>
                <div style="font-size:1rem;font-weight:900;color:#fff;line-height:1.1;text-shadow:0 0 10px rgba(192,132,252,0.5);">TODAY ONLY</div>
                <div style="font-size:0.62rem;color:rgba(255,255,255,0.55);margin-top:0.2rem;">Up to 40% off accessories</div>
                <div style="margin-top:0.4rem;display:inline-block;font-size:0.5rem;background:rgba(192,132,252,0.2);border:1px solid rgba(192,132,252,0.4);color:#e9d5ff;padding:0.15rem 0.45rem;border-radius:4px;">Shop Now &#8250;</div>
              </div>
            </div>

            <!-- Card 2: Book Repair -->
            <div style="border-radius:14px;overflow:hidden;position:relative;height:100px;background:linear-gradient(135deg,#0d1a0d,#0a2010,#0d2d1a);box-shadow:0 4px 18px rgba(74,222,128,0.25);border:1px solid rgba(74,222,128,0.15);cursor:pointer;transition:transform 0.2s;" onmouseenter="this.style.transform='translateY(-3px)'" onmouseleave="this.style.transform=''">
              <div style="position:absolute;right:6px;top:50%;transform:translateY(-50%);font-size:2.5rem;opacity:0.25;pointer-events:none;">&#128295;</div>
              <div style="position:relative;z-index:2;padding:0.9rem 1rem;">
                <div style="font-size:0.52rem;font-weight:800;letter-spacing:2px;text-transform:uppercase;color:#4ade80;margin-bottom:0.15rem;">FREE DIAGNOSIS</div>
                <div style="font-size:1rem;font-weight:900;color:#fff;line-height:1.1;">BOOK REPAIR</div>
                <div style="font-size:0.62rem;color:rgba(255,255,255,0.5);margin-top:0.2rem;">Certified technicians near you</div>
                <div style="margin-top:0.4rem;display:inline-block;font-size:0.5rem;background:rgba(74,222,128,0.15);border:1px solid rgba(74,222,128,0.35);color:#4ade80;padding:0.15rem 0.45rem;border-radius:4px;">Book Now &#8250;</div>
              </div>
            </div>

            <!-- Card 3: New Arrivals -->
            <div style="border-radius:14px;overflow:hidden;position:relative;height:100px;background:linear-gradient(135deg,#1a3a6b,#0d2a55,#0a1f40);box-shadow:0 4px 18px rgba(30,80,180,0.35);cursor:pointer;transition:transform 0.2s;" onmouseenter="this.style.transform='translateY(-3px)'" onmouseleave="this.style.transform=''">
              <div style="position:absolute;inset:0;background-image:linear-gradient(rgba(96,165,250,0.05) 1px,transparent 1px),linear-gradient(90deg,rgba(96,165,250,0.05) 1px,transparent 1px);background-size:18px 18px;pointer-events:none;"></div>
              <div style="position:absolute;right:6px;top:50%;transform:translateY(-50%);font-size:2.5rem;opacity:0.25;pointer-events:none;">&#128241;</div>
              <div style="position:relative;z-index:2;padding:0.9rem 1rem;">
                <div style="font-size:0.52rem;font-weight:800;letter-spacing:2px;text-transform:uppercase;color:#60a5fa;margin-bottom:0.15rem;">NEW ARRIVALS</div>
                <div style="font-size:1rem;font-weight:900;color:#fff;line-height:1.1;">ACCESSORIES</div>
                <div style="font-size:0.62rem;color:rgba(255,255,255,0.5);margin-top:0.2rem;">Cases, cables, chargers &amp; more</div>
                <div style="margin-top:0.4rem;display:inline-flex;align-items:center;gap:0.3rem;font-size:0.5rem;font-weight:700;color:#60a5fa;border:1px solid rgba(96,165,250,0.4);padding:0.15rem 0.5rem;border-radius:4px;">Browse &#8250;</div>
              </div>
            </div>

          </div>

          <!-- Row 2: Wide promo strip — Screen Repair Deal -->
          <div style="border-radius:14px;overflow:hidden;position:relative;margin-bottom:1.5rem;background:linear-gradient(135deg,#0a0a0a 0%,#1a0a00 50%,#0a0a0a 100%);border:1px solid rgba(230,168,0,0.15);padding:1.1rem 1.5rem;display:flex;align-items:center;justify-content:space-between;gap:1rem;flex-wrap:wrap;">
            <div style="position:absolute;top:-60px;left:30%;width:300px;height:200px;border-radius:50%;background:radial-gradient(ellipse,rgba(230,168,0,0.06),transparent 70%);pointer-events:none;"></div>
            <div style="position:relative;z-index:1;">
              <div style="font-size:0.58rem;font-weight:800;letter-spacing:2.5px;text-transform:uppercase;color:var(--orange);margin-bottom:0.3rem;">&#9889; EXCLUSIVE DEAL FOR YOU</div>
              <div style="font-size:1.15rem;font-weight:900;color:#fff;line-height:1.2;margin-bottom:0.3rem;">Screen Crack? <span style="color:var(--orange);">We Fix It Fast.</span></div>
              <div style="font-size:0.75rem;color:rgba(255,255,255,0.5);max-width:380px;line-height:1.55;">Book a screen repair today and get <strong style="color:var(--orange);">free diagnosis</strong> + <strong style="color:#4ade80;">90-day warranty</strong> on all parts &amp; labour.</div>
            </div>
            <div style="display:flex;gap:0.6rem;align-items:center;flex-wrap:wrap;flex-shrink:0;position:relative;z-index:1;">
              <div style="text-align:center;padding:0.5rem 0.75rem;background:rgba(230,168,0,0.08);border:1px solid rgba(230,168,0,0.2);border-radius:10px;">
                <div style="font-size:1.1rem;font-weight:900;color:var(--orange);">FREE</div>
                <div style="font-size:0.6rem;color:rgba(255,255,255,0.45);font-weight:600;">Diagnosis</div>
              </div>
              <div style="text-align:center;padding:0.5rem 0.75rem;background:rgba(74,222,128,0.08);border:1px solid rgba(74,222,128,0.2);border-radius:10px;">
                <div style="font-size:1.1rem;font-weight:900;color:#4ade80;">90d</div>
                <div style="font-size:0.6rem;color:rgba(255,255,255,0.45);font-weight:600;">Warranty</div>
              </div>
              <a href="#technicians" style="display:inline-flex;align-items:center;gap:0.4rem;padding:0.6rem 1.4rem;border-radius:50px;background:linear-gradient(135deg,var(--orange),var(--orange-hover));color:#000;font-size:0.82rem;font-weight:800;text-decoration:none;white-space:nowrap;box-shadow:0 4px 16px rgba(230,168,0,0.3);transition:all 0.2s;" onmouseenter="this.style.transform='translateY(-1px)'" onmouseleave="this.style.transform=''">&#128295; Book Now</a>
            </div>
          </div>

        </div><!-- /custAdSection -->

      </div><!-- /heroMarketplace -->

    </div>

  </section>

  <style>

    @keyframes spin { to { transform:rotate(360deg); } }

    .mkt-banner-slide { position:absolute;inset:0;height:100%;display:block;opacity:0;transition:opacity 0.6s ease;pointer-events:none;overflow:hidden; }
    .mkt-banner-slide.active { opacity:1;pointer-events:auto; }
    @media (max-width:640px) {
      .mkt-banner-slide > div[style*="padding:1.25rem"] {
        padding: 0.85rem 1rem !important;
      }
    }

    .mkt-promo-tile { flex:1;border-radius:12px;background:var(--bg-card);border:1px solid var(--border-color);display:flex;align-items:center;gap:0.75rem;padding:0.75rem 1rem;cursor:pointer;transition:border-color 0.2s,transform 0.2s;text-decoration:none; }

    .mkt-promo-tile:hover { border-color:var(--orange);transform:translateY(-2px); }

    .mkt-promo-tile-icon { width:44px;height:44px;border-radius:10px;flex-shrink:0;background:linear-gradient(135deg,rgba(230,168,0,0.2),rgba(230,168,0,0.06));border:1px solid rgba(230,168,0,0.25);display:flex;align-items:center;justify-content:center;font-size:1.3rem;color:var(--orange); }

    .mkt-promo-tile-label { font-size:0.82rem;font-weight:800;color:var(--text-primary);line-height:1.2; }

    .mkt-promo-tile-sub   { font-size:0.7rem;color:var(--text-secondary); }

    .mkt-cat-pill { display:flex;flex-direction:column;align-items:center;gap:0.4rem;min-width:72px;cursor:pointer;text-decoration:none;transition:transform 0.2s;flex-shrink:0; }

    .mkt-cat-pill:hover { transform:translateY(-3px); }

    .mkt-cat-pill-icon { width:60px;height:60px;border-radius:50%;background:var(--bg-card);border:1.5px solid var(--border-color);display:flex;align-items:center;justify-content:center;font-size:1.5rem;transition:border-color 0.2s,background 0.2s; }

    .mkt-cat-pill:hover .mkt-cat-pill-icon { border-color:var(--orange);background:rgba(230,168,0,0.08); }

    .mkt-cat-pill-label { font-size:0.7rem;font-weight:700;color:var(--text-secondary);text-align:center;white-space:nowrap; }

    .mkt-feat-card { background:var(--bg-card);border:1px solid var(--border-color);border-radius:14px;overflow:hidden;transition:transform 0.22s,box-shadow 0.22s,border-color 0.22s;display:flex;flex-direction:column;cursor:pointer; }

    .mkt-feat-card:hover { transform:translateY(-5px);box-shadow:0 12px 32px rgba(230,168,0,0.15);border-color:var(--orange); }

    .mkt-feat-img { width:100%;aspect-ratio:1/1;object-fit:cover;background:var(--bg); }

    .mkt-feat-img-ph { width:100%;aspect-ratio:1/1;background:linear-gradient(135deg,#1e1e1e,#141414);display:flex;flex-direction:column;align-items:center;justify-content:center;gap:0.4rem;position:relative;overflow:hidden; }
    .mkt-feat-img-ph::before { content:'';position:absolute;inset:0;background:repeating-linear-gradient(45deg,transparent,transparent 12px,rgba(230,168,0,0.03) 12px,rgba(230,168,0,0.03) 13px); }
    .mkt-feat-img-ph-icon { width:52px;height:52px;border-radius:12px;background:rgba(230,168,0,0.08);border:1.5px solid rgba(230,168,0,0.15);display:flex;align-items:center;justify-content:center;position:relative;z-index:1; }
    .mkt-feat-img-ph-label { font-size:0.58rem;font-weight:700;color:var(--text-secondary);text-transform:uppercase;letter-spacing:1px;position:relative;z-index:1; }

    .mkt-feat-body { padding:0.75rem 0.9rem 0.9rem;flex:1;display:flex;flex-direction:column; }

    .mkt-feat-shop { font-size:0.65rem;font-weight:700;color:#3b82f6;background:rgba(59,130,246,0.1);border:1px solid rgba(59,130,246,0.2);padding:0.1rem 0.45rem;border-radius:50px;display:inline-block;margin-bottom:0.3rem;max-width:fit-content; }

    .mkt-feat-cat  { font-size:0.65rem;font-weight:700;color:var(--orange);background:rgba(230,168,0,0.1);border:1px solid rgba(230,168,0,0.2);padding:0.1rem 0.45rem;border-radius:50px;display:inline-block;margin-bottom:0.35rem; }

    .mkt-feat-name { font-size:0.8rem;font-weight:700;color:var(--text-primary);line-height:1.3;margin-bottom:0.25rem;flex:1;display:-webkit-box;-webkit-line-clamp:2;line-clamp:2;-webkit-box-orient:vertical;overflow:hidden; }

    .mkt-feat-price { font-size:0.95rem;font-weight:900;color:var(--orange);margin-top:auto; }

    .mkt-tech-card { background:var(--bg-card);border:1px solid var(--border-color);border-radius:16px;padding:1.25rem 1rem;text-align:center;transition:transform 0.22s,box-shadow 0.22s,border-color 0.22s;cursor:pointer;position:relative;overflow:hidden; }

    .mkt-tech-card::before { content:'';position:absolute;top:0;left:0;right:0;height:3px;background:linear-gradient(90deg,var(--orange),#c98f00);opacity:0;transition:opacity 0.22s; }

    .mkt-tech-card:hover { transform:translateY(-5px);box-shadow:0 12px 32px rgba(230,168,0,0.14);border-color:var(--orange); }

    .mkt-tech-card:hover::before { opacity:1; }

    .mkt-tech-avatar { width:72px;height:72px;border-radius:50%;margin:0 auto 0.75rem;border:3px solid var(--border-color);object-fit:cover;display:block;transition:border-color 0.22s; }

    .mkt-tech-card:hover .mkt-tech-avatar { border-color:var(--orange); }

    .mkt-tech-avatar-ph { width:72px;height:72px;border-radius:50%;margin:0 auto 0.75rem;border:3px solid var(--border-color);background:linear-gradient(135deg,rgba(230,168,0,0.18),rgba(230,168,0,0.06));display:flex;align-items:center;justify-content:center;font-size:1.8rem;color:var(--orange);transition:border-color 0.22s; }

    .mkt-tech-card:hover .mkt-tech-avatar-ph { border-color:var(--orange); }

    .mkt-tech-name { font-size:0.9rem;font-weight:800;color:var(--text-primary);margin-bottom:0.2rem; }

    .mkt-tech-role { display:inline-block;font-size:0.65rem;font-weight:700;color:var(--orange);background:rgba(230,168,0,0.1);border:1px solid rgba(230,168,0,0.2);padding:0.12rem 0.55rem;border-radius:50px;margin-bottom:0.5rem; }

    .mkt-tech-shop { font-size:0.72rem;color:var(--text-secondary);margin-bottom:0.5rem; }

    .mkt-tech-specs { display:flex;flex-wrap:wrap;justify-content:center;gap:0.3rem;margin-bottom:0.6rem; }

    .mkt-tech-spec  { font-size:0.62rem;font-weight:600;color:var(--text-secondary);background:var(--bg);border:1px solid var(--border-color);padding:0.1rem 0.45rem;border-radius:50px; }

    .mkt-tech-cta   { margin-top:0.5rem;padding:0.4rem 0.9rem;border-radius:8px;background:rgba(230,168,0,0.1);border:1.5px solid rgba(230,168,0,0.3);color:var(--orange);font-size:0.75rem;font-weight:700;display:inline-flex;align-items:center;gap:0.35rem;transition:all 0.2s;cursor:pointer; }

    .mkt-tech-card:hover .mkt-tech-cta { background:var(--orange);border-color:var(--orange);color:#000; }

    @media(max-width:640px) { #mktBannerRow { height:180px !important; overflow:hidden !important; } #mktBannerSlides { overflow:hidden !important; } #mktAdStrip { grid-template-columns:1fr !important; } }

    /* Mobile: hide heavy ad blocks, show compact pill strip instead */
    @media (max-width: 991px) {
      #mktAdStrip     { display: none !important; }
      #custAdSection  { display: none !important; }
      #mobileAdStrip  { display: block !important; }
    }
    @media (min-width: 992px) {
      #mobileAdStrip  { display: none !important; }
    }

  </style>

<div id="guestOnlyContent">

  <!-- ── TRUST TICKER BAR ───────────────────────────────────── -->
  <div class="trust-ticker">
    <div class="ticker-track">
      <span class="ticker-item">🔧 Professional Repairs<span class="ticker-dot"></span></span>
      <span class="ticker-item">✅ 500+ Happy Customers<span class="ticker-dot"></span></span>
      <span class="ticker-item">🛡️ 90-Day Warranty<span class="ticker-dot"></span></span>
      <span class="ticker-item">⚡ Same-Day Service<span class="ticker-dot"></span></span>
      <span class="ticker-item">📱 All Major Brands<span class="ticker-dot"></span></span>
      <span class="ticker-item">🔋 Battery Replacement<span class="ticker-dot"></span></span>
      <span class="ticker-item">💧 Water Damage Repair<span class="ticker-dot"></span></span>
      <span class="ticker-item">📷 Camera Fix<span class="ticker-dot"></span></span>
      <span class="ticker-item">🏆 4.9★ Rated<span class="ticker-dot"></span></span>
      <span class="ticker-item">🚀 Free Diagnosis<span class="ticker-dot"></span></span>
      <span class="ticker-item">🔧 Professional Repairs<span class="ticker-dot"></span></span>
      <span class="ticker-item">✅ 500+ Happy Customers<span class="ticker-dot"></span></span>
      <span class="ticker-item">🛡️ 90-Day Warranty<span class="ticker-dot"></span></span>
      <span class="ticker-item">⚡ Same-Day Service<span class="ticker-dot"></span></span>
      <span class="ticker-item">📱 All Major Brands<span class="ticker-dot"></span></span>
      <span class="ticker-item">🔋 Battery Replacement<span class="ticker-dot"></span></span>
      <span class="ticker-item">💧 Water Damage Repair<span class="ticker-dot"></span></span>
      <span class="ticker-item">📷 Camera Fix<span class="ticker-dot"></span></span>
      <span class="ticker-item">🏆 4.9★ Rated<span class="ticker-dot"></span></span>
      <span class="ticker-item">🚀 Free Diagnosis<span class="ticker-dot"></span></span>
    </div>
  </div>

  <section class="services-section" id="services">
    <div class="container">
      <div class="section-label">What We Offer</div>
      <h2 class="section-heading">Our Repair Services</h2>
      <p class="services-subtitle">Expert solutions for every phone problem — fast, affordable, and guaranteed.</p>
      <!-- Desktop: Bootstrap grid | Mobile: swipe track -->
      <div class="svc-grid">
        <div class="svc-item">
          <div class="service-card">
            <div class="service-icon-wrap"><i class="fa-solid fa-mobile-screen-button"></i></div>
            <h5>Screen Repair</h5>
            <p>Cracked or unresponsive screen? We replace displays for all major brands using high-quality panels that restore your phone's original look and touch sensitivity.</p>
            <span class="service-badge">Starting at &#8369;499</span>
          </div>
        </div>
        <div class="svc-item">
          <div class="service-card">
            <div class="service-icon-wrap"><i class="fa-solid fa-battery-full"></i></div>
            <h5>Battery Replacement</h5>
            <p>Is your phone dying too fast or not charging properly? We swap out worn batteries with genuine replacements so you get a full day's charge again.</p>
            <span class="service-badge">Starting at &#8369;299</span>
          </div>
        </div>
        <div class="svc-item">
          <div class="service-card">
            <div class="service-icon-wrap"><i class="fa-solid fa-droplet-slash"></i></div>
            <h5>Water Damage</h5>
            <p>Dropped your phone in water? Don't panic. Our technicians perform thorough diagnostics and deep cleaning to recover water-damaged devices whenever possible.</p>
            <span class="service-badge">Free Diagnosis</span>
          </div>
        </div>
      </div>
      <div class="swipe-dots" id="svcDots">
        <span class="swipe-dot active"></span>
        <span class="swipe-dot"></span>
        <span class="swipe-dot"></span>
      </div>
    </div>
  </section>

  <!-- =====================

       PHONE ACCESSORIES SHOP SECTION

  ===================== -->


  <!-- =====================
       HOW IT WORKS
  ===================== -->
  <!-- =====================
       HOW IT WORKS
  ===================== -->
  <!-- ===================== HOW IT WORKS ===================== -->
  <section class="how-section">
    <div class="container">
      <div style="text-align:center;margin-bottom:2rem;">
        <div class="section-label">Simple Process</div>
        <h2 class="section-heading">How It Works</h2>
        <p style="color:var(--text-secondary);font-size:1rem;max-width:520px;margin:0.75rem auto 0;">Getting your phone fixed has never been easier.</p>
      </div>
      <div class="how-grid">
        <div class="how-item"><div class="step-card"><span class="step-icon">&#128269;</span><div class="step-number">1</div><h5>Find a Technician</h5><p>Browse certified technicians by specialization, location, and rating.</p></div></div>
        <div class="how-item"><div class="step-card"><span class="step-icon">&#128197;</span><div class="step-number">2</div><h5>Book a Repair</h5><p>Choose In-Shop or Home Service, describe the issue, and pick a schedule.</p></div></div>
        <div class="how-item"><div class="step-card"><span class="step-icon">&#128295;</span><div class="step-number">3</div><h5>Get It Fixed</h5><p>The technician repairs your device and sends photo or video proof when done.</p></div></div>
        <div class="how-item"><div class="step-card"><span class="step-icon">&#11088;</span><div class="step-number">4</div><h5>Rate &amp; Review</h5><p>Leave a review to help others find the best-rated technicians near them.</p></div></div>
      </div>
      <div class="swipe-dots" id="howDots">
        <span class="swipe-dot active"></span>
        <span class="swipe-dot"></span>
        <span class="swipe-dot"></span>
        <span class="swipe-dot"></span>
      </div>
    </div>
  </section>

</div><!-- /guestOnlyContent -->

  <section class="shop-section" id="shop">

    <div class="container">

      <div class="section-label">Phone Accessories</div>

      <h2 class="section-heading">Our Accessories Shop</h2>

      <p class="shop-subtitle">Browse our curated collection of phone accessories â€” cases, cables, chargers, tempered glass, and more.</p>

      <!-- Shop search row -->
      <div id="shopBannerContainer" style="display:none;"></div>

      <!-- Category filter tabs -->

      <div id="shopFilterRow" style="margin-bottom:1.25rem;">
        <!-- Category tabs — swipeable on mobile -->
        <div class="category-tabs" id="categoryTabs" style="margin-bottom:0.65rem;">
          <button class="cat-tab active" data-cat="all">All</button>
        </div>
        <!-- Search -->
        <input type="text" class="shop-search" id="shopSearch" placeholder="🔍  Search accessories…" style="width:100%;">
      </div>

      <!-- Compact mobile ad strip — shown only on mobile, replaces the full ad blocks -->
      <div id="mobileAdStrip" style="display:none;overflow-x:auto;white-space:nowrap;scrollbar-width:none;-ms-overflow-style:none;margin-bottom:1rem;padding-bottom:0.25rem;">
        <a href="#technicians" style="display:inline-flex;align-items:center;gap:0.35rem;padding:0.4rem 0.85rem;border-radius:50px;background:linear-gradient(135deg,#e6a800,#c98f00);color:#000;font-size:0.72rem;font-weight:800;text-decoration:none;margin-right:0.5rem;flex-shrink:0;">🔧 Book Repair</a>
        <a href="#shop" style="display:inline-flex;align-items:center;gap:0.35rem;padding:0.4rem 0.85rem;border-radius:50px;background:rgba(230,168,0,0.1);border:1px solid rgba(230,168,0,0.3);color:#e6a800;font-size:0.72rem;font-weight:700;text-decoration:none;margin-right:0.5rem;flex-shrink:0;">🛒 Shop Parts</a>
        <span style="display:inline-flex;align-items:center;gap:0.35rem;padding:0.4rem 0.85rem;border-radius:50px;background:rgba(74,222,128,0.08);border:1px solid rgba(74,222,128,0.2);color:#4ade80;font-size:0.72rem;font-weight:700;margin-right:0.5rem;flex-shrink:0;">🛡️ 90-Day Warranty</span>
        <span style="display:inline-flex;align-items:center;gap:0.35rem;padding:0.4rem 0.85rem;border-radius:50px;background:rgba(96,165,250,0.08);border:1px solid rgba(96,165,250,0.2);color:#60a5fa;font-size:0.72rem;font-weight:700;margin-right:0.5rem;flex-shrink:0;">🚚 Free Delivery</span>
        <span style="display:inline-flex;align-items:center;gap:0.35rem;padding:0.4rem 0.85rem;border-radius:50px;background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.1);color:rgba(255,255,255,0.6);font-size:0.72rem;font-weight:700;flex-shrink:0;">⚡ Same-Day Service</span>
      </div>

      <!-- Product grid -->

      <div id="shopProductsContainer">

        <div class="shop-loading">

          <div class="spinner"></div>

          Loading products…

        </div>

      </div>

    </div>

  </section>

  <!-- =====================

       PHONE TECHNICIANS SECTION

  ===================== -->


  <!-- =====================
       PROMO BANNER
  ===================== -->
  <div class="promo-banner">
    <div class="container" style="position:relative;z-index:2;">
      <div class="row align-items-center g-5">
        <div class="col-lg-8">
          <div style="font-size:0.7rem;font-weight:800;letter-spacing:3px;text-transform:uppercase;color:var(--orange);margin-bottom:0.6rem;">LIMITED OFFER</div>
          <h2 style="font-size:clamp(1.6rem,3.5vw,2.4rem);font-weight:900;color:#fff;margin-bottom:0.75rem;line-height:1.2;">Free Diagnosis on Your<br><span style="color:var(--orange);">First Repair Booking</span></h2>
          <p style="color:rgba(255,255,255,0.55);font-size:1rem;max-width:500px;line-height:1.75;margin-bottom:1.5rem;">Book your first repair today and get a free device diagnosis &mdash; no hidden fees, no commitments. Our certified technicians will assess your device and give you a full quote before any work begins.</p>
          <div style="display:flex;gap:0.75rem;flex-wrap:wrap;align-items:center;">
            <a href="register.php" style="display:inline-flex;align-items:center;gap:0.5rem;padding:0.85rem 2rem;border-radius:50px;background:linear-gradient(135deg,var(--orange),var(--orange-hover));color:#000;font-size:0.95rem;font-weight:800;text-decoration:none;box-shadow:0 4px 20px rgba(230,168,0,0.35);transition:all 0.25s;"><i class="fa-solid fa-wrench"></i> Book Free Diagnosis</a>
            <span style="font-size:0.75rem;color:rgba(255,255,255,0.5);background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.1);padding:0.4rem 0.9rem;border-radius:50px;">&#10003; No Hidden Fees</span>
            <span style="font-size:0.75rem;color:rgba(255,255,255,0.5);background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.1);padding:0.4rem 0.9rem;border-radius:50px;">&#128737; 90-Day Warranty</span>
          </div>
        </div>
        <div class="col-lg-4 text-center d-none d-lg-flex align-items-center justify-content-center">
          <div style="position:relative;width:180px;height:180px;">
            <div style="position:absolute;inset:0;border-radius:50%;background:radial-gradient(circle,rgba(230,168,0,0.15),transparent 70%);animation:heroPulse 2.5s ease-in-out infinite;"></div>
            <div style="position:absolute;inset:20px;border-radius:50%;background:rgba(230,168,0,0.06);border:2px solid rgba(230,168,0,0.15);display:flex;align-items:center;justify-content:center;font-size:4rem;">&#128295;</div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <section class="tech-section" id="technicians">

    <div class="container">

      <div class="section-label">Meet the Team</div>

      <h2 class="section-heading">Our Phone Technicians</h2>

      <p class="tech-subtitle">Certified professionals ready to fix your device right the first time.</p>

      <div style="text-align:center;">

        <span class="tech-count-badge" id="techCountBadge">

          <i class="fa-solid fa-users"></i> <span id="techCountNum">â€”</span> Registered Technicians

        </span>

      </div>

      <div id="techGridContainer">

        <div class="tech-loading">

          <div class="spinner"></div>

          Loading technicians…

        </div>

      </div>

    </div>

  </section>

  <!-- =====================

       INFO SECTION (ABOUT US)

  ===================== -->

  <section class="info-section" id="about">

    <div class="container">

      <div class="row align-items-center g-5">

        <!-- Text column -->

        <div class="col-lg-6">

          <div class="section-label">Expert Phone Repair Services</div>

          <h2 class="section-heading">Smart Repairs for Modern Devices</h2>

          <p class="section-paragraph">

            At Fix&amp;Go, we specialize in fast, reliable, and affordable phone repair services for all major brands

            including Apple, Samsung, Huawei, and more. Our certified technicians use only genuine parts to ensure

            your device performs like new. Whether it's a shattered screen, a failing battery, or water damage â€”

            we've got you covered with same-day service and a satisfaction guarantee.

          </p>

          <p class="section-paragraph">

            With years of experience and hundreds of happy customers, Fix&amp;Go is the go-to repair shop for

            anyone who needs their device fixed quickly and professionally. Walk in or book online â€” we're here

            when you need us most.

          </p>

          <div class="d-flex gap-3 flex-wrap">

            <div class="d-flex align-items-center gap-2" style="color: var(--text-secondary); font-size: 0.9rem;">

              <i class="fa-solid fa-circle-check" style="color: var(--orange);"></i> Same-day repairs

            </div>

            <div class="d-flex align-items-center gap-2" style="color: var(--text-secondary); font-size: 0.9rem;">

              <i class="fa-solid fa-circle-check" style="color: var(--orange);"></i> Genuine parts

            </div>

            <div class="d-flex align-items-center gap-2" style="color: var(--text-secondary); font-size: 0.9rem;">

              <i class="fa-solid fa-circle-check" style="color: var(--orange);"></i> 90-day warranty

            </div>

          </div>

        </div>

        <!-- Video column -->

        <div class="col-lg-6">

          <div class="video-wrapper">

            <iframe

              src="https://www.youtube.com/embed/OjTRVpgtcG4?si=iRweFvNgoSm8g9Gh"

              title="Fix&amp;Go – Phone Repair Services"

              allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"

              allowfullscreen>

            </iframe>

          </div>

        </div>

      </div>

    </div>

  </section>

  <!-- =====================

       FOOTER

  ===================== -->


  <!-- =====================
       TESTIMONIALS
  ===================== -->
<div id="guestOnlyContent2">

  <section class="testimonials-section">
    <div class="container">
      <div style="text-align:center;margin-bottom:3rem;">
        <div class="section-label">What Customers Say</div>
        <h2 class="section-heading">Trusted by Hundreds</h2>
        <p style="color:var(--text-secondary);font-size:1rem;max-width:500px;margin:0.75rem auto 0;">Real feedback from real customers who got their devices fixed with Fix&amp;Go.</p>
      </div>
      <div class="row g-4">
        <div class="col-md-4"><div class="testimonial-card"><div class="testimonial-stars">&#9733;&#9733;&#9733;&#9733;&#9733;</div><p class="testimonial-text">"Had my iPhone 14 screen replaced in under 2 hours. The technician sent a video of the finished repair before I even arrived. Love the transparency!"</p><div class="testimonial-author"><div class="t-avatar">MR</div><div><div class="t-name">Maria R.</div><div class="t-sub">Screen Repair &mdash; iPhone 14</div></div></div></div></div>
        <div class="col-md-4"><div class="testimonial-card"><div class="testimonial-stars">&#9733;&#9733;&#9733;&#9733;&#9733;</div><p class="testimonial-text">"Booked a home service for my Samsung S23. The technician arrived on time, fixed the battery on the spot, super professional. Will use Fix&amp;Go again!"</p><div class="testimonial-author"><div class="t-avatar">JD</div><div><div class="t-name">James D.</div><div class="t-sub">Battery Replacement &mdash; Samsung S23</div></div></div></div></div>
        <div class="col-md-4"><div class="testimonial-card"><div class="testimonial-stars">&#9733;&#9733;&#9733;&#9733;&#9734;</div><p class="testimonial-text">"Dropped my phone in water and panicked. Found a technician on Fix&amp;Go within minutes. Free diagnosis, fair price, and my phone is perfectly fine now!"</p><div class="testimonial-author"><div class="t-avatar">AS</div><div><div class="t-name">Anna S.</div><div class="t-sub">Water Damage Recovery</div></div></div></div></div>
      </div>
    </div>
  </section>

  <!-- =====================
       CTA JOIN BANNER
  ===================== -->
  <div style="background:linear-gradient(135deg,#e6a800 0%,#c98f00 100%);padding:56px 0;text-align:center;">
    <div class="container">
      <div style="font-size:0.7rem;font-weight:800;letter-spacing:3px;text-transform:uppercase;color:rgba(0,0,0,0.45);margin-bottom:0.6rem;">JOIN THE COMMUNITY</div>
      <h2 style="font-size:clamp(1.6rem,3.5vw,2.4rem);font-weight:900;color:#000;margin-bottom:0.75rem;">Are You a Phone Technician?</h2>
      <p style="color:rgba(0,0,0,0.55);font-size:1rem;max-width:500px;margin:0 auto 1.75rem;line-height:1.7;">Join Fix&amp;Go as a certified technician, build your profile, showcase your credentials, and receive repair bookings directly from customers in your area.</p>
      <div style="display:flex;gap:0.75rem;justify-content:center;flex-wrap:wrap;">
        <a href="register.php" style="display:inline-flex;align-items:center;gap:0.5rem;padding:0.85rem 2rem;border-radius:50px;background:#000;color:#fff;font-size:0.95rem;font-weight:800;text-decoration:none;transition:all 0.25s;"><i class="fa-solid fa-user-plus"></i> Register as Technician</a>
        <a href="#technicians" style="display:inline-flex;align-items:center;gap:0.5rem;padding:0.85rem 2rem;border-radius:50px;background:rgba(0,0,0,0.12);color:#000;border:2px solid rgba(0,0,0,0.2);font-size:0.95rem;font-weight:800;text-decoration:none;transition:all 0.25s;"><i class="fa-solid fa-magnifying-glass"></i> Browse Technicians</a>
      </div>
    </div>
  </div>


</div><!-- /guestOnlyContent2 -->

  <footer class="footer">

    <div class="container">

      <p>

        &copy; <span id="footerYear"></span> <span>Fix&amp;Go</span>. All rights reserved.

        Built with <i class="fa-solid fa-heart" style="color: var(--orange);"></i> for your devices.

      </p>

    </div>

  </footer>

  <!-- Bootstrap 5 JS CDN -->

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  <script>

    // =====================

    // AUTO-REDIRECT TO LOGIN AFTER LOGOUT

    // =====================

    (function() {

      const urlParams = new URLSearchParams(window.location.search);

      const fromLogout = urlParams.get('logout');

      const isBrowsing = urlParams.get('browse');

      // Never redirect if user explicitly clicked "Browse Shop"

      if (isBrowsing === '1') return;

      // Only redirect if explicitly logged out

      if (fromLogout === 'true') {

        window.location.href = 'login.html';

        return;

      }

      // If logged in as owner or supplier, let them browse freely

      const user = JSON.parse(sessionStorage.getItem('fg_user') || 'null');

      if (user && (user.role === 'owner' || user.role === 'supplier')) return;

    })();

    // =====================

    // MARKETPLACE HOME

    // =====================

    (function () {

      'use strict';

      const catIcons = {

        'LCD / Screen':   { icon: '\ud83d\udcf1', emoji: true },

        'Battery':        { icon: '\ud83d\udd0b', emoji: true },

        'Tempered Glass': { icon: '\ud83d\udee1\ufe0f', emoji: true },

        'Charger':        { icon: '\u26a1', emoji: true },

        'Earphones':      { icon: '\ud83c\udfa7', emoji: true },

        'Back Cover':     { icon: '\ud83d\udcf2', emoji: true },

        'Tools':          { icon: '\ud83d\udd27', emoji: true },

      };

      const bannerConfigs = [
        {
          bg: 'linear-gradient(135deg,#0a0020,#1a0040,#2d0060)',
          accent: '#c084fc',
          tag: 'NEW ARRIVALS',
          title: 'Latest Phone\nAccessories',
          sub: 'We got the latest accessories & gadgets',
          cta: 'Shop Now',
          href: '#shop',
          icon: '\ud83c\udfa7',
          iconRight: '\u231a',
          decorColor: 'rgba(192,132,252,0.15)',
        },
        {
          bg: 'linear-gradient(135deg,#001a3d,#003d7a,#0066cc)',
          accent: '#60a5fa',
          tag: 'ELECTRONICS SALE',
          title: 'Up to 30% OFF\nSelected Items',
          sub: 'Limited time offer on screens & batteries',
          cta: 'Grab Deals',
          href: '#shop',
          icon: '\ud83d\udcf1',
          iconRight: '\ud83d\udd0b',
          decorColor: 'rgba(96,165,250,0.15)',
        },
        {
          bg: 'linear-gradient(135deg,#001a0a,#003d1a,#006630)',
          accent: '#4ade80',
          tag: 'EXPERT REPAIR',
          title: 'Accessories\nRepair Service',
          sub: 'Fast, reliable, certified technicians',
          cta: 'Book Now',
          href: '#technicians',
          icon: '\ud83d\udd27',
          iconRight: '\ud83d\udcf2',
          decorColor: 'rgba(74,222,128,0.15)',
        },
      ];

      let bannerIdx = 0;

      let bannerTimer = null;

      function esc(s) {

        return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');

      }

      function initBanners() {
        const slidesEl = document.getElementById('mktBannerSlides');
        const dotsEl   = document.getElementById('mktBannerDots');
        if (!slidesEl) return;

        slidesEl.innerHTML = bannerConfigs.map((b, i) => {
          const lines = b.title.split('\n');
          return `<div class="mkt-banner-slide${i===0?' active':''}" style="background:${b.bg};">
            <div style="position:absolute;top:-40px;right:-40px;width:220px;height:220px;border-radius:50%;background:${b.decorColor};pointer-events:none;"></div>
            <div style="position:absolute;bottom:-60px;left:30%;width:180px;height:180px;border-radius:50%;background:${b.decorColor};pointer-events:none;"></div>
            <div style="position:absolute;left:1.5rem;top:50%;transform:translateY(-50%);font-size:5rem;opacity:0.15;pointer-events:none;user-select:none;">${b.icon}</div>
            <div style="position:absolute;right:1.5rem;top:50%;transform:translateY(-50%);font-size:4rem;opacity:0.12;pointer-events:none;user-select:none;">${b.iconRight}</div>
            <div style="position:relative;z-index:2;padding:1.25rem 1.5rem;height:100%;display:flex;flex-direction:column;justify-content:center;max-width:540px;overflow:hidden;">
              <span style="display:inline-block;font-size:0.62rem;font-weight:800;letter-spacing:2.5px;text-transform:uppercase;color:${b.accent};background:rgba(255,255,255,0.07);border:1px solid ${b.accent}55;padding:0.2rem 0.75rem;border-radius:50px;margin-bottom:0.5rem;width:fit-content;">${esc(b.tag)}</span>
              <h2 style="font-size:clamp(1.1rem,3.5vw,2.2rem);font-weight:900;color:#fff;line-height:1.15;margin:0 0 0.35rem;letter-spacing:-0.5px;">${lines.map(l=>`<span>${esc(l)}</span>`).join('<br>')}</h2>
              <p style="font-size:0.75rem;color:rgba(255,255,255,0.58);margin:0 0 0.6rem;line-height:1.4;">${esc(b.sub)}</p>
              <div style="display:flex;gap:0.75rem;align-items:center;flex-wrap:wrap;">
                <a href="${esc(b.href)}" style="display:inline-flex;align-items:center;gap:0.4rem;padding:0.45rem 1.2rem;border-radius:50px;background:${b.accent};color:#000;font-size:0.78rem;font-weight:800;text-decoration:none;transition:all 0.2s;box-shadow:0 4px 16px ${b.accent}44;" onmouseenter="this.style.transform='translateY(-2px)';this.style.boxShadow='0 8px 24px ${b.accent}66'" onmouseleave="this.style.transform='';this.style.boxShadow='0 4px 16px ${b.accent}44'">${esc(b.cta)} &rarr;</a>
                <span style="font-size:0.68rem;color:rgba(255,255,255,0.38);font-weight:600;">Free Delivery &bull; 30-Day Warranty</span>
              </div>
            </div>
          </div>`;
        }).join('');

        if (dotsEl) {
          dotsEl.innerHTML = bannerConfigs.map((_, i) =>
            `<span onclick="bannerGoTo(${i})" style="width:8px;height:8px;border-radius:50%;background:${i===0?'#e6a800':'rgba(255,255,255,0.35)'};cursor:pointer;transition:background 0.3s;display:inline-block;"></span>`
          ).join('');
        }
        // Hide the placeholder loader now that real slides are in
        const loader = document.getElementById('mktBannerLoader');
        if (loader) loader.style.display = 'none';
        bannerTimer = setInterval(() => bannerSlide(1), 4000);
      }



      window.bannerSlide = function(dir) {

        bannerGoTo((bannerIdx + dir + bannerConfigs.length) % bannerConfigs.length);

      };

      window.bannerGoTo = function(idx) {

        clearInterval(bannerTimer);

        const slides = document.querySelectorAll('.mkt-banner-slide');

        const dots   = document.querySelectorAll('#mktBannerDots span');

        slides.forEach((s,i) => s.classList.toggle('active', i===idx));

        dots.forEach((d,i) => { d.style.background = i===idx ? '#e6a800' : 'rgba(255,255,255,0.35)'; });

        bannerIdx = idx;

        bannerTimer = setInterval(() => bannerSlide(1), 4000);

      };

      function renderPromoTiles(products, suppliers) { return; // side tiles removed — full-width banner used instead

        const leftEl  = document.getElementById('mktPromoLeft');

        const rightEl = document.getElementById('mktPromoRight');

        if (!leftEl || !rightEl) return;

        // Left: category promo tiles (top 3 categories by count)

        const catCount = {};

        products.forEach(p => { catCount[p.category] = (catCount[p.category]||0)+1; });

        const topCats = Object.entries(catCount).sort((a,b)=>b[1]-a[1]).slice(0,3);

        leftEl.innerHTML = topCats.map(([cat, cnt]) => {

          const ci = catIcons[cat] || { icon: '\ud83d\udce6', emoji: true };

          return `<a href="#shop" class="mkt-promo-tile" onclick="filterShopCat('${esc(cat)}')">

            <div class="mkt-promo-tile-icon">${ci.icon}</div>

            <div>

              <div class="mkt-promo-tile-label">${esc(cat)}</div>

              <div class="mkt-promo-tile-sub">${cnt} item${cnt!==1?'s':''} available</div>

            </div>

          </a>`;

        }).join('');

        // Right: supplier shop tiles

        const uniqueSuppliers = [...new Map(products.map(p => [p.seller_name||p.supplier_name, p])).values()].slice(0,3);

        rightEl.innerHTML = uniqueSuppliers.map(p => {

          const name = p.seller_name || p.supplier_name || 'Shop';

          const initial = name.charAt(0).toUpperCase();

          return `<a href="#shop" class="mkt-promo-tile">

            <div class="mkt-promo-tile-icon" style="font-size:1rem;font-weight:800;">${esc(initial)}</div>

            <div>

              <div class="mkt-promo-tile-label">${esc(name)}</div>

              <div class="mkt-promo-tile-sub">Verified Supplier</div>

            </div>

          </a>`;

        }).join('');

      }

      function renderCategoryRow(products) {

        const el = document.getElementById('mktCategoryRow');

        if (!el) return;

        const cats = [...new Set(products.map(p => p.category))];

        el.innerHTML = [

          `<a href="#shop" class="mkt-cat-pill" onclick="filterShopCat('all')">

            <div class="mkt-cat-pill-icon">&#128722;</div>

            <span class="mkt-cat-pill-label">All</span>

          </a>`

        ].concat(cats.map(cat => {

          const ci = catIcons[cat] || { icon: '\ud83d\udce6' };

          return `<a href="#shop" class="mkt-cat-pill" onclick="filterShopCat('${esc(cat)}')">

            <div class="mkt-cat-pill-icon">${ci.icon}</div>

            <span class="mkt-cat-pill-label">${esc(cat)}</span>

          </a>`;

        })).join('');

      }

      function renderFeaturedProducts(products) {

        const el = document.getElementById('mktFeaturedGrid');

        if (!el) return;

        // Pick up to 8 featured: mix of categories, highest price first

        const featured = products

          .filter(p => p.qty > 0)

          .sort((a,b) => parseFloat(b.srp) - parseFloat(a.srp))

          .slice(0, 8);

        if (!featured.length) {

          el.innerHTML = '<div style="text-align:center;padding:2rem;color:var(--text-secondary);grid-column:1/-1;">No products available yet.</div>';

          return;

        }

        el.innerHTML = featured.map(p => {

          const ci = catIcons[p.category] || { icon: '\ud83d\udce6' };

          const img = p.image_path

            ? `<img class="mkt-feat-img" src="${esc(p.image_path)}" alt="${esc(p.item_description)}" loading="lazy" onerror="this.outerHTML='<div class=\'mkt-feat-img-ph\'><div class=\'mkt-feat-img-ph-icon\'><svg width=\'26\' height=\'26\' viewBox=\'0 0 24 24\' fill=\'none\' stroke=\'rgba(230,168,0,0.5)\' stroke-width=\'1.5\' stroke-linecap=\'round\' stroke-linejoin=\'round\'><rect x=\'5\' y=\'2\' width=\'14\' height=\'20\' rx=\'2\'/><line x1=\'12\' y1=\'18\' x2=\'12.01\' y2=\'18\'/></svg></div></div>'">`

            : `<div class="mkt-feat-img-ph"><div class="mkt-feat-img-ph-icon"><svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="rgba(230,168,0,0.5)" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect x="5" y="2" width="14" height="20" rx="2"/><line x1="12" y1="18" x2="12.01" y2="18"/></svg></div><span class="mkt-feat-img-ph-label">${esc(p.category)}</span></div>`;

          const shopName = p.seller_name || p.supplier_name || '';

          const dataAttr = `data-product='${JSON.stringify({id:p.id,category:p.category,brand:p.brand||'',item_description:p.item_description,qty:p.qty,srp:p.srp,image_path:p.image_path||null,notes:p.notes||'',avg_rating:avgR,review_count:revC}).replace(/'/g,"&#39;")}'`;

          return `<div class="mkt-feat-card" onclick="openProductDetail(this)" ${dataAttr}>

            ${img}

            <div class="mkt-feat-body">

              ${shopName ? `<span class="mkt-feat-shop">&#127978; ${esc(shopName)}</span>` : ''}

              <span class="mkt-feat-cat">${esc(p.category)}</span>

              <div class="mkt-feat-name">${esc(p.item_description)}</div>

              <div class="mkt-feat-price">&#8369;${parseFloat(p.srp).toLocaleString('en-PH',{minimumFractionDigits:2})}</div>

              <div class="mkt-feat-stock">${p.qty} in stock</div>

            </div>

          </div>`;

        }).join('');

      }

      function renderTechSpotlight(techs) {

        const el = document.getElementById('mktTechSpotlight');

        if (!el) return;

        const featured = techs.slice(0, 4);

        if (!featured.length) {

          el.innerHTML = '<div style="text-align:center;padding:2rem;color:var(--text-secondary);grid-column:1/-1;">No technicians registered yet.</div>';

          return;

        }

        el.innerHTML = featured.map(t => {

          const fullName = esc(t.first_name + ' ' + t.last_name);

          const avatar = t.avatar_url

            ? `<img class="mkt-tech-avatar" src="${esc(t.avatar_url)}" alt="${fullName}" loading="lazy" onerror="this.outerHTML='<div class=\'mkt-tech-avatar-ph\'><i class=\'fa-solid fa-user-tie\'></i></div>'">`

            : `<div class="mkt-tech-avatar-ph"><i class="fa-solid fa-user-tie"></i></div>`;

          const specs = (t.specialization||'').split(',').filter(Boolean).slice(0,3);

          const rating = parseFloat(t.rating_avg)||0;

          const stars = rating > 0

            ? `<div style="display:flex;justify-content:center;gap:2px;margin-bottom:0.4rem;">${[1,2,3,4,5].map(i=>`<i class="fa-${i<=Math.round(rating)?'solid':'regular'} fa-star" style="font-size:0.65rem;color:${i<=Math.round(rating)?'var(--orange)':'var(--border-color)'};"></i>`).join('')}<span style="font-size:0.68rem;color:var(--text-secondary);margin-left:3px;">${rating.toFixed(1)}</span></div>`

            : '';

          return `<div class="mkt-tech-card" onclick="techCardClick()">

            ${avatar}

            <div class="mkt-tech-name">${fullName}</div>

            <span class="mkt-tech-role"><i class="fa-solid fa-screwdriver-wrench"></i> Technician</span>

            ${stars}

            ${t.shop_name ? `<div class="mkt-tech-shop"><i class="fa-solid fa-store"></i> ${esc(t.shop_name)}</div>` : ''}

            ${specs.length ? `<div class="mkt-tech-specs">${specs.map(s=>`<span class="mkt-tech-spec">${esc(s.trim())}</span>`).join('')}</div>` : ''}

            <div class="mkt-tech-cta"><i class="fa-solid fa-calendar-check"></i> Book Now</div>

          </div>`;

        }).join('');

      }

      // Expose category filter for promo tile clicks

      window.filterShopCat = function(cat) {

        // Trigger the shop section filter

        const tabs = document.querySelectorAll('.cat-tab');

        tabs.forEach(t => {

          const isActive = t.dataset.cat === cat || (cat==='all' && t.dataset.cat==='all');

          t.classList.toggle('active', isActive);

        });

        // Dispatch a custom event the shop section listens to

        document.dispatchEvent(new CustomEvent('mkt:filterCat', { detail: cat }));

      };

      // Main init

      function initMarketplace(user) {

        const mktEl = document.getElementById('heroMarketplace');

        const outEl = document.getElementById('heroLoggedOut');

        if (!mktEl || !outEl) return;

        if (user) {

          mktEl.style.display = 'block';
          // Force reflow so position:absolute children inside get correct dimensions
          void mktEl.offsetHeight;

          outEl.style.display = 'none';

          const titleEl = document.getElementById('mktWelcomeTitle');

          if (titleEl) titleEl.textContent = 'Welcome back, ' + (user.firstName || user.first_name || 'there') + '!';

          // Small timeout lets the browser complete layout before filling slides
          setTimeout(function() {
            initBanners();
          }, 0);

          // Load products for category pills only
          fetch('api/session/user'shop_products.php?action=all')
            .then(r=>r.json())
            .catch(()=>({success:false}))
            .then(shopData => {
              const products = shopData.success ? (shopData.products||[]) : [];
              renderCategoryRow(products);
            });

        } else {

          mktEl.style.display = 'none';

          outEl.style.display = 'block';

        }

      }

      // Wait for auth to resolve

      // Run immediately — script is at bottom of body, DOM is ready
      // Also register DOMContentLoaded as fallback
      function _runMktInit() {
        // Always show heroLoggedOut first — server check will hide it if truly logged in
        const outEl0 = document.getElementById('heroLoggedOut');
        const mktEl0 = document.getElementById('heroMarketplace');
        if (outEl0) outEl0.style.display = 'block';
        if (mktEl0) mktEl0.style.display = 'none';

        const cached = (window.FGAuth && window.FGAuth.UserStore)
          ? window.FGAuth.UserStore.get()
          : JSON.parse(sessionStorage.getItem('fg_user')||'null');
        // Always verify with server — cached sessionStorage may be stale.
        // Show cached state immediately for speed, then correct if server disagrees.
        if (cached) initMarketplace(cached);
        fetch('api/session/user')
          .then(r=>r.json())
          .then(d => {
            const serverUser = d.loggedIn && d.user ? d.user : null;
            // If server says logged-out but cache said logged-in, correct it
            if (!serverUser) {
              sessionStorage.removeItem('fg_user');
              initMarketplace(null);
            } else {
              document.body.classList.add('fg-logged-in');
              initMarketplace(serverUser);
            }
          })
          .catch(() => { if (!cached) initMarketplace(null); });
      }
      if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', _runMktInit);
      } else {
        _runMktInit();
      }

    })();

    // =====================

    // SHOP â€” Phone Accessories

    // =====================

    (function () {

      const API = 'api/session/user'shop_products.php?action=all';

      const bannerEl    = document.getElementById('shopBannerContainer');

      const productsEl  = document.getElementById('shopProductsContainer');

      const tabsEl      = document.getElementById('categoryTabs');

      const searchEl    = document.getElementById('shopSearch');

      let allProducts   = [];

      let activeCategory = 'all';

      function esc(str) {

        return String(str || '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');

      }

      function renderBanner(shop) {

        if (!shop) return;

        bannerEl.innerHTML = `

          <div class="shop-banner">

            <div class="shop-banner-icon"><i class="fa-solid fa-store"></i></div>

            <div class="shop-banner-info">

              <h6>${esc(shop.name)}</h6>

              <p>${esc(shop.description || 'Quality phone accessories at great prices.')}</p>

            </div>

            <div class="shop-banner-meta">

              ${shop.city ? `<span class="shop-meta-item"><i class="fa-solid fa-location-dot"></i>${esc(shop.city)}</span>` : ''}

              ${shop.phone ? `<span class="shop-meta-item"><i class="fa-solid fa-phone"></i>${esc(shop.phone)}</span>` : ''}

              ${shop.email ? `<span class="shop-meta-item"><i class="fa-solid fa-envelope"></i>${esc(shop.email)}</span>` : ''}

            </div>

          </div>`;

      }

      function buildCategoryTabs(products) {

        const cats = ['all', ...new Set(products.map(p => p.category).filter(Boolean).sort())];

        tabsEl.innerHTML = cats.map(c =>

          `<button class="cat-tab${c === 'all' ? ' active' : ''}" data-cat="${esc(c)}">${esc(c === 'all' ? 'All' : c)}</button>`

        ).join('');

        tabsEl.querySelectorAll('.cat-tab').forEach(btn => {

          btn.addEventListener('click', function () {

            tabsEl.querySelectorAll('.cat-tab').forEach(b => b.classList.remove('active'));

            this.classList.add('active');

            activeCategory = this.dataset.cat;

            renderProducts();

          });

        });

      }

      function renderProducts() {

        const q = searchEl.value.toLowerCase().trim();

        const filtered = allProducts.filter(p => {

          const matchCat = activeCategory === 'all' || p.category === activeCategory;

          const matchQ   = !q || [p.category, p.brand, p.item_description, p.notes]

            .join(' ').toLowerCase().includes(q);

          return matchCat && matchQ;

        });

        if (!filtered.length) {

          productsEl.innerHTML = `

            <div class="shop-empty">

              <i class="fa-solid fa-box-open"></i>

              <p>No products found${q ? ' for "<strong>' + esc(q) + '</strong>"' : ''}.</p>

            </div>`;

          return;

        }

        productsEl.innerHTML = `<div class="product-grid">${filtered.map(p => {

          let imgSrc = null;

          if (p.image_path) {

            imgSrc = (p.image_path.startsWith('http') || p.image_path.startsWith('data:'))

              ? p.image_path : p.image_path;

          }

          const avgR = parseFloat(p.avg_rating||0); const revC = parseInt(p.review_count||0);
          const isOutOfStock = p.qty <= 0;

          const dataAttr = `data-product='${JSON.stringify({id:p.id,category:p.category,brand:p.brand||'',item_description:p.item_description,qty:p.qty,srp:p.srp,image_path:p.image_path||null,notes:p.notes||'',avg_rating:avgR,review_count:revC}).replace(/'/g,"&#39;")}'`;

          return `

            <div class="product-card" style="cursor:pointer;" data-clickable="product" ${dataAttr}>

              ${imgSrc

                ? `<img class="product-card-img" src="${esc(imgSrc)}" alt="${esc(p.item_description)}" loading="lazy" onerror="this.outerHTML='<div class=\\'product-card-img-placeholder\\'><i class=\\'fa-solid fa-mobile-screen-button\\'></i></div>'">`

                : `<div class="product-card-img-placeholder"><i class="fa-solid fa-mobile-screen-button"></i></div>`

              }

              <div class="product-card-body">

                <span class="product-cat-badge">${esc(p.category)}</span>

                ${p.seller_name ? `<div style="display:inline-flex;align-items:center;gap:0.3rem;font-size:0.68rem;font-weight:700;color:#3b82f6;background:rgba(59,130,246,0.1);border:1px solid rgba(59,130,246,0.2);padding:0.12rem 0.5rem;border-radius:50px;margin-bottom:0.35rem;"><i class="fa-solid fa-store" style="font-size:0.6rem;"></i>${esc(p.seller_name)}</div>` : ''}

                <div class="product-card-title">${esc(p.item_description)}</div>

                ${p.brand ? `<div class="product-card-brand">${esc(p.brand)}</div>` : ""}
                ${avgR > 0 ? `<div style="display:flex;align-items:center;gap:0.25rem;margin-top:0.2rem;margin-bottom:0.25rem;">${[1,2,3,4,5].map(i => i <= Math.floor(avgR) ? '<i class="fa-solid fa-star" style="color:var(--orange);font-size:0.65rem;"></i>' : (i - avgR < 1 && i - avgR > 0 ? '<i class="fa-solid fa-star-half-stroke" style="color:var(--orange);font-size:0.65rem;"></i>' : '<i class="fa-regular fa-star" style="color:var(--border-color);font-size:0.65rem;"></i>')).join("")}<span style="font-size:0.68rem;color:var(--text-secondary);margin-left:0.2rem;">${avgR.toFixed(1)} (${revC})</span></div>` : ""}

                <div class="product-card-footer">

                  <span class="product-price">₱${parseFloat(p.srp).toLocaleString('en-PH',{minimumFractionDigits:2})}</span>

                  <span class="product-qty" style="${isOutOfStock ? 'color:#dc3545;font-weight:700;' : ''}">${p.qty > 0 ? p.qty + ' in stock' : 'Out of stock'}</span>

                </div>

                <div class="product-card-cta" style="${isOutOfStock ? 'background:rgba(150,150,150,0.1);color:#999;border-color:rgba(150,150,150,0.2);cursor:not-allowed;' : ''}">

                  <i class="fa-solid ${isOutOfStock ? 'fa-ban' : 'fa-cart-shopping'}"></i> ${isOutOfStock ? 'Out of Stock' : 'View & Order'}

                </div>

              </div>

            </div>`;

        }).join('')}</div>`;

      }

      function loadShop() {

        fetch(API)

          .then(r => r.json())

          .then(data => {

            if (!data.success) throw new Error(data.message || 'Failed');

            const shop = data.shops && data.shops[0];

            renderBanner(shop);

            // Use shop products if available, otherwise fall back to flat list

            allProducts = (shop && shop.products && shop.products.length)

              ? shop.products

              : (data.products || []);

            buildCategoryTabs(allProducts);

            renderProducts();

            // Expose for global search

            window._fgShopProducts = allProducts;

          })

          .catch(() => {

            productsEl.innerHTML = `

              <div class="shop-empty">

                <i class="fa-solid fa-store-slash"></i>

                <p>Shop products will appear here once the owner adds inventory.</p>

              </div>`;

          });

      }

      searchEl.addEventListener('input', renderProducts);

      // Listen for category filter events from marketplace home

      document.addEventListener('mkt:filterCat', function(e) {

        const cat = e.detail;

        activeCategory = cat;

        const tabs = tabsEl.querySelectorAll('.cat-tab');

        tabs.forEach(t => {

          const isActive = t.dataset.cat === cat || (cat === 'all' && t.dataset.cat === 'all');

          t.classList.toggle('active', isActive);

        });

        renderProducts();

        // Scroll to shop section

        document.getElementById('shop').scrollIntoView({ behavior: 'smooth' });

      });

      loadShop();

    })();

    // â”€â”€ Product card click â€” open detail modal â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€

    function openProductDetail(cardEl) {

      let product;

      try { product = JSON.parse(cardEl.getAttribute('data-product')); } catch(e) { return; }

      const user = (window.FGAuth && window.FGAuth.UserStore)

        ? window.FGAuth.UserStore.get()

        : JSON.parse(sessionStorage.getItem('fg_user') || 'null');

      if (!user) {

        window.location.href = 'login.html?redirect=' + encodeURIComponent(window.location.href + '#shop');

        return;

      }

      showProductModal(product, user);

    }

    // Expose globally so onclick= in HTML can call it
    window.openProductDetail = openProductDetail;

    // Delegated click handler for product cards (works inside IIFE scope)
    document.addEventListener('click', function(e) {
      const card = e.target.closest('[data-clickable="product"]');
      if (card) openProductDetail(card);
    });


    // =====================

    // TECHNICIANS

    // =====================

    (function () {

      const API = 'api/session/user'technicians.php?action=list';

      const gridEl    = document.getElementById('techGridContainer');

      const countNum  = document.getElementById('techCountNum');

      function esc(str) {

        return String(str || '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');

      }

      function sinceYear(dateStr) {

        return dateStr ? new Date(dateStr).getFullYear() : 'â€”';

      }

      function renderTechnicians(techs) {

        countNum.textContent = techs.length;

        if (!techs.length) {

          gridEl.innerHTML = `

            <div class="tech-empty">

              <i class="fa-solid fa-user-slash"></i>

              <p>No technicians registered yet. Check back soon!</p>

            </div>`;

          return;

        }

        const availColor = { available: '#28a745', busy: '#e6a800', unavailable: '#dc3545' };

        const availLabel = { available: 'Available', busy: 'Busy', unavailable: 'Unavailable' };

        gridEl.innerHTML = `<div class="tech-grid">${techs.map(t => {

          const fullName = esc(t.first_name + ' ' + t.last_name);

          const avail    = t.availability || 'available';

          const avatar = t.avatar_url

            ? `<img class="tech-avatar" src="${esc(t.avatar_url)}" alt="${fullName}" loading="lazy" onerror="this.outerHTML='<div class=\\'tech-avatar-placeholder\\'><i class=\\'fa-solid fa-user-tie\\'></i></div>'">`

            : `<div class="tech-avatar-placeholder"><i class="fa-solid fa-user-tie"></i></div>`;

          const availDot = `<span class="tech-avail-dot" style="background:${availColor[avail] || '#aaa'};" title="${availLabel[avail] || avail}"></span>`;

          const specs = t.specialization

            ? t.specialization.split(',').slice(0, 2).map(s =>

                `<span class="tech-spec-pill">${esc(s.trim())}</span>`

              ).join('')

            : '';

          const rating    = parseFloat(t.rating_avg) || 0;

          const fullStars = Math.floor(rating);

          const halfStar  = (rating - fullStars) >= 0.5;

          let stars = '';

          for (let i = 0; i < 5; i++) {

            if (i < fullStars)

              stars += '<i class="fa-solid fa-star" style="color:var(--orange);font-size:0.7rem;"></i>';

            else if (i === fullStars && halfStar)

              stars += '<i class="fa-solid fa-star-half-stroke" style="color:var(--orange);font-size:0.7rem;"></i>';

            else

              stars += '<i class="fa-regular fa-star" style="color:var(--border-color);font-size:0.7rem;"></i>';

          }

          const ratingLine = `<div class="tech-rating">${stars} <span style="font-size:0.72rem;color:var(--text-secondary);">(${t.rating_count})</span></div>`;

          const shopLine = t.shop_name

            ? `<div class="tech-shop"><i class="fa-solid fa-store"></i>${esc(t.shop_name)}${t.shop_city ? ', ' + esc(t.shop_city) : ''}</div>`

            : `<div class="tech-shop"><i class="fa-solid fa-store"></i>Unassigned</div>`;

          const bioLine = t.bio

            ? `<div class="tech-bio">${esc(t.bio)}</div>`

            : '';

          return `

            <div class="tech-card" style="cursor:pointer;" onclick="openTechProfile(${t.id})">


              <div class="tech-avail-wrap">${availDot}</div>

              ${avatar}

              <div class="tech-name">${fullName}</div>

              <span class="tech-role-badge"><i class="fa-solid fa-screwdriver-wrench"></i> Phone Technician</span>

              ${ratingLine}

              ${specs ? `<div class="tech-specs">${specs}</div>` : ''}

              ${bioLine}

              ${shopLine}

              <div style="flex:1;"></div>

              <div class="tech-stats">

                <div class="tech-stat">

                  <div class="tech-stat-val">${t.repairs_done}</div>

                  <div class="tech-stat-lbl">Repairs</div>

                </div>

                <div class="tech-stat">

                  <div class="tech-stat-val">${t.experience_years > 0 ? t.experience_years + 'y' : 'â€”'}</div>

                  <div class="tech-stat-lbl">Exp.</div>

                </div>

                <div class="tech-stat">

                  <div class="tech-stat-val">${sinceYear(t.created_at)}</div>

                  <div class="tech-stat-lbl">Since</div>

                </div>

              </div>

              <div class="tech-card-cta">

                <i class="fa-solid fa-calendar-check"></i> Book Now

              </div>

            </div>`;

        }).join('')}</div>`;

      }

      fetch(API)

        .then(r => r.text())

        .then(raw => {

          let data;

          try { data = JSON.parse(raw); }

          catch (e) {

            console.error('Technicians API raw response:', raw);

            throw new Error('Invalid JSON from server');

          }

          if (!data.success) throw new Error(data.message || 'API returned success=false');

          renderTechnicians(data.technicians || []);

          // Expose for global search

          window._fgTechnicians = data.technicians || [];

        })

        .catch(err => {

          console.error('Technicians API error:', err.message);

          countNum.textContent = '0';

          gridEl.innerHTML = `

            <div class="tech-empty">

              <i class="fa-solid fa-user-slash"></i>

              <p>Could not load technicians. Check the browser console for details.</p>

            </div>`;

        });

    })();


    // ── Tech card click → open profile modal ─────────────────────────────

    function openTechProfile(techId) {
      const user = (window.FGAuth && window.FGAuth.UserStore)
        ? window.FGAuth.UserStore.get()
        : JSON.parse(sessionStorage.getItem('fg_user') || 'null');

      if (!user) {
        window.location.href = 'login.html?redirect=' + encodeURIComponent(window.location.href + '#technicians');
        return;
      }

      // Open the technician profile modal
      showTechProfileModal(techId);
    }


    // =====================

    // THEME TOGGLE

    // =====================

    const html = document.documentElement;

    const themeToggle = document.getElementById('themeToggle');

    const themeIcon = document.getElementById('themeIcon');

    const savedTheme = localStorage.getItem('fixandgo-theme') || 'dark';

    html.setAttribute('data-theme', savedTheme);

    updateThemeIcon(savedTheme);

    themeToggle.addEventListener('click', function () {

      const current = html.getAttribute('data-theme');

      const next = current === 'dark' ? 'light' : 'dark';

      html.setAttribute('data-theme', next);

      localStorage.setItem('fixandgo-theme', next);

      updateThemeIcon(next);

    });

    function updateThemeIcon(theme) {

      if (theme === 'dark') {

        themeIcon.className = 'fa-solid fa-moon';

        themeToggle.setAttribute('title', 'Switch to light mode');

      } else {

        themeIcon.className = 'fa-solid fa-sun';

        themeToggle.setAttribute('title', 'Switch to dark mode');

      }

    }

    // =====================

    // FOOTER YEAR

    // =====================

    document.getElementById('footerYear').textContent = new Date().getFullYear();

    // ── Sync mobile navbar icons with login state ─────────────
    (function syncMobileNav() {
      // Mirror theme icon
      var themeD = document.getElementById('themeIcon');
      var themeM = document.getElementById('themeIconMobile');
      if (themeD && themeM) {
        var obs = new MutationObserver(function() {
          themeM.className = themeD.className;
        });
        obs.observe(themeD, { attributes: true, attributeFilter: ['class'] });
        themeM.className = themeD.className;
      }
      // Mirror login button state
      var mobBtn = document.getElementById('navLoginBtnMob');
      var dskBtn = document.getElementById('navLoginBtn');
      if (mobBtn && dskBtn) {
        var linkObs = new MutationObserver(function() {
          mobBtn.href = dskBtn.href || 'login.html';
          if (dskBtn.innerHTML && dskBtn.innerHTML.indexOf('fa-gauge') >= 0) {
            // Logged in — show full Dashboard button
            mobBtn.innerHTML = '<i class="fa-solid fa-right-to-bracket"></i> <span class="mob-btn-label">Dashboard</span>';
            mobBtn.style.padding = '0.45rem 1rem';
            mobBtn.style.color   = '#000';
          } else if (dskBtn.innerHTML && dskBtn.innerHTML.indexOf('fa-right-to-bracket') >= 0) {
            mobBtn.innerHTML = '<i class="fa-solid fa-right-to-bracket"></i> <span class="mob-btn-label">Login</span>';
            mobBtn.style.padding = '0.45rem 1rem';
            mobBtn.style.color   = '#000';
          }
        });
        linkObs.observe(dskBtn, { attributes: true, childList: true, subtree: true });
        // Run once immediately in case already set
        if (dskBtn.innerHTML && dskBtn.innerHTML.indexOf('fa-gauge') >= 0) {
          mobBtn.href    = dskBtn.href || 'dashboard.php';
          mobBtn.innerHTML = '<i class="fa-solid fa-right-to-bracket"></i> <span class="mob-btn-label">Dashboard</span>';
          mobBtn.style.padding = '0.45rem 1rem';
          mobBtn.style.color   = '#000';
        }
      }
      // Mirror cart badge
      var cartBadgeDsk = document.getElementById('customerCartBadge');
      var cartBadgeMob = document.getElementById('cartBadgeMob');
      var cartWrapDsk  = document.getElementById('customerCartWrap');
      var cartWrapMob  = document.getElementById('customerCartWrapMob');
      if (cartBadgeMob && cartBadgeDsk) {
        var cartObs = new MutationObserver(function() {
          cartBadgeMob.textContent = cartBadgeDsk.textContent;
          cartBadgeMob.style.display = cartBadgeDsk.style.display;
          if (cartWrapDsk && cartWrapMob) {
            cartWrapMob.style.display = cartWrapDsk.style.display;
          }
        });
        cartObs.observe(cartBadgeDsk, { attributes: true, childList: true });
        cartObs.observe(cartWrapDsk, { attributes: true });
      }

      // ── Mirror notification badge to mobile bell ──────────────
      var notifItem    = document.getElementById('navItemNotifications');
      var notifBadgeDsk = document.getElementById('notificationBadge');
      var notifWrapMob  = document.getElementById('navNotifWrapMob');
      var notifBadgeMob = document.getElementById('notifBadgeMob');
      var drawerNotifRow   = document.getElementById('drawerNotifRow');
      var drawerNotifBadge = document.getElementById('drawerNotifBadge');
      var shopBnNotif  = document.getElementById('shopBnNotif');
      var shopBnBadge  = document.getElementById('shopBnBadge');

      function syncNotifBadge() {
        if (!notifBadgeDsk) return;
        var val = notifBadgeDsk.textContent;
        var visible = notifBadgeDsk.style.display !== 'none';
        // Mobile navbar bell
        if (notifBadgeMob) { notifBadgeMob.textContent = val; notifBadgeMob.style.display = visible ? 'inline-block' : 'none'; }
        // Drawer badge
        if (drawerNotifBadge) { drawerNotifBadge.textContent = val; drawerNotifBadge.style.display = visible ? 'inline-block' : 'none'; }
        // Shop bottom nav badge
        if (shopBnBadge) { shopBnBadge.textContent = val; shopBnBadge.style.display = visible ? 'inline-block' : 'none'; }
      }

      function syncNotifVisibility() {
        if (!notifItem) return;
        var show = notifItem.style.display !== 'none';
        // navNotifWrapMob is now a messages button — always show when user is logged in (notifItem visible = logged in)
        if (notifWrapMob)   notifWrapMob.style.display   = show ? 'block' : 'none';
        if (drawerNotifRow) drawerNotifRow.style.display  = show ? 'block' : 'none';
        if (shopBnNotif)    shopBnNotif.style.display     = show ? 'list-item' : 'none';
        syncNotifBadge();
      }

      if (notifBadgeDsk) {
        var badgeObs = new MutationObserver(syncNotifBadge);
        badgeObs.observe(notifBadgeDsk, { attributes: true, childList: true, characterData: true });
      }
      if (notifItem) {
        var itemObs = new MutationObserver(syncNotifVisibility);
        itemObs.observe(notifItem, { attributes: true, attributeFilter: ['style'] });
        // Run immediately to set initial state
        syncNotifVisibility();
      }
    })();


    // =====================

    // NAVBAR GLOBAL SEARCH

    // Searches accessories (shop products) + technicians

    // =====================

    (function () {

      const input     = document.getElementById('navSearchInput');

      const clearBtn  = document.getElementById('navSearchClear');

      const resultsEl = document.getElementById('navSearchResults');

      if (!input) return;

      // Cache data loaded by the shop/technician IIFEs

      // We poll for it since those IIFEs run async

      let searchData = { products: [], technicians: [] };

      function syncData() {

        // Products are stored in the shop IIFE's allProducts variable â€”

        // we expose them via a global after load

        if (window._fgShopProducts)      searchData.products     = window._fgShopProducts;

        if (window._fgTechnicians)        searchData.technicians  = window._fgTechnicians;

      }

      function esc(s) {

        return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');

      }

      function highlight(text, q) {

        if (!q) return esc(text);

        const re = new RegExp('(' + q.replace(/[.*+?^${}()|[\]\\]/g,'\\$&') + ')', 'gi');

        return esc(text).replace(re, '<mark style="background:rgba(230,168,0,0.3);color:inherit;border-radius:2px;padding:0 1px;">$1</mark>');

      }

      function renderResults(q) {

        syncData();

        if (!q) { resultsEl.style.display = 'none'; return; }

        const ql = q.toLowerCase();

        // Filter products

        const matchedProducts = searchData.products.filter(p =>

          [p.category, p.brand, p.item_description, p.notes]

            .join(' ').toLowerCase().includes(ql)

        ).slice(0, 5);

        // Filter technicians

        const matchedTechs = searchData.technicians.filter(t =>

          [t.first_name, t.last_name, t.specialization, t.shop_name]

            .join(' ').toLowerCase().includes(ql)

        ).slice(0, 4);

        if (!matchedProducts.length && !matchedTechs.length) {

          resultsEl.innerHTML = `<div class="nsr-empty">

            <i class="fa-solid fa-magnifying-glass" style="display:block;font-size:1.5rem;margin-bottom:0.5rem;opacity:0.4;"></i>

            No results for "<strong>${esc(q)}</strong>"

          </div>`;

          resultsEl.style.display = 'block';

          return;

        }

        let html = '';

        if (matchedProducts.length) {

          html += `<div class="nsr-section-label"><i class="fa-solid fa-box-open" style="margin-right:0.3rem;color:var(--orange);"></i>Accessories</div>`;

          html += matchedProducts.map(p => {

            const img = p.image_path

              ? `<img class="nsr-item-img" src="${esc(p.image_path)}" alt="" onerror="this.outerHTML='<div class=\\'nsr-item-img-ph\\'><i class=\\'fa-solid fa-mobile-screen-button\\'></i></div>'">`

              : `<div class="nsr-item-img-ph"><i class="fa-solid fa-mobile-screen-button"></i></div>`;

            return `

              <div class="nsr-item" onclick="document.getElementById('shopSearch').value='${esc(p.category)}';document.getElementById('shopSearch').dispatchEvent(new Event('input'));document.querySelector('#shop').scrollIntoView({behavior:'smooth'});closeSearch();">

                ${img}

                <div class="nsr-item-body">

                  <div class="nsr-item-name">${highlight(p.item_description, q)}</div>

                  <div class="nsr-item-sub">${esc(p.category)}${p.brand ? ' · ' + esc(p.brand) : ''}</div>

                </div>

                <span class="nsr-item-price">₱${parseFloat(p.srp).toLocaleString('en-PH',{minimumFractionDigits:2})}</span>

              </div>`;

          }).join('');

        }

        if (matchedProducts.length && matchedTechs.length) {

          html += `<div class="nsr-divider"></div>`;

        }

        if (matchedTechs.length) {

          html += `<div class="nsr-section-label"><i class="fa-solid fa-screwdriver-wrench" style="margin-right:0.3rem;color:var(--orange);"></i>Technicians</div>`;

          html += matchedTechs.map(t => {

            const name = t.first_name + ' ' + t.last_name;

            const avail = t.availability || 'available';

            const availColor = { available:'#28a745', busy:'#e6a800', unavailable:'#dc3545' };

            const dot = `<span style="display:inline-block;width:8px;height:8px;border-radius:50%;background:${availColor[avail]||'#aaa'};margin-right:4px;"></span>`;

            return `

              <div class="nsr-item" onclick="document.querySelector('#technicians').scrollIntoView({behavior:'smooth'});closeSearch();">

                <div class="nsr-item-avatar"><i class="fa-solid fa-user-tie"></i></div>

                <div class="nsr-item-body">

                  <div class="nsr-item-name">${highlight(name, q)}</div>

                  <div class="nsr-item-sub">${dot}${esc(t.specialization || 'Phone Technician')}${t.shop_name ? ' · ' + esc(t.shop_name) : ''}</div>

                </div>

              </div>`;

          }).join('');

        }

        resultsEl.innerHTML = html;

        resultsEl.style.display = 'block';

      }

      function closeSearch() {

        resultsEl.style.display = 'none';

        input.value = '';

        clearBtn.style.display = 'none';

      }

      input.addEventListener('input', function () {

        const q = this.value.trim();

        clearBtn.style.display = q ? 'block' : 'none';

        renderResults(q);

      });

      clearBtn.addEventListener('click', closeSearch);

      // Close on outside click

      document.addEventListener('click', function (e) {

        if (!document.getElementById('navSearchWrap').contains(e.target)) {

          resultsEl.style.display = 'none';

        }

      });

      // Keyboard: Escape closes

      input.addEventListener('keydown', function (e) {

        if (e.key === 'Escape') closeSearch();

      });

    })();

    // ── Mobile search bar wiring ─────────────────────────────────
    (function () {
      var mobInput   = document.getElementById('mobileSearchInput');
      var mobClear   = document.getElementById('mobileSearchClear');
      var mobResults = document.getElementById('mobileSearchResults');
      var deskInput  = document.getElementById('navSearchInput');
      var deskRes    = document.getElementById('navSearchResults');
      if (!mobInput || !deskInput) return;

      // Mirror typing into desktop input to reuse its search engine
      mobInput.addEventListener('input', function () {
        var q = this.value.trim();
        mobClear.style.display = q ? 'block' : 'none';
        // Trigger desktop search
        deskInput.value = q;
        deskInput.dispatchEvent(new Event('input'));
        // Copy results into mobile panel
        setTimeout(function () {
          if (deskRes.innerHTML && deskRes.style.display !== 'none') {
            mobResults.innerHTML = deskRes.innerHTML;
            mobResults.style.display = 'block';
            deskRes.style.display = 'none'; // hide desktop results while mobile is active
          } else if (!q) {
            mobResults.style.display = 'none';
          }
        }, 50);
      });

      mobInput.addEventListener('focus', function () {
        if (this.value.trim()) this.dispatchEvent(new Event('input'));
      });

      mobClear.addEventListener('click', function () {
        mobInput.value = '';
        mobClear.style.display = 'none';
        mobResults.style.display = 'none';
        deskInput.value = '';
        deskRes.style.display = 'none';
      });

      mobInput.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') { mobClear.click(); mobInput.blur(); }
      });

      // Close on outside click
      document.addEventListener('click', function (e) {
        var bar = document.getElementById('mobileSearchBar');
        if (bar && !bar.contains(e.target)) {
          mobResults.style.display = 'none';
        }
      });

      // Focus border highlight
      mobInput.addEventListener('focus',  function () { this.style.borderColor = 'var(--orange)'; });
      mobInput.addEventListener('blur',   function () { this.style.borderColor = 'var(--border-color,#2a2a2a)'; });
    })();

    // =====================

    // SUPPLIER-AWARE UI

    // Hides login/register + services/about when a supplier is logged in

    // =====================

    (function () {

      const user = (window.FGAuth && window.FGAuth.UserStore)

        ? window.FGAuth.UserStore.get()

        : JSON.parse(sessionStorage.getItem('fg_user') || 'null');

      if (!user || (user.role !== 'supplier' && user.role !== 'owner' && user.role !== 'sales_person')) return; // applies to supplier, owner and sales_person

      // Immediately swap hero: hide logged-out, show marketplace
      var _hlo = document.getElementById('heroLoggedOut');
      var _hmk = document.getElementById('heroMarketplace');
      if (_hlo) _hlo.style.display = 'none';
      var _goc = document.getElementById('guestOnlyContent'); if (_goc) _goc.style.display = 'none';
      if (_hmk) { _hmk.style.display = 'block'; void _hmk.offsetHeight; }
      var _gocb = document.getElementById('guestOnlyContent2'); if (_gocb) _gocb.style.display = 'none';

      const isOwner      = user.role === 'owner';

      const isSalesPerson = user.role === 'sales_person';

      const roleLabel  = isOwner ? 'Owner' : isSalesPerson ? 'Sales Person' : 'Supplier';

      const roleIcon   = isOwner ? 'fa-store' : isSalesPerson ? 'fa-briefcase' : 'fa-box-open';

      const profileUrl = isOwner

        ? 'views/user/owner/profile.php'

        : isSalesPerson

          ? 'views/user/sales_person/profile.php'

          : 'views/user/supplier/profile.php';

      const heroMsg    = isOwner

        ? 'Welcome back, ' + (user.firstName || 'Owner') + '! Browse the shop, manage bookings, and run your repair shop from your dashboard.'

        : isSalesPerson

          ? 'Welcome back, ' + (user.firstName || 'Sales Person') + '! Browse the shop, view your products, and manage customer orders from your dashboard.'

          : 'Welcome back, ' + (user.firstName || 'Supplier') + '! Browse the shop, check your products, and manage your inventory from your dashboard.';

      // â”€â”€ Navbar: hide Login button, show Dashboard button â”€â”€

      const navLoginBtn = document.getElementById('navLoginBtn');

      if (navLoginBtn) {

        navLoginBtn.href        = 'dashboard.php';

        navLoginBtn.innerHTML   = '<i class="fa-solid fa-gauge me-1"></i> Dashboard';

      }

      // -- Mobile Dashboard button sync (owner/supplier/sales_person)
      const mobLoginBtn1 = document.getElementById('navLoginBtnMob');
      if (mobLoginBtn1) {
        mobLoginBtn1.href      = 'dashboard.php';
        mobLoginBtn1.innerHTML = '<i class="fa-solid fa-right-to-bracket"></i> <span class="mob-btn-label">Dashboard</span>';
        mobLoginBtn1.style.padding = '0.45rem 1rem';
        mobLoginBtn1.style.color   = '#000';
      }

      // â”€â”€ Show supplier/owner profile widget â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€

      const profileWidget = document.getElementById('supplierNavProfile');

      if (profileWidget) {

        profileWidget.style.display = 'flex';

        const snpName = document.getElementById('snpName');

        if (snpName) snpName.textContent = (user.firstName || '') + ' ' + (user.lastName || '');

        // Update role label dynamically

        const snpRole = profileWidget.querySelector('.snp-role');

        if (snpRole) snpRole.textContent = (isOwner ? '🏪' : isSalesPerson ? '💼' : '📦') + ' ' + roleLabel;

        // If avatar_url is available

        if (user.avatar_url) {

          const avatarEl = document.getElementById('snpAvatar');

          if (avatarEl) avatarEl.innerHTML = `<img src="${user.avatar_url}" alt="avatar">`;

        }

        // Make it a link to the correct profile page

        profileWidget.style.cursor = 'pointer';

        profileWidget.addEventListener('click', function () {

          window.location.href = profileUrl;

        });

      }

      // Show message icon for sales_person

      if (isSalesPerson) {

        const msgWrap = document.getElementById('navMsgWrap');

        const msgLink = document.getElementById('navMsgLink');

        if (msgWrap) msgWrap.style.display = 'flex';

        if (msgLink) msgLink.href = 'views/user/sales_person/messages.php';

        loadNavMsgCount('views/user/sales_person/messages.php');

      }

      // â”€â”€ Navbar: hide Services & About Us links â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€

      const navItemServices = document.getElementById('navItemServices');

      const navItemAbout    = document.getElementById('navItemAbout');

      if (navItemServices) navItemServices.style.display = 'none';

      if (navItemAbout)    navItemAbout.style.display    = 'none';

      // â”€â”€ Hero: replace Login/Register with Dashboard CTA â”€â”€â”€

      const heroLoginBtn    = document.getElementById('heroLoginBtn');

      const heroRegisterBtn = document.getElementById('heroRegisterBtn');

      if (heroLoginBtn) {

        heroLoginBtn.href      = 'dashboard.php';

        heroLoginBtn.innerHTML = '<i class="fa-solid fa-gauge me-2"></i>Go to Dashboard';

      }

      if (heroRegisterBtn) heroRegisterBtn.style.display = 'none';

      // â”€â”€ Hero: update subtitle â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€

      const heroSubtext = document.querySelector('.hero-subtext');

      if (heroSubtext) {

        heroSubtext.textContent = heroMsg;

      }

      // â”€â”€ Hide Services section entirely â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€

      const servicesSection = document.getElementById('services');

      if (servicesSection) servicesSection.style.display = 'none';

      // â”€â”€ Hide About Us section entirely â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€

      const aboutSection = document.getElementById('about');

      if (aboutSection) aboutSection.style.display = 'none';

      // â”€â”€ Add a welcome bar below the navbar â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€

      const supplierBar = document.createElement('div');

      supplierBar.id = 'supplierWelcomeBar';

      supplierBar.innerHTML = `

        <i class="fa-solid fa-${roleIcon}"></i>

        You're viewing as <strong>${(user.firstName || '') + ' ' + (user.lastName || '')} â€” ${roleLabel}</strong>.

        <a href="dashboard.php" style="color:#fff;font-weight:700;margin-left:0.5rem;text-decoration:underline;">

          Go to your dashboard â†’

        </a>

      `;

      supplierBar.style.cssText = `

        position: fixed; top: 68px; left: 0; right: 0; z-index: 999;

        background: var(--orange); color: #fff;

        padding: 0.5rem 1.5rem;

        font-size: 0.85rem; font-weight: 500;

        display: flex; align-items: center; gap: 0.5rem;

        justify-content: center; flex-wrap: wrap;

        box-shadow: 0 2px 8px rgba(0,0,0,0.2);

      `;

      document.body.appendChild(supplierBar);

      // Push content down to account for the extra bar
      const heroSection = document.querySelector('.hero-section');
      if (heroSection) {
        const isMobile = window.innerWidth <= 991;
        heroSection.style.paddingTop = isMobile ? '100px' : '110px';
      }

    })();

    // =====================

    // CUSTOMER-AWARE UI

    // Shows customer profile in navbar when a customer is browsing the shop

    // =====================

    (function () {

      const user = (window.FGAuth && window.FGAuth.UserStore)

        ? window.FGAuth.UserStore.get()

        : JSON.parse(sessionStorage.getItem('fg_user') || 'null');

      if (!user || user.role !== 'customer') return;

      // Immediately swap hero: hide logged-out, show marketplace
      var _hlo2 = document.getElementById('heroLoggedOut');
      var _hmk2 = document.getElementById('heroMarketplace');
      if (_hlo2) _hlo2.style.display = 'none';
      if (_hmk2) { _hmk2.style.display = 'block'; void _hmk2.offsetHeight; }
      var _goc2 = document.getElementById('guestOnlyContent'); if (_goc2) _goc2.style.display = 'none';
      var _goc2b = document.getElementById('guestOnlyContent2'); if (_goc2b) _goc2b.style.display = 'none';

      const fullName = ((user.firstName || '') + ' ' + (user.lastName || '')).trim();

      const initials = ((user.firstName || '')[0] || '') + ((user.lastName || '')[0] || '');

      // â”€â”€ Show customer profile widget in navbar â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€

      const profileWidget = document.getElementById('customerNavProfile');

      if (profileWidget) {

        profileWidget.style.display = 'flex';

        const nameEl = document.getElementById('customerNavName');

        if (nameEl) nameEl.textContent = fullName || user.email || 'Customer';

        // Show initials avatar

        const avatarEl = document.getElementById('customerNavAvatar');

        if (avatarEl) {

          if (user.avatar_url) {

            avatarEl.innerHTML = `<img src="${user.avatar_url}" alt="avatar" style="width:100%;height:100%;border-radius:50%;object-fit:cover;">`;

          } else if (initials) {

            avatarEl.innerHTML = `<span style="font-size:0.85rem;font-weight:700;">${initials.toUpperCase()}</span>`;

            avatarEl.style.background = 'linear-gradient(135deg,rgba(230,168,0,0.3),rgba(230,168,0,0.1))';

          }

        }

      }

      // â”€â”€ Update Login button â†’ Dashboard â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€

      const navLoginBtn = document.getElementById('navLoginBtn');

      if (navLoginBtn) {

        navLoginBtn.href      = 'views/user/customer/dashboard.php';

        navLoginBtn.innerHTML = '<i class="fa-solid fa-gauge me-1"></i> Dashboard';

      }

      // -- Mobile Dashboard button sync (customer)
      const mobLoginBtn2 = document.getElementById('navLoginBtnMob');
      if (mobLoginBtn2) {
        mobLoginBtn2.href      = 'views/user/customer/dashboard.php';
        mobLoginBtn2.innerHTML = '<i class="fa-solid fa-right-to-bracket"></i> <span class="mob-btn-label">Dashboard</span>';
        mobLoginBtn2.style.padding = '0.45rem 1rem';
        mobLoginBtn2.style.color   = '#000';
      }

      // ── Fix bottom nav links for logged-in customer ────────────
      (function fixCustomerBottomNav() {
        // Technicians → scrolls to technicians section on landing page
        var bnRepairs = document.querySelector('#shopBottomNav a[href="#technicians"]');
        // Keep as #technicians — correct for all users

        // Alerts → customer notifications page (was a dropdown trigger)
        var bnAlertBtn = document.getElementById('shopBnNotifBtn');
        if (bnAlertBtn) {
          var parent = bnAlertBtn.parentNode;
          // Replace the <a href="#"> with a proper navigation link
          var newLink = document.createElement('a');
          newLink.href = 'views/user/customer/notifications.php';
          newLink.style.cssText = bnAlertBtn.style.cssText ||
            'display:flex;flex-direction:column;align-items:center;gap:0.15rem;padding:0.3rem 0.75rem;color:var(--text-secondary,#888);text-decoration:none;font-size:0.62rem;font-weight:700;';
          newLink.innerHTML = bnAlertBtn.innerHTML;
          parent.replaceChild(newLink, bnAlertBtn);
        }

        // Me → customer dashboard
        var bnMe = document.getElementById('shopBnLogin');
        if (bnMe) bnMe.href = 'views/user/customer/dashboard.php';
      })();

      // â”€â”€ Hero: replace buttons with dashboard CTA â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€

      const heroLoginBtn    = document.getElementById('heroLoginBtn');

      const heroRegisterBtn = document.getElementById('heroRegisterBtn');

      if (heroLoginBtn) {

        heroLoginBtn.href      = 'views/user/customer/dashboard.php';

        heroLoginBtn.innerHTML = '<i class="fa-solid fa-gauge me-2"></i>Go to Dashboard';

      }

      if (heroRegisterBtn) heroRegisterBtn.style.display = 'none';

      // Show message icon for customer

      const msgWrapC = document.getElementById('navMsgWrap');

      const msgLinkC = document.getElementById('navMsgLink');

      if (msgWrapC) msgWrapC.style.display = 'flex';

      if (msgLinkC) msgLinkC.href = 'views/user/customer/messages.php';

      loadNavMsgCount('views/user/customer/messages.php');

      // â”€â”€ Hero: personalised subtitle â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€

      const heroSubtext = document.querySelector('.hero-subtext');

      if (heroSubtext) {

        heroSubtext.textContent =

          'Welcome back, ' + (user.firstName || 'there') + '! Browse accessories, book repairs, and manage your orders.';

      }

      // â”€â”€ Hide Services & About Us nav links â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€

      const navItemServices = document.getElementById('navItemServices');

      const navItemAbout    = document.getElementById('navItemAbout');

      if (navItemServices) navItemServices.style.display = 'none';

      if (navItemAbout)    navItemAbout.style.display    = 'none';

      // â”€â”€ Hide Services & About Us sections â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€

      const servicesSection = document.getElementById('services');

      const aboutSection    = document.getElementById('about');

      if (servicesSection) servicesSection.style.display = 'none';

      if (aboutSection)    aboutSection.style.display    = 'none';

    })();

    // =====================
    // SESSION AVATAR REFRESH
    // Fetches fresh user data from server to update avatar_url in sessionStorage
    // and re-renders the navbar profile widgets with the latest photo
    // =====================
    (function () {
      var _user = (window.FGAuth && window.FGAuth.UserStore)
        ? window.FGAuth.UserStore.get()
        : JSON.parse(sessionStorage.getItem('fg_user') || 'null');
      if (!_user) return; // not logged in

      fetch('api/session/user', { credentials: 'include' })
        .then(function(r) { return r.json(); })
        .then(function(data) {
          if (!data.loggedIn || !data.user) return;
          var fresh = data.user;
          if (!fresh.avatar_url) return;

          // Merge avatar_url into stored user and re-save
          var stored = JSON.parse(sessionStorage.getItem('fg_user') || 'null');
          if (stored) {
            stored.avatar_url = fresh.avatar_url;
            sessionStorage.setItem('fg_user', JSON.stringify(stored));
          }

          // Update supplier/owner/sales_person avatar
          var snpAvatar = document.getElementById('snpAvatar');
          var snpProfile = document.getElementById('supplierNavProfile');
          if (snpAvatar && snpProfile && snpProfile.style.display !== 'none') {
            snpAvatar.innerHTML = '<img src="' + fresh.avatar_url + '" alt="avatar" style="width:100%;height:100%;border-radius:50%;object-fit:cover;" onerror="this.parentElement.innerHTML=\'<i class=\\\'fa-solid fa-user\\\'></i>\'">';
          }

          // Update customer avatar
          var custAvatar = document.getElementById('customerNavAvatar');
          var custProfile = document.getElementById('customerNavProfile');
          if (custAvatar && custProfile && custProfile.style.display !== 'none') {
            custAvatar.innerHTML = '<img src="' + fresh.avatar_url + '" alt="avatar" style="width:100%;height:100%;border-radius:50%;object-fit:cover;" onerror="this.parentElement.innerHTML=\'<i class=\\\'fa-solid fa-user\\\'></i>\'">';
          }
        })
        .catch(function() {}); // silent fail
    })();

    // =====================

    // NAV MESSAGE BADGE

    // =====================

    function loadNavMsgCount(messagesUrl) {

      fetch('api/session/user'messages.php?action=unread_count', { credentials: 'include' })

        .then(function(r) { return r.json(); })

        .then(function(d) {

          if (d.success && d.count > 0) {

            var badge = document.getElementById('navMsgBadgeIndex');

            if (badge) { badge.textContent = d.count > 99 ? '99+' : d.count; badge.style.display = 'inline-block'; }

          }

        }).catch(function() {});

      setTimeout(function() { loadNavMsgCount(messagesUrl); }, 10000);

    }

    // =====================

    // NAVBAR ACTIVE LINK ON SCROLL

    // =====================

    const sections = document.querySelectorAll('section[id]');

    const navLinks = document.querySelectorAll('.nav-link');

    function onScroll() {

      let scrollY = window.scrollY + 100;

      sections.forEach(function (section) {

        const top = section.offsetTop;

        const height = section.offsetHeight;

        const id = section.getAttribute('id');

        if (scrollY >= top && scrollY < top + height) {

          navLinks.forEach(function (link) {

            link.classList.remove('active');

            if (link.getAttribute('href') === '#' + id || (id === 'home' && link.getAttribute('href') === '#')) {

              link.classList.add('active');

            }

          });

        }

      });

    }

    window.addEventListener('scroll', onScroll);

    // =====================

    // NOTIFICATIONS SYSTEM

    // =====================

    (function () {

      const user = (window.FGAuth && window.FGAuth.UserStore)

        ? window.FGAuth.UserStore.get()

        : JSON.parse(sessionStorage.getItem('fg_user') || 'null');

      // Only show notifications for logged-in users who are NOT supervisors

      if (!user || user.role === 'supervisor') return;

      const navItem = document.getElementById('navItemNotifications');

      const navLink = document.getElementById('navNotifications');

      const badge = document.getElementById('notificationBadge');

      const dropdown = document.getElementById('notificationDropdown');

      const listEl = document.getElementById('notificationList');

      const markAllBtn = document.getElementById('markAllReadBtn');

      const closeBtn = document.getElementById('closeNotificationsBtn');

      if (!navItem || !navLink || !badge || !dropdown || !listEl) return;

      // Show the notification nav item

      navItem.style.display = 'block';

      let notifications = [];

      let isDropdownOpen = false;

      // â”€â”€ Fetch unread count â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€

      function fetchUnreadCount() {

        fetch('api/session/user'notifications.php?action=unread')

          .then(r => r.json())

          .then(data => {

            if (data.success) {

              const count = data.unread_count || 0;

              if (count > 0) {

                badge.textContent = count > 99 ? '99+' : count;

                badge.style.display = 'block';

              } else {

                badge.style.display = 'none';

              }

            }

          })

          .catch(err => console.error('Failed to fetch unread count:', err));

      }

      // â”€â”€ Fetch all notifications â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€

      function fetchNotifications() {

        listEl.innerHTML = `

          <div class="notification-loading">

            <div class="spinner"></div>

            Loading notifications…

          </div>`;

        fetch('api/session/user'notifications.php?action=list&limit=20')

          .then(r => r.json())

          .then(data => {

            if (data.success) {

              notifications = data.notifications || [];

              renderNotifications();

            } else {

              listEl.innerHTML = `

                <div class="notification-empty">

                  <i class="fa-solid fa-bell-slash"></i>

                  <p>Could not load notifications.</p>

                </div>`;

            }

          })

          .catch(err => {

            console.error('Failed to fetch notifications:', err);

            listEl.innerHTML = `

              <div class="notification-empty">

                <i class="fa-solid fa-exclamation-triangle"></i>

                <p>Error loading notifications.</p>

              </div>`;

          });

      }

      // â”€â”€ Render notifications â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€

      function renderNotifications() {

        if (!notifications.length) {

          listEl.innerHTML = `

            <div class="notification-empty">

              <i class="fa-solid fa-bell"></i>

              <p>No notifications yet. We'll notify you when something happens!</p>

            </div>`;

          return;

        }

        listEl.innerHTML = notifications.map(n => {

          const isUnread = n.is_read === 0 || n.is_read === '0';

          const icon = getNotificationIcon(n.type);

          const timeAgo = formatTimeAgo(n.created_at);

          return `

            <div class="notification-item ${isUnread ? 'unread' : ''}" data-id="${n.id}">

              <div class="notification-icon">

                <i class="${icon}"></i>

              </div>

              <div class="notification-content">

                <div class="notification-title">${esc(n.title)}</div>

                <div class="notification-body">${esc(n.body)}</div>

                <div class="notification-time">${timeAgo}</div>

              </div>

            </div>`;

        }).join('');

        // Add click handlers

        listEl.querySelectorAll('.notification-item').forEach(item => {

          item.addEventListener('click', function () {

            const id = parseInt(this.dataset.id);

            const notif = notifications.find(n => n.id === id);

            if (notif && (notif.is_read === 0 || notif.is_read === '0')) {

              markAsRead([id]);

            }

          });

        });

      }

      // â”€â”€ Get icon based on notification type â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€

      function getNotificationIcon(type) {

        const icons = {

          'booking_confirmed': 'fa-solid fa-calendar-check',

          'booking_cancelled': 'fa-solid fa-calendar-xmark',

          'order_placed': 'fa-solid fa-cart-shopping',

          'order_shipped': 'fa-solid fa-truck',

          'order_delivered': 'fa-solid fa-box-check',

          'payment_received': 'fa-solid fa-money-bill-wave',

          'message': 'fa-solid fa-envelope',

          'otp': 'fa-solid fa-key',

          'system': 'fa-solid fa-info-circle',

          'promotion': 'fa-solid fa-tag'

        };

        return icons[type] || 'fa-solid fa-bell';

      }

      // â”€â”€ Format time ago â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€

      function formatTimeAgo(dateStr) {

        const now = new Date();

        const date = new Date(dateStr);

        const seconds = Math.floor((now - date) / 1000);

        if (seconds < 60) return 'Just now';

        if (seconds < 3600) return Math.floor(seconds / 60) + 'm ago';

        if (seconds < 86400) return Math.floor(seconds / 3600) + 'h ago';

        if (seconds < 604800) return Math.floor(seconds / 86400) + 'd ago';

        return date.toLocaleDateString();

      }

      // â”€â”€ Escape HTML â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€

      function esc(str) {

        return String(str || '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');

      }

      // â”€â”€ Mark notification(s) as read â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€

      function markAsRead(ids) {

        fetch('api/session/user'notifications.php?action=mark_read', {

          method: 'POST',

          headers: { 'Content-Type': 'application/json' },

          body: JSON.stringify({ ids })

        })

          .then(r => r.json())

          .then(data => {

            if (data.success) {

              // Update local state

              ids.forEach(id => {

                const notif = notifications.find(n => n.id === id);

                if (notif) notif.is_read = 1;

              });

              renderNotifications();

              fetchUnreadCount();

            }

          })

          .catch(err => console.error('Failed to mark as read:', err));

      }

      // â”€â”€ Mark all as read â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€

      function markAllAsRead() {

        fetch('api/session/user'notifications.php?action=mark_all_read', {

          method: 'POST',

          headers: { 'Content-Type': 'application/json' },

          body: JSON.stringify({})

        })

          .then(r => r.json())

          .then(data => {

            if (data.success) {

              notifications.forEach(n => n.is_read = 1);

              renderNotifications();

              fetchUnreadCount();

            }

          })

          .catch(err => console.error('Failed to mark all as read:', err));

      }

      // -- Toggle dropdown
      function toggleDropdown() {
        isDropdownOpen = !isDropdownOpen;
        if (isDropdownOpen) {
          dropdown.style.display = 'flex';
          fetchNotifications();
        } else {
          dropdown.style.display = 'none';
        }
      }

      function closeDropdown() {
        isDropdownOpen = false;
        dropdown.style.display = 'none';
      }

      // Expose globally so all mobile bells can call it
      window.toggleNotifDropdown = function(e) {
        if (e) { e.preventDefault(); e.stopPropagation(); }
        toggleDropdown();
      };
      window.closeNotifDropdown = closeDropdown;

      // -- Event listeners
      navLink.addEventListener('click', function (e) {
        e.preventDefault();
        e.stopPropagation();
        toggleDropdown();
      });

      // Mobile navbar message button — update href based on user role
      var mobMsgBtn = document.getElementById('navMobMsgBtn');
      if (mobMsgBtn) {
        var _u = (window.FGAuth && window.FGAuth.UserStore) ? window.FGAuth.UserStore.get() : null;
        if (_u) {
          var msgPages = {
            'customer':         'views/user/customer/messages.php',
            'phone_technician': 'views/user/phone_technician/messages.php',
            'owner':            'views/user/owner/messages.php',
            'supplier':         'views/user/supplier/messages.php',
            'sales_person':     'views/user/sales_person/messages.php'
          };
          if (msgPages[_u.role]) mobMsgBtn.href = msgPages[_u.role];
        }
      }

      // Bottom nav Inbox tab is now a plain <a> link — no JS wiring needed

      closeBtn.addEventListener('click', function(e) {
        e.stopPropagation();
        closeDropdown();
      });

      markAllBtn.addEventListener('click', function () {
        markAllAsRead();
      });

      // Close dropdown when clicking outside
      document.addEventListener('click', function (e) {
        if (!isDropdownOpen) return;
        if (
          !dropdown.contains(e.target) &&
          !navLink.contains(e.target)
        ) {
          closeDropdown();
        }
      });

      // â”€â”€ Initial load â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€

      fetchUnreadCount();

      // Poll for new notifications every 30 seconds

      setInterval(fetchUnreadCount, 30000);

    })();

  </script>

<!-- =====================================================================

     PRODUCT DETAIL MODAL

===================================================================== -->

<div id="productDetailModal" style="display:none;position:fixed;inset:0;z-index:9000;background:rgba(0,0,0,0.7);backdrop-filter:blur(6px);align-items:center;justify-content:center;padding:1rem;overflow-y:auto;">

  <div style="background:var(--bg-card);border:1px solid var(--border-color);border-radius:20px;width:100%;max-width:860px;margin:auto;box-shadow:0 32px 80px rgba(0,0,0,0.5);animation:pdmIn 0.25s cubic-bezier(0.16,1,0.3,1);position:relative;">

    <!-- Close -->

    <button onclick="closeProductModal()" style="position:absolute;top:1rem;right:1rem;width:36px;height:36px;border-radius:10px;border:1.5px solid var(--border-color);background:transparent;color:var(--text-secondary);cursor:pointer;display:flex;align-items:center;justify-content:center;font-size:1rem;z-index:10;transition:all 0.2s;" onmouseenter="this.style.borderColor='#dc3545';this.style.color='#dc3545'" onmouseleave="this.style.borderColor='var(--border-color)';this.style.color='var(--text-secondary)'">

      <i class="fa-solid fa-xmark"></i>

    </button>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:0;" id="pdmGrid">

      <!-- Left: Image -->

      <div style="padding:2rem;border-right:1px solid var(--border-color);">

        <div id="pdmImageWrap" style="border-radius:14px;overflow:hidden;background:var(--bg);aspect-ratio:1/1;display:flex;align-items:center;justify-content:center;">

          <i class="fa-solid fa-mobile-screen-button" style="font-size:4rem;color:var(--text-secondary);opacity:0.3;"></i>

        </div>

        <!-- Shop info -->

        <div id="pdmShopInfo" style="margin-top:1.25rem;padding:1rem;background:var(--bg);border-radius:12px;border:1px solid var(--border-color);display:none;">

          <div style="font-size:0.7rem;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:var(--text-secondary);margin-bottom:0.5rem;">Sold by</div>

          <div style="display:flex;align-items:center;gap:0.6rem;">

            <div style="width:36px;height:36px;border-radius:10px;background:rgba(230,168,0,0.15);display:flex;align-items:center;justify-content:center;color:var(--orange);font-size:1rem;flex-shrink:0;"><i class="fa-solid fa-store"></i></div>

            <div>

              <div id="pdmShopName" style="font-weight:700;font-size:0.88rem;color:var(--text-primary);"></div>

              <div id="pdmShopCity" style="font-size:0.75rem;color:var(--text-secondary);"></div>

            </div>

          </div>

        </div>

      </div>

      <!-- Right: Details -->

      <div style="padding:2rem;display:flex;flex-direction:column;gap:0;">

        <div id="pdmCatBadge" style="display:inline-block;font-size:0.68rem;font-weight:700;color:var(--orange);background:rgba(230,168,0,0.1);border:1px solid rgba(230,168,0,0.2);padding:0.2rem 0.65rem;border-radius:50px;margin-bottom:0.75rem;width:fit-content;"></div>

        <h3 id="pdmTitle" style="font-size:1.25rem;font-weight:800;color:var(--text-primary);margin:0 0 0.3rem;line-height:1.3;"></h3>

        <div id="pdmBrand" style="font-size:0.82rem;color:var(--text-secondary);margin-bottom:0.75rem;"></div>

        <!-- Rating summary -->

        <div id="pdmRatingSummary" style="display:flex;align-items:center;gap:0.5rem;margin-bottom:1rem;">

          <div id="pdmStars" style="display:flex;gap:2px;"></div>

          <span id="pdmRatingText" style="font-size:0.8rem;color:var(--text-secondary);"></span>

        </div>

        <div id="pdmPrice" style="font-size:2rem;font-weight:900;color:var(--orange);margin-bottom:0.5rem;"></div>

        <div id="pdmStock" style="font-size:0.82rem;margin-bottom:1rem;"></div>

        <!-- Description -->

        <div id="pdmNotesWrap" style="display:none;margin-bottom:1rem;">

          <div style="font-size:0.72rem;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:var(--text-secondary);margin-bottom:0.4rem;">Description</div>

          <div id="pdmNotes" style="font-size:0.85rem;color:var(--text-primary);line-height:1.6;background:var(--bg);border-radius:10px;padding:0.75rem 1rem;border:1px solid var(--border-color);"></div>

        </div>

        <!-- Quantity selector -->

        <div style="margin-bottom:1rem;">

          <div style="font-size:0.72rem;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:var(--text-secondary);margin-bottom:0.5rem;">Quantity</div>

          <div style="display:flex;align-items:center;gap:0.5rem;">

            <button onclick="pdmQtyChange(-1)" style="width:36px;height:36px;border-radius:8px;border:1.5px solid var(--border-color);background:transparent;color:var(--text-primary);font-size:1.1rem;cursor:pointer;display:flex;align-items:center;justify-content:center;transition:all 0.2s;" onmouseenter="this.style.borderColor='var(--orange)'" onmouseleave="this.style.borderColor='var(--border-color)'">âˆ’</button>

            <input type="number" id="pdmQtyInput" value="1" min="1" style="width:60px;text-align:center;border:1.5px solid var(--border-color);border-radius:8px;background:var(--bg);color:var(--text-primary);padding:0.4rem;font-size:0.9rem;font-weight:700;outline:none;" oninput="pdmQtyValidate()">

            <button onclick="pdmQtyChange(1)" style="width:36px;height:36px;border-radius:8px;border:1.5px solid var(--border-color);background:transparent;color:var(--text-primary);font-size:1.1rem;cursor:pointer;display:flex;align-items:center;justify-content:center;transition:all 0.2s;" onmouseenter="this.style.borderColor='var(--orange)'" onmouseleave="this.style.borderColor='var(--border-color)'">+</button>

            <span id="pdmQtyMax" style="font-size:0.75rem;color:var(--text-secondary);"></span>

          </div>

        </div>

        <!-- Action buttons -->

        <div id="pdmActions" style="display:flex;flex-direction:column;gap:0.6rem;margin-bottom:1rem;">

          <button id="pdmBuyNowBtn" onclick="pdmBuyNow()" style="padding:0.75rem;border-radius:12px;background:var(--orange);color:#000;border:none;font-weight:800;font-size:0.95rem;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:0.5rem;transition:all 0.2s;" onmouseenter="this.style.background='var(--orange-hover)'" onmouseleave="this.style.background='var(--orange)'">

            <i class="fa-solid fa-bolt"></i> Buy Now

          </button>

          <button id="pdmAddCartBtn" onclick="pdmAddToCart()" style="padding:0.75rem;border-radius:12px;background:transparent;color:var(--orange);border:2px solid var(--orange);font-weight:700;font-size:0.95rem;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:0.5rem;transition:all 0.2s;" onmouseenter="this.style.background='rgba(230,168,0,0.1)'" onmouseleave="this.style.background='transparent'">

            <i class="fa-solid fa-cart-plus"></i> Add to Cart

          </button>


          <button id="pdmRequestBtn" onclick="pdmRequestProduct()" style="display:none;padding:0.75rem;border-radius:12px;background:#8b5cf6;color:#fff;border:none;font-weight:700;font-size:0.95rem;cursor:pointer;align-items:center;justify-content:center;gap:0.5rem;transition:all 0.2s;" onmouseenter="this.style.background='#7c3aed'" onmouseleave="this.style.background='#8b5cf6'">

            <i class="fa-solid fa-paper-plane"></i> Request from Supplier

          </button>
        </div>

        <!-- Alert -->

        <div id="pdmAlert" style="display:none;padding:0.65rem 1rem;border-radius:10px;font-size:0.83rem;font-weight:600;"></div>

      </div>

    </div>

    <!-- Reviews section -->

    <div style="border-top:1px solid var(--border-color);padding:1.75rem 2rem;">

      <h5 style="font-size:1rem;font-weight:800;color:var(--text-primary);margin:0 0 1.25rem;display:flex;align-items:center;gap:0.5rem;">

        <i class="fa-solid fa-star" style="color:var(--orange);"></i> Ratings & Reviews

      </h5>

      <!-- Write review (only for logged-in customers) -->

      <div id="pdmWriteReview" style="display:none;background:var(--bg);border:1px solid var(--border-color);border-radius:14px;padding:1.25rem;margin-bottom:1.5rem;">

        <div style="font-size:0.82rem;font-weight:700;color:var(--text-primary);margin-bottom:0.75rem;">Write a Review</div>

        <div style="display:flex;gap:0.3rem;margin-bottom:0.75rem;" id="pdmStarPicker">

          <i class="fa-solid fa-star pdm-star-pick" data-val="1" style="font-size:1.4rem;color:var(--orange);cursor:pointer;transition:transform 0.1s;"></i>

          <i class="fa-solid fa-star pdm-star-pick" data-val="2" style="font-size:1.4rem;color:var(--orange);cursor:pointer;transition:transform 0.1s;"></i>

          <i class="fa-solid fa-star pdm-star-pick" data-val="3" style="font-size:1.4rem;color:var(--orange);cursor:pointer;transition:transform 0.1s;"></i>

          <i class="fa-solid fa-star pdm-star-pick" data-val="4" style="font-size:1.4rem;color:var(--orange);cursor:pointer;transition:transform 0.1s;"></i>

          <i class="fa-solid fa-star pdm-star-pick" data-val="5" style="font-size:1.4rem;color:var(--orange);cursor:pointer;transition:transform 0.1s;"></i>

        </div>

        <textarea id="pdmReviewText" placeholder="Share your experience with this product..." rows="3" style="width:100%;background:var(--bg-card);border:1.5px solid var(--border-color);border-radius:10px;color:var(--text-primary);padding:0.65rem 0.9rem;font-size:0.85rem;resize:vertical;outline:none;font-family:inherit;transition:border-color 0.2s;" onfocus="this.style.borderColor='var(--orange)'" onblur="this.style.borderColor='var(--border-color)'"></textarea>

        <button onclick="pdmSubmitReview()" style="margin-top:0.65rem;padding:0.55rem 1.25rem;border-radius:9px;background:var(--orange);color:#000;border:none;font-weight:700;font-size:0.83rem;cursor:pointer;transition:all 0.2s;" onmouseenter="this.style.background='var(--orange-hover)'" onmouseleave="this.style.background='var(--orange)'">

          <i class="fa-solid fa-paper-plane"></i> Submit Review

        </button>

      </div>

      <!-- Reviews list -->

      <div id="pdmReviewsList" style="display:flex;flex-direction:column;gap:1rem;">

        <div style="text-align:center;padding:2rem;color:var(--text-secondary);font-size:0.85rem;">Loading reviews…</div>

      </div>

    </div>

  </div>

</div>

<!-- =====================================================================

     CART DRAWER

===================================================================== -->

<div id="cartDrawerOverlay" onclick="closeCartDrawer()" style="display:none;position:fixed;inset:0;z-index:8900;background:rgba(0,0,0,0.5);backdrop-filter:blur(4px);"></div>

<div id="cartDrawer" style="display:none;position:fixed;top:0;right:0;bottom:0;z-index:8950;width:100%;max-width:420px;background:var(--bg-card);border-left:1px solid var(--border-color);box-shadow:-20px 0 60px rgba(0,0,0,0.4);flex-direction:column;overflow:hidden;">

  <!-- Header -->

  <div style="padding:1.25rem 1.5rem;border-bottom:1px solid var(--border-color);display:flex;align-items:center;justify-content:space-between;flex-shrink:0;">

    <h5 style="margin:0;font-weight:800;font-size:1.05rem;color:var(--text-primary);display:flex;align-items:center;gap:0.5rem;">

      <i class="fa-solid fa-cart-shopping" style="color:var(--orange);"></i> My Cart

      <span id="cartDrawerCount" style="background:var(--orange);color:#000;font-size:0.65rem;font-weight:800;padding:0.1rem 0.45rem;border-radius:10px;"></span>

    </h5>

    <button onclick="closeCartDrawer()" style="width:32px;height:32px;border-radius:8px;border:1.5px solid var(--border-color);background:transparent;color:var(--text-secondary);cursor:pointer;display:flex;align-items:center;justify-content:center;font-size:0.9rem;transition:all 0.2s;" onmouseenter="this.style.borderColor='#dc3545';this.style.color='#dc3545'" onmouseleave="this.style.borderColor='var(--border-color)';this.style.color='var(--text-secondary)'">

      <i class="fa-solid fa-xmark"></i>

    </button>

  </div>

  <!-- Items -->

  <div id="cartDrawerItems" style="flex:1;overflow-y:auto;padding:1rem 1.5rem;"></div>

  <!-- Footer -->

  <div id="cartDrawerFooter" style="padding:1.25rem 1.5rem;border-top:1px solid var(--border-color);flex-shrink:0;display:none;">

    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem;">

      <span style="font-size:0.88rem;color:var(--text-secondary);font-weight:600;">Total</span>

      <span id="cartDrawerTotal" style="font-size:1.4rem;font-weight:900;color:var(--orange);"></span>

    </div>

    <button onclick="cartCheckout()" style="width:100%;padding:0.85rem;border-radius:12px;background:var(--orange);color:#000;border:none;font-weight:800;font-size:0.95rem;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:0.5rem;transition:all 0.2s;" onmouseenter="this.style.background='var(--orange-hover)'" onmouseleave="this.style.background='var(--orange)'">

      <i class="fa-solid fa-bolt"></i> Checkout

    </button>

    <button onclick="cartClearAll()" style="width:100%;margin-top:0.5rem;padding:0.6rem;border-radius:10px;background:transparent;color:var(--text-secondary);border:1.5px solid var(--border-color);font-weight:600;font-size:0.82rem;cursor:pointer;transition:all 0.2s;" onmouseenter="this.style.borderColor='#dc3545';this.style.color='#dc3545'" onmouseleave="this.style.borderColor='var(--border-color)';this.style.color='var(--text-secondary)'">

      <i class="fa-solid fa-trash"></i> Clear Cart

    </button>

  </div>

</div>

<!-- =====================================================================

     CHECKOUT MODAL

===================================================================== -->

<div id="checkoutModal" style="display:none;position:fixed;inset:0;z-index:9100;background:rgba(0,0,0,0.7);backdrop-filter:blur(6px);align-items:center;justify-content:center;padding:1rem;">

  <div style="background:var(--bg-card);border:1px solid var(--border-color);border-radius:20px;width:100%;max-width:480px;box-shadow:0 32px 80px rgba(0,0,0,0.5);animation:pdmIn 0.25s cubic-bezier(0.16,1,0.3,1);">

    <div style="padding:1.5rem 1.75rem 1.25rem;border-bottom:1px solid var(--border-color);display:flex;align-items:center;justify-content:space-between;">

      <h5 style="margin:0;font-weight:800;font-size:1.05rem;color:var(--text-primary);display:flex;align-items:center;gap:0.5rem;">

        <i class="fa-solid fa-bag-shopping" style="color:var(--orange);"></i> Checkout

      </h5>

      <button onclick="closeCheckoutModal()" style="width:32px;height:32px;border-radius:8px;border:1.5px solid var(--border-color);background:transparent;color:var(--text-secondary);cursor:pointer;display:flex;align-items:center;justify-content:center;font-size:0.9rem;">

        <i class="fa-solid fa-xmark"></i>

      </button>

    </div>

    <div style="padding:1.5rem 1.75rem;">

      <!-- Order summary -->

      <div id="checkoutSummary" style="background:var(--bg);border-radius:12px;padding:1rem;margin-bottom:1.25rem;border:1px solid var(--border-color);max-height:200px;overflow-y:auto;"></div>

      <!-- Payment method -->

      <div style="margin-bottom:1.25rem;">

        <div style="font-size:0.75rem;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:var(--text-secondary);margin-bottom:0.6rem;">Payment Method</div>

        <div style="display:flex;gap:0.5rem;flex-wrap:wrap;">

          <label style="flex:1;min-width:100px;cursor:pointer;">

            <input type="radio" name="payMethod" value="cod" checked style="display:none;" onchange="updatePayMethod(this)">

            <div class="pay-method-btn pay-active" id="payBtn_cod" style="padding:0.6rem 0.75rem;border-radius:10px;border:2px solid var(--orange);background:rgba(230,168,0,0.1);text-align:center;font-size:0.8rem;font-weight:700;color:var(--orange);transition:all 0.2s;">

              <i class="fa-solid fa-money-bill-wave"></i><br>Cash on Delivery

            </div>

          </label>

          <label style="flex:1;min-width:100px;cursor:pointer;">

            <input type="radio" name="payMethod" value="gcash" style="display:none;" onchange="updatePayMethod(this)">

            <div class="pay-method-btn" id="payBtn_gcash" style="padding:0.6rem 0.75rem;border-radius:10px;border:2px solid var(--border-color);background:transparent;text-align:center;font-size:0.8rem;font-weight:700;color:var(--text-secondary);transition:all 0.2s;">

              <i class="fa-solid fa-mobile-screen"></i><br>GCash

            </div>

          </label>

          <label style="flex:1;min-width:100px;cursor:pointer;">

            <input type="radio" name="payMethod" value="card" style="display:none;" onchange="updatePayMethod(this)">

            <div class="pay-method-btn" id="payBtn_card" style="padding:0.6rem 0.75rem;border-radius:10px;border:2px solid var(--border-color);background:transparent;text-align:center;font-size:0.8rem;font-weight:700;color:var(--text-secondary);transition:all 0.2s;">

              <i class="fa-solid fa-credit-card"></i><br>Card

            </div>

          </label>

        </div>

      </div>

      <!-- Notes -->

      <div style="margin-bottom:1.25rem;">

        <div style="font-size:0.75rem;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:var(--text-secondary);margin-bottom:0.5rem;">Order Notes <span style="font-weight:400;text-transform:none;">(optional)</span></div>

        <textarea id="checkoutNotes" placeholder="Any special instructions..." rows="2" style="width:100%;background:var(--bg);border:1.5px solid var(--border-color);border-radius:10px;color:var(--text-primary);padding:0.6rem 0.85rem;font-size:0.83rem;resize:none;outline:none;font-family:inherit;" onfocus="this.style.borderColor='var(--orange)'" onblur="this.style.borderColor='var(--border-color)'"></textarea>

      </div>

      <!-- Total -->

      <div style="display:flex;justify-content:space-between;align-items:center;padding:0.75rem 1rem;background:var(--bg);border-radius:10px;margin-bottom:1.25rem;border:1px solid var(--border-color);">

        <span style="font-weight:700;color:var(--text-primary);">Total Amount</span>

        <span id="checkoutTotal" style="font-size:1.3rem;font-weight:900;color:var(--orange);"></span>

      </div>

      <div id="checkoutAlert" style="display:none;padding:0.65rem 1rem;border-radius:10px;font-size:0.83rem;font-weight:600;margin-bottom:1rem;"></div>

      <button id="checkoutConfirmBtn" onclick="confirmCheckout()" style="width:100%;padding:0.85rem;border-radius:12px;background:var(--orange);color:#000;border:none;font-weight:800;font-size:0.95rem;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:0.5rem;transition:all 0.2s;">

        <i class="fa-solid fa-check"></i> Place Order

      </button>

    </div>

  </div>

</div>

<style>

@keyframes pdmIn {

  from { opacity:0; transform:scale(0.93) translateY(14px); }

  to   { opacity:1; transform:scale(1) translateY(0); }

}

@media (max-width:640px) {

  #pdmGrid { grid-template-columns:1fr !important; }

  #pdmGrid > div:first-child { border-right:none !important; border-bottom:1px solid var(--border-color); }

}

</style>

<script>

// =====================================================================

// CUSTOMER CART (sessionStorage)

// =====================================================================

const FGCustomerCart = (function () {

  const KEY = 'fg_customer_cart';

  function get() {

    try { return JSON.parse(sessionStorage.getItem(KEY) || '[]'); } catch(e) { return []; }

  }

  function save(cart) {

    sessionStorage.setItem(KEY, JSON.stringify(cart));

    updateCartBadge();

  }

  function add(product, qty) {

    const cart = get();

    const idx  = cart.findIndex(i => i.id === product.id);

    if (idx >= 0) {

      cart[idx].quantity = Math.min(cart[idx].quantity + qty, parseInt(product.qty) || 99);

    } else {

      cart.push({

        id: product.id,

        item_description: product.item_description,

        category: product.category,

        brand: product.brand || '',

        srp: parseFloat(product.srp),

        image_path: product.image_path || null,

        quantity: qty,

        maxQty: parseInt(product.qty) || 99,

      });

    }

    save(cart);

  }

  function remove(id) { save(get().filter(i => i.id !== id)); }

  function updateQty(id, qty) {

    const cart = get();

    const item = cart.find(i => i.id === id);

    if (item) { item.quantity = Math.max(1, Math.min(qty, item.maxQty)); save(cart); }

  }

  function clear() { sessionStorage.removeItem(KEY); updateCartBadge(); }

  function count() { return get().reduce((s, i) => s + i.quantity, 0); }

  function total() { return get().reduce((s, i) => s + i.srp * i.quantity, 0); }

  function updateCartBadge() {

    const n = count();

    const badge = document.getElementById('customerCartBadge');

    if (!badge) return;

    badge.textContent = n > 99 ? '99+' : n;

    badge.style.display = n > 0 ? 'inline-block' : 'none';

  }

  return { get, add, remove, updateQty, clear, count, total, updateCartBadge };

})();

// =====================================================================

// PRODUCT DETAIL MODAL

// =====================================================================

let _pdmProduct  = null;

let _pdmRating   = 5;

let _pdmUser     = null;

function showProductModal(product, user) {

  _pdmProduct = product;

  _pdmUser    = user;

  _pdmRating  = 5;

  const modal = document.getElementById('productDetailModal');

  modal.style.display = 'flex';

  document.body.style.overflow = 'hidden';

  // Image

  const imgWrap = document.getElementById('pdmImageWrap');

  if (product.image_path) {

    imgWrap.innerHTML = `<img src="${escH(product.image_path)}" alt="${escH(product.item_description)}"

      style="width:100%;height:100%;object-fit:cover;"

      onerror="this.parentElement.innerHTML='<i class=\\'fa-solid fa-mobile-screen-button\\'style=\\'font-size:4rem;color:var(--text-secondary);opacity:0.3;\\'></i>'">`;

  } else {

    imgWrap.innerHTML = `<i class="fa-solid fa-mobile-screen-button" style="font-size:4rem;color:var(--text-secondary);opacity:0.3;"></i>`;

  }

  // Basic info

  document.getElementById('pdmCatBadge').textContent = product.category || '';

  document.getElementById('pdmTitle').textContent    = product.item_description || '';

  document.getElementById('pdmBrand').textContent    = product.brand ? '🏷 ' + product.brand : '';

  document.getElementById('pdmPrice').textContent    = '₱' + parseFloat(product.srp).toLocaleString('en-PH', {minimumFractionDigits:2});

  const stockEl = document.getElementById('pdmStock');

  if (product.qty > 0) {

    stockEl.innerHTML = `<span style="color:#28a745;font-weight:700;"><i class="fa-solid fa-circle-check"></i> In Stock</span> <span style="color:var(--text-secondary);">(${product.qty} available)</span>`;

  } else {

    stockEl.innerHTML = `<span style="color:#dc3545;font-weight:700;"><i class="fa-solid fa-circle-xmark"></i> Out of Stock</span>`;

  }

  // Notes/description

  const notesWrap = document.getElementById('pdmNotesWrap');

  if (product.notes && product.notes.trim()) {

    document.getElementById('pdmNotes').textContent = product.notes;

    notesWrap.style.display = 'block';

  } else {

    notesWrap.style.display = 'none';

  }

  // Qty input

  const qtyInput = document.getElementById('pdmQtyInput');

  qtyInput.value = 1;

  qtyInput.max   = product.qty;

  document.getElementById('pdmQtyMax').textContent = 'Max: ' + product.qty;

  // Disable buttons if out of stock

  const _buyBtn  = document.getElementById('pdmBuyNowBtn');

  const _cartBtn = document.getElementById('pdmAddCartBtn');

  if (product.qty <= 0) {

    if (_buyBtn)  { _buyBtn.disabled  = true;  _buyBtn.style.opacity  = '0.4'; }

    if (_cartBtn) { _cartBtn.disabled = true;  _cartBtn.style.opacity = '0.4'; }

  } else {

    if (_buyBtn)  { _buyBtn.disabled  = false; _buyBtn.style.opacity  = '1'; }

    if (_cartBtn) { _cartBtn.disabled = false; _cartBtn.style.opacity = '1'; }

  }









  // Hide alert

  const alertEl = document.getElementById('pdmAlert');

  alertEl.style.display = 'none';

  // Show write review only for customers

  const writeReview = document.getElementById('pdmWriteReview');

  writeReview.style.display = (user && user.role === 'customer') ? 'block' : 'none';

  // Show/hide buttons based on role
  const isTech = user && user.role === 'phone_technician';
  const isCust = user && user.role === 'customer';
  const buyBtn  = document.getElementById('pdmBuyNowBtn');
  const cartBtn = document.getElementById('pdmAddCartBtn');
  const reqBtn  = document.getElementById('pdmRequestBtn');
  if (buyBtn)  buyBtn.style.display  = isCust ? 'flex' : 'none';
  if (cartBtn) cartBtn.style.display = isCust ? 'flex' : 'none';
  if (reqBtn)  reqBtn.style.display  = isTech ? 'flex' : 'none';

  // Star picker

  initStarPicker();

  // Load full detail (reviews + shop info) from backend

  loadProductDetail(product.id);

}

function loadProductDetail(productId) {

  // Reset reviews

  document.getElementById('pdmReviewsList').innerHTML =

    '<div style="text-align:center;padding:2rem;color:var(--text-secondary);font-size:0.85rem;">Loading reviews…</div>';

  document.getElementById('pdmRatingSummary').style.display = 'none';

  document.getElementById('pdmShopInfo').style.display = 'none';

  fetch('api/session/user'customer_orders.php?action=product&id=' + productId)

    .then(r => r.json())

    .then(data => {

      if (!data.success) return;

      // Rating summary

      const avgRating = parseFloat(data.avg_rating || 0);

      const totalRev  = parseInt(data.total_reviews || 0);

      const ratingSum = document.getElementById('pdmRatingSummary');

      ratingSum.style.display = 'flex';

      document.getElementById('pdmStars').innerHTML = renderStars(avgRating);

      document.getElementById('pdmRatingText').textContent =

        avgRating > 0 ? `${avgRating.toFixed(1)} (${totalRev} review${totalRev !== 1 ? 's' : ''})` : 'No reviews yet';

      // Shop info

      const p = data.product;

      if (p && p.shop_name) {

        document.getElementById('pdmShopName').textContent = p.shop_name;

        document.getElementById('pdmShopCity').textContent = p.shop_city ? '📍 ' + p.shop_city : '';

        document.getElementById('pdmShopInfo').style.display = 'block';

      } else if (p && p.seller_first) {

        document.getElementById('pdmShopName').textContent = p.seller_first + ' ' + p.seller_last;

        document.getElementById('pdmShopCity').textContent = '💼 Sales Person';

        document.getElementById('pdmShopInfo').style.display = 'block';

      }

      // Reviews

      renderReviews(data.reviews || []);

    })

    .catch(() => {

      document.getElementById('pdmReviewsList').innerHTML =

        '<div style="text-align:center;padding:1.5rem;color:var(--text-secondary);font-size:0.83rem;">Could not load reviews.</div>';

    });

}

function renderReviews(reviews) {

  const el = document.getElementById('pdmReviewsList');

  if (!reviews.length) {

    el.innerHTML = '<div style="text-align:center;padding:2rem;color:var(--text-secondary);font-size:0.85rem;"><i class="fa-regular fa-comment-dots" style="font-size:2rem;display:block;margin-bottom:0.5rem;opacity:0.3;"></i>No reviews yet. Be the first!</div>';

    return;

  }

  el.innerHTML = reviews.map(r => `

    <div style="background:var(--bg);border:1px solid var(--border-color);border-radius:12px;padding:1rem 1.25rem;">

      <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:0.5rem;">

        <div style="display:flex;align-items:center;gap:0.5rem;">

          <div style="width:32px;height:32px;border-radius:50%;background:rgba(230,168,0,0.15);display:flex;align-items:center;justify-content:center;font-size:0.8rem;font-weight:800;color:var(--orange);">${escH(r.reviewer_name[0] || '?')}</div>

          <span style="font-weight:700;font-size:0.85rem;color:var(--text-primary);">${escH(r.reviewer_name)}</span>

        </div>

        <span style="font-size:0.72rem;color:var(--text-secondary);">${new Date(r.created_at).toLocaleDateString('en-PH')}</span>

      </div>

      <div style="display:flex;gap:2px;margin-bottom:0.5rem;">${renderStars(r.rating)}</div>

      ${r.review_text ? `<p style="font-size:0.83rem;color:var(--text-primary);margin:0;line-height:1.6;">${escH(r.review_text)}</p>` : ''}

    </div>`).join('');

}

function renderStars(rating) {

  let html = '';

  for (let i = 1; i <= 5; i++) {

    if (i <= Math.floor(rating))

      html += '<i class="fa-solid fa-star" style="color:var(--orange);font-size:0.8rem;"></i>';

    else if (i - rating < 1 && i - rating > 0)

      html += '<i class="fa-solid fa-star-half-stroke" style="color:var(--orange);font-size:0.8rem;"></i>';

    else

      html += '<i class="fa-regular fa-star" style="color:var(--border-color);font-size:0.8rem;"></i>';

  }

  return html;

}

function closeProductModal() {

  document.getElementById('productDetailModal').style.display = 'none';

  document.body.style.overflow = '';

}

// Close on backdrop click

document.getElementById('productDetailModal').addEventListener('click', function(e) {

  if (e.target === this) closeProductModal();

});

// Qty controls

function pdmQtyChange(delta) {

  const inp = document.getElementById('pdmQtyInput');

  const max = _pdmProduct ? parseInt(_pdmProduct.qty) : 99;

  inp.value = Math.max(1, Math.min(parseInt(inp.value || 1) + delta, max));

}

function pdmQtyValidate() {

  const inp = document.getElementById('pdmQtyInput');

  const max = _pdmProduct ? parseInt(_pdmProduct.qty) : 99;

  inp.value = Math.max(1, Math.min(parseInt(inp.value || 1), max));

}

// Add to cart

function pdmAddToCart() {

  if (!_pdmProduct || !_pdmUser) return;

  if (_pdmUser.role !== 'customer') {

    pdmShowAlert('Only customers can add to cart.', 'warning'); return;

  }

  const qty = parseInt(document.getElementById('pdmQtyInput').value) || 1;

  FGCustomerCart.add(_pdmProduct, qty);

  pdmShowAlert(`Added ${qty}× to cart! <a href="#" onclick="openCartDrawer();return false;" style="color:var(--orange);font-weight:700;">View Cart â†’</a>`, 'success');

}

// Buy now â€” open checkout directly

function pdmBuyNow() {

  if (!_pdmProduct || !_pdmUser) return;

  if (_pdmUser.role !== 'customer') {

    pdmShowAlert('Only customers can place orders.', 'warning'); return;

  }

  const qty = parseInt(document.getElementById('pdmQtyInput').value) || 1;

  // Temporarily add to cart then open checkout

  FGCustomerCart.add(_pdmProduct, qty);

  closeProductModal();

  window.location.href = 'views/user/customer/checkout.php';

}

function pdmShowAlert(msg, type) {

  const el = document.getElementById('pdmAlert');

  const colors = { success:'rgba(40,167,69,0.12)', warning:'rgba(230,168,0,0.12)', danger:'rgba(220,53,69,0.12)' };

  const textColors = { success:'#28a745', warning:'#c98f00', danger:'#dc3545' };

  el.style.display = 'block';

// Request product from supplier (technician only)
function pdmRequestProduct() {
  if (!_pdmProduct || !_pdmUser) return;
  if (_pdmUser.role !== 'phone_technician') {
    pdmShowAlert('Only technicians can request products from suppliers.', 'warning'); return;
  }
  const qty  = parseInt(document.getElementById('pdmQtyInput').value) || 1;
  const note = window.prompt('Optional note for the supplier (leave blank if none):', '') || '';
  if (note === null) return; // user cancelled prompt
  const supplierId = parseInt(_pdmProduct.supplier_id) || 0;
  if (!supplierId) { pdmShowAlert('Cannot determine supplier for this product.', 'danger'); return; }
  let reqBtn = document.getElementById('pdmRequestBtn');
  if (reqBtn) { reqBtn.disabled = true; reqBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Requesting…'; }
  fetch('api/session/user'technician_marketplace.php', {
    method: 'POST', credentials: 'include',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ action: 'request_product', product_id: _pdmProduct.id, supplier_id: supplierId, quantity: qty, note: note })
  })
    .then(r => r.json())
    .then(d => {
      if (!d.success) throw new Error(d.message || 'Request failed.');
      pdmShowAlert('✅ Request submitted! <a href="views/user/phone_technician/supply-requests.php" style="color:#8b5cf6;font-weight:700;">View Requests →</a>', 'success');
    })
    .catch(err => pdmShowAlert(err.message, 'danger'))
    .finally(() => {
      if (reqBtn) { reqBtn.disabled = false; reqBtn.innerHTML = '<i class="fa-solid fa-paper-plane"></i> Request from Supplier'; }
    });
}

  el.style.background = colors[type] || colors.success;

  el.style.color = textColors[type] || textColors.success;

  el.style.border = `1px solid ${textColors[type] || textColors.success}`;

  el.innerHTML = msg;

}

// Star picker

function initStarPicker() {

  _pdmRating = 5;

  const stars = document.querySelectorAll('.pdm-star-pick');

  stars.forEach(s => {

    s.style.color = 'var(--orange)';

    s.addEventListener('mouseover', function() {

      const val = parseInt(this.dataset.val);

      stars.forEach(st => { st.style.color = parseInt(st.dataset.val) <= val ? 'var(--orange)' : 'var(--border-color)'; });

    });

    s.addEventListener('mouseout', function() {

      stars.forEach(st => { st.style.color = parseInt(st.dataset.val) <= _pdmRating ? 'var(--orange)' : 'var(--border-color)'; });

    });

    s.addEventListener('click', function() {

      _pdmRating = parseInt(this.dataset.val);

      stars.forEach(st => { st.style.color = parseInt(st.dataset.val) <= _pdmRating ? 'var(--orange)' : 'var(--border-color)'; });

    });

  });

}

function pdmSubmitReview() {

  if (!_pdmProduct || !_pdmUser) return;

  const text = document.getElementById('pdmReviewText').value.trim();

  fetch('api/session/user'customer_orders.php', {

    method: 'POST',

    headers: { 'Content-Type': 'application/json' },

    body: JSON.stringify({ action: 'review', product_id: _pdmProduct.id, rating: _pdmRating, review_text: text })

  })

    .then(r => r.json())

    .then(d => {

      if (d.success) {

        document.getElementById('pdmReviewText').value = '';

        document.getElementById('pdmWriteReview').innerHTML = '<div style="color:#28a745;font-weight:700;font-size:0.85rem;"><i class="fa-solid fa-check-circle"></i> Review submitted! Thank you.</div>';

        loadProductDetail(_pdmProduct.id);

      } else {

        alert(d.message || 'Could not submit review.');

      }

    })

    .catch(() => alert('Network error.'));

}

// =====================================================================

// CART DRAWER

// =====================================================================

function openCartDrawer() {

  const user = (window.FGAuth && window.FGAuth.UserStore)

    ? window.FGAuth.UserStore.get()

    : JSON.parse(sessionStorage.getItem('fg_user') || 'null');

  if (!user) { window.location.href = 'login.html'; return; }

  renderCartDrawer();

  document.getElementById('cartDrawerOverlay').style.display = 'block';

  const drawer = document.getElementById('cartDrawer');

  drawer.style.display = 'flex';

  document.body.style.overflow = 'hidden';

}

function closeCartDrawer() {

  document.getElementById('cartDrawerOverlay').style.display = 'none';

  document.getElementById('cartDrawer').style.display = 'none';

  document.body.style.overflow = '';

}

function renderCartDrawer() {

  const cart  = FGCustomerCart.get();

  const items = document.getElementById('cartDrawerItems');

  const footer = document.getElementById('cartDrawerFooter');

  const countEl = document.getElementById('cartDrawerCount');

  const totalEl = document.getElementById('cartDrawerTotal');

  countEl.textContent = FGCustomerCart.count();

  if (!cart.length) {

    items.innerHTML = `<div style="text-align:center;padding:3rem 1rem;color:var(--text-secondary);">

      <i class="fa-solid fa-cart-shopping" style="font-size:3rem;display:block;margin-bottom:1rem;opacity:0.2;"></i>

      <p style="font-size:0.9rem;">Your cart is empty.</p>

      <p style="font-size:0.8rem;">Browse the shop and add products!</p>

    </div>`;

    footer.style.display = 'none';

    return;

  }

  items.innerHTML = cart.map(item => {

    const img = item.image_path

      ? `<img src="${escH(item.image_path)}" style="width:56px;height:56px;border-radius:10px;object-fit:cover;flex-shrink:0;" onerror="this.style.display='none'">`

      : `<div style="width:56px;height:56px;border-radius:10px;background:var(--bg);border:1px solid var(--border-color);display:flex;align-items:center;justify-content:center;flex-shrink:0;"><i class="fa-solid fa-box" style="color:var(--text-secondary);"></i></div>`;

    return `

      <div style="display:flex;gap:0.85rem;padding:0.85rem 0;border-bottom:1px solid var(--border-color);align-items:flex-start;">

        ${img}

        <div style="flex:1;min-width:0;">

          <div style="font-weight:700;font-size:0.85rem;color:var(--text-primary);margin-bottom:0.2rem;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">${escH(item.item_description)}</div>

          <div style="font-size:0.72rem;color:var(--text-secondary);margin-bottom:0.5rem;">${escH(item.category)}${item.brand ? ' · ' + escH(item.brand) : ''}</div>

          <div style="display:flex;align-items:center;justify-content:space-between;gap:0.5rem;">

            <div style="display:flex;align-items:center;gap:0.3rem;">

              <button onclick="cartQtyChange(${item.id},-1)" style="width:26px;height:26px;border-radius:6px;border:1.5px solid var(--border-color);background:transparent;color:var(--text-primary);cursor:pointer;font-size:0.9rem;display:flex;align-items:center;justify-content:center;">âˆ’</button>

              <span style="font-size:0.85rem;font-weight:700;min-width:24px;text-align:center;">${item.quantity}</span>

              <button onclick="cartQtyChange(${item.id},1)" style="width:26px;height:26px;border-radius:6px;border:1.5px solid var(--border-color);background:transparent;color:var(--text-primary);cursor:pointer;font-size:0.9rem;display:flex;align-items:center;justify-content:center;">+</button>

            </div>

            <span style="font-weight:800;color:var(--orange);font-size:0.9rem;">₱${(item.srp * item.quantity).toLocaleString('en-PH',{minimumFractionDigits:2})}</span>

            <button onclick="cartRemoveItem(${item.id})" style="width:26px;height:26px;border-radius:6px;border:1.5px solid rgba(220,53,69,0.3);background:transparent;color:#dc3545;cursor:pointer;font-size:0.75rem;display:flex;align-items:center;justify-content:center;flex-shrink:0;">

              <i class="fa-solid fa-trash"></i>

            </button>

          </div>

        </div>

      </div>`;

  }).join('');

  totalEl.textContent = '₱' + FGCustomerCart.total().toLocaleString('en-PH', {minimumFractionDigits:2});

  footer.style.display = 'block';

}

function cartQtyChange(id, delta) {

  const cart = FGCustomerCart.get();

  const item = cart.find(i => i.id === id);

  if (!item) return;

  const newQty = item.quantity + delta;

  if (newQty < 1) { FGCustomerCart.remove(id); }

  else { FGCustomerCart.updateQty(id, newQty); }

  renderCartDrawer();

}

function cartRemoveItem(id) {

  FGCustomerCart.remove(id);

  renderCartDrawer();

}

function cartClearAll() {

  if (!confirm('Clear all items from cart?')) return;

  FGCustomerCart.clear();

  renderCartDrawer();

}

function cartCheckout() {

  closeCartDrawer();

  window.location.href = 'views/user/customer/checkout.php';

}

// =====================================================================

// CHECKOUT MODAL

// =====================================================================

let _checkoutPayMethod = 'cod';

let _checkoutItems     = [];

function openCheckoutModal() {

  window.location.href = 'views/user/customer/checkout.php';

}

function _openCheckoutModalLegacy() {

  _checkoutItems = FGCustomerCart.get();

  if (!_checkoutItems.length) { alert('Your cart is empty.'); return; }

  _checkoutPayMethod = 'cod';

  document.querySelectorAll('.pay-method-btn').forEach(b => {

    b.style.borderColor = 'var(--border-color)';

    b.style.background  = 'transparent';

    b.style.color       = 'var(--text-secondary)';

  });

  const codBtn = document.getElementById('payBtn_cod');

  if (codBtn) { codBtn.style.borderColor='var(--orange)'; codBtn.style.background='rgba(230,168,0,0.1)'; codBtn.style.color='var(--orange)'; }

  document.querySelectorAll('input[name="payMethod"]').forEach(r => { r.checked = r.value === 'cod'; });

  // Summary

  const summary = document.getElementById('checkoutSummary');

  summary.innerHTML = _checkoutItems.map(item => `

    <div style="display:flex;justify-content:space-between;align-items:center;padding:0.4rem 0;border-bottom:1px solid var(--border-color);">

      <div style="flex:1;min-width:0;">

        <div style="font-size:0.82rem;font-weight:700;color:var(--text-primary);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">${escH(item.item_description)}</div>

        <div style="font-size:0.72rem;color:var(--text-secondary);">×${item.quantity} @ ₱${item.srp.toLocaleString('en-PH',{minimumFractionDigits:2})}</div>

      </div>

      <span style="font-weight:800;color:var(--orange);font-size:0.85rem;margin-left:0.75rem;">₱${(item.srp*item.quantity).toLocaleString('en-PH',{minimumFractionDigits:2})}</span>

    </div>`).join('');

  document.getElementById('checkoutTotal').textContent = '₱' + FGCustomerCart.total().toLocaleString('en-PH',{minimumFractionDigits:2});

  document.getElementById('checkoutNotes').value = '';

  document.getElementById('checkoutAlert').style.display = 'none';

  document.getElementById('checkoutConfirmBtn').disabled = false;

  document.getElementById('checkoutConfirmBtn').innerHTML = '<i class="fa-solid fa-check"></i> Place Order';

  const modal = document.getElementById('checkoutModal');

  modal.style.display = 'flex';

  document.body.style.overflow = 'hidden';

}

function closeCheckoutModal() {

  document.getElementById('checkoutModal').style.display = 'none';

  document.body.style.overflow = '';

}

function updatePayMethod(radio) {

  _checkoutPayMethod = radio.value;

  document.querySelectorAll('.pay-method-btn').forEach(b => {

    b.style.borderColor = 'var(--border-color)';

    b.style.background  = 'transparent';

    b.style.color       = 'var(--text-secondary)';

  });

  const active = document.getElementById('payBtn_' + radio.value);

  if (active) { active.style.borderColor='var(--orange)'; active.style.background='rgba(230,168,0,0.1)'; active.style.color='var(--orange)'; }

}

function confirmCheckout() {

  const items = FGCustomerCart.get();

  if (!items.length) return;

  const btn   = document.getElementById('checkoutConfirmBtn');

  const alert = document.getElementById('checkoutAlert');

  const notes = document.getElementById('checkoutNotes').value.trim();

  btn.disabled = true;

  btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Placing orders…';

  alert.style.display = 'none';

  // Place one order per cart item

  const promises = items.map(item =>

    fetch('api/session/user'customer_orders.php', {

      method: 'POST',

      headers: { 'Content-Type': 'application/json' },

      body: JSON.stringify({

        action: 'place',

        product_id: item.id,

        quantity: item.quantity,

        payment_method: _checkoutPayMethod,

        notes: notes,

      })

    }).then(r => r.json())

  );

  Promise.all(promises)

    .then(results => {

      const failed = results.filter(r => !r.success);

      if (failed.length === 0) {

        FGCustomerCart.clear();

        closeCheckoutModal();

        showOrderSuccessToast();

      } else {

        const msg = failed.map(r => r.message).join(', ');

        alert.style.display = 'block';

        alert.style.background = 'rgba(220,53,69,0.1)';

        alert.style.color = '#dc3545';

        alert.style.border = '1px solid rgba(220,53,69,0.3)';

        alert.innerHTML = '<i class="fa-solid fa-circle-exclamation"></i> ' + escH(msg);

        btn.disabled = false;

        btn.innerHTML = '<i class="fa-solid fa-check"></i> Place Order';

      }

    })

    .catch(() => {

      alert.style.display = 'block';

      alert.style.background = 'rgba(220,53,69,0.1)';

      alert.style.color = '#dc3545';

      alert.style.border = '1px solid rgba(220,53,69,0.3)';

      alert.innerHTML = '<i class="fa-solid fa-circle-exclamation"></i> Network error. Please try again.';

      btn.disabled = false;

      btn.innerHTML = '<i class="fa-solid fa-check"></i> Place Order';

    });

}

function showOrderSuccessToast() {

  const toast = document.createElement('div');

  toast.style.cssText = 'position:fixed;bottom:2rem;left:50%;transform:translateX(-50%);background:#28a745;color:#fff;padding:1rem 2rem;border-radius:14px;font-weight:700;font-size:0.95rem;z-index:99999;box-shadow:0 8px 30px rgba(40,167,69,0.4);display:flex;align-items:center;gap:0.6rem;animation:pdmIn 0.3s ease;';

  toast.innerHTML = '<i class="fa-solid fa-check-circle"></i> Order placed successfully! Check your orders in the dashboard.';

  document.body.appendChild(toast);

  setTimeout(() => toast.remove(), 4500);

}

function escH(str) {

  return String(str || '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');

}

// Show cart icon for customers

document.addEventListener('DOMContentLoaded', function() {

  const user = (window.FGAuth && window.FGAuth.UserStore)

    ? window.FGAuth.UserStore.get()

    : JSON.parse(sessionStorage.getItem('fg_user') || 'null');

  if (user && user.role === 'customer') {

    const wrap = document.getElementById('customerCartWrap');

    if (wrap) wrap.style.display = 'block';

    FGCustomerCart.updateCartBadge();

  }

});

</script>

<!-- =====================================================================

     CART DRAWER

===================================================================== -->

<div id="cartDrawerOverlay" onclick="closeCartDrawer()" style="display:none;position:fixed;inset:0;z-index:8900;background:rgba(0,0,0,0.5);backdrop-filter:blur(4px);"></div>

<div id="cartDrawer" style="display:none;position:fixed;top:0;right:0;bottom:0;z-index:8950;width:100%;max-width:420px;background:var(--bg-card);border-left:1px solid var(--border-color);box-shadow:-20px 0 60px rgba(0,0,0,0.4);flex-direction:column;overflow:hidden;">

  <!-- Header -->

  <div style="padding:1.25rem 1.5rem;border-bottom:1px solid var(--border-color);display:flex;align-items:center;justify-content:space-between;flex-shrink:0;">

    <h5 style="margin:0;font-weight:800;font-size:1.05rem;color:var(--text-primary);display:flex;align-items:center;gap:0.5rem;">

      <i class="fa-solid fa-cart-shopping" style="color:var(--orange);"></i> My Cart

      <span id="cartDrawerCount" style="background:var(--orange);color:#000;font-size:0.65rem;font-weight:800;padding:0.1rem 0.45rem;border-radius:10px;"></span>

    </h5>

    <button onclick="closeCartDrawer()" style="width:32px;height:32px;border-radius:8px;border:1.5px solid var(--border-color);background:transparent;color:var(--text-secondary);cursor:pointer;display:flex;align-items:center;justify-content:center;font-size:0.9rem;transition:all 0.2s;" onmouseenter="this.style.borderColor='#dc3545';this.style.color='#dc3545'" onmouseleave="this.style.borderColor='var(--border-color)';this.style.color='var(--text-secondary)'">

      <i class="fa-solid fa-xmark"></i>

    </button>

  </div>

  <!-- Items -->

  <div id="cartDrawerItems" style="flex:1;overflow-y:auto;padding:1rem 1.5rem;"></div>

  <!-- Footer -->

  <div id="cartDrawerFooter" style="padding:1.25rem 1.5rem;border-top:1px solid var(--border-color);flex-shrink:0;display:none;">

    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem;">

      <span style="font-size:0.88rem;color:var(--text-secondary);font-weight:600;">Total</span>

      <span id="cartDrawerTotal" style="font-size:1.4rem;font-weight:900;color:var(--orange);"></span>

    </div>

    <button onclick="cartCheckout()" style="width:100%;padding:0.85rem;border-radius:12px;background:var(--orange);color:#000;border:none;font-weight:800;font-size:0.95rem;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:0.5rem;transition:all 0.2s;" onmouseenter="this.style.background='var(--orange-hover)'" onmouseleave="this.style.background='var(--orange)'">

      <i class="fa-solid fa-bolt"></i> Checkout

    </button>

    <button onclick="cartClearAll()" style="width:100%;margin-top:0.5rem;padding:0.6rem;border-radius:10px;background:transparent;color:var(--text-secondary);border:1.5px solid var(--border-color);font-weight:600;font-size:0.82rem;cursor:pointer;transition:all 0.2s;" onmouseenter="this.style.borderColor='#dc3545';this.style.color='#dc3545'" onmouseleave="this.style.borderColor='var(--border-color)';this.style.color='var(--text-secondary)'">

      <i class="fa-solid fa-trash"></i> Clear Cart

    </button>

  </div>

</div>

<!-- =====================================================================

     CHECKOUT MODAL

===================================================================== -->

<div id="checkoutModal" style="display:none;position:fixed;inset:0;z-index:9100;background:rgba(0,0,0,0.7);backdrop-filter:blur(6px);align-items:center;justify-content:center;padding:1rem;">

  <div style="background:var(--bg-card);border:1px solid var(--border-color);border-radius:20px;width:100%;max-width:480px;box-shadow:0 32px 80px rgba(0,0,0,0.5);animation:pdmIn 0.25s cubic-bezier(0.16,1,0.3,1);">

    <div style="padding:1.5rem 1.75rem 1.25rem;border-bottom:1px solid var(--border-color);display:flex;align-items:center;justify-content:space-between;">

      <h5 style="margin:0;font-weight:800;font-size:1.05rem;color:var(--text-primary);display:flex;align-items:center;gap:0.5rem;">

        <i class="fa-solid fa-bag-shopping" style="color:var(--orange);"></i> Checkout

      </h5>

      <button onclick="closeCheckoutModal()" style="width:32px;height:32px;border-radius:8px;border:1.5px solid var(--border-color);background:transparent;color:var(--text-secondary);cursor:pointer;display:flex;align-items:center;justify-content:center;font-size:0.9rem;">

        <i class="fa-solid fa-xmark"></i>

      </button>

    </div>

    <div style="padding:1.5rem 1.75rem;">

      <!-- Order summary -->

      <div id="checkoutSummary" style="background:var(--bg);border-radius:12px;padding:1rem;margin-bottom:1.25rem;border:1px solid var(--border-color);max-height:200px;overflow-y:auto;"></div>

      <!-- Payment method -->

      <div style="margin-bottom:1.25rem;">

        <div style="font-size:0.75rem;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:var(--text-secondary);margin-bottom:0.6rem;">Payment Method</div>

        <div style="display:flex;gap:0.5rem;flex-wrap:wrap;">

          <label style="flex:1;min-width:100px;cursor:pointer;">

            <input type="radio" name="payMethod" value="cod" checked style="display:none;" onchange="updatePayMethod(this)">

            <div class="pay-method-btn pay-active" id="payBtn_cod" style="padding:0.6rem 0.75rem;border-radius:10px;border:2px solid var(--orange);background:rgba(230,168,0,0.1);text-align:center;font-size:0.8rem;font-weight:700;color:var(--orange);transition:all 0.2s;">

              <i class="fa-solid fa-money-bill-wave"></i><br>Cash on Delivery

            </div>

          </label>

          <label style="flex:1;min-width:100px;cursor:pointer;">

            <input type="radio" name="payMethod" value="gcash" style="display:none;" onchange="updatePayMethod(this)">

            <div class="pay-method-btn" id="payBtn_gcash" style="padding:0.6rem 0.75rem;border-radius:10px;border:2px solid var(--border-color);background:transparent;text-align:center;font-size:0.8rem;font-weight:700;color:var(--text-secondary);transition:all 0.2s;">

              <i class="fa-solid fa-mobile-screen"></i><br>GCash

            </div>

          </label>

          <label style="flex:1;min-width:100px;cursor:pointer;">

            <input type="radio" name="payMethod" value="card" style="display:none;" onchange="updatePayMethod(this)">

            <div class="pay-method-btn" id="payBtn_card" style="padding:0.6rem 0.75rem;border-radius:10px;border:2px solid var(--border-color);background:transparent;text-align:center;font-size:0.8rem;font-weight:700;color:var(--text-secondary);transition:all 0.2s;">

              <i class="fa-solid fa-credit-card"></i><br>Card

            </div>

          </label>

        </div>

      </div>

      <!-- Notes -->

      <div style="margin-bottom:1.25rem;">

        <div style="font-size:0.75rem;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:var(--text-secondary);margin-bottom:0.5rem;">Order Notes <span style="font-weight:400;text-transform:none;">(optional)</span></div>

        <textarea id="checkoutNotes" placeholder="Any special instructions..." rows="2" style="width:100%;background:var(--bg);border:1.5px solid var(--border-color);border-radius:10px;color:var(--text-primary);padding:0.6rem 0.85rem;font-size:0.83rem;resize:none;outline:none;font-family:inherit;" onfocus="this.style.borderColor='var(--orange)'" onblur="this.style.borderColor='var(--border-color)'"></textarea>

      </div>

      <!-- Total -->

      <div style="display:flex;justify-content:space-between;align-items:center;padding:0.75rem 1rem;background:var(--bg);border-radius:10px;margin-bottom:1.25rem;border:1px solid var(--border-color);">

        <span style="font-weight:700;color:var(--text-primary);">Total Amount</span>

        <span id="checkoutTotal" style="font-size:1.3rem;font-weight:900;color:var(--orange);"></span>

      </div>

      <div id="checkoutAlert" style="display:none;padding:0.65rem 1rem;border-radius:10px;font-size:0.83rem;font-weight:600;margin-bottom:1rem;"></div>

      <button id="checkoutConfirmBtn" onclick="confirmCheckout()" style="width:100%;padding:0.85rem;border-radius:12px;background:var(--orange);color:#000;border:none;font-weight:800;font-size:0.95rem;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:0.5rem;transition:all 0.2s;">

        <i class="fa-solid fa-check"></i> Place Order

      </button>

    </div>

  </div>

</div>

<style>

@keyframes pdmIn {

  from { opacity:0; transform:scale(0.93) translateY(14px); }

  to   { opacity:1; transform:scale(1) translateY(0); }

}

@media (max-width:640px) {

  #pdmGrid { grid-template-columns:1fr !important; }

  #pdmGrid > div:first-child { border-right:none !important; border-bottom:1px solid var(--border-color); }

}

</style>


<!-- =====================================================================
     TECHNICIAN PROFILE + BOOKING MODAL
===================================================================== -->
<div id="techProfileModal" style="display:none;position:fixed;inset:0;z-index:9500;background:rgba(0,0,0,0.72);backdrop-filter:blur(6px);align-items:flex-start;justify-content:center;padding:1rem;overflow-y:auto;">
  <div style="background:var(--bg-card);border:1px solid var(--border-color);border-radius:20px;width:100%;max-width:780px;margin:2rem auto;box-shadow:0 32px 80px rgba(0,0,0,0.5);animation:pdmIn 0.25s cubic-bezier(0.16,1,0.3,1);">

    <!-- Header -->
    <div style="padding:1.25rem 1.5rem;border-bottom:1px solid var(--border-color);display:flex;align-items:center;justify-content:space-between;">
      <h5 style="margin:0;font-weight:800;font-size:1rem;color:var(--text-primary);">🔧 Technician Profile</h5>
      <button onclick="closeTechProfileModal()" style="width:32px;height:32px;border-radius:8px;border:1.5px solid var(--border-color);background:transparent;cursor:pointer;display:flex;align-items:center;justify-content:center;color:var(--text-secondary);font-size:1rem;" onmouseenter="this.style.borderColor='#dc3545';this.style.color='#dc3545'" onmouseleave="this.style.borderColor='var(--border-color)';this.style.color='var(--text-secondary)'"><i class="fa-solid fa-xmark"></i></button>
    </div>

    <!-- Loading state -->
    <div id="tpmLoading" style="text-align:center;padding:3rem;color:var(--text-secondary);">
      <div style="width:32px;height:32px;border:3px solid var(--border-color);border-top-color:var(--orange);border-radius:50%;animation:spin 0.7s linear infinite;margin:0 auto 1rem;"></div>
      Loading technician profile…
    </div>

    <!-- Profile content -->
    <div id="tpmContent" style="display:none;">

      <!-- Profile hero -->
      <div style="padding:1.5rem;display:flex;gap:1.25rem;align-items:flex-start;border-bottom:1px solid var(--border-color);">
        <div id="tpmAvatar" style="width:80px;height:80px;border-radius:50%;background:linear-gradient(135deg,rgba(230,168,0,0.2),rgba(230,168,0,0.06));border:3px solid rgba(230,168,0,0.35);display:flex;align-items:center;justify-content:center;font-size:2rem;flex-shrink:0;overflow:hidden;"></div>
        <div style="flex:1;min-width:0;">
          <div style="display:flex;align-items:center;gap:0.75rem;flex-wrap:wrap;margin-bottom:0.4rem;">
            <h4 id="tpmName" style="margin:0;font-size:1.25rem;font-weight:800;color:var(--text-primary);"></h4>
            <span id="tpmAvailBadge" style="padding:0.2rem 0.65rem;border-radius:20px;font-size:0.72rem;font-weight:700;"></span>
          </div>
          <div id="tpmRating" style="display:flex;align-items:center;gap:0.5rem;margin-bottom:0.5rem;"></div>
          <div id="tpmSpecs" style="display:flex;flex-wrap:wrap;gap:0.4rem;margin-bottom:0.5rem;"></div>
          <div id="tpmBio" style="font-size:0.85rem;color:var(--text-secondary);line-height:1.6;"></div>
        </div>
      </div>

      <!-- Description (full shop/service description) -->
      <div id="tpmDescSection" style="display:none;padding:1.25rem 1.5rem;border-bottom:1px solid var(--border-color);">
        <div style="font-size:0.75rem;font-weight:700;text-transform:uppercase;letter-spacing:0.5px;color:var(--text-secondary);margin-bottom:0.6rem;">📝 About / Shop Description</div>
        <div id="tpmDescription" style="font-size:0.88rem;color:var(--text-primary);line-height:1.7;white-space:pre-line;"></div>
      </div>

      <!-- Stats row -->
      <div style="display:grid;grid-template-columns:repeat(3,1fr);border-bottom:1px solid var(--border-color);">
        <div style="padding:1rem;text-align:center;border-right:1px solid var(--border-color);">
          <div id="tpmRepairs" style="font-size:1.5rem;font-weight:800;color:var(--orange);">—</div>
          <div style="font-size:0.72rem;color:var(--text-secondary);font-weight:600;">Repairs Done</div>
        </div>
        <div style="padding:1rem;text-align:center;border-right:1px solid var(--border-color);">
          <div id="tpmExp" style="font-size:1.5rem;font-weight:800;color:var(--orange);">—</div>
          <div style="font-size:0.72rem;color:var(--text-secondary);font-weight:600;">Years Exp.</div>
        </div>
        <div style="padding:1rem;text-align:center;">
          <div id="tpmRatingNum" style="font-size:1.5rem;font-weight:800;color:var(--orange);">—</div>
          <div style="font-size:0.72rem;color:var(--text-secondary);font-weight:600;">Rating</div>
        </div>
      </div>

      <!-- Shop Photos Gallery (up to 5) -->
      <div id="tpmShopGalleryWrap" style="display:none;padding:1.25rem 1.5rem;border-bottom:1px solid var(--border-color);">
        <div style="font-size:0.75rem;font-weight:700;text-transform:uppercase;letter-spacing:0.5px;color:var(--text-secondary);margin-bottom:0.75rem;">🏪 Shop Photos</div>
        <div id="tpmShopGallery" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(110px,1fr));gap:0.5rem;"></div>
      </div>

      <!-- Legacy single shop image (kept for backward compat, hidden if gallery present) -->
      <div id="tpmShopImageWrap" style="display:none;padding:1.25rem 1.5rem;border-bottom:1px solid var(--border-color);"></div>

      <!-- Work Videos -->
      <div id="tpmWorkVideosWrap" style="display:none;padding:1.25rem 1.5rem;border-bottom:1px solid var(--border-color);">
        <div style="font-size:0.75rem;font-weight:700;text-transform:uppercase;letter-spacing:0.5px;color:var(--text-secondary);margin-bottom:0.75rem;">🎬 Work Videos</div>
        <div id="tpmWorkVideos" style="display:flex;flex-direction:column;gap:0.85rem;"></div>
      </div>

      <!-- Shop & Location -->
      <div id="tpmShopSection" style="padding:1.25rem 1.5rem;border-bottom:1px solid var(--border-color);">
        <div style="font-size:0.75rem;font-weight:700;text-transform:uppercase;letter-spacing:0.5px;color:var(--text-secondary);margin-bottom:0.75rem;">🏪 Shop & Location</div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:0.75rem;font-size:0.85rem;">
          <div><span style="color:var(--text-secondary);">Shop Name:</span> <span id="tpmShopName" style="font-weight:700;color:var(--text-primary);">—</span></div>
          <div><span style="color:var(--text-secondary);">City:</span> <span id="tpmCity" style="font-weight:700;color:var(--text-primary);">—</span></div>
          <div style="grid-column:1/-1;display:flex;align-items:flex-start;gap:0.4rem;">
            <i class="fa-solid fa-location-dot" style="color:var(--orange);margin-top:0.15rem;flex-shrink:0;font-size:0.8rem;"></i>
            <span id="tpmAddress" style="font-weight:600;color:var(--text-primary);line-height:1.5;">—</span>
          </div>
        </div>
      </div>

      <!-- Documents -->
      <div id="tpmDocsSection" style="padding:1.25rem 1.5rem;border-bottom:1px solid var(--border-color);">
        <div style="font-size:0.75rem;font-weight:700;text-transform:uppercase;letter-spacing:0.5px;color:var(--text-secondary);margin-bottom:0.75rem;">Credentials & Documents</div>
        <div id="tpmDocs" style="display:flex;flex-wrap:wrap;gap:0.6rem;"></div>
      </div>

      <!-- Reviews -->
      <div id="tpmReviewsSection" style="padding:1.25rem 1.5rem;border-bottom:1px solid var(--border-color);">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:0.75rem;">
          <div style="font-size:0.75rem;font-weight:700;text-transform:uppercase;letter-spacing:0.5px;color:var(--text-secondary);">Customer Reviews</div>
          <div id="tpmReviewCount" style="font-size:0.72rem;color:var(--text-secondary);"></div>
        </div>
        <div id="tpmReviews" style="display:flex;flex-direction:column;gap:0.85rem;"></div>
      </div>

      <!-- Book Now button -->
      <div id="tpmBookSection" style="padding:1.25rem 1.5rem;display:flex;flex-direction:column;gap:0.75rem;">
        <button id="tpmBookBtn" onclick="msgTechnician()" style="width:100%;padding:0.85rem;border-radius:12px;background:var(--orange);color:#000;border:none;font-weight:800;font-size:1rem;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:0.5rem;transition:all 0.2s;" onmouseenter="this.style.background='var(--orange-hover)'" onmouseleave="this.style.background='var(--orange)'">
          <i class="fa-solid fa-comment-dots"></i> Message Technician
        </button>
        <p style="text-align:center;font-size:0.78rem;color:var(--text-secondary);margin:0;">Chat first, then book a repair from within the conversation.</p>
        <p id="tpmLoginHint" style="display:none;text-align:center;font-size:0.83rem;color:var(--text-secondary);margin:0;">
          <a href="login.html" style="color:var(--orange);font-weight:700;">Log in</a> to contact a technician
        </p>
      </div>

    </div><!-- /tpmContent -->

    <!-- Booking Form (hidden initially) -->
    <div id="tpmBookingForm" style="display:none;padding:1.5rem;">
      <div style="display:flex;align-items:center;gap:0.75rem;margin-bottom:1.25rem;">
        <button onclick="hideBookingForm()" style="width:32px;height:32px;border-radius:8px;border:1.5px solid var(--border-color);background:transparent;cursor:pointer;display:flex;align-items:center;justify-content:center;color:var(--text-secondary);font-size:0.9rem;"><i class="fa-solid fa-arrow-left"></i></button>
        <h5 style="margin:0;font-weight:800;font-size:1rem;color:var(--text-primary);">📋 Repair Booking Form</h5>
      </div>

      <div id="bookingAlert" style="display:none;padding:0.75rem 1rem;border-radius:10px;font-size:0.85rem;font-weight:600;margin-bottom:1rem;"></div>

      <!-- Service Type Selector -->
      <div style="margin-bottom:1.1rem;">
        <label style="display:block;font-size:0.82rem;font-weight:700;color:var(--text-primary);margin-bottom:0.5rem;">Service Type <span style="color:#dc3545;">*</span></label>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:0.65rem;">
          <label id="bfSvcHome" onclick="bfPickServiceType('home_service')" style="display:flex;align-items:center;gap:0.7rem;padding:0.75rem 1rem;border:2px solid var(--border-color);border-radius:12px;cursor:pointer;transition:all 0.2s;background:var(--bg);">
            <span style="font-size:1.4rem;">🏠</span>
            <div>
              <div style="font-size:0.84rem;font-weight:700;color:var(--text-primary);">Home Service</div>
              <div style="font-size:0.7rem;color:var(--text-secondary);">Technician visits you</div>
            </div>
          </label>
          <label id="bfSvcShop" onclick="bfPickServiceType('shop_fix')" style="display:flex;align-items:center;gap:0.7rem;padding:0.75rem 1rem;border:2px solid var(--orange);border-radius:12px;cursor:pointer;transition:all 0.2s;background:rgba(230,168,0,0.07);">
            <span style="font-size:1.4rem;">🏪</span>
            <div>
              <div style="font-size:0.84rem;font-weight:700;color:var(--text-primary);">In-Shop Fix</div>
              <div style="font-size:0.7rem;color:var(--text-secondary);">Bring to technician shop</div>
            </div>
          </label>
        </div>
        <input type="hidden" id="bfServiceType" value="shop_fix">
      </div>

      <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
        <div>
          <label style="display:block;font-size:0.82rem;font-weight:700;color:var(--text-primary);margin-bottom:0.4rem;">Full Name <span style="color:#dc3545;">*</span></label>
          <input type="text" id="bfName" style="width:100%;padding:0.65rem 0.9rem;border:1.5px solid var(--border-color);border-radius:10px;background:var(--bg);color:var(--text-primary);font-size:0.88rem;outline:none;font-family:inherit;transition:border-color 0.2s;" placeholder="Your full name" onfocus="this.style.borderColor='var(--orange)'" onblur="this.style.borderColor='var(--border-color)'">
        </div>
        <div>
          <label style="display:block;font-size:0.82rem;font-weight:700;color:var(--text-primary);margin-bottom:0.4rem;">Contact Number <span style="color:#dc3545;">*</span></label>
          <input type="tel" id="bfContact" style="width:100%;padding:0.65rem 0.9rem;border:1.5px solid var(--border-color);border-radius:10px;background:var(--bg);color:var(--text-primary);font-size:0.88rem;outline:none;font-family:inherit;transition:border-color 0.2s;" placeholder="+63 9XX XXX XXXX" onfocus="this.style.borderColor='var(--orange)'" onblur="this.style.borderColor='var(--border-color)'">
        </div>
        <div style="grid-column:1/-1;">
          <label style="display:block;font-size:0.82rem;font-weight:700;color:var(--text-primary);margin-bottom:0.4rem;">Address <span style="color:#dc3545;">*</span></label>
          <input type="text" id="bfAddress" style="width:100%;padding:0.65rem 0.9rem;border:1.5px solid var(--border-color);border-radius:10px;background:var(--bg);color:var(--text-primary);font-size:0.88rem;outline:none;font-family:inherit;transition:border-color 0.2s;" placeholder="House/Unit, Street, Barangay, City" onfocus="this.style.borderColor='var(--orange)'" onblur="this.style.borderColor='var(--border-color)'">
        </div>
        <div>
          <label style="display:block;font-size:0.82rem;font-weight:700;color:var(--text-primary);margin-bottom:0.4rem;">Device Name <span style="color:#dc3545;">*</span></label>
          <input type="text" id="bfDevice" style="width:100%;padding:0.65rem 0.9rem;border:1.5px solid var(--border-color);border-radius:10px;background:var(--bg);color:var(--text-primary);font-size:0.88rem;outline:none;font-family:inherit;transition:border-color 0.2s;" placeholder="e.g. iPhone 14, Samsung S23" onfocus="this.style.borderColor='var(--orange)'" onblur="this.style.borderColor='var(--border-color)'">
        </div>
        <div>
          <label style="display:block;font-size:0.82rem;font-weight:700;color:var(--text-primary);margin-bottom:0.4rem;">Preferred Schedule</label>
          <input type="datetime-local" id="bfSchedule" style="width:100%;padding:0.65rem 0.9rem;border:1.5px solid var(--border-color);border-radius:10px;background:var(--bg);color:var(--text-primary);font-size:0.88rem;outline:none;font-family:inherit;transition:border-color 0.2s;" onfocus="this.style.borderColor='var(--orange)'" onblur="this.style.borderColor='var(--border-color)'">
        </div>
        <div style="grid-column:1/-1;">
          <label style="display:block;font-size:0.82rem;font-weight:700;color:var(--text-primary);margin-bottom:0.4rem;">Fault Description <span style="color:#dc3545;">*</span></label>
          <textarea id="bfFault" rows="3" style="width:100%;padding:0.65rem 0.9rem;border:1.5px solid var(--border-color);border-radius:10px;background:var(--bg);color:var(--text-primary);font-size:0.88rem;outline:none;resize:vertical;font-family:inherit;transition:border-color 0.2s;" placeholder="Describe the problem with your device…" onfocus="this.style.borderColor='var(--orange)'" onblur="this.style.borderColor='var(--border-color)'"></textarea>
        </div>
        <div style="grid-column:1/-1;">
          <label style="display:block;font-size:0.82rem;font-weight:700;color:var(--text-primary);margin-bottom:0.4rem;">History of Phone</label>
          <textarea id="bfHistory" rows="2" style="width:100%;padding:0.65rem 0.9rem;border:1.5px solid var(--border-color);border-radius:10px;background:var(--bg);color:var(--text-primary);font-size:0.88rem;outline:none;resize:vertical;font-family:inherit;transition:border-color 0.2s;" placeholder="Previous repairs, drops, water damage, etc." onfocus="this.style.borderColor='var(--orange)'" onblur="this.style.borderColor='var(--border-color)'"></textarea>
        </div>
        <div style="grid-column:1/-1;">
          <label style="display:block;font-size:0.82rem;font-weight:700;color:var(--text-primary);margin-bottom:0.4rem;">Expected Fix</label>
          <textarea id="bfExpected" rows="2" style="width:100%;padding:0.65rem 0.9rem;border:1.5px solid var(--border-color);border-radius:10px;background:var(--bg);color:var(--text-primary);font-size:0.88rem;outline:none;resize:vertical;font-family:inherit;transition:border-color 0.2s;" placeholder="What do you expect to be fixed or resolved?" onfocus="this.style.borderColor='var(--orange)'" onblur="this.style.borderColor='var(--border-color)'"></textarea>
        </div>

        <!-- Phone Photo Upload -->
        <div style="grid-column:1/-1;">
          <label style="display:block;font-size:0.82rem;font-weight:700;color:var(--text-primary);margin-bottom:0.4rem;">
            📷 Photo of Phone <span style="font-weight:400;color:var(--text-secondary);font-size:0.78rem;">(optional — helps the technician assess the issue)</span>
          </label>
          <label for="bfPhonePhoto" style="display:flex;align-items:center;gap:0.75rem;padding:0.75rem 1rem;border:2px dashed var(--border-color);border-radius:10px;cursor:pointer;transition:border-color 0.2s;background:var(--bg);" onmouseenter="this.style.borderColor='var(--orange)'" onmouseleave="this.style.borderColor='var(--border-color)'">
            <i class="fa-solid fa-camera" style="color:var(--orange);font-size:1.2rem;"></i>
            <span style="font-size:0.85rem;color:var(--text-secondary);">Click to upload a photo of your phone</span>
          </label>
          <input type="file" id="bfPhonePhoto" accept="image/*" style="display:none;" onchange="previewPhonePhoto(this)">
          <div id="bfPhonePreview" style="display:none;margin-top:0.75rem;position:relative;display:none;">
            <img id="bfPhonePreviewImg" style="width:100%;max-height:200px;object-fit:contain;border-radius:10px;border:1px solid var(--border-color);">
            <button onclick="clearPhonePhoto()" style="position:absolute;top:0.4rem;right:0.4rem;width:28px;height:28px;border-radius:50%;background:rgba(220,53,69,0.9);border:none;color:#fff;cursor:pointer;font-size:0.8rem;display:flex;align-items:center;justify-content:center;">✕</button>
          </div>
        </div>
      </div>

      <button id="submitBookingBtn" onclick="submitBooking()" style="width:100%;margin-top:1.25rem;padding:0.85rem;border-radius:12px;background:var(--orange);color:#000;border:none;font-weight:800;font-size:1rem;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:0.5rem;transition:all 0.2s;" onmouseenter="this.style.background='var(--orange-hover)'" onmouseleave="this.style.background='var(--orange)'">
        <i class="fa-solid fa-paper-plane"></i> Submit Booking
      </button>
    </div><!-- /tpmBookingForm -->

  </div>
</div>

<script>
// ── Technician Profile Modal ──────────────────────────────────
let _tpmTechId = null;

function escH(s){ return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }

function showTechProfileModal(techId) {
  _tpmTechId = techId;
  document.getElementById('techProfileModal').style.display = 'flex';
  document.getElementById('tpmLoading').style.display = 'block';
  document.getElementById('tpmContent').style.display = 'none';
  document.getElementById('tpmBookingForm').style.display = 'none';
  document.body.style.overflow = 'hidden';

  fetch('api/session/user'repair_bookings.php?action=technician_profile&id=' + techId)
    .then(r => r.json())
    .then(d => {
      if (!d.success) throw new Error(d.message || 'Failed to load profile.');
      renderTechProfile(d.profile, d.reviews || []);
    })
    .catch(err => {
      document.getElementById('tpmLoading').innerHTML =
        '<div style="color:#dc3545;padding:2rem;text-align:center;"><i class="fa-solid fa-circle-exclamation" style="font-size:2rem;display:block;margin-bottom:0.75rem;"></i>' + escH(err.message) + '</div>';
    });
}

function closeTechProfileModal() {
  document.getElementById('techProfileModal').style.display = 'none';
  document.body.style.overflow = '';
  _tpmTechId = null;
}
document.getElementById('techProfileModal').addEventListener('click', function(e) {
  if (e.target === this) closeTechProfileModal();
});

function renderTechProfile(p, reviews) {
  // Avatar
  const av = document.getElementById('tpmAvatar');
  if (p.avatar_url) {
    av.innerHTML = `<img src="${escH(p.avatar_url)}" style="width:100%;height:100%;object-fit:cover;border-radius:50%;" onerror="this.parentElement.textContent='🔧'">`;
  } else {
    const initials = ((p.first_name||'')[0]||'') + ((p.last_name||'')[0]||'');
    av.textContent = initials.toUpperCase() || '🔧';
  }

  // Name
  document.getElementById('tpmName').textContent = (p.first_name + ' ' + p.last_name).trim();

  // Availability badge
  const availColors = { available:'#28a745', busy:'#e6a800', offline:'#6c757d' };
  const availLabels = { available:'✅ Available', busy:'⏳ Busy', offline:'⭕ Offline' };
  const avail = p.availability || 'available';
  const badge = document.getElementById('tpmAvailBadge');
  badge.textContent = availLabels[avail] || avail;
  badge.style.background = (availColors[avail] || '#aaa') + '22';
  badge.style.color = availColors[avail] || '#aaa';
  badge.style.border = '1px solid ' + (availColors[avail] || '#aaa') + '55';

  // Rating
  const rating = parseFloat(p.rating_avg) || 0;
  const fullStars = Math.floor(rating);
  const halfStar = (rating - fullStars) >= 0.5;
  let stars = '';
  for (let i = 0; i < 5; i++) {
    if (i < fullStars) stars += '<i class="fa-solid fa-star" style="color:var(--orange);font-size:0.85rem;"></i>';
    else if (i === fullStars && halfStar) stars += '<i class="fa-solid fa-star-half-stroke" style="color:var(--orange);font-size:0.85rem;"></i>';
    else stars += '<i class="fa-regular fa-star" style="color:var(--border-color);font-size:0.85rem;"></i>';
  }
  document.getElementById('tpmRating').innerHTML = stars + `<span style="font-size:0.82rem;color:var(--text-secondary);margin-left:0.35rem;">${rating.toFixed(1)} (${p.rating_count} reviews)</span>`;

  // Specializations
  const specsEl = document.getElementById('tpmSpecs');
  if (p.specializations) {
    specsEl.innerHTML = p.specializations.split(',').map(s =>
      `<span style="padding:0.2rem 0.65rem;border-radius:20px;background:rgba(230,168,0,0.12);color:var(--orange);font-size:0.72rem;font-weight:700;border:1px solid rgba(230,168,0,0.25);">${escH(s.trim())}</span>`
    ).join('');
  }

  // Bio
  document.getElementById('tpmBio').textContent = p.bio || '';

  // Description (full shop description)
  const descEl  = document.getElementById('tpmDescription');
  const descSec = document.getElementById('tpmDescSection');
  const desc    = p.description || '';
  if (descEl && descSec) {
    if (desc) {
      descEl.textContent = desc;
      descSec.style.display = 'block';
    } else {
      descSec.style.display = 'none';
    }
  }

  // Stats
  document.getElementById('tpmRepairs').textContent = p.repairs_done || 0;
  // Use experience_years from technician_profiles (may come as experience_years or experience_years_direct)
  const expYrs = parseInt(p.experience_years) || parseInt(p.experience_years_direct) || 0;
  document.getElementById('tpmExp').textContent = expYrs > 0 ? expYrs + 'y' : '—';
  document.getElementById('tpmRatingNum').textContent = rating.toFixed(1);

  // ── Shop Photos Gallery (from credentials table) ──────────
  const creds       = p.credentials || [];
  const shopPhotos  = creds.filter(c => c.doc_type === 'shop_image');
  const workVideos  = creds.filter(c => c.doc_type === 'work_video');
  const docCreds    = creds.filter(c => c.doc_type !== 'shop_image' && c.doc_type !== 'work_video');

  const shopGalleryWrap = document.getElementById('tpmShopGalleryWrap');
  const shopGallery     = document.getElementById('tpmShopGallery');
  const shopImgWrap     = document.getElementById('tpmShopImageWrap');

  if (shopPhotos.length && shopGallery) {
    shopGallery.innerHTML = shopPhotos.map(c => {
      const url = escH(c.file_url_full || c.file_url);
      return `<div style="aspect-ratio:1;border-radius:10px;overflow:hidden;border:1px solid var(--border-color);cursor:pointer;"
                   onclick="openTpmMedia('${url}','image')">
        <img src="${url}" style="width:100%;height:100%;object-fit:cover;"
          onerror="this.parentElement.style.display='none'">
      </div>`;
    }).join('');
    shopGalleryWrap.style.display = 'block';
    // Hide legacy single shop image since gallery takes over
    if (shopImgWrap) shopImgWrap.style.display = 'none';
  } else {
    // Fall back to legacy single shop_image from users table
    if (shopImgWrap) {
      if (p.shop_image) {
        shopImgWrap.innerHTML = `<img src="${escH(p.shop_image)}" alt="Shop"
          style="width:100%;height:180px;object-fit:cover;border-radius:12px;border:1px solid var(--border-color);cursor:pointer;"
          onclick="openTpmMedia('${escH(p.shop_image)}','image')"
          onerror="this.parentElement.style.display='none'">`;
        shopImgWrap.style.display = 'block';
      } else {
        shopImgWrap.style.display = 'none';
      }
    }
    if (shopGalleryWrap) shopGalleryWrap.style.display = 'none';
  }

  // ── Work Videos ───────────────────────────────────────────
  const workVideosWrap = document.getElementById('tpmWorkVideosWrap');
  const workVideosList = document.getElementById('tpmWorkVideos');
  if (workVideos.length && workVideosWrap && workVideosList) {
    workVideosList.innerHTML = workVideos.map(v => {
      const url   = escH(v.file_url_full || v.file_url);
      const label = escH(v.label || '🎬 Work Video');
      return `<div style="background:var(--bg);border:1px solid var(--border-color);border-radius:12px;overflow:hidden;">
        <video src="${url}" controls preload="metadata"
          style="width:100%;max-height:220px;object-fit:cover;display:block;background:#000;"
          onerror="this.outerHTML='<div style=\'padding:1rem;color:#dc3545;font-size:0.82rem;\'>Video unavailable</div>'">
        </video>
        <div style="padding:0.5rem 0.85rem;font-size:0.78rem;font-weight:600;color:var(--text-secondary);">${label}</div>
      </div>`;
    }).join('');
    workVideosWrap.style.display = 'block';
  } else if (workVideosWrap) {
    workVideosWrap.style.display = 'none';
  }

  // Shop & location — build full address from all available fields
  document.getElementById('tpmShopName').textContent = p.shop_name || p.business_name || '—';
  const cityParts = [p.city, p.province].filter(Boolean);
  const cityStr = cityParts.length ? cityParts.join(', ') : (p.general_location || '');
  document.getElementById('tpmCity').textContent = cityStr || '—';

  // Address: prefer shop_address (from application), fallback to address_line + city
  let fullAddr = p.shop_address || '';
  if (!fullAddr) {
    const addrParts = [p.address_line, p.city, p.province, p.zip_code].filter(Boolean);
    fullAddr = addrParts.join(', ');
  }
  document.getElementById('tpmAddress').textContent = fullAddr || '—';

  // Documents — legacy (from seller_applications) + new self-uploaded credentials
  const docsEl = document.getElementById('tpmDocs');

  // Legacy docs from seller_applications
  const docMap = [
    { key: 'doc_gov_id', label: '🪪 Gov ID',      icon: 'fa-id-card' },
    { key: 'doc_bir',    label: '📄 BIR Cert',    icon: 'fa-file-invoice' },
    { key: 'doc_cert',   label: '🏅 Tech Cert',   icon: 'fa-certificate' },
    { key: 'doc_bank',   label: '🏦 Bank Doc',    icon: 'fa-building-columns' },
    { key: 'doc_dti',    label: '📋 DTI Permit',  icon: 'fa-file-contract' },
  ];
  const validLegacyDocs = docMap.filter(d => p[d.key] && p[d.key].trim());

  // New self-uploaded credentials (exclude shop_image and work_video — shown above)
  const credentials = docCreds; // already filtered above

  if (validLegacyDocs.length || credentials.length) {
    const legacyHtml = validLegacyDocs.map(d =>
      `<a href="${escH(p[d.key])}" target="_blank" rel="noopener noreferrer"
          style="display:inline-flex;align-items:center;gap:0.4rem;padding:0.4rem 0.9rem;border-radius:8px;
                 background:rgba(230,168,0,0.08);border:1.5px solid rgba(230,168,0,0.3);
                 color:var(--orange);font-size:0.78rem;font-weight:700;text-decoration:none;transition:all 0.2s;"
          onmouseenter="this.style.background='rgba(230,168,0,0.2)'"
          onmouseleave="this.style.background='rgba(230,168,0,0.08)'">
        <i class="fa-solid ${d.icon}" style="font-size:0.75rem;"></i>
        ${d.label}
        <i class="fa-solid fa-arrow-up-right-from-square" style="font-size:0.6rem;opacity:0.7;"></i>
      </a>`
    ).join('');

    const credHtml = credentials.map(c => {
      const isPdf = c.file_ext === 'pdf';
      const icon  = isPdf ? 'fa-file-pdf' : (c.is_image ? 'fa-image' : 'fa-file');
      const label = escH(c.label || c.doc_type);
      const url   = escH(c.file_url_full || c.file_url);
      return `<a href="${url}" target="_blank" rel="noopener noreferrer"
          style="display:inline-flex;align-items:center;gap:0.4rem;padding:0.4rem 0.9rem;border-radius:8px;
                 background:rgba(139,92,246,0.07);border:1.5px solid rgba(139,92,246,0.25);
                 color:#8b5cf6;font-size:0.78rem;font-weight:700;text-decoration:none;transition:all 0.2s;flex-shrink:0;"
          onmouseenter="this.style.background='rgba(139,92,246,0.18)'"
          onmouseleave="this.style.background='rgba(139,92,246,0.07)'">
        <i class="fa-solid ${icon}" style="font-size:0.75rem;"></i>
        ${label}
        <i class="fa-solid fa-arrow-up-right-from-square" style="font-size:0.6rem;opacity:0.7;"></i>
      </a>`;
    }).join('');

    // Image credential thumbnails (show inline previews for image credentials — not shop photos)
    const imgCreds = credentials.filter(c => c.is_image && c.file_url_full);
    const imgThumbHtml = imgCreds.length
      ? `<div style="display:flex;flex-wrap:wrap;gap:0.5rem;margin-top:0.6rem;">` +
        imgCreds.map(c =>
          `<img src="${escH(c.file_url_full)}" alt="${escH(c.label)}" title="${escH(c.label)}"
            onclick="openTpmMedia('${escH(c.file_url_full)}','image')"
            style="width:72px;height:72px;object-fit:cover;border-radius:8px;border:1px solid var(--border-color);cursor:pointer;"
            onerror="this.style.display='none'">`
        ).join('') + `</div>`
      : '';

    docsEl.innerHTML = `<div style="display:flex;flex-wrap:wrap;gap:0.6rem;">${legacyHtml}${credHtml}</div>${imgThumbHtml}`;
  } else {
    docsEl.innerHTML = '<span style="font-size:0.83rem;color:var(--text-secondary);font-style:italic;">No credentials uploaded yet.</span>';
  }

  // Reviews
  const revEl = document.getElementById('tpmReviews');
  const reviewCountEl = document.getElementById('tpmReviewCount');
  if (reviewCountEl) reviewCountEl.textContent = reviews.length ? reviews.length + ' review' + (reviews.length !== 1 ? 's' : '') : '';

  if (reviews.length) {
    revEl.innerHTML = reviews.map(r => {
      const initials = (r.customer_name||'?').split(' ').map(w=>w[0]||'').join('').toUpperCase().slice(0,2);
      const avatarHtml = r.customer_avatar
        ? `<img src="${escH(r.customer_avatar)}" style="width:36px;height:36px;border-radius:50%;object-fit:cover;flex-shrink:0;" onerror="this.outerHTML='<div style=\'width:36px;height:36px;border-radius:50%;background:rgba(230,168,0,0.12);border:2px solid rgba(230,168,0,0.25);display:flex;align-items:center;justify-content:center;font-size:0.8rem;font-weight:800;color:var(--orange);flex-shrink:0;\'>${escH(initials)}</div>'">`
        : `<div style="width:36px;height:36px;border-radius:50%;background:rgba(230,168,0,0.12);border:2px solid rgba(230,168,0,0.25);display:flex;align-items:center;justify-content:center;font-size:0.8rem;font-weight:800;color:var(--orange);flex-shrink:0;">${escH(initials)}</div>`;

      // Star rating
      let starsHtml = '';
      if (r.rating) {
        const rVal = parseInt(r.rating);
        for (let i = 1; i <= 5; i++) {
          starsHtml += `<i class="fa-solid fa-star" style="color:${i<=rVal?'#e6a800':'#444'};font-size:0.7rem;"></i>`;
        }
        starsHtml = `<div style="display:flex;gap:1px;margin-bottom:0.3rem;">${starsHtml}</div>`;
      }

      // Media thumbnails
      let mediaHtml = '';
      const mediaPairs = [
        [r.media_1_url, r.media_1_type],
        [r.media_2_url, r.media_2_type],
        [r.media_3_url, r.media_3_type],
      ].filter(([u]) => u);
      if (mediaPairs.length) {
        const thumbs = mediaPairs.map(([url, type]) => {
          if (type === 'video') {
            return `<div onclick="openTpmMedia('${escH(url)}','video')" style="width:68px;height:68px;border-radius:8px;background:#000;border:1px solid var(--border-color);cursor:pointer;display:flex;align-items:center;justify-content:center;position:relative;overflow:hidden;flex-shrink:0;">
              <video src="${escH(url)}" style="width:100%;height:100%;object-fit:cover;opacity:0.7;"></video>
              <div style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;font-size:1.2rem;">▶</div>
            </div>`;
          }
          return `<img src="${escH(url)}" onclick="openTpmMedia('${escH(url)}','image')"
            style="width:68px;height:68px;object-fit:cover;border-radius:8px;border:1px solid var(--border-color);cursor:pointer;flex-shrink:0;"
            onerror="this.style.display='none'">`;
        }).join('');
        mediaHtml = `<div style="display:flex;gap:0.5rem;flex-wrap:wrap;margin-top:0.6rem;">${thumbs}</div>`;
      }

      const serviceLabel = r.service_type === 'home_service' ? '🏠 Home Service' : '🏪 In-Shop';
      const repairText = r.repair_desc ? escH(r.repair_desc.slice(0, 80)) + (r.repair_desc.length > 80 ? '…' : '') : '';
      const dateStr = r.created_at ? new Date(r.created_at).toLocaleDateString('en-PH',{month:'short',day:'numeric',year:'numeric'}) : '';

      return `<div style="background:var(--bg);border:1px solid var(--border-color);border-radius:12px;padding:0.85rem 1rem;">
        <div style="display:flex;gap:0.7rem;align-items:flex-start;">
          <div style="flex-shrink:0;">${avatarHtml}</div>
          <div style="flex:1;min-width:0;">
            <div style="display:flex;align-items:center;justify-content:space-between;gap:0.5rem;flex-wrap:wrap;">
              <div style="font-size:0.84rem;font-weight:700;color:var(--text-primary);">${escH(r.customer_name||'Customer')}</div>
              <div style="font-size:0.68rem;color:var(--text-secondary);">${dateStr}</div>
            </div>
            ${starsHtml}
            ${repairText ? `<div style="font-size:0.76rem;color:var(--text-secondary);margin-bottom:0.25rem;">🔧 ${repairText} <span style="opacity:0.6;">${serviceLabel}</span></div>` : ''}
            ${r.comment ? `<div style="font-size:0.82rem;color:var(--text-primary);line-height:1.55;">"${escH(r.comment)}"</div>` : ''}
            ${mediaHtml}
          </div>
        </div>
      </div>`;
    }).join('');
  } else {
    revEl.innerHTML = '<div style="font-size:0.83rem;color:var(--text-secondary);font-style:italic;text-align:center;padding:0.5rem 0;">No reviews yet — be the first to review!</div>';
  }

  // Book button visibility
  const user = (window.FGAuth && window.FGAuth.UserStore) ? window.FGAuth.UserStore.get() : JSON.parse(sessionStorage.getItem('fg_user')||'null');
  const isCustomer = user && user.role === 'customer';
  document.getElementById('tpmBookBtn').style.display = isCustomer ? 'flex' : 'none';
  document.getElementById('tpmLoginHint').style.display = isCustomer ? 'none' : 'block';

  document.getElementById('tpmLoading').style.display = 'none';
  document.getElementById('tpmContent').style.display = 'block';
}

function msgTechnician() {
  const user = (window.FGAuth && window.FGAuth.UserStore) ? window.FGAuth.UserStore.get() : JSON.parse(sessionStorage.getItem('fg_user')||'null');
  if (!user || user.role !== 'customer') {
    window.location.href = 'login.html?redirect=messages';
    return;
  }
  const techId = _tpmTechId; // capture before closing modal
  if (!techId) return;
  closeTechProfileModal();
  window.location.href = 'views/user/customer/messages.php?with=' + techId;
}

function selectBfServiceType(type) {
  var homeEl = document.getElementById('bfSvcHome');
  var shopEl = document.getElementById('bfSvcShop');
  document.getElementById('bfServiceType').value = type;
  if (type === 'home_service') {
    homeEl.style.border = '2px solid var(--orange)';
    homeEl.style.background = 'rgba(230,168,0,0.07)';
    shopEl.style.border = '2px solid var(--border-color)';
    shopEl.style.background = 'var(--bg)';
  } else {
    shopEl.style.border = '2px solid var(--orange)';
    shopEl.style.background = 'rgba(230,168,0,0.07)';
    homeEl.style.border = '2px solid var(--border-color)';
    homeEl.style.background = 'var(--bg)';
  }
}

// Called when customer clicks a service type option in the booking form
function bfPickServiceType(type) {
  if (type === 'home_service') {
    // Show home service agreement before allowing switch
    showBfAgreementModal('home_service');
  } else {
    selectBfServiceType('shop_fix');
  }
}

// ── Index Booking Agreement Modal ─────────────────────────────
var _bfPendingServiceType = null;

function showBfAgreementModal(serviceType) {
  _bfPendingServiceType = serviceType;
  var modal = document.getElementById('bfAgreementModal');
  var body  = document.getElementById('bfAgreementBody');
  var agreeBtn = document.getElementById('bfAgreementAgreeBtn');

  agreeBtn.disabled = true;
  agreeBtn.style.opacity = '0.5';
  agreeBtn.style.cursor = 'not-allowed';
  document.getElementById('bfAgreementScrollHint').style.display = 'flex';

  var isHome = serviceType === 'home_service';

  var content = `
    <div style="font-size:0.88rem;line-height:1.75;color:var(--text-primary);">
      <div style="text-align:center;margin-bottom:1.25rem;">
        <div style="font-size:2rem;margin-bottom:0.3rem;">${isHome ? '🏠' : '📋'}</div>
        <div style="font-size:1rem;font-weight:800;">${isHome ? 'Home Service Agreement' : 'Repair Service Agreement'}</div>
        <div style="font-size:0.75rem;color:#888;margin-top:0.2rem;">Please read carefully before proceeding</div>
      </div>

      ${isHome ? `
      <div style="background:rgba(59,130,246,0.1);border:1px solid rgba(59,130,246,0.3);border-radius:10px;padding:0.9rem 1rem;margin-bottom:1.1rem;">
        <div style="font-weight:800;color:#3b82f6;margin-bottom:0.5rem;font-size:0.9rem;">🏠 Home Service — Extra Charges Notice</div>
        <p style="margin:0 0 0.6rem;font-size:0.85rem;">By selecting <strong>Home Service</strong>, you acknowledge and agree to the following:</p>
        <ul style="margin:0;padding-left:1.3rem;font-size:0.84rem;">
          <li style="margin-bottom:0.4rem;">A <strong>home service fee</strong> will be charged <strong>in addition</strong> to the standard repair cost. The exact amount will be communicated by the technician before any work begins.</li>
          <li style="margin-bottom:0.4rem;">The home service fee covers transportation, travel time, and on-site setup costs.</li>
          <li style="margin-bottom:0.4rem;">You agree to provide a <strong>safe, well-lit, and accessible workspace</strong> for the technician at your location.</li>
          <li style="margin-bottom:0.4rem;">The technician reserves the right to <strong>decline or reschedule</strong> if the environment is deemed unsafe or unsuitable for repair.</li>
          <li style="margin-bottom:0.4rem;">Travel time and distance may affect availability. The technician will confirm if your location is within their service area.</li>
          <li>Payment of the home service fee is required regardless of whether the repair is completed, unless the technician is unable to attend.</li>
        </ul>
      </div>` : `
      <div style="background:rgba(139,92,246,0.08);border:1px solid rgba(139,92,246,0.2);border-radius:10px;padding:0.9rem 1rem;margin-bottom:1.1rem;">
        <div style="font-weight:800;color:#8b5cf6;margin-bottom:0.4rem;font-size:0.9rem;">🏪 In-Shop Fix — Reminders</div>
        <ul style="margin:0;padding-left:1.3rem;font-size:0.84rem;">
          <li style="margin-bottom:0.4rem;">Please bring your device to the technician's shop at the agreed schedule.</li>
          <li style="margin-bottom:0.4rem;">Back up your data before dropping off — data loss may occur during repair.</li>
          <li>A cost estimate will be provided before any paid repair work is performed.</li>
        </ul>
      </div>`}

      <div style="font-weight:800;margin-bottom:0.5rem;font-size:0.9rem;">⚖️ Legal Disclaimer — Parts Replacement &amp; Repair Risks</div>
      <p style="font-size:0.84rem;margin-bottom:0.6rem;">Under <strong>Republic Act No. 7394</strong> (Consumer Act of the Philippines) and related consumer protection laws, the following terms govern all repair services booked through Fix&amp;Go:</p>

      <ol style="padding-left:1.3rem;margin-bottom:0.9rem;font-size:0.84rem;">
        <li style="margin-bottom:0.55rem;"><strong>Risk of Parts Replacement:</strong> Replacing components (screens, batteries, charging ports, cameras, motherboard parts, etc.) carries inherent risks. Replacement parts — whether OEM, aftermarket, or refurbished — may affect original device performance, durability, or warranty status. The customer accepts these risks by proceeding with the repair.</li>
        <li style="margin-bottom:0.55rem;"><strong>Manufacturer Warranty Voiding:</strong> Repair services performed outside of the manufacturer's authorized service centers may void any remaining device warranty. Fix&amp;Go technicians are independent service providers and are not affiliated with any device manufacturer.</li>
        <li style="margin-bottom:0.55rem;"><strong>Data Loss:</strong> The customer acknowledges that repair work may result in partial or complete data loss. It is the customer's sole responsibility to back up all data before submitting the device. Fix&amp;Go and its technicians shall not be held liable for any data loss.</li>
        <li style="margin-bottom:0.55rem;"><strong>Liability Limitation:</strong> Technicians exercise reasonable care in all repairs. However, if pre-existing damage, undisclosed conditions, or inherent device defects cause additional damage during repair, the technician's liability shall be limited to the cost of the repair service rendered, as provided under RA 7394.</li>
        <li style="margin-bottom:0.55rem;"><strong>Technician Protection:</strong> The customer agrees not to hold the technician liable for damages arising from pre-existing conditions, device age, or prior unauthorized repairs. Any dispute shall first be resolved amicably before escalation under applicable Philippine law.</li>
        <li style="margin-bottom:0.55rem;"><strong>Potential Harm to Customer:</strong> The customer understands that using non-original replacement parts may affect the safe operation of the device, including risks such as battery swelling, overheating, or reduced structural integrity. The technician will advise on part quality options, but the final decision rests with the customer.</li>
        <li style="margin-bottom:0.55rem;"><strong>Technician Occupational Risk:</strong> Device repair involves handling electronic components, sharp edges, adhesives, and chemical solvents. The technician assumes occupational responsibility for the work performed under applicable Philippine labor and occupational health standards (RA 11058).</li>
        <li style="margin-bottom:0.55rem;"><strong>Consent to Repair:</strong> By proceeding with this booking, the customer expressly consents to the technician performing diagnostic inspection and, upon agreement, repair services under the terms stated herein.</li>
        <li><strong>Privacy (RA 10173):</strong> Personal information provided in this booking form is collected solely for service coordination and will not be shared with third parties without the customer's consent, in accordance with the Data Privacy Act of 2012.</li>
      </ol>

      <div style="background:rgba(220,53,69,0.08);border:1px solid rgba(220,53,69,0.2);border-radius:10px;padding:0.75rem 1rem;">
        <div style="font-weight:800;color:#dc3545;margin-bottom:0.3rem;font-size:0.85rem;">⚠️ Important</div>
        <p style="margin:0;font-size:0.82rem;">By clicking <strong>"I Agree &amp; Proceed"</strong>, you confirm that you have read, understood, and agree to all terms stated in this agreement. This constitutes a legally binding acknowledgment under Philippine law.</p>
      </div>
    </div>`;

  body.innerHTML = content;
  document.getElementById('bfAgreementModalTitle').textContent = isHome ? '🏠 Home Service Agreement' : '📋 Repair Service Agreement';
  modal.style.display = 'flex';
  document.body.style.overflow = 'hidden';

  var scrollEl = document.getElementById('bfAgreementScrollArea');
  scrollEl.scrollTop = 0;
  function onScroll() {
    if (scrollEl.scrollHeight - scrollEl.scrollTop - scrollEl.clientHeight < 50) {
      agreeBtn.disabled = false;
      agreeBtn.style.opacity = '1';
      agreeBtn.style.cursor = 'pointer';
      document.getElementById('bfAgreementScrollHint').style.display = 'none';
      scrollEl.removeEventListener('scroll', onScroll);
    }
  }
  scrollEl.addEventListener('scroll', onScroll);
  setTimeout(function() {
    if (scrollEl.scrollHeight <= scrollEl.clientHeight + 50) {
      agreeBtn.disabled = false;
      agreeBtn.style.opacity = '1';
      agreeBtn.style.cursor = 'pointer';
      document.getElementById('bfAgreementScrollHint').style.display = 'none';
    }
  }, 200);
}

function closeBfAgreementModal() {
  document.getElementById('bfAgreementModal').style.display = 'none';
  document.body.style.overflow = '';
  _bfPendingServiceType = null;
}

function bfAgreementAgreeAndProceed() {
  var type = _bfPendingServiceType || 'shop_fix';
  var fromInit = document.getElementById('tpmBookingForm').style.display === 'none';
  document.getElementById('bfAgreementModal').style.display = 'none';
  document.body.style.overflow = '';
  _bfPendingServiceType = null;
  if (fromInit) {
    // Agreement was shown before opening the form — now open it
    _openBookingFormAfterAgreement();
  } else {
    // Agreement was for home service switch inside the form
    selectBfServiceType(type);
  }
}

function showBookingForm() {
  const user = (window.FGAuth && window.FGAuth.UserStore) ? window.FGAuth.UserStore.get() : JSON.parse(sessionStorage.getItem('fg_user')||'null');
  if (user) {
    document.getElementById('bfName').value = ((user.firstName||'') + ' ' + (user.lastName||'')).trim();
    document.getElementById('bfContact').value = user.phone || '';
  }
  // Show general repair agreement first, then open form
  _bfPendingServiceType = 'shop_fix';
  showBfAgreementModal('shop_fix');
}

function _openBookingFormAfterAgreement() {
  document.getElementById('tpmContent').style.display = 'none';
  document.getElementById('tpmBookingForm').style.display = 'block';
  document.getElementById('bookingAlert').style.display = 'none';
  selectBfServiceType('shop_fix');
}

function previewPhonePhoto(input) {
  const preview = document.getElementById('bfPhonePreview');
  const img     = document.getElementById('bfPhonePreviewImg');
  if (input.files && input.files[0]) {
    const reader = new FileReader();
    reader.onload = e => {
      img.src = e.target.result;
      preview.style.display = 'block';
    };
    reader.readAsDataURL(input.files[0]);
  }
}

function clearPhonePhoto() {
  document.getElementById('bfPhonePhoto').value = '';
  document.getElementById('bfPhonePreview').style.display = 'none';
  document.getElementById('bfPhonePreviewImg').src = '';
}

function hideBookingForm() {
  document.getElementById('tpmBookingForm').style.display = 'none';
  document.getElementById('tpmContent').style.display = 'block';
}

function submitBooking() {
  const name    = document.getElementById('bfName').value.trim();
  const contact = document.getElementById('bfContact').value.trim();
  const address = document.getElementById('bfAddress').value.trim();
  const device  = document.getElementById('bfDevice').value.trim();
  const fault   = document.getElementById('bfFault').value.trim();
  const history = document.getElementById('bfHistory').value.trim();
  const expected= document.getElementById('bfExpected').value.trim();
  const schedule= document.getElementById('bfSchedule').value;
  const photoInput = document.getElementById('bfPhonePhoto');

  if (!name || !contact || !address || !device || !fault) {
    showBookingAlert('danger', 'Please fill in all required fields (Name, Contact, Address, Device, Fault Description).');
    return;
  }

  const btn = document.getElementById('submitBookingBtn');
  btn.disabled = true;
  btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Submitting…';

  // Use FormData to support file upload
  const fd = new FormData();
  fd.append('action', 'book');
  fd.append('technician_id', _tpmTechId);
  fd.append('name', name);
  fd.append('contact_number', contact);
  fd.append('address', address);
  fd.append('device_name', device);
  fd.append('fault_description', fault);
  fd.append('phone_history', history);
  fd.append('expected_fix', expected);
  if (schedule) fd.append('scheduled_at', schedule);
  if (photoInput && photoInput.files[0]) fd.append('phone_photo', photoInput.files[0]);
  fd.append('service_type', document.getElementById('bfServiceType')?.value || 'shop_fix');

  fetch('api/session/user'repair_bookings.php', {
    method: 'POST',
    credentials: 'include',
    body: fd   // No Content-Type header — browser sets multipart boundary automatically
  })
    .then(r => r.json())
    .then(d => {
      if (!d.success) throw new Error(d.message || 'Booking failed.');
      showBookingAlert('success', '✅ Booking submitted! Booking #' + d.booking_id + '. The technician will confirm shortly.');
      ['bfName','bfContact','bfAddress','bfDevice','bfFault','bfHistory','bfExpected','bfSchedule'].forEach(id => {
        document.getElementById(id).value = '';
      });
      if (photoInput) photoInput.value = '';
      const preview = document.getElementById('bfPhonePreview');
      if (preview) preview.style.display = 'none';
      btn.innerHTML = '<i class="fa-solid fa-check"></i> Booking Submitted!';
      setTimeout(() => {
        closeTechProfileModal();
        btn.disabled = false;
        btn.innerHTML = '<i class="fa-solid fa-paper-plane"></i> Submit Booking';
      }, 3000);
    })
    .catch(err => {
      showBookingAlert('danger', err.message);
      btn.disabled = false;
      btn.innerHTML = '<i class="fa-solid fa-paper-plane"></i> Submit Booking';
    });
}

function showBookingAlert(type, msg) {
  const el = document.getElementById('bookingAlert');
  el.style.display = 'block';
  el.style.background = type === 'success' ? 'rgba(40,167,69,0.12)' : 'rgba(220,53,69,0.12)';
  el.style.color = type === 'success' ? '#28a745' : '#dc3545';
  el.style.border = '1px solid ' + (type === 'success' ? 'rgba(40,167,69,0.25)' : 'rgba(220,53,69,0.25)');
  el.innerHTML = msg;
  el.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
}

// ── Review media viewer (fullscreen overlay) ──────────────────
function openTpmMedia(src, type) {
  const overlay = document.createElement('div');
  overlay.style.cssText = 'position:fixed;inset:0;background:rgba(0,0,0,0.94);z-index:99999;display:flex;align-items:center;justify-content:center;cursor:pointer;';
  overlay.title = 'Click to close';
  overlay.onclick = () => document.body.removeChild(overlay);
  if (type === 'image') {
    overlay.innerHTML = '<img src="' + src + '" style="max-width:94vw;max-height:92vh;border-radius:10px;object-fit:contain;">';
  } else {
    overlay.innerHTML = '<video src="' + src + '" controls autoplay style="max-width:94vw;max-height:92vh;border-radius:10px;" onclick="event.stopPropagation()"></video>';
  }
  document.body.appendChild(overlay);
}
</script>

<!-- ── Booking Agreement Modal (Index) ─────────────────────────────── -->
<div id="bfAgreementModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.78);backdrop-filter:blur(6px);z-index:10500;align-items:center;justify-content:center;padding:1rem;">
  <div style="background:#1a1a2e;border:1px solid #333;border-radius:20px;width:100%;max-width:560px;max-height:94vh;overflow:hidden;display:flex;flex-direction:column;box-shadow:0 32px 80px rgba(0,0,0,0.65);" onclick="event.stopPropagation()">

    <!-- Header -->
    <div style="background:linear-gradient(135deg,#dc3545,#b02a37);padding:1.1rem 1.35rem;display:flex;align-items:center;justify-content:space-between;flex-shrink:0;">
      <div style="display:flex;align-items:center;gap:0.6rem;">
        <i class="fa-solid fa-file-contract" style="color:#fff;font-size:1.05rem;"></i>
        <div>
          <div id="bfAgreementModalTitle" style="color:#fff;font-weight:800;font-size:1rem;">Repair Service Agreement</div>
          <div style="color:rgba(255,255,255,0.75);font-size:0.72rem;margin-top:0.1rem;">Read the full agreement before proceeding</div>
        </div>
      </div>
      <button onclick="closeBfAgreementModal()"
        style="background:rgba(255,255,255,0.18);color:#fff;border:1px solid rgba(255,255,255,0.3);border-radius:8px;width:32px;height:32px;display:flex;align-items:center;justify-content:center;font-size:1rem;cursor:pointer;"
        onmouseenter="this.style.background='rgba(255,255,255,0.32)'" onmouseleave="this.style.background='rgba(255,255,255,0.18)'">&#x2715;</button>
    </div>

    <!-- Scroll hint -->
    <div id="bfAgreementScrollHint" style="display:flex;align-items:center;gap:0.5rem;background:rgba(220,53,69,0.12);border-bottom:1px solid rgba(220,53,69,0.2);padding:0.5rem 1.1rem;font-size:0.78rem;color:#dc3545;font-weight:600;flex-shrink:0;">
      <i class="fa-solid fa-arrow-down"></i>
      <span>Please scroll down and read the entire agreement to enable the Agree button</span>
    </div>

    <!-- Scrollable body -->
    <div id="bfAgreementScrollArea" style="overflow-y:auto;flex:1;padding:1.25rem 1.35rem;">
      <div id="bfAgreementBody"></div>
    </div>

    <!-- Footer -->
    <div style="padding:1rem 1.35rem;border-top:1px solid #333;background:#1a1a2e;flex-shrink:0;display:flex;gap:0.65rem;">
      <button onclick="closeBfAgreementModal()"
        style="flex:1;padding:0.7rem;border-radius:10px;border:1.5px solid #555;background:transparent;color:#aaa;font-weight:700;font-size:0.88rem;cursor:pointer;"
        onmouseenter="this.style.borderColor='#888';this.style.color='#ccc'" onmouseleave="this.style.borderColor='#555';this.style.color='#aaa'">
        Cancel
      </button>
      <button id="bfAgreementAgreeBtn" onclick="bfAgreementAgreeAndProceed()"
        style="flex:2;padding:0.7rem;border-radius:10px;border:none;background:#dc3545;color:#fff;font-weight:800;font-size:0.9rem;cursor:not-allowed;opacity:0.5;transition:all 0.2s;"
        onmouseenter="if(!this.disabled)this.style.background='#b02a37'" onmouseleave="if(!this.disabled)this.style.background='#dc3545'">
        <i class="fa-solid fa-check"></i> I Agree &amp; Proceed
      </button>
    </div>

  </div>
</div>
<script>
  document.getElementById('bfAgreementModal').addEventListener('click', function(e) {
    if (e.target === this) closeBfAgreementModal();
  });
</script>

<script src="fixandgo/assets/js/pwa.js" defer></script>
<script>
/* ── Session expired / logout notification on landing page ── */
(function(){
  var params = new URLSearchParams(location.search);
  var reason = params.get('session') || params.get('logout') || params.get('reason');
  if (!reason) return;

  // Clean the URL immediately
  history.replaceState({}, '', location.pathname);

  var msg = '';
  if (reason === 'expired') {
    msg = '⏱️  Your session expired due to inactivity. Please log in to continue.';
  } else if (reason === 'true' || reason === 'timeout') {
    msg = '✅  You have been logged out successfully.';
  }
  if (!msg) return;

  var toast = document.createElement('div');
  toast.setAttribute('role', 'status');
  toast.setAttribute('aria-live', 'polite');
  toast.style.cssText = [
    'position:fixed;top:76px;left:50%;transform:translateX(-50%) translateY(-10px);',
    'z-index:99999;display:flex;align-items:center;gap:0.6rem;',
    'background:linear-gradient(135deg,rgba(26,29,39,0.98),rgba(37,42,61,0.98));',
    'color:#e2e8f0;padding:0.85rem 1.4rem;border-radius:14px;',
    'font-size:0.88rem;font-weight:600;font-family:inherit;',
    'border:1px solid rgba(230,168,0,0.3);',
    'box-shadow:0 12px 40px rgba(0,0,0,0.5);',
    'max-width:min(92vw,440px);text-align:center;',
    'transition:opacity 0.45s ease,transform 0.35s ease;opacity:0;',
    'pointer-events:none;backdrop-filter:blur(8px);',
  ].join('');
  toast.textContent = msg;
  document.body.appendChild(toast);

  // Slide in
  requestAnimationFrame(function(){
    requestAnimationFrame(function(){
      toast.style.opacity = '1';
      toast.style.transform = 'translateX(-50%) translateY(0)';
    });
  });

  // Fade out after 5s
  setTimeout(function(){
    toast.style.opacity  = '0';
    toast.style.transform = 'translateX(-50%) translateY(-6px)';
    setTimeout(function(){ if(toast.parentNode) toast.parentNode.removeChild(toast); }, 500);
  }, 5000);
})();
</script>

<script>
// ── Swipe carousel dot indicators ───────────────────────────
(function() {
  function initSwipeDots(gridSel, dotsSel) {
    var grid = document.querySelector(gridSel);
    var dots = document.querySelectorAll(dotsSel + ' .swipe-dot');
    if (!grid || !dots.length) return;
    var items = grid.children;
    grid.addEventListener('scroll', function() {
      var idx = Math.round(grid.scrollLeft / grid.offsetWidth);
      dots.forEach(function(d, i) {
        d.classList.toggle('active', i === idx);
      });
    }, { passive: true });
  }
  initSwipeDots('.svc-grid',  '#svcDots');
  initSwipeDots('.how-grid',  '#howDots');
})();
</script>

<script>
// ── Force scroll to hash anchor even when logged-in marketplace is shown ──
(function handleHashScroll() {
  function scrollToHash() {
    var hash = window.location.hash;
    if (!hash) return;
    var target = document.querySelector(hash);
    if (target) {
      setTimeout(function() {
        target.scrollIntoView({ behavior: 'smooth', block: 'start' });
      }, 350);
    }
  }
  // Run on load (after marketplace renders)
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', scrollToHash);
  } else {
    setTimeout(scrollToHash, 400);
  }
  // Also handle hash changes
  window.addEventListener('hashchange', scrollToHash);
})();

// ── Universal bottom nav fix — runs after all role blocks ──────
// Fixes Repairs + Alerts links for any logged-in user
(function fixBottomNavForLoggedInUser() {
  var user = null;
  try {
    user = (window.FGAuth && window.FGAuth.UserStore)
      ? window.FGAuth.UserStore.get()
      : JSON.parse(sessionStorage.getItem('fg_user') || 'null');
  } catch(e) {}

  if (!user) return; // guest — keep default anchor links

  var role = user.role;

  // Map each role to their repair and notifications pages
  var repairLinks = {
    'customer':         '#technicians',
    'phone_technician': '#technicians',
    'owner':            '#technicians',
    'supplier':         '#technicians',
    'sales_person':     '#technicians'
  };
  var notifLinks = {
    'customer':         'views/user/customer/notifications.php',
    'phone_technician': 'views/user/phone_technician/dashboard.php',
    'owner':            'dashboard.php',
    'supplier':         'views/user/supplier/dashboard.php',
    'sales_person':     'dashboard.php'
  };
  var dashLinks = {
    'customer':         'views/user/customer/dashboard.php',
    'phone_technician': 'views/user/phone_technician/dashboard.php',
    'owner':            'dashboard.php',
    'supplier':         'views/user/supplier/dashboard.php',
    'sales_person':     'dashboard.php'
  };

  // Fix Repairs link (#technicians → actual repairs page)
  var bnRepairs = document.querySelector('#shopBottomNav a[href="#technicians"]');
  if (bnRepairs && repairLinks[role]) bnRepairs.href = repairLinks[role];

  // Fix Alerts — replace dropdown trigger with a navigation link
  var bnAlertBtn = document.getElementById('shopBnNotifBtn');
  if (bnAlertBtn && notifLinks[role]) {
    var parent = bnAlertBtn.parentNode;
    var newLink = document.createElement('a');
    newLink.href = notifLinks[role];
    newLink.style.cssText = 'display:flex;flex-direction:column;align-items:center;gap:0.15rem;padding:0.3rem 0.75rem;color:var(--text-secondary,#888);text-decoration:none;font-size:0.62rem;font-weight:700;';
    newLink.innerHTML = bnAlertBtn.innerHTML;
    parent.replaceChild(newLink, bnAlertBtn);
  }

  // Fix Me link → user's dashboard
  var bnMe = document.getElementById('shopBnLogin');
  if (bnMe && dashLinks[role]) bnMe.href = dashLinks[role];

  // For technician: also show the Alerts tab (hidden by default)
  if (role === 'phone_technician') {
    var bnNotifItem = document.getElementById('shopBnNotif');
    if (bnNotifItem) bnNotifItem.style.display = 'list-item';
  }
})();
</script>
</body>

</html>


