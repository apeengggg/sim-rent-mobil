<script setup>
import {
    requiredValidator,
  } from '@validators'
</script>
<template>
  <section>
    <VRow>
      <VCol cols="12">
        <VCard title="Tambah Mobil">
          <VCardText>
            <VRow>
              <VCol cols="12">
                <VAlert v-if="successMessage != '' && successMessage != null" color="success" variant="tonal" @click="() => this.successMessage = ''">
                  {{successMessage}}
                </VAlert>
                <VAlert v-if="errorMessage != '' && errorMessage != null" color="error" variant="tonal" @click="() => this.errorMessage = ''">
                  {{errorMessage}}
                </VAlert>
                <VAlert v-if="errorMessageList != '' && errorMessageList != null" color="error" variant="tonal" @click="() => this.errorMessageList = ''">
                  {{errorMessageList}}
                </VAlert>
              </VCol>
            </VRow>
            <VRow class="justify-center">
              <VCol cols="12">
                <VImg
                  v-if="body.foto_preview != ''"
                  :height="200"
                  :src="body.foto_preview"
                  class="mx-auto mb-5"
                />
              </VCol>
            </VRow>
            <VRow>
              <VCol cols="12">
                <VForm
                  :key="formKey"
                  ref="myForm"
                  v-model="formValid"
                  @submit.prevent="doAdd"
                >
                  <VRow>
                    <VCol cols="6">
                      <VSelect
                        v-model="body.merek_mobil_id"
                        label="Select Merek Mobil"
                        :items="merek_mobil"
                        :rules="[requiredValidator]"
                        clearable
                        clear-icon="tabler-x"
                      />
                    </VCol>
                    <VCol cols="6">
                      <VTextField
                        v-model="body.model"
                        :rules="[requiredValidator]"
                        label="Model Mobil"
                        required
                        clearable
                      />
                    </VCol>
                    <VCol cols="6">
                      <VTextField
                        v-model="body.no_plat"
                        :rules="[requiredValidator]"
                        label="No Plat"
                        required
                        clearable
                      />
                    </VCol>
                    <VCol cols="6">
                      <VTextField
                        v-model="body.warna"
                        :rules="[requiredValidator]"
                        label="Warna"
                        required
                        clearable
                      />
                    </VCol>
                    <VCol cols="6">
                      <VTextField
                        v-model="body.tarif"
                        :rules="[requiredValidator]"
                        label="Tarif"
                        prefix="Rp"
                        type="number"
                      />
                    </VCol>
                    <VCol cols="6">
                      <VSelect
                        v-model="body.description"
                        :items="fasilitas"
                        label="Fasilitas"
                        :rules="[requiredValidator]"
                        chips
                        multiple
                        clearable
                      />
                    </VCol>
                    <VCol cols="6">
                      <VFileInput
                        accept="image/*"
                        label="Foto"
                        v-model="body.foto"
                        v-on:change="handleFileChange($event)"
                        :rules=[requiredValidator]
                        clearable
                      />
                    </VCol>
                  </VRow>
                  <VRow class="text-right">  
                    <VCol
                      cols="12"
                    >
                      <VBtn
                        color="success"
                        type="submit"
                        class="me-2"
                      >
                        Submit
                      </VBtn>
                    </VCol>
                  </VRow>
                </VForm>
              </VCol>
            </VRow>
          </VCardText>
        </VCard>
      </VCol>
    </VRow>
  </section>
</template>

