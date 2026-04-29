<template>
    <span class="badge" :class="badgeClass" :style="badgeStyle">
        {{ statut?.label ?? '—' }}
    </span>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import type { Statut } from '@/types'

const props = defineProps<{
    statut: Statut | null
    size?: 'sm' | 'lg'
    variant?: 'solid' | 'outline' | 'light'  // Nouveau: style du badge
}>()

const colorMap: Record<string, { bg: string; text: string; border: string }> = {
    gray: {
        bg: '#f3f4f6',
        text: '#374151',
        border: '#d1d5db'
    },
    blue: {
        bg: '#dbeafe',
        text: '#1d4ed8',
        border: '#93c5fd'
    },
    purple: {
        bg: '#ede9fe',
        text: '#6d28d9',
        border: '#c4b5fd'
    },
    yellow: {
        bg: '#fef9c3',
        text: '#854d0e',
        border: '#fde047'
    },
    red: {
        bg: '#fee2e2',
        text: '#b91c1c',
        border: '#fecaca'
    },
    green: {
        bg: '#dcfce7',
        text: '#15803d',
        border: '#bbf7d0'
    },
    emerald: {
        bg: '#d1fae5',
        text: '#065f46',
        border: '#a7f3d0'
    },
}

// Classes de bordure par couleur (Tailwind)
const borderClasses: Record<string, string> = {
    gray: 'border-gray-300',
    blue: 'border-blue-300',
    purple: 'border-purple-300',
    yellow: 'border-yellow-300',
    red: 'border-red-300',
    green: 'border-green-300',
    emerald: 'border-emerald-300',
}

const badgeClass = computed(() => {
    if (!props.statut) return ''
    const colorKey = props.statut.color || 'gray'

    if (props.variant === 'outline') {
        return `badge-outline ${borderClasses[colorKey]}`
    }

    return ''
})

const badgeStyle = computed(() => {
    if (!props.statut) {
        return {
            backgroundColor: '#f3f4f6',
            color: '#6b7280',
            fontSize: props.size === 'lg' ? '.9rem' : '.75rem',
            padding: props.size === 'lg' ? '4px 14px' : '2px 10px',
            border: '1px solid #d1d5db',
        }
    }

    const colorKey = props.statut.color || 'gray'
    const colors = colorMap[colorKey]

    // Style selon la variante
    if (props.variant === 'outline') {
        return {
            backgroundColor: 'transparent',
            color: colors.text,
            border: `1px solid ${colors.border}`,
            fontSize: props.size === 'lg' ? '.9rem' : '.75rem',
            padding: props.size === 'lg' ? '4px 14px' : '2px 10px',
        }
    }

    if (props.variant === 'light') {
        return {
            backgroundColor: `${colors.bg}80`, // 50% opacity
            color: colors.text,
            border: `1px solid ${colors.border}`,
            fontSize: props.size === 'lg' ? '.9rem' : '.75rem',
            padding: props.size === 'lg' ? '4px 14px' : '2px 10px',
        }
    }

    // Style solid (par défaut)
    return {
        backgroundColor: colors.bg,
        color: colors.text,
        border: `1px solid ${colors.border}`,
        fontSize: props.size === 'lg' ? '.9rem' : '.75rem',
        padding: props.size === 'lg' ? '4px 14px' : '2px 10px',
    }
})
</script>

<style scoped>
.badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    border-radius: 999px;
    font-weight: 600;
    white-space: nowrap;
    transition: all 0.2s ease;
    line-height: 1.2;
}

/* Animation subtile au hover */
.badge:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

/* Variante outline avec effet hover */
.badge-outline:hover {
    background-color: rgba(0, 0, 0, 0.02);
}

/* Ajout d'une petite puce colorée pour plus de visibilité */
.badge::before {
    content: '';
    display: inline-block;
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background-color: currentColor;
    opacity: 0.7;
}

/* Style sans la puce pour les très petits badges */
.badge[style*="padding: 2px 10px"]::before {
    width: 6px;
    height: 6px;
    margin-right: 4px;
}

/* Variante outline - puce plus discrète */
.badge-outline::before {
    opacity: 0.5;
}
</style>
