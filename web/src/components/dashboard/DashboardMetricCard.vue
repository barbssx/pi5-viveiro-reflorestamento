<script setup>
defineProps({
  label: {
    type: String,
    required: true,
  },
  value: {
    type: String,
    required: true,
  },
  unit: {
    type: String,
    default: '',
  },
  helper: {
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
  <article class="metric-card" :data-tone="tone">
    <p class="metric-label">{{ label }}</p>
    <div class="metric-value-row">
      <strong class="metric-value">{{ value }}</strong>
      <span v-if="unit" class="metric-unit">{{ unit }}</span>
    </div>
    <p class="metric-helper">{{ helper }}</p>
  </article>
</template>

<style scoped>
.metric-card {
  display: grid;
  gap: 10px;
  min-height: 166px;
  padding: 18px;
  border-radius: 8px;
  border: 1px solid rgba(201, 191, 166, 0.14);
  border-top: 4px solid var(--tone-color, #9aa3ad);
  background: rgba(34, 37, 43, 0.82);
  box-shadow: 0 16px 34px rgba(0, 0, 0, 0.2);
}

.metric-card[data-tone='success'] {
  --tone-color: #9aaa8f;
}

.metric-card[data-tone='warning'] {
  --tone-color: #c3a86d;
}

.metric-card[data-tone='danger'] {
  --tone-color: #c17878;
}

.metric-card[data-tone='neutral'] {
  --tone-color: #9aa3ad;
}

.metric-label {
  font-size: 0.82rem;
  letter-spacing: 0.08em;
  text-transform: uppercase;
  color: #b1a898;
  font-weight: 800;
}

.metric-value-row {
  display: flex;
  align-items: baseline;
  gap: 6px;
  color: #eee7dc;
}

.metric-value {
  font-size: clamp(1.7rem, 2.3vw, 2.35rem);
  line-height: 1;
  font-family: 'Avenir Next', 'Trebuchet MS', sans-serif;
  font-weight: 850;
}

.metric-unit {
  font-size: 1rem;
  font-weight: 800;
}

.metric-helper {
  color: #b1a898;
  font-size: 0.9rem;
  line-height: 1.45;
}
</style>
