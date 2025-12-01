<template>
  <section id="inicio" class="hero">
    <div class="container">
      <div class="hero-content">
        <div class="hero-text">
          <transition name="fade" mode="out-in">
            <div v-if="currentSlide === 0" key="slide1" class="slide">
              <h1 class="hero-title">Desarrollo inmobiliario</h1>
              <p class="hero-subtitle">Construimos tu próxima inversión con 50 años de experiencia.</p>
              <a @click="goToContact('Solicitar Presupuesto')" class="cta-button">Solicitar Presupuesto</a>
            </div>
            <div v-else-if="currentSlide === 1" key="slide2" class="slide">
              <h1 class="hero-title">Vender una propiedad</h1>
              <p class="hero-subtitle">Te ayudamos a vender tu propiedad al mejor precio.</p>
              <a @click="goToContact('Vender Propiedad')" class="cta-button">Vender Propiedad</a>
            </div>
            <div v-else-if="currentSlide === 2" key="slide3" class="slide">
              <h1 class="hero-title">Comprar una propiedad</h1>
              <p class="hero-subtitle">Encontrá la propiedad perfecta para vos.</p>
              <a @click="goToContact('Comprar Propiedad')" class="cta-button">Comprar Propiedad</a>
            </div>
            <div v-else key="slide4" class="slide">
              <h1 class="hero-title">Alquilar una propiedad</h1>
              <p class="hero-subtitle">El alquiler ideal para vos o tu familia.</p>
              <a @click="goToContact('Alquilar Propiedad')" class="cta-button">Buscar Alquiler</a>
            </div>
          </transition>
          
          <div class="carousel-dots">
            <button 
              v-for="(slide, index) in 4" 
              :key="index"
              @click="currentSlide = index"
              :class="['dot', { active: currentSlide === index }]"
              :aria-label="`Ir a slide ${index + 1}`"
            ></button>
          </div>
        </div>
        <div class="hero-image">
          <img src="/img/principal.jpg" alt="Schweizer Desarrollos" class="hero-building-img">
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'

const currentSlide = ref(0)
let intervalId = null

const goToContact = (subject) => {
  // Guardar el asunto en sessionStorage
  sessionStorage.setItem('contactSubject', subject)
  // Navegar al formulario con smooth scroll
  const element = document.getElementById('contacto-form')
  if (element) {
    element.scrollIntoView({ behavior: 'smooth' })
  }
}

const startCarousel = () => {
  intervalId = setInterval(() => {
    currentSlide.value = (currentSlide.value + 1) % 4
  }, 5000) // Cambia cada 5 segundos
}

onMounted(() => {
  startCarousel()
})

onUnmounted(() => {
  if (intervalId) {
    clearInterval(intervalId)
  }
})
</script>

<style scoped>
.hero {
  background: #0a0a0a;
  color: white;
  padding: 120px 0;
}

.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 20px;
}

.hero-content {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 4rem;
  align-items: center;
}

.hero-title {
  font-size: 3rem;
  font-weight: bold;
  margin-bottom: 1rem;
  line-height: 1.2;
  background: linear-gradient(135deg, #ffffff 0%, #ff6b35 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

.hero-subtitle {
  font-size: 1.5rem;
  margin-bottom: 2rem;
  opacity: 0.95;
}

.cta-button {
  display: inline-block;
  background: linear-gradient(135deg, #ff6b35 0%, #ff8c5a 100%);
  color: #000000;
  padding: 1.2rem 3rem;
  border-radius: 50px;
  text-decoration: none;
  font-weight: 700;
  font-size: 1.1rem;
  transition: transform 0.3s, box-shadow 0.3s;
  border: none;
  box-shadow: 0 4px 15px rgba(255, 107, 53, 0.4);
  position: relative;
  overflow: hidden;
}

.cta-button::before {
  content: '';
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
  transition: left 0.5s;
}

.cta-button:hover {
  transform: translateY(-3px);
  box-shadow: 0 8px 25px rgba(255, 107, 53, 0.6);
}

.cta-button:hover::before {
  left: 100%;
}

.hero-image {
  display: flex;
  justify-content: center;
  align-items: center;
}

.hero-building-img {
  max-width: 100%;
  height: auto;
  border-radius: 15px;
  box-shadow: 0 10px 40px rgba(255, 107, 53, 0.3);
  border: 2px solid rgba(255, 107, 53, 0.3);
}

.slide {
  animation: slideIn 0.5s ease-out;
}

@keyframes slideIn {
  from {
    opacity: 0;
    transform: translateX(20px);
  }
  to {
    opacity: 1;
    transform: translateX(0);
  }
}

.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.5s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

.carousel-dots {
  display: flex;
  gap: 0.75rem;
  margin-top: 2rem;
  justify-content: flex-start;
}

.dot {
  width: 12px;
  height: 12px;
  border-radius: 50%;
  background: rgba(255, 255, 255, 0.3);
  border: 2px solid rgba(255, 107, 53, 0.5);
  cursor: pointer;
  transition: all 0.3s;
  padding: 0;
}

.dot:hover {
  background: rgba(255, 107, 53, 0.6);
  transform: scale(1.2);
}

.dot.active {
  background: #ff6b35;
  width: 32px;
  border-radius: 6px;
}

@keyframes float {
  0%, 100% {
    transform: translateY(0px);
  }
  50% {
    transform: translateY(-20px);
  }
}

@media (max-width: 768px) {
  .hero {
    padding: 60px 0;
  }
  
  .hero-content {
    grid-template-columns: 1fr;
    gap: 2rem;
    text-align: center;
  }
  
  .hero-title {
    font-size: 2rem;
  }
  
  .hero-subtitle {
    font-size: 1.2rem;
  }
  
  .hero-building-img {
    max-width: 100%;
    margin-top: 2rem;
  }
}
</style>
