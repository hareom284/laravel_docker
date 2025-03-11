<script setup>
// import { useLayouts } from "@layouts";
import { config } from "@layouts/config";
import { Link } from "@inertiajs/inertia-vue3";
import { router } from "@inertiajs/core";
import { usePage } from "@inertiajs/vue3";
// import { can } from "@layouts/plugins/casl";
// import { getComputedNavLinkToProp, isNavLinkActive } from "@layouts/utils";

const props = defineProps({
  item: {
    type: null,
    required: true,
  },
  isSubItem: {
    type: Boolean,
    required: false,
    default: false,
  },
});

const auth = computed(() => usePage().props.auth);
// const { dynamicI18nProps } = useLayouts();
let isLinkActive = (currentRoute) => {
  return route().current().includes(currentRoute);
};
let goLink = (url) => {
  router.get(url);
};
</script>

<template>
  <v-btn
    variant="text"
    :prepend-icon="item.icon.icon"
    class="mx-2 text-none"
    :class="isLinkActive(item.route_name) ? 'bg-primary' : ''"
    :color="isLinkActive(item.route_name) ? '#fff' : ''"
    @click="goLink(item.url)"
    :hidden="
      !auth?.data?.permissions?.includes(item?.access_module) &&
      item?.access_module != 'access_dashboard'
        ? true
        : false
    "
  >
    <span :style="isLinkActive(item.route_name) ? 'color: #fff' : ''">
      {{ item.title }}
    </span>
  </v-btn>
</template>

<style lang="scss">
.layout-horizontal-nav {
  .nav-link a {
    display: flex;
    align-items: center;
  }
}
</style>
