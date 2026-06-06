<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta name="csrf-token" content="">
  <title>AppointCare — Smart Appointment Management SaaS</title>
  <meta name="description" content="AppointCare is a smart solution for rapidly building appointment management SaaS applications. Effortlessly streamline your schedule — simple, effective, stress-free." />
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    :root {
      --bg:       #080c14;
      --bg2:      #0d1220;
      --bg3:      #111827;
      --card:     #141b2d;
      --border:   rgba(255,255,255,0.07);
      --accent:   #5b8def;
      --accent2:  #38d9c0;
      --text:     #e2e8f0;
      --muted:    #8b98b0;
      --white:    #ffffff;
    }

    html { scroll-behavior: smooth; }

    body {
      background: var(--bg);
      color: var(--text);
      font-family: 'DM Sans', sans-serif;
      font-size: 15px;
      line-height: 1.7;
      antialiased: true;
      overflow-x: hidden;
    }

    h1,h2,h3,h4,h5,h6 { font-family: 'Syne', sans-serif; line-height: 1.2; }

    a { color: inherit; text-decoration: none; }

    /* ── Noise overlay ── */
    body::before {
      content: '';
      position: fixed; inset: 0; z-index: 0;
      background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 200 200' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)' opacity='0.03'/%3E%3C/svg%3E");
      pointer-events: none;
    }

    /* ── Glow blobs ── */
    .blob {
      position: absolute; border-radius: 50%; filter: blur(120px); pointer-events: none; z-index: 0;
    }

    /* ─────────── NAV ─────────── */
    header {
      position: sticky; top: 0; z-index: 100;
      padding: 18px 0;
      background: rgba(8,12,20,0.82);
      backdrop-filter: blur(16px);
      border-bottom: 1px solid var(--border);
    }
    .nav-inner {
      max-width: 1200px; margin: 0 auto; padding: 0 32px;
      display: flex; align-items: center; justify-content: space-between;
    }
    .logo {
      display: flex; align-items: center; gap: 10px;
    }
    .logo-icon {
      width: 38px; height: 38px; border-radius: 10px;
      background: linear-gradient(135deg, var(--accent), var(--accent2));
      display: grid; place-items: center;
    }
    .logo span { font-family: 'Syne', sans-serif; font-weight: 700; font-size: 17px; }
    nav { display: flex; align-items: center; gap: 28px; }
    nav a { font-size: 14px; color: var(--muted); transition: color .2s; }
    nav a:hover { color: var(--white); }
    .btn-nav {
      padding: 8px 20px; border-radius: 8px;
      background: var(--accent); color: var(--white);
      font-size: 13px; font-weight: 500;
      transition: opacity .2s;
    }
    .btn-nav:hover { opacity: .85; }
    .btn-nav-ghost {
      padding: 8px 20px; border-radius: 8px;
      border: 1px solid var(--border); color: var(--muted);
      font-size: 13px; transition: all .2s;
    }
    .btn-nav-ghost:hover { border-color: var(--accent); color: var(--white); }

    /* ─────────── HERO ─────────── */
    .hero {
      position: relative; overflow: hidden;
      padding: 100px 0 80px;
    }
    .hero .blob-1 {
      width: 600px; height: 600px;
      background: rgba(91,141,239,0.12);
      top: -200px; left: -100px;
    }
    .hero .blob-2 {
      width: 500px; height: 500px;
      background: rgba(56,217,192,0.08);
      bottom: -200px; right: -80px;
    }
    .hero-inner {
      max-width: 1200px; margin: 0 auto; padding: 0 32px;
      display: grid; grid-template-columns: 1fr 1fr; gap: 60px; align-items: center;
      position: relative; z-index: 1;
    }
    .hero-badge {
      display: inline-flex; align-items: center; gap: 8px;
      padding: 6px 14px; border-radius: 40px;
      border: 1px solid rgba(91,141,239,0.35);
      background: rgba(91,141,239,0.07);
      font-size: 12px; color: var(--accent);
      margin-bottom: 20px;
      font-family: 'Syne', sans-serif; font-weight: 600; letter-spacing: .5px;
    }
    .hero-badge span { width: 6px; height: 6px; border-radius: 50%; background: var(--accent2); display:block; }
    h1.hero-title {
      font-size: clamp(36px, 4.5vw, 58px);
      font-weight: 800;
      color: var(--white);
      line-height: 1.1;
      margin-bottom: 20px;
    }
    h1.hero-title em {
      font-style: normal;
      background: linear-gradient(90deg, var(--accent), var(--accent2));
      -webkit-background-clip: text; -webkit-text-fill-color: transparent;
    }
    .hero-sub {
      color: var(--muted); font-size: 16px; margin-bottom: 36px;
      max-width: 460px;
    }
    .hero-ctas { display: flex; gap: 14px; flex-wrap: wrap; }
    .btn-primary {
      padding: 13px 28px; border-radius: 10px;
      background: linear-gradient(135deg, var(--accent), var(--accent2));
      color: var(--white); font-weight: 600; font-size: 14px;
      transition: transform .2s, box-shadow .2s;
      box-shadow: 0 8px 32px rgba(91,141,239,0.3);
    }
    .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 12px 40px rgba(91,141,239,0.4); }
    .btn-secondary {
      padding: 13px 28px; border-radius: 10px;
      border: 1px solid var(--border); color: var(--muted); font-size: 14px;
      transition: all .2s;
    }
    .btn-secondary:hover { border-color: var(--accent); color: var(--white); }

    /* trusted bar */
    .trusted-row {
      margin-top: 48px;
      display: flex; align-items: center; gap: 16px;
      color: var(--muted); font-size: 13px;
    }
    .trusted-avatars { display: flex; }
    .avatar-circle {
      width: 30px; height: 30px; border-radius: 50%;
      border: 2px solid var(--bg);
      margin-left: -8px;
      display: grid; place-items: center;
      font-size: 11px; font-weight: 600; color: var(--white);
    }
    .avatar-circle:first-child { margin-left: 0; }

    /* Hero dashboard card */
    .hero-card {
      background: var(--card);
      border: 1px solid var(--border);
      border-radius: 20px;
      padding: 28px;
      box-shadow: 0 40px 100px rgba(0,0,0,0.5);
    }
    .appt-item {
      display: flex; align-items: center; gap: 14px;
      padding: 14px; border-radius: 12px;
      background: var(--bg2);
      margin-bottom: 12px;
    }
    .appt-avatar {
      width: 40px; height: 40px; border-radius: 12px;
      display: grid; place-items: center;
      font-size: 18px; flex-shrink: 0;
    }
    .appt-info { flex: 1; }
    .appt-name { font-family: 'Syne',sans-serif; font-weight: 600; font-size: 14px; color: var(--white); }
    .appt-meta { font-size: 12px; color: var(--muted); }
    .appt-badge {
      font-size: 11px; padding: 4px 10px; border-radius: 20px;
      font-weight: 600;
    }
    .badge-green { background: rgba(52,211,153,0.15); color: #34d399; }
    .badge-yellow { background: rgba(251,191,36,0.15); color: #fbbf24; }
    .badge-blue { background: rgba(91,141,239,0.15); color: var(--accent); }
    .stats-row {
      display: grid; grid-template-columns: repeat(3,1fr); gap: 10px; margin-top: 16px;
    }
    .stat-box {
      background: var(--bg2); border-radius: 12px; padding: 14px;
      text-align: center;
    }
    .stat-num { font-family: 'Syne',sans-serif; font-weight: 800; font-size: 22px; color: var(--white); }
    .stat-lbl { font-size: 11px; color: var(--muted); margin-top: 2px; }

    /* ─────────── SECTION COMMONS ─────────── */
    section { position: relative; }
    .section-inner { max-width: 1200px; margin: 0 auto; padding: 0 32px; }
    .section-head { text-align: center; margin-bottom: 56px; }
    .overline {
      font-size: 12px; font-weight: 600; letter-spacing: 2px;
      text-transform: uppercase; color: var(--accent);
      margin-bottom: 10px; font-family: 'Syne',sans-serif;
    }
    .section-title { font-size: clamp(28px,3vw,42px); font-weight: 800; color: var(--white); margin-bottom: 12px; }
    .section-sub { color: var(--muted); max-width: 520px; margin: 0 auto; font-size: 15px; }

    /* ─────────── STATS BAND ─────────── */
    .stats-band {
      padding: 60px 0;
      border-top: 1px solid var(--border);
      border-bottom: 1px solid var(--border);
      background: var(--bg2);
    }
    .stats-grid { display: grid; grid-template-columns: repeat(4,1fr); gap: 20px; }
    .stat-card {
      text-align: center; padding: 28px 20px;
    }
    .stat-card .num { font-family: 'Syne',sans-serif; font-size: 40px; font-weight: 800; color: var(--white); }
    .stat-card .num span { background: linear-gradient(90deg,var(--accent),var(--accent2)); -webkit-background-clip:text; -webkit-text-fill-color:transparent; }
    .stat-card .label { color: var(--muted); font-size: 13px; margin-top: 4px; }

    /* ─────────── FEATURES ─────────── */
    .features { padding: 100px 0; }
    .features-grid { display: grid; grid-template-columns: repeat(4,1fr); gap: 18px; }
    .feat-card {
      background: var(--card); border: 1px solid var(--border);
      border-radius: 16px; padding: 28px 24px;
      transition: transform .25s, border-color .25s, box-shadow .25s;
    }
    .feat-card:hover {
      transform: translateY(-4px);
      border-color: rgba(91,141,239,0.3);
      box-shadow: 0 20px 60px rgba(0,0,0,0.4);
    }
    .feat-icon {
      width: 48px; height: 48px; border-radius: 14px;
      background: linear-gradient(135deg, rgba(91,141,239,0.15), rgba(56,217,192,0.08));
      display: grid; place-items: center; margin-bottom: 18px;
      font-size: 22px;
    }
    .feat-title { font-family:'Syne',sans-serif; font-size: 15px; font-weight: 700; color:var(--white); margin-bottom: 8px; }
    .feat-desc { font-size: 13px; color: var(--muted); line-height: 1.6; }

    /* ─────────── HOW IT WORKS ─────────── */
    .how { padding: 100px 0; background: var(--bg2); }
    .steps-grid { display: grid; grid-template-columns: repeat(3,1fr); gap: 28px; }
    .step-card {
      background: var(--card); border: 1px solid var(--border);
      border-radius: 16px; padding: 36px 28px; text-align: center;
      position: relative;
    }
    .step-num {
      width: 52px; height: 52px; border-radius: 50%;
      background: linear-gradient(135deg,var(--accent),var(--accent2));
      display: grid; place-items: center;
      font-family: 'Syne',sans-serif; font-size: 20px; font-weight: 800;
      color: var(--white); margin: 0 auto 20px;
    }
    .step-title { font-family:'Syne',sans-serif; font-size: 17px; font-weight: 700; color:var(--white); margin-bottom: 10px; }
    .step-desc { font-size: 13px; color: var(--muted); }

    /* ─────────── INDUSTRIES ─────────── */
    .industries { padding: 100px 0; }
    .ind-grid { display: grid; grid-template-columns: repeat(4,1fr); gap: 16px; }
    .ind-card {
      background: var(--card); border: 1px solid var(--border);
      border-radius: 14px; padding: 28px 20px; text-align: center;
      transition: transform .2s, border-color .2s;
    }
    .ind-card:hover { transform: translateY(-3px); border-color: rgba(91,141,239,0.3); }
    .ind-icon { font-size: 36px; margin-bottom: 12px; }
    .ind-name { font-family:'Syne',sans-serif; font-size: 13px; font-weight: 700; color: var(--white); }

    /* ─────────── PRICING ─────────── */
    .pricing { padding: 100px 0; background: var(--bg2); }
    .price-toggle {
      display: flex; justify-content: center; gap: 0; margin-bottom: 48px;
    }
    .toggle-btn {
      padding: 9px 28px; font-size: 13px; font-weight: 600;
      border: 1px solid var(--border); cursor: pointer;
      transition: all .2s; font-family: 'Syne',sans-serif;
      background: transparent; color: var(--muted);
    }
    .toggle-btn:first-child { border-radius: 8px 0 0 8px; }
    .toggle-btn:last-child { border-radius: 0 8px 8px 0; }
    .toggle-btn.active {
      background: linear-gradient(135deg,var(--accent),var(--accent2));
      color: var(--white); border-color: transparent;
    }
    .pricing-grid { display: grid; grid-template-columns: repeat(4,1fr); gap: 18px; }
    .price-card {
      background: var(--card); border: 1px solid var(--border);
      border-radius: 18px; padding: 32px 26px;
      transition: transform .25s;
    }
    .price-card:hover { transform: translateY(-4px); }
    .price-card.popular {
      background: linear-gradient(160deg, rgba(91,141,239,0.18), rgba(56,217,192,0.08));
      border-color: rgba(91,141,239,0.4);
    }
    .pop-badge {
      display: inline-block; padding: 4px 12px;
      background: linear-gradient(90deg,var(--accent),var(--accent2));
      border-radius: 20px; font-size: 11px; font-weight: 700;
      color: var(--white); margin-bottom: 14px;
    }
    .plan-name { font-family:'Syne',sans-serif; font-size: 13px; font-weight: 700; color: var(--muted); letter-spacing: 1.5px; text-transform: uppercase; margin-bottom: 8px; }
    .plan-price {
      font-family: 'Syne',sans-serif; font-size: 38px; font-weight: 800;
      color: var(--white); margin-bottom: 4px;
    }
    .plan-price sup { font-size: 18px; vertical-align: super; }
    .plan-period { font-size: 13px; color: var(--muted); margin-bottom: 24px; }
    .plan-features { list-style: none; margin-bottom: 28px; }
    .plan-features li {
      font-size: 13px; color: var(--muted); padding: 7px 0;
      border-bottom: 1px solid var(--border);
      display: flex; align-items: center; gap: 8px;
    }
    .plan-features li::before { content: '✓'; color: var(--accent2); font-weight: 700; }
    .btn-plan {
      display: block; width: 100%; padding: 12px;
      border-radius: 10px; text-align: center;
      font-size: 13px; font-weight: 700; font-family: 'Syne',sans-serif;
      transition: all .2s; border: 1px solid var(--border); color: var(--muted);
    }
    .btn-plan:hover { border-color: var(--accent); color: var(--white); }
    .btn-plan-primary {
      background: linear-gradient(135deg,var(--accent),var(--accent2));
      border-color: transparent; color: var(--white);
    }
    .btn-plan-primary:hover { opacity: .88; }

    /* ─────────── TESTIMONIALS ─────────── */
    .testimonials { padding: 100px 0; }
    .test-grid { display: grid; grid-template-columns: repeat(2,1fr); gap: 20px; }
    .test-card {
      background: var(--card); border: 1px solid var(--border);
      border-radius: 16px; padding: 28px;
    }
    .stars { color: #fbbf24; font-size: 14px; margin-bottom: 14px; }
    .test-text { font-size: 14px; color: var(--muted); line-height: 1.7; margin-bottom: 18px; }
    .test-author { display: flex; align-items: center; gap: 12px; }
    .test-av {
      width: 40px; height: 40px; border-radius: 50%;
      background: linear-gradient(135deg,var(--accent),var(--accent2));
      display: grid; place-items: center; font-weight: 800; color: var(--white); font-size: 16px;
    }
    .test-name { font-family:'Syne',sans-serif; font-size: 14px; font-weight: 700; color: var(--white); }
    .test-role { font-size: 12px; color: var(--muted); }

    /* ─────────── FAQ ─────────── */
    .faq { padding: 100px 0; background: var(--bg2); }
    .faq-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; max-width: 900px; margin: 0 auto; }
    .faq-item {
      background: var(--card); border: 1px solid var(--border);
      border-radius: 14px; padding: 24px 22px;
    }
    .faq-q { font-family:'Syne',sans-serif; font-size: 15px; font-weight: 700; color: var(--white); margin-bottom: 10px; }
    .faq-a { font-size: 13px; color: var(--muted); line-height: 1.65; }

    /* ─────────── BLOG ─────────── */
    .blog { padding: 100px 0; }
    .blog-grid { display: grid; grid-template-columns: repeat(3,1fr); gap: 22px; }
    .blog-card {
      background: var(--card); border: 1px solid var(--border);
      border-radius: 16px; overflow: hidden;
      transition: transform .25s, box-shadow .25s;
    }
    .blog-card:hover { transform: translateY(-4px); box-shadow: 0 20px 60px rgba(0,0,0,0.4); }
    .blog-img {
      height: 180px;
      background: linear-gradient(135deg, rgba(91,141,239,0.2), rgba(56,217,192,0.1));
      display: grid; place-items: center; font-size: 40px;
    }
    .blog-body { padding: 22px 20px; }
    .blog-date { font-size: 11px; color: var(--accent); font-weight: 600; margin-bottom: 8px; }
    .blog-title { font-family:'Syne',sans-serif; font-size: 15px; font-weight: 700; color: var(--white); margin-bottom: 8px; line-height: 1.4; }
    .blog-excerpt { font-size: 12px; color: var(--muted); line-height: 1.6; margin-bottom: 14px; }
    .blog-link { font-size: 12px; color: var(--accent); font-weight: 600; }

    /* ─────────── CTA BAND ─────────── */
    .cta-band {
      padding: 80px 0;
      background: linear-gradient(135deg, rgba(91,141,239,0.12), rgba(56,217,192,0.06));
      border-top: 1px solid var(--border); border-bottom: 1px solid var(--border);
    }
    .cta-inner { max-width: 600px; margin: 0 auto; text-align: center; padding: 0 32px; }
    .cta-title { font-size: clamp(26px,3vw,40px); font-weight: 800; color: var(--white); margin-bottom: 14px; }
    .cta-sub { color: var(--muted); margin-bottom: 32px; }

    /* ─────────── AI WIDGET ─────────── */
    #ai-widget {
      position: fixed; bottom: 24px; right: 24px; width: 340px; z-index: 200;
    }
    .widget-card {
      background: var(--card); border: 1px solid var(--border);
      border-radius: 16px; overflow: hidden;
      box-shadow: 0 20px 60px rgba(0,0,0,0.6);
    }
    .widget-head {
      padding: 14px 18px;
      background: linear-gradient(90deg,var(--accent),var(--accent2));
      font-family:'Syne',sans-serif; font-size: 13px; font-weight: 700;
      color: var(--white); display: flex; align-items: center; gap: 8px;
    }
    .widget-body { padding: 14px; }
    #aiPrompt {
      width: 100%; padding: 10px 12px; border-radius: 10px;
      background: var(--bg2); border: 1px solid var(--border);
      color: var(--text); font-size: 13px; resize: none;
      font-family: 'DM Sans', sans-serif;
    }
    #aiPrompt:focus { outline: none; border-color: var(--accent); }
    .widget-btns { margin-top: 10px; display: flex; gap: 8px; }
    #aiAsk {
      flex: 1; padding: 9px; border-radius: 8px;
      background: linear-gradient(135deg,var(--accent),var(--accent2));
      color: var(--white); font-size: 12px; font-weight: 700;
      border: none; cursor: pointer; font-family: 'Syne',sans-serif;
    }
    #aiClear {
      padding: 9px 14px; border-radius: 8px;
      border: 1px solid var(--border); background: transparent;
      color: var(--muted); font-size: 12px; cursor: pointer;
    }
    #aiAnswer {
      margin-top: 10px; font-size: 12px; color: var(--text);
      white-space: pre-wrap; line-height: 1.6;
    }

    /* ─────────── FOOTER ─────────── */
    footer {
      background: var(--bg2); padding: 60px 0 28px;
      border-top: 1px solid var(--border);
    }
    .footer-grid {
      display: grid; grid-template-columns: 2fr 1fr 1fr 1.5fr; gap: 40px; margin-bottom: 48px;
    }
    .footer-brand p { font-size: 13px; color: var(--muted); margin-top: 12px; max-width: 240px; }
    .footer-col h4 { font-family:'Syne',sans-serif; font-size: 13px; font-weight: 700; color: var(--white); margin-bottom: 16px; letter-spacing: 1px; text-transform: uppercase; }
    .footer-col ul { list-style: none; }
    .footer-col ul li { margin-bottom: 10px; }
    .footer-col ul li a { font-size: 13px; color: var(--muted); transition: color .2s; }
    .footer-col ul li a:hover { color: var(--white); }
    .contact-item { display: flex; gap: 8px; align-items: flex-start; font-size: 13px; color: var(--muted); margin-bottom: 8px; }
    .footer-bottom {
      border-top: 1px solid var(--border); padding-top: 24px;
      display: flex; align-items: center; justify-content: space-between;
      font-size: 12px; color: var(--muted);
    }
    .social-links { display: flex; gap: 10px; }
    .social-link {
      width: 32px; height: 32px; border-radius: 8px;
      border: 1px solid var(--border); display: grid; place-items: center;
      font-size: 14px; transition: border-color .2s;
    }
    .social-link:hover { border-color: var(--accent); }

    /* ─────────── RESPONSIVE ─────────── */
    @media (max-width: 900px) {
      .hero-inner { grid-template-columns: 1fr; }
      .features-grid, .ind-grid { grid-template-columns: repeat(2,1fr); }
      .pricing-grid { grid-template-columns: repeat(2,1fr); }
      .test-grid { grid-template-columns: 1fr; }
      .blog-grid { grid-template-columns: 1fr 1fr; }
      .footer-grid { grid-template-columns: 1fr 1fr; }
      .stats-grid { grid-template-columns: repeat(2,1fr); }
      .faq-grid { grid-template-columns: 1fr; }
    }
    @media (max-width: 600px) {
      .features-grid, .pricing-grid, .blog-grid, .ind-grid, .steps-grid { grid-template-columns: 1fr; }
      nav .btn-nav-ghost { display: none; }
    }
  </style>
