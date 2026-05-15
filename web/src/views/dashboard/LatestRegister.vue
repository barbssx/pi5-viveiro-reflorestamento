<script>
import DashboardMetricCard from '@/components/dashboard/DashboardMetricCard.vue'
import DashboardSectionCard from '@/components/dashboard/DashboardSectionCard.vue'
import { getDevices } from '@/services/devices'
import { getLatestReading, getReadings } from '@/services/readings'
import {
  formatReadingTimestamp,
  getIrrigationPresentation,
  getReadingChips,
  getReadingHeadline,
  getReadingMetrics,
} from '@/utils/reading'

function normalizeCollection(response) {
  if (Array.isArray(response)) {
    return response
  }

  return response?.data || []
}

function toNumber(value) {
  const numericValue = Number(value)
  return Number.isFinite(numericValue) ? numericValue : null
}

function average(values) {
  const numericValues = values.map(toNumber).filter((value) => value !== null)

  if (!numericValues.length) {
    return null
  }

  return numericValues.reduce((total, value) => total + value, 0) / numericValues.length
}

function formatStat(value, unit = '') {
  const numericValue = toNumber(value)

  if (numericValue === null) {
    return '--'
  }

  return `${numericValue.toFixed(1)}${unit}`
}

function getDateRange(period) {
  if (period === 'all') {
    return {}
  }

  const now = new Date()
  const from = new Date(now)

  if (period === '24h') {
    from.setDate(now.getDate() - 1)
  }

  if (period === '7d') {
    from.setDate(now.getDate() - 7)
  }

  if (period === '30d') {
    from.setDate(now.getDate() - 30)
  }

  return { from: from.toISOString(), to: now.toISOString() }
}

export default {
  components: {
    DashboardMetricCard,
    DashboardSectionCard,
  },

  data() {
    return {
      devices: [],
      error: null,
      latestReading: null,
      readings: [],
      selectedDeviceId: '',
      selectedLimit: 80,
      selectedPeriod: 'all',
      status: 'idle',
    }
  },

  computed: {
    activeRouteName() {
      return this.$route.name || 'home'
    },

    currentDeviceParams() {
      return this.selectedDeviceId ? { device_id: this.selectedDeviceId } : {}
    },

    currentReadingParams() {
      return {
        ...this.currentDeviceParams,
        ...getDateRange(this.selectedPeriod),
        limit: this.selectedLimit,
      }
    },

    dataFormatada() {
      return formatReadingTimestamp(this.latestReading?.collected_at)
    },

    devicesAtivos() {
      return this.devices.filter((device) => device.is_active).length
    },

    dispositivo() {
      return this.latestReading?.device
    },

    estadoIrrigacao() {
      return getIrrigationPresentation(this.latestReading)
    },

    isLoading() {
      return this.status === 'loading'
    },

    readingsStats() {
      const soilValues = this.readings.map((reading) => reading.soil_moisture)
      const temperatureValues = this.readings.map((reading) => reading.air_temperature)
      const humidityValues = this.readings.map((reading) => reading.air_humidity)
      const irrigations = this.readings.filter((reading) => reading.irrigation_status).length

      return {
        airHumidityAverage: formatStat(average(humidityValues), '%'),
        irrigationRate: this.readings.length
          ? formatStat((irrigations / this.readings.length) * 100, '%')
          : '--',
        soilAverage: formatStat(average(soilValues), '%'),
        soilMin: formatStat(
          Math.min(...soilValues.map(toNumber).filter((value) => value !== null)),
          '%',
        ),
        temperatureAverage: formatStat(average(temperatureValues), '°C'),
      }
    },

    selectedDeviceLabel() {
      const selectedDevice = this.devices.find(
        (device) => String(device.id) === String(this.selectedDeviceId),
      )
      return selectedDevice
        ? `${selectedDevice.name} - ${selectedDevice.code}`
        : 'Todos os dispositivos'
    },

    showDevicesSection() {
      return ['home', 'devices'].includes(this.activeRouteName)
    },

    showLatestSection() {
      return ['home', 'latest'].includes(this.activeRouteName)
    },

    showReadingsSection() {
      return ['home', 'readings'].includes(this.activeRouteName)
    },

    manchete() {
      return getReadingHeadline(this.latestReading)
    },

    metricas() {
      return getReadingMetrics(this.latestReading)
    },

    resumoLeitura() {
      if (!this.latestReading) {
        return 'Nenhuma leitura foi encontrada para os filtros atuais.'
      }

      return (
        this.latestReading.summary ||
        'Leitura consolidada com base no registro mais recente, no historico carregado e no dispositivo vinculado.'
      )
    },

    chipsDeStatus() {
      return getReadingChips(this.latestReading)
    },

    totalLeituras() {
      return this.readings.length
    },
  },

  mounted() {
    this.carregarUltimo()
  },

  methods: {
    async carregarUltimo() {
      this.status = 'loading'
      this.error = null

      try {
        const params = this.currentDeviceParams
        const [devicesResponse, readingsResponse, latestReading] = await Promise.all([
          getDevices({ limit: 100 }),
          getReadings(this.currentReadingParams),
          getLatestReading(params),
        ])

        this.devices = normalizeCollection(devicesResponse)
        this.readings = normalizeCollection(readingsResponse)
        this.latestReading = latestReading
        this.status = 'success'
      } catch (error) {
        this.status = 'error'
        this.latestReading = null
        this.readings = []
        this.error =
          error?.response?.data?.message ||
          'Nao foi possivel carregar os dados do viveiro. Confira se o servidor esta ativo e tente novamente.'
      }
    },

    formatarData(value) {
      return formatReadingTimestamp(value)
    },

    limparFiltros() {
      this.selectedDeviceId = ''
      this.selectedLimit = 80
      this.selectedPeriod = 'all'
      this.carregarUltimo()
    },
  },
}
</script>

