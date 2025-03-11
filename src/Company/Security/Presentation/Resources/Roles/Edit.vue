<script setup>
import { watch, defineProps, computed, ref, onUpdated } from "vue";
import { useForm } from "@inertiajs/vue3";
import { requiredValidator } from "@validators";
import { toastAlert } from "@Composables/useToastAlert";
const isDialogVisible = ref(false);
let props = defineProps(["permissions", "role", "flash"]);
const isFormValid = ref(false);
const refForm = ref();
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
  name: props.role.name,
  description: props.role.description,
  selectedIds: [],
});
//## end for form submit

//## start uncheck modules when selectedIds array is empty
let watchSelectedIds = watch(form.selectedIds, (value) => {
  if (value.length <= 0) {
    document.getElementById("check-all-edit" + props.role.id).checked = false;
  }
});
//## end uncheck modules when selectedIds array is empty

//## start select all permissions
let selectAll = () => {
  form.selectedIds = [];
  let isChecked = document.getElementById(
    "check-all-edit" + props.role.id
  ).checked;
  if (isChecked) {
    modules.value.forEach((item, index) => {
      document.getElementById(
        `${props.role.id}-edit-checkbox-${index}`
      ).checked = true;
      selectByModule(item, index);
    });
  } else {
    modules.value.forEach((item, index) => {
      document.getElementById(
        `${props.role.id}-edit-checkbox-${index}`
      ).checked = false;
    });
    form.selectedIds = [];
  }
};
//## end select all permissions

//## start select permission by module
let selectByModule = (item, index) => {
  let isChecked = document.getElementById(
    `${props.role.id}-edit-checkbox-${index}`
  ).checked;
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

//## start updateRole
let updateRole = (id) => {
  refForm.value?.validate().then(({ valid }) => {
    if (valid) {
      form.put(route("roles.update", { id: id }), {
        onSuccess: () => {
          toastAlert({
            title: props.flash?.successMessage,
          });
          isDialogVisible.value = false;
          refForm.value?.reset();
          refForm.value?.resetValidation();
        },
        onError: (error) => {
          form.setError("name", error?.name);
        },
      });
      //## form.reset();
    }
  });
};
//## end updateRole

//## start reative name and description when edit
onUpdated(() => {
  form.selectedIds = [];
  props.role.permissions.filter((rp) => form.selectedIds.push(rp.id));
  form.name = props.role.name;
  form.description = props.role.description;
});
//## end reative name and description when edit
</script>

<template>
  <VDialog v-model="isDialogVisible" max-width="1000">
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
    <VCard title="Add Role">
      <VForm
        ref="refForm"
        v-model="isFormValid"
        @submit.prevent="updateRole(props.role.id)"
      >
        <DialogCloseBtn
          variant="text"
          size="small"
          @click="isDialogVisible = false"
        />

        <VCardText>
          <VRow>
            <VCol cols="12">
              <VTextField
                label="Role Name"
                v-model="form.name"
                :rules="[requiredValidator]"
              />
            </VCol>
            <VCol cols="12">
              <VTextarea label="Description" v-model="form.description" />
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
                              :id="'check-all-edit' + props.role.id"
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
                            :id="role.id + '-edit-checkbox-' + item.key"
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
