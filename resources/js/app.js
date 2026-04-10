import './bootstrap';

const fieldNames = ['title', 'description', 'status', 'priority', 'assigned_users', 'due_date'];

const createForm = document.getElementById('task-form');
const editForm = document.getElementById('edit-task-form');
const taskList = document.getElementById('task-list');
const emptyState = document.getElementById('empty-state');
const pageAlert = document.getElementById('page-alert');
const deleteModal = document.getElementById('delete-modal');

const alertStyles = {
    error: {
        borderColor: '#ef444430',
        background: '#ef444415',
        color: '#fca5a5',
    },
    success: {
        borderColor: '#22c55e30',
        background: '#22c55e15',
        color: '#86efac',
    },
};

const setElementAlert = (element, message, type = 'error') => {
    if (! element) {
        return;
    }

    const selectedStyle = alertStyles[type] ?? alertStyles.error;

    element.textContent = message;
    element.classList.remove('hidden');
    element.style.borderColor = selectedStyle.borderColor;
    element.style.background = selectedStyle.background;
    element.style.color = selectedStyle.color;
};

const clearElementAlert = (element) => {
    if (! element) {
        return;
    }

    element.textContent = '';
    element.classList.add('hidden');
};

let pageAlertTimeout = null;

const showPageAlert = (message, type = 'success') => {
    if (! pageAlert) {
        return;
    }

    if (pageAlertTimeout) {
        window.clearTimeout(pageAlertTimeout);
    }

    setElementAlert(pageAlert, message, type);
    pageAlert.classList.remove('translate-y-2', 'opacity-0');
    pageAlert.classList.add('opacity-100');

    pageAlertTimeout = window.setTimeout(() => {
        clearElementAlert(pageAlert);
    }, 3000);
};

const updateEmptyState = () => {
    if (! taskList || ! emptyState) {
        return;
    }

    const hasTasks = taskList.children.length > 0;
    taskList.classList.toggle('hidden', ! hasTasks);
    emptyState.classList.toggle('hidden', hasTasks);
    emptyState.classList.toggle('flex', ! hasTasks);
};

const getCard = (taskId) => document.getElementById(`task-card-${taskId}`);

const getTaskData = (taskId) => {
    const card = getCard(taskId);

    if (! card?.dataset.task) {
        return null;
    }

    try {
        return JSON.parse(card.dataset.task);
    } catch {
        return null;
    }
};

const replaceTaskCard = (taskId, html) => {
    const currentCard = getCard(taskId);

    if (currentCard) {
        currentCard.outerHTML = html;
    }

    updateEmptyState();
};

const prependTaskCard = (html) => {
    if (! taskList) {
        return;
    }

    taskList.insertAdjacentHTML('afterbegin', html);
    updateEmptyState();
};

const removeTaskCard = (taskId) => {
    getCard(taskId)?.remove();
    updateEmptyState();
};

const setFieldError = (form, prefix, fieldName, message = '') => {
    const errorElement = document.getElementById(`${prefix}${fieldName}-error`);
    const inputElement = form?.querySelector(`[name="${fieldName}"], [name="${fieldName}[]"]`);

    if (errorElement) {
        errorElement.textContent = message;
        errorElement.classList.toggle('hidden', message === '');
    }

    if (inputElement) {
        inputElement.style.borderColor = message === '' ? '' : '#ef4444';
    }
};

const clearValidationErrors = (form, prefix = '') => {
    fieldNames.forEach((fieldName) => {
        setFieldError(form, prefix, fieldName);
    });
};

const showValidationErrors = (form, prefix, errors) => {
    Object.entries(errors).forEach(([fieldName, messages]) => {
        const normalizedFieldName = fieldName.replace(/\.\d+$/, '');
        setFieldError(form, prefix, normalizedFieldName, messages[0] ?? '');
    });
};

const normalizeAssignedUsers = (formData) => {
    if (! formData.get('assigned_users[]')) {
        formData.delete('assigned_users[]');
    }

    return formData;
};

const handleValidationFailure = (form, prefix, alertElement, errors) => {
    setElementAlert(alertElement, 'Please fix the validation errors and try again.');
    showValidationErrors(form, prefix, errors ?? {});
};

const setSubmittingState = (button, isSubmitting, idleText, busyText) => {
    if (! button) {
        return;
    }

    button.disabled = isSubmitting;
    button.textContent = isSubmitting ? busyText : idleText;
};

if (createForm) {
    const createAlert = document.getElementById('task-form-alert');
    const createButton = document.getElementById('modal-save-btn');

    window.addEventListener('task-modal:opened', () => {
        clearElementAlert(createAlert);
        clearValidationErrors(createForm);
    });

    window.addEventListener('task-modal:closed', () => {
        clearElementAlert(createAlert);
        clearValidationErrors(createForm);
    });

    createForm.addEventListener('submit', async (event) => {
        event.preventDefault();

        clearElementAlert(createAlert);
        clearValidationErrors(createForm);
        setSubmittingState(createButton, true, 'Create Task', 'Saving...');

        try {
            const response = await window.axios.post(createForm.action, normalizeAssignedUsers(new FormData(createForm)), {
                headers: {
                    Accept: 'application/json',
                },
            });

            prependTaskCard(response.data.task?.html ?? '');
            setElementAlert(createAlert, response.data.message ?? 'Task created successfully.', 'success');
            showPageAlert(response.data.message ?? 'Task created successfully.');
            createForm.reset();

            window.setTimeout(() => {
                if (typeof window.closeModal === 'function') {
                    window.closeModal();
                }
            }, 700);
        } catch (error) {
            if (error.response?.status === 422) {
                handleValidationFailure(createForm, '', createAlert, error.response.data.errors);
            } else {
                setElementAlert(createAlert, error.response?.data?.message ?? 'Something went wrong while creating the task.');
            }
        } finally {
            setSubmittingState(createButton, false, 'Create Task', 'Saving...');
        }
    });
}

