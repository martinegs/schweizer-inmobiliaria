<template>
  <section id="nosotros" class="about">
    <div class="container">
      <h2 class="section-title">Sobre Nosotros</h2>
      <div class="about-content">
        <div class="about-card">
          <img src="/img/experiencia.jpg" alt="50 Años de Experiencia" class="card-icon-img">
          <h3>50 Años de Experiencia</h3>
          <p>Medio siglo construyendo confianza y desarrollos inmobiliarios de calidad en Mendoza</p>
        </div>
        <div class="about-card">
          <img src="/img/calidad.jpg" alt="Desarrollos de Calidad" class="card-icon-img">
          <h3>Desarrollos de Calidad</h3>
          <p>Proyectos inmobiliarios pensados para tu inversión y crecimiento patrimonial</p>
        </div>
        <div class="about-card">
          <img src="/img/compromiso1.jpg" alt="Compromiso" class="card-icon-img">
          <h3>Compromiso</h3>
          <p>Acompañamos cada proyecto desde su inicio hasta la entrega final</p>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
</script>

<style scoped>
.about {
  padding: 60px 0;
  background: #0a0a0a;
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
  margin-bottom: 3rem;
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

.about-content {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  gap: 2rem;
}

.about-card {
  background: linear-gradient(145deg, #1a1a1a, #0f0f0f);
  padding: 2.5rem;
  border-radius: 15px;
  text-align: center;
  box-shadow: 0 8px 20px rgba(0, 0, 0, 0.5);
  transition: transform 0.3s, box-shadow 0.3s, border 0.3s;
  border: 1px solid rgba(255, 107, 53, 0.2);
  position: relative;
  overflow: hidden;
}

.about-card::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 3px;
  background: linear-gradient(90deg, transparent, #ff6b35, transparent);
  transform: scaleX(0);
  transition: transform 0.3s;
}

.about-card:hover {
  transform: translateY(-8px);
  box-shadow: 0 12px 30px rgba(255, 107, 53, 0.3);
  border-color: #ff6b35;
}

.about-card:hover::before {
  transform: scaleX(1);
}

.icon {
  font-size: 4rem;
  margin-bottom: 1rem;
}

.card-icon-img {
  width: 100%;
  height: 200px;
  object-fit: cover;
  border-radius: 10px;
  margin-bottom: 1rem;
}

.about-card h3 {
  color: #ff6b35;
  margin-bottom: 1rem;
  font-size: 1.5rem;
}

.about-card p {
  color: #cccccc;
  line-height: 1.8;
}

@media (max-width: 768px) {
  .about {
    padding: 60px 0;
  }
  
  .section-title {
    font-size: 2rem;
  }
}
</style>
