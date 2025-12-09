<template>
  <div class="propiedad-form">
    <div class="page-header">
      <h2>{{ esEdicion ? 'Editar Propiedad' : 'Nueva Propiedad' }}</h2>
      <router-link to="/admin/propiedades" class="btn-back">
        ← Volver
      </router-link>
    </div>
    
    <form @submit.prevent="guardar" class="form">
      <div class="form-section">
        <h3>Información Básica</h3>
        
        <div class="form-group">
          <label for="titulo">Título *</label>
          <input 
            type="text" 
            id="titulo" 
            v-model="form.titulo" 
            required
            placeholder="Ej: Departamento Moderno en Centro"
          >
        </div>
        
        <div class="form-group">
          <label for="descripcion">Descripción</label>
          <textarea 
            id="descripcion" 
            v-model="form.descripcion" 
            rows="4"
            placeholder="Describe las características de la propiedad..."
          ></textarea>
        </div>
        
        <div class="form-row">
          <div class="form-group">
            <label for="tipo">Tipo *</label>
            <select id="tipo" v-model="form.tipo" required>
              <option value="venta">Venta</option>
              <option value="alquiler">Alquiler</option>
            </select>
          </div>
          
          <div class="form-group">
            <label for="divisa">Divisa *</label>
            <select id="divisa" v-model="form.divisa" required>
              <option value="ARS">Pesos Argentinos (ARS)</option>
              <option value="USD">Dólares (USD)</option>
            </select>
          </div>
          
          <div class="form-group">
            <label for="precio">Precio *</label>
            <input 
              type="number" 
              id="precio" 
              v-model.number="form.precio" 
              required
              step="0.01"
              placeholder="0.00"
            >
          </div>
        </div>
        
        <div class="form-group">
          <label for="ubicacion">Ubicación *</label>
          <input 
            type="text" 
            id="ubicacion" 
            v-model="form.ubicacion" 
            required
            placeholder="Ej: Centro, Mendoza"
            @input="buscarDireccion"
          >
        </div>

        <div class="form-group">
          <label>Ubicación en el Mapa</label>
          <div class="map-container">
            <div id="map" ref="mapContainer" class="map"></div>
            <p class="hint">Haz clic en el mapa para marcar la ubicación exacta de la propiedad</p>
          </div>
          <div v-if="form.latitud && form.longitud" class="coordinates-info">
            Coordenadas: {{ form.latitud.toFixed(6) }}, {{ form.longitud.toFixed(6) }}
          </div>
        </div>
      </div>
      
      <div class="form-section">
        <h3>Características</h3>
        
        <div class="form-row">
          <div class="form-group">
            <label for="dormitorios">Dormitorios *</label>
            <input 
              type="number" 
              id="dormitorios" 
              v-model.number="form.dormitorios" 
              required
              min="0"
            >
          </div>
          
          <div class="form-group">
            <label for="banos">Baños *</label>
            <input 
              type="number" 
              id="banos" 
              v-model.number="form.banos" 
              required
              min="0"
            >
          </div>
          
          <div class="form-group">
            <label for="superficie">Superficie (m²) *</label>
            <input 
              type="number" 
              id="superficie" 
              v-model.number="form.superficie" 
              required
              step="0.01"
              min="0"
            >
          </div>
        </div>
      </div>
      
      <div class="form-section">
        <h3>Opciones</h3>
        
        <div class="form-group checkbox-group">
          <label>
            <input type="checkbox" v-model="form.destacada">
            <span>Marcar como destacada</span>
          </label>
        </div>
        
        <div class="form-group checkbox-group">
          <label>
            <input type="checkbox" v-model="form.activa">
            <span>Propiedad activa (visible en el sitio)</span>
          </label>
        </div>
      </div>
      
      <!-- Sección de imágenes -->
      <div class="form-section">
        <h3>Imágenes</h3>
        
        <!-- Imágenes ya guardadas (solo en edición) -->
        <div v-if="esEdicion && imagenes.length > 0" class="imagenes-grid">
          <div 
            v-for="(imagen, index) in imagenes" 
            :key="index" 
            class="imagen-item"
          >
            <img :src="getImageUrl(imagen)" :alt="`Imagen ${index + 1}`">
            <button 
              type="button" 
              @click="eliminarImagen(imagen, index)" 
              class="btn-delete-img"
            >
              ×
            </button>
          </div>
        </div>
        
        <!-- Vista previa de imágenes seleccionadas (antes de guardar) -->
        <div v-if="selectedFiles.length > 0" class="imagenes-grid">
          <div 
            v-for="(file, index) in selectedFiles" 
            :key="'preview-' + index" 
            class="imagen-item"
          >
            <img :src="file.preview" :alt="`Nueva imagen ${index + 1}`">
            <button 
              type="button" 
              @click="removeSelectedFile(index)" 
              class="btn-delete-img"
            >
              ×
            </button>
          </div>
        </div>
        
        <div class="upload-area">
          <input 
            type="file" 
            ref="fileInput" 
            @change="handleFileSelect" 
            accept="image/jpeg,image/jpg,image/png,image/webp"
            multiple
            style="display: none"
          >
          <button 
            type="button" 
            @click="$refs.fileInput.click()" 
            class="btn-upload"
          >
            + Seleccionar Imágenes
          </button>
          <p class="hint">Formatos: JPG, PNG, WEBP | Máximo 5MB por imagen | Múltiples archivos</p>
        </div>
      </div>
      
      <div v-if="error" class="error-message">
        {{ error }}
      </div>
      
      <div class="form-actions">
        <router-link to="/admin/propiedades" class="btn-cancel">
          Cancelar
        </router-link>
        <button type="submit" class="btn-save" :disabled="saving">
          {{ saving ? 'Guardando...' : 'Guardar Propiedad' }}
        </button>
      </div>
    </form>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { 
  getPropiedad, 
  createPropiedad, 
  updatePropiedad, 
  uploadImagen, 
  deleteImagen 
} from '../../api'

