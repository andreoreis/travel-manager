import axios from 'axios'

export const login = async (email: string, password: string) => {
  const response = await axios.post('http://localhost:8080/api/auth/login', { email, password })

  const token = response.data.access_token
  localStorage.setItem('authToken', token) // salva no localStorage
  return token
}
export const getAuthToken = () => {
  return localStorage.getItem('authToken')
}
