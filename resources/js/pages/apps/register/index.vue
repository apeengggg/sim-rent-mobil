V<script setup>

const isPasswordVisible = ref(false)
</script>

<template>
  <div class="auth-wrapper d-flex align-center justify-center pa-4">
    <div class="position-relative my-sm-16">
      <!-- 👉 Top shape -->
      <VImg
        :src="authV1TopShape"
        class="auth-v1-top-shape d-none d-sm-block"
      />

      <!-- 👉 Bottom shape -->
      <VImg
        :src="authV1BottomShape"
        class="auth-v1-bottom-shape d-none d-sm-block"
      />

      <!-- 👉 Auth card -->
      <VCard
        class="auth-card pa-4"
      >
        <VCardItem class="justify-center">
          <template #prepend>
            <div class="d-flex">
              <VNodeRenderer :nodes="themeConfig.app.logo" />
            </div>
          </template>

          <VCardTitle class="font-weight-bold text-h5 py-1">
            {{ themeConfig.app.title }}
          </VCardTitle>
        </VCardItem>

        <VCardText class="pt-2">
          <h5 class="text-h5 font-weight-semibold mb-1">
            Daftar Pengguna Baru
          </h5>
        </VCardText>

        <VCardText>
          <VForm @submit.prevent="doSave">
            <VRow>
              <!-- Username -->
              <VCol cols="6">
                <VTextField
                  v-model="form.nama"
                  label="Nama"
                />
              </VCol>

              <VCol cols="6">
                <VTextField
                  v-model="form.username"
                  label="Username"
                />
              </VCol>

              <VCol cols="6">
                <VTextarea
                  v-model="form.alamat"
                  label="Alamat"
                  rows="1"
                  auto-grow
                />
              </VCol>

              <VCol cols="6">
                <VTextField
                  v-model="form.no_telepon"
                  label="No. Telepon"
                />
              </VCol>

              <VCol cols="6">
                <VFileInput
                  accept="image/*"
                  v-model="form.foto_sim"
                  label="Foto SIM"
                />
              </VCol>

              <VCol cols="6">
                <VTextField
                  v-model="form.no_sim"
                  label="No SIM"
                />
              </VCol>

              <!-- password -->
              <VCol cols="6">
                <VTextField
                  v-model="form.password"
                  label="Password"
                  :type="isPasswordVisible ? 'text' : 'password'"
                  :append-inner-icon="isPasswordVisible ? 'tabler-eye-off' : 'tabler-eye'"
                  @click:append-inner="isPasswordVisible = !isPasswordVisible"
                />
              </VCol>

              <VCol cols="6">
                <VTextField
                  v-model="form.confirmation_password"
                  label="Confirmation Password"
                  :type="isPasswordVisible ? 'text' : 'password'"
                  :append-inner-icon="isPasswordVisible ? 'tabler-eye-off' : 'tabler-eye'"
                  @click:append-inner="isPasswordVisible = !isPasswordVisible"
                />
              </VCol>
              <VCol cols="6">
              </VCol>
              <VCol cols="6" class="d-flex justify-end">
                <VBtn
                  block
                  type="submit"
                >
                  Daftar
                </VBtn>
              </VCol>

              <!-- login instead -->
              <VCol
                cols="12"
                class="text-center text-base"
              >
                <span>Sudah punya akun?</span>
                <RouterLink
                  class="text-primary ms-2"
                  :to="{ name: 'apps-login' }"
                >
                  Login disini
                </RouterLink>
              </VCol>
            </VRow>
          </VForm>
        </VCardText>
      </VCard>
    </div>
  </div>
</template>

<script>
// import
import api from "@/apis/CommonAPI"
import Swal from 'sweetalert2'
import authV1BottomShape from '@images/svg/auth-v1-bottom-shape.svg'
import authV1TopShape from '@images/svg/auth-v1-top-shape.svg'
import AuthProvider from '@/views/pages/authentication/AuthProvider.vue'
import { VNodeRenderer } from '@layouts/components/VNodeRenderer'
import { themeConfig } from '@themeConfig'

export default {
  components: {
    themeConfig,
    VNodeRenderer,
    authV1BottomShape,
    authV1TopShape,
    AuthProvider
  },
  created(){
  },
  mounted(){
    localStorage.removeItem('token')
    localStorage.removeItem('user_data')
    localStorage.removeItem('userAbilities')
  },
  data(){
    return{
      form: {
        email: '',
        password: '',
        remember: '',
      },
      isPasswordVisible: false,
      loading: false,
    }
  },
  methods: {
    async login(){
      this.loading = true

      let uri = `/api/v1/auth/login`;
      let responseBody = await api.jsonApi(uri,'POST', JSON.stringify(this.form));
      console.log('Message', Array.isArray(responseBody.message))
      if( responseBody.status != 200 ){
        let msg = ''
        if(Array.isArray(responseBody.message)){
          msg = responseBody.message.toString()
        }else{
          msg = responseBody.message
        }

        Swal.fire('Error!', msg, 'error')
      }else{
        localStorage.setItem('user_data', JSON.stringify(responseBody.data))
        localStorage.setItem('token', responseBody.data.token)
        this.$router.push('/apps/masters/users');
      }
      this.loading = false
    }
  },
  watch: {

  },
  computed: {

  }
}
</script>

<style lang="scss">
@use "@core-scss/template/pages/page-auth.scss";
</style>

<route lang="yaml">
meta:
  layout: blank
</route>