<template>
  <div class="dashboard-reading" :data-tone="estadoIrrigacao.tone">
    <div v-if="status === 'error'" class="feedback-card error-card">
      <div>
        <p class="feedback-eyebrow">Falha de comunicacao</p>
        <h3>{{ error }}</h3>
        <p>Verifique a conexao com o servidor de dados e atualize o painel.</p>
      </div>
      <button type="button" class="ghost-button" @click="carregarUltimo()">Tentar novamente</button>
    </div>

    <section v-else class="dashboard-panel">
      <header class="hero-card">
        <div class="hero-copy">
          <p class="feedback-eyebrow">Monitoramento do viveiro</p>
          <h2>{{ latestReading ? estadoIrrigacao.label : 'Carregando sensores' }}</h2>
          <p class="hero-description">
            {{
              latestReading ? manchete : 'Buscando leituras, dispositivos e status da irrigacao.'
            }}
          </p>
        </div>

        <div class="filters-panel">
          <div class="control-group">
            <label for="device-filter">Dispositivo</label>
            <select
              id="device-filter"
              v-model="selectedDeviceId"
              :disabled="isLoading"
              @change="carregarUltimo()"
            >
              <option value="">Todos os dispositivos</option>
              <option v-for="device in devices" :key="device.id" :value="device.id">
                {{ device.name }} - {{ device.code }}
              </option>
            </select>
          </div>

          <div class="filter-row">
            <div class="control-group compact-control">
              <label for="period-filter">Periodo</label>
              <select
                id="period-filter"
                v-model="selectedPeriod"
                :disabled="isLoading"
                @change="carregarUltimo()"
              >
                <option value="all">Tudo</option>
                <option value="24h">24h</option>
                <option value="7d">7 dias</option>
                <option value="30d">30 dias</option>
              </select>
            </div>

            <div class="control-group compact-control">
              <label for="limit-filter">Limite</label>
              <select
                id="limit-filter"
                v-model.number="selectedLimit"
                :disabled="isLoading"
                @change="carregarUltimo()"
              >
                <option :value="20">20</option>
                <option :value="50">50</option>
                <option :value="80">80</option>
                <option :value="150">150</option>
              </select>
            </div>
          </div>

          <button type="button" class="ghost-button" :disabled="isLoading" @click="limparFiltros()">
            Limpar filtros
          </button>
        </div>
      </header>

      <div class="overview-strip">
        <div class="overview-item">
          <span>Ultima coleta</span>
          <strong>{{ dataFormatada }}</strong>
        </div>
        <div class="overview-item">
          <span>Dispositivos ativos</span>
          <strong>{{ devicesAtivos }}/{{ devices.length }}</strong>
        </div>
        <div class="overview-item">
          <span>Leituras carregadas</span>
          <strong>{{ totalLeituras }}</strong>
        </div>
        <div class="overview-item">
          <span>Filtro ativo</span>
          <strong>{{ selectedDeviceLabel }}</strong>
        </div>
      </div>

      <div class="overview-strip">
        <div class="overview-item">
          <span>Media do solo</span>
          <strong>{{ readingsStats.soilAverage }}</strong>
        </div>
        <div class="overview-item">
          <span>Menor umidade</span>
          <strong>{{ readingsStats.soilMin }}</strong>
        </div>
        <div class="overview-item">
          <span>Media temp.</span>
          <strong>{{ readingsStats.temperatureAverage }}</strong>
        </div>
        <div class="overview-item">
          <span>Media umid. ar</span>
          <strong>{{ readingsStats.airHumidityAverage }}</strong>
        </div>
        <div class="overview-item">
          <span>Irrigacao ativa</span>
          <strong>{{ readingsStats.irrigationRate }}</strong>
        </div>
        <div class="overview-item">
          <span>Origem</span>
          <strong>{{ latestReading?.source || '--' }}</strong>
        </div>
      </div>

      <div v-if="showLatestSection" class="metrics-grid">
        <DashboardMetricCard
          v-for="metrica in metricas"
          :key="metrica.id"
          :label="metrica.label"
          :value="metrica.value"
          :unit="metrica.unit"
          :helper="metrica.helper"
          :tone="metrica.tone"
        />
      </div>

      <div v-if="showLatestSection" class="details-grid">
        <DashboardSectionCard
          title="Resumo operacional"
          subtitle="Status consolidado da leitura selecionada"
          :tone="estadoIrrigacao.tone"
        >
          <p class="summary-text">{{ resumoLeitura }}</p>

          <div class="chip-row">
            <span
              v-for="chip in chipsDeStatus"
              :key="chip.id"
              class="status-pill"
              :data-tone="chip.tone"
            >
              {{ chip.label }}
            </span>
          </div>
        </DashboardSectionCard>

        <DashboardSectionCard title="Dispositivo vinculado">
          <ul class="detail-list">
            <li>
              <span>Nome</span>
              <strong>{{ dispositivo?.name || '--' }}</strong>
            </li>
            <li>
              <span>Codigo</span>
              <strong>{{ dispositivo?.code || '--' }}</strong>
            </li>
            <li>
              <span>Local</span>
              <strong>{{ dispositivo?.location || '--' }}</strong>
            </li>
            <li>
              <span>Status</span>
              <strong>{{ dispositivo?.is_active ? 'Ativo' : 'Inativo' }}</strong>
            </li>
          </ul>
        </DashboardSectionCard>
      </div>

      <DashboardSectionCard v-if="showReadingsSection" title="Historico de leituras">
        <div class="table-wrap">
          <table>
            <thead>
              <tr>
                <th>Coleta</th>
                <th>Dispositivo</th>
                <th>Solo</th>
                <th>Temp.</th>
                <th>Ar</th>
                <th>Irrigacao</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="reading in readings" :key="reading.id">
                <td>{{ formatarData(reading.collected_at) }}</td>
                <td>{{ reading.device?.name || reading.device_id }}</td>
                <td>{{ reading.soil_moisture ?? '--' }}%</td>
                <td>{{ reading.air_temperature ?? '--' }}°C</td>
                <td>{{ reading.air_humidity ?? '--' }}%</td>
                <td>
                  <span
                    class="status-pill"
                    :data-tone="reading.irrigation_status ? 'success' : 'neutral'"
                  >
                    {{ reading.irrigation_status ? 'Ligada' : 'Desligada' }}
                  </span>
                </td>
              </tr>
              <tr v-if="!readings.length">
                <td colspan="6" class="empty-cell">
                  Nenhuma leitura encontrada para os filtros atuais.
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </DashboardSectionCard>

      <DashboardSectionCard
        v-if="showDevicesSection"
        title="Dispositivos cadastrados"
        subtitle="Sensores disponiveis para acompanhamento"
      >
        <div class="device-grid">
          <article v-for="device in devices" :key="device.id" class="device-card">
            <div>
              <h4>{{ device.name }}</h4>
              <p>{{ device.location || 'Sem local informado' }}</p>
            </div>
            <span class="status-pill" :data-tone="device.is_active ? 'success' : 'neutral'">
              {{ device.code }}
            </span>
          </article>
        </div>
      </DashboardSectionCard>
    </section>
  </div>
