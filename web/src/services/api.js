import axios from 'axios'

function getApiBaseUrl() {
  const configuredUrl = import.meta.env.VITE_API_URL || 'http://localhost:8000/api'
  const normalizedUrl = configuredUrl.replace(/\/+$/, '')

  return normalizedUrl.endsWith('/api') ? normalizedUrl : `${normalizedUrl}/api`
}

const api = axios.create({
  baseURL: getApiBaseUrl(),
  timeout: 10000,
  headers: {
    Accept: 'application/json',
  },
})

export default api
