import api from '@/services/api'

export async function getLatestReading(params = {}) {
  const { data } = await api.get('/readings/latest', { params })
  return data
}

export async function getReadings(params = {}) {
  const { data } = await api.get('/readings', { params })
  return data
}
