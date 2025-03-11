<script setup>
import { defineProps,ref } from "vue";

let props = defineProps(["sytemErrorMessage"]);



</script>

  <template>
  <VAlert
    variant="tonal"
    density="compact"
    type="error"
    closable
    class="mt-4 mb-4"
    close-label="Close Alert"
    style="padding: 6px 16px"
  >
    <template #text>
      <span style="font-size:24px:"> System Error </span>
      <br />
      <span>
        {{ props.sytemErrorMessage }}
      </span>
    </template>
  </VAlert>
</template>

