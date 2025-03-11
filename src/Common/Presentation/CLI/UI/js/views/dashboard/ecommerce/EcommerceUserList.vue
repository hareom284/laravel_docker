<script setup>
import { useUserListStore } from '@/views/apps/user/useUserListStore'
import { avatarText } from '@core/utils/formatters'

const userListStore = useUserListStore()
const searchQuery = ref('')
const selectedRole = ref()
const selectedPlan = ref()
const selectedStatus = ref()
const rowPerPage = ref(7)
const currentPage = ref(1)
const totalPage = ref(1)
const totalUsers = ref(0)
const users = ref([])

// 👉 Fetching users
const fetchUsers = () => {
  userListStore.fetchUsers({
    q: searchQuery.value,
    status: selectedStatus.value,
    plan: selectedPlan.value,
    role: selectedRole.value,
    perPage: rowPerPage.value,
    currentPage: currentPage.value,
  }).then(response => {
    users.value = response.data.users
    totalPage.value = response.data.totalPage
    totalUsers.value = response.data.totalUsers
  }).catch(error => {
    console.error(error)
  })
}

watchEffect(fetchUsers)

const resolveUserRoleVariant = role => {
  if (role === 'subscriber')
    return {
      color: 'primary',
      icon: 'mdi-account-outline',
    }
  if (role === 'author')
    return {
      color: 'warning',
      icon: 'mdi-cog-outline',
    }
  if (role === 'maintainer')
    return {
      color: 'success',
      icon: 'mdi-chart-donut',
    }
  if (role === 'editor')
    return {
      color: 'info',
      icon: 'mdi-pencil-outline',
    }
  if (role === 'admin')
    return {
      color: 'error',
      icon: 'mdi-laptop',
    }
  
  return {
    color: 'primary',
    icon: 'mdi-account-outline',
  }
}

const resolveUserStatusVariant = stat => {
  if (stat === 'pending')
    return 'warning'
  if (stat === 'active')
    return 'success'
  if (stat === 'inactive')
    return 'secondary'
  
  return 'primary'
}
</script>

<template>
  <VCard>
    <VTable class="text-no-wrap table-header-bg">
      <!-- 👉 table head -->
      <thead>
        <tr>
          <th scope="col">
            USER
          </th>
          <th scope="col">
            EMAIL
          </th>
          <th scope="col">
            ROLE
          </th>
          <th scope="col">
            STATUS
          </th>
        </tr>
      </thead>

      <!-- 👉 table body -->
      <tbody>
        <tr
          v-for="user in users"
          :key="user.id"
        >
          <!-- 👉 User -->
          <td>
            <div class="d-flex align-center">
              <VAvatar
                variant="tonal"
                :color="resolveUserRoleVariant(user.role).color"
                class="me-3"
                size="34"
              >
                <VImg
                  v-if="user.avatar"
                  :src="user.avatar"
                />
                <span
                  v-else
                  class="text-sm"
                >{{ avatarText(user.fullName) }}</span>
              </VAvatar>

              <div class="d-flex flex-column">
                <h6 class="text-sm">
                  <RouterLink
                    :to="{ name: 'apps-user-view-id', params: { id: user.id } }"
                    class="font-weight-medium user-list-name"
                  >
                    {{ user.fullName }}
                  </RouterLink>
                </h6>
                <span class="text-xs">@{{ user.username }}</span>
              </div>
            </div>
          </td>

          <!-- 👉 Email -->
          <td>
            {{ user.email }}
          </td>

          <!-- 👉 Role -->
          <td>
            <VIcon
              :icon="resolveUserRoleVariant(user.role).icon"
              :color="resolveUserRoleVariant(user.role).color"
              :size="22"
              class="me-3"
            />
            <span class="text-capitalize text-base">{{ user.role }}</span>
          </td>

          <!-- 👉 Status -->
          <td>
            <VChip
              :color="resolveUserStatusVariant(user.status)"
              size="small"
              class="text-capitalize"
            >
              {{ user.status }}
            </VChip>
          </td>
        </tr>
      </tbody>

      <!-- 👉 table footer  -->
      <tfoot v-show="!users.length">
        <tr>
          <td
            colspan="7"
            class="text-center"
          >
            No data available
          </td>
        </tr>
      </tfoot>
    </VTable>
  </VCard>
</template>

<style lang="scss">
.text-capitalize {
  text-transform: capitalize;
}

.user-list-name:not(:hover) {
  color: rgba(var(--v-theme-on-background), var(--v-high-emphasis-opacity));
}
</style>
