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
        
        <button type="submit" class="submit-button">
          Enviar mensaje
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
  if (savedSubject) {
    formData.value.subject = savedSubject
    sessionStorage.removeItem('contactSubject')
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

const handleSubmit = () => {
  const subject = `Contacto desde web - ${formData.value.subject}`
  const body = `
Nombre: ${formData.value.name}
Email: ${formData.value.email}
Teléfono: ${formData.value.phone}
Asunto: ${formData.value.subject}

Mensaje:
${formData.value.message}
  `.trim()
  
  // Reemplaza con tu email
  const mailtoLink = `mailto:gerenciageneral@schweizerinmobiliaria.com?subject=${encodeURIComponent(subject)}&body=${encodeURIComponent(body)}`
  
  window.location.href = mailtoLink
  
  // Resetear formulario
  formData.value = {
    name: '',
    email: '',
    phone: '',
    subject: '',
    message: ''
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
