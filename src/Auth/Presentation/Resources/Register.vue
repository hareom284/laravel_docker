<script setup>
import { themeConfig } from "@themeConfig";
import { onMounted, ref } from "vue";
import { Link } from "@inertiajs/vue3";
import { router } from "@inertiajs/core";
import { toastAlert } from "@Composables/useToastAlert";

import B2CRegister from "./B2CRegister.vue";
import B2BRegister from "./B2BRegister.vue";
let organization = ref(false);
let isAlertVisible = ref(true);
const items = [
  "California",
  "Colorado",
  "Florida",
  "Georgia",
  "Texas",
  "Wyoming",
];
const isPasswordVisible = ref(false);
let agreed = ref("");
let props = defineProps(["ErrorMessage"]);
</script>


<template>
  <div class="layout-navbar">
    <div
      class="navbar-content-container d-flex justify-space-between px-10 py-5"
    >
      <h1 class="text-h5 font-weight-bold leading-normal text-capitalize">
        {{ themeConfig.app.title }}
      </h1>
      <VBtn color="primary" class="b-0 text-white">
        <Link :href="route('login')" class="text-white"> Login </Link>
      </VBtn>
    </div>
  </div>
 <SystemErrorAlert :sytemErrorMessage="sytemErrorMessage" v-if="sytemErrorMessage"/>

  <VDivider></VDivider>
  <div class="regiser-image">
    <div style="max-width: 1024px; margin-inline: auto">

      <VRow class="d-flex justify-center" style="padding-top: 100px">
        <VCol lg="6" md="6" sm="12" class="text-center">
          <div class="auth-card" max-width="448">
            <VCardText class="pt-2">
              <h1 class="mb-1 pb-3 text-center primary">
                Are you signing up under
              </h1>
              <h1 class="text-center primary">an Organization account?</h1>
            </VCardText>

            <VCardText>
              <VRow class="">
                <VCol cols="12" class="">
                  <VRadioGroup
                    v-model="organization"
                    inline
                    class="border-dashed pl-14 w-50 mx-auto"
                    style="border: 3px solid #5271ff; border-radius: 10px"
                  >
                    <VRadio label="Yes" class="primary" value="on" />
                    <VRadio label="No" class="primary" value="off" />
                  </VRadioGroup>
                </VCol>
                <VCol v-if="organization == 'on'">
                  <h2 class="text-center pb-3 primary">
                    Please Select Organization
                  </h2>
                  <VAutocomplete
                    variant="outlined"
                    :items="items"
                    class="w-75 mx-auto"
                  />
                </VCol>

                <!-- login instead -->
                <VCol cols="9" class="d-flex mx-auto">
                  <VCheckbox v-model="agreed" class="primary" />
                  <Vlabel class="text-justify pt-3 pl-3 primary">
                    Yes! I would like to receive udpates, special offers, and
                    other information from Ed+
                  </Vlabel>
                </VCol>
              </VRow>
            </VCardText>
          </div>
        </VCol>
        <VCol md="6" lg="6" sm="12" v-if="organization">
          <B2BRegister
            :errorMessage="ErrorMessage"
            v-if="organization == 'on'"
            :class="
              organization == 'on'
                ? 'animate__animated animate__backInRight animate__delay-0.1s'
                : 'text-clip'
            "
          />
          <B2CRegister
            v-if="organization == 'off'"
            :class="
              organization == 'off'
                ? 'animate__animated animate__backInRight animate__delay-0.1s'
                : 'text-clip'
            "
          />
        </VCol>
      </VRow>
    </div>
  </div>
</template>


<style lang="scss">
@use "@styles/@core/template/pages/page-auth.scss";
.regiser-image {
  background: url("/public/images/signupnew.png") 100% no-repeat;
  height: 100vh;
  background-size: cover;
}
.primary {
  color: #001a8f !important;
}

</style>
