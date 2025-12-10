<template>
  <section id="contacto-form" class="contact-form-section">
    <div class="container">
      <h2 class="section-title">Contactanos</h2>
      <p class="section-subtitle">Completá el formulario y nos pondremos en contacto a la brevedad</p>
      
      <form @submit.prevent="handleSubmit" class="contact-form">
        <div class="form-row">
          <div class="form-group">
            <label for="name">Nombre completo</label>
            <input 
              type="text" 
              id="name" 
              v-model="formData.name" 
              placeholder="Tu nombre"
              required
            >
          </div>
          
          <div class="form-group">
            <label for="email">Email</label>
            <input 
              type="email" 
              id="email" 
              v-model="formData.email" 
              placeholder="tu@email.com"
              required
            >
          </div>
        </div>
        
        <div class="form-row">
          <div class="form-group">
            <label for="phone">Teléfono</label>
            <input 
              type="tel" 
              id="phone" 
              v-model="formData.phone" 
              placeholder="Tu teléfono"
              required
            >
          </div>
          
          <div class="form-group">
            <label for="subject">Asunto</label>
            <select id="subject" v-model="formData.subject" required>
              <option value="">Seleccioná una opción</option>
              <option value="Solicitar Presupuesto">Solicitar Presupuesto</option>
              <option value="Vender Propiedad">Vender Propiedad</option>
              <option value="Comprar Propiedad">Comprar Propiedad</option>
              <option value="Alquilar Propiedad">Alquilar Propiedad</option>
              <option value="Información de proyectos">Información de proyectos</option>
              <option value="Consulta general">Consulta general</option>
              <option value="Otro">Otro</option>
            </select>
          </div>
        </div>
        
        <div class="form-group">
          <label for="message">Mensaje</label>
          <textarea 
            id="message" 
            v-model="formData.message" 
            placeholder="Contanos sobre tu proyecto o consulta..."
            rows="5"
            required
          ></textarea>
        </div>
        
        <div v-if="statusMessage" class="status-message" :class="{ success: statusMessage.includes('correctamente'), error: !statusMessage.includes('correctamente') }">
          {{ statusMessage }}
        </div>
        
        <button type="submit" class="submit-button" :disabled="sending">
          {{ sending ? 'Enviando...' : 'Enviar mensaje' }}
        </button>
      </form>
    </div>
  </section>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'

const formData = ref({
  name: '',
  email: '',
  phone: '',
  subject: '',
  message: ''
})

const checkSubject = () => {
  const savedSubject = sessionStorage.getItem('contactSubject')
  const savedPropertyInfo = sessionStorage.getItem('propertyInfo')
  
  if (savedSubject) {
    formData.value.subject = savedSubject
    sessionStorage.removeItem('contactSubject')
  }
  
  if (savedPropertyInfo) {
    formData.value.message = `Hola! ${savedPropertyInfo} está disponible. Me gustaría obtener más información.`
    sessionStorage.removeItem('propertyInfo')
  }
}

let intervalId = null

onMounted(() => {
  // Verificar inmediatamente
  checkSubject()
  
  // Verificar cada 100ms por si viene de un click
  intervalId = setInterval(checkSubject, 100)
})

onUnmounted(() => {
  if (intervalId) {
    clearInterval(intervalId)
  }
})

const sending = ref(false)
const statusMessage = ref('')

