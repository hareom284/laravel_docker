<script setup>
import ThemeSwitcher from "@core/components/ThemeSwitcher.vue";
const themes = [
  {
    name: "system",
    icon: "mdi-laptop",
  },
  {
    name: "light",
    icon: "mdi-weather-sunny",
  },
  {
    name: "dark",
    icon: "mdi-weather-night",
  },
];
</script>

<template>
  <ThemeSwitcher :themes="themes" />
</template>
