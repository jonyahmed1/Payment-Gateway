import { defineStore } from 'pinia';
import api from '../services/api';

export const useAuthStore = defineStore('auth', {
  state: () => ({ user: JSON.parse(localStorage.getItem('auth_user')||'null') }),
  actions: {
    async login(payload) {
      const res = await api.post('/login', payload);
      this.user = res.data.user;
      localStorage.setItem('auth_user', JSON.stringify(this.user));
      return res;
    },
    logout() {
      localStorage.removeItem('auth_user');
      this.user = null;
    }
  }
});