</head>
<body>

  <!-- HEADER -->
  <header>
    <div class="nav-inner">
      <a href="/" class="logo">
        <div class="logo-icon">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
          </svg>
        </div>
        <span>AppointCare</span>
      </a>
      <nav>
        <a href="#features">Features</a>
        <a href="#how">How it works</a>
        <a href="#pricing">Pricing</a>
        <a href="#blog">Blog</a>
        <a href="#contact">Contact</a>
        <a href="/login" class="btn-nav-ghost">Sign in</a>
        <a href="/register" class="btn-nav">Get Started</a>
      </nav>
    </div>
  </header>

  <!-- HERO -->
  <section class="hero">
    <div class="blob blob-1"></div>
    <div class="blob blob-2"></div>
    <div class="hero-inner">
      <div>
        <div class="hero-badge"><span></span> Trusted by 95,000+ businesses worldwide</div>
        <h1 class="hero-title">Simplified Appointment<br>Management with <em>AppointCare</em></h1>
        <p class="hero-sub">Effortlessly streamline your schedule with AppointCare — making appointment management simple, effective, and stress-free.</p>
        <div class="hero-ctas">
          <a href="/register" class="btn-primary">Get Started — Free Trial</a>
          <a href="#how" class="btn-secondary">How it works</a>
        </div>
        <div class="trusted-row">
          <div class="trusted-avatars">
            <div class="avatar-circle" style="background:#5b8def">J</div>
            <div class="avatar-circle" style="background:#38d9c0">A</div>
            <div class="avatar-circle" style="background:#f59e0b">M</div>
            <div class="avatar-circle" style="background:#ec4899">S</div>
            <div class="avatar-circle" style="background:#8b5cf6">R</div>
          </div>
          <span>Join thousands of satisfied users</span>
        </div>
      </div>
      <div>
        <div class="hero-card">
          <div style="font-family:'Syne',sans-serif;font-size:13px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:1px;margin-bottom:16px;">Today's Appointments</div>
          <div class="appt-item">
            <div class="appt-avatar" style="background:rgba(91,141,239,0.15)">👩‍⚕️</div>
            <div class="appt-info">
              <div class="appt-name">Dr. Sarah Mitchell</div>
              <div class="appt-meta">Healthcare • 09:00 AM</div>
            </div>
            <span class="appt-badge badge-green">Confirmed</span>
          </div>
          <div class="appt-item">
            <div class="appt-avatar" style="background:rgba(56,217,192,0.15)">💇</div>
            <div class="appt-info">
              <div class="appt-name">Alexandra — Haircut</div>
              <div class="appt-meta">Hair Salon • 10:00 AM</div>
            </div>
            <span class="appt-badge badge-yellow">Pending</span>
          </div>
          <div class="appt-item" style="margin-bottom:0">
            <div class="appt-avatar" style="background:rgba(245,158,11,0.15)">⚖️</div>
            <div class="appt-info">
              <div class="appt-name">James Attorney</div>
              <div class="appt-meta">Legal • 02:30 PM</div>
            </div>
            <span class="appt-badge badge-blue">Scheduled</span>
          </div>
          <div class="stats-row">
            <div class="stat-box">
              <div class="stat-num">24</div>
              <div class="stat-lbl">Today</div>
            </div>
            <div class="stat-box">
              <div class="stat-num">98%</div>
              <div class="stat-lbl">Show Rate</div>
            </div>
            <div class="stat-box">
              <div class="stat-num">4.9</div>
              <div class="stat-lbl">Rating</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- STATS BAND -->
  <div class="stats-band">
    <div class="section-inner">
      <div class="stats-grid">
        <div class="stat-card">
          <div class="num"><span>98%</span></div>
          <div class="label">Customer Satisfaction</div>
        </div>
        <div class="stat-card">
          <div class="num"><span>15M</span></div>
          <div class="label">Subscription Members</div>
        </div>
        <div class="stat-card">
          <div class="num"><span>40%</span></div>
          <div class="label">Cost Savings</div>
        </div>
        <div class="stat-card">
          <div class="num"><span>69K</span></div>
          <div class="label">Positive Reviews</div>
        </div>
      </div>
    </div>
  </div>

  <!-- FEATURES -->
  <section class="features" id="features">
    <div class="section-inner">
      <div class="section-head">
        <div class="overline">Our Features</div>
        <h2 class="section-title">Best Platform for Your<br>Appointment Service</h2>
        <p class="section-sub">AppointCare handles scheduling and managing appointments for businesses across various industries — efficiently and automatically.</p>
      </div>
      <div class="features-grid">
        <div class="feat-card">
          <div class="feat-icon">🌐</div>
          <div class="feat-title">Accept Online Bookings</div>
          <div class="feat-desc">Enable hassle-free online bookings through our intuitive, mobile-optimized platform. Clients book from anywhere, anytime.</div>
        </div>
        <div class="feat-card">
          <div class="feat-icon">💬</div>
          <div class="feat-title">SMS & Email Notifications</div>
          <div class="feat-desc">Stay informed with instant notifications via SMS and email. Keep clients updated with timely alerts straight to their devices.</div>
        </div>
        <div class="feat-card">
          <div class="feat-icon">📱</div>
          <div class="feat-title">Client & Admin App</div>
          <div class="feat-desc">Access your booking system anytime, anywhere. Empower clients and administrators with a dedicated mobile app experience.</div>
        </div>
        <div class="feat-card">
          <div class="feat-icon">💳</div>
          <div class="feat-title">Accept Payments</div>
          <div class="feat-desc">Securely accept payments for bookings through an integrated payment gateway. Offer flexible payment options to clients.</div>
        </div>
        <div class="feat-card">
          <div class="feat-icon">🔗</div>
          <div class="feat-title">Integrations & API</div>
          <div class="feat-desc">Connect your appointment system seamlessly with existing software via our robust API. Twilio, OpenAI, webhooks and more.</div>
        </div>
        <div class="feat-card">
          <div class="feat-icon">⚙️</div>
          <div class="feat-title">Custom Features</div>
          <div class="feat-desc">Tailor your appointment management system with custom features to match your unique business needs and unlock advanced workflows.</div>
        </div>
        <div class="feat-card">
          <div class="feat-icon">🎨</div>
          <div class="feat-title">Full Customization</div>
          <div class="feat-desc">Personalize your booking website and admin interface with custom branding, colors, and design for a fully on-brand experience.</div>
        </div>
        <div class="feat-card">
          <div class="feat-icon">🎁</div>
          <div class="feat-title">Products & Promotions</div>
          <div class="feat-desc">Boost sales by offering products and promotions alongside bookings. Drive revenue growth with integrated product management.</div>
        </div>
      </div>
    </div>
  </section>

  <!-- HOW IT WORKS -->
  <section class="how" id="how">
    <div class="section-inner">
      <div class="section-head">
        <div class="overline">How It Works</div>
        <h2 class="section-title">Simple. Powerful. Effortless.</h2>
        <p class="section-sub">Customers request appointments → AI confirms → Staff notified and calendar updated automatically.</p>
      </div>
      <div class="steps-grid">
        <div class="step-card">
          <div class="step-num">1</div>
          <div class="step-title">Book</div>
          <div class="step-desc">Clients book via web, phone, or staff interface. Our mobile-optimized system makes scheduling simple from any device.</div>
        </div>
        <div class="step-card">
          <div class="step-num">2</div>
          <div class="step-title">Confirm</div>
          <div class="step-desc">AI-powered reminders and Twilio integrations confirm appointments, detect intents to reschedule or cancel, and notify staff.</div>
        </div>
        <div class="step-card">
          <div class="step-num">3</div>
          <div class="step-title">Attend</div>
          <div class="step-desc">Staff manage schedules with conflict detection. Analytics dashboards measure performance and optimize your business.</div>
        </div>
      </div>
    </div>
  </section>

  <!-- WHO BENEFITS -->
  <section class="industries" id="industries">
    <div class="section-inner">
      <div class="section-head">
        <div class="overline">Who Benefits</div>
        <h2 class="section-title">Built for Service-Based Industries</h2>
        <p class="section-sub">AppointCare is used across various industries to automate and organize the appointment-setting process for both providers and clients.</p>
      </div>
      <div class="ind-grid">
        <div class="ind-card">
          <div class="ind-icon">🏥</div>
          <div class="ind-name">Doctors & Healthcare</div>
        </div>
        <div class="ind-card">
          <div class="ind-icon">💼</div>
          <div class="ind-name">Business Consultants</div>
        </div>
        <div class="ind-card">
          <div class="ind-icon">💻</div>
          <div class="ind-name">Freelancers</div>
        </div>
        <div class="ind-card">
          <div class="ind-icon">⚖️</div>
          <div class="ind-name">Lawyers & Attorneys</div>
        </div>
        <div class="ind-card">
          <div class="ind-icon">🎯</div>
          <div class="ind-name">Consultants</div>
        </div>
        <div class="ind-card">
          <div class="ind-icon">🏋️</div>
          <div class="ind-name">Professional Trainers</div>
        </div>
        <div class="ind-card">
          <div class="ind-icon">📊</div>
          <div class="ind-name">Financial Advisors</div>
        </div>
        <div class="ind-card">
          <div class="ind-icon">📚</div>
          <div class="ind-name">Tutors & Teachers</div>
        </div>
      </div>
    </div>
  </section>

  <!-- PRICING -->
  <section class="pricing" id="pricing">
    <div class="section-inner">
      <div class="section-head">
        <div class="overline">Our Plans</div>
        <h2 class="section-title">Tailored Plans for Every Business</h2>
        <p class="section-sub">Explore plans designed specifically to suit your unique schedule and budget requirements perfectly.</p>
      </div>
      <div class="price-toggle">
        <button class="toggle-btn active" onclick="setPricing('monthly',this)">Monthly</button>
        <button class="toggle-btn" onclick="setPricing('yearly',this)">Yearly</button>
      </div>
      <div class="pricing-grid">
        <div class="price-card">
          <div class="plan-name">Basic</div>
          <div class="plan-price" data-monthly="9.99" data-yearly="99.99"><sup>$</sup>9.99</div>
          <div class="plan-period">USD / month</div>
          <ul class="plan-features">
            <li>5 Staff members</li>
            <li>10 daily appointments</li>
            <li>150 monthly appointments</li>
            <li>Email support</li>
          </ul>
          <a href="/register" class="btn-plan">Subscribe Now</a>
        </div>
        <div class="price-card popular">
          <div class="pop-badge">Most Popular</div>
          <div class="plan-name">Standard</div>
          <div class="plan-price" data-monthly="14.99" data-yearly="139.99"><sup>$</sup>14.99</div>
          <div class="plan-period">USD / month</div>
          <ul class="plan-features">
            <li>10 Staff members</li>
            <li>12 daily appointments</li>
            <li>300 monthly appointments</li>
            <li>Priority support</li>
          </ul>
          <a href="/register" class="btn-plan btn-plan-primary">Subscribe Now</a>
        </div>
        <div class="price-card">
          <div class="plan-name">Premium</div>
          <div class="plan-price" data-monthly="19.99" data-yearly="199.99"><sup>$</sup>19.99</div>
          <div class="plan-period">USD / month</div>
          <ul class="plan-features">
            <li>15 Staff members</li>
            <li>15 daily appointments</li>
            <li>450 monthly appointments</li>
            <li>AI voice & SMS</li>
          </ul>
          <a href="/register" class="btn-plan">Subscribe Now</a>
        </div>
        <div class="price-card">
          <div class="plan-name">Elite</div>
          <div class="plan-price" data-monthly="39.99" data-yearly="399.99"><sup>$</sup>39.99</div>
          <div class="plan-period">USD / month</div>
          <ul class="plan-features">
            <li>50 Staff members</li>
            <li>50 daily appointments</li>
            <li>1,500 monthly appointments</li>
            <li>White-label & SLA</li>
          </ul>
          <a href="/register" class="btn-plan">Subscribe Now</a>
        </div>
      </div>
    </div>
  </section>

  <!-- TESTIMONIALS -->
  <section class="testimonials" id="testimonials">
    <div class="section-inner">
      <div class="section-head">
        <div class="overline">Happy Clients</div>
        <h2 class="section-title">What Our Users Say</h2>
      </div>
      <div class="test-grid">
        <div class="test-card">
          <div class="stars">★★★★★</div>
          <p class="test-text">AppointCare has truly transformed the way I manage appointments for my business. With its intuitive interface and robust features, scheduling has become a breeze. I can easily set up appointments, manage my calendar, and send automated reminders to clients.</p>
          <div class="test-author">
            <div class="test-av">J</div>
            <div>
              <div class="test-name">John Doe</div>
              <div class="test-role">Healthcare Provider</div>
            </div>
          </div>
        </div>
        <div class="test-card">
          <div class="stars">★★★★★</div>
          <p class="test-text">As a busy professional, I rely on AppointCare to keep my schedule organized, and it hasn't disappointed. The platform is incredibly user-friendly — I can quickly book appointments, sync my calendar across devices, and access client information on the go.</p>
          <div class="test-author">
            <div class="test-av">D</div>
            <div>
              <div class="test-name">David Smith</div>
              <div class="test-role">Business Consultant</div>
            </div>
          </div>
        </div>
        <div class="test-card">
          <div class="stars">★★★★★</div>
          <p class="test-text">AppointCare has been a lifesaver for my small business. It's packed with all the features I need to manage appointments efficiently. I love how customizable it is — I can tailor it to my specific needs. The online bookings feature has been a game-changer.</p>
          <div class="test-author">
            <div class="test-av">A</div>
            <div>
              <div class="test-name">Alex Hae</div>
              <div class="test-role">Freelancer</div>
            </div>
          </div>
        </div>
        <div class="test-card">
          <div class="stars">★★★★★</div>
          <p class="test-text">AppointCare has simplified my appointment management process like never before. The seamless integration with my existing tools made the transition effortless. The automation features like reminders and follow-ups have helped me stay perfectly organized.</p>
          <div class="test-author">
            <div class="test-av">S</div>
            <div>
              <div class="test-name">Steve Warn</div>
              <div class="test-role">Attorney</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- FAQ -->
  <section class="faq" id="faq">
    <div class="section-inner">
      <div class="section-head">
        <div class="overline">FAQ</div>
        <h2 class="section-title">Frequently Asked Questions</h2>
      </div>
      <div class="faq-grid">
        <div class="faq-item">
          <div class="faq-q">What is AppointCare?</div>
          <div class="faq-a">AppointCare is an intuitive online platform designed to simplify appointment scheduling and management for businesses of all sizes.</div>
        </div>
        <div class="faq-item">
          <div class="faq-q">How does AppointCare work?</div>
          <div class="faq-a">Users create, manage, and track appointments through a user-friendly interface. Set availability, accept bookings, and send automated reminders to clients.</div>
        </div>
        <div class="faq-item">
          <div class="faq-q">Is AppointCare suitable for my business?</div>
          <div class="faq-a">Yes — AppointCare caters to healthcare, beauty, education, legal, and professional services. It's adaptable to different scheduling needs and fully customizable.</div>
        </div>
        <div class="faq-item">
          <div class="faq-q">How secure is my data?</div>
          <div class="faq-a">Security is a top priority. We employ industry-standard encryption and security protocols to safeguard your data and ensure privacy and confidentiality.</div>
        </div>
        <div class="faq-item">
          <div class="faq-q">How can I get started?</div>
          <div class="faq-a">Getting started is easy! Simply sign up for an account, customize your settings, and start scheduling appointments hassle-free — no coding required.</div>
        </div>
        <div class="faq-item">
          <div class="faq-q">Does it integrate with Twilio & OpenAI?</div>
          <div class="faq-a">Yes — AppointCare integrates with Twilio for voice & SMS, OpenAI for AI-powered reminders and intent detection, plus webhooks for custom workflows.</div>
        </div>
      </div>
    </div>
  </section>

  <!-- BLOG -->
  <section class="blog" id="blog">
    <div class="section-inner">
      <div class="section-head">
        <div class="overline">Our Latest Blog</div>
        <h2 class="section-title">Fresh Perspectives & Insights</h2>
        <p class="section-sub">Discover our latest entries — engaging content and insightful stories about appointment management.</p>
      </div>
      <div class="blog-grid">
        <div class="blog-card">
          <div class="blog-img">📋</div>
          <div class="blog-body">
            <div class="blog-date">28 Oct 2020</div>
            <div class="blog-title">Top 5 Features to Look for in an Appointment Management SaaS Solution</div>
            <div class="blog-excerpt">When choosing the right appointment management SaaS, look beyond basic functionality and consider features that truly add value to your business...</div>
            <a href="#" class="blog-link">Read More →</a>
          </div>
        </div>
        <div class="blog-card">
          <div class="blog-img">⏱️</div>
          <div class="blog-body">
            <div class="blog-date">25 Jan 2024</div>
            <div class="blog-title">The Importance of Efficient Appointment Management for Your Business</div>
            <div class="blog-excerpt">In today's fast-paced business environment, where every minute counts, efficient appointment management is crucial for success and growth...</div>
            <a href="#" class="blog-link">Read More →</a>
          </div>
        </div>
        <div class="blog-card">
          <div class="blog-img">🤖</div>
          <div class="blog-body">
            <div class="blog-date">27 Mar 2024</div>
            <div class="blog-title">The Future of Appointment Management Systems</div>
            <div class="blog-excerpt">As AI and machine learning technologies advance, appointment management is poised to undergo significant transformations in efficiency and intelligence...</div>
            <a href="#" class="blog-link">Read More →</a>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- CTA BAND -->
  <div class="cta-band" id="contact">
    <div class="cta-inner">
      <h2 class="cta-title">Ready to Streamline Your Appointments?</h2>
      <p class="cta-sub">Join 95,000+ businesses worldwide. Start your free trial today — no credit card required.</p>
      <div style="display:flex;gap:14px;justify-content:center;flex-wrap:wrap">
        <a href="/register" class="btn-primary">Start Free Trial</a>
        <a href="mailto:sales@appointcare.com" class="btn-secondary">Contact Sales</a>
      </div>
    </div>
  </div>

  <!-- AI WIDGET -->
  <div id="ai-widget">
    <div class="widget-card">
      <div class="widget-head">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M12 2v3M12 19v3M2 12h3M19 12h3"/></svg>
        Ask our AI assistant
      </div>
      <div class="widget-body">
        <textarea id="aiPrompt" rows="3" placeholder="Ask about bookings, features, or how AppointCare works..."></textarea>
        <div class="widget-btns">
          <button id="aiAsk">Ask</button>
          <button id="aiClear">Clear</button>
        </div>
        <pre id="aiAnswer"></pre>
      </div>
    </div>
  </div>

  <!-- FOOTER -->
  <footer>
    <div class="section-inner">
      <div class="footer-grid">
        <div class="footer-brand">
          <div class="logo">
            <div class="logo-icon">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
              </svg>
            </div>
            <span>AppointCare</span>
          </div>
          <p>A smart solution for rapidly building appointment management SaaS applications. Make appointment scheduling effortless in record time.</p>
        </div>
        <div class="footer-col">
          <h4>Quick Links</h4>
          <ul>
            <li><a href="/">Home</a></li>
            <li><a href="#pricing">Pricing</a></li>
            <li><a href="#blog">Blog</a></li>
            <li><a href="#contact">Contact</a></li>
          </ul>
        </div>
        <div class="footer-col">
          <h4>Policy Links</h4>
          <ul>
            <li><a href="#">Privacy Policy</a></li>
            <li><a href="#">Terms of Service</a></li>
            <li><a href="#">Appointment Policy</a></li>
          </ul>
        </div>
        <div class="footer-col">
          <h4>Contact Info</h4>
          <div class="contact-item">📍 123 Main Street, London</div>
          <div class="contact-item">📞 +44 20 1234 5678</div>
          <div class="contact-item">✉️ info@appointcare.com</div>
        </div>
      </div>
      <div class="footer-bottom">
        <div>© 2026 AppointCare. All Rights Reserved.</div>
        <div class="social-links">
          <a href="#" class="social-link">f</a>
          <a href="#" class="social-link">𝕏</a>
          <a href="#" class="social-link">in</a>
          <a href="#" class="social-link">ig</a>
        </div>
      </div>
    </div>
  </footer>

  <script>
    // Pricing toggle
    function setPricing(mode, btn) {
      document.querySelectorAll('.toggle-btn').forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
      document.querySelectorAll('.plan-price').forEach(el => {
        const val = mode === 'monthly' ? el.dataset.monthly : el.dataset.yearly;
        const period = el.closest('.price-card').querySelector('.plan-period');
        el.innerHTML = `<sup>$</sup>${val}`;
        period.textContent = mode === 'monthly' ? 'USD / month' : 'USD / year';
      });
    }

    // AI widget
    async function postJSON(url, body) {
      const res = await fetch(url, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
        },
        body: JSON.stringify(body)
      });
      return res.json();
    }

    document.getElementById('aiAsk').addEventListener('click', async () => {
      const prompt = document.getElementById('aiPrompt').value.trim();
      if (!prompt) return;
      document.getElementById('aiAnswer').textContent = 'Thinking…';
      try {
        const result = await postJSON('/ai/respond', { prompt });
        document.getElementById('aiAnswer').textContent = result.answer || result.error || 'No response';
      } catch {
        document.getElementById('aiAnswer').textContent = 'Could not reach server.';
      }
    });

    document.getElementById('aiClear').addEventListener('click', () => {
      document.getElementById('aiPrompt').value = '';
      document.getElementById('aiAnswer').textContent = '';
    });
  </script>

</body>
</html>