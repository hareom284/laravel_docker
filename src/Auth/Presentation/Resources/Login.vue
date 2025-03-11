<script setup>
import { VForm } from "vuetify/components";
import { themeConfig } from "@themeConfig";
import { requiredValidator, emailValidator } from "@validators";
import { Link, useForm } from "@inertiajs/vue3";
import { defineProps } from "vue";
import SystemErrorAlert from "@mainRoot/components/SystemErrorAlert.vue";
import { toastAlert } from "@Composables/useToastAlert";
const isPasswordVisible = ref(false);
const rememberMe = ref(false);
let props = defineProps(["errorMessage", "sytemErrorMessage", "tenant"]);

let form = useForm({
  email: "",
  password: "",
});

/***
 *  `${props?.tenant}login-post
 *   this will get tenant route name that extends @route c.login-post  if it has
 *   organiztion or just simple @route login-post
 */

const onSubmit = () => {
  form.post(route('login-post'), {
    onSuccess: () => {
     console.log("okay")
    },
    onError: (error) => {
      toastAlert({
        title: "Invalid Creditional",
        icon: "error",
        bgColor: "red",
        textColor: "white",
        iconColor: "white",
      });
    },
  });
};
</script>


<template>
  <div class="container">
    <div class="layout-navbar">
      <div
        class="navbar-content-container d-flex justify-space-between px-10 py-5"
      >
        <h1 class="text-h5 font-weight-bold leading-normal text-capitalize">
          {{ themeConfig.app.title }}
        </h1>
        <VBtn color="primary" class="b-0 text-white">
          <Link :href="route('register')" class="text-white"> Register </Link>
        </VBtn>
      </div>
    </div>
    <!-- <SystemErrorAlert
      sytemErrorMessage="Something is wrong"
      v-if="form.errors"
    /> -->
    <VDivider></VDivider>
    <div class="login-bg">
      <VRow
        no-gutters
        class="auth-wrapper d-flex justify-center align-center"
        style="height: 100vh"
      >
        <VCol
          cols="12"
          md="4"
          class="auth-card-v2 d-flex align-center justify-center"
          style="background-color: rgb(var(--v-theme-surface))"
        >
          <div :max-width="500" class="mt-12 mt-sm-0 pa-4">
            <VCardText>
              <h5 class="text-h4 text-center font-weight-semibold mb-1 primary">
                Enter your email address
              </h5>
            </VCardText>
            <VCardText>
              <VForm @submit.prevent="onSubmit">
                <VRow class="login-field">
                  <!-- email -->
                  <VCol cols="12">
                    <label-input class="primary"
                      >Enter your work email</label-input
                    >
                    <VTextField
                      variant="solo"
                      v-model="form.email"
                      type="email"
                      class="bg-primary"
                      :rules="[requiredValidator, emailValidator]"
                      :error-messages="form?.errors?.email"
                    />
                  </VCol>
                  <!-- password -->
                  <VCol cols="12">
                    <label-input class="primary"
                      >Enter your password</label-input
                    >
                    <VTextField
                      variant="solo"
                      v-model="form.password"
                      :rules="[requiredValidator]"
                      :type="isPasswordVisible ? 'text' : 'password'"
                      :error-messages="form?.errors?.password"
                      :append-inner-icon="
                        isPasswordVisible
                          ? 'mdi-eye-off-outline'
                          : 'mdi-eye-outline'
                      "
                      @click:append-inner="
                        isPasswordVisible = !isPasswordVisible
                      "
                    />

                    <span
                      style="color: red; padding-top: 10px"
                      v-if="props?.errorMessage"
                    >
                      {{ props?.errorMessage }}
                    </span>
                    <div
                      class="d-flex align-center flex-wrap justify-space-between mt-1 mb-4"
                    >
                      <VCheckbox
                        style="color: black !important"
                        v-model="rememberMe"
                        label="Remember me"
                      />
                    </div>
                    <VBtn block class="bg-primary" type="submit"> Login </VBtn>
                  </VCol>
                  <!-- create account -->
                  <VCol cols="12" class="text-base text-center primary">
                    <span>New on our platform?</span>
                    <Link
                      class="ms-2 text-decoration-underline"
                      :href="register"
                    >
                      Sign up
                    </Link>
                  </VCol>
                </VRow>
              </VForm>
            </VCardText>
          </div>
        </VCol>
      </VRow>
    </div>
  </div>
</template>


<style lang="scss">
@use "@styles/@core/template/pages/page-auth.scss";
.login-bg {
  background: url("/public/images/register.png") 100% no-repeat;
  height: 100%;
  background-size: 100% 100%;
  z-index: -1;
}
.v-messages__message {
  color: red !important;
}
</style>
