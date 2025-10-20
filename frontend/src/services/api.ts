import axios, { AxiosError, AxiosInstance, AxiosRequestConfig, AxiosResponse } from 'axios'
import { useAuthStore } from '@/stores/auth'

interface FailedRequest {
  resolve: (token?: string) => void
  reject: (error: any) => void
}

let isRefreshing = false
let failedQueue: FailedRequest[] = []
const BASE_URL = 'http://localhost:8080/api'

const processQueue = (error: any, token: string | null = null) => {
  failedQueue.forEach(({ resolve, reject }) => {
    if (error) reject(error)
    else resolve(token!)
  })
  failedQueue = []
}

// Cria instância do Axios
const api: AxiosInstance = axios.create({
  baseURL: BASE_URL,
  withCredentials: true,
  headers: {
    'Content-Type': 'application/json',
    Accept: 'application/json'
  }
})

// Interceptor de requisição: adiciona token se existir
api.interceptors.request.use((config) => {
  const auth = useAuthStore()
  if (auth.token && config.headers) {
    config.headers.Authorization = `Bearer ${auth.token}`
  }
  return config
})

// Interceptor de resposta: trata 401 e refresh token
api.interceptors.response.use(
  (response: AxiosResponse) => response,
  async (error: AxiosError & { config?: AxiosRequestConfig & { _retry?: boolean } }) => {
    const originalRequest = error.config
    const auth = useAuthStore()

    if (error.response?.status === 401 && originalRequest && !originalRequest._retry) {
      if (isRefreshing) {
        return new Promise<string | undefined>((resolve, reject) => {
          failedQueue.push({ resolve, reject })
        })
          .then((token) => {
            if (originalRequest.headers) {
              originalRequest.headers.Authorization = `Bearer ${token}`
            }
            return axios(originalRequest)
          })
          .catch((err) => Promise.reject(err))
      }

      originalRequest._retry = true
      isRefreshing = true

      try {
        const refreshResp = await axios.post(
          `${BASE_URL}/auth/refresh`,
          {},
          { withCredentials: true }
        )

        const newToken = refreshResp.data.token
        auth.setToken(newToken)
        processQueue(null, newToken)

        if (originalRequest.headers) {
          originalRequest.headers.Authorization = `Bearer ${newToken}`
        }
        return axios(originalRequest)
      } catch (err) {
        processQueue(err, null)
        auth.logout()
        return Promise.reject(err)
      } finally {
        isRefreshing = false
      }
    }

    return Promise.reject(error)
  }
)

export default api
