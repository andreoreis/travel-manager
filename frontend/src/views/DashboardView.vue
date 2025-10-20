<template>
  <div class="dashboard-container">
    <h1>Pedidos de Viagem</h1>

    <!-- Filtros -->
    <div class="filters">
      <label>
        Status:
        <select v-model="statusFilter" @change="applyFilter">
          <option value="">Todos</option>
          <option value="solicitado">Solicitado</option>
          <option value="aprovado">Aprovado</option>
          <option value="cancelado">Cancelado</option>
        </select>
      </label>
      <button class="btn-primary" @click="showModal = true">Novo Pedido</button>
    </div>

    <!-- Feedback do usuário -->
    <div v-if="feedback.message" :class="['feedback', feedback.type]">{{ feedback.message }}</div>

    <!-- Loading global -->
    <div v-if="travelStore.loading" class="loading">
      <div class="spinner"></div> Carregando...
    </div>

    <!-- Tabela de pedidos -->
    <table v-if="!travelStore.loading && travelStore.travelRequests.length">
      <thead>
        <tr>
          <th>ID</th>
          <th>Nome</th>
          <th>Destino</th>
          <th>Status</th>
          <th>Alterar Status</th>
          <th>Criado em</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="request in filteredRequests" :key="request.id">
          <td>{{ request.id }}</td>
          <td>{{ request.requester_name }}</td>
          <td>{{ request.destination }}</td>
          <td>
            <span :class="statusClass(request.status)">{{ request.status }}</span>
          </td>
          <td>
            <div class="status-update">
              <select v-model="request.status" @change="updateStatus(request)">
                <option value="solicitado">Solicitado</option>
                <option value="aprovado">Aprovado</option>
                <option value="cancelado">Cancelado</option>
              </select>
              <div v-if="request.loading" class="spinner-small"></div>
            </div>
          </td>
          <td>{{ formatDate(request.created_at) }}</td>
        </tr>
      </tbody>
    </table>

    <div v-if="!travelStore.loading && !travelStore.travelRequests.length" class="empty">
      Nenhum pedido encontrado.
    </div>

    <!-- Modal de criação -->
    <div v-if="showModal" class="modal-overlay">
      <div class="modal">
        <h2>Novo Pedido de Viagem</h2>
        <form @submit.prevent="submit">
          <label>
            Usuário ID:
            <input type="number" v-model.number="form.user_id" required />
          </label>

          <label>
            Nome do solicitante:
            <input type="text" v-model="form.requester_name" required />
          </label>

          <label>
            Destino:
            <input type="text" v-model="form.destination" required />
          </label>

          <label>
            Status:
            <select v-model="form.status">
              <option value="solicitado">Solicitado</option>
              <option value="aprovado">Aprovado</option>
              <option value="cancelado">Cancelado</option>
            </select>
          </label>

          <label>
            Data Início:
            <input type="date" v-model="form.start_date" required />
          </label>

          <label>
            Data Fim:
            <input type="date" v-model="form.end_date" required />
          </label>

          <div class="buttons">
            <button type="button" class="btn-secondary" @click="closeModal">Cancelar</button>
            <button type="submit" class="btn-primary">
              <span v-if="formLoading" class="spinner-small"></span>
              Criar
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useTravelStore } from '@/stores/travelRequests'

const travelStore = useTravelStore()
const statusFilter = ref('')
const showModal = ref(false)
const formLoading = ref(false)
const feedback = ref({ message: '', type: '' })

const form = ref({
  user_id: null,
  destination: '',
  status: 'solicitado',
  start_date: '',
  end_date: '',
  requester_name: ''
})

const applyFilter = () => {
  travelStore.fetchTravelRequests({
    status: statusFilter.value || undefined,
    user_id: form.value.user_id || undefined
  })
}

const filteredRequests = computed(() => {
  if (!statusFilter.value) return travelStore.travelRequests
  return travelStore.travelRequests.filter(r => r.status === statusFilter.value)
})

const updateStatus = async (request: any) => {
  const oldStatus = request.status
  request.loading = true
  try {
    await travelStore.updateStatus(request.id, request.status)
    feedback.value = { message: 'Status atualizado com sucesso!', type: 'success' }
  } catch (error) {
    request.status = oldStatus
    feedback.value = { message: 'Falha ao atualizar status', type: 'error' }
  } finally {
    request.loading = false
    setTimeout(() => feedback.value.message = '', 3000)
  }
}

