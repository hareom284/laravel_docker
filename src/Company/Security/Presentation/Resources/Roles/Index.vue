<script setup>
import Create from "./Create.vue";
import Edit from "./Edit.vue";
import AdminLayout from "@Layouts/Dashboard/AdminLayout.vue";
import { usePage } from "@inertiajs/vue3";
import { computed, defineProps } from "vue";
import deleteItem from "@Composables/useDeleteItem.js";
import {
  serverParams,
  onColumnFilter,
  searchItems,
  truncatedText,
  onPageChange,
  onPerPageChange,
  serverPage,
  serverPerPage,
} from "@Composables/useServerSideDatable.js";

//## start variable section
let props = defineProps(["roles", "permissions", "auth", "flash"]);
let permissions = computed(() => usePage().props.auth.data.permissions);

serverPage.value = ref(props.roles.meta.current_page ?? 1);
serverPerPage.value = ref(10);
//## end variable section
let options = ref({
  enabled: true,
  mode: "pages",
  perPage: props.roles.meta.per_page,
  setCurrentPage: props.roles.meta.current_page,
  perPageDropdown: [10, 20, 50, 100],
  dropdownAllowAll: false,
});
//## start datatable section
let columns = [
  {
    label: "NAME",
    field: "name",
    sortable: false,
  },
  {
    label: "PERMISSION",
    field: "permission",
    sortable: false,
  },
  {
    label: "DESCRIPTION",
    field: "description",
    sortable: false,
  },
  {
    label: "GUARD NAME",
    field: "guard_name",
    sortable: false,
  },
  {
    label: "ACTION",
    field: "action",
    sortable: false,
  },
];

//## delete role and delete in database
const deleteRole = (id) => {
  deleteItem(id, "roles");
};

//## watch per page change
watch(serverPerPage, function (value) {
  onPerPageChange(value);
});
</script>


<template>
  <AdminLayout>
    <section>
      <VCard>
        <VCardText class="d-flex flex-wrap gap-4">
          <!-- 👉 Export button -->
          <!-- 👉 Search  -->
          <VTextField
            @keyup.enter="searchItems"
            v-model="serverParams.search"
            placeholder="Search Role"
            density="compact"
          />
          <VSpacer />

          <div class="app-user-search-filter d-flex align-center justify-end">
            <!-- 👉 Add permission button -->
            <Create
              :permissions="props.permissions"
              :flash="flash"
              v-if="permissions.includes('create_role')"
            />
          </div>
        </VCardText>

        <VDivider />

        <vue-good-table
          class="role-data-table"
          mode="remote"
          @column-filter="onColumnFilter"
          :totalRows="props.roles.meta.total"
          styleClass="vgt-table"
          :pagination-options="options"
          :rows="props.roles.data"
          :columns="columns"
        >
          <template #table-row="dataProps">
            <div
              v-if="dataProps.column.field == 'permission'"
              class="flex flex-wrap"
              style="max-width: 600px"
            >
              <v-chip
                v-for="permission in dataProps.row.permissions"
                :key="permission.id"
                class="ma-2"
                color="primary"
                size="small"
                >{{ permission.name }}</v-chip
              >
            </div>
            <div
              v-if="dataProps.column.field == 'description'"
              class="flex flex-wrap"
            >
              <span>{{ truncatedText(dataProps.row.description) }}</span>
            </div>
            <div
              v-if="dataProps.column.field == 'action'"
              class="flex flex-nowrap"
            >
              <div class="d-flex">
                <Edit
                  :permissions="props.permissions"
                  :role="dataProps.row"
                  :flash="flash"
                  v-if="permissions.includes('edit_role')"
                />
                <VBtn
                  density="compact"
                  icon="mdi-trash"
                  class="ml-2"
                  color="secondary"
                  variant="text"
                  v-if="permissions.includes('delete_role')"
                  @click="deleteRole(dataProps.row.id)"
                >
                </VBtn>
              </div>
            </div>
          </template>
          <template #pagination-bottom>
            <VRow class="pa-4">
              <VCol cols="12" class="d-flex justify-space-between">
                <span
                  >Showing {{ props.roles.meta.from }} to
                  {{ props.roles.meta.to }} of
                  {{ props.roles.meta.total }} entries</span
                >
                <div>
                  <div class="d-flex align-center">
                    <span class="me-2">Show</span>
                    <VSelect
                      v-model="serverPerPage"
                      density="compact"
                      :items="options.perPageDropdown"
                    ></VSelect>
                    <VPagination
                      v-model="serverPage"
                      size="small"
                      :total-visible="5"
                      :length="props.roles.meta.last_page"
                      @next="onPageChange"
                      @prev="onPageChange"
                      @click="onPageChange"
                    />
                  </div>
                </div>
              </VCol>
            </VRow>
          </template>
        </vue-good-table>

        <VDivider />
      </VCard>
    </section>
  </AdminLayout>
</template>


<style lang="scss">
.app-user-search-filter {
  inline-size: 24.0625rem;
}
.role-data-table table.vgt-table {
  background-color: rgb(var(--v-theme-surface));
  border-color: rgb(var(--v-theme-surface));
}
.role-data-table table.vgt-table td {
  color: rgba(var(--v-theme-on-background), var(--v-high-emphasis-opacity));
}
.role-data-table table.vgt-table thead th {
  background: rgb(var(--v-theme-surface)) !important;
  color: rgba(var(--v-theme-on-background), var(--v-high-emphasis-opacity));
}
.text-capitalize {
  text-transform: capitalize;
}

.user-list-name:not(:hover) {
  color: rgba(var(--v-theme-on-background), var(--v-high-emphasis-opacity));
}
</style>