if (editForm) {
    const editAlert = document.getElementById('edit-task-form-alert');
    const editButton = document.getElementById('edit-modal-save-btn');
    const editModal = document.getElementById('edit-modal');
    const updateUrlTemplate = editForm.dataset.updateUrlTemplate ?? '';

    const showEditModal = () => {
        editModal?.classList.remove('hidden');
        editModal?.classList.add('flex');

        window.setTimeout(() => {
            document.getElementById('edit-task-title')?.focus();
        }, 100);
    };

    window.addEventListener('task-edit:open', (event) => {
        clearElementAlert(editAlert);
        clearValidationErrors(editForm, 'edit-');

        const taskId = event.detail?.taskId;
        const task = getTaskData(taskId);

        if (! task) {
            showPageAlert('Unable to load that task for editing.', 'error');
            return;
        }

        editForm.action = updateUrlTemplate.replace('__TASK__', String(task.id));
        document.getElementById('edit-task-id').value = task.id;
        document.getElementById('edit-task-title').value = task.title ?? '';
        document.getElementById('edit-task-desc').value = task.description ?? '';
        document.getElementById('edit-task-status').value = task.status ?? 'pending';
        document.getElementById('edit-task-priority').value = task.priority ?? 'medium';
        document.getElementById('edit-task-due').value = task.due_date ?? '';
        document.getElementById('edit-task-assignee').value = task.assigned_users?.[0] ?? '';

        showEditModal();
    });

    window.addEventListener('task-edit:closed', () => {
        clearElementAlert(editAlert);
        clearValidationErrors(editForm, 'edit-');
    });

    editForm.addEventListener('submit', async (event) => {
        event.preventDefault();

        clearElementAlert(editAlert);
        clearValidationErrors(editForm, 'edit-');
        setSubmittingState(editButton, true, 'Update Task', 'Updating...');

        try {
            const response = await window.axios.post(editForm.action, normalizeAssignedUsers(new FormData(editForm)), {
                headers: {
                    Accept: 'application/json',
                },
            });

            replaceTaskCard(response.data.task?.id, response.data.task?.html ?? '');
            setElementAlert(editAlert, response.data.message ?? 'Task updated successfully.', 'success');
            showPageAlert(response.data.message ?? 'Task updated successfully.');

            window.setTimeout(() => {
                if (typeof window.closeEditModal === 'function') {
                    window.closeEditModal();
                }
            }, 700);
        } catch (error) {
            if (error.response?.status === 422) {
                handleValidationFailure(editForm, 'edit-', editAlert, error.response.data.errors);
            } else {
                setElementAlert(editAlert, error.response?.data?.message ?? 'Something went wrong while updating the task.');
            }
        } finally {
            setSubmittingState(editButton, false, 'Update Task', 'Updating...');
        }
    });
}

window.changeStatus = async (taskId, status, element = null) => {
    const task = getTaskData(taskId);

    if (! task) {
        showPageAlert('Unable to update that task right now.', 'error');
        return false;
    }

    try {
        if (element) {
            element.disabled = true;
        }

        const response = await window.axios.patch(`/tasks/${taskId}`, {
            title: task.title,
            description: task.description,
            status,
            priority: task.priority,
            due_date: task.due_date,
            assigned_users: task.assigned_users ?? [],
        }, {
            headers: {
                Accept: 'application/json',
            },
        });

        replaceTaskCard(taskId, response.data.task?.html ?? '');
        showPageAlert(response.data.message ?? 'Task updated successfully.');
        return true;
    } catch (error) {
        if (element) {
            element.value = task.status;
        }

        showPageAlert(error.response?.data?.message ?? 'Unable to update the task status.', 'error');
        return false;
    } finally {
        if (element) {
            element.disabled = false;
        }
    }
};

window.toggleComplete = async (taskId, isCompleted, element = null) => {
    const task = getTaskData(taskId);
    const wasUpdated = await window.changeStatus(taskId, isCompleted ? 'completed' : 'pending', element);

    if (! wasUpdated && element && task) {
        element.checked = task.status === 'completed';
    }
};

window.confirmDelete = async () => {
    const taskId = deleteModal?.dataset.taskId;

    if (! taskId) {
        return;
    }

    try {
        const destroyUrl = (deleteModal?.dataset.destroyUrlTemplate ?? '').replace('__TASK__', taskId);

        const response = await window.axios.delete(destroyUrl, {
            headers: {
                Accept: 'application/json',
            },
        });

        removeTaskCard(response.data.task?.id ?? taskId);
        showPageAlert(response.data.message ?? 'Task deleted successfully.');

        if (typeof window.closeDeleteModal === 'function') {
            window.closeDeleteModal();
        }
    } catch (error) {
        showPageAlert(error.response?.data?.message ?? 'Unable to delete the task.', 'error');
    }
};

updateEmptyState();
