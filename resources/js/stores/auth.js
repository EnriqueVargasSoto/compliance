import { defineStore } from 'pinia'

export const useAuthStore = defineStore('auth', {
  state: () => ({
    isAuthenticated: false, // Estado de autenticación
    user: null, // Datos del usuario
  }),
  actions: {
    login(userData) {
      this.isAuthenticated = true
      this.user = userData
    },
    logout() {
      this.isAuthenticated = false
      this.user = null
    },
  },
  persist: true // Esto guarda el estado en localStorage
})
