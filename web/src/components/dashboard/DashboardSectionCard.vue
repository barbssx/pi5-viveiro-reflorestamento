<script setup>
defineProps({
  title: {
    type: String,
    required: true,
  },
  subtitle: {
    type: String,
    default: '',
  },
  tone: {
    type: String,
    default: 'neutral',
  },
})
</script>

<template>
  <section class="section-card" :data-tone="tone">
    <header class="section-header">
      <h3>{{ title }}</h3>
      <p v-if="subtitle">{{ subtitle }}</p>
    </header>
    <div class="section-content">
      <slot />
    </div>
  </section>
</template>

<style scoped>
.section-card {
  padding: 20px;
  border-radius: 8px;
  border: 1px solid var(--color-border);
  border-top: 4px solid var(--tone-color, var(--tone-neutral));
  background: var(--color-surface);
  box-shadow: 0 16px 34px var(--color-shadow);
}

.section-card[data-tone='success'] {
  --tone-color: var(--tone-success);
}

.section-card[data-tone='warning'] {
  --tone-color: var(--tone-warning);
}

.section-card[data-tone='danger'] {
  --tone-color: var(--tone-danger);
}

.section-header {
  display: grid;
  gap: 6px;
  margin-bottom: 16px;
}

.section-header h3 {
  font-size: 1.02rem;
  font-weight: 850;
  color: var(--color-primary-strong);
}

.section-header p {
  color: var(--color-muted);
  font-size: 0.92rem;
  line-height: 1.45;
}

.section-content {
  color: var(--color-ink);
}
</style>
