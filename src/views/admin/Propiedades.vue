<template>
  <div class="admin-propiedades">
    <div class="page-header">
      <h2>Gestión de Propiedades</h2>
      <router-link to="/admin/propiedades/nueva" class="btn-primary">
        + Nueva Propiedad
      </router-link>
    </div>
    
    <div class="filters">
      <button 
        @click="filtroActual = 'todas'" 
        :class="['filter-btn', { active: filtroActual === 'todas' }]"
      >
        Todas ({{ propiedades.length }})
      </button>
      <button 
        @click="filtroActual = 'venta'" 
        :class="['filter-btn', { active: filtroActual === 'venta' }]"
      >
        En Venta ({{ propiedadesFiltradas.filter(p => p.tipo === 'venta').length }})
      </button>
      <button 
        @click="filtroActual = 'alquiler'" 
        :class="['filter-btn', { active: filtroActual === 'alquiler' }]"
      >
        En Alquiler ({{ propiedadesFiltradas.filter(p => p.tipo === 'alquiler').length }})
      </button>
    </div>
    
    <div v-if="loading" class="loading">
      Cargando propiedades...
    </div>
    
    <div v-else-if="propiedadesFiltradas.length === 0" class="empty">
      <p>No hay propiedades aún.</p>
      <router-link to="/admin/propiedades/nueva" class="btn-primary">
        Crear Primera Propiedad
      </router-link>
    </div>
    
    <div v-else class="propiedades-grid">
      <div v-for="propiedad in propiedadesFiltradas" :key="propiedad.id" class="propiedad-card">
        <div class="card-image">
          <img 
            v-if="propiedad.imagenes && propiedad.imagenes.length > 0" 
            :src="getImageUrl(propiedad.imagenes[0])" 
            :alt="propiedad.titulo"
          >
          <div v-else class="no-image">Sin imagen</div>
          
          <div class="badges">
            <span :class="['badge', `badge-${propiedad.tipo}`]">
              {{ propiedad.tipo === 'venta' ? 'Venta' : 'Alquiler' }}
            </span>
            <span v-if="propiedad.destacada" class="badge badge-destacada">
              ⭐ Destacada
            </span>
            <span v-if="!propiedad.activa" class="badge badge-inactive">
              Inactiva
            </span>
          </div>
        </div>
        
        <div class="card-content">
          <h3>{{ propiedad.titulo }}</h3>
          <p class="ubicacion">{{ propiedad.ubicacion }}</p>
          
          <div class="features">
            <span>{{ propiedad.dormitorios }} dorm</span>
            <span>{{ propiedad.banos }} baños</span>
            <span>{{ propiedad.superficie }}m²</span>
          </div>
          
          <div class="precio">
            ${{ Number(propiedad.precio).toLocaleString() }}
            <span v-if="propiedad.tipo === 'alquiler'">/mes</span>
          </div>
          
          <div class="card-actions">
            <router-link 
              :to="`/admin/propiedades/${propiedad.id}/editar`" 
              class="btn-edit"
            >
              Editar
            </router-link>
            <button 
              @click="confirmarEliminar(propiedad)" 
              class="btn-delete"
            >
              Eliminar
            </button>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Modal de confirmación -->
    <div v-if="propiedadAEliminar" class="modal-overlay" @click="cancelarEliminar">
      <div class="modal" @click.stop>
        <h3>¿Eliminar propiedad?</h3>
        <p>¿Estás seguro de que deseas eliminar "{{ propiedadAEliminar.titulo }}"?</p>
        <p class="warning">Esta acción no se puede deshacer.</p>
        <div class="modal-actions">
          <button @click="cancelarEliminar" class="btn-cancel">
            Cancelar
          </button>
          <button @click="eliminarPropiedad" class="btn-delete">
            Eliminar
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { getPropiedades, deletePropiedad } from '../../api'

const propiedades = ref([])
const loading = ref(true)
const filtroActual = ref('todas')
const propiedadAEliminar = ref(null)

const propiedadesFiltradas = computed(() => {
  if (filtroActual.value === 'todas') return propiedades.value
  return propiedades.value.filter(p => p.tipo === filtroActual.value)
})

const getImageUrl = (filename) => {
  return `${import.meta.env.VITE_API_URL.replace('/api', '')}/uploads/${filename}`
}

const cargarPropiedades = async () => {
  loading.value = true
  try {
    const response = await getPropiedades()
    propiedades.value = response.data.data || []
  } catch (error) {
    console.error('Error al cargar propiedades:', error)
    alert('Error al cargar las propiedades')
  } finally {
    loading.value = false
  }
}

const confirmarEliminar = (propiedad) => {
  propiedadAEliminar.value = propiedad
}

const cancelarEliminar = () => {
  propiedadAEliminar.value = null
}

const eliminarPropiedad = async () => {
  try {
    await deletePropiedad(propiedadAEliminar.value.id)
    propiedades.value = propiedades.value.filter(p => p.id !== propiedadAEliminar.value.id)
    propiedadAEliminar.value = null
    alert('Propiedad eliminada exitosamente')
  } catch (error) {
    console.error('Error al eliminar:', error)
    alert('Error al eliminar la propiedad')
  }
}

