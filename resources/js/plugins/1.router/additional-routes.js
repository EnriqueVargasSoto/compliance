/* const emailRouteComponent = () => import('@/pages/apps/email/index.vue')
 */
// 👉 Redirects
export const redirects = [
  // ℹ️ We are redirecting to different pages based on role.
  // NOTE: Role is just for UI purposes. ACL is based on abilities.
  {
    path: '/',
    name: 'index',
    redirect: to => {
      // TODO: Get type from backend
      const userData = useCookie('userData')
      const userRole = userData.value?.role
      if (userRole === 'admin')
        return { name: '/' }
      if (userRole === 'client')
        return { name: 'usuarios' }

      return { name: 'login', query: to.query }
    },
  },
  /* {
    path: '/pages/user-profile',
    name: 'pages-user-profile',
    redirect: () => ({ name: 'pages-user-profile-tab', params: { tab: 'profile' } }),
  },
  {
    path: '/pages/account-settings',
    name: 'pages-account-settings',
    redirect: () => ({ name: 'pages-account-settings-tab', params: { tab: 'account' } }),
  }, */
]

export const routes = [
    { path: '/', name:'/', redirect: '/dashboard' },

    {
        path: '/dashboard', // 📌 Asegúrate de que la ruta sea correcta
        name: 'dashboard',
        component: () => import('@/views/dashboards/Dashboard.vue'),//Dashboard,//DashboardAnalytics
        meta: { requiresAuth: true } // Ruta protegida
    },


    {
        path: '/modules', // 📌 Asegúrate de que la ruta sea correcta
        name: 'modules',
        component: () => import('@/views/security/modules/Index.vue'),//DashboardAnalytics
        meta: { requiresAuth: true } // Ruta protegida
    },
    {
        path: '/usuarios', // 📌 Asegúrate de que la ruta sea correcta
        name: 'users',
        component: () => import('@/views/security/users/Index.vue'),//DashboardAnalytics
        meta: { requiresAuth: true } // Ruta protegida
    },
    {
        path: '/roles', // 📌 Asegúrate de que la ruta sea correcta
        name: 'roles',
        component: () => import('@/views/security/roles_and_permissions/roles/Index.vue'),//DashboardAnalytics
        meta: { requiresAuth: true } // Ruta protegida
    },
    {
        path: '/seguridad/roles-permisos/permisos', // 📌 Asegúrate de que la ruta sea correcta
        name: 'permissions',
        component: () => import('@/views/roles-permisos/permisos/Index.vue'),//DashboardAnalytics
        meta: { requiresAuth: true } // Ruta protegida
    },

    {
        path: '/branch', // 📌 Asegúrate de que la ruta sea correcta
        name: 'branch',
        component: () => import('@/views/maestros/oficinas/Index.vue'),//DashboardAnalytics
        meta: { requiresAuth: true } // Ruta protegida
    },

    {
        path: '/processes', // 📌 Asegúrate de que la ruta sea correcta
        name: 'processes',
        component: () => import('@/views/maestros/tipos_documentos/Index.vue'),//DashboardAnalytics
        meta: { requiresAuth: true } // Ruta protegida
    },

    {
        path: '/compliant-documents', // 📌 Asegúrate de que la ruta sea correcta
        name: 'compliant-documents',
        component: () => import('@/views/maestros/tipo_movimientos/Index.vue'),//DashboardAnalytics
        meta: { requiresAuth: true } // Ruta protegida
    },
    {
        path: '/observed-documents', // 📌 Asegúrate de que la ruta sea correcta
        name: 'observed-documents',
        component: () => import('@/views/maestros/tipos_unidad_organica/Index.vue'),//DashboardAnalytics
        meta: { requiresAuth: true } // Ruta protegida
    },

    {
        path: '/chat', // 📌 Asegúrate de que la ruta sea correcta
        name: 'chat',
        component: () => import('@/views/maestros/estados/Index.vue'),//DashboardAnalytics
        meta: { requiresAuth: true } // Ruta protegida
    },

    {
        path: '/digital-signature', // 📌 Asegúrate de que la ruta sea correcta
        name: 'digital-signature',
        component: () => import('@/views/maestros/prioridades/Index.vue'),//DashboardAnalytics
        meta: { requiresAuth: true } // Ruta protegida
    },
    {
        path: '/synchronization', // 📌 Asegúrate de que la ruta sea correcta
        name: 'synchronization',
        component: () => import('@/views/maestros/cargos/Index.vue'),//DashboardAnalytics
        meta: { requiresAuth: true } // Ruta protegida
    },

    {
        path: '/backup', // 📌 Asegúrate de que la ruta sea correcta
        name: 'backup',
        component: () => import('@/views/maestros/unidades_organicas/Index.vue'),//DashboardAnalytics
        meta: { requiresAuth: true } // Ruta protegida
    },
    {
        path: '/maestros/unidad-organica/crear-unidad-organica', // 📌 Asegúrate de que la ruta sea correcta
        name: 'crear-unidad-organica',
        component: () => import('@/views/maestros/unidades_organicas/Create.vue'),//DashboardAnalytics
        meta: { requiresAuth: true, navActiveLink: 'unidad-organica'}, // Ruta protegida
    },
    {
        path: '/maestros/unidad-organica/editar-unidad-organica', // 📌 Asegúrate de que la ruta sea correcta
        name: 'editar-unidad-organica',
        component: () => import('@/views/maestros/unidades_organicas/Edit.vue'),//DashboardAnalytics
        meta: { requiresAuth: true, navActiveLink: 'unidad-organica'}, // Ruta protegida
    },

    {
        path: '/maestros/tipo-derivacion', // 📌 Asegúrate de que la ruta sea correcta
        name: 'tipo-derivacion',
        component: () => import('@/views/maestros/tipos_derivacion/Index.vue'),//DashboardAnalytics
        meta: { requiresAuth: true } // Ruta protegida
    },
    {
        path: '/maestros/tipo-documento-identidad', // 📌 Asegúrate de que la ruta sea correcta
        name: 'tipo-documento-identidad',
        component: () => import('@/views/maestros/tipos_documentos_identidad/Index.vue'),//DashboardAnalytics
        meta: { requiresAuth: true } // Ruta protegida
    },

    {
        path: '/proceso-tramite/expedientes-pendientes', // 📌 Asegúrate de que la ruta sea correcta
        name: 'expedientes-pendientes',
        component: () => import('@/views/proceso_tramite/expedientes_pendientes/Index.vue'),//DashboardAnalytics
        meta: { requiresAuth: true } // Ruta protegida
    },
    {
        path: '/proceso-tramite/expedientes-pendientes/crear-expediente', // 📌 Asegúrate de que la ruta sea correcta
        name: 'crear-expediente',
        component: () => import('@/views/proceso_tramite/expedientes_pendientes/Create.vue'),//DashboardAnalytics
        meta: { requiresAuth: true, navActiveLink: 'expedientes-pendientes' } // Ruta protegida
    },

    {
        path: '/login', // 📌 Asegúrate de que la ruta sea correcta
        name: 'login',
        meta: {
            layout: 'blank',
/*             public: true, */
            unauthenticatedOnly: true,
        },
        component: () => import('@/views/login/Index.vue')//DashboardAnalytics
    },
    {
        path: '/offices', // 📌 Asegúrate de que la ruta sea correcta
        name: 'offices',
        meta: {
            layout: 'blank',
            //unauthenticatedOnly: true,
        },
        component: () => import('@/views/offices/Index.vue')//DashboardAnalytics
    }
];
