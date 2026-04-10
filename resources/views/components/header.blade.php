<!-- ── HEADER ────────────────────────────────── -->
<header class="shrink-0 flex items-center justify-between px-8 py-5"
  style="border-bottom:1px solid var(--color-border); background:var(--color-surface);">
  <div>
    <h1 class="text-2xl font-800 tracking-tight" style="color:var(--color-text);">Task Board</h1>
    <p class="mono text-xs mt-0.5" id="date-label" style="color:var(--color-text-muted);"></p>
  </div>
  <div class="flex items-center gap-3">
    <!-- Add Task -->
    <button onclick="openModal()" class="btn-primary px-4 py-2 rounded-lg text-sm flex items-center gap-2">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
        <line x1="12" y1="5" x2="12" y2="19" />
        <line x1="5" y1="12" x2="19" y2="12" />
      </svg>
      New Task
    </button>
  </div>
</header>
