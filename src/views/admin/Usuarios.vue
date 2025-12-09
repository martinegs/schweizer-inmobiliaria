<template>
  <div class="usuarios-container">
    <div class="page-header">
      <h2>Usuarios</h2>
      <button @click="showModal = true" class="btn-primary">
        + Nuevo Usuario
      </button>
    </div>

    <div v-if="loading" class="loading">Cargando...</div>
    <div v-else-if="error" class="error-message">{{ error }}</div>
    
    <div v-else class="usuarios-list">
      <table class="usuarios-table">
        <thead>
          <tr>
            <th>Email</th>
            <th>Nombre</th>
            <th>Estado</th>
            <th>Creado</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="usuario in usuarios" :key="usuario.id">
            <td>{{ usuario.email }}</td>
            <td>{{ usuario.nombre }}</td>
            <td>
              <span :class="['badge', usuario.activo ? 'badge-active' : 'badge-inactive']">
                {{ usuario.activo ? 'Activo' : 'Inactivo' }}
              </span>
            </td>
            <td>{{ formatDate(usuario.created_at) }}</td>
            <td>
              <div class="actions">
                <button @click="editUsuario(usuario)" class="btn-icon btn-edit" title="Editar">
                  Editar
                </button>
                <button 
                  @click="toggleEstado(usuario)" 
                  class="btn-icon btn-toggle" 
                  :title="usuario.activo ? 'Desactivar' : 'Activar'"
                >
                  {{ usuario.activo ? 'Desactivar' : 'Activar' }}
                </button>
                <button @click="deleteUsuario(usuario)" class="btn-icon btn-danger" title="Eliminar">
                  Eliminar
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Modal para crear/editar usuario -->
    <div v-if="showModal" class="modal-overlay" @click.self="closeModal">
      <div class="modal-content">
        <div class="modal-header">
          <h3>{{ editingUsuario ? 'Editar Usuario' : 'Nuevo Usuario' }}</h3>
          <button @click="closeModal" class="btn-close">×</button>
        </div>
        
        <form @submit.prevent="saveUsuario" class="modal-form">
          <div class="form-group">
            <label>Email *</label>
            <input 
              type="email" 
              v-model="formData.email" 
              required 
              :disabled="editingUsuario"
            >
          </div>
          
          <div class="form-group">
            <label>Nombre *</label>
            <input 
              type="text" 
              v-model="formData.nombre" 
              required
            >
          </div>
          
          <div class="form-group">
            <label>Contraseña {{ editingUsuario ? '(dejar vacío para no cambiar)' : '*' }}</label>
            <input 
              type="password" 
              v-model="formData.password" 
              :required="!editingUsuario"
              autocomplete="new-password"
            >
          </div>
          
          <div class="form-group">
            <label class="checkbox-label">
              <input type="checkbox" v-model="formData.activo">
              Usuario activo
            </label>
          </div>
          
          <div v-if="formError" class="error-message">{{ formError }}</div>
          
          <div class="modal-actions">
            <button type="button" @click="closeModal" class="btn-secondary">
              Cancelar
            </button>
            <button type="submit" class="btn-primary" :disabled="saving">
              {{ saving ? 'Guardando...' : 'Guardar' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { getUsuarios, createUsuario, updateUsuario, deleteUsuario as deleteUsuarioApi } from '../../api'

const usuarios = ref([])
const loading = ref(true)
const error = ref('')
const showModal = ref(false)
const editingUsuario = ref(null)
const saving = ref(false)
const formError = ref('')

const formData = ref({
  email: '',
  nombre: '',
  password: '',
  activo: true
})

onMounted(() => {
  loadUsuarios()
})

const loadUsuarios = async () => {
  try {
    loading.value = true
    const response = await getUsuarios()
    usuarios.value = response.data
    error.value = ''
  } catch (err) {
    error.value = 'Error al cargar usuarios'
    console.error(err)
  } finally {
    loading.value = false
  }
}

const editUsuario = (usuario) => {
  editingUsuario.value = usuario
  formData.value = {
    email: usuario.email,
    nombre: usuario.nombre,
    password: '',
    activo: usuario.activo
  }
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
  editingUsuario.value = null
  formData.value = {
    email: '',
    nombre: '',
    password: '',
    activo: true
  }
  formError.value = ''
}

const saveUsuario = async () => {
  try {
    saving.value = true
    formError.value = ''
    
    if (editingUsuario.value) {
      await updateUsuario(editingUsuario.value.id, formData.value)
    } else {
      await createUsuario(formData.value)
    }
    
    await loadUsuarios()
    closeModal()
  } catch (err) {
    formError.value = err.response?.data?.message || 'Error al guardar usuario'
  } finally {
    saving.value = false
  }
}

const toggleEstado = async (usuario) => {
  if (!confirm(`¿Deseas ${usuario.activo ? 'desactivar' : 'activar'} este usuario?`)) {
    return
  }
  
  try {
    await updateUsuario(usuario.id, { activo: !usuario.activo })
    await loadUsuarios()
  } catch (err) {
    alert('Error al cambiar estado del usuario')
  }
}

const deleteUsuario = async (usuario) => {
  if (!confirm(`¿Estás seguro de eliminar al usuario ${usuario.email}?`)) {
    return
  }
  
  try {
    await deleteUsuarioApi(usuario.id)
    await loadUsuarios()
  } catch (err) {
    alert('Error al eliminar usuario')
  }
}

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('es-AR', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  })
}
</script>

