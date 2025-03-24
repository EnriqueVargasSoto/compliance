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
//export const routes = [
  // Email filter
  /* {
    path: '/apps/email/filter/:filter',
    name: 'apps-email-filter',
    component: emailRouteComponent,
    meta: {
      navActiveLink: 'apps-email',
      layoutWrapperClasses: 'layout-content-height-fixed',
    },
  }, */

  // Email label
  /* {
    path: '/apps/email/label/:label',
    name: 'apps-email-label',
    component: emailRouteComponent,
    meta: {
      // contentClass: 'email-application',
      navActiveLink: 'apps-email',
      layoutWrapperClasses: 'layout-content-height-fixed',
    },
  },
  {
    path: '/dashboards/logistics',
    name: 'dashboards-logistics',
    component: () => import('@/pages/apps/logistics/dashboard.vue'),
  },
  {
    path: '/dashboards/academy',
    name: 'dashboards-academy',
    component: () => import('@/pages/apps/academy/dashboard.vue'),
  },
  {
    path: '/apps/ecommerce/dashboard',
    name: 'apps-ecommerce-dashboard',
    component: () => import('@/pages/dashboards/ecommerce.vue'),
  }, */
//]
export const routes = [
    { path: '/', name:'/', redirect: '/dashboard' },
    {
        path: '/dashboard', // 📌 Asegúrate de que la ruta sea correcta
        name: 'dashboard',
        component: () => import('@/views/dashboards/Dashboard.vue'),//Dashboard,//DashboardAnalytics
        meta: { requiresAuth: true } // Ruta protegida
    },
    {
        path: '/seguridad/modulos', // 📌 Asegúrate de que la ruta sea correcta
        name: 'modulos',
        component: () => import('@/views/modulos/Index.vue'),//DashboardAnalytics
        meta: { requiresAuth: true } // Ruta protegida
    },
    {
        path: '/seguridad/usuarios', // 📌 Asegúrate de que la ruta sea correcta
        name: 'usuarios',
        component: () => import('@/views/users/Index.vue'),//DashboardAnalytics
        meta: { requiresAuth: true } // Ruta protegida
    },
    {
        path: '/seguridad/roles-permisos/roles', // 📌 Asegúrate de que la ruta sea correcta
        name: 'roles',
        component: () => import('@/views/roles-permisos/roles/Index.vue'),//DashboardAnalytics
        meta: { requiresAuth: true } // Ruta protegida
    },
    {
        path: '/seguridad/roles-permisos/permisos', // 📌 Asegúrate de que la ruta sea correcta
        name: 'permisos',
        component: () => import('@/views/roles-permisos/permisos/Index.vue'),//DashboardAnalytics
        meta: { requiresAuth: true } // Ruta protegida
    },

    {
        path: '/maestros/oficinas', // 📌 Asegúrate de que la ruta sea correcta
        name: 'oficinas',
        component: () => import('@/views/maestros/oficinas/Index.vue'),//DashboardAnalytics
        meta: { requiresAuth: true } // Ruta protegida
    },
    {
        path: '/maestros/tipo-documento', // 📌 Asegúrate de que la ruta sea correcta
        name: 'tipo-documento',
        component: () => import('@/views/maestros/tipos_documentos/Index.vue'),//DashboardAnalytics
        meta: { requiresAuth: true } // Ruta protegida
    },
    {
        path: '/maestros/tipo-movimientos', // 📌 Asegúrate de que la ruta sea correcta
        name: 'tipo-movimientos',
        component: () => import('@/views/maestros/tipo_movimientos/Index.vue'),//DashboardAnalytics
        meta: { requiresAuth: true } // Ruta protegida
    },
    {
        path: '/maestros/tipo-unidad-organica', // 📌 Asegúrate de que la ruta sea correcta
        name: 'tipo-unidad-organica',
        component: () => import('@/views/maestros/tipos_unidad_organica/Index.vue'),//DashboardAnalytics
        meta: { requiresAuth: true } // Ruta protegida
    },
    {
        path: '/maestros/estados', // 📌 Asegúrate de que la ruta sea correcta
        name: 'estados',
        component: () => import('@/views/maestros/estados/Index.vue'),//DashboardAnalytics
        meta: { requiresAuth: true } // Ruta protegida
    },
    {
        path: '/maestros/prioridades', // 📌 Asegúrate de que la ruta sea correcta
        name: 'prioridades',
        component: () => import('@/views/maestros/prioridades/Index.vue'),//DashboardAnalytics
        meta: { requiresAuth: true } // Ruta protegida
    },
    {
        path: '/maestros/cargos', // 📌 Asegúrate de que la ruta sea correcta
        name: 'cargos',
        component: () => import('@/views/maestros/cargos/Index.vue'),//DashboardAnalytics
        meta: { requiresAuth: true } // Ruta protegida
    },

    {
        path: '/maestros/unidad-organica', // 📌 Asegúrate de que la ruta sea correcta
        name: 'unidad-organica',
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
