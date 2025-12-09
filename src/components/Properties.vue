<template>
  <section v-if="!loading && properties.length > 0" id="propiedades" class="properties">
    <div class="container">
      <h2 class="section-title">Propiedades Disponibles</h2>
      <p class="section-subtitle">Encontrá tu próxima inversión o el hogar ideal</p>
      
      <!-- Filtros -->
      <div class="filters">
        <button 
          :class="['filter-btn', { active: filterType === 'todas' }]"
          @click="filterType = 'todas'"
        >
          Todas
        </button>
        <button 
          :class="['filter-btn', { active: filterType === 'venta' }]"
          @click="filterType = 'venta'"
        >
          En Venta
        </button>
        <button 
          :class="['filter-btn', { active: filterType === 'alquiler' }]"
          @click="filterType = 'alquiler'"
        >
          En Alquiler
        </button>
      </div>

      <!-- Grid de propiedades -->
      <div class="properties-grid">
        <div 
          v-for="property in filteredProperties" 
          :key="property.id"
          class="property-card"
          @click="openModal(property)"
        >
          <div class="property-image">
            <img :src="property.image" :alt="property.title">
            <span class="property-badge" :class="property.type">
              {{ property.type === 'venta' ? 'Venta' : 'Alquiler' }}
            </span>
          </div>
          
          <div class="property-content">
            <h3 class="property-title">{{ property.title }}</h3>
            <p class="property-location">{{ property.location }}</p>
            
            <div class="property-features">
              <span class="feature">
                {{ property.bedrooms }} dorm
              </span>
              <span class="feature">
                {{ property.bathrooms }} baños
              </span>
              <span class="feature">
                {{ property.size }}m²
              </span>
            </div>
            
            <div class="property-footer">
              <span class="property-price">{{ property.price }}</span>
              <button class="contact-btn" @click.stop="contactProperty(property)">
                Consultar
              </button>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Mensaje si no hay propiedades -->
      <div v-if="filteredProperties.length === 0" class="no-properties">
        <p>No hay propiedades disponibles en esta categoría.</p>
      </div>
    </div>

    <!-- Modal de Detalles -->
    <div v-if="selectedProperty" class="modal-overlay" @click="closeModal">
      <div class="modal-content" @click.stop>
        <button class="modal-close" @click="closeModal">×</button>
        
        <div class="modal-body">
          <!-- Imagen principal con navegación -->
          <div class="modal-image">
            <img 
              :src="getCurrentImage()" 
              :alt="selectedProperty.title"
              @click="openImageFullscreen"
              class="clickable-image"
            >
            <span class="property-badge" :class="selectedProperty.type">
              {{ selectedProperty.type === 'venta' ? 'En Venta' : 'En Alquiler' }}
            </span>
            
            <!-- Controles de navegación de imágenes -->
            <div v-if="selectedProperty.images && selectedProperty.images.length > 1" class="image-navigation">
              <button 
                class="nav-btn prev" 
                @click="prevImage" 
                :disabled="currentImageIndex === 0"
              >
                ‹
              </button>
              <div class="image-counter">
                {{ currentImageIndex + 1 }} / {{ selectedProperty.images.length }}
              </div>
              <button 
                class="nav-btn next" 
                @click="nextImage"
                :disabled="currentImageIndex === selectedProperty.images.length - 1"
              >
                ›
              </button>
            </div>
          </div>

          <!-- Información -->
          <div class="modal-info">
            <h2 class="modal-title">{{ selectedProperty.title }}</h2>
            <p class="modal-location">{{ selectedProperty.location }}</p>
            
            <div class="modal-price">
              {{ selectedProperty.price }}
            </div>

            <div class="modal-features">
              <div class="feature-item">
                <div class="feature-details">
                  <strong>{{ selectedProperty.bedrooms }}</strong>
                  <span>Dormitorios</span>
                </div>
              </div>
              <div class="feature-item">
                <div class="feature-details">
                  <strong>{{ selectedProperty.bathrooms }}</strong>
                  <span>Baños</span>
                </div>
              </div>
              <div class="feature-item">
                <div class="feature-details">
                  <strong>{{ selectedProperty.size }}m²</strong>
                  <span>Superficie</span>
                </div>
              </div>
            </div>

            <div class="modal-description" v-if="selectedProperty.description">
              <h3>Descripción</h3>
              <p>{{ selectedProperty.description }}</p>
            </div>

            <div class="modal-actions">
              <button class="btn-contact-modal" @click="contactProperty(selectedProperty)">
                Consultar por esta propiedad
              </button>
              <button class="btn-whatsapp" @click="contactWhatsApp(selectedProperty)">
                WhatsApp
              </button>
            </div>

            <div class="modal-map" v-if="selectedProperty.latitud && selectedProperty.longitud">
              <h3>Ubicación</h3>
              <div :id="'modal-map-' + selectedProperty.id" class="property-map"></div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Overlay de imagen ampliada -->
    <div v-if="imageFullscreen" class="fullscreen-overlay" @click="closeImageFullscreen">
      <button class="fullscreen-close" @click="closeImageFullscreen">×</button>
      <img :src="getCurrentImage()" :alt="selectedProperty?.title" class="fullscreen-image">
      
      <!-- Navegación en pantalla completa -->
      <div v-if="selectedProperty?.images && selectedProperty.images.length > 1" class="fullscreen-navigation">
        <button 
          class="fullscreen-nav-btn prev" 
          @click.stop="prevImage" 
          :disabled="currentImageIndex === 0"
        >
          ‹
        </button>
        <div class="fullscreen-counter">
          {{ currentImageIndex + 1 }} / {{ selectedProperty.images.length }}
        </div>
        <button 
          class="fullscreen-nav-btn next" 
          @click.stop="nextImage"
          :disabled="currentImageIndex === selectedProperty.images.length - 1"
        >
          ›
        </button>
      </div>
    </div>
  </section>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { getPropiedades } from '../api'