</template>

<style scoped>
.dashboard-reading {
  --tone-color: var(--tone-neutral);
  display: grid;
  gap: 18px;
}

.dashboard-reading[data-tone='success'] {
  --tone-color: var(--tone-success);
}

.dashboard-reading[data-tone='warning'] {
  --tone-color: var(--tone-warning);
}

.dashboard-reading[data-tone='danger'] {
  --tone-color: var(--tone-danger);
}

.dashboard-panel {
  display: grid;
  gap: 18px;
}

.hero-card,
.feedback-card {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  gap: 24px;
  padding: 24px;
  border-radius: 8px;
  background:
    linear-gradient(135deg, rgba(20, 49, 36, 0.96), rgba(36, 84, 63, 0.92)),
    var(--color-primary-strong);
  border: 1px solid rgba(255, 255, 255, 0.18);
  border-left: 6px solid var(--tone-color);
  box-shadow: 0 20px 46px rgba(28, 50, 36, 0.22);
}

.error-card {
  border-left-color: var(--tone-danger);
}

.feedback-eyebrow {
  font-size: 0.82rem;
  letter-spacing: 0.12em;
  text-transform: uppercase;
  color: #dfe9d6;
  margin-bottom: 10px;
  font-weight: 800;
}

.hero-copy {
  max-width: 720px;
}

