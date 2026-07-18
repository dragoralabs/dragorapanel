// ── Plugin Manager ──
async function loadPlugins() {
  const r = await api('/panel/plugins');
  if (!r.success) { document.getElementById('pluginList').innerHTML = '<div class="empty-state">Failed to load plugins.</div>'; return; }
  const list = document.getElementById('pluginList');
  if (!r.plugins || r.plugins.length === 0) {
    list.innerHTML = '<div class="empty-state"><i class="fas fa-puzzle-piece" style="font-size:32px;opacity:.3;margin-bottom:10px;display:block"></i> No plugins installed. Upload a <code>.zip</code> package with a <code>plugin.json</code> manifest to get started.</div>';
    return;
  }
  list.innerHTML = r.plugins.map(p => {
    const h = p.hooks || {};
    const hasConfig = h.config_page;
    return '<div class="plugin-card' + (p.enabled ? '' : ' disabled') + '">' +
      '<div class="plugin-icon"><i class="fas ' + (p.icon || 'fa-plug') + '"></i></div>' +
      '<div class="plugin-info"><strong>' + escHtml(p.name) + '</strong> <span class="plugin-ver">v' + escHtml(p.version) + '</span>' +
      (p.description ? '<div class="plugin-desc">' + escHtml(p.description) + '</div>' : '') +
      (p.author ? '<div class="plugin-author">by ' + escHtml(p.author) + '</div>' : '') +
      '</div>' +
      '<div class="plugin-actions">' +
      (hasConfig ? '<button class="power-btn restart" style="padding:6px 12px;font-size:11px" onclick="showPluginConfig(\'' + p.unique_id + '\')"><i class="fas fa-cog"></i></button> ' : '') +
      '<button class="power-btn ' + (p.enabled ? 'stop' : 'start') + '" style="padding:6px 12px;font-size:11px" onclick="togglePlugin(\'' + p.unique_id + '\')">' +
      (p.enabled ? '<i class="fas fa-pause"></i> Disable' : '<i class="fas fa-play"></i> Enable') + '</button> ' +
      '<button class="power-btn restart" style="padding:6px 12px;font-size:11px" onclick="deletePlugin(\'' + p.unique_id + '\')"><i class="fas fa-trash"></i></button>' +
      '</div></div>';
  }).join('');
}

async function uploadPlugin(input) {
  const file = input.files[0];
  if (!file) return;
  const fd = new FormData();
  fd.append('plugin', file);
  try {
    const r = await fetch('/api/panel/plugins/upload', {
      method: 'POST',
      headers: { 'Authorization': 'Bearer ' + localStorage.getItem(TOKEN_KEY) },
      body: fd
    });
    const data = await r.json();
    if (data.success) { showToast('Plugin "' + data.plugin.name + '" installed!'); loadPlugins(); loadActiveHooks(); }
    else showToast(data.error || 'Upload failed', true);
  } catch (e) { showToast('Upload failed', true); }
  input.value = '';
}

async function togglePlugin(id) {
  const r = await api('/panel/plugins/' + id + '/toggle', { method: 'POST' });
  if (r.success) { showToast('Toggled'); loadPlugins(); loadActiveHooks(); }
  else showToast(r.error || 'Failed', true);
}

async function deletePlugin(id) {
  if (!confirm('Uninstall this plugin?')) return;
  const r = await api('/panel/plugins/' + id, { method: 'DELETE' });
  if (r.success) { showToast('Plugin uninstalled.'); loadPlugins(); loadActiveHooks(); }
  else showToast(r.error || 'Failed', true);
}

// ── Active Hooks Loader ──
let loadedPluginCss = {};
let loadedPluginJs = {};

