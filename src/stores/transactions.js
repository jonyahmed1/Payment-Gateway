import { defineStore } from 'pinia';
import api from '../services/api';

export const useTransactionsStore = defineStore('transactions', {
  state: () => ({ list: [], pagination: {} }),
  actions: {
    async fetch(params = {}) {
      const res = await api.get('/transactions', { params });
      this.list = res.data.data;
      this.pagination = { ...res.data };
      return res;
    },
    async get(id) {
      const res = await api.get(`/transactions/${id}`);
      return res.data;
    },
    async verify(id) {
      return api.post(`/transactions/${id}/verify`);
    },
    async approve(id) {
      return api.post(`/transactions/${id}/approve`);
    },
    async reject(id, reason) {
      return api.post(`/transactions/${id}/reject`, { reason });
    }
  }
});