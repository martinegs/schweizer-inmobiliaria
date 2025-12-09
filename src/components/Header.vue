<template>
  <header class="header">
    <div class="container">
      <div class="header-content">
        <div class="logo">
          <img src="/img/2.png" alt="Schweizer Desarrollos" class="logo-img">
        </div>
        <nav class="nav">
          <a href="#inicio" class="nav-link">Inicio</a>
          <a href="#nosotros" class="nav-link">Nosotros</a>
          <a href="#propiedades" class="nav-link">Propiedades</a>
          <a href="#contacto-form" class="nav-link">Contacto</a>
        </nav>
      </div>
    </div>
  </header>
</template>

<script setup>
</script>

<style scoped>
.header {
  background: #1a1a1a;
  box-shadow: 0 2px 10px rgba(255, 107, 53, 0.3);
  position: sticky;
  top: 0;
  z-index: 1000;
  border-bottom: 2px solid #ff6b35;
}

.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 20px;
}

.header-content {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1rem 0;
}

.logo-img {
  height: 80px;
  width: auto;
  filter: drop-shadow(0 2px 8px rgba(255, 107, 53, 0.3));
}

.nav {
  display: flex;
  gap: 2rem;
}

.nav-link {
  color: #ffffff;
  text-decoration: none;
  font-weight: 600;
  font-size: 1.3rem;
  transition: color 0.3s, transform 0.3s;
  position: relative;
}

.nav-link::after {
  content: '';
  position: absolute;
  bottom: -5px;
  left: 0;
  width: 0;
  height: 2px;
  background: #ff6b35;
  transition: width 0.3s;
}

.nav-link:hover {
  color: #ff6b35;
  transform: translateY(-2px);
}

.nav-link:hover::after {
  width: 100%;
}

@media (max-width: 768px) {
  .header-content {
    flex-direction: column;
    gap: 1rem;
  }
  
  .nav {
    gap: 1rem;
  }
}
</style>
