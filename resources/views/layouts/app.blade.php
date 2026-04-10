<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Team Task Manager</title>
  <link
    href="https://fonts.googleapis.com/css2?family=Syne:wght@400;500;600;700;800&family=DM+Mono:wght@300;400;500&display=swap"
    rel="stylesheet" />
  @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
  <div id="page-alert"
    class="pointer-events-none fixed right-6 top-6 via-zinc-600 hidden max-w-sm rounded-xl border px-4 py-3 text-sm shadow-2xl">
  </div>

  <div class="flex h-screen overflow-hidden">
    @include('components.sidebar')

    <!-- ── MAIN CONTENT ───────────────────────────── -->
    <main class="flex-1 flex flex-col overflow-hidden">
      @include('components.header')

      @yield('content')
    </main>
  </div>

  <!-- ── MODAL ──────────────────────────────────────── -->
  <div id="modal" class="fixed inset-0 z-50 hidden items-center justify-center modal-overlay">
    <div class="w-full max-w-md mx-4 rounded-2xl p-6 shadow-2xl"
      style="background:var(--color-panel); border:1px solid var(--color-border-light);" id="modal-box">
      <div class="flex justify-between items-center mb-6">
        <h2 class="text-lg font-700" id="modal-title">New Task</h2>
        <button onclick="closeModal()"
          class="w-8 h-8 rounded-lg flex items-center justify-center transition-all hover:bg-white/5"
          style="color:var(--color-text-muted);">✕</button>
      </div>

      <div class="space-y-4">
        <form action="{{ route('tasks.store') }}" method="post" id="task-form">
          @csrf
          <div id="task-form-alert" class="hidden rounded-lg border px-4 py-3 text-sm"></div>
          <!-- Title -->
          <div>
            <label class="mono text-xs uppercase tracking-wider block mb-1.5"
              style="color:var(--color-text-muted);">Task
              Title *</label>
            <input id="task-title" type="text" name="title" placeholder="What needs to be done?"
              class="tf-input w-full px-4 py-2.5 rounded-lg text-sm">
            <p id="title-error" class="mt-1 text-xs text-red-400 hidden"></p>
          </div>
          <!-- Description -->
          <div class="mt-2">
            <label class="mono text-xs uppercase tracking-wider block mb-1.5"
              style="color:var(--color-text-muted);">Description</label>
            <textarea id="task-desc" name="description" placeholder="Add details…" rows="3"
              class="tf-input w-full px-4 py-2.5 rounded-lg text-sm resize-none"></textarea>
            <p id="description-error" class="mt-1 text-xs text-red-400 hidden"></p>
          </div>
          <!-- Row: status + priority -->
          <div class="grid grid-cols-2 gap-3">
            <div class="mt-2">
              <label class="mono text-xs uppercase tracking-wider block mb-1.5"
                style="color:var(--color-text-muted);">Status</label>
              <select id="task-status" name="status"
                class="tf-input w-full px-3 py-2.5 rounded-lg text-sm cursor-pointer">
                <option value="pending">Pending</option>
                <option value="in_progress">In Progress</option>
                <option value="completed">Completed</option>
              </select>
              <p id="status-error" class="mt-1 text-xs text-red-400 hidden"></p>
            </div>
            <div class="mt-2">
              <label class="mono text-xs uppercase tracking-wider block mb-1.5"
                style="color:var(--color-text-muted);">Priority</label>
              <select id="task-priority" name="priority"
                class="tf-input w-full px-3 py-2.5 rounded-lg text-sm cursor-pointer">
                <option value="medium">Medium</option>
                <option value="high">High</option>
                <option value="low">Low</option>
              </select>
              <p id="priority-error" class="mt-1 text-xs text-red-400 hidden"></p>
            </div>
          </div>
          <!-- Assignee -->
          <div class="mt-2">
            <label class="mono text-xs uppercase tracking-wider block mb-1.5"
              style="color:var(--color-text-muted);">Assignee</label>
            <select id="task-assignee" name="assigned_users[]"
              class="tf-input w-full px-3 py-2.5 rounded-lg text-sm cursor-pointer">
              <option value="">All</option>
              @foreach ($users as $user)
                <option value="{{ $user->id }}">{{ $user->name }}</option>
              @endforeach
            </select>
            <p id="assigned_users-error" class="mt-1 text-xs text-red-400 hidden"></p>
          </div>
          <!-- Due date -->
          <div class="mt-2">
            <label class="mono text-xs uppercase tracking-wider block mb-1.5" style="color:var(--color-text-muted);">Due
              Date</label>
            <input id="task-due" type="date" name="due_date" class="tf-input w-full px-4 py-2.5 rounded-lg text-sm">
            <p id="due_date-error" class="mt-1 text-xs text-red-400 hidden"></p>
          </div>
      </div>

      <!-- Actions -->
      <div class="flex gap-3 mt-6">
        <button type="button" onclick="closeModal()"
          class="flex-1 px-4 py-2.5 rounded-lg text-sm font-600 transition-all hover:bg-white/5"
          style="border:1px solid var(--color-border); color:var(--color-text-muted);">
          Cancel
        </button>
        <button type="submit" class="btn-primary flex-1 px-4 py-2.5 rounded-lg text-sm" id="modal-save-btn">
          Create Task
        </button>
      </div>
      </form>
    </div>
  </div>

  <div id="edit-modal" class="fixed inset-0 z-50 hidden items-center justify-center modal-overlay">
    <div class="mx-4 w-full max-w-md rounded-2xl p-6 shadow-2xl"
      style="background:var(--color-panel); border:1px solid var(--color-border-light);">
      <div class="mb-6 flex items-center justify-between">
        <h2 class="text-lg font-700">Edit Task</h2>
        <button type="button" onclick="closeEditModal()"
          class="flex h-8 w-8 items-center justify-center rounded-lg transition-all hover:bg-white/5"
          style="color:var(--color-text-muted);">✕</button>
      </div>

      <div class="space-y-4">
        <form method="post" id="edit-task-form"
          data-update-url-template="{{ route('tasks.update', ['task' => '__TASK__']) }}">
          @csrf
          @method('PATCH')
          <input type="hidden" id="edit-task-id" name="task_id">
          <div id="edit-task-form-alert" class="hidden rounded-lg border px-4 py-3 text-sm"></div>

          <div>
            <label class="mono mb-1.5 block text-xs uppercase tracking-wider"
              style="color:var(--color-text-muted);">Task Title *</label>
            <input id="edit-task-title" type="text" name="title" placeholder="What needs to be done?"
              class="tf-input w-full rounded-lg px-4 py-2.5 text-sm">
            <p id="edit-title-error" class="mt-1 hidden text-xs text-red-400"></p>
          </div>

          <div class="mt-2">
            <label class="mono mb-1.5 block text-xs uppercase tracking-wider"
              style="color:var(--color-text-muted);">Description</label>
            <textarea id="edit-task-desc" name="description" placeholder="Add details…" rows="3"
              class="tf-input w-full resize-none rounded-lg px-4 py-2.5 text-sm"></textarea>
            <p id="edit-description-error" class="mt-1 hidden text-xs text-red-400"></p>
          </div>

          <div class="grid grid-cols-2 gap-3">
            <div class="mt-2">
              <label class="mono mb-1.5 block text-xs uppercase tracking-wider"
                style="color:var(--color-text-muted);">Status</label>
              <select id="edit-task-status" name="status"
                class="tf-input w-full cursor-pointer rounded-lg px-3 py-2.5 text-sm">
                <option value="pending">Pending</option>
                <option value="in_progress">In Progress</option>
                <option value="completed">Completed</option>
              </select>
              <p id="edit-status-error" class="mt-1 hidden text-xs text-red-400"></p>
            </div>

            <div class="mt-2">
              <label class="mono mb-1.5 block text-xs uppercase tracking-wider"
                style="color:var(--color-text-muted);">Priority</label>
              <select id="edit-task-priority" name="priority"
                class="tf-input w-full cursor-pointer rounded-lg px-3 py-2.5 text-sm">
                <option value="medium">Medium</option>
                <option value="high">High</option>
                <option value="low">Low</option>
              </select>
              <p id="edit-priority-error" class="mt-1 hidden text-xs text-red-400"></p>
            </div>
          </div>

          <div class="mt-2">
            <label class="mono mb-1.5 block text-xs uppercase tracking-wider"
              style="color:var(--color-text-muted);">Assignee</label>
            <select id="edit-task-assignee" name="assigned_users[]"
              class="tf-input w-full cursor-pointer rounded-lg px-3 py-2.5 text-sm">
              <option value="">All</option>
              @foreach ($users as $user)
                <option value="{{ $user->id }}">{{ $user->name }}</option>
              @endforeach
            </select>
            <p id="edit-assigned_users-error" class="mt-1 hidden text-xs text-red-400"></p>
          </div>

          <div class="mt-2">
            <label class="mono mb-1.5 block text-xs uppercase tracking-wider" style="color:var(--color-text-muted);">Due
              Date</label>
            <input id="edit-task-due" type="date" name="due_date"
              class="tf-input w-full rounded-lg px-4 py-2.5 text-sm">
            <p id="edit-due_date-error" class="mt-1 hidden text-xs text-red-400"></p>
          </div>

          <div class="mt-6 flex gap-3">
            <button type="button" onclick="closeEditModal()"
              class="flex-1 rounded-lg px-4 py-2.5 text-sm font-600 transition-all hover:bg-white/5"
              style="border:1px solid var(--color-border); color:var(--color-text-muted);">
              Cancel
            </button>
            <button type="submit" class="btn-primary flex-1 rounded-lg px-4 py-2.5 text-sm" id="edit-modal-save-btn">
              Update Task
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- ── DELETE CONFIRM MODAL ───────────────────────── -->
  <div id="delete-modal" class="fixed inset-0 z-50 hidden items-center justify-center modal-overlay"
    data-destroy-url-template="{{ route('tasks.destroy', ['task' => '__TASK__']) }}">
    <div class="w-full max-w-sm mx-4 rounded-2xl p-6 shadow-2xl"
      style="background:var(--color-panel); border:1px solid var(--color-border-light);">
      <div class="w-12 h-12 rounded-xl flex items-center justify-center mb-4"
        style="background:#ef444415; border:1px solid #ef444430;">
        <svg width="22" height="22" fill="none" stroke="#ef4444" stroke-width="2" viewBox="0 0 24 24">
          <polyline points="3 6 5 6 21 6" />
          <path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6M10 11v6M14 11v6M9 6V4h6v2" />
        </svg>
      </div>
      <h3 class="font-700 text-base mb-1">Delete Task?</h3>
      <p class="text-sm mb-6" style="color:var(--color-text-muted);">This action cannot be undone. The task will be
        permanently removed.</p>
      <div class="flex gap-3">
        <button type="button" onclick="closeDeleteModal()"
          class="flex-1 px-4 py-2.5 rounded-lg text-sm font-600 transition-all hover:bg-white/5"
          style="border:1px solid var(--color-border); color:var(--color-text-muted);">
          Cancel
        </button>
        <button type="button" onclick="confirmDelete()"
          class="flex-1 px-4 py-2.5 rounded-lg text-sm font-700 transition-all" style="background:#ef4444; color:white;"
          onmouseover="this.style.background='#dc2626'" onmouseout="this.style.background='#ef4444'">
          Delete
        </button>
      </div>
    </div>
  </div>

  <script>
    function openModal() {
      showModal(true);
    }

    function closeModal() {
      showModal(false);
    }

    function openEditModal(taskId) {
      window.dispatchEvent(new CustomEvent("task-edit:open", {
        detail: {
          taskId,
        },
      }));
    }

    function closeEditModal() {
      const modal = document.getElementById("edit-modal");
      modal.classList.add("hidden");
      modal.classList.remove("flex");
      document.getElementById("edit-task-form")?.reset();
      window.dispatchEvent(new CustomEvent("task-edit:closed"));
    }

    function openDeleteModal(taskId) {
      const modal = document.getElementById("delete-modal");
      modal.dataset.taskId = taskId;
      modal.classList.remove("hidden");
      modal.classList.add("flex");
    }

    function closeDeleteModal() {
      const modal = document.getElementById("delete-modal");
      modal.classList.add("hidden");
      modal.classList.remove("flex");
      delete modal.dataset.taskId;
    }

    function showModal(show) {
      const m = document.getElementById("modal");
      if (show) {
        m.classList.remove("hidden");
        m.classList.add("flex");
        window.dispatchEvent(new CustomEvent("task-modal:opened"));
        setTimeout(() => document.getElementById("task-title").focus(), 100);
      } else {
        m.classList.add("hidden");
        m.classList.remove("flex");
        document.getElementById("task-form")?.reset();
        window.dispatchEvent(new CustomEvent("task-modal:closed"));
      }
    }
  </script>
</body>

</html>
