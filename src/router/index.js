import { createRouter, createWebHistory } from 'vue-router';
import Login from '../views/Auth/Login.vue';
import Dashboard from '../views/Dashboard.vue';
import TransactionsList from '../views/Transactions/List.vue';
import TransactionDetail from '../views/Transactions/Detail.vue';

const routes = [
  { path: '/login', name: 'login', component: Login },
  { path: '/', name: 'dashboard', component: Dashboard, meta: { requiresAuth: true } },
  { path: '/transactions', name: 'transactions', component: TransactionsList, meta: { requiresAuth: true } },
  { path: '/transactions/:id', name: 'transaction.detail', component: TransactionDetail, meta: { requiresAuth: true } },
];

const router = createRouter({ history: createWebHistory(), routes });

router.beforeEach((to, from, next) => {
  const isAuth = !!localStorage.getItem('auth_user');
  if (to.meta.requiresAuth && !isAuth) return next({ name: 'login' });
  next();
});

export default router;