import { createApp, h } from 'vue'
import { createInertiaApp } from '@inertiajs/vue3'
import { Ziggy } from './ziggy';
/* eslint-disable import/order */

import '@/@iconify/icons-bundle'
import ability from '@/plugins/casl/ability'
import i18n from '@/plugins/i18n'
import layoutsPlugin from '@/plugins/layouts'
import vuetify from '@/plugins/vuetify'

//plugin
import { abilitiesPlugin } from '@casl/vue'
// vue good table plugin
import VueGoodTablePlugin from "vue-good-table-next";
import { VueGoodTable } from "vue-good-table-next";
import moment from 'moment'; //monentjs


//system Error Alert
import SystemErrorAlert from "@mainRoot/components/SystemErrorAlert.vue";

//scss group
import '@core-scss/template/index.scss'
import '@styles/styles.scss'
import "vue-good-table-next/dist/vue-good-table-next.css";


//animation css
import 'animate.css';

// confirm dialog
import pages from './route';
//core


createInertiaApp({
    resolve: async (name) => {
        return pages[name];
    },
  setup({ el, App, props, plugin }) {
    createApp({ render: () => h(App, props) })
      .use(plugin)
      .use(Ziggy)
      .use(vuetify)
      .use(layoutsPlugin)
      .use(VueGoodTablePlugin)
      .use(i18n)
      .use(abilitiesPlugin, ability, {
        useGlobalProperties: true,
      })
      .mixin({
        methods:{route,moment},
      components:{
        VueGoodTable,
        SystemErrorAlert
      }
      })
      .mount(el);
  },
})

