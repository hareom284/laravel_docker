<script setup>
import HorizontalNavGroup from "@layouts/components/HorizontalNavGroup.vue";
import HorizontalNavLink from "@layouts/components/HorizontalNavLink.vue";

const props = defineProps({
  navItems: {
    type: null,
    required: true,
  },
});
const resolveNavItemComponent = (item) => {
  if ("children" in item) return HorizontalNavGroup;

  return HorizontalNavLink;
};
</script>

<template>
  <Component
    :is="resolveNavItemComponent(item)"
    v-for="(item, index) in navItems"
    :key="index"
    :item="item"
  />
</template>

<style lang="scss">
.nav-items {
  display: flex;
  flex-wrap: wrap;
}
</style>
