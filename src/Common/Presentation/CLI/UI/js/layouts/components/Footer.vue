<template>
  <div class="h-100 d-flex align-center justify-space-between">
    <!-- 👉 Footer: left content -->
    <span class="d-flex align-center">
      &copy;
      {{ new Date().getFullYear() }}
      Made With
      <VIcon
        icon="mdi-heart-outline"
        color="error"
        size="1.25rem"
        class="mx-1"
      />
      By
      <span href="#" target="_blank" rel="noopener noreferrer" class="ms-1"
        >Leaptechnology</span
      >
    </span>
    <!-- 👉 Footer: right content -->
    <span class="d-md-flex gap-x-4 text-primary d-none">
      <a
        href="https://pixinvent.com/demo/materialize-vuejs-admin-template/documentation/"
        target="noopener noreferrer"
        >Documentation</a
      >
      <a href="#" target="noopener noreferrer">Support</a>
    </span>
  </div>
</template>