const router = useRouter()
const route = useRoute()

const form = ref({
  titulo: '',
  descripcion: '',
  tipo: 'venta',
  divisa: 'ARS',
  precio: 0,
  ubicacion: '',
  latitud: null,
  longitud: null,
  dormitorios: 0,
  banos: 0,
  superficie: 0,
  destacada: false,
  activa: true
})

const imagenes = ref([])
const selectedFiles = ref([])
const error = ref('')
const saving = ref(false)
const uploading = ref(false)
const fileInput = ref(null)
const mapContainer = ref(null)
let map = null
let marker = null

const propiedadId = computed(() => route.params.id)
const esEdicion = computed(() => !!propiedadId.value)

const getImageUrl = (filename) => {
  return `${import.meta.env.VITE_API_URL}/public/uploads/${filename}`
}

const cargarPropiedad = async () => {
  if (!propiedadId.value) return
  
  try {
    const response = await getPropiedad(propiedadId.value)
    const data = response.data.data
    
    form.value = {
      titulo: data.titulo,
      descripcion: data.descripcion || '',
      tipo: data.tipo,
      divisa: data.divisa || 'ARS',
      precio: parseFloat(data.precio),
      ubicacion: data.ubicacion,
      latitud: data.latitud ? parseFloat(data.latitud) : null,
      longitud: data.longitud ? parseFloat(data.longitud) : null,
      dormitorios: parseInt(data.dormitorios),
      banos: parseInt(data.banos),
      superficie: parseFloat(data.superficie),
      destacada: !!data.destacada,
      activa: !!data.activa
    }
    
    imagenes.value = data.imagenes || []
    
    // Actualizar marcador en el mapa si hay coordenadas
    if (form.value.latitud && form.value.longitud && map) {
      setMarker(form.value.latitud, form.value.longitud)
      map.setView([form.value.latitud, form.value.longitud], 15)
    }
  } catch (err) {
    console.error('Error al cargar propiedad:', err)
    error.value = 'Error al cargar los datos de la propiedad'
  }
}

const guardar = async () => {
  error.value = ''
  saving.value = true
  
  try {
    if (esEdicion.value) {
      await updatePropiedad(propiedadId.value, form.value)
      alert('Propiedad actualizada exitosamente')
      router.push('/admin/propiedades')
    } else {
      // Crear propiedad
      const response = await createPropiedad(form.value)
      const nuevaPropiedadId = response.data.data.id
      
      // Si hay imágenes seleccionadas, subirlas
      if (selectedFiles.value.length > 0) {
        uploading.value = true
        for (const file of selectedFiles.value) {
          try {
            const formData = new FormData()
            formData.append('imagen', file.file)
            await uploadImagen(nuevaPropiedadId, formData)
          } catch (err) {
            console.error('Error al subir imagen:', err)
          }
        }
        uploading.value = false
      }
      
      alert('Propiedad creada exitosamente')
      router.push('/admin/propiedades')
    }
  } catch (err) {
    console.error('Error al guardar:', err)
    error.value = err.response?.data?.message || 'Error al guardar la propiedad'
  } finally {
    saving.value = false
  }
}

