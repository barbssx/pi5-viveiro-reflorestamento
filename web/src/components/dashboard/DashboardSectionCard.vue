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
  border: 1px solid rgba(201, 191, 166, 0.14);
  border-top: 4px solid var(--tone-color, #9aa3ad);
  background: rgba(34, 37, 43, 0.82);
  box-shadow: 0 16px 34px rgba(0, 0, 0, 0.2);
}

.section-card[data-tone='success'] {
  --tone-color: #9aaa8f;
}

.section-card[data-tone='warning'] {
  --tone-color: #c3a86d;
}

.section-card[data-tone='danger'] {
  --tone-color: #c17878;
}

.section-header {
  display: grid;
  gap: 6px;
  margin-bottom: 16px;
}

.section-header h3 {
  font-size: 1.02rem;
  font-weight: 850;
  color: #eee7dc;
}

.section-header p {
  color: #b1a898;
  font-size: 0.92rem;
  line-height: 1.45;
}

.section-content {
  color: #e4ddd1;
}
</style>
