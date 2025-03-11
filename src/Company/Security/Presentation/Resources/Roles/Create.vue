<script setup>
import { watch, defineProps, computed, ref } from "vue";
import { useForm } from "@inertiajs/vue3";
import { requiredValidator } from "@validators";
import { toastAlert } from "@Composables/useToastAlert";
//# start variable section
const isDialogVisible = ref(false);
let props = defineProps(["permissions", "flash"]);
const isFormValid = ref(false);
const refForm = ref();
//## end variable  section

//## start get only module from permission
let modules = computed(() => {
  let permissions = [];
  props.permissions.forEach((permission) => {
    let perArray = permission.name.split("_");
    permissions.push(perArray[1]);
  });
  return new Set(permissions);
});
//## end get only module from permission

//## start get modules with related permission
let permissions_modules = computed(() => {
  let newArrays = [];
  modules.value.forEach((item, index) => {
    newArrays.push({
      key: index,
      name: item,
      permissions: props.permissions.filter((permission) =>
        permission.name.includes(item)
      ),
    });
  });
  return newArrays;
});
//## end get modules with related permission

//## start for form submit
let form = useForm({
  name: "",
  description: "",
  selectedIds: [],
});
//## end for form submit

//## start uncheck modules when selectedIds array is empty
let watchSelectedIds = watch(form.selectedIds, (value) => {
  if (value.length <= 0) {
    document.getElementById("check-all").checked = false;
  }
});
//## end uncheck modules when selectedIds array is empty

//## start select all permissions
let selectAll = () => {
  form.selectedIds = [];
  let isChecked = document.getElementById("check-all").checked;
  if (isChecked) {
    modules.value.forEach((item, index) => {
      document.getElementById(`module-checkbox-${index}`).checked = true;
      selectByModule(item, index);
    });
  } else {
    modules.value.forEach((item, index) => {
      document.getElementById(`module-checkbox-${index}`).checked = false;
    });
    form.selectedIds = [];
  }
};
//## end select all permissions

//## start select permission by module
let selectByModule = (item, index) => {
  let isChecked = document.getElementById(`module-checkbox-${index}`).checked;
  if (isChecked) {
    props.permissions.forEach((per) => {
      if (
        per.name.split("_")[1].includes(item) &&
        !form.selectedIds.includes(per.id)
      ) {
        form.selectedIds.push(per.id);
      }
    });
  } else {
    props.permissions.forEach((per) => {
      if (per.name.split("_")[1].includes(item)) {
        form.selectedIds = form.selectedIds.filter((item) => item != per.id);
      }
    });
  }
};
//## end select permission by module

//## start save role
let saveRole = () => {
  refForm.value?.validate().then(({ valid }) => {
    if (valid) {
      form.post(route("roles.store"), {
        onSuccess: () => {
          toastAlert({
            title: props.flash?.successMessage,
          });
          isDialogVisible.value = false;
          form.reset();
          refForm.value?.reset();
          refForm.value?.resetValidation();
        },
        onError: (error) => {
          // form.setError("name", error?.name);
        },
      });
    }
  });
};
//## end save role
</script>


<template>
  <VDialog v-model="isDialogVisible" max-width="1000">
    <!-- Dialog Activator -->
    <template #activator="{ props }">
      <VBtn v-bind="props"> Add Role </VBtn>
    </template>

    <!-- Dialog Content -->
    <VCard title="Add Role">
      <VForm ref="refForm" v-model="isFormValid" @submit.prevent="saveRole">
        <DialogCloseBtn
          variant="text"
          size="small"
          @click="isDialogVisible = false"
        />

        <VCardText>
          <VRow>
            <VCol cols="12">
              <VTextField
                :error-messages="form.errors?.name"
                label="Role Name"
                v-model="form.name"
                :rules="[requiredValidator]"
              />
            </VCol>
            <VCol cols="12">
              <VTextarea
                label="Description"
                v-model="form.description"
                :error-messages="form.errors?.description"
              />
            </VCol>
            <VCol cols="12">
              <div class="mb-6 flex-auto">
                <label for="permission" class="px-6"
                  >Assign Permission To Roles</label
                >

                <div class="relative overflow-x-auto">
                  <table class="w-100">
                    <thead>
                      <tr>
                        <th scope="col" class="px-4 py-4">
                          <div class="flex items-center">
                            <VCheckbox
                              id="check-all"
                              label="Module"
                              density="compact"
                              @click="selectAll"
                              style="font-weight: bold"
                            >
                              <template #label="{ label }">
                                <span>{{ label }}</span>
                              </template>
                            </VCheckbox>
                          </div>
                        </th>
                        <th scope="col" class="px-6 py-3" align="start">
                          <VLabel> Permissions </VLabel>
                        </th>
                      </tr>
                    </thead>
                    <tbody class="text-xs">
                      <tr v-for="item in permissions_modules" :key="item.key">
                        <td class="px-4 py-4">
                          <VCheckbox
                            :id="'module-checkbox-' + item.key"
                            @click="selectByModule(item.name, item.key)"
                            :label="item.name"
                            density="compact"
                            style="font-weight: bold"
                          >
                            <template #label="{ label }">
                              <span class="text-no-wrap">{{ label }}</span>
                            </template>
                          </VCheckbox>
                        </td>
                        <td class="px-4 py-4">
                          <VRow>
                            <VCol
                              cols="4"
                              md="2"
                              v-for="permission in item.permissions"
                              :key="permission.id"
                            >
                              <VCheckbox
                                v-model="form.selectedIds"
                                :value="permission.id"
                                :label="permission.name"
                                density="compact"
                                style="font-weight: bold"
                              >
                                <template #label="{ label }">
                                  <span>{{ label.split("_")[0] }}</span>
                                </template>
                              </VCheckbox>
                            </VCol>
                          </VRow>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
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
<style lang="scss" scoped>
table td,
table td * {
  vertical-align: top;
}
</style>
