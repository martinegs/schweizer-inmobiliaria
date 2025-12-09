<template>
  <div class="admin-dashboard">
    <nav class="admin-nav">
      <div class="nav-header">
        <img src="/img/2.png" alt="Schweizer" class="nav-logo">
        <h1>Panel Admin</h1>
      </div>
      
      <div class="nav-links">
        <router-link to="/admin/propiedades" class="nav-link">
          Propiedades
        </router-link>
        <router-link to="/admin/usuarios" class="nav-link">
          Usuarios
        </router-link>
      </div>
      
      <div class="nav-footer">
        <div class="user-info">
          <span>{{ username }}</span>
        </div>
        <button @click="handleLogout" class="btn-logout">
          Cerrar Sesión
        </button>
      </div>
    </nav>
    
    <main class="admin-content">
      <router-view></router-view>
    </main>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { logout, checkAuth } from '../../api'

const router = useRouter()
const username = ref('Admin')

onMounted(async () => {
  try {
    const response = await checkAuth()
    if (response.data.authenticated && response.data.user) {
      username.value = response.data.user.nombre || response.data.user.email
    }
  } catch (error) {
    console.error('Error al verificar auth:', error)
  }
})

const handleLogout = async () => {
  try {
    await logout()
    router.push('/admin/login')
  } catch (error) {
    console.error('Error al cerrar sesión:', error)
    router.push('/admin/login')
  }
}
</script>

<style scoped>
.admin-dashboard {
  display: flex;
  min-height: 100vh;
  background: #f5f5f5;
}

.admin-nav {
  width: 280px;
  background: #1a1a1a;
  color: white;
  display: flex;
  flex-direction: column;
  position: fixed;
  height: 100vh;
  overflow-y: auto;
}

.nav-header {
  padding: 2rem;
  border-bottom: 1px solid rgba(255, 255, 255, 0.1);
  text-align: center;
}

.nav-logo {
  height: 60px;
  margin-bottom: 1rem;
}

.nav-header h1 {
  font-size: 1.5rem;
  color: #ff6b35;
  margin: 0;
}

.nav-links {
  flex: 1;
  padding: 2rem 0;
}

.nav-link {
  display: flex;
  align-items: center;
  gap: 1rem;
  padding: 1rem 2rem;
  color: white;
  text-decoration: none;
  transition: all 0.3s;
  border-left: 3px solid transparent;
}

.nav-link:hover,
.nav-link.router-link-active {
  background: rgba(255, 107, 53, 0.1);
  border-left-color: #ff6b35;
}

.nav-link .icon {
  font-size: 1.5rem;
}

.nav-footer {
  padding: 2rem;
  border-top: 1px solid rgba(255, 255, 255, 0.1);
}

.user-info {
  margin-bottom: 1rem;
  padding: 0.5rem;
  text-align: center;
  color: #ccc;
}

.btn-logout {
  width: 100%;
  padding: 0.875rem;
  background: transparent;
  border: 2px solid #ff6b35;
  color: #ff6b35;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s;
}

.btn-logout:hover {
  background: #ff6b35;
  color: white;
}

.admin-content {
  margin-left: 280px;
  flex: 1;
  padding: 2rem;
  min-height: 100vh;
}

@media (max-width: 768px) {
  .admin-nav {
    width: 100%;
    position: relative;
    height: auto;
  }
  
  .admin-content {
    margin-left: 0;
  }
}
</style>
