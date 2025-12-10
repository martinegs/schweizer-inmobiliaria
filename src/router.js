import { createRouter, createWebHistory } from 'vue-router'
import { checkAuth } from './api'

// Componentes públicos
const Home = () => import('./views/Home.vue')

// Componentes admin
const AdminLogin = () => import('./views/admin/Login.vue')
const AdminDashboard = () => import('./views/admin/Dashboard.vue')
const AdminPropiedades = () => import('./views/admin/Propiedades.vue')
const AdminPropiedadForm = () => import('./views/admin/PropiedadForm.vue')
const AdminUsuarios = () => import('./views/admin/Usuarios.vue')

const routes = [
  {
    path: '/',
    name: 'Home',
    component: Home
  },
  {
    path: '/admin/login',
    name: 'AdminLogin',
    component: AdminLogin,
    meta: { requiresGuest: true }
  },
  {
    path: '/admin',
    name: 'Admin',
    component: AdminDashboard,
    meta: { requiresAuth: true },
    redirect: '/admin/propiedades',
    children: [
      {
        path: 'propiedades',
        name: 'AdminPropiedades',
        component: AdminPropiedades
      },
      {
        path: 'propiedades/nueva',
        name: 'AdminPropiedadNueva',
        component: AdminPropiedadForm
      },
      {
        path: 'propiedades/:id/editar',
        name: 'AdminPropiedadEditar',
        component: AdminPropiedadForm
      },
      {
        path: 'usuarios',
        name: 'AdminUsuarios',
        component: AdminUsuarios,
        meta: { requiresSuperAdmin: true }
      }
    ]
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes,
  scrollBehavior(to, from, savedPosition) {
    if (savedPosition) {
      return savedPosition
    } else if (to.hash) {
      return {
        el: to.hash,
        behavior: 'smooth'
      }
    } else {
      return { top: 0 }
    }
  }
})

// Guard de autenticación
router.beforeEach(async (to, from, next) => {
  if (to.meta.requiresAuth || to.meta.requiresSuperAdmin) {
    try {
      const response = await checkAuth()
      if (response.data.authenticated) {
        // Verificar si la ruta requiere super admin
        if (to.meta.requiresSuperAdmin) {
          if (response.data.user && response.data.user.id === 1) {
            next()
          } else {
            // Redirigir a propiedades si no es super admin
            next('/admin/propiedades')
          }
        } else {
          next()
        }
      } else {
        next('/admin/login')
      }
    } catch (error) {
      next('/admin/login')
    }
  } else if (to.meta.requiresGuest) {
    try {
      const response = await checkAuth()
      if (response.data.authenticated) {
        next('/admin')
      } else {
        next()
      }
    } catch (error) {
      next()
    }
  } else {
    next()
  }
})

export default router