const filterType = ref('todas')
const properties = ref([])
const loading = ref(true)
const selectedProperty = ref(null)
const currentImageIndex = ref(0)
const imageFullscreen = ref(false)

// Cargar propiedades desde la API
onMounted(async () => {
  loading.value = true
  try {
    const response = await getPropiedades()
    if (response.data.success) {
      // Transformar datos de la API al formato del componente
      properties.value = response.data.data.map(p => {
        const divisa = p.divisa === 'USD' ? 'USD' : '$'
        const precioFormateado = Number(p.precio).toLocaleString('es-AR')
        
        return {
          id: p.id,
          title: p.titulo,
          description: p.descripcion || '',
          location: p.ubicacion,
          type: p.tipo,
          divisa: p.divisa,
          latitud: p.latitud ? parseFloat(p.latitud) : null,
          longitud: p.longitud ? parseFloat(p.longitud) : null,
          price: p.tipo === 'venta' 
            ? `${divisa} ${precioFormateado}` 
            : `${divisa} ${precioFormateado}/mes`,
          bedrooms: p.dormitorios,
          bathrooms: p.banos,
          size: Number(p.superficie),
          images: p.imagenes && p.imagenes.length > 0
            ? p.imagenes.map(img => `${import.meta.env.VITE_API_URL.replace('/api', '')}/uploads/${img}`)
            : ['/img/placeholder.jpg'],
          image: p.imagenes && p.imagenes.length > 0 
            ? `${import.meta.env.VITE_API_URL.replace('/api', '')}/uploads/${p.imagenes[0]}`
            : '/img/placeholder.jpg'
        }
      })
    }
  } catch (error) {
    console.error('Error al cargar propiedades:', error)
    // Datos de ejemplo si falla la API
    properties.value = [
      {
        id: 1,
        title: 'Departamento 2 ambientes',
        description: 'Hermoso departamento con excelente ubicación, luminoso y recientemente refaccionado. Ideal para inversión o primera vivienda.',
        location: 'Godoy Cruz, Mendoza',
        type: 'venta',
        price: 'USD 85.000',
        bedrooms: 1,
        bathrooms: 1,
        size: 45,
        image: '/img/depto1.jpg'
      },
      {
        id: 2,
        title: 'Casa 3 dormitorios',
        description: 'Amplia casa familiar con patio, quincho y cochera. Excelente estado de conservación. Zona tranquila y segura.',
        location: 'Luján de Cuyo, Mendoza',
        type: 'venta',
        price: 'USD 180.000',
        bedrooms: 3,
        bathrooms: 2,
        size: 120,
        image: '/img/casa1.jpg'
      },
      {
        id: 3,
        title: 'Departamento moderno',
        description: 'Departamento totalmente amoblado y equipado. Edificio con seguridad 24hs y amenities. Disponible inmediatamente.',
        location: 'Capital, Mendoza',
        type: 'alquiler',
        price: '$200.000/mes',
        bedrooms: 2,
        bathrooms: 1,
        size: 60,
        image: '/img/depto2.jpg'
      },
      {
        id: 4,
        title: 'Casa amplia con patio',
        description: 'Casa espaciosa con gran patio y galería. Perfecta para familias. Incluye cochera para 2 autos. Excelente ubicación.',
        location: 'Guaymallén, Mendoza',
        type: 'alquiler',
        price: '$250.000/mes',
        bedrooms: 3,
        bathrooms: 2,
        size: 150,
        image: '/img/casa2.jpg'
      }
    ]
  } finally {
    loading.value = false
  }
})

