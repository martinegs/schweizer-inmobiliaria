<template>
  <div class="home">
    <Header />
    <main>
      <Hero />
      <About />
      <Properties />
      <ContactForm />
    </main>
    <Footer />
  </div>
</template>

<script setup>
import Header from '../components/Header.vue'
import Hero from '../components/Hero.vue'
import About from '../components/About.vue'
import Properties from '../components/Properties.vue'
import ContactForm from '../components/ContactForm.vue'
import Footer from '../components/Footer.vue'
</script>

<style scoped>
.home {
  min-height: 100vh;
}
</style>