async function loadActiveHooks() {
  const r = await api('/panel/plugins/hooks/active');
  if (!r.success || !r.hooks) return;
  const hooks = r.hooks;

  // Load CSS
  if (hooks.css) {
    hooks.css.forEach(item => {
      const url = item.url || item;
      if (loadedPluginCss[url]) return;
      loadedPluginCss[url] = true;
      const link = document.createElement('link');
      link.rel = 'stylesheet';
      link.href = url;
      document.head.appendChild(link);
    });
  }

  // Load JS
  if (hooks.js) {
    hooks.js.forEach(item => {
      const url = item.url || item;
      if (loadedPluginJs[url]) return;
      loadedPluginJs[url] = true;
      const script = document.createElement('script');
      script.src = url;
      script.defer = true;
      document.body.appendChild(script);
    });
  }

  // Register admin tabs
  if (hooks.admin_tabs) {
    const adminTabs = document.querySelector('.admin-sub-tabs');
    const adminView = document.getElementById('view-admin');
    hooks.admin_tabs.forEach(tab => {
      if (document.getElementById('atab-' + tab.id)) return;
      const btn = document.createElement('button');
      btn.className = 'admin-sub-tab';
      btn.dataset.atab = tab.id;
      btn.innerHTML = '<i class="fas ' + (tab.icon || 'fa-puzzle-piece') + '"></i> ' + tab.label;
      adminTabs.appendChild(btn);

      const content = document.createElement('div');
      content.className = 'admin-sub-content';
      content.id = 'atab-' + tab.id;
      content.innerHTML = tab.html || '<div class="empty-state">Plugin tab loaded.</div>';
      adminView.appendChild(content);

      btn.dataset.atab = tab.id;
      btn.addEventListener('click', function() {
        document.querySelectorAll('.admin-sub-tab').forEach(t => t.classList.remove('active'));
        this.classList.add('active');
        document.querySelectorAll('.admin-sub-content').forEach(c => c.classList.remove('active'));
        const ct = document.getElementById('atab-' + this.dataset.atab);
        if (ct) ct.classList.add('active');
        document.getElementById('nodeDetailNav').style.display = 'none';
        history.pushState({ viewName: 'admin', sub: this.dataset.atab }, '', '/panel/admin/' + this.dataset.atab);
        document.getElementById('pageTitle').textContent = 'Admin - ' + this.dataset.atab;
      });
    });
  }

  // Register sidebar items
  if (hooks.sidebar_items) {
    const sidebar = document.querySelector('.sidebar-nav');
    hooks.sidebar_items.forEach(item => {
      if (document.querySelector('[data-view="' + item.id + '"]')) return;
      const nav = document.createElement('button');
      nav.className = 'nav-item';
      nav.dataset.view = item.id;
      nav.onclick = function() {
        navigate(item.id);
      };
      nav.innerHTML = '<i class="fas ' + (item.icon || 'fa-puzzle-piece') + '"></i> ' + item.label;
      sidebar.appendChild(nav);

    });
  }
}

// ── Developer Docs ──
async function loadDevelopersDocs() {
  const container = document.getElementById('developerDocs');
  const doc = `{
  "plugin.json Reference": {
    "required": ["name", "unique_id", "version"],
    "optional": ["description", "author", "license", "icon", "hooks"],
    "description": "The manifest file defines your plugin metadata and hooks into the panel."
  },
  "Extension Hooks": {
    "css": {
      "type": "array",
      "items": "string (filename relative to plugin root)",
      "description": "CSS files injected into the panel head"
    },
    "js": {
      "type": "array",
      "items": "string (filename relative to plugin root)",
      "description": "JavaScript files injected into the panel body"
    },
    "admin_tabs": {
      "type": "array",
      "items": {
        "id": "string (unique)",
        "label": "string",
        "icon": "string (Font Awesome class)",
        "html": "string (HTML content of the tab)"
      },
      "description": "Custom admin sub-tabs shown in the admin panel"
    },
    "sidebar_items": {
      "type": "array",
      "items": {
        "id": "string (unique view name)",
        "label": "string",
        "icon": "string (Font Awesome class)",
        "view": "string (content HTML)"
      },
      "description": "New navigation items in the sidebar"
    },
    "server_tabs": {
      "type": "array",
      "items": {
        "id": "string",
        "label": "string",
        "icon": "string",
        "html": "string"
      },
      "description": "Additional tabs in the server detail view"
    },
    "config_page": {
      "type": "boolean",
      "description": "If true, a config button appears in the plugin manager"
    }
  },
  "Example Plugin": {
    "structure": {
      "my-plugin.zip": ["plugin.json", "style.css", "script.js"]
    },
    "manifest": {
      "name": "Custom Theme",
      "unique_id": "my_custom_theme",
      "version": "1.0.0",
      "description": "A beautiful custom theme for the panel",
      "author": "You",
      "icon": "fa-palette",
      "hooks": {
        "css": ["theme.css"],
        "js": ["theme.js"]
      }
    }
  }
}`;
  container.innerHTML = `<div id="devDocsApp"></div>`;
  // Simple markdown-like renderer
  renderDevDocs(container, JSON.parse(doc));
}

