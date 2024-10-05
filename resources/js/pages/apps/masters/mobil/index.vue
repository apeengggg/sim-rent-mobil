<script setup>
import {
    alphaDashValidator,
    alphaValidator,
    betweenValidator,
    confirmedValidator,
    emailValidator,
    integerValidator,
    lengthValidator,
    passwordValidator,
    regexValidator,
    requiredValidator,
    urlValidator,
  } from '@validators'
</script>
<template>
  <section>
    <VRow>
      <VCol cols="12">
        <VCard title="Data Mobil">
          <!-- 👉 Filters -->
          <VCardText>
            <VRow>
                <VCol cols="12" v-if="successMessage != ''">
                  <VAlert v-if="successMessage != '' && successMessage != null" color="success" variant="tonal" @click="() => this.successMessage = ''">
                    {{successMessage}}
                  </VAlert>
                  <VAlert v-if="errorMessage != '' && errorMessage != null" color="error" variant="tonal" @click="() => this.errorMessage = ''">
                    {{errorMessage}}
                  </VAlert>
                </VCol>
            </VRow>
            <VRow>
              <VCol
                cols="12"
                sm="4"
              >
                <VSelect
                  v-model="param_query.merek_mobil_id"
                  label="Merek Mobil"
                  :items="merek_mobil"
                  clearable
                  clear-icon="tabler-x"
                />
              </VCol>
              <VCol
                cols="12"
                sm="4"
              >
              <VTextField
                  v-model="param_query.model"
                  label="Model Mobil"
                  clearable
                  placeholder="Name"
                  density="compact"
                  v-on:keyup.enter="doSearch(1)"
                />
              </VCol>
              <VCol
                cols="12"
                sm="4"
              >
                <VSelect
                  v-model="param_query.is_rent"
                  label="Filter Tersedia"
                  :items="filterRent"
                  clearable
                  clear-icon="tabler-x"
                />
              </VCol>
            </VRow>
          </VCardText>

          <VDivider />

          <VCardText class="">
            <VRow>
              <VCol cols="6">
                <div
                  class="me-3"
                  style="width: 80px;"
                >
                  <VSelect
                    v-model="this.page.pageSize"
                    density="compact"
                    variant="outlined"
                    :items="[10, 20, 30, 50]"
                  />
                </div>
              </VCol>
              <VCol cols="6">
                <VRow class="justify-end">
                  <VCol cols="6">
                    <VBtn
                      prepend-icon="tabler-plus"
                      @click="doAdd()"
                    >
                      Tambah Mobil
                    </VBtn>
                  </VCol>
                </VRow>
              </VCol>
            </VRow>
           
          </VCardText>

          <VTable class="text-no-wrap">
            <thead>
              <tr>
                <th scope="col">
                  NO
                </th>
                <th scope="col">
                  MOBIL
                </th>
                <th scope="col">
                  NO PLAT
                </th>
                <th scope="col">
                  WARNA
                </th>
                <th scope="col">
                  FASILITAS
                </th>
                <th scope="col">
                  TARIF
                </th>
                <th scope="col">
                  TERSEDIA
                </th>
                <th scope="col">
                  ACTIONS
                </th>
              </tr>
            </thead>
            <tbody>
              <tr
                v-for="(mobil, index) in this.data"
                :key="mobil.mobil_id"
              >
                <td>
                  <div class="d-flex align-center">
                    <span class="text-capitalize text-base">{{ ( (page.pageNo-1) * page.pageSize) + index + 1 }}</span>
                  </div>
                </td>
                <td>
                  <div class="d-flex align-center">
                    <VAvatar
                      variant="tonal"
                      class="me-3"
                      size="38"
                    >
                      <VImg
                        v-if="mobil.foto"
                        :src="mobil.foto"
                      />
                    </VAvatar>

                    <div class="d-flex flex-column">
                      <h6 class="text-base">
                        {{ mobil.merek_mobil }}
                      </h6>
                      <span class="text-sm text-disabled">{{ mobil.model }}</span>
                    </div>
                  </div>
                </td>
                <td>
                  <span class="text-capitalize text-base">{{ mobil.no_plat }}</span>
                </td>
                <td>
                  <span class="text-capitalize text-base">{{ mobil.warna }}</span>
                </td>
                <td style="max-width: 250px; white-space: normal; overflow: hidden; text-overflow: ellipsis;">
                  <div v-for="fas in mobil.description">
                    <v-badge class="me-2 mb-2 mt-2" :content="fas">
                    </v-badge>
                  </div>
                </td>
                <td>
                  <span class="text-capitalize text-base">{{ mobil.tarif }}</span>
                </td>
                <td>
                  <VChip
                    label
                    :color="resolveUserStatusVariant(mobil.is_rent)"
                    size="small"
                    class="text-capitalize"
                  >
                    {{ mobil.is_rent == 1 ? 'Tidak Tersedia' : 'Tersedia'}}
                  </VChip>
                </td>

                <td
                  class="text-center"
                  style="width: 5rem;"
                >
                    <VBtn
                      icon
                      size="x-small"
                      color="default"
                      variant="text"
                      @click="doGetById(mobil.mobil_id)"
                    >
                      <VIcon
                        size="22"
                        icon="tabler-edit"
                      />
                    </VBtn>
                    <VBtn
                      icon
                      size="x-small"
                      color="default"
                      variant="text"
                      @click="doDelete(mobil.mobil_id)"
                    >
                      <VIcon
                        size="22"
                        icon="tabler-trash"
                      />
                    </VBtn>
                </td>
              </tr>
            </tbody>

            <tfoot v-show="!this.data.length">
              <tr>
                <td
                  colspan="11"
                  class="text-center"
                >
                  No data available
                </td>
              </tr>
            </tfoot>
          </VTable>

          <VDivider />

          <VCardText class="d-flex align-center flex-wrap justify-space-between gap-4 py-3 px-5">
            <span class="text-sm text-disabled">
              {{ paginationData }}
            </span>

            <VPagination
              v-model="this.page.pageNo"
              size="small"
              :total-visible="11"
              :length="this.page.totalPages"
            />
          </VCardText>
        </VCard>
      </VCol>
    </VRow>
  </section>
