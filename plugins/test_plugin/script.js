// ── Explation Tab Runtime ──
document.addEventListener('DOMContentLoaded', () => {
  // Watch for the explation tab being shown
  const observer = new MutationObserver(() => {
    const app = document.getElementById('explationApp');
    if (app && app.closest('.admin-sub-content.active')) {
      renderExplation(app);
    }
  });
  observer.observe(document.getElementById('view-admin') || document.body, {
    childList: true, subtree: true, attributes: true, attributeFilter: ['class']
  });
});

function renderExplation(container) {
  container.innerHTML = `
    <div class="ex-hero">
      <h2><i class="fas fa-lightbulb"></i> Explation</h2>
      <p>A demo admin tab created by the Dragora Panel Plugin (DPP) system. This proves plugins can extend any part of the panel.</p>
    </div>

    <div class="ex-grid">
      <div class="ex-card">
        <span class="ex-icon"><i class="fas fa-cube"></i></span>
        <h3>Plugin Architecture</h3>
        <p>Plugins are packaged as <strong>.dpp</strong> files — ZIP archives with a <code>plugin.json</code> manifest defining hooks.</p>
        <span class="ex-tag info">DPP v1</span>
      </div>
      <div class="ex-card">
        <span class="ex-icon"><i class="fas fa-puzzle-piece"></i></span>
        <h3>Extension Points</h3>
        <p>Admin tabs, sidebar items, CSS injection, JS injection, server tabs, and config pages.</p>
        <span class="ex-tag success">6 hooks</span>
      </div>
      <div class="ex-card">
        <span class="ex-icon"><i class="fas fa-code"></i></span>
        <h3>Developer Friendly</h3>
        <p>Write standard HTML/CSS/JS. Package as a single <code>.dpp</code> file. Upload and enable in seconds.</p>
        <span class="ex-tag warning">No build step</span>
      </div>
    </div>

    <div class="ex-section">
      <h3><i class="fas fa-info-circle" style="color:var(--accent)"></i> Plugin Info</h3>
      <div class="ex-info-row"><span class="ex-label">Name</span> <span>Explation Tab</span></div>
      <div class="ex-info-row"><span class="ex-label">Version</span> <span>1.0.0</span></div>
      <div class="ex-info-row"><span class="ex-label">Unique ID</span> <span style="font-family:monospace;font-size:12px;color:var(--accent)">explanet_tab</span></div>
      <div class="ex-info-row"><span class="ex-label">Format</span> <span class="ex-badge dpp"><i class="fas fa-file-archive"></i> .dpp</span></div>
      <div class="ex-info-row"><span class="ex-label">Status</span> <span class="ex-badge plugin"><i class="fas fa-check-circle"></i> Active</span></div>
    </div>
  `;
}

// Re-render when tab becomes active
setTimeout(() => {
  const app = document.getElementById('explationApp');
  const panel = app && app.closest('.admin-sub-content');
  if (panel && panel.classList.contains('active')) {
    renderExplation(app);
  }
}, 100);
