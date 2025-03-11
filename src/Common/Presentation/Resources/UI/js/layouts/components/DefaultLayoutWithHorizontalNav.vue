<script setup>
import navItems from "@/navigation/horizontal";
import { useThemeConfig } from "@core/composable/useThemeConfig";
import TheCustomizer from "@core/components/TheCustomizer.vue";
import { themeConfig } from "@themeConfig";
// Components
import Footer from "@/layouts/components/Footer.vue";
import NavBarNotifications from "@/layouts/components/NavBarNotifications.vue";
import NavbarShortcuts from "@/layouts/components/NavbarShortcuts.vue";
import NavbarThemeSwitcher from "@/layouts/components/NavbarThemeSwitcher.vue";
import NavSearchBar from "@/layouts/components/NavSearchBar.vue";
import UserProfile from "@/layouts/components/UserProfile.vue";
import HorizontalNavLayout from "@layouts/components/HorizontalNavLayout.vue";
import { VNodeRenderer } from "@layouts/components/VNodeRenderer";
import { Link, usePage } from "@inertiajs/inertia-vue3";
import MobileSidebar from "./MobileSidebar.vue";
import NavigationBar from "@/layouts/components/NavigationBar.vue";
import { ref } from "vue";

const { appRouteTransition } = useThemeConfig();
const layout = ref('horizontal');
let drawer = ref(false);

const toggleDrawer = t => {
  drawer.value = t;
};

</script>
<template>
  <HorizontalNavLayout :nav-items="navItems" :drawer="drawer">
    <!-- :point_right: navbar -->
    <template #navbar>
      <NavigationBar :checkLayout="layout" @toggle_drawer="toggleDrawer"/>
    </template>
    <!-- :point_right: Pages -->
    <Transition :name="appRouteTransition" mode="out-in">
      <main class="" style="min-height: 485px">
        <slot> </slot>
      </main>
    </Transition>
    <!-- :point_right: Footer -->
    <template #footer>
      <Footer />
    </template>
    <!-- :point_right: Customizer -->
    <!-- 👉 Customizer -->
    <TheCustomizer />
  </HorizontalNavLayout>
</template>