</template>

<script>
  import api from "@/apis/CommonAPI"
  import utils from "@/utils/CommonUtils"
  import AddNewUser from '@/views/apps/masters/user/AddNewUser.vue'
  import Swal from 'sweetalert2'

  export default {
    components: {
      AddNewUser
    },
    mounted(){
      this.doSearch(1)
      this.doGetMerekMobil()
    },
    data(){
      return {
        merek_mobil: [],
        filterRent:[
          {
            title: 'Tersedia',
            value: 2,
          },
          {
            title: 'Tidak Tersedia',
            value: 1,
          },
        ],
        switch: true,
        formValid: false,
        isShowPassword: false,
        param_query: {
          merek_mobil_id: '',
          model: '',
          is_rent: '',
        },
        body: {
          merek_mobil: '',
          merek_mobil_id: '',
          status: ''
        },
        selectedStatus: 1,
        loading: false,
        data: [],
        errorMessage: '',
        successMessage: '',
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
    watch: {
      'page.pageNo'() {
          if (this.page.pageNo > this.page.totalPages){
              this.page.pageNo = this.page.totalPages
          }
          this.doSearch(this.page.pageNo)
        },
      'page.pageSize'(){
        this.doSearch(this.page.pageNo)
      },
      'param_query.merek_mobil_id'(){
        this.doSearch(1)
      },
      'param_query.is_rent'(){
        this.doSearch(1)
      }
    },
    computed: {
      paginationData(){
        const firstIndex = this.data.length ? (this.page.pageNo - 1) * this.page.pageSize + 1 : 0
        const lastIndex = this.data.length + (this.page.pageNo - 1) * this.page.pageSize
  
        return `Showing ${ firstIndex } to ${ lastIndex } of ${ this.data.length } entries`
      }
    },
    methods: {
      async doGetMerekMobil(){
        this.loading = true

        this.errorMessage = ''
        this.successMessage = ''

        let uri = `/api/v1/mobils/combo`;
        let responseBody = await api.jsonApi(uri,'GET');
        if( responseBody.status != 200 ){
          let msg = Array.isArray(responseBody.message) ? responseBody.message.toString() : responseBody.message;
          this.errorMessage = msg
        }else{
          this.merek_mobil = responseBody.data.merek_mobil
        }
        this.loading = false
      },
      doGetById(mobil_id){
        this.$router.push(`/apps/masters/mobil/form/${mobil_id}`)
      },
      doAdd(){
        this.$router.push('/apps/masters/mobil/form')
      },
      cancelEdit(){
        this.isEdit = false; 
        this.body.merek_mobil = ''; 
        this.body.merek_mobil_id = ''
        this.body.status = ''
      },
      async doSearch(page){
        this.loading = true

        this.errorMessage = ''
        this.successMessage = ''

        let param = `orderBy=${this.orderBy}&dir=${this.dir}&perPage=${this.page.pageSize}&page=${page}&status=${this.selectedStatus == null ? 1 : this.selectedStatus}`

        if(this.param_query.merek_mobil_id != "" && this.param_query.merek_mobil_id != null){
          param += `&merek_mobil_id=${this.param_query.merek_mobil_id}`
        }

        if(this.param_query.model != "" && this.param_query.model != null){
          param += `&model=${this.param_query.model}`
        }

        if(this.param_query.is_rent != "" && this.param_query.is_rent != null){
          param += `&is_rent=${this.param_query.is_rent}`
        }

        let uri = `/api/v1/mobils?${param}`;
        let responseBody = await api.jsonApi(uri,'GET');
        console.log("🚀 ~ doSearch ~ responseBody:", responseBody)
        if( responseBody.status != 200 ){
          let msg = Array.isArray(responseBody.message) ? responseBody.message.toString() : responseBody.message;
          this.errorMessage = msg
        }else{
          this.data = responseBody.data.data;

          this.data.map(obj => {
            obj.description = obj.description.split(",")
            obj.tarif = new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0, // You can adjust this if you want decimal places
                maximumFractionDigits: 0
            }).format(obj.tarif);
          })

          console.log("🚀 ~ doSearch ~ this.data:", this.data)

          this.page.totalRecords= responseBody.data.total
          this.page.totalPages= responseBody.data.last_page
          this.page.pageNo= responseBody.data.current_page
          this.page.pageSize= responseBody.data.per_page
        }
      },
      async doDelete(mobil_id){
        this.errorMessage = ''
        this.successMessage = ''

        Swal.fire({
          title: "Are you sure?",
          text: "You won't be able to revert this!",
          icon: "warning",
          showCancelButton: true,
          confirmButtonColor: "#7367F0",
          cancelButtonColor: "#EA5455",
          confirmButtonText: "Yes, delete it!",
          customClass: {
            confirmButton: 'confirm-button-text-white',
            cancelButton: 'confirm-button-text-white'
          }
      }).then(async (result) => {
          if (result.isConfirmed) {
            this.loading = true
            let uri = `/api/v1/mobils`;
            let responseBody = await api.jsonApi(uri,'DELETE',JSON.stringify({mobil_id: mobil_id}));
            if( responseBody.status != 200 ){
              let msg = Array.isArray(responseBody.message) ? responseBody.message.toString() : responseBody.message;
              this.errorMessage = msg
            }else{
              this.successMessage = responseBody.message
            }
            this.loading = false
            this.doSearch(1)
          }
        })
          
      },
      resolveUserStatusVariant(stat){
        if(stat == 1){
          return 'error'
        }
        
        return 'success'
      }
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
</route>
