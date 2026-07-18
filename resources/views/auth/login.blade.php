<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{{ $panelName }} - Sign In / Sign Up</title>
@if($panelLogo)<link rel="icon" href="{{ $panelLogo }}">@endif
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<style>
:root{--bg:#12121a;--surface:#1e1e2c;--text:#e8e4e0;--text2:#999;--text3:#555;--border:rgba(255,255,255,.06);--shadow:rgba(0,0,0,.2);--accent:#ff6b6b;--accent2:#ffd93d;--input-bg:rgba(255,255,255,.04);--card-shadow:rgba(0,0,0,.3)}
*{margin:0;padding:0;box-sizing:border-box}
body{font-family:'Segoe UI',system-ui,-apple-system,sans-serif;background:var(--bg);color:var(--text);min-height:100vh;display:flex;align-items:center;justify-content:center;overflow:hidden;transition:background .4s,color .4s;padding:20px}
.auth-card{background:var(--surface);border:1px solid var(--border);border-radius:24px;padding:36px 32px;width:100%;max-width:400px;box-shadow:0 20px 60px var(--card-shadow);position:relative;transition:all .4s;animation:popIn .5s cubic-bezier(.22,1,.36,1) both}
@keyframes popIn{from{opacity:0;transform:scale(.96) translateY(12px)}to{opacity:1;transform:scale(1) translateY(0)}}
.logo{display:flex;align-items:center;gap:8px;text-decoration:none;color:var(--text);margin-bottom:24px;font-size:20px;font-weight:700}
.tabs{display:flex;gap:4px;background:var(--bg);border-radius:12px;padding:4px;margin-bottom:24px}
.tab{flex:1;text-align:center;padding:9px;border-radius:9px;cursor:pointer;font-size:14px;font-weight:500;color:var(--text2);transition:all .3s;border:none;background:none}
.tab.active{background:var(--surface);color:var(--text);box-shadow:0 2px 8px var(--shadow)}
.form-group{margin-bottom:16px}
.form-group label{display:block;font-size:12px;font-weight:500;color:var(--text2);margin-bottom:4px}
.form-group input{width:100%;padding:11px 14px;background:var(--input-bg);border:1px solid var(--border);border-radius:11px;color:var(--text);font-size:14px;transition:all .3s;outline:none}
.form-group input:focus{border-color:var(--accent);box-shadow:0 0 0 3px rgba(255,107,107,.1)}
.name-row{display:flex;gap:10px}
.name-row .form-group{flex:1}
.btn-primary{width:100%;padding:12px;background:var(--accent);border:none;border-radius:11px;color:#fff;font-size:15px;font-weight:600;cursor:pointer;transition:all .3s}
.btn-primary:hover{transform:translateY(-1px);box-shadow:0 4px 16px rgba(255,107,107,.3)}
.btn-primary:disabled{opacity:.4;cursor:not-allowed;transform:none!important}
.divider{display:flex;align-items:center;gap:12px;margin:20px 0;color:var(--text3);font-size:12px}
.divider::before,.divider::after{content:'';flex:1;height:1px;background:var(--border)}
.social-btns{display:flex;gap:10px}
.social-btn{flex:1;display:flex;align-items:center;justify-content:center;gap:8px;padding:11px;border-radius:11px;border:1px solid var(--border);background:transparent;color:var(--text2);font-size:13px;font-weight:500;cursor:pointer;transition:all .3s;text-decoration:none}
.social-btn:hover{background:var(--bg);border-color:var(--text3)}
.auth-footer{text-align:center;margin-top:16px;font-size:13px;color:var(--text3)}
.auth-footer a{color:var(--accent);text-decoration:none;font-weight:500}
.msg{font-size:13px;text-align:center;padding:10px;border-radius:10px;margin-bottom:16px;display:none}
.msg.error{display:block;color:#e74c3c;background:rgba(231,76,60,.08)}
.msg.success{display:block;color:#27ae60;background:rgba(39,174,96,.08)}
.login-form{display:block}
.login-form.hidden{display:none}
.signup-form{display:none}
.signup-form.active{display:block}
.forgot{text-align:right;font-size:12px;margin:-8px 0 16px}
.forgot a{color:var(--text3);text-decoration:none}
</style>
</head>
<body>

<div class="auth-card">
  <a href="{{ url('/') }}" class="logo"><span>@if($panelLogo)<img src="{{ $panelLogo }}" style="width:20px;height:20px;border-radius:4px;object-fit:cover;vertical-align:middle">@else<i class="fas fa-cubes"></i>@endif</span> {{ $panelName }}</a>

  <div class="tabs">
    <button class="tab active" onclick="switchTab('login')">Sign In</button>
    <button class="tab" onclick="switchTab('signup')">Sign Up</button>
  </div>

  <div id="errorMsg" class="msg error"></div>
  <div id="successMsg" class="msg success"></div>

  <form class="login-form" id="loginForm" onsubmit="handleLogin(event)">
    <div class="form-group"><label>email</label><input type="email" id="loginEmail" placeholder="you@example.com" required></div>
    <div class="form-group"><label>password</label><input type="password" id="loginPass" placeholder="enter your password" required></div>
    <div class="forgot"><a href="#" onclick="showToast('Password reset coming soon')">forgot password?</a></div>
    <button type="submit" class="btn-primary" id="loginBtn">sign in</button>
    <div class="divider">or</div>
    <div class="social-btns">
      <button type="button" class="social-btn google" onclick="socialLogin('google')"><i class="fab fa-google"></i> Google</button>
      <button type="button" class="social-btn discord" onclick="socialLogin('discord')"><i class="fab fa-discord"></i> Discord</button>
    </div>
  </form>

  <form class="signup-form" id="signupForm" onsubmit="handleSignup(event)">
    <div class="name-row">
      <div class="form-group"><label>first name</label><input type="text" id="signupFirst" placeholder="First name" required></div>
      <div class="form-group"><label>last name</label><input type="text" id="signupLast" placeholder="Last name" required></div>
    </div>
    <div class="form-group"><label>email</label><input type="email" id="signupEmail" placeholder="you@example.com" required></div>
    <div class="form-group"><label>password</label><input type="password" id="signupPass" placeholder="minimum 8 characters" required minlength="8"></div>
    <div class="form-group"><label>confirm password</label><input type="password" id="signupConfirm" placeholder="re-enter password" required></div>
    <button type="submit" class="btn-primary" id="signupBtn">create account</button>
    <div class="divider">or</div>
    <div class="social-btns">
      <button type="button" class="social-btn google" onclick="socialLogin('google')"><i class="fab fa-google"></i> Google</button>
      <button type="button" class="social-btn discord" onclick="socialLogin('discord')"><i class="fab fa-discord"></i> Discord</button>
    </div>
  </form>
</div>

<script>
function switchTab(tab) {
  document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'))
  const lf = document.getElementById('loginForm'), sf = document.getElementById('signupForm')
  if (tab === 'login') { document.querySelectorAll('.tab')[0].classList.add('active'); lf.classList.remove('hidden'); sf.classList.remove('active') }
  else { document.querySelectorAll('.tab')[1].classList.add('active'); sf.classList.add('active'); lf.classList.add('hidden') }
}

function showError(m) { const e = document.getElementById('errorMsg'); e.textContent = m; e.style.display = 'block'; setTimeout(() => e.style.display = 'none', 5000) }
function showSuccess(m) { const e = document.getElementById('successMsg'); e.textContent = m; e.style.display = 'block'; setTimeout(() => e.style.display = 'none', 5000) }

async function api(path, data) {
  const r = await fetch(path, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(data),
  });
  return r.json();
}

async function handleLogin(e) {
  e.preventDefault();
  const email = document.getElementById('loginEmail').value;
  const password = document.getElementById('loginPass').value;
  if (!email || !password) { showError('Please fill in all required fields.'); return; }
  const btn = document.getElementById('loginBtn');
  btn.disabled = true; btn.textContent = 'signing in...';
  const res = await api('/api/auth?action=login', { email, password });
  if (res.success) {
    localStorage.setItem('hostit_token', res.token);
    window.location.href = '/panel';
  } else {
    showError(res.error || 'Login failed.');
    btn.disabled = false; btn.textContent = 'sign in';
  }
}

async function handleSignup(e) {
  e.preventDefault();
  const first = document.getElementById('signupFirst').value;
  const last = document.getElementById('signupLast').value;
  const email = document.getElementById('signupEmail').value;
  const pass = document.getElementById('signupPass').value;
  const confirm = document.getElementById('signupConfirm').value;
  if (pass !== confirm) { showError('Passwords do not match.'); return; }
  if (pass.length < 8) { showError('Password must be at least 8 characters.'); return; }
  const btn = document.getElementById('signupBtn');
  btn.disabled = true; btn.textContent = 'creating account...';
  const res = await api('/api/auth?action=register', { email, password: pass, first_name: first, last_name: last });
  if (res.success) {
    localStorage.setItem('hostit_token', res.token);
    showSuccess('Account created. Redirecting...');
    setTimeout(() => window.location.href = '/panel', 800);
  } else {
    showError(res.error || 'Registration failed.');
    btn.disabled = false; btn.textContent = 'create account';
  }
}

function socialLogin(p) {
  showError('OAuth login for ' + p + ' is not configured yet. Please use email/password.');
}

function showToast(m) {
  const e = document.querySelector('.toast'); if (e) e.remove();
  const t = document.createElement('div'); t.className = 'toast'; t.textContent = m;
  Object.assign(t.style, {position:'fixed',bottom:'24px',left:'50%',transform:'translateX(-50%)',padding:'10px 20px',background:'var(--accent)',borderRadius:'12px',color:'#fff',fontSize:'13px',fontWeight:'500',zIndex:'9999',whiteSpace:'nowrap'});
  document.body.appendChild(t); setTimeout(() => { t.style.opacity = '0'; t.style.transition = 'opacity .3s'; setTimeout(() => t.remove(), 300) }, 3000);
}
</script>
</body>
</html>
