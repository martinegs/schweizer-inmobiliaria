<template>
  <footer id="contacto" class="footer">
    <div class="container">
      <div class="footer-content">
        <div class="footer-section footer-logo-section">
          <img src="/img/2.png" alt="Schweizer Desarrollos" class="footer-logo">
          <p class="footer-tagline">Construimos tu próxima inversión</p>
        </div>
        
        <div class="footer-section">
          <h3>Contacto</h3>
          <div class="contact-info">
            <p><a href="https://wa.me/542613725657" target="_blank" class="contact-link">0261 372-5657</a></p>
            <p><a href="mailto:gerenciageneral@schweizerinmobiliaria.com" class="contact-link">gerenciageneral@schweizerinmobiliaria.com</a></p>
            <p><a href="https://www.google.com/maps/search/?api=1&query=Teniente+1°+Ibañez+N°99,+Godoy+Cruz,+Mendoza,+Argentina" target="_blank" class="contact-link">Teniente 1° Ibañez N°99<br>Godoy Cruz, Mendoza<br>Argentina 5505</a></p>
          </div>
        </div>
      </div>
      
      <div class="footer-bottom">
        <p>&copy; {{ currentYear }} Schweizer Desarrollos - Todos los derechos reservados</p>
      </div>
    </div>
  </footer>
</template>

<script setup>
import { ref } from 'vue'

const currentYear = ref(new Date().getFullYear())
</script>

<style scoped>
.footer {
  background: #1a1a1a;
  color: white;
  padding: 60px 0 20px;
}

.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 20px;
}

.footer-content {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 3rem;
  margin-bottom: 3rem;
}

.footer-logo-section {
  text-align: center;
}

.footer-logo {
  height: 80px;
  width: auto;
  margin-bottom: 1rem;
  filter: drop-shadow(0 2px 8px rgba(255, 107, 53, 0.3));
  display: inline-block;
}

.footer-tagline {
  opacity: 0.9;
  font-style: italic;
  font-size: 1.2rem;
  margin: 0;
}

.footer-section h3 {
  margin-bottom: 1rem;
  color: #ff6b35;
  font-size: 1.3rem;
}

.contact-info p {
  margin: 0.5rem 0;
  opacity: 0.9;
}

.contact-link {
  color: white;
  text-decoration: none;
  transition: color 0.3s ease;
}

.contact-link:hover {
  color: #ff6b35;
}

.footer-section p {
  margin: 0.5rem 0;
  opacity: 0.9;
}

.footer-bottom {
  border-top: 1px solid rgba(255, 255, 255, 0.1);
  padding-top: 20px;
  text-align: center;
  opacity: 0.8;
}

@media (max-width: 768px) {
  .footer {
    padding: 40px 0 20px;
  }
  
  .footer-content {
    grid-template-columns: 1fr;
    gap: 2rem;
  }
}
</style>
