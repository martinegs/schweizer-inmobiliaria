<template>
  <div class="login-page">
    <div class="login-container">
      <div class="login-card">
        <div class="logo">
          <img src="/img/2.png" alt="Schweizer Desarrollos">
        </div>
        <h2>Panel de Administración</h2>
        
        <form @submit.prevent="handleLogin" class="login-form">
          <div class="form-group">
            <label for="email">Email</label>
            <input 
              type="email" 
              id="email" 
              v-model="email" 
              required 
              autocomplete="email"
              placeholder="correo@ejemplo.com"
            >
          </div>
          
          <div class="form-group">
            <label for="password">Contraseña</label>
            <input 
              type="password" 
              id="password" 
              v-model="password" 
              required 
              autocomplete="current-password"
            >
          </div>
          
          <div v-if="error" class="error-message">
            {{ error }}
          </div>
          
          <button type="submit" class="btn-login" :disabled="loading">
            {{ loading ? 'Ingresando...' : 'Ingresar' }}
          </button>
        </form>
        
        <div class="back-home">
          <router-link to="/">← Volver al sitio</router-link>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { login } from '../../api'

const router = useRouter()
const email = ref('')
const password = ref('')
const error = ref('')
const loading = ref(false)

const handleLogin = async () => {
  error.value = ''
  loading.value = true
  
  try {
    const response = await login(email.value, password.value)
    
    if (response.data.success) {
      router.push('/admin')
    } else {
      error.value = response.data.message || 'Error al iniciar sesión'
    }
  } catch (err) {
    error.value = err.response?.data?.message || 'Error al conectar con el servidor'
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
.login-page {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
  padding: 2rem;
}

.login-container {
  width: 100%;
  max-width: 450px;
}

.login-card {
  background: #ffffff;
  border-radius: 16px;
  padding: 3rem;
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
}

.logo {
  text-align: center;
  margin-bottom: 2rem;
}

.logo img {
  height: 80px;
  width: auto;
}

h2 {
  text-align: center;
  color: #1a1a1a;
  margin-bottom: 2rem;
  font-size: 1.8rem;
}

.login-form {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

label {
  color: #333;
  font-weight: 600;
  font-size: 0.95rem;
}

input {
  padding: 0.875rem;
  border: 2px solid #e0e0e0;
  border-radius: 8px;
  font-size: 1rem;
  transition: all 0.3s;
}

input:focus {
  outline: none;
  border-color: #ff6b35;
  box-shadow: 0 0 0 3px rgba(255, 107, 53, 0.1);
}

.error-message {
  background: #fee;
  color: #c00;
  padding: 0.875rem;
  border-radius: 8px;
  font-size: 0.9rem;
  border-left: 4px solid #c00;
}

.btn-login {
  background: #ff6b35;
  color: white;
  border: none;
  padding: 1rem;
  border-radius: 8px;
  font-size: 1.1rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s;
  margin-top: 1rem;
}

.btn-login:hover:not(:disabled) {
  background: #e55a2b;
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(255, 107, 53, 0.3);
}

.btn-login:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.back-home {
  text-align: center;
  margin-top: 2rem;
  padding-top: 2rem;
  border-top: 1px solid #e0e0e0;
}

.back-home a {
  color: #666;
  text-decoration: none;
  font-size: 0.95rem;
  transition: color 0.3s;
}

.back-home a:hover {
  color: #ff6b35;
}

@media (max-width: 768px) {
  .login-card {
    padding: 2rem;
  }
  
  h2 {
    font-size: 1.5rem;
  }
}
</style>
