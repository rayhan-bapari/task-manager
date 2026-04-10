<!-- ── SIDEBAR ────────────────────────────────── -->
<aside class="w-64 shrink-0 flex" style="background:var(--color-surface); border-right:1px solid var(--color-border);">
  <div class="sidebar-accent shrink-0"></div>
  <div class="flex-1 flex flex-col p-6 overflow-y-auto">

    <!-- Logo -->
    <div class="mb-10">
      <div class="flex items-center gap-2 mb-1">
        <div class="w-7 h-7 rounded-md flex items-center justify-center pulse-amber"
          style="background:var(--color-amber);">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#0d0d0d" stroke-width="2.5">
            <polyline points="9 11 12 14 22 4" />
            <path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11" />
          </svg>
        </div>
        <span class="text-lg font-800 tracking-tight" style="color:var(--color-text);">TaskFlow</span>
      </div>
      <p class="mono text-xs" style="color:var(--color-text-muted);">Team Task Manager</p>
    </div>

    <!-- Stats -->
    <div class="space-y-3 mb-8">
      <p class="mono text-xs uppercase tracking-widest mb-4" style="color:var(--color-text-dim);">Overview</p>
      <div class="stat-card rounded-lg p-3">
        <div class="flex justify-between items-center mb-2">
          <span class="text-xs" style="color:var(--color-text-muted);">Total Tasks</span>
          <span class="mono font-500 text-sm" id="stat-total" style="color:var(--color-amber);">0</span>
        </div>
        <div class="flex justify-between items-center mb-2">
          <span class="text-xs" style="color:var(--color-text-muted);">In Progress</span>
          <span class="mono font-500 text-sm" id="stat-progress" style="color:#60a5fa;">0</span>
        </div>
        <div class="flex justify-between items-center">
          <span class="text-xs" style="color:var(--color-text-muted);">Completed</span>
          <span class="mono font-500 text-sm" id="stat-done" style="color:#4ade80;">0</span>
        </div>
      </div>

      <!-- Progress bar -->
      <div>
        <div class="flex justify-between mb-1.5">
          <span class="text-xs" style="color:var(--color-text-muted);">Progress</span>
          <span class="mono text-xs" id="stat-pct" style="color:var(--color-amber);">0%</span>
        </div>
        <div class="h-1.5 rounded-full overflow-hidden" style="background:var(--color-border);">
          <div class="progress-bar-fill h-full rounded-full" id="progress-bar" style="width:0%"></div>
        </div>
      </div>
    </div>
  </div>
</aside>