.hero-copy h2,
.feedback-card h3 {
  font-size: clamp(1.35rem, 2vw, 1.85rem);
  line-height: 1.2;
  font-family: 'Avenir Next', 'Trebuchet MS', sans-serif;
  font-weight: 800;
  color: var(--color-inverse);
}

.hero-description,
.feedback-card p,
.summary-text {
  color: #d9e4d3;
  font-size: 0.96rem;
  line-height: 1.6;
}

.filters-panel {
  min-width: 320px;
  display: grid;
  gap: 12px;
}

.filter-row {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 10px;
}

.control-group {
  min-width: 270px;
  display: grid;
  gap: 8px;
}

.compact-control {
  min-width: 0;
}

.control-group label {
  color: #dfe9d6;
  font-size: 0.78rem;
  font-weight: 800;
  letter-spacing: 0.1em;
  text-transform: uppercase;
}

.control-group select {
  width: 100%;
  min-height: 42px;
  border: 1px solid rgba(255, 255, 255, 0.26);
  border-radius: 8px;
  background: rgba(255, 255, 255, 0.12);
  color: var(--color-inverse);
  padding: 0 12px;
}

.control-group select option {
  color: var(--color-ink);
}

.control-group select:focus-visible,
.ghost-button:focus-visible {
  outline: none;
  box-shadow: var(--focus-ring);
}

.ghost-button {
  min-height: 42px;
  padding: 0 16px;
  border: 1px solid rgba(255, 255, 255, 0.28);
  border-radius: 8px;
  background: rgba(255, 255, 255, 0.12);
  color: var(--color-inverse);
  cursor: pointer;
  font-weight: 800;
}

.ghost-button:disabled {
  cursor: not-allowed;
  opacity: 0.62;
}

