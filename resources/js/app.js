import { createApp } from 'vue';
/* import { createPinia } from 'pinia'
import vuetify from './plugins/vuetify' // Asegúrate de importar Vuetify */
import App from './App.vue';
/* import router from './router';
import { createI18n } from 'vue-i18n'

// Si usas CASL para gestionar permisos, puedes importar useAbility globalmente
/* import { abilitiesPlugin } from '@casl/vue';
import { createAbility } from '@casl/ability'; */

/* import abilitiesPlugin from './plugins/casl' // Asegúrate de importar Vuetify */

// Styles
import '@core-scss/template/index.scss'
import '@styles/styles.scss'

import { registerPlugins } from '@core/utils/plugins'

/* const app = createApp(App); */




import Vue3Pdfjs from 'vue3-pdfjs'; // Importa la biblioteca
import PdfjsViewer from 'vue3-pdfjs'; // Asegúrate de importar correctamente



const app = createApp(App);
/* const pinia = createPinia();
const i18n = createI18n({
    legacy: false, // Necesario para Vue 3
    locale: 'en',  // Idioma predeterminado
    //messages,      // Mensajes para diferentes idiomas
  }); */

// Crear la habilidad (abilities) que utilizarás
/* const ability = createAbility([
    { action: 'read', subject: 'Article' },
    { action: 'create', subject: 'Article' },
  ]);
 */
// Register plugins
registerPlugins(app)
  // Usar abilitiesPlugin con la instancia de habilidad
/* app.use(abilitiesPlugin);
app.use(i18n)
app.use(vuetify)  // Usa Vuetify
app.use(pinia) // 🔹 REGISTRAR PINIA ANTES DE USARLO */
app.use(Vue3Pdfjs); // Registra la biblioteca como un plugin
app.use(PdfjsViewer);  // Registra el plugin aquí
/* app.use(router); */
app.mount('#app');
