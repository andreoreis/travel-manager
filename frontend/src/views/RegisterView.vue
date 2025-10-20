<template>
  <div class="register-container">
    <h1>Cadastro</h1>
    <form @submit.prevent="handleRegister">
      <input type="text" v-model="name" placeholder="Nome" required />
      <input type="email" v-model="email" placeholder="Email" required />
      <input type="password" v-model="password" placeholder="Senha" required />
      <input type="password" v-model="passwordConfirmation" placeholder="Confirme a senha" required />
      <button type="submit" :disabled="loading">
        {{ loading ? 'Cadastrando...' : 'Cadastrar' }}
      </button>
      <p v-if="error" class="error">{{ error }}</p>
      <p v-if="success" class="success">{{ success }}</p>
    </form>
    <p class="redirect">
      Já tem conta?
      <a @click.prevent="goToLogin" href="#">Faça login</a>
    </p>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import axios from 'axios'
import { useRouter } from 'vue-router'

const name = ref('')
const email = ref('')
const password = ref('')
const passwordConfirmation = ref('')
const loading = ref(false)
const error = ref('')
const success = ref('')

const router = useRouter()

const handleRegister = async () => {
  loading.value = true
  error.value = ''
  success.value = ''
  try {
    await axios.post('http://localhost:8080/api/auth/register', {
      name: name.value,
      email: email.value,
      password: password.value,
      password_confirmation: passwordConfirmation.value,
    })
    success.value = 'Cadastro realizado com sucesso! Você já pode fazer login.'
  } catch (err) {
    if (axios.isAxiosError(err)) {
      error.value = err.response?.data?.message || 'Falha ao cadastrar'
    } else if (err instanceof Error) {
      error.value = err.message
    } else {
      error.value = 'Falha ao cadastrar'
    }
  } finally {
    loading.value = false
  }
}

const goToLogin = () => {
  router.push('/') // volta para tela de login
}
</script>

<style scoped>
.register-container {
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

.register-container h1 {
  text-align: center;
  color: #333;
  font-size: 2rem;
  font-weight: 600;
}

.register-container form {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.register-container input {
  padding: 0.75rem 1rem;
  border: 1px solid #ccc;
  border-radius: 8px;
  font-size: 1rem;
  transition: border-color 0.2s, box-shadow 0.2s;
}

.register-container input:focus {
  outline: none;
  border-color: #4f46e5;
  box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.2);
}

.register-container button {
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

.register-container button:disabled {
  background-color: #a5b4fc;
  cursor: not-allowed;
}

.register-container button:hover:not(:disabled) {
  background-color: #4338ca;
  transform: translateY(-1px);
}

.error {
  color: #dc2626;
  font-size: 0.875rem;
  text-align: center;
}

.redirect {
  text-align: center;
  margin-top: 1rem;
}
.redirect a {
  color: #4f46e5;
  cursor: pointer;
  text-decoration: underline;
}
</style>