.overview-strip,
.metrics-grid,
.details-grid,
.device-grid {
  display: grid;
  gap: 14px;
}

.overview-strip {
  grid-template-columns: repeat(auto-fit, minmax(170px, 1fr));
}

.overview-item {
  padding: 16px;
  border-radius: 8px;
  background: var(--color-surface);
  border: 1px solid var(--color-border);
  box-shadow: 0 12px 26px var(--color-shadow);
}

.overview-item span {
  display: block;
  color: var(--color-muted);
  font-size: 0.78rem;
  font-weight: 800;
  letter-spacing: 0.08em;
  text-transform: uppercase;
}

.overview-item strong {
  display: block;
  margin-top: 8px;
  color: var(--color-primary-strong);
  font-size: 1.15rem;
  font-weight: 850;
  overflow-wrap: anywhere;
}

.metrics-grid {
  grid-template-columns: repeat(4, minmax(0, 1fr));
}

.details-grid {
  grid-template-columns: minmax(0, 1.1fr) minmax(0, 0.9fr);
}

.chip-row {
  display: flex;
  flex-wrap: wrap;
  gap: 10px;
}

.status-pill {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  min-height: 30px;
  padding: 0 10px;
  border-radius: 999px;
  font-size: 0.76rem;
  font-weight: 800;
  background: #e8ede4;
  color: var(--color-muted-strong);
  white-space: nowrap;
}

.status-pill[data-tone='success'] {
  background: #dfe9d6;
  color: #24543f;
}

.status-pill[data-tone='warning'] {
  background: #f4e8c9;
  color: #745316;
}

.status-pill[data-tone='danger'] {
  background: #f2d9d6;
  color: #813a35;
}

.detail-list {
  list-style: none;
  display: grid;
  gap: 14px;
  padding: 0;
}

.detail-list li {
  display: flex;
  justify-content: space-between;
  gap: 16px;
  padding-bottom: 12px;
  border-bottom: 1px solid var(--color-border);
}

.detail-list li:last-child {
  padding-bottom: 0;
  border-bottom: 0;
}

.detail-list span {
  color: var(--color-muted);
}

.detail-list strong {
  color: var(--color-primary-strong);
  text-align: right;
  font-weight: 800;
}

.table-wrap {
  overflow-x: auto;
}

table {
  width: 100%;
  border-collapse: collapse;
  min-width: 760px;
}

th,
td {
  padding: 12px 10px;
  border-bottom: 1px solid var(--color-border);
  text-align: left;
}

th {
  color: var(--color-muted);
  font-size: 0.76rem;
  font-weight: 850;
  letter-spacing: 0.08em;
  text-transform: uppercase;
}

td {
  color: var(--color-ink);
}

.empty-cell {
  text-align: center;
  color: var(--color-muted);
}

.device-grid {
  grid-template-columns: repeat(3, minmax(0, 1fr));
}

.device-card {
  display: flex;
  justify-content: space-between;
  gap: 14px;
  padding: 16px;
  border-radius: 8px;
  background: var(--color-surface-muted);
  border: 1px solid var(--color-border);
}

.device-card h4 {
  color: var(--color-primary-strong);
  font-size: 1rem;
  font-weight: 850;
}

.device-card p {
  color: var(--color-muted);
  margin-top: 4px;
}

@media (max-width: 1100px) {
  .metrics-grid,
  .overview-strip {
    grid-template-columns: repeat(2, minmax(0, 1fr));
  }

  .details-grid,
  .device-grid {
    grid-template-columns: 1fr;
  }
}

@media (max-width: 720px) {
  .hero-card,
  .feedback-card {
    flex-direction: column;
    padding: 18px;
  }

  .filters-panel,
  .control-group,
  .ghost-button {
    width: 100%;
  }

  .metrics-grid,
  .overview-strip {
    grid-template-columns: 1fr;
  }

  .detail-list li,
  .device-card {
    flex-direction: column;
  }

  .detail-list strong {
    text-align: left;
  }
}
</style>