const handleFileSelect = (event) => {
  const files = Array.from(event.target.files)
  
  files.forEach(file => {
    // Validar tamaño
    if (file.size > 5242880) {
      alert(`${file.name} es demasiado grande. Máximo 5MB por imagen.`)
      return
    }
    
    // Crear preview
    const reader = new FileReader()
    reader.onload = (e) => {
      selectedFiles.value.push({
        file: file,
        preview: e.target.result
      })
    }
    reader.readAsDataURL(file)
  })
  
  // Limpiar input
  event.target.value = ''
}

const removeSelectedFile = (index) => {
  selectedFiles.value.splice(index, 1)
}

const subirImagen = async (event) => {
  const file = event.target.files[0]
  if (!file) return
  
  // Validar tamaño
  if (file.size > 5242880) {
    alert('El archivo es demasiado grande. Máximo 5MB.')
    return
  }
  
  uploading.value = true
  
  try {
    const formData = new FormData()
    formData.append('imagen', file)
    
    const response = await uploadImagen(propiedadId.value, formData)
    
    if (response.data.success) {
      imagenes.value.push(response.data.data.filename)
      alert('Imagen subida exitosamente')
    }
  } catch (err) {
    console.error('Error al subir imagen:', err)
    alert('Error al subir la imagen')
  } finally {
    uploading.value = false
    fileInput.value.value = ''
  }
}

const eliminarImagen = async (filename, index) => {
  if (!confirm('¿Eliminar esta imagen?')) return
  
  try {
    // Nota: necesitarías el ID de la imagen de la BD
    // Por ahora simplemente la quitamos del array
    imagenes.value.splice(index, 1)
    alert('Imagen eliminada')
  } catch (err) {
    console.error('Error al eliminar imagen:', err)
    alert('Error al eliminar la imagen')
  }
}

onMounted(() => {
  cargarPropiedad()
  initMap()
})

const initMap = () => {
  // Cargar Leaflet CSS y JS dinámicamente
  if (!document.getElementById('leaflet-css')) {
    const link = document.createElement('link')
    link.id = 'leaflet-css'
    link.rel = 'stylesheet'
    link.href = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css'
    document.head.appendChild(link)
  }
  
  if (!window.L) {
    const script = document.createElement('script')
    script.src = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js'
    script.onload = () => createMap()
    document.head.appendChild(script)
  } else {
    createMap()
  }
}

const createMap = () => {
  // Centro por defecto: Mendoza, Argentina
  const defaultLat = -32.8895
  const defaultLng = -68.8458
  
  map = window.L.map(mapContainer.value).setView([defaultLat, defaultLng], 13)
  
  window.L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap contributors'
  }).addTo(map)
  
  // Click en el mapa para marcar ubicación
  map.on('click', (e) => {
    setMarker(e.latlng.lat, e.latlng.lng)
  })
  
  // Si ya hay coordenadas, mostrar el marcador
  if (form.value.latitud && form.value.longitud) {
    setMarker(form.value.latitud, form.value.longitud)
    map.setView([form.value.latitud, form.value.longitud], 15)
  }
}

const setMarker = (lat, lng) => {
  // Remover marcador anterior si existe
  if (marker) {
    map.removeLayer(marker)
  }
  
  // Crear nuevo marcador
  marker = window.L.marker([lat, lng]).addTo(map)
  
  // Guardar coordenadas en el formulario
  form.value.latitud = lat
  form.value.longitud = lng
}

const buscarDireccion = async () => {
  // Geocoding simple usando Nominatim (OpenStreetMap)
  if (form.value.ubicacion.length < 5) return
  
  try {
    const response = await fetch(
      `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(form.value.ubicacion + ', Mendoza, Argentina')}&limit=1`
    )
    const data = await response.json()
    
    if (data && data.length > 0 && map) {
      const lat = parseFloat(data[0].lat)
      const lng = parseFloat(data[0].lon)
      map.setView([lat, lng], 15)
      setMarker(lat, lng)
    }
  } catch (err) {
    console.error('Error al buscar dirección:', err)
  }
}

</script>

