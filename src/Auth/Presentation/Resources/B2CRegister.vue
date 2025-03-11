<script setup>
import { Link, useForm } from "@inertiajs/vue3";
import { toastAlert } from "@Composables/useToastAlert";

//## start variable section
const form = useForm({
  email: "",
  password: "",
});
const isPasswordVisible = ref(false);
let ErrorMessage = defineProps(["errorMessage"]);
//## end variable section

//## start register function
let register = () => {
  form.post(route("b2cstore"), {
    onSuccess: () => {

      if (ErrorMessage) {
          toastAlert({
            bgColor: "red",
            icon: "error",
            title: "Fail to send email"
          });
      }
    },
    onError: (error) => {
      console.log(error);
      form.setError("email", error?.email);
      form.setError("password", error?.password);
    },
  });
};
//## end register function
</script>

<template>
  <div class="auth-wrapper d-flex align-center justify-center">
    <div class="auth-card" max-width="448">
      <VCardText class="pt-2">
        <h5 class="text-h5 text-center font-weight-semibold mb-1 primary">
          Enter your email address(B2C)
        </h5>
      </VCardText>

      <VCardText>
        <VForm @submit.prevent="register">
          <VRow>
            <!-- Email -->
            <VCol cols="12">
              <VLabel class="primary pl-1">Enter your work email</VLabel>
              <VTextField
                variant="solo"
                placeholder="Email"
                v-model="form.email"
                :error-messages="form?.errors?.email"
              />
            </VCol>
            <!-- password -->
            <VCol cols="12">
              <VLabel class="primary pl-1">Enter your work password</VLabel>
              <VTextField
                variant="solo"
                v-model="form.password"
                placeholder="Enter Your Password"
                :error-messages="form?.errors?.password"
                :type="isPasswordVisible ? 'text' : 'password'"
                :append-inner-icon="
                  isPasswordVisible ? 'mdi-eye-off-outline' : 'mdi-eye-outline'
                "
                @click:append-inner="isPasswordVisible = !isPasswordVisible"
              />
              <VCol cols="12">
                <p
                  class="font-weight-bold text-blue-grey-lighten-3 text-justify primary"
                >
                  Ed+ will use your data to personalize and improve your
                  experience and to send you information about Ed+. You can
                  change your communication preference anytime. We may use yor
                  data as described in our Privacy Policy. By clicking “Agree
                  and Sign up”, you agree to our Subcriber Agreement and
                  acknowledge that you have read our Privacy Policy for
                  Singapore.
                </p>
              </VCol>
              <VBtn block type="submit" class="primary">
                Agree and Sign up
              </VBtn>
            </VCol>

            <!-- login instead -->
            <VCol cols="12" class="text-center text-base">
              <span class="primary">Already have an account?</span>
              <Link
                class="ms-2 text-decoration-underline primary"
                :href="login"
              >
                Log in
              </Link>
            </VCol>
          </VRow>
        </VForm>
      </VCardText>
    </div>
  </div>
</template>


<style lang="scss">
</style>