const handleSubmit = async () => {
  sending.value = true
  statusMessage.value = ''
  
  try {
    // Crear FormData para Web3Forms
    const formDataToSend = new FormData()
    formDataToSend.append('access_key', '5ab77381-6fa4-42be-b0c5-1ca31ecbebae') // Vas a obtener esto en el paso siguiente
    formDataToSend.append('name', formData.value.name)
    formDataToSend.append('email', formData.value.email)
    formDataToSend.append('phone', formData.value.phone)
    formDataToSend.append('subject', `Contacto desde web - ${formData.value.subject}`)
    formDataToSend.append('message', formData.value.message)
    
    const response = await fetch('https://api.web3forms.com/submit', {
      method: 'POST',
      body: formDataToSend
    })
    
    const result = await response.json()
    
    if (response.ok && result.success) {
      statusMessage.value = 'Mensaje enviado correctamente. Nos contactaremos pronto.'
      
      // Resetear formulario
      formData.value = {
        name: '',
        email: '',
        phone: '',
        subject: '',
        message: ''
      }
      
      // Limpiar mensaje después de 5 segundos
      setTimeout(() => {
        statusMessage.value = ''
      }, 5000)
    } else {
      statusMessage.value = result.message || 'Error al enviar el mensaje. Intenta nuevamente.'
    }
  } catch (error) {
    statusMessage.value = 'Error de conexión. Por favor intenta nuevamente.'
    console.error('Error:', error)
  } finally {
    sending.value = false
  }
}
</script>

<style scoped>
.contact-form-section {
  padding: 60px 0;
  background: #0a0a0a;
}

.container {
  max-width: 800px;
  margin: 0 auto;
  padding: 0 20px;
}

.section-title {
  text-align: center;
  font-size: 2.5rem;
  color: #ffffff;
  margin-bottom: 1rem;
}

.section-subtitle {
  text-align: center;
  color: #cccccc;
  margin-bottom: 3rem;
  font-size: 1.1rem;
}

.contact-form {
  background: linear-gradient(145deg, #1a1a1a, #0f0f0f);
  padding: 3rem;
  border-radius: 15px;
  border: 1px solid rgba(255, 107, 53, 0.2);
  box-shadow: 0 10px 40px rgba(0, 0, 0, 0.5);
}

.form-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1.5rem;
  margin-bottom: 1.5rem;
}

.form-group {
  display: flex;
  flex-direction: column;
}

.form-group label {
  color: #ff6b35;
  font-weight: 600;
  margin-bottom: 0.5rem;
  font-size: 0.95rem;
}

.form-group input,
.form-group select,
.form-group textarea {
  background: #0a0a0a;
  border: 2px solid rgba(255, 107, 53, 0.3);
  color: #ffffff;
  padding: 0.9rem;
  border-radius: 8px;
  font-size: 1rem;
  transition: border-color 0.3s, box-shadow 0.3s;
  font-family: inherit;
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
  outline: none;
  border-color: #ff6b35;
  box-shadow: 0 0 0 3px rgba(255, 107, 53, 0.1);
}

.form-group input::placeholder,
.form-group textarea::placeholder {
  color: #666666;
}

.form-group textarea {
  resize: vertical;
  min-height: 120px;
}

.submit-button {
  width: 100%;
  background: linear-gradient(135deg, #ff6b35 0%, #ff8c5a 100%);
  color: #000000;
  padding: 1.2rem 3rem;
  border: none;
  border-radius: 50px;
  font-weight: 700;
  font-size: 1.1rem;
  cursor: pointer;
  transition: transform 0.3s, box-shadow 0.3s;
  box-shadow: 0 4px 15px rgba(255, 107, 53, 0.4);
  margin-top: 1rem;
}

.submit-button:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 25px rgba(255, 107, 53, 0.6);
}

.submit-button:active {
  transform: translateY(0);
}

.submit-button:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.status-message {
  padding: 1rem;
  border-radius: 8px;
  margin-bottom: 1rem;
  text-align: center;
  font-weight: 600;
}

.status-message.success {
  background: rgba(76, 175, 80, 0.2);
  border: 2px solid #4caf50;
  color: #4caf50;
}

.status-message.error {
  background: rgba(244, 67, 54, 0.2);
  border: 2px solid #f44336;
  color: #f44336;
}

@media (max-width: 768px) {
  .contact-form-section {
    padding: 60px 0;
  }
  
  .section-title {
    font-size: 2rem;
  }
  
  .contact-form {
    padding: 2rem;
  }
  
  .form-row {
    grid-template-columns: 1fr;
    gap: 1.5rem;
  }
}
</style>
