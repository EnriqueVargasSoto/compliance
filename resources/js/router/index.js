import { createRouter, createWebHistory, createWebHashHistory } from 'vue-router';
import Home from '../views/Home.vue';
import Dashboard from '../views/dashboards/Dashboard.vue';
import IndexPermisos from '../views/roles-permisos/permisos/Index.vue';
import IndexRoles from '../views/roles-permisos/roles/Index.vue';
import IndexUsuarios from '../views/users/Index.vue';
import Blank from '../layouts/blank.vue';
import { useAuthStore } from '@/stores/auth'

const routes = [
    { path: '/', redirect: '/dashboard' },
    {
        path: '/dashboard', // 📌 Asegúrate de que la ruta sea correcta
        name: 'dashboard',
        component: () => import('@/views/dashboards/'),//Dashboard,//DashboardAnalytics
        //meta: { requiresAuth: true } // Ruta protegida
    },
    {
        path: '/usuarios', // 📌 Asegúrate de que la ruta sea correcta
        name: 'usuarios',
        component: IndexUsuarios,//DashboardAnalytics
        //meta: { requiresAuth: true } // Ruta protegida
    },
    {
        path: '/roles-permisos/roles', // 📌 Asegúrate de que la ruta sea correcta
        name: 'roles',
        component: IndexRoles,//DashboardAnalytics
        //meta: { requiresAuth: true } // Ruta protegida
    },
    {
        path: '/roles-permisos/permisos', // 📌 Asegúrate de que la ruta sea correcta
        name: 'permisos',
        component: IndexPermisos,//DashboardAnalytics
        //meta: { requiresAuth: true } // Ruta protegida
    },
    {
        path: '/login', // 📌 Asegúrate de que la ruta sea correcta
        name: 'login',
        component: () => import('@/views/login/Index.vue')//DashboardAnalytics
    },
    {
        path: '/offices', // 📌 Asegúrate de que la ruta sea correcta
        name: 'offices',
        component: () => import('@/views/offices/Index.vue')
    }
];

const router = createRouter({
    history: createWebHashHistory(),//createWebHistory(),
    routes,
});

/* // ⛔ Navigation Guard para bloquear rutas protegidas
router.beforeEach((to, from, next) => {
    const authStore = useAuthStore()
    if (to.meta.requiresAuth && !authStore.isAuthenticated) {
        next('/login')
    } else {
        next()
    }
}); */

export default router;
