<template>
  <span class="badge" :style="badgeStyle">{{ statut?.label ?? '—' }}</span>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import type { Statut } from '@/types'

const props = defineProps<{ statut: Statut | null; size?: 'sm' | 'lg' }>()

const colorMap: Record<string, { bg: string; text: string }> = {
  gray   : { bg: '#f3f4f6', text: '#374151' },
  blue   : { bg: '#dbeafe', text: '#1d4ed8' },
  purple : { bg: '#ede9fe', text: '#6d28d9' },
  yellow : { bg: '#fef9c3', text: '#854d0e' },
  red    : { bg: '#fee2e2', text: '#b91c1c' },
  green  : { bg: '#dcfce7', text: '#15803d' },
  emerald: { bg: '#d1fae5', text: '#065f46' },
}

const badgeStyle = computed(() => {
  const c = colorMap[props.statut?.color ?? 'gray'] ?? colorMap.gray
  return {
    backgroundColor: c.bg,
    color          : c.text,
    fontSize       : props.size === 'lg' ? '.9rem' : '.75rem',
    padding        : props.size === 'lg' ? '4px 14px' : '2px 10px',
  }
})
</script>

<style scoped>
.badge { display: inline-block; border-radius: 999px; font-weight: 600; }
</style>
