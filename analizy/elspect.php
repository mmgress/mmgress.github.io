<?php
/**
 * ELSPECT Group — index.php
 * Strona statycznie generowana w PHP, bez CMS.
 *
 * Założenia:
 * - PHP generuje klasę motywu: dzień / noc / pochmurno / jasno.
 * - Pogoda pobierana jest z Open-Meteo bez klucza API i cache'owana lokalnie.
 * - Jeżeli serwer nie może pobrać pogody, strona działa normalnie na podstawie godziny.
 * - Formularz przygotowuje wiadomość e-mail po stronie przeglądarki; w realnym wdrożeniu można podłączyć wysyłkę PHP.
 */

$company = [
    'name' => 'ELSPECT Group Sp. z o.o.',
    'phone' => '+48 697 568 747',
    'phone_href' => '+48697568747',
    'email' => 'biuro@elspect.pl',
    'city' => 'Wrocław',
    'address' => 'ul. Małopanewska 18, 54-212 Wrocław',
];

$timezone = new DateTimeZone('Europe/Warsaw');
$now = new DateTime('now', $timezone);
$hour = (int)$now->format('G');
$isNight = ($hour < 6 || $hour >= 20);

/** Lokalizacja domyślna: Wrocław. */
$lat = 51.1166;
$lon = 17.0120;
$weather = getWeather($lat, $lon);
$weatherCode = isset($weather['weather_code']) ? (int)$weather['weather_code'] : (isset($weather['weathercode']) ? (int)$weather['weathercode'] : null);
$cloudCover = isset($weather['cloud_cover']) ? $weather['cloud_cover'] : (isset($weather['cloudcover']) ? $weather['cloudcover'] : null);
$isCloudy = isCloudy($weatherCode, $cloudCover);
$isClear = !$isCloudy;

$themeClass = $isNight ? 'theme-night' : 'theme-day';
if (!$isNight && $isCloudy) {
    $themeClass .= ' theme-cloudy';
}
if (!$isNight && $isClear) {
    $themeClass .= ' theme-clear';
}

