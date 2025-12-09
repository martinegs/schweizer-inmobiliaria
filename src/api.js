import axios from 'axios'

const api = axios.create({
  baseURL: import.meta.env.VITE_API_URL || 'http://localhost:8000/api',
  withCredentials: true,
  headers: {
    'Content-Type': 'application/json'
  }
})

// Auth
export const login = (email, password) => {
  return api.post('/auth/login', { email, password })
}

export const logout = () => {
  return api.post('/auth/logout')
}

export const checkAuth = () => {
  return api.get('/auth/check')
}

// Propiedades públicas
export const getPropiedades = (tipo = null) => {
  const params = tipo ? { tipo } : {}
  return api.get('/propiedades', { params })
}

export const getPropiedad = (id) => {
  return api.get(`/propiedades/${id}`)
}

// Propiedades admin
export const createPropiedad = (data) => {
  return api.post('/admin/propiedades', data)
}

export const updatePropiedad = (id, data) => {
  return api.put(`/admin/propiedades/${id}`, data)
}

export const deletePropiedad = (id) => {
  return api.delete(`/admin/propiedades/${id}`)
}

export const uploadImagen = (propiedadId, formData) => {
  return api.post(`/admin/propiedades/${propiedadId}/imagenes`, formData, {
    headers: {
      'Content-Type': 'multipart/form-data'
    }
  })
}

export const deleteImagen = (imageId) => {
  return api.delete(`/admin/propiedades/imagenes/${imageId}`)
}

// Usuarios admin
export const getUsuarios = () => {
  return api.get('/admin/usuarios')
}

export const createUsuario = (data) => {
  return api.post('/admin/usuarios', data)
}

export const updateUsuario = (id, data) => {
  return api.put(`/admin/usuarios/${id}`, data)
}

export const deleteUsuario = (id) => {
  return api.delete(`/admin/usuarios/${id}`)
}

export default api
