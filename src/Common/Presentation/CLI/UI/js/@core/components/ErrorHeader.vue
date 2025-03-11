<script setup>
const props = defineProps({
  errorCode: {
    type: String,
    required: true,
  },
  errorTitle: {
    type: String,
    required: true,
  },
  errorDescription: {
    type: String,
    required: true,
  },
})
</script>

<template>
  <div class="text-center mb-4">
    <!-- 👉 Title and subtitle -->
    <h1 class="text-h1">
      {{ props.errorCode }}
    </h1>
    <h5 class="text-h5 font-weight-semibold mb-3">
      {{ props.errorTitle }}
    </h5>
    <p>{{ props.errorDescription }}</p>
  </div>
</template>