const submit = async () => {
  if (!form.value.user_id || !form.value.destination) return
  formLoading.value = true
  try {
    await travelStore.createTravelRequest(form.value)
    feedback.value = { message: 'Pedido criado com sucesso!', type: 'success' }
    closeModal()
  } catch (err) {
    console.error(err)
    feedback.value = { message: 'Falha ao criar pedido', type: 'error' }
  } finally {
    formLoading.value = false
    setTimeout(() => feedback.value.message = '', 3000)
  }
}

const closeModal = () => {
  showModal.value = false
  form.value = { user_id: null, destination: '', status: 'solicitado', start_date: '', end_date: '', requester_name: '' }
}

const statusClass = (status: string) => {
  switch (status) {
    case 'aprovado': return 'tag tag-success'
    case 'cancelado': return 'tag tag-danger'
    case 'solicitado': return 'tag tag-warning'
    default: return 'tag'
  }
}

const formatDate = (isoString: string) => {
  if (!isoString) return ''
  const date = new Date(isoString)
  const day = String(date.getDate()).padStart(2, '0')
  const month = String(date.getMonth() + 1).padStart(2, '0')
  const year = date.getFullYear()
  const hours = String(date.getHours()).padStart(2, '0')
  const minutes = String(date.getMinutes()).padStart(2, '0')
  return `${day}/${month}/${year} às ${hours}h${minutes}`
}

onMounted(() => {
  travelStore.fetchTravelRequests()
})
</script>

<style scoped>
/* --- Base Element Style --- */
.dashboard-container {
  padding: 2rem;
  max-width: 950px;
  margin: 0 auto;
  font-family: "Segoe UI", Arial, sans-serif;
}

h1 { margin-bottom: 1.5rem; }
.filters { display: flex; gap: 1rem; align-items: center; margin-bottom: 1rem; }

button {
  cursor: pointer;
  border: none;
  border-radius: 4px;
  padding: 0.4rem 1rem;
  font-weight: 500;
}

.btn-primary { background-color: #409EFF; color: #fff; }
.btn-primary:hover { background-color: #66b1ff; }
.btn-secondary { background-color: #f0f0f0; color: #606266; }
.btn-secondary:hover { background-color: #dcdcdc; }

table {
  width: 100%;
  border-collapse: collapse;
  margin-bottom: 1rem;
  font-size: 0.95rem;
}

th, td {
  border: 1px solid #e4e7ed;
  padding: 0.6rem 0.8rem;
}

th { background-color: #f5f7fa; font-weight: 600; }
tbody tr:nth-child(even) { background-color: #f9f9f9; }

select { padding: 0.25rem 0.4rem; border-radius: 4px; border: 1px solid #dcdfe6; }

.tag {
  padding: 0.2rem 0.5rem;
  border-radius: 12px;
  color: #fff;
  font-size: 0.85rem;
  font-weight: 500;
  text-transform: capitalize;
}

.tag-success { background-color: #67C23A; }
.tag-danger { background-color: #F56C6C; }
.tag-warning { background-color: #E6A23C; }

.error { color: #f56c6c; margin-bottom: 1rem; }
.loading { color: #909399; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem; }
.empty { color: #909399; margin-top: 1rem; text-align: center; }

.feedback { padding: 0.6rem 1rem; border-radius: 4px; margin-bottom: 1rem; font-weight: 500; }
.feedback.success { background-color: #f0f9eb; color: #67C23A; border: 1px solid #e1f3d8; }
.feedback.error { background-color: #fef0f0; color: #F56C6C; border: 1px solid #fde2e2; }

/* --- Modal --- */
.modal-overlay {
  position: fixed; inset: 0;
  background: rgba(0,0,0,0.4);
  display: flex;
  justify-content: center;
  align-items: center;
}
.modal {
  background: #fff;
  padding: 1.5rem;
  border-radius: 8px;
  width: 360px;
  box-shadow: 0 2px 12px rgba(0,0,0,0.2);
}
.modal label { display: block; margin-bottom: 0.3rem; font-weight: 500; }
.modal input, .modal select { width: 100%; padding: 0.35rem; margin-bottom: 0.8rem; border: 1px solid #dcdfe6; border-radius: 4px; font-size: 0.95rem; }
.buttons { display: flex; justify-content: flex-end; gap: 0.5rem; }

/* --- Spinner --- */
.spinner, .spinner-small {
  border: 2px solid #f3f3f3;
  border-top: 2px solid #409EFF;
  border-radius: 50%;
  animation: spin 0.8s linear infinite;
}

.spinner { width: 18px; height: 18px; }
.spinner-small { width: 14px; height: 14px; display: inline-block; margin-right: 0.3rem; vertical-align: middle; }

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

/* Status update container */
.status-update { display: flex; align-items: center; gap: 0.3rem; }
</style>
