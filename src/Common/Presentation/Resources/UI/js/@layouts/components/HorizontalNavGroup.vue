<script setup>
import { useLayouts } from "@layouts";
import { HorizontalNavLink, HorizontalNavPopper } from "@layouts/components";
import { config } from "@layouts/config";
import { canViewNavMenuGroup } from "@layouts/plugins/casl";
import { isNavGroupActive } from "@layouts/utils";
import { router } from "@inertiajs/core";
import { usePage } from "@inertiajs/vue3";
import { computed } from "vue";

const props = defineProps({
  item: {
    type: null,
    required: true,
  },
  childrenAtEnd: {
    type: Boolean,
    required: false,
    default: false,
  },
  isSubItem: {
    type: Boolean,
    required: false,
    default: false,
  },
});

defineOptions({ name: "HorizontalNavGroup" });

const auth = computed(() => usePage().props.auth);

const { dynamicI18nProps, isAppRtl } = useLayouts();
const isGroupActive = ref(false);
let items = [
  { title: "Click Me" },
  { title: "Click Me" },
  { title: "Click Me" },
  { title: "Click Me 2" },
];
let isLinkActive = (currentRoute) => {
  return route().current().includes(currentRoute);
};
let isParentActive = (routeList) => {
  return routeList.find((item) => route().current().includes(item.route_name));
};
let goLink = (item) => {
  if (item?.isNativeLink) {
    window.location.href = item.url;
  } else {
    router.get(item.url);
  }
};
</script>

<template>
  <div class="text-center">
    <v-menu open-on-hover>
      <template v-slot:activator="{ props }">
        <v-btn
          variant="text"
          :prepend-icon="item.icon.icon"
          append-icon="mdi-chevron-down"
          class="mx-2 text-none"
          :class="isParentActive(item.children) ? 'bg-primary' : ''"
          :color="isParentActive(item.children) ? '#fff' : ''"
          v-bind="props"
          :hidden="
            !auth?.data?.permissions?.includes(item?.access_module) &&
            item?.access_module != 'access_dashboard'
              ? true
              : false
          "
        >
          {{ item.title }}
        </v-btn>
      </template>

      <v-list density="compact">
        <v-list-item
          v-for="(sitem, sindex) in item.children"
          :key="sindex"
          :value="sitem"
          @click="goLink(sitem)"
          :variant="isLinkActive(sitem.route_name) ? 'tonal' : 'text'"
          :hidden="
            !auth?.data?.permissions?.includes(sitem?.access_module) &&
            item?.access_module != 'access_dashboard'
              ? true
              : false
          "
        >
          <template v-slot:prepend>
            <v-icon icon="mdi-circle-small"></v-icon>
          </template>
          <v-list-item-title>{{ sitem.title }}</v-list-item-title>
        </v-list-item>
      </v-list>
    </v-menu>
  </div>
</template>

<style lang="scss">
.active-list {
  background-color: #ededff !important;
  color: #666cff !important;
}
.layout-horizontal-nav {
  .nav-group {
    .nav-group-label {
      display: flex;
      align-items: center;
      cursor: pointer;
    }

    .popper-content {
      z-index: 1;

      > div {
        overflow-x: hidden;
        overflow-y: auto;
      }
    }
  }
}
</style>
