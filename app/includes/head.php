<?php
$theme = require __DIR__ . '/theme.php';
$fontFamily = $theme['font_family'] ?? 'Sarabun';
$fontsLink  = $theme['fonts_link']  ?? 'https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700&display=swap';
?>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="<?= htmlspecialchars($fontsLink) ?>" rel="stylesheet">
<script>
  tailwind = window.tailwind || {};
  tailwind.config = {
    theme: {
      extend: {
        fontFamily: {
          sans: <?= json_encode([$fontFamily,'ui-sans-serif','system-ui','-apple-system','Segoe UI','Roboto','Noto Sans','Ubuntu','Cantarell','Helvetica Neue','Arial','sans-serif']) ?>
        },
        colors: { brand: { green:'#16a34a', neon:'#00ff9c', dark:'#0b132b', light:'#E8FFF4' } },
        boxShadow: { 'neon':'0 0 10px rgba(0,255,156,0.6), 0 0 30px rgba(0,255,156,0.3)', 'glass':'0 4px 40px rgba(0,0,0,0.15)' }
      }
    }
  }
</script>
<script src="https://cdn.tailwindcss.com"></script>
<style>
  :root{ --brand-green:#16a34a; --brand-neon:#00ff9c; --brand-bg:#f8fffb; --brand-deep:#0b132b; }
  html, body, .font-sans{
    font-family: '<?= htmlspecialchars($fontFamily, ENT_QUOTES) ?>', ui-sans-serif, system-ui, -apple-system, 'Segoe UI', Roboto, 'Noto Sans', Ubuntu, Cantarell, 'Helvetica Neue', Arial, sans-serif !important;
    background:
      radial-gradient(1200px 600px at 80% -10%, rgba(0,255,156,0.20), transparent 60%),
      radial-gradient(800px 400px at 10% 110%, rgba(22,163,74,0.15), transparent 60%),
      linear-gradient(180deg, var(--brand-bg), #ffffff 60%);
  }
  .grid-overlay::before{ content:''; position:fixed; inset:0; pointer-events:none;
    background: linear-gradient(transparent 96%, rgba(22,163,74,0.25) 98%), linear-gradient(90deg, transparent 96%, rgba(22,163,74,0.25) 98%);
    background-size:20px 20px; mask-image: radial-gradient(circle at 50% 30%, rgba(0,0,0,0.45), transparent 70%); opacity:.35; }
  .card-glass{ background: rgba(255,255,255,0.75); backdrop-filter: blur(10px); box-shadow: var(--tw-shadow), 0 0 0 1px rgba(22,163,74,0.08); }
  .btn-neon{ position:relative; transition: transform .15s, box-shadow .2s; box-shadow: 0 0 0 1px rgba(22,163,74,0.35), 0 6px 24px rgba(0,0,0,.10); }
  .btn-neon::after{ content:''; position:absolute; inset:-2px; border-radius:16px; background: conic-gradient(from 180deg, rgba(0,255,156,.0), rgba(0,255,156,.8), rgba(0,255,156,.0));
    filter: blur(10px); opacity:.0; transition: opacity .25s; z-index:-1; }
  .btn-neon:hover{ transform: translateY(-1px); box-shadow: 0 0 0 1px rgba(0,255,156,.5), 0 10px 30px rgba(0,255,156,.25); }
  .btn-neon:hover::after{ opacity:.8; }
  .input-fx{ border:1px solid rgba(22,163,74,0.25); background: linear-gradient(180deg, rgba(255,255,255,.95), rgba(255,255,255,.80));
    transition: box-shadow .15s, border-color .15s; }
  .input-fx:focus{ outline:none; border-color: rgba(0,255,156,.8); box-shadow: 0 0 0 3px rgba(0,255,156,.18), inset 0 0 10px rgba(0,255,156,.1); }
  .title-underline{ position:relative; }
  .title-underline::after{ content:''; position:absolute; left:0; bottom:-6px; width:120px; height:3px; background: linear-gradient(90deg, var(--brand-green), var(--brand-neon)); filter: drop-shadow(0 2px 6px rgba(0,255,156,.5)); border-radius:999px; }
  .toast-wrap{ position:fixed; right:20px; bottom:20px; display:grid; gap:10px; z-index:9999; }
  .toast{ padding:10px 14px; border-radius:12px; color:#083d2b; background:#E8FFF4; border:1px solid rgba(0,255,156,.4);
    box-shadow: 0 8px 24px rgba(0,0,0,.1), 0 0 0 1px rgba(22,163,74,.05); transform: translateY(20px); opacity:0; animation: toastIn .35s ease forwards; }
  .toast.error{ background:#ffeaea; color:#7a1c1c; border-color: rgba(239,68,68,.45); }
  @keyframes toastIn{ to { transform: translateY(0); opacity:1; } }
  .glow-ring{ box-shadow: 0 0 0 6px rgba(0,255,156,.12), 0 0 34px rgba(0,255,156,.35); border-radius:9999px; }
  .sparkle { position:absolute; width:6px; height:6px; background: var(--brand-neon); border-radius:9999px; filter:blur(2px); opacity:.7; animation: float 6s ease-in-out infinite; }
  @keyframes float { 0%{ transform:translateY(0) translateX(0); opacity:.4; } 50%{ transform:translateY(-18px) translateX(6px); opacity:.8; } 100%{ transform:translateY(0) translateX(0); opacity:.4; } }
</style>
