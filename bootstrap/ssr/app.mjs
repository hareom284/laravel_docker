import { computed, mergeProps, unref, useSSRContext, createApp, h } from "vue";
import { createInertiaApp, Link, usePage } from "@inertiajs/vue3";
import PrimeVue from "primevue/config";
import VueGoodTablePlugin, { VueGoodTable } from "vue-good-table-next";
import Button from "primevue/button";
import Image from "primevue/image";
import Card from "primevue/card";
import Divider from "primevue/divider";
import InputText from "primevue/inputtext";
import Sidebar from "primevue/sidebar";
import Dialog from "primevue/dialog";
import PanelMenu from "primevue/panelmenu";
import Accordion from "primevue/accordion";
import AccordionTab from "primevue/accordiontab";
import DataTable from "primevue/datatable";
import Column from "primevue/column";
import { ssrRenderAttrs, ssrRenderSlot } from "vue/server-renderer";
import Badge from "primevue/badge";
import Message from "primevue/message";
import Menu from "primevue/menu";
import Avatar from "primevue/avatar";
import DataView from "primevue/dataview";
import Skeleton from "primevue/skeleton";
import DataViewLayoutOptions from "primevue/dataviewlayoutoptions";
import Fieldset from "primevue/fieldset";
import OverlayPanel from "primevue/overlaypanel";
import Toast from "primevue/toast";
import ToastService from "primevue/toastservice";
import ConfirmDialog from "primevue/confirmdialog";
import ConfirmationService from "primevue/confirmationservice";
const Ziggy = { "url": "http://localhost:8000", "port": 8e3, "defaults": {}, "routes": { "sanctum.csrf-cookie": { "uri": "sanctum/csrf-cookie", "methods": ["GET", "HEAD"] }, "login-page": { "uri": "test/login", "methods": ["GET", "HEAD"] }, "logout": { "uri": "test/logout", "methods": ["GET", "HEAD"] }, "dashboard": { "uri": "home", "methods": ["GET", "HEAD"] } } };
if (typeof window !== "undefined" && typeof window.Ziggy !== "undefined") {
  Object.assign(Ziggy.routes, window.Ziggy.routes);
}
const _sfc_main$1 = {
  __name: "IconButton",
  __ssrInlineRender: true,
  props: {
    type: {
      type: String,
      default: "button"
    },
    buttonColor: {
      type: String,
      default: "default"
    }
  },
  emits: ["click:runFunction"],
  setup(__props, { emit }) {
    const props = __props;
    let dynamic_color = computed(() => {
      switch (props.buttonColor) {
        case "green":
          return "mb-2 text-white bg-green-400 hover:bg-green-700 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm p-2.5 text-center inline-flex items-center mr-2";
        case "blue":
          return "mb-2 text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm p-2.5 text-center inline-flex items-center mr-2 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800";
        case "yellow":
          return "mb-2 text-white bg-yellow-400 hover:bg-yellow-700 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm p-2.5 text-center inline-flex items-center mr-2";
        case "red":
          return "mb-2 text-white bg-red-600 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm p-2.5 text-center inline-flex items-center mr-2";
        default:
          return "mb-2 text-white bg-white hover:bg-gray-100 text-gray-500 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm p-2.5 text-center inline-flex items-center mr-2";
      }
    });
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<button${ssrRenderAttrs(mergeProps({
        type: __props.type,
        class: unref(dynamic_color)
      }, _attrs))}>`);
      ssrRenderSlot(_ctx.$slots, "default", {}, null, _push, _parent);
      _push(`</button>`);
    };
  }
};
const _sfc_setup$1 = _sfc_main$1.setup;
_sfc_main$1.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("src/Common/Layouts/Composables/IconButton.vue");
  return _sfc_setup$1 ? _sfc_setup$1(props, ctx) : void 0;
};
const _imports_0 = "/build/assets/add-09c48bf0.svg";
const _export_sfc = (sfc, props) => {
  const target = sfc.__vccOpts || sfc;
  for (const [key, val] of props) {
    target[key] = val;
  }
  return target;
};
const _sfc_main = {};
function _sfc_ssrRender(_ctx, _push, _parent, _attrs) {
  _push(`<img${ssrRenderAttrs(mergeProps({
    src: _imports_0,
    class: "h-10 w-10 pr-2"
  }, _attrs))}>`);
}
const _sfc_setup = _sfc_main.setup;
_sfc_main.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("src/Common/Layouts/Composables/icons/AddIcon.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
const AddIcon = /* @__PURE__ */ _export_sfc(_sfc_main, [["ssrRender", _sfc_ssrRender]]);
const primevue_min = "";
const primeicons = "";
const animate = "";
const vueGoodTableNext = "";
const theme = "";
createInertiaApp({
  resolve: async (name) => {
    console.log(name);
    let page = null;
    let isModule = name.split("::");
    if (isModule.length > 1) {
      let module = isModule[0];
      let pathTo = isModule[1];
      page = await import(`../../src/${module}/${pathTo}.vue`);
    }
    return page.default;
  },
  setup({ el, App, props, plugin }) {
    createApp({ render: () => h(App, props) }).use(plugin).use(VueGoodTablePlugin).use(Ziggy).use(PrimeVue).use(ToastService).use(Toast).use(ConfirmDialog).use(ConfirmationService).mixin({
      methods: { route },
      components: {
        VueGoodTable,
        Button,
        Image,
        Card,
        Divider,
        InputText,
        PanelMenu,
        Accordion,
        Badge,
        Sidebar,
        Dialog,
        Menu,
        Avatar,
        AccordionTab,
        DataTable,
        Column,
        OverlayPanel,
        DataView,
        DataViewLayoutOptions,
        Message,
        Fieldset,
        IconButton: _sfc_main$1,
        Link,
        usePage,
        AddIcon,
        Toast,
        ConfirmDialog,
        Skeleton
      }
    }).mount(el);
  }
});
