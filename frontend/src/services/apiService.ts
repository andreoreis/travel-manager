import axios from 'axios'
import { getAuthToken } from './authService'

const API_URL = 'http://localhost:8080/api'

export const apiClient = axios.create({
  baseURL: API_URL,
})

// Interceptor para enviar o token em todas as requisições
apiClient.interceptors.request.use(config => {
  const token = getAuthToken()
  if (token) {
    config.headers.Authorization = `Bearer ${token}`
  }
  return config
})