<script>
  import api from "@/apis/CommonAPI"
  import utils from "@/utils/CommonUtils"
  import Swal from 'sweetalert2'

  export default {
    components: {
    },
    mounted(){
      this.getFasilitasAndMerekMobil()
    },
    data(){
      return {
        formKey: 1,
        fasilitas: [],
        merek_mobil: [],
        tags: [],
        formValid: false,
        isShowPassword: false,
        param_query: {
          merek_mobil: ''
        },
        body: {
          mobil_id: "",
          merek_mobil_id: "",
          model: "",
          no_plat: "",
          warna: "",
          description: [],
          tarif: "",
          foto: "",
          foto_file: "",
          foto_preview: "",
        },
        selectedStatus: 1,
        loading: false,
        data: [],
        errorMessage: '',
        successMessage: '',
        errorMessageList: '',
        page: {
          totalRecords: 0,
          totalPages: 0,
          pageNo: 1,
          pageSize: 10
        },
        orderBy: 'merek_mobil_id',
        dir: 'asc',
        isEdit: false,
        status: [
          {
            title: 'Active',
            value: 1,
          },
          {
            title: 'Inactive',
            value: 0,
          },
        ]
      }
    },
    methods: {
      handleFileChange(event){
        this.errorMessage = ''
        const file = event.target.files[0]
        console.log("🚀 ~ handleFileChange ~ file:", file)

        if(file.size > 1048576){
          this.errorMessage = 'Foto Max 1 MB'
        }

        if(!file.type.includes("image")){
          this.errorMessage = 'File Not Allowed'
        }

        if (file) {
          const reader = new FileReader();
          reader.onload = (e) => {
            this.body.foto_preview = e.target.result;
          };
          reader.readAsDataURL(file);
        }
        this.body.foto = file
      },
      async getFasilitasAndMerekMobil(){
        this.loading = true
        
        this.errorMessage = ''
        this.successMessage = ''
        this.errorMessageList = ''

        let uri = `/api/v1/mobils/combo`;
        let responseBody = await api.jsonApi(uri,'GET');
        console.log("🚀 ~ doSaveAllRole ~ responseBody:", responseBody)
        if( responseBody.status != 200 ){
          this.errorMessage = responseBody.message;
        }else{
          const fasilitas = responseBody.data.fasilitas

          fasilitas.forEach(obj => {
            this.fasilitas.push(obj.fasilitas)
          })

          this.merek_mobil = responseBody.data.merek_mobil
        }
        this.loading = false
      },
      cancelEdit(){
        this.isEdit = false; 
        this.body.merek_mobil = ''; 
        this.body.merek_mobil_id = ''
        this.body.status = ''
      },
      // async doGetById(merek_mobil_id){
      //   this.isEdit = true
      //   this.loading = true

      //   let uri = `/api/v1/merek-mobils/${merek_mobil_id}`;
      //   let responseBody = await api.jsonApi(uri,'GET');
      //   if( responseBody.status != 200 ){
      //     this.errorMessageList = responseBody.message;
      //   }else{
      //     this.body = {...responseBody.data};
      //     this.body.switchToggle = this.body.status == 1 ? true : false
      //     console.log("🚀 ~ doGetById ~ this.body:", this.body)
      //   }
      // },
      async doAdd(){
        if(!this.formValid){
          return
        }
        this.errorMessage = ''
        this.errorMessageList = ''
        this.successMessage = ''

        Swal.fire({
          title:"Area you sure want to save this data?",
          icon: "warning",
          showCancelButton: true,
          confirmButtonColor: "#7367F0",
          cancelButtonColor: "#EA5455",
          confirmButtonText: "Yes",
          customClass: {
            confirmButton: 'confirm-button-text-white',
            cancelButton: 'confirm-button-text-white'
          }
      }).then(async (result) => {
          if (result.isConfirmed) {
            try{
              this.loading = true

              let formData = new FormData()
              formData.append("merek_mobil_id", this.body.merek_mobil_id)
              formData.append("model", this.body.model)
              formData.append("no_plat", this.body.no_plat)
              formData.append("warna", this.body.warna)
              formData.append("description", this.body.description.toString())
              formData.append("tarif", this.body.tarif)
              formData.append("foto", this.body.foto)

              let uri = `/api/v1/mobils`;
              let responseBody = await api.uploadApi(uri, 'POST', formData);
              console.log("🚀 ~ doAdd ~ responseBody:", responseBody)
              if( responseBody.status != 200 ){
                let msg = Array.isArray(responseBody.message) ? responseBody.message.toString() : responseBody.message;
                this.errorMessage = msg
                window.scrollTo({
                  top: 0,
                  behavior: 'smooth' // You can also use 'auto' for instant scrolling
                });
              }else{
                this.successMessage = responseBody.message
                this.body = {
                  mobil_id: "",
                  merek_mobil_id: "",
                  model: "",
                  no_plat: "",
                  warna: "",
                  description: [],
                  tarif: "",
                  foto: "",
                  foto_file: "",
                  foto_preview: "",
                }
                this.formKey +=1
                window.scrollTo({
                  top: 0,
                  behavior: 'smooth' // You can also use 'auto' for instant scrolling
                });
                
              }
            }catch(error){
              this.errorMessage = error.message
            }
          }
        })
      },
      async doSave(){
        this.loading = true

        this.errorMessageList = ''
        this.errorMessage = ''
        this.successMessage = ''

        let formData = new FormData()
        formData.append("merek_mobil_id", this.body.merek_mobil_id)
        formData.append("model", this.body.model)
        formData.append("no_plat", this.body.no_plat)
        formData.append("warna", this.body.warna)
        formData.append("description", this.body.description)
        formData.append("tarif", this.body.tarif)
        formData.append("foto", this.body.foto)

        let uri = `/api/v1/mobils`;
        let responseBody = await api.uploadPostApi(uri, formData);
        if( responseBody.status != 200 ){
          this.errorMessageList = responseBody.message;
        }else{
          this.successMessage = responseBody.message
        }
      },
    },
  }
</script>

<style lang="scss">
.app-user-search-filter {
  inline-size: 31.6rem;
}

.text-capitalize {
  text-transform: capitalize;
}

.user-list-name:not(:hover) {
  color: rgba(var(--v-theme-on-background), var(--v-high-emphasis-opacity));
}

.confirm-button-text-white {
    color: white !important;
}
</style>

<route lang="yaml">
  meta:
    requiresLogin: true
    isAdmin: true
</route>
