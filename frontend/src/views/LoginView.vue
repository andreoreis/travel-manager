<template>
  <div class="login-container">
    <h1>Login</h1>
    <form @submit.prevent="handleLogin">
      <input type="email" v-model="email" placeholder="Email" required />
      <input type="password" v-model="password" placeholder="Senha" required />
      <button type="submit" :disabled="loading">
        {{ loading ? 'Entrando...' : 'Entrar' }}
      </button>
      <p v-if="error" class="error">{{ error }}</p>
    </form>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { useAuthStore } from '../stores/auth'
import axios from 'axios'

const email = ref('')
const password = ref('')
const loading = ref(false)
const error = ref('')

const auth = useAuthStore()

const handleLogin = async () => {
  loading.value = true
  error.value = ''
  try {
    await auth.login(email.value, password.value)
    // redireciona para dashboard ou outra rota protegida
    window.location.href = '/dashboard'
  } catch (err) {
    // eslint-safe catch
    if (axios.isAxiosError(err)) {
      error.value = err.response?.data?.message || 'Falha ao autenticar'
    } else if (err instanceof Error) {
      error.value = err.message
    } else {
      error.value = 'Falha ao autenticar'
    }
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
.login-container {
  max-width: 400px;
  margin: 5vh auto;
  padding: 2.5rem 2rem;
  background-color: #ffffff;
  border-radius: 12px;
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
  font-family: 'Inter', sans-serif;
}

.login-container h1 {
  text-align: center;
  color: #333;
  font-size: 2rem;
  font-weight: 600;
}

.login-container form {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.login-container input {
  padding: 0.75rem 1rem;
  border: 1px solid #ccc;
  border-radius: 8px;
  font-size: 1rem;
  transition: border-color 0.2s, box-shadow 0.2s;
}

.login-container input:focus {
  outline: none;
  border-color: #4f46e5;
  box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.2);
}

.login-container button {
  padding: 0.75rem 1rem;
  background-color: #4f46e5;
  color: #fff;
  border: none;
  border-radius: 8px;
  font-size: 1rem;
  font-weight: 500;
  cursor: pointer;
  transition: background-color 0.2s, transform 0.1s;
}

.login-container button:disabled {
  background-color: #a5b4fc;
  cursor: not-allowed;
}

.login-container button:hover:not(:disabled) {
  background-color: #4338ca;
  transform: translateY(-1px);
}

.error {
  color: #dc2626;
  font-size: 0.875rem;
  text-align: center;
}
</style>