<style scoped>
.usuarios-container {
  max-width: 1200px;
  margin: 0 auto;
}

.page-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 2rem;
}

.page-header h2 {
  font-size: 2rem;
  color: #333;
  margin: 0;
}

.usuarios-table {
  width: 100%;
  background: white;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.usuarios-table thead {
  background: #1a1a1a;
  color: white;
}

.usuarios-table th {
  padding: 1rem;
  text-align: left;
  font-weight: 600;
}

.usuarios-table td {
  padding: 1rem;
  border-top: 1px solid #eee;
  color: #333;
}

.usuarios-table tbody tr:hover {
  background: #f9f9f9;
}

.badge {
  padding: 0.375rem 0.75rem;
  border-radius: 20px;
  font-size: 0.875rem;
  font-weight: 600;
}

.badge-active {
  background: #d4edda;
  color: #155724;
}

.badge-inactive {
  background: #f8d7da;
  color: #721c24;
}

.actions {
  display: flex;
  gap: 0.5rem;
}

.btn-icon {
  background: transparent;
  border: 1px solid #ddd;
  font-size: 0.875rem;
  cursor: pointer;
  padding: 0.375rem 0.75rem;
  border-radius: 4px;
  transition: all 0.2s;
  color: #333;
}

.btn-icon:hover {
  background: #f5f5f5;
}

.btn-icon.btn-edit:hover {
  background: #007bff;
  color: white;
  border-color: #007bff;
}

.btn-icon.btn-toggle:hover {
  background: #28a745;
  color: white;
  border-color: #28a745;
}

.btn-icon.btn-danger {
  color: #dc3545;
  border-color: #dc3545;
}

.btn-icon.btn-danger:hover {
  background: #dc3545;
  color: white;
}

.btn-primary {
  background: #ff6b35;
  color: white;
  border: none;
  padding: 0.875rem 1.5rem;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s;
}

.btn-primary:hover {
  background: #e55a2b;
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(255, 107, 53, 0.3);
}

.btn-secondary {
  background: #6c757d;
  color: white;
  border: none;
  padding: 0.875rem 1.5rem;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s;
}

.btn-secondary:hover {
  background: #5a6268;
}

.loading,
.error-message {
  text-align: center;
  padding: 2rem;
  background: white;
  border-radius: 12px;
}

.error-message {
  color: #721c24;
  background: #f8d7da;
}

.modal-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
  padding: 1rem;
}

.modal-content {
  background: white;
  border-radius: 16px;
  width: 100%;
  max-width: 500px;
  max-height: 90vh;
  overflow-y: auto;
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1.5rem;
  border-bottom: 1px solid #eee;
}

.modal-header h3 {
  margin: 0;
  font-size: 1.5rem;
}

.btn-close {
  background: transparent;
  border: none;
  font-size: 2rem;
  cursor: pointer;
  color: #999;
  line-height: 1;
}

.btn-close:hover {
  color: #333;
}

.modal-form {
  padding: 1.5rem;
}

.form-group {
  margin-bottom: 1.5rem;
}

.form-group label {
  display: block;
  margin-bottom: 0.5rem;
  font-weight: 600;
  color: #333;
}

.form-group input[type="text"],
.form-group input[type="email"],
.form-group input[type="password"] {
  width: 100%;
  padding: 0.75rem;
  border: 2px solid #e0e0e0;
  border-radius: 8px;
  font-size: 1rem;
  transition: border-color 0.3s;
}

.form-group input:focus {
  outline: none;
  border-color: #ff6b35;
}

.form-group input:disabled {
  background: #f5f5f5;
  cursor: not-allowed;
}

.checkbox-label {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  cursor: pointer;
}

.checkbox-label input[type="checkbox"] {
  width: 20px;
  height: 20px;
  cursor: pointer;
}

.modal-actions {
  display: flex;
  gap: 1rem;
  justify-content: flex-end;
  margin-top: 2rem;
}

@media (max-width: 768px) {
  .usuarios-table {
    font-size: 0.875rem;
  }
  
  .usuarios-table th,
  .usuarios-table td {
    padding: 0.75rem 0.5rem;
  }
  
  .page-header {
    flex-direction: column;
    gap: 1rem;
    align-items: stretch;
  }
}
</style>
