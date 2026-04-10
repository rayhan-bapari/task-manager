@extends('layouts.app')

@section('content')
  <!-- Task List -->
  <div class="flex-1 overflow-y-auto px-8 py-6">
    <div class="mx-auto max-w-3xl space-y-4">
      <div id="task-list" class="space-y-3 {{ $tasks->isEmpty() ? 'hidden' : '' }}">
        @foreach ($tasks as $task)
          @include('pages.tasks.partials.task-card', ['task' => $task, 'users' => $users])
        @endforeach
      </div>

      <div id="empty-state"
        class="{{ $tasks->isEmpty() ? 'flex' : 'hidden' }} empty-state mx-auto max-w-sm flex-col items-center justify-center py-24 text-center">
        <div class="mb-4 flex h-16 w-16 items-center justify-center rounded-2xl"
        style="background:var(--color-panel); border:1px solid var(--color-border);">
          <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"
            style="color:var(--color-text-dim);">
            <rect x="3" y="3" width="18" height="18" rx="2" />
            <path d="M9 12h6M12 9v6" />
          </svg>
        </div>
        <h3 class="mb-2 text-lg font-700" style="color:var(--color-text);">No tasks here</h3>
        <p class="mb-5 text-sm leading-relaxed" style="color:var(--color-text-muted);">Create your first task to start
          organizing your team's work.</p>
        <button onclick="openModal()" class="btn-primary rounded-lg px-5 py-2.5 text-sm">+ Add Task</button>
      </div>

      @if ($tasks->hasPages())
        <div class="rounded-xl border px-4 py-3" style="background:var(--color-panel); border-color:var(--color-border);">
          {{ $tasks->onEachSide(1)->links() }}
        </div>
      @endif
    </div>
  </div>
@endsection
