<template>
  <div class="overlay" @click.self="$emit('cancel')">
    <div class="dialog">
      <p class="dialog-msg">{{ message }}</p>
      <div class="dialog-actions">
        <button class="btn-cancel"  @click="$emit('cancel')">Annuler</button>
        <button class="btn-confirm" @click="$emit('confirm')">Confirmer</button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
defineProps<{ message: string }>()
defineEmits<{ cancel: []; confirm: [] }>()
</script>

<style scoped>
.overlay      { position: fixed; inset: 0; background: rgba(0,0,0,.4); display: flex; align-items: center; justify-content: center; z-index: 100; }
.dialog       { background: #fff; border-radius: 14px; padding: 2rem; max-width: 400px; width: 90%; box-shadow: 0 8px 32px rgba(0,0,0,.15); }
.dialog-msg   { font-size: 1rem; color: #1e293b; margin-bottom: 1.5rem; text-align: center; }
.dialog-actions { display: flex; gap: .75rem; justify-content: center; }
.btn-cancel   { padding: .55rem 1.25rem; border: 1px solid #e2e8f0; border-radius: 8px; background: #fff; cursor: pointer; }
.btn-confirm  { padding: .55rem 1.25rem; background: #ef4444; color: #fff; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; }
</style>
