<template>
    <div class="confirm-modal-overlay" @click.self="cancel">
        <div
            class="confirm-modal"
            :class="{
                'confirm-modal-danger': type === 'danger',
                'confirm-modal-warning': type === 'warning',
                'confirm-modal-success': type === 'success',
            }"
        >
            <div class="confirm-modal-header">
                <div class="confirm-modal-icon">
                    <svg
                        v-if="type === 'danger'"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"
                        />
                    </svg>
                    <svg
                        v-else-if="type === 'warning'"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"
                        />
                    </svg>
                    <svg
                        v-else-if="type === 'success'"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                        />
                    </svg>
                    <svg
                        v-else
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                        />
                    </svg>
                </div>
                <h3 class="confirm-modal-title">{{ title }}</h3>
            </div>

            <div class="confirm-modal-body">
                <p>{{ message }}</p>
                <p v-if="details" class="confirm-modal-details">
                    {{ details }}
                </p>
            </div>

            <div class="confirm-modal-footer">
                <button
                    type="button"
                    @click="cancel"
                    class="confirm-modal-btn confirm-modal-btn-secondary"
                    :disabled="loading"
                >
                    {{ cancelText }}
                </button>
                <button
                    type="button"
                    @click="confirm"
                    class="confirm-modal-btn confirm-modal-btn-primary"
                    :class="{
                        'btn-danger': type === 'danger',
                        'btn-warning': type === 'warning',
                        'btn-success': type === 'success',
                    }"
                    :disabled="loading"
                >
                    <svg
                        v-if="loading"
                        class="spinner"
                        viewBox="0 0 24 24"
                        fill="none"
                    >
                        <circle
                            cx="12"
                            cy="12"
                            r="10"
                            stroke="currentColor"
                            stroke-width="3"
                            stroke-dasharray="31.4"
                            stroke-dashoffset="10"
                        />
                    </svg>
                    <span v-else>{{ confirmText }}</span>
                </button>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref } from "vue";

interface Props {
    title?: string;
    message: string;
    details?: string;
    confirmText?: string;
    cancelText?: string;
    type?: "info" | "warning" | "danger" | "success";
}

const props = withDefaults(defineProps<Props>(), {
    title: "Confirmation",
    confirmText: "Confirmer",
    cancelText: "Annuler",
    type: "info",
});

const emit = defineEmits<{
    confirm: [];
    cancel: [];
}>();

const loading = ref(false);

const confirm = async () => {
    loading.value = true;
    try {
        emit("confirm");
    } finally {
        loading.value = false;
    }
};

const cancel = () => {
    emit("cancel");
};
</script>

<style scoped>
.confirm-modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: rgba(0, 0, 0, 0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1100;
    animation: fadeIn 0.2s ease-out;
}

.confirm-modal {
    background: white;
    border-radius: 1rem;
    width: 90%;
    max-width: 450px;
    overflow: hidden;
    animation: slideUp 0.3s ease-out;
    box-shadow:
        0 20px 25px -5px rgba(0, 0, 0, 0.1),
        0 10px 10px -5px rgba(0, 0, 0, 0.04);
}

.confirm-modal-header {
    padding: 1.5rem 1.5rem 0 1.5rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.confirm-modal-icon {
    width: 2rem;
    height: 2rem;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 9999px;
    flex-shrink: 0;
}

.confirm-modal-danger .confirm-modal-icon {
    background-color: #fee2e2;
    color: #dc2626;
}

.confirm-modal-warning .confirm-modal-icon {
    background-color: #fef3c7;
    color: #d97706;
}

.confirm-modal-success .confirm-modal-icon {
    background-color: #d1fae5;
    color: #059669;
}

.confirm-modal:not(.confirm-modal-danger):not(.confirm-modal-warning):not(
        .confirm-modal-success
    )
    .confirm-modal-icon {
    background-color: #dbeafe;
    color: #3b82f6;
}

.confirm-modal-icon svg {
    width: 1.25rem;
    height: 1.25rem;
}

.confirm-modal-title {
    font-size: 1.125rem;
    font-weight: 600;
    color: #1a202c;
    margin: 0;
}

.confirm-modal-body {
    padding: 1rem 1.5rem;
}

.confirm-modal-body p {
    margin: 0;
    color: #4a5568;
    font-size: 0.875rem;
    line-height: 1.5;
}

.confirm-modal-details {
    margin-top: 0.5rem !important;
    padding-top: 0.5rem;
    border-top: 1px solid #e2e8f0;
    font-size: 0.75rem !important;
    color: #718096;
    font-family: monospace;
}

.confirm-modal-footer {
    padding: 1rem 1.5rem 1.5rem 1.5rem;
    display: flex;
    justify-content: flex-end;
    gap: 0.75rem;
    background-color: #f7fafc;
}

.confirm-modal-btn {
    padding: 0.5rem 1rem;
    border-radius: 0.5rem;
    font-size: 0.875rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s;
    border: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.confirm-modal-btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.confirm-modal-btn-secondary {
    background-color: white;
    border: 1px solid #e2e8f0;
    color: #4a5568;
}

.confirm-modal-btn-secondary:hover:not(:disabled) {
    background-color: #f7fafc;
    border-color: #cbd5e0;
}

.confirm-modal-btn-primary {
    background-color: #3b82f6;
    color: white;
}

.confirm-modal-btn-primary:hover:not(:disabled) {
    background-color: #2563eb;
    transform: translateY(-1px);
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.confirm-modal-btn-primary.btn-danger {
    background-color: #dc2626;
}

.confirm-modal-btn-primary.btn-danger:hover:not(:disabled) {
    background-color: #b91c1c;
}

.confirm-modal-btn-primary.btn-warning {
    background-color: #d97706;
}

.confirm-modal-btn-primary.btn-warning:hover:not(:disabled) {
    background-color: #b45309;
}

.confirm-modal-btn-primary.btn-success {
    background-color: #059669;
}

.confirm-modal-btn-primary.btn-success:hover:not(:disabled) {
    background-color: #047857;
}

.spinner {
    width: 1rem;
    height: 1rem;
    animation: spin 1s linear infinite;
}

@keyframes fadeIn {
    from {
        opacity: 0;
    }
    to {
        opacity: 1;
    }
}

@keyframes slideUp {
    from {
        transform: translateY(20px);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}

@keyframes spin {
    from {
        transform: rotate(0deg);
    }
    to {
        transform: rotate(360deg);
    }
}

/* Responsive */
@media (max-width: 640px) {
    .confirm-modal {
        width: 95%;
    }

    .confirm-modal-footer {
        flex-direction: column-reverse;
    }

    .confirm-modal-btn {
        width: 100%;
        justify-content: center;
    }
}
</style>
