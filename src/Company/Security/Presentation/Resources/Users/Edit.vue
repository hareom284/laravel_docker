<script setup>
// component
import AppDateTimePicker from "@core/components/AppDateTimePicker.vue";
import ImageUpload from "@Composables/ImageUpload.vue";
import { useForm } from "@inertiajs/vue3";
import { ref } from "vue";
import { emailValidator, requiredValidator } from "@validators";
import { toastAlert } from "@Composables/useToastAlert";
const isDialogVisible = ref(false);
const isPasswordVisible = ref(false);
const isFormValid = ref(false);
const refForm = ref();
let props = defineProps(["user", "roles", "flash", "organizations"]);

let form = useForm({
  role: props?.user?.roles[0]?.id,
  name: props.user.name,
  password: "",
  organization_id: props.user.organization_id,
  contact_number: props.user.contact_number,
  email: props.user.email,
  image: props?.user?.image[0]?.original_url || "",
  _method: "put",
  dob: props.user.dob,
});

// Update create form
let handleUpdate = (id) => {
  form.post(route("users.update", { id: id }), {
    onSuccess: (status) => {
      toastAlert({
        title: props.flash?.successMessage,
      });
      isDialogVisible.value = false;
    },
    onError: (error) => {
      console.log("Something unexcepted");
    },
  });
};

onUpdated(() => {
  form.role = props?.user?.roles[0]?.id;
  form.name = props.user.name;
  form.contact_number = props.user.contact_number;
  form.email = props.user.email;
  (form.organization_id = props.user.organization_id),
    (form.image = props?.user?.image[0]?.original_url || "");
  form.dob = props.user.dob;
});
</script>


<template>
  <VDialog v-model="isDialogVisible" max-width="900" persistent>
    <!-- Dialog Activator -->
    <template #activator="{ props }">
      <VBtn
        density="compact"
        icon="mdi-pencil"
        class="ml-2"
        color="secondary"
        variant="text"
        v-bind="props"
      >
      </VBtn>
    </template>
    <!-- Dialog Content -->
    <VCard title="User Particulars">
      <VForm
        @submit.prevent="handleUpdate(props.user.id)"
        ref="refForm"
        v-model="isFormValid"
      >
        <DialogCloseBtn
          variant="text"
          size="small"
          @click="isDialogVisible = false"
        />
        <VCardText>
          <VRow>
            <VCol cols="6">
              <VRow>
                <VCol cols="12">
                  <VSelect
                    label="User Roles"
                    v-model="form.role"
                    :items="roles"
                    item-title="name"
                    item-value="id"
                    :error-messages="form?.errors?.role"
                    :rules="[requiredValidator]"
                  />
                </VCol>
                <VCol cols="12" v-if="form.role === 4 || form.role === 5">
                  <VSelect
                    label="Select Organization"
                    v-model="form.organization_id"
                    :items="organizations"
                    item-title="name"
                    item-value="id"
                    :rules="[requiredValidator]"
                    :error-messages="form?.errors?.organization_id"
                  />
                </VCol>
                <VCol cols="12">
                  <VTextField
                    label="Name"
                    v-model="form.name"
                    class="w-100"
                    :error-messages="form?.errors?.name"
                    :rules="[requiredValidator]"
                  />
                </VCol>
                <VCol cols="12">
                  <VTextField
                    label="Contact Number"
                    v-model="form.contact_number"
                    class="w-100"
                    :error-messages="form?.errors?.contact_number"
                    :rules="[requiredValidator]"
                  />
                </VCol>
                <VCol cols="12">
                  <VTextField
                    label="Email"
                    v-model="form.email"
                    class="w-100"
                    :error-messages="form?.errors?.email"
                    :rules="[requiredValidator, emailValidator]"
                  />
                </VCol>
                <VCol cols="12">
                  <VTextField
                    label="Password"
                    v-model="form.password"
                    :type="isPasswordVisible ? 'text' : 'password'"
                    :error-messages="form?.errors?.password"
                    :append-inner-icon="
                      isPasswordVisible
                        ? 'mdi-eye-off-outline'
                        : 'mdi-eye-outline'
                    "
                    @click:append-inner="isPasswordVisible = !isPasswordVisible"
                  />
                </VCol>
                <VCol cols="12">
                  <AppDateTimePicker v-model="form.dob" label="Dob" />
                </VCol>
              </VRow>
            </VCol>
            <VCol cols="6">
              <ImageUpload v-model="form.image" :old_img="form.image" />
            </VCol>
            <VCol cols="12" class="d-flex justify-center">
              <VBtn type="submit" class="me-3"> Submit </VBtn>
              <VBtn
                type="reset"
                variant="outlined"
                color="secondary"
                @click="isDialogVisible = false"
              >
                Cancel
              </VBtn>
            </VCol>
          </VRow>
        </VCardText>
      </VForm>
    </VCard>
  </VDialog>
</template>
