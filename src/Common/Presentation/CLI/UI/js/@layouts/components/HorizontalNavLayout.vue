<script setup>
import { HorizontalNav } from "@layouts/components";

// import { useLayouts } from '@layouts'
import { useLayouts } from "@layouts/composable/useLayouts";
import { ref, watch } from "vue";
import HorizontalMobileNav from "./HorizontalMobileNav.vue";
import HorizontalMobileNavLink from "./HorizontalMobileNavLink.vue";
import HorizontalMobileNavGroup from "./HorizontalMobileNavGroup.vue";
import { themeConfig } from "@themeConfig";
import { VNodeRenderer } from "@layouts/components/VNodeRenderer";
import { Link } from "@inertiajs/inertia-vue3";

const props = defineProps({
  navItems: {
    type: null,
    required: true,
  },
  drawer: {
    type: Boolean,
    default: false,
  },
});

const { y: windowScrollY } = useWindowScroll();
const { width: windowWidth } = useWindowSize();
const shallShowPageLoading = ref(false);

const { _layoutClasses: layoutClasses, isNavbarBlurEnabled } = useLayouts();
let open = ref(false);

watch(props, (value) => {
  open.value = !open.value;
});
const resolveNavItemComponent = (item) => {
  if ("children" in item) return HorizontalMobileNavGroup;

  return HorizontalMobileNavLink;
};
</script>

<template>
  <div
    class="layout-wrapper"
    :class="layoutClasses(windowWidth, windowScrollY)"
  >
    <HorizontalMobileNav>
      <VNavigation-drawer v-model="open" temporary>
        <template v-slot:prepend>
          <Link to="/" class="d-flex align-start gap-x-2 pa-5">
          <img :src="$page?.props?.site_settings?.media[0]?.original_url" width="40" height="40" />
          <h1 class="font-weight-bold leading-normal text-truncate text-xl">
           {{$page?.props?.site_settings?.site_name}}
          </h1>
          </Link>
        </template>

        <v-divider></v-divider>

        <v-list density="compact" nav>
          <Component
            :is="resolveNavItemComponent(item)"
            v-for="(item, index) in navItems"
            :key="index"
            :item="item"
          />
        </v-list>
      </VNavigation-drawer>
    </HorizontalMobileNav>
    <div
      class="layout-navbar-and-nav-container"
      :class="isNavbarBlurEnabled && 'header-blur'"
    >
      <!-- 👉 Navbar -->
      <div class="layout-navbar">
        <div class="navbar-content-container">
          <slot name="navbar" />
        </div>
      </div>
      <!-- 👉 Navigation -->
      <div class="layout-horizontal-nav d-none d-md-flex toolbar-fixed">
        <v-toolbar class="w-100 tb px-13" elevation="1">
          <HorizontalNav :nav-items="navItems" />
        </v-toolbar>
      </div>
    </div>
    <div class="d-none d-md-flex" style="padding-top: 100px"></div>
    <main class="layout-page-content">
      <template v-if="$slots['content-loading']">
        <template v-if="shallShowPageLoading">
          <slot name="content-loading" />
        </template>
        <template v-else>
          <slot />
        </template>
      </template>
      <template v-else>
        <slot />
      </template>
    </main>

    <!-- 👉 Footer -->
    <footer class="layout-footer">
      <div class="footer-content-container">
        <slot name="footer" />
      </div>
    </footer>
  </div>
</template>
<style lang="scss">
@use "@configured-variables" as variables;
@use "@layouts/styles/placeholders";
@use "@layouts/styles/mixins";
.toolbar-fixed {
  position: fixed;
  width: 100%;
}
.v-toolbar.tb {
  background: rgb(var(--v-theme-surface)) !important;
}
.layout-wrapper {
  &.layout-nav-type-horizontal {
    display: flex;
    flex-direction: column;

    // // TODO(v2): Check why we need height in vertical nav & min-height in horizontal nav
    // min-height: 100%;
    min-block-size: calc(var(--vh, 1vh) * 100);

    .layout-navbar-and-nav-container {
      z-index: 1;
    }

    .layout-navbar {
      z-index: variables.$layout-horizontal-nav-layout-navbar-z-index;
      block-size: variables.$layout-horizontal-nav-navbar-height;

      // ℹ️ For now we are not independently managing navbar and horizontal nav so we won't use below style to avoid conflicting with combo style of navbar and horizontal nav
      // If we add independent style of navbar & horizontal nav then we have to add :not for avoiding conflict with combo styles
      // .layout-navbar-sticky & {
      //   @extend %layout-navbar-sticky;
      // }

      // ℹ️ For now we are not independently managing navbar and horizontal nav so we won't use below style to avoid conflicting with combo style of navbar and horizontal nav
      // If we add independent style of navbar & horizontal nav then we have to add :not for avoiding conflict with combo styles
      // .layout-navbar-hidden & {
      //   @extend %layout-navbar-hidden;
      // }
    }

    // 👉 Navbar
    .navbar-content-container {
      @include mixins.boxed-content;
    }

    // 👉   Content height fixed
    &.layout-content-height-fixed {
      max-block-size: calc(var(--vh) * 100);

      .layout-page-content {
        overflow: hidden;

        > :first-child {
          max-block-size: 100%;
          overflow-y: auto;
        }
      }
    }

    // 👉 Footer
    // Boxed content
    .layout-footer {
      .footer-content-container {
        @include mixins.boxed-content;
      }
    }
  }

  // If both navbar & horizontal nav sticky
  &.layout-navbar-sticky.horizontal-nav-sticky {
    .layout-navbar-and-nav-container {
      position: sticky;
      inset-block-start: 0;
      will-change: transform;
    }
  }

  &.layout-navbar-hidden.horizontal-nav-hidden {
    .layout-navbar-and-nav-container {
      display: none;
    }
  }
}

// 👉 Horizontal nav nav
.layout-horizontal-nav {
  border-top: 1px solid rgba(var(--v-border-color), var(--v-border-opacity));
  z-index: variables.$layout-horizontal-nav-z-index;
  padding-block: 0px !important;
  // .horizontal-nav-sticky & {
  //   width: 100%;
  //   will-change: transform;
  //   position: sticky;
  //   top: 0;
  // }

  // .horizontal-nav-hidden & {
  //   display: none;
  // }

  .horizontal-nav-content-container {
    @include mixins.boxed-content(true);
  }
}
</style>

