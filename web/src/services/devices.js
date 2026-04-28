import api from '@/services/api'

export async function getDevices(params = {}) {
  const { data } = await api.get('/devices', { params })
  return data
}

export async function getDeviceReadings(deviceId, params = {}) {
  const { data } = await api.get(`/devices/${deviceId}/readings`, { params })
  return data
}