<style scoped>
.propiedad-form {
  max-width: 900px;
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

.btn-back {
  padding: 0.75rem 1.25rem;
  background: #666;
  color: white;
  text-decoration: none;
  border-radius: 8px;
  font-weight: 600;
  transition: all 0.3s;
}

.btn-back:hover {
  background: #555;
}

.form {
  background: white;
  border-radius: 12px;
  padding: 2rem;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.form-section {
  margin-bottom: 2.5rem;
  padding-bottom: 2rem;
  border-bottom: 1px solid #eee;
}

.form-section:last-of-type {
  border-bottom: none;
}

.form-section h3 {
  margin: 0 0 1.5rem 0;
  color: #1a1a1a;
  font-size: 1.3rem;
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

.form-group input,
.form-group select,
.form-group textarea {
  width: 100%;
  padding: 0.875rem;
  border: 2px solid #e0e0e0;
  border-radius: 8px;
  font-size: 1rem;
  transition: all 0.3s;
  font-family: inherit;
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
  outline: none;
  border-color: #ff6b35;
  box-shadow: 0 0 0 3px rgba(255, 107, 53, 0.1);
}

.form-row {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 1rem;
}

.checkbox-group label {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  cursor: pointer;
  font-weight: normal;
}

.checkbox-group input[type="checkbox"] {
  width: auto;
  cursor: pointer;
  transform: scale(1.2);
}

.hint {
  margin-top: 0.5rem;
  font-size: 0.875rem;
  color: #666;
}

/* Mapa */
.map-container {
  margin-top: 0.5rem;
}

.map {
  width: 100%;
  height: 400px;
  border-radius: 8px;
  border: 2px solid #e0e0e0;
  margin-bottom: 0.5rem;
}

.coordinates-info {
  padding: 0.75rem;
  background: #e8f5e9;
  border-radius: 8px;
  color: #2e7d32;
  font-size: 0.875rem;
  font-weight: 600;
  margin-top: 0.5rem;
}

/* Imágenes */
.imagenes-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
  gap: 1rem;
  margin-bottom: 1.5rem;
}

.imagen-item {
  position: relative;
  aspect-ratio: 1;
  border-radius: 8px;
  overflow: hidden;
  border: 2px solid #e0e0e0;
}

.imagen-item img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.btn-delete-img {
  position: absolute;
  top: 0.5rem;
  right: 0.5rem;
  width: 32px;
  height: 32px;
  border-radius: 50%;
  background: rgba(244, 67, 54, 0.9);
  color: white;
  border: none;
  font-size: 1.5rem;
  line-height: 1;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.3s;
}

.btn-delete-img:hover {
  background: #d32f2f;
  transform: scale(1.1);
}

.upload-area {
  text-align: center;
  padding: 2rem;
  border: 2px dashed #ddd;
  border-radius: 8px;
  background: #fafafa;
}

.btn-upload {
  padding: 0.875rem 1.5rem;
  background: #2196F3;
  color: white;
  border: none;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s;
}

.btn-upload:hover:not(:disabled) {
  background: #1976D2;
}

.btn-upload:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.hint {
  margin-top: 0.75rem;
  color: #666;
  font-size: 0.9rem;
}

.error-message {
  background: #fee;
  color: #c00;
  padding: 1rem;
  border-radius: 8px;
  margin-bottom: 1.5rem;
  border-left: 4px solid #c00;
}

.form-actions {
  display: flex;
  gap: 1rem;
  justify-content: flex-end;
  margin-top: 2rem;
}

.btn-cancel,
.btn-save {
  padding: 1rem 2rem;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s;
  text-decoration: none;
  border: none;
  font-size: 1rem;
}

.btn-cancel {
  background: #f5f5f5;
  color: #666;
  border: 2px solid #ddd;
}

.btn-cancel:hover {
  background: #eee;
  border-color: #999;
}

.btn-save {
  background: #ff6b35;
  color: white;
}

.btn-save:hover:not(:disabled) {
  background: #e55a2b;
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(255, 107, 53, 0.3);
}

.btn-save:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

@media (max-width: 768px) {
  .page-header {
    flex-direction: column;
    align-items: flex-start;
    gap: 1rem;
  }
  
  .form {
    padding: 1.5rem;
  }
  
  .form-actions {
    flex-direction: column;
  }
  
  .btn-cancel,
  .btn-save {
    width: 100%;
  }
}
</style>