const filteredProperties = computed(() => {
  if (filterType.value === 'todas') {
    return properties.value
  }
  return properties.value.filter(p => p.type === filterType.value)
})

const contactProperty = (property) => {
  // Restaurar scroll PRIMERO
  document.body.style.overflow = ''
  
  // Guardar info de la propiedad y redirigir al formulario
  const subject = property.type === 'venta' ? 'Comprar Propiedad' : 'Alquilar Propiedad'
  sessionStorage.setItem('contactSubject', subject)
  sessionStorage.setItem('propertyInfo', `Consulta sobre: ${property.title} - ${property.location}`)
  
  // Cerrar modal
  selectedProperty.value = null
  
  // Esperar un momento antes de hacer scroll para que el modal se cierre
  setTimeout(() => {
    const element = document.getElementById('contacto-form')
    if (element) {
      element.scrollIntoView({ behavior: 'smooth' })
    }
  }, 100)
}

const openModal = (property) => {
  selectedProperty.value = property
  currentImageIndex.value = 0
  // Bloquear scroll del body
  document.body.style.overflow = 'hidden'
  
  // Cargar mapa si hay coordenadas
  if (property.latitud && property.longitud) {
    setTimeout(() => initPropertyMap(property), 100)
  }
}

const closeModal = () => {
  selectedProperty.value = null
  currentImageIndex.value = 0
  // Restaurar scroll del body
  document.body.style.overflow = ''
}

const getCurrentImage = () => {
  if (!selectedProperty.value || !selectedProperty.value.images) {
    return selectedProperty.value?.image || '/img/placeholder.jpg'
  }
  return selectedProperty.value.images[currentImageIndex.value]
}

const nextImage = () => {
  if (selectedProperty.value && selectedProperty.value.images) {
    if (currentImageIndex.value < selectedProperty.value.images.length - 1) {
      currentImageIndex.value++
    }
  }
}

const prevImage = () => {
  if (currentImageIndex.value > 0) {
    currentImageIndex.value--
  }
}

const contactWhatsApp = (property) => {
  const phone = '5492613725657'
  const url = `https://wa.me/${phone}`
  window.open(url, '_blank')
}

const openImageFullscreen = () => {
  imageFullscreen.value = true
}

const closeImageFullscreen = () => {
  imageFullscreen.value = false
}

const initPropertyMap = (property) => {
  // Cargar Leaflet si no está cargado
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
    script.onload = () => createPropertyMap(property)
    document.head.appendChild(script)
  } else {
    createPropertyMap(property)
  }
}

const createPropertyMap = (property) => {
  const mapElement = document.getElementById(`modal-map-${property.id}`)
  if (!mapElement) return
  
  const map = window.L.map(mapElement).setView([property.latitud, property.longitud], 15)
  
  window.L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap contributors'
  }).addTo(map)
  
  // Marcador en la ubicación
  window.L.marker([property.latitud, property.longitud])
    .addTo(map)
    .bindPopup(property.title)
    .openPopup()
}
</script>

