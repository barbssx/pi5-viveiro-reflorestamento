<script>
import LatestRegister from './dashboard/LatestRegister.vue'

export default {
  components: {
    LatestRegister,
  },

  mounted() {
    this.carregarDados()
  },
  computed: {
    pageCopy() {
      const copies = {
        devices: {
          subtitle:
            'Consulte os sensores cadastrados e acompanhe quais unidades estao ativas no viveiro.',
          title: 'Dispositivos do viveiro',
        },
        latest: {
          subtitle:
            'Veja a leitura mais recente dos sensores, o estado da irrigacao e o dispositivo vinculado.',
          title: 'Ultimo registro',
        },
        readings: {
          subtitle:
            'Analise o historico de leituras com filtros de periodo, limite e dispositivo.',
          title: 'Historico de leituras',
        },
      }

      return (
        copies[this.$route.name] || {
          subtitle:
            'Acompanhe sensores, historico de leituras e o estado atual da irrigacao em uma unica visao.',
          title: 'Dashboard do viveiro',
        }
      )
    },
  },
  methods: {
    carregarDados() {
      this.$refs.refLatestRegister?.carregarUltimo()
    },
  },
}
</script>

<template>
  <div class="page-shell">
    <div class="dashboard-container">
      <div class="dashboard-header">
        <div class="heading-block">
          <p class="eyebrow">Reflorestamento</p>
          <h1>{{ pageCopy.title }}</h1>
          <p class="subtitle">{{ pageCopy.subtitle }}</p>
        </div>

        <div class="header-actions">
          <button type="button" class="btn-refresh" @click="carregarDados()">
            Atualizar painel
          </button>
        </div>
      </div>

      <div class="header-divider"></div>

      <nav class="dashboard-nav" aria-label="Navegacao do dashboard">
        <RouterLink to="/">Visao geral</RouterLink>
        <RouterLink to="/latest">Ultimo registro</RouterLink>
        <RouterLink to="/readings">Historico</RouterLink>
        <RouterLink to="/devices">Dispositivos</RouterLink>
      </nav>
    </div>

    <div class="dashboard-content">
      <LatestRegister ref="refLatestRegister" />
    </div>
  </div>
</template>
<style scoped>
.page-shell {
  min-height: 100vh;
  padding: 36px 20px 56px;
  background:
    linear-gradient(90deg, rgba(199, 190, 166, 0.045) 1px, transparent 1px),
    linear-gradient(180deg, rgba(199, 190, 166, 0.045) 1px, transparent 1px),
    linear-gradient(180deg, #15171c 0%, #1f2329 52%, #181b20 100%);
  background-size: 28px 28px;
}

.dashboard-container {
  max-width: 1180px;
  margin: 0 auto;
  padding: 0 0 18px;
}

.dashboard-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 24px;
  margin-bottom: 18px;
  padding: 22px 24px;
  border: 1px solid rgba(201, 191, 166, 0.14);
  border-radius: 8px;
  background: rgba(34, 37, 43, 0.88);
  box-shadow: 0 18px 44px rgba(0, 0, 0, 0.26);
}

.heading-block {
  max-width: 720px;
}

.eyebrow {
  font-size: 0.82rem;
  letter-spacing: 0.12em;
  text-transform: uppercase;
  color: #b9b0a0;
  margin-bottom: 8px;
  font-weight: 800;
}

h1 {
  font-size: clamp(1.85rem, 3vw, 2.6rem);
  line-height: 1.1;
  font-family: 'Avenir Next', 'Trebuchet MS', sans-serif;
  font-weight: 800;
  color: #eee7dc;
}

.header-actions {
  display: flex;
  align-items: center;
}

.subtitle {
  color: #c7beb0;
  margin-top: 10px;
  font-size: 0.98rem;
  line-height: 1.55;
}

.btn-refresh {
  min-height: 44px;
  padding: 0 18px;
  background: #c3a86d;
  color: #1b1d22;
  border: 1px solid #d3bd83;
  border-radius: 8px;
  cursor: pointer;
  font-weight: 800;
  white-space: nowrap;
  box-shadow: 0 10px 18px rgba(0, 0, 0, 0.2);
  transition:
    transform 0.2s ease,
    box-shadow 0.2s ease;
}

.btn-refresh:hover {
  transform: translateY(-1px);
  box-shadow: 0 14px 22px rgba(0, 0, 0, 0.24);
}

.header-divider {
  height: 1px;
  background: linear-gradient(90deg, rgba(201, 191, 166, 0.18), rgba(201, 191, 166, 0));
}

.dashboard-nav {
  display: flex;
  flex-wrap: wrap;
  gap: 10px;
}

.dashboard-nav a {
  min-height: 38px;
  display: inline-flex;
  align-items: center;
  padding: 0 14px;
  border: 1px solid rgba(201, 191, 166, 0.14);
  border-radius: 8px;
  background: rgba(34, 37, 43, 0.72);
  color: #c7beb0;
  font-weight: 800;
  text-decoration: none;
}

.dashboard-nav a.router-link-active {
  background: #c3a86d;
  border-color: #d3bd83;
  color: #1b1d22;
}

.dashboard-content {
  max-width: 1180px;
  margin: 0 auto;
}

@media (max-width: 820px) {
  .page-shell {
    padding: 18px 14px 40px;
  }

  .dashboard-header {
    flex-direction: column;
    align-items: flex-start;
    padding: 18px;
  }

  .header-actions,
  .btn-refresh,
  .dashboard-nav a {
    width: 100%;
  }
}
</style>