function renderDevDocs(container, data) {
  let html = '<div class="dev-docs">';
  Object.keys(data).forEach(section => {
    const s = data[section];
    html += '<h2><i class="fas fa-book" style="color:var(--accent);font-size:16px"></i> ' + section + '</h2>';

    if (section === 'plugin.json Reference') {
      html += '<p>' + s.description + '</p>';
      html += '<div style="display:flex;gap:12px;flex-wrap:wrap;margin:10px 0">';
      if (s.required) html += '<span class="pill required">Required</span> ' + s.required.join(', ');
      if (s.optional) html += '<span class="pill optional">Optional</span> ' + s.optional.join(', ');
      html += '</div>';
      html += '<pre>{';
      html += '\n  <span style="color:#fbbf24">"name"</span>:      <span style="color:#34d399">"My Plugin"</span>,';
      html += '\n  <span style="color:#fbbf24">"unique_id"</span>: <span style="color:#34d399">"author_myplugin"</span>,';
      html += '\n  <span style="color:#fbbf24">"version"</span>:   <span style="color:#34d399">"1.0.0"</span>,';
      html += '\n  <span style="color:#fbbf24">"description"</span>: <span style="color:#34d399">"Does amazing things."</span>,';
      html += '\n  <span style="color:#fbbf24">"author"</span>:    <span style="color:#34d399">"You"</span>,';
      html += '\n  <span style="color:#fbbf24">"icon"</span>:      <span style="color:#34d399">"fa-star"</span>,';
      html += '\n  <span style="color:#fbbf24">"hooks"</span>:     { <span style="color:#9b98a8">/* see below */</span> }';
      html += '\n}</pre>';
    }

    if (section === 'Extension Hooks') {
      Object.keys(s).forEach(hookName => {
        const h = s[hookName];
        html += '<div class="hook-card">';
        html += '<h4><code>' + hookName + '</code> <span style="font-weight:400;color:var(--text3);font-size:12px">' + (Array.isArray(h) ? 'array' : typeof h) + '</span></h4>';
        html += '<p>' + (typeof h === 'string' ? h : h.description || '') + '</p>';
        if (h.items) {
          html += '<pre style="margin:6px 0 0;font-size:11px">' + JSON.stringify(h.items, null, 2).replace(/</g,'&lt;') + '</pre>';
        }
        html += '</div>';
      });
    }

    if (section === 'Example Plugin') {
      html += '<div class="example-plugin">';
      html += '<div class="file-label"><i class="fas fa-file-archive"></i> my-plugin.zip</div>';
      html += '<pre style="margin:0"><span style="color:var(--text3)">my-plugin.zip</span>';
      html += '\n  ├── <span style="color:var(--accent)">plugin.json</span>';
      html += '\n  ├── <span style="color:var(--green)">style.css</span>';
      html += '\n  ├── <span style="color:var(--green)">script.js</span>';
      html += '\n  └── icon.png</pre>';
      html += '</div>';
      html += '<div class="example-plugin">';
      html += '<div class="file-label"><i class="fas fa-file-code"></i> plugin.json</div>';
      html += '<pre style="margin:0">' + JSON.stringify(s.manifest, null, 2).replace(/</g,'&lt;') + '</pre>';
      html += '</div>';
    }
  });
  html += '</div>';
  container.innerHTML = html;
}


