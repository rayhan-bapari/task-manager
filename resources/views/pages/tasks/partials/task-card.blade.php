@php
  $isCompleted = $task->status === 'completed';
  $statusBadgeClass = match ($task->status) {
      'completed' => 'badge-done',
      'in_progress' => 'badge-progress',
      default => 'badge-pending',
  };
  $statusLabel = match ($task->status) {
      'in_progress' => 'In Progress',
      'completed' => 'Completed',
      default => 'Pending',
  };
  $dotClass = match ($task->priority) {
      'high' => 'dot-high',
      'medium' => 'dot-medium',
      default => 'dot-low',
  };
  $assigneeNames = collect($task->assigned_users ?? [])
      ->map(fn ($userId) => $users->get((int) $userId)?->name)
      ->filter()
      ->values();
  $assigneeLabel = $assigneeNames->implode(', ');
  $dueDateLabel = $task->due_date?->format('d M Y');
  $isOverdue = $task->due_date?->isPast() && ! $isCompleted;
  $taskPayload = [
      'id' => $task->id,
      'title' => $task->title,
      'description' => $task->description,
      'status' => $task->status,
      'priority' => $task->priority,
      'due_date' => $task->due_date?->format('Y-m-d'),
      'assigned_users' => $task->assigned_users ?? [],
  ];
@endphp

<div class="task-card rounded-xl p-4 {{ $isCompleted ? 'completed-card' : '' }}" id="task-card-{{ $task->id }}"
  data-task='@json($taskPayload)'>
  <div class="flex items-start gap-3">
    <input type="checkbox" class="task-check mt-0.5" {{ $isCompleted ? 'checked' : '' }}
      onchange="toggleComplete('{{ $task->id }}', this.checked, this)" />
    <div class="min-w-0 flex-1">
      <div class="mb-1.5 flex items-start justify-between gap-3">
        <h3 class="text-sm leading-snug font-semibold {{ $isCompleted ? 'line-through' : '' }}"
          style="color:{{ $isCompleted ? 'var(--color-text-muted)' : 'var(--color-text)' }};">
          {{ $task->title }}
        </h3>
        <div class="flex flex-shrink-0 items-center gap-1.5">
          <button onclick="openEditModal('{{ $task->id }}')"
            class="flex h-7 w-7 items-center justify-center rounded-md transition-all hover:bg-white/8"
            style="color:var(--color-text-muted);" title="Edit">
            <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7" />
              <path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z" />
            </svg>
          </button>
          <button onclick="openDeleteModal('{{ $task->id }}')"
            class="flex h-7 w-7 items-center justify-center rounded-md transition-all hover:bg-red-500/10"
            style="color:var(--color-text-dim);" title="Delete">
            <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <polyline points="3 6 5 6 21 6" />
              <path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6M10 11v6M14 11v6" />
            </svg>
          </button>
        </div>
      </div>

      @if ($task->description)
        <p class="mb-2.5 text-xs leading-relaxed" style="color:var(--color-text-muted);">
          {{ $task->description }}
        </p>
      @endif

      <div class="mt-2 flex flex-wrap items-center gap-2">
        <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-xs font-medium {{ $statusBadgeClass }}">
          <span class="h-1.5 w-1.5 rounded-full {{ $dotClass }}"></span>{{ $statusLabel }}
        </span>

        <div class="flex items-center gap-1.5">
          <span class="h-1.5 w-1.5 rounded-full {{ $dotClass }}"></span>
          <span class="text-xs capitalize" style="color:var(--color-text-muted);">{{ $task->priority }}</span>
        </div>

        @if ($assigneeLabel !== '')
          <span class="mono rounded-md px-2 py-0.5 text-xs"
            style="background:var(--color-ink-soft); color:var(--color-text-muted); border:1px solid var(--color-border);">
            {{ $assigneeLabel }}
          </span>
        @endif

        @if ($dueDateLabel)
          <span class="mono text-xs" style="color:{{ $isOverdue ? '#f87171' : 'var(--color-text-dim)' }};">
            {{ $isOverdue ? 'Overdue: ' : '' }}{{ $dueDateLabel }}
          </span>
        @endif

        <select onchange="changeStatus('{{ $task->id }}', this.value, this)"
          class="ml-auto cursor-pointer rounded-md px-2 py-0.5 text-xs"
          style="background:var(--color-ink-soft); border:1px solid var(--color-border); color:var(--color-text-muted); font-family:var(--font-display); outline:none;">
          <option value="pending" @selected($task->status === 'pending')>Pending</option>
          <option value="in_progress" @selected($task->status === 'in_progress')>In Progress</option>
          <option value="completed" @selected($task->status === 'completed')>Completed</option>
        </select>
      </div>
    </div>
  </div>
</div>