function getWeather($lat, $lon)
{
    $cacheDir = __DIR__ . '/cache';
    $cacheFile = $cacheDir . '/weather.json';
    $cacheTtl = 60 * 30;

    if (!is_dir($cacheDir)) {
        @mkdir($cacheDir, 0755, true);
    }

    if (is_file($cacheFile) && (time() - filemtime($cacheFile) < $cacheTtl)) {
        $cached = json_decode((string)file_get_contents($cacheFile), true);
        if (is_array($cached)) {
            return $cached;
        }
    }

    $url = 'https://api.open-meteo.com/v1/forecast?latitude=' . urlencode((string)$lat) .
        '&longitude=' . urlencode((string)$lon) .
        '&current=weather_code,cloud_cover,is_day&timezone=Europe%2FWarsaw';

    $context = stream_context_create([
        'http' => [
            'timeout' => 2.5,
            'header' => "User-Agent: ELSPECT-Website/1.0\r\n",
        ],
    ]);

    $response = @file_get_contents($url, false, $context);
    if ($response === false) {
        return [];
    }

    $data = json_decode($response, true);
    $current = (is_array($data) && isset($data['current']) && is_array($data['current'])) ? $data['current'] : [];
    if (!empty($current)) {
        @file_put_contents($cacheFile, json_encode($current, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        return $current;
    }

    return [];
}

function isCloudy($weatherCode, $cloudCover)
{
    if ($cloudCover !== null && is_numeric($cloudCover)) {
        return (int)$cloudCover > 55;
    }

    if ($weatherCode === null) {
        return false;
    }

    return in_array($weatherCode, [2, 3, 45, 48, 51, 53, 55, 56, 57, 61, 63, 65, 66, 67, 71, 73, 75, 77, 80, 81, 82, 85, 86, 95, 96, 99], true);
}

function e($value)
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}
?>
<!doctype html>
<html lang="pl" class="<?= e($themeClass) ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ELSPECT Group — moc bierna, kompensacja, monitoring, wykonawstwo techniczne</title>
    <meta name="description" content="ELSPECT Group pomaga firmom rozwiązywać problemy z mocą bierną, kosztami energii, jakością zasilania, kompensacją, monitoringiem, wykonawstwem technicznym i przygotowaniem założeń do przetargów.">
    <meta name="theme-color" content="#eaf6fb">
    <style>
        :root {
            --bg: #f6fbff;
            --bg-soft: #eef7fb;
            --surface: #ffffff;
            --surface-2: #f8fcff;
            --ink: #152236;
            --muted: #5d6d80;
            --soft: #8291a3;
            --line: rgba(32, 63, 91, .13);
            --brand: #1f83a9;
            --brand-2: #4db6ac;
            --brand-dark: #0f5f7d;
            --warm: #fff4dc;
            --ok: #2f9f7b;
            --warn: #c58a22;
            --danger: #c85d5d;
            --shadow: 0 20px 60px rgba(28, 64, 90, .10);
            --radius: 24px;
            --radius-sm: 16px;
            --max: 1180px;
            --font: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif;
        }

        .theme-night {
            --bg: #111827;
            --bg-soft: #182235;
            --surface: #f8fbff;
            --surface-2: #eef5fb;
            --ink: #111827;
            --muted: #4e5d70;
            --soft: #718196;
            --line: rgba(255,255,255,.14);
            --brand: #5bbfe3;
            --brand-2: #7bd6c9;
            --brand-dark: #235d7a;
            --warm: #1c2a3d;
            --shadow: 0 24px 70px rgba(0, 0, 0, .18);
        }

        .theme-cloudy {
            --bg: #eef5f8;
            --bg-soft: #e7f0f4;
            --brand: #327f9b;
            --brand-2: #67a89d;
        }

        .theme-clear {
            --bg: #f7fcff;
            --bg-soft: #ecf9fb;
            --brand: #1887b2;
            --brand-2: #48bfae;
        }

        * { box-sizing: border-box; }
        html { scroll-behavior: smooth; }
        body {
            margin: 0;
            font-family: var(--font);
            color: var(--ink);
            background:
                radial-gradient(circle at 8% 0%, rgba(77, 182, 172, .18), transparent 34rem),
                radial-gradient(circle at 92% 10%, rgba(31, 131, 169, .16), transparent 30rem),
                linear-gradient(180deg, var(--bg) 0%, var(--bg-soft) 48%, #ffffff 100%);
            line-height: 1.58;
        }

        .theme-night body {
            background:
                radial-gradient(circle at 10% 0%, rgba(91,191,227,.16), transparent 32rem),
                radial-gradient(circle at 88% 12%, rgba(123,214,201,.12), transparent 28rem),
                linear-gradient(180deg, #111827 0%, #182235 44%, #f5f8fb 44%, #ffffff 100%);
        }

        a { color: inherit; text-decoration: none; }
        img, svg { max-width: 100%; display: block; }
        button, input, textarea, select { font: inherit; }
        .wrap { width: min(var(--max), calc(100% - 40px)); margin: 0 auto; }

        .topbar {
            font-size: 14px;
            color: rgba(21,34,54,.75);
            border-bottom: 1px solid rgba(32,63,91,.10);
            background: rgba(255,255,255,.62);
            backdrop-filter: blur(14px);
        }
        .theme-night .topbar { color: rgba(248,251,255,.78); background: rgba(17,24,39,.70); }
        .topbar .wrap {
            min-height: 42px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 18px;
            flex-wrap: wrap;
        }
        .topbar__items { display: flex; gap: 18px; align-items: center; flex-wrap: wrap; }
        .weather-note { display: inline-flex; align-items: center; gap: 8px; }
        .sun-dot { width: 8px; height: 8px; border-radius: 999px; background: var(--brand-2); box-shadow: 0 0 20px rgba(77,182,172,.45); }

        .header {
            position: sticky;
            top: 0;
            z-index: 40;
            background: rgba(255,255,255,.74);
            backdrop-filter: blur(18px);
            border-bottom: 1px solid rgba(32,63,91,.08);
            transition: box-shadow .2s ease, background .2s ease;
        }
        .theme-night .header { background: rgba(17,24,39,.78); border-bottom-color: rgba(255,255,255,.10); }
        .header.is-scrolled { box-shadow: 0 12px 38px rgba(20,52,75,.12); }
        .nav { height: 78px; display: flex; align-items: center; justify-content: space-between; gap: 24px; }
        .brand { display: inline-flex; align-items: center; gap: 12px; font-weight: 900; letter-spacing: .035em; }
        .brand__mark {
            width: 44px; height: 44px; border-radius: 15px;
            display: grid; place-items: center;
            background: linear-gradient(135deg, var(--brand), var(--brand-2));
            color: #fff;
            box-shadow: 0 16px 36px rgba(31,131,169,.22);
        }
        .brand__text span { display: block; margin-top: -3px; font-size: 12px; color: var(--muted); font-weight: 700; letter-spacing: .07em; }
        .theme-night .brand__text { color: #f8fbff; }
        .theme-night .brand__text span { color: rgba(248,251,255,.68); }
        .nav__links { display: flex; gap: 22px; align-items: center; color: var(--muted); font-size: 15px; font-weight: 700; }
        .theme-night .nav__links { color: rgba(248,251,255,.72); }
        .nav__links a:hover { color: var(--brand-dark); }
        .theme-night .nav__links a:hover { color: #fff; }
        .nav__actions { display: flex; align-items: center; gap: 10px; }
        .menu-btn { display: none; border: 1px solid rgba(32,63,91,.13); background: #fff; border-radius: 14px; padding: 10px 12px; cursor: pointer; color: #152236; }

        .btn {
            display: inline-flex;
            justify-content: center;
            align-items: center;
            min-height: 48px;
            padding: 13px 18px;
            border-radius: 999px;
            border: 1px solid transparent;
            font-weight: 850;
            cursor: pointer;
            transition: transform .18s ease, box-shadow .18s ease, background .18s ease;
            white-space: nowrap;
        }
        .btn:hover { transform: translateY(-2px); }
        .btn--primary { background: linear-gradient(135deg, var(--brand), var(--brand-2)); color: #fff; box-shadow: 0 18px 42px rgba(31,131,169,.22); }
        .btn--light { background: #fff; color: var(--brand-dark); border-color: rgba(32,63,91,.10); }
        .btn--dark { background: #152236; color: #fff; box-shadow: 0 18px 42px rgba(21,34,54,.20); }
        .btn--soft { background: rgba(31,131,169,.08); color: var(--brand-dark); border-color: rgba(31,131,169,.12); }

        .hero { padding: 78px 0 76px; }
        .hero__grid { display: grid; grid-template-columns: minmax(0, 1.04fr) minmax(380px, .96fr); gap: 48px; align-items: center; }
        .eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 9px;
            padding: 8px 13px;
            border: 1px solid rgba(31,131,169,.14);
            background: rgba(255,255,255,.64);
            border-radius: 999px;
            color: var(--brand-dark);
            font-weight: 850;
            font-size: 14px;
        }
        .theme-night .eyebrow { background: rgba(255,255,255,.09); color: #dff8ff; border-color: rgba(255,255,255,.13); }
        h1, h2, h3 { margin: 0; line-height: 1.08; letter-spacing: -.042em; }
        h1 { margin-top: 22px; font-size: clamp(40px, 5.9vw, 72px); color: #102035; }
        .theme-night h1 { color: #f8fbff; }
        .hero__lead { max-width: 760px; margin: 24px 0 0; font-size: clamp(18px, 2vw, 22px); color: var(--muted); }
        .theme-night .hero__lead { color: rgba(248,251,255,.75); }
        .hero__lead strong { color: var(--brand-dark); }
        .theme-night .hero__lead strong { color: #dff8ff; }
        .hero__actions { display: flex; align-items: center; gap: 14px; flex-wrap: wrap; margin-top: 32px; }
        .hero__quick { margin-top: 30px; display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; }
        .quick-card {
            padding: 16px;
            border-radius: 18px;
            background: rgba(255,255,255,.72);
            border: 1px solid rgba(32,63,91,.10);
            box-shadow: 0 12px 35px rgba(28,64,90,.06);
        }
        .quick-card strong { display: block; color: #102035; line-height: 1.25; }
        .quick-card span { display: block; margin-top: 6px; color: var(--muted); font-size: 14px; line-height: 1.35; }
        .theme-night .quick-card { background: rgba(255,255,255,.92); }

        .tech-panel {
            border-radius: 32px;
            padding: 22px;
            background: rgba(255,255,255,.78);
            border: 1px solid rgba(32,63,91,.10);
            box-shadow: var(--shadow);
        }
        .theme-night .tech-panel { background: rgba(255,255,255,.94); }
        .panel-head { display: flex; justify-content: space-between; gap: 12px; align-items: center; margin-bottom: 16px; color: var(--muted); font-size: 14px; font-weight: 750; }
        .panel-light { display: flex; gap: 7px; }
        .panel-light i { width: 10px; height: 10px; border-radius: 999px; background: #d7e5ec; }
        .panel-light i:nth-child(1) { background: #77d2bf; }
        .panel-light i:nth-child(2) { background: #ffd27a; }
        .panel-light i:nth-child(3) { background: #7ac5e6; }
        .diagnostic-list { display: grid; gap: 12px; }
        .diagnostic-item {
            display: grid;
            grid-template-columns: 54px 1fr auto;
            gap: 14px;
            align-items: center;
            padding: 16px;
            border-radius: 20px;
            background: #f6fbff;
            border: 1px solid rgba(32,63,91,.08);
        }
        .dicon { width: 54px; height: 54px; border-radius: 18px; display: grid; place-items: center; background: linear-gradient(135deg, rgba(31,131,169,.15), rgba(77,182,172,.18)); color: var(--brand-dark); font-weight: 950; }
        .diagnostic-item strong { display: block; color: #102035; line-height: 1.2; }
        .diagnostic-item span { display: block; color: var(--muted); font-size: 14px; margin-top: 4px; }
        .pill { padding: 7px 10px; border-radius: 999px; font-size: 12px; font-weight: 900; white-space: nowrap; }
        .pill.ok { background: rgba(47,159,123,.10); color: var(--ok); }
        .pill.warn { background: rgba(197,138,34,.12); color: var(--warn); }
        .pill.info { background: rgba(31,131,169,.11); color: var(--brand-dark); }
        .panel-note { margin-top: 16px; padding: 16px; border-radius: 20px; background: #fff7e6; color: #6d5424; border: 1px solid rgba(197,138,34,.14); }
        .panel-note strong { color: #5d451c; }

        .section { padding: 82px 0; background: #fff; }
        .section--soft { background: linear-gradient(180deg, #ffffff, var(--bg-soft)); }
        .section--blue { background: #eff8fb; }
        .section--dark { background: #132034; color: #f8fbff; }
        .section__head { display: grid; grid-template-columns: minmax(0, .86fr) minmax(320px, .66fr); gap: 34px; align-items: end; margin-bottom: 34px; }
        .kicker { margin: 0 0 12px; color: var(--brand-dark); font-weight: 950; letter-spacing: .08em; text-transform: uppercase; font-size: 13px; }
        .section--dark .kicker { color: #8fe8df; }
        h2 { font-size: clamp(32px, 4.5vw, 56px); color: #102035; }
        .section--dark h2 { color: #fff; }
        .section__text { margin: 0; color: var(--muted); font-size: 18px; }
        .section--dark .section__text { color: rgba(248,251,255,.72); }

        .problem-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 18px; }
        .problem-card {
            padding: 26px;
            border-radius: var(--radius);
            background: #fff;
            border: 1px solid rgba(32,63,91,.09);
            box-shadow: 0 18px 46px rgba(28,64,90,.07);
            min-height: 280px;
            display: flex;
            flex-direction: column;
        }
        .problem-card h3 { margin-top: 18px; font-size: 24px; color: #102035; }
        .problem-card p { color: var(--muted); margin: 13px 0 18px; }
        .problem-card a { margin-top: auto; color: var(--brand-dark); font-weight: 900; }

        .what-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 22px; align-items: start; }
        .what-box { padding: 28px; border-radius: var(--radius); background: #fff; border: 1px solid rgba(32,63,91,.09); box-shadow: var(--shadow); }
        .what-box h3 { font-size: 30px; color: #102035; }
        .what-list { display: grid; gap: 12px; margin-top: 22px; }
        .what-list div { display: grid; grid-template-columns: 38px 1fr; gap: 12px; align-items: start; padding: 14px; background: #f6fbff; border-radius: 16px; border: 1px solid rgba(32,63,91,.08); }
        .num { width: 38px; height: 38px; border-radius: 13px; display: grid; place-items: center; background: linear-gradient(135deg, var(--brand), var(--brand-2)); color: #fff; font-weight: 950; }
        .what-list strong { color: #102035; }
        .what-list span { display: block; margin-top: 3px; color: var(--muted); }

        .integrator {
            display: grid;
            grid-template-columns: minmax(0, .95fr) minmax(360px, 1.05fr);
            gap: 30px;
            align-items: center;
        }
        .integrator-card { border-radius: 30px; padding: 28px; background: rgba(255,255,255,.08); border: 1px solid rgba(255,255,255,.12); }
        .integrator-steps { display: grid; gap: 14px; }
        .integrator-step { padding: 17px; border-radius: 18px; background: rgba(255,255,255,.08); border: 1px solid rgba(255,255,255,.10); }
        .integrator-step strong { display: block; color: #fff; }
        .integrator-step span { display: block; margin-top: 4px; color: rgba(248,251,255,.72); }

        .services-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 14px; }
        .service { padding: 22px; border-radius: 22px; background: #fff; border: 1px solid rgba(32,63,91,.09); box-shadow: 0 16px 40px rgba(28,64,90,.055); }
        .service strong { display: block; margin-top: 13px; color: #102035; font-size: 20px; line-height: 1.2; }
        .service span { display: block; margin-top: 9px; color: var(--muted); font-size: 15px; }

        .tender-box {
            border-radius: 30px;
            padding: 32px;
            background: linear-gradient(135deg, #f6fbff, #ffffff);
            border: 1px solid rgba(32,63,91,.09);
            box-shadow: var(--shadow);
            display: grid;
            grid-template-columns: .85fr 1.15fr;
            gap: 28px;
            align-items: center;
        }
        .tender-box h3 { font-size: 36px; color: #102035; }
        .tender-box p { color: var(--muted); font-size: 18px; }
        .checklist { display: grid; gap: 10px; }
        .checklist div { padding: 13px 14px; border-radius: 15px; background: #fff; border: 1px solid rgba(32,63,91,.08); color: #405166; }
        .checklist b { color: var(--brand-dark); }

        .contact-grid { display: grid; grid-template-columns: minmax(0, .82fr) minmax(380px, 1fr); gap: 24px; align-items: start; }
        .contact-card, .form-card { background: #fff; border-radius: var(--radius); border: 1px solid rgba(32,63,91,.09); box-shadow: var(--shadow); padding: 28px; }
        .contact-card h2 { font-size: clamp(32px, 4vw, 48px); }
        .contact-list { display: grid; gap: 12px; margin-top: 24px; }
        .contact-list a, .contact-list div { display: flex; align-items: center; gap: 12px; padding: 14px; background: #f6fbff; border-radius: 16px; border: 1px solid rgba(32,63,91,.08); color: #405166; font-weight: 750; }
        .field { display: grid; gap: 7px; margin-top: 13px; }
        .field label { color: #26384f; font-size: 14px; font-weight: 900; }
        .field input, .field textarea, .field select {
            width: 100%;
            border: 1px solid rgba(32,63,91,.15);
            background: #f8fcff;
            color: #102035;
            border-radius: 16px;
            padding: 14px;
            outline: none;
        }
        .field textarea { min-height: 132px; resize: vertical; }
        .field input:focus, .field textarea:focus, .field select:focus { border-color: var(--brand); box-shadow: 0 0 0 4px rgba(31,131,169,.12); background: #fff; }
        .form-note { color: var(--muted); font-size: 13px; margin: 12px 0 0; }
        .form-status { display: none; margin-top: 14px; padding: 12px 14px; border-radius: 14px; background: #edfff9; color: #1f7158; font-weight: 850; }
        .form-card h3 { font-size: 28px; color: #102035; }

        .footer { padding: 34px 0; background: #132034; color: rgba(248,251,255,.72); }
        .footer .wrap { display: flex; justify-content: space-between; align-items: center; gap: 18px; flex-wrap: wrap; }
        .footer .brand__text { color: #fff; }
        .footer .brand__text span { color: rgba(248,251,255,.66); }

        .reveal { opacity: 0; transform: translateY(18px); transition: opacity .7s ease, transform .7s ease; }
        .reveal.in-view { opacity: 1; transform: translateY(0); }

        @media (max-width: 1080px) {
            .hero__grid, .section__head, .what-grid, .integrator, .tender-box, .contact-grid { grid-template-columns: 1fr; }
            .problem-grid { grid-template-columns: repeat(2, 1fr); }
            .services-grid { grid-template-columns: repeat(2, 1fr); }
            .tech-panel { max-width: 760px; }
        }
        @media (max-width: 820px) {
            .nav__links {
                display: none;
                position: fixed;
                top: 118px;
                left: 20px;
                right: 20px;
                flex-direction: column;
                align-items: stretch;
                gap: 0;
                padding: 12px;
                background: #fff;
                border-radius: 22px;
                box-shadow: var(--shadow);
                border: 1px solid rgba(32,63,91,.10);
                color: #152236;
            }
            .nav__links.is-open { display: flex; }
            .nav__links a { padding: 14px; border-radius: 14px; }
            .nav__links a:hover { background: #f2f8fb; }
            .menu-btn { display: inline-flex; }
            .nav__actions .btn { display: none; }
            .hero__quick, .problem-grid, .services-grid { grid-template-columns: 1fr; }
            .diagnostic-item { grid-template-columns: 46px 1fr; }
            .diagnostic-item .pill { grid-column: 2; justify-self: start; }
        }
        @media (max-width: 560px) {
            .wrap { width: min(100% - 28px, var(--max)); }
            .topbar .wrap { justify-content: center; text-align: center; }
            .nav { height: 70px; }
            .brand__text span { display: none; }
            .hero { padding: 54px 0 60px; }
            .hero__actions .btn { width: 100%; }
            .section { padding: 64px 0; }
            .tech-panel, .problem-card, .what-box, .tender-box, .contact-card, .form-card { padding: 22px; border-radius: 20px; }
        }
    </style>
</head>
<body>
    <div class="topbar">
        <div class="wrap">
            <div class="weather-note"><span class="sun-dot"></span><span>Tryb strony: <?= $isNight ? 'nocny' : ($isCloudy ? 'dzienny, spokojny' : 'dzienny, jasny') ?></span></div>
            <div class="topbar__items">
                <a href="tel:<?= e($company['phone_href']) ?>"><?= e($company['phone']) ?></a>
                <a href="mailto:<?= e($company['email']) ?>"><?= e($company['email']) ?></a>
            </div>
        </div>
    </div>

    <header class="header" id="header">
        <div class="wrap nav">
            <a class="brand" href="#start" aria-label="ELSPECT Group">
                <span class="brand__mark">E</span>
                <span class="brand__text">ELSPECT GROUP <span>technika • energia • wykonawstwo</span></span>
            </a>
            <nav class="nav__links" id="navLinks" aria-label="Nawigacja główna">
                <a href="#problemy">Problemy</a>
                <a href="#dostajesz">Co dostajesz</a>
                <a href="#integrator">Integrator</a>
                <a href="#uslugi">Zakres</a>
                <a href="#przetargi">Przetargi</a>
                <a href="#kontakt">Kontakt</a>
            </nav>
            <div class="nav__actions">
                <a class="btn btn--primary" href="#kontakt">Opisz problem</a>
                <button class="menu-btn" id="menuBtn" type="button" aria-expanded="false" aria-controls="navLinks">Menu</button>
            </div>
        </div>
    </header>

    <main id="start">
        <section class="hero">
            <div class="wrap hero__grid">
                <div>
                    <div class="eyebrow"><span class="sun-dot"></span> Technicznie. Konkretnie. Od analizy do wdrożenia.</div>
                    <h1>Moc bierna, kompensacja, monitoring i wykonawstwo techniczne dla firm.</h1>
                    <p class="hero__lead">Chcecie kupić kompensator, zrobić przetarg, ograniczyć opłaty za energię bierną albo wyjaśnić problemy z jakością zasilania? <strong>Najpierw sprawdzamy dane, liczymy, mierzymy i określamy zasady techniczne.</strong> Dopiero później dobieramy urządzenia, organizujemy wykonanie i odbieramy efekt.</p>
                    <div class="hero__actions">
                        <a class="btn btn--primary" href="#kontakt">Wyślij faktury lub opisz problem</a>
                        <a class="btn btn--light" href="#problemy">Zobacz, kiedy pomagamy</a>
                    </div>
                    <div class="hero__quick">
                        <div class="quick-card"><strong>Problem z mocą bierną?</strong><span>Nie zaczynamy od katalogu kompensatorów, tylko od przyczyny opłat.</span></div>
                        <div class="quick-card"><strong>Przetarg techniczny?</strong><span>Pomagamy określić wymagania, pomiary, warunki i sensowny zakres.</span></div>
                        <div class="quick-card"><strong>Monitoring obiektu?</strong><span>Dobieramy, co mierzyć, gdzie mierzyć i jakie alarmy mają mieć znaczenie.</span></div>
                    </div>
                </div>

                <aside class="tech-panel reveal" aria-label="Panel techniczny ELSPECT">
                    <div class="panel-head">
                        <div class="panel-light"><i></i><i></i><i></i></div>
                        <span>ścieżka techniczna ELSPECT</span>
                    </div>
                    <div class="diagnostic-list">
                        <div class="diagnostic-item"><span class="dicon">1</span><div><strong>Faktury i profile 15-minutowe</strong><span>Sprawdzamy, kiedy powstaje koszt i czy problem jest stały, okresowy czy pozorny.</span></div><b class="pill info">analiza</b></div>
                        <div class="diagnostic-item"><span class="dicon">2</span><div><strong>Pomiary i rejestracja</strong><span>Moc, energia bierna, harmoniczne, zapady, obciążenie, prądy, napięcia i praca urządzeń.</span></div><b class="pill ok">pomiary</b></div>
                        <div class="diagnostic-item"><span class="dicon">3</span><div><strong>Dobór rozwiązania</strong><span>Kompensator, SVG, regulacja, modernizacja, monitoring albo zmiana założeń przetargu.</span></div><b class="pill warn">decyzja</b></div>
                    </div>
                    <div class="panel-note"><strong>Ważne:</strong> źle dobrana kompensacja może nie rozwiązać problemu, a czasem stworzyć następny. Dlatego najpierw ustalamy, co naprawdę mierzy licznik i jak pracuje obiekt.</div>
                </aside>
            </div>
        </section>

        <section class="section section--soft" id="problemy">
            <div class="wrap">
                <div class="section__head reveal">
                    <div>
                        <p class="kicker">Kiedy warto zadzwonić</p>
                        <h2>Najczęstsze problemy techniczne, które rozwiązujemy.</h2>
                    </div>
                    <p class="section__text">Strona główna ma prowadzić klienta po konkretnych sytuacjach: opłaty za energię bierną, dobór kompensatora, pomiary jakości zasilania, monitoring obiektu, przetarg techniczny albo modernizacja instalacji.</p>
                </div>

                <div class="problem-grid">
                    <article class="problem-card reveal"><span class="dicon">Q</span><h3>Opłaty za energię bierną</h3><p>Pojawiają się opłaty indukcyjne, pojemnościowe albo obie naraz. Kompensator istnieje, ale problem wraca. Najpierw sprawdzamy profil pracy, miejsce pomiaru i przyczynę.</p><a href="#kontakt">Wyślij faktury →</a></article>
                    <article class="problem-card reveal"><span class="dicon">C</span><h3>Dobór kompensatora</h3><p>Klient chce kupić kompensator, ale nie wie, czy wystarczy bateria kondensatorów, układ dławikowany, SVG, czy może regulacja obecnego układu.</p><a href="#kontakt">Sprawdźmy dobór →</a></article>
                    <article class="problem-card reveal"><span class="dicon">P</span><h3>Przetarg lub zapytanie ofertowe</h3><p>Zanim ogłosicie przetarg, trzeba określić zasady: dane wejściowe, pomiary, wymagania, odpowiedzialność wykonawcy i sposób odbioru efektu.</p><a href="#przetargi">Zobacz podejście →</a></article>
                    <article class="problem-card reveal"><span class="dicon">J</span><h3>Jakość zasilania i awarie</h3><p>Zapady napięcia, krótkie zaniki, zadziałania zabezpieczeń, reset urządzeń, przestoje produkcji lub niestabilna praca automatyki.</p><a href="#kontakt">Opisz zdarzenia →</a></article>
                    <article class="problem-card reveal"><span class="dicon">M</span><h3>Monitoring infrastruktury</h3><p>Obiekt działa, ale brakuje bieżącej wiedzy o obciążeniu, mocy, alarmach, stanie kompensacji, przekroczeniach i historii zdarzeń.</p><a href="#kontakt">Porozmawiajmy o monitoringu →</a></article>
                    <article class="problem-card reveal"><span class="dicon">R</span><h3>Modernizacja rozdzielni lub instalacji</h3><p>Trzeba uporządkować zasilanie, zabezpieczenia, trasy kablowe, sterowanie, pomiary albo przygotować prace bez ryzyka chaosu organizacyjnego.</p><a href="#kontakt">Ustalmy zakres →</a></article>
                </div>
            </div>
        </section>

        <section class="section" id="dostajesz">
            <div class="wrap what-grid">
                <div class="what-box reveal">
                    <p class="kicker">Co dostaje klient</p>
                    <h3>Raport, dobór, zakres wykonania i decyzja techniczna.</h3>
                    <div class="what-list">
                        <div><span class="num">1</span><p><strong>Analizę techniczną i ekonomiczną</strong><span>Faktury, profile 15-minutowe, moc umowna, energia bierna, obciążenia i ryzyka.</span></p></div>
                        <div><span class="num">2</span><p><strong>Wyniki pomiarów i interpretację</strong><span>Nie tylko wykresy, ale odpowiedź: co z tego wynika i co należy zrobić.</span></p></div>
                        <div><span class="num">3</span><p><strong>Dobór rozwiązania</strong><span>Urządzenie, regulacja, modernizacja, monitoring, zmiana założeń albo brak inwestycji, jeśli nie ma sensu.</span></p></div>
                        <div><span class="num">4</span><p><strong>Zakres do oferty lub przetargu</strong><span>Opis wymagań technicznych, kryteriów odbioru i danych, których wykonawca musi użyć.</span></p></div>
                    </div>
                </div>
                <div class="what-box reveal">
                    <p class="kicker">Dlaczego tak</p>
                    <h3>Zanim powiemy „kupcie to”, chcemy wiedzieć, dlaczego.</h3>
                    <p class="section__text" style="margin-top:16px">W energetyce obiektowej łatwo pomylić objaw z przyczyną. Wysoka opłata za moc bierną nie zawsze oznacza, że trzeba dołożyć większą baterię. Problem z zabezpieczeniem nie zawsze oznacza, że zabezpieczenie jest złe. A monitoring bez jasnego celu szybko staje się kolejnym ekranem, na który nikt nie patrzy.</p>
                    <p class="section__text" style="margin-top:16px">Dlatego pracujemy technicznie: dane, pomiar, obliczenia, dobór i odpowiedzialność za sens rozwiązania.</p>
                    <div class="hero__actions"><a class="btn btn--soft" href="#kontakt">Mam dane do sprawdzenia</a></div>
                </div>
            </div>
        </section>

        <section class="section section--dark" id="integrator">
            <div class="wrap integrator">
                <div class="reveal">
                    <p class="kicker">ELSPECT Group</p>
                    <h2>Jesteśmy firmą wykonawczo-integratorską. Prowadzimy temat od analizy do odbioru.</h2>
                    <p class="section__text" style="margin-top:18px">ELSPECT Group pozostaje firmą techniczną. Naszą rolą jest zrozumieć problem, policzyć go, dobrać rozwiązanie, ustalić wymagania i poprowadzić wdrożenie. Część prac wykonujemy bezpośrednio, część przez sprawdzonych specjalistów i partnerów, ale klient dostaje jeden techniczny punkt prowadzenia tematu: analizę, dobór, koordynację, nadzór i odbiór.</p>
                    <div class="hero__actions"><a class="btn btn--primary" href="#kontakt">Zacznij od konsultacji</a></div>
                </div>
                <div class="integrator-card reveal">
                    <div class="integrator-steps">
                        <div class="integrator-step"><strong>1. Rozpoznajemy problem techniczny</strong><span>Ustalamy, czy przyczyną są opłaty, układ pomiarowy, kompensacja, jakość zasilania, obciążenie, nastawy czy eksploatacja.</span></div>
                        <div class="integrator-step"><strong>2. Ustalamy wymagania techniczne</strong><span>Dane, pomiary, parametry, odpowiedzialność, odbiór, monitoring i sposób potwierdzenia efektu.</span></div>
                        <div class="integrator-step"><strong>3. Organizujemy wykonanie</strong><span>Dobór urządzeń, wykonawców, harmonogram, dokumentacja, nadzór i odbiór techniczny.</span></div>
                        <div class="integrator-step"><strong>4. Zostawiamy klienta z efektem i dokumentacją</strong><span>Raport, zalecenia, historia pomiarów, potwierdzenie działania i jasny następny krok.</span></div>
                    </div>
                </div>
            </div>
        </section>

        <section class="section section--blue" id="uslugi">
            <div class="wrap">
                <div class="section__head reveal">
                    <div>
                        <p class="kicker">Zakres techniczny</p>
                        <h2>Obszary, w których ELSPECT Group ma być kojarzony jasno.</h2>
                    </div>
                    <p class="section__text">Tu komunikacja ma być prosta: klient ma mieć problem, kliknąć temat i zrozumieć, że może wysłać dane albo zadzwonić.</p>
                </div>
                <div class="services-grid">
                    <div class="service reveal"><span class="dicon">Q</span><strong>Moc bierna i kompensacja</strong><span>Analiza, dobór, modernizacja, SVG, baterie, regulatory, praca przy OZE i transformatorach.</span></div>
                    <div class="service reveal"><span class="dicon">E</span><strong>Koszty energii</strong><span>Faktury, profile, moc umowna, przekroczenia, taryfy, nietypowe obciążenia i ryzyka.</span></div>
                    <div class="service reveal"><span class="dicon">J</span><strong>Jakość energii</strong><span>Zapady, zaniki, harmoniczne, asymetria, przeciążenia, rejestracja zdarzeń.</span></div>
                    <div class="service reveal"><span class="dicon">M</span><strong>Monitoring</strong><span>Stały nadzór nad rozdzielnią, kompensacją, SOG, falownikami, obciążeniami i alarmami.</span></div>
                    <div class="service reveal"><span class="dicon">S</span><strong>Rozdzielnie i zabezpieczenia</strong><span>Nastawy, dobór, selektywność, dokumentacja, ryzyka eksploatacyjne i odbiory.</span></div>
                    <div class="service reveal"><span class="dicon">A</span><strong>Automatyka i zasilanie urządzeń</strong><span>Problemy na styku elektryki, sterowania, UPS, falowników i pracy maszyn.</span></div>
                    <div class="service reveal"><span class="dicon">I</span><strong>Infrastruktura obiektowa</strong><span>Trasy kablowe, sygnalizacja, niskie prądy, serwerownie, obiekty logistyczne i techniczne.</span></div>
                    <div class="service reveal"><span class="dicon">D</span><strong>Dokumentacja i wymagania</strong><span>Założenia techniczne, opisy do ofert, warunki odbioru, formularze oględzin i standardy.</span></div>
                </div>
            </div>
        </section>

        <section class="section" id="przetargi">
            <div class="wrap tender-box reveal">
                <div>
                    <p class="kicker">Przetargi i zakupy techniczne</p>
                    <h3>Chcecie ogłosić przetarg? Najpierw określcie, co ma zostać osiągnięte.</h3>
                    <p>Pomagamy przygotować założenia techniczne tak, żeby nie kupować „czegoś podobnego”, tylko rozwiązanie dobrane do rzeczywistego problemu.</p>
                    <div class="hero__actions"><a class="btn btn--primary" href="#kontakt">Przygotujmy zasady przetargu</a></div>
                </div>
                <div class="checklist">
                    <div><b>✓</b> jakie dane wykonawca musi dostać przed ofertą</div>
                    <div><b>✓</b> jakie pomiary są konieczne przed doborem urządzenia</div>
                    <div><b>✓</b> jak opisać wymagany efekt, a nie tylko nazwę urządzenia</div>
                    <div><b>✓</b> jak sprawdzić, czy kompensacja lub monitoring faktycznie działa</div>
                    <div><b>✓</b> jak ograniczyć ryzyko ofert najtańszych, ale technicznie błędnych</div>
                </div>
            </div>
        </section>

        <section class="section section--soft" id="kontakt">
            <div class="wrap contact-grid">
                <div class="contact-card reveal">
                    <p class="kicker">Kontakt techniczny</p>
                    <h2>Wyślij faktury, profile albo krótki opis problemu.</h2>
                    <p class="section__text" style="margin-top:16px">Najlepiej zacząć od krótkiego opisu: co się dzieje, od kiedy, jaki jest obiekt, jakie są opłaty lub objawy oraz czy macie faktury, profile 15-minutowe, zdjęcia rozdzielni albo dotychczasowe pomiary.</p>
                    <div class="contact-list">
                        <a href="tel:<?= e($company['phone_href']) ?>"><span class="dicon">☎</span><span><?= e($company['phone']) ?></span></a>
                        <a href="mailto:<?= e($company['email']) ?>"><span class="dicon">@</span><span><?= e($company['email']) ?></span></a>
                        <div><span class="dicon">⌂</span><span><?= e($company['address']) ?></span></div>
                    </div>
                </div>

                <div class="form-card reveal">
                    <h3>Formularz opisu problemu</h3>
                    <form id="contactForm" data-email="<?= e($company['email']) ?>">
                        <div class="field"><label for="name">Imię i firma</label><input id="name" name="name" placeholder="np. Jan Kowalski, Firma Produkcyjna" required></div>
                        <div class="field"><label for="contact">E-mail lub telefon</label><input id="contact" name="contact" placeholder="np. jan@firma.pl / +48 ..." required></div>
                        <div class="field"><label for="topic">Temat</label><select id="topic" name="topic"><option>Moc bierna / kompensacja</option><option>Analiza faktur i kosztów energii</option><option>Jakość zasilania / zapady / awarie</option><option>Monitoring infrastruktury</option><option>Przetarg / zapytanie techniczne</option><option>Modernizacja instalacji</option></select></div>
                        <div class="field"><label for="message">Co chcecie rozwiązać?</label><textarea id="message" name="message" placeholder="Napisz, co się dzieje, jakie są objawy, koszty, urządzenia, lokalizacja problemu i co już było sprawdzane." required></textarea></div>
                        <p class="form-note">Wersja bez CMS: formularz przygotowuje wiadomość e-mail. Docelowo można dodać wysyłkę PHP, zapis do pliku lub integrację z prostym CRM.</p>
                        <div class="form-status" id="formStatus">Przygotowuję wiadomość e-mail...</div>
                        <div class="hero__actions"><button class="btn btn--dark" type="submit">Przygotuj wiadomość</button></div>
                    </form>
                </div>
            </div>
        </section>
    </main>

    <footer class="footer">
        <div class="wrap">
            <a class="brand" href="#start"><span class="brand__mark">E</span><span class="brand__text">ELSPECT GROUP <span>technika • energia • wykonawstwo</span></span></a>
            <div>© <?= e($now->format('Y')) ?> <?= e($company['name']) ?> · <a href="mailto:<?= e($company['email']) ?>"><?= e($company['email']) ?></a></div>
        </div>
    </footer>

    <script>
        const header = document.getElementById('header');
        const menuBtn = document.getElementById('menuBtn');
        const navLinks = document.getElementById('navLinks');
        const form = document.getElementById('contactForm');
        const formStatus = document.getElementById('formStatus');

        window.addEventListener('scroll', () => {
            header.classList.toggle('is-scrolled', window.scrollY > 12);
        });

        menuBtn.addEventListener('click', () => {
            const open = navLinks.classList.toggle('is-open');
            menuBtn.setAttribute('aria-expanded', String(open));
            menuBtn.textContent = open ? 'Zamknij' : 'Menu';
        });

        navLinks.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', () => {
                navLinks.classList.remove('is-open');
                menuBtn.setAttribute('aria-expanded', 'false');
                menuBtn.textContent = 'Menu';
            });
        });

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('in-view');
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.12 });
        document.querySelectorAll('.reveal').forEach(el => observer.observe(el));

        form.addEventListener('submit', (event) => {
            event.preventDefault();
            const data = new FormData(form);
            const subject = encodeURIComponent('ELSPECT — ' + data.get('topic'));
            const body = encodeURIComponent(
                'Imię i firma: ' + data.get('name') + '\n' +
                'Kontakt: ' + data.get('contact') + '\n' +
                'Temat: ' + data.get('topic') + '\n\n' +
                'Opis problemu:\n' + data.get('message') + '\n\n' +
                'Wysłane ze strony ELSPECT Group.'
            );
            formStatus.style.display = 'block';
            const recipientEmailRaw = form.dataset.email || 'biuro@elspect.pl';
            const recipientEmail = recipientEmailRaw.includes('<?') ? 'biuro@elspect.pl' : recipientEmailRaw;
            window.location.href = 'mailto:' + recipientEmail + '?subject=' + subject + '&body=' + body;
        });
    </script>

    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "LocalBusiness",
        "name": "<?= e($company['name']) ?>",
        "url": "https://elspect.pl",
        "email": "<?= e($company['email']) ?>",
        "telephone": "<?= e($company['phone_href']) ?>",
        "address": {
            "@type": "PostalAddress",
            "streetAddress": "ul. Małopanewska 18",
            "postalCode": "54-212",
            "addressLocality": "Wrocław",
            "addressCountry": "PL"
        },
        "description": "Moc bierna, kompensacja, analiza kosztów energii, jakość zasilania, monitoring infrastruktury i założenia techniczne do przetargów."
    }
    </script>
</body>
</html>