<style scoped>
.properties {
  padding: 80px 0;
  background: #000000;
}

.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 20px;
}

.section-title {
  text-align: center;
  font-size: 2.5rem;
  color: #ffffff;
  margin-bottom: 1rem;
  position: relative;
  display: inline-block;
  width: 100%;
}

.section-title::after {
  content: '';
  position: absolute;
  bottom: -10px;
  left: 50%;
  transform: translateX(-50%);
  width: 100px;
  height: 3px;
  background: linear-gradient(90deg, transparent, #ff6b35, transparent);
}

.section-subtitle {
  text-align: center;
  color: #cccccc;
  margin-bottom: 3rem;
  font-size: 1.1rem;
}

.filters {
  display: flex;
  justify-content: center;
  gap: 1rem;
  margin-bottom: 3rem;
  flex-wrap: wrap;
}

.filter-btn {
  padding: 0.8rem 2rem;
  background: #1a1a1a;
  border: 2px solid rgba(255, 107, 53, 0.3);
  color: #ffffff;
  border-radius: 50px;
  font-size: 1rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s;
}

.filter-btn:hover {
  border-color: #ff6b35;
  transform: translateY(-2px);
}

.filter-btn.active {
  background: linear-gradient(135deg, #ff6b35 0%, #ff8c5a 100%);
  color: #000000;
  border-color: #ff6b35;
}

.properties-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 2rem;
  margin-bottom: 2rem;
}

.property-card {
  background: linear-gradient(145deg, #1a1a1a, #0f0f0f);
  border-radius: 15px;
  overflow: hidden;
  border: 1px solid rgba(255, 107, 53, 0.2);
  transition: transform 0.3s, box-shadow 0.3s;
  cursor: pointer;
}

.property-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 10px 30px rgba(255, 107, 53, 0.3);
  border-color: #ff6b35;
}

.property-image {
  position: relative;
  width: 100%;
  height: 200px;
  overflow: hidden;
}

.property-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.3s;
}

.property-card:hover .property-image img {
  transform: scale(1.1);
}

.property-badge {
  position: absolute;
  top: 15px;
  right: 15px;
  padding: 0.5rem 1rem;
  border-radius: 20px;
  font-weight: 700;
  font-size: 0.85rem;
  text-transform: uppercase;
}

.property-badge.venta {
  background: #4caf50;
  color: white;
}

.property-badge.alquiler {
  background: #2196f3;
  color: white;
}

.property-content {
  padding: 1.5rem;
}

.property-title {
  font-size: 1.3rem;
  color: #ffffff;
  margin-bottom: 0.5rem;
}

.property-location {
  color: #cccccc;
  margin-bottom: 1rem;
  font-size: 0.95rem;
}

.property-features {
  display: flex;
  gap: 1rem;
  margin-bottom: 1.5rem;
  flex-wrap: wrap;
}

.feature {
  color: #ff6b35;
  font-size: 0.9rem;
  font-weight: 600;
}

.property-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
  border-top: 1px solid rgba(255, 107, 53, 0.2);
  padding-top: 1rem;
}

.property-price {
  font-size: 1.5rem;
  font-weight: 700;
  color: #ff6b35;
}

.contact-btn {
  padding: 0.6rem 1.5rem;
  background: linear-gradient(135deg, #ff6b35 0%, #ff8c5a 100%);
  color: #000000;
  border: none;
  border-radius: 25px;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.3s;
}

.contact-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 5px 15px rgba(255, 107, 53, 0.4);
}

.no-properties {
  text-align: center;
  padding: 3rem;
  color: #cccccc;
  font-size: 1.1rem;
}

/* Modal */
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.85);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 9999;
  padding: 20px;
  overflow-y: auto;
}

.modal-content {
  background: #1a1a1a;
  border-radius: 20px;
  max-width: 900px;
  width: 100%;
  max-height: 90vh;
  overflow-y: auto;
  position: relative;
  box-shadow: 0 20px 60px rgba(255, 107, 53, 0.3);
  border: 2px solid rgba(255, 107, 53, 0.3);
  animation: modalSlideIn 0.3s ease-out;
}