onMounted(() => {
  cargarPropiedades()
})
</script>

<style scoped>
.admin-propiedades {
  max-width: 1400px;
}

.page-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 2rem;
}

.page-header h2 {
  font-size: 2rem;
  color: #1a1a1a;
  margin: 0;
}

.btn-primary {
  background: #ff6b35;
  color: white;
  padding: 0.875rem 1.5rem;
  border-radius: 8px;
  text-decoration: none;
  font-weight: 600;
  transition: all 0.3s;
  display: inline-block;
  border: none;
  cursor: pointer;
}

.btn-primary:hover {
  background: #e55a2b;
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(255, 107, 53, 0.3);
}

.filters {
  display: flex;
  gap: 1rem;
  margin-bottom: 2rem;
}

.filter-btn {
  padding: 0.75rem 1.5rem;
  border: 2px solid #ddd;
  background: white;
  border-radius: 8px;
  cursor: pointer;
  font-weight: 600;
  transition: all 0.3s;
}

.filter-btn.active {
  background: #ff6b35;
  color: white;
  border-color: #ff6b35;
}

.filter-btn:hover:not(.active) {
  border-color: #ff6b35;
  color: #ff6b35;
}

.loading,
.empty {
  text-align: center;
  padding: 4rem 2rem;
  color: #666;
}

.empty p {
  margin-bottom: 2rem;
  font-size: 1.2rem;
}

.propiedades-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
  gap: 2rem;
}

.propiedad-card {
  background: white;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
  transition: transform 0.3s, box-shadow 0.3s;
}

.propiedad-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
}

.card-image {
  position: relative;
  height: 220px;
  overflow: hidden;
  background: #f0f0f0;
}

.card-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.no-image {
  width: 100%;
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #999;
  font-size: 1.1rem;
}

.badges {
  position: absolute;
  top: 1rem;
  right: 1rem;
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
  align-items: flex-end;
}

.badge {
  padding: 0.4rem 0.8rem;
  border-radius: 6px;
  font-size: 0.85rem;
  font-weight: 600;
  backdrop-filter: blur(10px);
}

.badge-venta {
  background: rgba(76, 175, 80, 0.9);
  color: white;
}

.badge-alquiler {
  background: rgba(33, 150, 243, 0.9);
  color: white;
}

.badge-destacada {
  background: rgba(255, 193, 7, 0.9);
  color: #333;
}

.badge-inactive {
  background: rgba(158, 158, 158, 0.9);
  color: white;
}

.card-content {
  padding: 1.5rem;
}

.card-content h3 {
  font-size: 1.3rem;
  margin: 0 0 0.5rem 0;
  color: #1a1a1a;
}

.ubicacion {
  color: #666;
  margin: 0 0 1rem 0;
  font-size: 0.95rem;
}

.features {
  display: flex;
  gap: 1rem;
  margin-bottom: 1rem;
  padding-bottom: 1rem;
  border-bottom: 1px solid #eee;
}

.features span {
  font-size: 0.9rem;
  color: #666;
}

.precio {
  font-size: 1.5rem;
  font-weight: bold;
  color: #ff6b35;
  margin-bottom: 1.5rem;
}

.precio span {
  font-size: 1rem;
  color: #666;
}

.card-actions {
  display: flex;
  gap: 0.75rem;
}

.btn-edit,
.btn-delete {
  flex: 1;
  padding: 0.75rem;
  border-radius: 8px;
  text-decoration: none;
  text-align: center;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s;
  border: none;
  font-size: 0.95rem;
}

.btn-edit {
  background: #2196F3;
  color: white;
}

.btn-edit:hover {
  background: #1976D2;
}

.btn-delete {
  background: #f44336;
  color: white;
}

.btn-delete:hover {
  background: #d32f2f;
}

/* Modal */
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.7);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
}

.modal {
  background: white;
  padding: 2rem;
  border-radius: 12px;
  max-width: 500px;
  width: 90%;
}

.modal h3 {
  margin: 0 0 1rem 0;
  color: #1a1a1a;
}

.modal p {
  margin: 0.5rem 0;
  color: #666;
}

.modal .warning {
  color: #f44336;
  font-weight: 600;
  margin: 1rem 0;
}

.modal-actions {
  display: flex;
  gap: 1rem;
  margin-top: 2rem;
}

.btn-cancel {
  flex: 1;
  padding: 0.875rem;
  border: 2px solid #ddd;
  background: white;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s;
}

.btn-cancel:hover {
  border-color: #999;
  background: #f5f5f5;
}

@media (max-width: 768px) {
  .page-header {
    flex-direction: column;
    align-items: flex-start;
    gap: 1rem;
  }
  
  .filters {
    flex-direction: column;
  }
  
  .propiedades-grid {
    grid-template-columns: 1fr;
  }
}
</style>
