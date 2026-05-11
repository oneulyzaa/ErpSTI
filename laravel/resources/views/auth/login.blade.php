<!doctype html>
<html lang="id" class="h-full">
 <head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login</title>
  <script src="/_sdk/element_sdk.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/lucide@0.263.0/dist/umd/lucide.min.js"></script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Playfair+Display:wght@600;700&family=Dancing+Script:wght@500;600&display=swap" rel="stylesheet">
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    html, body { height: 100%; width: 100%; }
    body {
      font-family: 'DM Sans', sans-serif;
      background: linear-gradient(135deg, #f0f4f8 0%, #e2e8f0 50%, #ddd6f3 100%);
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 20px;
      overflow: auto;
    }

    .login-card {
      width: 100%;
      max-width: 920px;
      background: #fff;
      border-radius: 24px;
      box-shadow:
        0 4px 6px -1px rgba(0,0,0,0.04),
        0 20px 50px -12px rgba(0,0,0,0.12),
        0 0 0 1px rgba(0,0,0,0.03);
      display: flex;
      overflow: hidden;
      animation: cardIn 0.6s cubic-bezier(0.22, 1, 0.36, 1);
    }

    @keyframes cardIn {
      from { opacity: 0; transform: translateY(24px) scale(0.98); }
      to { opacity: 1; transform: translateY(0) scale(1); }
    }

    /* Left column */
    .brand-col {
      flex: 1;
      padding: 56px 40px;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      text-align: center;
      position: relative;
      overflow: hidden;
    }
    .brand-col::before {
      content: '';
      position: absolute;
      width: 260px; height: 260px;
      border-radius: 50%;
      background: rgba(139, 92, 246, 0.12);
      top: -60px; right: -60px;
    }
    .brand-col::after {
      content: '';
      position: absolute;
      width: 180px; height: 180px;
      border-radius: 50%;
      bottom: -40px; left: -40px;
    }

    .logo-wrap {
      border-radius: 18px;
      display: flex;
      align-items: center;
      justify-content: center;
      margin-bottom: 24px;
      position: relative;
      z-index: 1;
    }
    .logo-wrap svg { width: 38px; height: 38px; }

    .brand-name {
      color: #f8fafc;
      font-size: 28px;
      font-weight: 700;
      letter-spacing: -0.5px;
      margin-bottom: 12px;
      position: relative; z-index: 1;
    }
    .brand-slogan {
      font-family: 'Dancing Script', cursive;
      color: #94a3b8;
      font-size: 25px;
      line-height: 1.6;
      max-width: 240px;
      position: relative; z-index: 1;
    }
    .brand-divider {
      width: 40px; height: 3px;
      border-radius: 2px;
      margin: 20px auto;
      position: relative; z-index: 1;
    }
    .demo-badge {
      position: absolute;
      bottom: 16px;
      font-size: 10px;
      color: #64748b;
      letter-spacing: 1px;
      text-transform: uppercase;
      z-index: 1;
    }

    /* Right column */
    .form-col {
      flex: 1;
      padding: 56px 44px;
      display: flex;
      flex-direction: column;
      justify-content: center;
    }
    .form-welcome {
      font-size: 26px;
      font-weight: 700;
      margin-bottom: 6px;
    }
    .form-subtitle {
      font-size: 14px;
      margin-bottom: 32px;
    }

    .field-group { margin-bottom: 20px; }
    .field-group label {
      display: block;
      font-size: 12px;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.8px;
      margin-bottom: 8px;
    }
    .field-wrap {
      display: flex;
      align-items: center;
      border: 1.5px solid #e2e8f0;
      border-radius: 12px;
      padding: 0 14px;
      height: 48px;
      transition: border-color 0.2s, box-shadow 0.2s;
      background: #f8fafc;
    }
    .field-wrap:focus-within {
      border-color: #8b5cf6;
      box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.1);
      background: #fff;
    }
    .field-wrap i { width: 18px; height: 18px; flex-shrink: 0; }
    .field-wrap input {
      flex: 1;
      border: none;
      outline: none;
      background: transparent;
      font-family: 'DM Sans', sans-serif;
      font-size: 14px;
      padding: 0 10px;
      color: #1e293b;
    }
    .field-wrap input::placeholder { color: #94a3b8; }
    .toggle-pw {
      background: none; border: none; cursor: pointer; padding: 4px;
      color: #94a3b8;
      transition: color 0.2s;
    }
    .toggle-pw:hover { color: #64748b; }
    .toggle-pw i { width: 18px; height: 18px; }

    .remember-row {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 28px;
      font-size: 13px;
    }
    .remember-row label { text-transform: none; letter-spacing: 0; font-weight: 400; display: flex; align-items: center; gap: 6px; cursor: pointer; }
    .remember-row input[type="checkbox"] { accent-color: #8b5cf6; width: 16px; height: 16px; }
    .remember-row a { text-decoration: none; font-weight: 500; transition: opacity 0.2s; }
    .remember-row a:hover { opacity: 0.8; }

    .btn-login {
      width: 100%;
      height: 50px;
      border: none;
      border-radius: 12px;
      font-family: 'DM Sans', sans-serif;
      font-size: 15px;
      font-weight: 600;
      color: #fff;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      transition: transform 0.15s, box-shadow 0.2s;
      letter-spacing: 0.3px;
    }
    .btn-login:hover { transform: translateY(-1px); }
    .btn-login:active { transform: translateY(0); }
    .btn-login i { width: 18px; height: 18px; }

    .toast-msg {
      margin-top: 16px;
      padding: 12px 16px;
      border-radius: 10px;
      font-size: 13px;
      text-align: center;
      display: none;
      animation: fadeIn 0.3s;
    }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(4px); } to { opacity: 1; transform: translateY(0); } }

    @media (max-width: 700px) {
      .login-card { flex-direction: column; max-width: 440px; }
      .brand-col { padding: 36px 28px; }
      .form-col { padding: 36px 28px; }
    }
  </style>
  <style>body { box-sizing: border-box; }</style>
  <script src="https://cdn.tailwindcss.com/3.4.17" type="text/javascript"></script>
  <script src="/_sdk/data_sdk.js" type="text/javascript"></script>
 </head>
 <body>
  <div class="login-card" role="main"><!-- Left: Branding -->
   <div class="brand-col">
    <div class="logo-wrap" id="logoWrap">
<img src="/assets/gambar/logo-sti.png" alt="">
    </div>
    <div class="brand-name" id="brandName">
     PT. SISTEM TEKNOLOGI INTEGRATOR 
    </div>
    <div class="brand-divider" id="brandDivider" style="background: #8b5cf6;"></div>
    <div class="brand-slogan" id="brandSlogan" style="font-family: 'Dancing Script', cursive">
     Ready to connected
    </div>
    <div class="demo-badge">
     @2026 Universitas Krisnadwipayana — Demo Only
    </div>
   </div><!-- Right: Form -->
   <div class="form-col">
    <div class="form-welcome" id="formWelcome" style="color: #1e293b;">
     Selamat Datang
    </div>
    <div class="form-subtitle" id="formSubtitle" style="color: #64748b;">
     Masuk ke akun Anda untuk melanjutkan
    </div>
    @if(session('error') || $errors->any())
    <div style="background:#fee2e2;color:#991b1b;border:1px solid #fecaca;border-radius:10px;padding:12px 16px;font-size:13px;text-align:center;margin-bottom:20px;">
      &#9888;&nbsp;
      @if(session('error'))
        {{ session('error') }}
      @else
        {{ $errors->first() }}
      @endif
    </div>
    @endif
    <form id="loginForm" method="POST" action="/login">
     <div class="field-group"><label id="emailLabel" style="color: #64748b;">Username / Email</label>
      <div class="field-wrap"><i data-lucide="mail" style="color: #94a3b8;"></i> <input type="text" name="email" placeholder="nama@email.com" autocomplete="off" aria-label="Username atau Email">
      </div>
     </div>
     <div class="field-group"><label id="pwLabel" style="color: #64748b;">Password</label>
      <div class="field-wrap"><i data-lucide="lock" style="color: #94a3b8;"></i> <input type="password" name="password" id="pwInput" placeholder="••••••••" autocomplete="off" aria-label="Password"> <button type="button" class="toggle-pw" onclick="togglePw()" aria-label="Toggle password visibility"> <i data-lucide="eye" id="eyeIcon"></i> </button>
      </div>
     </div><button type="submit" class="btn-login" id="btnLogin" style="background: linear-gradient(135deg, #8b5cf6, #6366f1); box-shadow: 0 4px 14px rgba(139,92,246,0.35);"> <span id="btnText">Masuk</span> <i data-lucide="arrow-right"></i> </button>
     {{-- <div class="toast-msg" id="toast" style="background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0;">
      ⚠ Ini adalah halaman demo. Login tidak diproses.
     </div> --}}
     @csrf 
    </form>
   </div>
  </div>
  <script>
  function togglePw() {
    const inp = document.getElementById('pwInput');
    const icon = document.getElementById('eyeIcon');
    if (inp.type === 'password') {
      inp.type = 'text';
      icon.setAttribute('data-lucide', 'eye-off');
    } else {
      inp.type = 'password';
      icon.setAttribute('data-lucide', 'eye');
    }
    lucide.createIcons();
  }

  

  // --- Element SDK ---
  const defaultConfig = {
    company_name: 'NexaCorp',
    company_slogan: 'Inovasi tanpa batas untuk masa depan yang lebih cerah',
    welcome_text: 'Selamat Datang',
    welcome_subtitle: 'Masuk ke akun Anda untuk melanjutkan',
    button_text: 'Masuk',
    background_color: '#f0f4f8',
    surface_color: '#1e293b',
    text_color: '#1e293b',
    primary_action: '#8b5cf6',
    secondary_action: '#64748b',
    font_family: 'DM Sans',
    font_size: 14
  };

  function applyConfig(c) {
    const bg = c.background_color || defaultConfig.background_color;
    const surface = c.surface_color || defaultConfig.surface_color;
    const text = c.text_color || defaultConfig.text_color;
    const primary = c.primary_action || defaultConfig.primary_action;
    const secondary = c.secondary_action || defaultConfig.secondary_action;
    const font = c.font_family || defaultConfig.font_family;
    const fs = c.font_size || defaultConfig.font_size;

    document.body.style.background = `linear-gradient(135deg, ${bg} 0%, ${bg} 100%)`;

    const brandCol = document.querySelector('.brand-col');
    brandCol.style.background = `linear-gradient(160deg, ${surface} 0%, ${surface}dd 100%)`;

    document.getElementById('logoWrap').style.background = `linear-gradient(135deg, ${primary}, ${primary}cc)`;
    document.getElementById('brandDivider').style.background = primary;
    document.getElementById('brandName').textContent = c.company_name || defaultConfig.company_name;
    document.getElementById('brandSlogan').textContent = c.company_slogan || defaultConfig.company_slogan;

    document.getElementById('formWelcome').textContent = c.welcome_text || defaultConfig.welcome_text;
    document.getElementById('formWelcome').style.color = text;
    document.getElementById('formSubtitle').textContent = c.welcome_subtitle || defaultConfig.welcome_subtitle;
    document.getElementById('formSubtitle').style.color = secondary;

    document.getElementById('emailLabel').style.color = secondary;
    document.getElementById('pwLabel').style.color = secondary;

    const btn = document.getElementById('btnLogin');
    btn.style.background = `linear-gradient(135deg, ${primary}, ${primary}cc)`;
    btn.style.boxShadow = `0 4px 14px ${primary}55`;
    document.getElementById('btnText').textContent = c.button_text || defaultConfig.button_text;

    document.getElementById('forgotLink').style.color = primary;

    // Fonts
    const fontStack = `${font}, 'DM Sans', sans-serif`;
    document.body.style.fontFamily = fontStack;
    // Font size
    document.querySelector('.form-subtitle').style.fontSize = `${fs}px`;
    document.querySelector('.brand-slogan').style.fontSize = `${fs}px`;
    document.querySelector('.form-welcome').style.fontSize = `${fs * 1.85}px`;
    document.querySelector('.brand-name').style.fontSize = `${fs * 2}px`;
    document.querySelectorAll('.field-group label').forEach(el => el.style.fontSize = `${fs * 0.85}px`);
    document.querySelectorAll('.field-wrap input').forEach(el => el.style.fontSize = `${fs}px`);
    document.querySelector('.btn-login').style.fontSize = `${fs * 1.07}px`;
  }

  window.elementSdk.init({
    defaultConfig,
    onConfigChange: async (config) => applyConfig(config),
    mapToCapabilities: (config) => ({
      recolorables: [
        { get: () => config.background_color || defaultConfig.background_color, set: (v) => { config.background_color = v; window.elementSdk.setConfig({ background_color: v }); } },
        { get: () => config.surface_color || defaultConfig.surface_color, set: (v) => { config.surface_color = v; window.elementSdk.setConfig({ surface_color: v }); } },
        { get: () => config.text_color || defaultConfig.text_color, set: (v) => { config.text_color = v; window.elementSdk.setConfig({ text_color: v }); } },
        { get: () => config.primary_action || defaultConfig.primary_action, set: (v) => { config.primary_action = v; window.elementSdk.setConfig({ primary_action: v }); } },
        { get: () => config.secondary_action || defaultConfig.secondary_action, set: (v) => { config.secondary_action = v; window.elementSdk.setConfig({ secondary_action: v }); } }
      ],
      borderables: [],
      fontEditable: {
        get: () => config.font_family || defaultConfig.font_family,
        set: (v) => { config.font_family = v; window.elementSdk.setConfig({ font_family: v }); }
      },
      fontSizeable: {
        get: () => config.font_size || defaultConfig.font_size,
        set: (v) => { config.font_size = v; window.elementSdk.setConfig({ font_size: v }); }
      }
    }),
    mapToEditPanelValues: (config) => new Map([
      ['company_name', config.company_name || defaultConfig.company_name],
      ['company_slogan', config.company_slogan || defaultConfig.company_slogan],
      ['welcome_text', config.welcome_text || defaultConfig.welcome_text],
      ['welcome_subtitle', config.welcome_subtitle || defaultConfig.welcome_subtitle],
      ['button_text', config.button_text || defaultConfig.button_text]
    ])
  });

  lucide.createIcons();
</script>
 <script>(function(){function c(){var b=a.contentDocument||a.contentWindow.document;if(b){var d=b.createElement('script');d.innerHTML="window.__CF$cv$params={r:'9f22aeb994c98799',t:'MTc3NzE3NjA0Ny4wMDAwMDA='};var a=document.createElement('script');a.nonce='';a.src='/cdn-cgi/challenge-platform/scripts/jsd/main.js';document.getElementsByTagName('head')[0].appendChild(a);";b.getElementsByTagName('head')[0].appendChild(d)}}if(document.body){var a=document.createElement('iframe');a.height=1;a.width=1;a.style.position='absolute';a.style.top=0;a.style.left=0;a.style.border='none';a.style.visibility='hidden';document.body.appendChild(a);if('loading'!==document.readyState)c();else if(window.addEventListener)document.addEventListener('DOMContentLoaded',c);else{var e=document.onreadystatechange||function(){};document.onreadystatechange=function(b){e(b);'loading'!==document.readyState&&(document.onreadystatechange=e,c())}}}})();</script></body>
</html>
