<script setup>
import { useGenerateImageVariant } from "@/@core/composable/useGenerateImageVariant";
import SystemErrorAlert from "@mainRoot/components/SystemErrorAlert.vue"
import authV2LoginMaskDark from "@images/pages/auth-v2-login-mask-dark.png";
import authV2LoginMaskLight from "@images/pages/auth-v2-login-mask-light.png";
import authV2VerifyEmailIllustrationBorderedDark from "@images/pages/auth-v2-verify-email-illustration-bordered-dark.png";
import authV2VerifyEmailIllustrationBorderedLight from "@images/pages/auth-v2-verify-email-illustration-bordered-light.png";
import authV2VerifyEmailIllustrationDark from "@images/pages/auth-v2-verify-email-illustration-dark.png";
import authV2VerifyEmailIllustrationLight from "@images/pages/auth-v2-verify-email-illustration-light.png";
import { VNodeRenderer } from "@layouts/components/VNodeRenderer";
import { themeConfig } from "@themeConfig";
import { defineProps } from "vue";
import { router } from "@inertiajs/core";

const authV1ThemeVerifyEmailMask = useGenerateImageVariant(
  authV2LoginMaskLight,
  authV2LoginMaskDark
);
const authV2VerifyEmailIllustration = useGenerateImageVariant(
  authV2VerifyEmailIllustrationLight,
  authV2VerifyEmailIllustrationDark,
  authV2VerifyEmailIllustrationBorderedLight,
  authV2VerifyEmailIllustrationBorderedDark,
  true
);
const props = defineProps(["verified","sytemErrorMessage"]);
const goLogin = () => {
  router.get("/login");
};
</script>

<template>
  <div class="auth-logo d-flex align-center gap-x-2">
    <div>
      <VNodeRenderer :nodes="themeConfig.app.logo" />
    </div>

    <h5 class="text-h5 font-weight-bold leading-normal text-capitalize">
      {{ themeConfig.app.title }}
    </h5>
  </div>
  <br/> <br/><br/> <br/>
  <SystemErrorAlert :sytemErrorMessage="sytemErrorMessage" v-if="sytemErrorMessage"/>

  <VRow class="auth-wrapper" no-gutters>
    <VCol
      md="8"
      class="d-none d-md-flex align-center justify-center position-relative"
    >
      <div class="d-flex align-center justify-center pa-10">
        <img
          :src="authV2VerifyEmailIllustration"
          class="auth-illustration w-100"
          alt="auth-illustration"
        />
      </div>
      <VImg
        :src="authV1ThemeVerifyEmailMask"
        class="d-none d-md-flex auth-footer-mask"
        alt="auth-mask"
      />
    </VCol>

    <VCol
      cols="12"
      md="4"
      class="auth-card-v2 d-flex align-center justify-center"
      style="background-color: rgb(var(--v-theme-surface))"
    >
      <VCard flat :max-width="500" class="mt-12 mt-sm-0 pa-4">
        <VCardText>
          <h5 class="text-h5 font-weight-semibold mb-1" v-if="props?.verified">
            Email is successfully verified ✉️
          </h5>
          <h5 class="text-h5 font-weight-semibold mb-1" v-else>
            Verify your email ✉️
          </h5>
          <p v-if="props?.verified">
            Your email is now verified. Please press the button from below and
            continue.
          </p>
          <p v-else>
            Account activation link sent to your email address:
            hello@example.com Please follow the link inside to continue.
          </p>

          <VBtn v-if="props?.verified" block @click="goLogin" class="mb-6">
            Go Login
          </VBtn>

          <div class="d-flex align-center justify-center">
            <span class="me-1">Didn't get the mail? </span
            ><a href="#">Resend</a>
          </div>
        </VCardText>
      </VCard>
    </VCol>
  </VRow>
</template>

<style lang="scss">
@use "@styles/@core/template/pages/page-auth.scss";
</style>