@keyframes modalSlideIn {
  from {
    opacity: 0;
    transform: translateY(-50px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.modal-close {
  position: absolute;
  top: 20px;
  right: 20px;
  width: 40px;
  height: 40px;
  border-radius: 50%;
  background: rgba(255, 107, 53, 0.9);
  color: white;
  border: none;
  font-size: 28px;
  line-height: 1;
  cursor: pointer;
  z-index: 10;
  transition: all 0.3s;
  display: flex;
  align-items: center;
  justify-content: center;
}

.modal-close:hover {
  background: #ff6b35;
  transform: rotate(90deg);
  box-shadow: 0 4px 12px rgba(255, 107, 53, 0.5);
}

.modal-body {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 0;
}

.modal-image {
  position: relative;
  height: 100%;
  min-height: 400px;
  overflow: hidden;
  border-radius: 20px 0 0 20px;
}

.modal-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.modal-image .property-badge {
  position: absolute;
  top: 20px;
  left: 20px;
  padding: 8px 20px;
  border-radius: 25px;
  font-weight: 700;
  font-size: 0.9rem;
  backdrop-filter: blur(10px);
}

.image-navigation {
  position: absolute;
  bottom: 20px;
  left: 50%;
  transform: translateX(-50%);
  display: flex;
  align-items: center;
  gap: 15px;
  background: rgba(0, 0, 0, 0.7);
  padding: 10px 20px;
  border-radius: 30px;
  backdrop-filter: blur(10px);
}

.nav-btn {
  background: rgba(255, 107, 53, 0.9);
  color: white;
  border: none;
  width: 35px;
  height: 35px;
  border-radius: 50%;
  font-size: 24px;
  cursor: pointer;
  transition: all 0.3s;
  display: flex;
  align-items: center;
  justify-content: center;
  line-height: 1;
}

.nav-btn:hover:not(:disabled) {
  background: #ff6b35;
  transform: scale(1.1);
}

.nav-btn:disabled {
  background: rgba(255, 107, 53, 0.3);
  cursor: not-allowed;
}

.image-counter {
  color: white;
  font-weight: 600;
  font-size: 0.9rem;
  min-width: 60px;
  text-align: center;
}

.clickable-image {
  cursor: zoom-in;
  transition: transform 0.3s;
}

.clickable-image:hover {
  transform: scale(1.02);
}

/* Pantalla completa de imagen */
.fullscreen-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.95);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 10000;
  cursor: zoom-out;
  animation: fadeIn 0.2s ease;
}

@keyframes fadeIn {
  from {
    opacity: 0;
  }
  to {
    opacity: 1;
  }
}

.fullscreen-image {
  max-width: 95vw;
  max-height: 95vh;
  object-fit: contain;
  box-shadow: 0 0 50px rgba(0, 0, 0, 0.8);
}

.fullscreen-close {
  position: absolute;
  top: 30px;
  right: 30px;
  width: 50px;
  height: 50px;
  border-radius: 50%;
  background: rgba(255, 107, 53, 0.9);
  color: white;
  border: none;
  font-size: 32px;
  line-height: 1;
  cursor: pointer;
  z-index: 10001;
  transition: all 0.3s;
  display: flex;
  align-items: center;
  justify-content: center;
}

.fullscreen-close:hover {
  background: #ff6b35;
  transform: rotate(90deg);
}

.fullscreen-navigation {
  position: absolute;
  bottom: 40px;
  left: 50%;
  transform: translateX(-50%);
  display: flex;
  align-items: center;
  gap: 20px;
  background: rgba(0, 0, 0, 0.8);
  padding: 15px 30px;
  border-radius: 50px;
  backdrop-filter: blur(10px);
  z-index: 10001;
}

.fullscreen-nav-btn {
  background: rgba(255, 107, 53, 0.9);
  color: white;
  border: none;
  width: 45px;
  height: 45px;
  border-radius: 50%;
  font-size: 28px;
  cursor: pointer;
  transition: all 0.3s;
  display: flex;
  align-items: center;
  justify-content: center;
  line-height: 1;
}

.fullscreen-nav-btn:hover:not(:disabled) {
  background: #ff6b35;
  transform: scale(1.15);
}

.fullscreen-nav-btn:disabled {
  background: rgba(255, 107, 53, 0.3);
  cursor: not-allowed;
}

.fullscreen-counter {
  color: white;
  font-weight: 700;
  font-size: 1.1rem;
  min-width: 80px;
  text-align: center;
}

.modal-info {
  padding: 40px;
  display: flex;
  flex-direction: column;
  gap: 25px;
}

.modal-title {
  font-size: 2rem;
  color: #ffffff;
  margin: 0;
  line-height: 1.2;
}

.modal-location {
  font-size: 1.1rem;
  color: #cccccc;
  margin: 0;
}

.modal-price {
  font-size: 2.5rem;
  font-weight: 800;
  color: #ff6b35;
  margin: 10px 0;
}

.modal-features {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 20px;
  padding: 20px 0;
  border-top: 1px solid rgba(255, 107, 53, 0.2);
  border-bottom: 1px solid rgba(255, 107, 53, 0.2);
}

.feature-item {
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
  gap: 8px;
}

.feature-icon {
  font-size: 2rem;
}

.feature-details {
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.feature-details strong {
  font-size: 1.2rem;
  color: #ffffff;
}

.feature-details span {
  font-size: 0.85rem;
  color: #999;
}

.modal-description {
  flex: 1;
}

.modal-description h3 {
  color: #ffffff;
  margin: 0 0 15px 0;
  font-size: 1.3rem;
}

.modal-description p {
  color: #cccccc;
  line-height: 1.7;
  margin: 0;
}

.modal-map {
  flex: 1;
}

.modal-map h3 {
  color: #ffffff;
  margin: 0 0 15px 0;
  font-size: 1.3rem;
}

.property-map {
  width: 100%;
  height: 300px;
  border-radius: 12px;
  overflow: hidden;
  border: 2px solid rgba(255, 107, 53, 0.3);
}

.modal-actions {
  display: flex;
  gap: 15px;
  margin-top: auto;
}

.btn-contact-modal,
.btn-whatsapp {
  flex: 1;
  padding: 16px 24px;
  border: none;
  border-radius: 12px;
  font-weight: 700;
  font-size: 1rem;
  cursor: pointer;
  transition: all 0.3s;
}

.btn-contact-modal {
  background: linear-gradient(135deg, #ff6b35 0%, #ff8c5a 100%);
  color: #000000;
}

.btn-contact-modal:hover {
  transform: translateY(-3px);
  box-shadow: 0 8px 20px rgba(255, 107, 53, 0.4);
}

.btn-whatsapp {
  background: linear-gradient(135deg, #25D366 0%, #128C7E 100%);
  color: white;
}

.btn-whatsapp:hover {
  transform: translateY(-3px);
  box-shadow: 0 8px 20px rgba(37, 211, 102, 0.4);
}

@media (max-width: 768px) {
  .properties {
    padding: 60px 0;
  }
  
  .section-title {
    font-size: 2rem;
  }
  
  .properties-grid {
    grid-template-columns: 1fr;
  }
  
  .filters {
    gap: 0.5rem;
  }
  
  .filter-btn {
    padding: 0.6rem 1.5rem;
    font-size: 0.9rem;
  }

  .modal-content {
    max-height: 95vh;
    margin: 10px;
  }

  .modal-body {
    grid-template-columns: 1fr;
  }

  .modal-image {
    border-radius: 20px 20px 0 0;
    min-height: 300px;
  }

  .modal-info {
    padding: 30px 20px;
  }

  .modal-title {
    font-size: 1.6rem;
  }

  .modal-price {
    font-size: 2rem;
  }

  .modal-features {
    grid-template-columns: repeat(3, 1fr);
    gap: 15px;
  }

  .modal-actions {
    flex-direction: column;
  }

  .modal-close {
    top: 15px;
    right: 15px;
    width: 35px;
    height: 35px;
    font-size: 24px;
  }
}
</style>
