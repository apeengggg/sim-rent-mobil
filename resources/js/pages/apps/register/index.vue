<script setup>
import authV1BottomShape from '@images/svg/auth-v1-bottom-shape.svg'
import authV1TopShape from '@images/svg/auth-v1-top-shape.svg'
import { VNodeRenderer } from '@layouts/components/VNodeRenderer'
import { themeConfig } from '@themeConfig'
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

        <VRow>
          <VCol cols="12">
            <VAlert v-if="successMessage != '' && successMessage != null" color="success" variant="tonal" @click="() => this.successMessage = ''">
              {{successMessage}}
            </VAlert>
            <VAlert v-if="errorMessage != '' && errorMessage != null" color="error" variant="tonal" @click="() => this.errorMessage = ''">
              {{errorMessage}}
            </VAlert>
          </VCol>
        </VRow>

        <VCardText class="pt-2">
          <h5 class="text-h5 font-weight-semibold mb-1">
            Daftar Pengguna Baru
          </h5>
        </VCardText>

        <VCardText>
          <VForm @submit.prevent="doSave($event)">
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
                  accept="image/jpg, image/png, image/jpeg"
                  v-model="form.foto_sim"
                  label="Foto SIM"
                  @input="handleFileUpload($event)"
                />
              </VCol>

              <VCol cols="6">
                <VTextField
                  v-model="form.no_sim"
                  label="No SIM"
                  type="number"
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
import utils from "@/utils/CommonUtils"

export default {
  components: {},
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
        nama: '',
        username: '',
        no_telepon: '',
        no_sim: '',
        foto_sim: '',
        foto_sim_file: '',
        password: '',
        confirmation_password: '',
        alamat: '',
      },
      errorMessage:'',
      successMessage:'',
      isPasswordVisible: false,
      loading: false,
      allowed_mime_type: ['image/jpg', 'image/jpeg', 'image/png']
    }
  },
  methods: {
    handleFileUpload(e){
      const file = e.target.files[0];
      console.log("🚀 ~ handleFileUpload ~ file:", file)
      if(file.size > 1048576){
        this.form.foto_sim = ''
        this.erroMessage = 'Foto SIM Max 1 MB'
      }
      
      if(!this.allowed_mime_type.includes(file.type)){
        this.form.foto_sim = ''
        this.errorMessage = 'Foto SIM Harus JPEG, JPG, atau PNG'
      }

      this.form.foto_sim_file = file
      console.log("🚀 ~ handleFileUpload ~ this.foto_sim_file:", this.form.foto_sim_file)
    },
    async doSave(event){
      this.loading = true

      this.successMessage = ''
      this.errorMessage = ''

      if(this.form.password != this.form.confirmation_password){
        this.errorMessage = 'Password dan konfirmasi password tidak cocok'
      }

      let form = new FormData()
      form.append("nama", this.form.nama)
      form.append("username", this.form.username)
      form.append("no_telepon", this.form.no_telepon)
      form.append("no_sim", this.form.no_sim)
      form.append("foto_sim_file", this.form.foto_sim_file)
      form.append("password", this.form.password)
      form.append("alamat", this.form.alamat)

      event.preventDefault();
      event.stopPropagation();

      let uri = `/api/v1/auth/register`;
      let responseBody = await api.uploadPostApi(uri, form);
      if( responseBody.status != 200 ){
        let msg = ''
        if(Array.isArray(responseBody.message)){
          msg = responseBody.message.toString()
        }else{
          msg = responseBody.message
        }
        this.errorMessage = msg
      }else{
        this.successMessage = responseBody.message
        this.form = {
            nama: '',
            username: '',
            no_telepon: '',
            no_sim: '',
            foto_sim: '',
            foto_sim_file: '',
            password: '',
            confirmation_password: '',
            alamat: '',
          }
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
