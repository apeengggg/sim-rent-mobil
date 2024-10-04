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
      <VCol cols="4">
        <VCard title="Tambah Merek Mobil">
          <VCardText>
            <VRow class="justify-end">
              <VCol cols="12" v-if="successMessage != ''">
                <VAlert color="success" variant="tonal" @click="() => this.successMessage = ''">
                  {{successMessage}}
                </VAlert>
              </VCol>
              <VCol cols="12" v-if="errorMessage != ''">
                <VAlert color="error" variant="tonal" @click="() => this.errorMessage = ''">
                  {{errorMessage}}
                </VAlert>
              </VCol>
              <VCol cols="12">
                
                <VForm
                  ref="myForm"
                  v-model="formValid"
                  @submit.prevent="doAdd"
                >
                  <VRow>
                    <VCol cols="12">
                      <VTextField
                        v-model="body.merek_mobil"
                        :rules="[requiredValidator]"
                        label="Merek Mobil"
                        required
                      />
                    </VCol>
                    <VCol cols="12" v-if="isEdit">
                      <VSwitch
                        v-model="body.switchToggle"
                        :label="body.status == 1 ? 'Active' : 'Inactive'"
                      />
                    </VCol>
                    <VCol
                      :cols="isEdit ? 6 : 12"
                      :class='isEdit ? "d-flex" : "d-flex justify-end"'
                    >
                      <VBtn
                        color="success"
                        type="submit"
                      >
                        Submit
                      </VBtn>
                    </VCol>
                    <VCol
                      v-if="isEdit"
                      cols="6"
                      class="d-flex"
                    >
                      <VBtn
                        color="error"
                        @click="cancelEdit()"
                        type="reset"
                      >
                        Cancel
                      </VBtn>
                    </VCol>
                  </VRow>
                </VForm>
              </VCol>
            </VRow>
          </VCardText>
        </VCard>
      </VCol>
      <VCol cols="8">
        <VCard title="Data Merek Mobil">
          <!-- 👉 Filters -->
          <VCardText>
            <VRow>
              <VCol
                cols="12"
                sm="4"
              >
              <VTextField
                  v-model="param_query.merek_mobil"
                  label="Merek Mobil"
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
                  v-model="selectedStatus"
                  label="Select Status"
                  :items="status"
                  clearable
                  clear-icon="tabler-x"
                />
              </VCol>
            </VRow>
          </VCardText>

          <VDivider />

          <VCardText class="d-flex flex-wrap py-4 gap-4">
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
          </VCardText>

          <!-- <VDivider /> -->

          <VTable class="text-no-wrap">
            <thead>
              <tr>
                <th scope="col">
                  NO
                </th>
                <th scope="col">
                  MEREK MOBIL
                </th>
                <th scope="col">
                  JUMLAH MOBIL
                </th>
                <th scope="col">
                  STATUS
                </th>
                <th scope="col">
                  ACTIONS
                </th>
              </tr>
            </thead>
            <tbody>
              <tr
                v-for="(merek, index) in this.data"
                :key="merek.merek_mobil_id"
                style="height: 3.75rem;"
              >
                <td>
                  <div class="d-flex align-center">
                    <span class="text-capitalize text-base">{{ ( (page.pageNo-1) * page.pageSize) + index + 1 }}</span>
                  </div>
                </td>
                <td>
                  <div class="d-flex align-center">
                    <span class="text-capitalize text-base">{{ merek.merek_mobil }}</span>
                  </div>
                </td>
                <td>
                  <span class="text-capitalize text-base">{{ merek.jumlah }}</span>
                </td>
                <td>
                  <VChip
                    label
                    :color="resolveUserStatusVariant(merek.status)"
                    size="small"
                    class="text-capitalize"
                  >
                    {{ merek.status == 1 ? 'Active' : 'Inactive'}}
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
                      @click="doGetById(merek.merek_mobil_id)"
                    >
                      <VIcon
                        size="22"
                        icon="tabler-edit"
                      />
                    </VBtn>
                </td>
              </tr>
            </tbody>

            <tfoot v-show="!this.data.length">
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
    },
    data(){
      return {
        switch: true,
        formValid: false,
        isShowPassword: false,
        param_query: {
          merek_mobil: ''
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
      'selectedStatus'(){
        this.doSearch(this.page.pageNo)
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
      cancelEdit(){
        this.isEdit = false; 
        this.body.merek_mobil = ''; 
        this.body.merek_mobil_id = ''
        this.body.status = ''
      },
      async doGetById(merek_mobil_id){
        this.isEdit = true
        this.loading = true

        let uri = `/api/v1/merek-mobils/${merek_mobil_id}`;
        let responseBody = await api.jsonApi(uri,'GET');
        if( responseBody.status != 200 ){
          this.errorMessageList = responseBody.message;
        }else{
          this.body = {...responseBody.data};
          this.body.switchToggle = this.body.status == 1 ? true : false
          console.log("🚀 ~ doGetById ~ this.body:", this.body)
        }
      },
      async doAdd(){
        this.errorMessage = ''
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
              let uri = `/api/v1/merek-mobils`;
              let method = this.isEdit ? 'PUT' : 'POST'
              this.body.status = this.body.switchToggle ? 1 : 0

              let responseBody = await api.jsonApi(uri, method, JSON.stringify(this.body));
              if( responseBody.status != 200 ){
                let msg = Array.isArray(responseBody.message) ? responseBody.message.toString() : responseBody.message;
                this.errorMessage = msg
              }else{
                this.successMessage = responseBody.message
                this.doSearch(1)
                this.isEdit = false
                this.$refs.myForm.reset();
              }
            }catch(error){
              Swal.fire('Error!', error.message, 'error')
            }
          }
        })
      },
      async doSearch(page){
        this.loading = true
        let param = `orderBy=${this.orderBy}&dir=${this.dir}&perPage=${this.page.pageSize}&page=${page}&status=${this.selectedStatus == null ? 1 : this.selectedStatus}`

        if(this.param_query.merek_mobil != "" && this.param_query.merek_mobil != "null"){
          param += `&merek_mobil=${this.param_query.merek_mobil}`
        }

        let uri = `/api/v1/merek-mobils?${param}`;
        let responseBody = await api.jsonApi(uri,'GET');
        // console.log("🚀 ~ doSearch ~ responseBody:", responseBody)
        if( responseBody.status != 200 ){
          this.errorMessageList = responseBody.message;
        }else{
          this.data = responseBody.data.data;

          this.page.totalRecords= responseBody.data.total
          this.page.totalPages= responseBody.data.last_page
          this.page.pageNo= responseBody.data.current_page
          this.page.pageSize= responseBody.data.per_page
        }
      },
      async doDelete(user_id){
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
          //   this.loading = true
          //   let uri = `/api/v1/users`;
          //   let responseBody = await api.jsonApi(uri,'DELETE',JSON.stringify({userId: user_id}));
            
          //   if( responseBody.status != 200 ){
          //     this.infoMessage = '';
          //     this.warningMessage = '';
          //     this.errorMessage = responseBody.message;
          //   }else{
          //     Swal.fire('Deleted!', responseBody.message, 'success')
          //   }
          //   this.loading = false
          //   this.doSearch(1)
          }
        })
          
      },
      resolveUserStatusVariant(stat){
        if(stat == 1){
          return 'success'
        }
        
        return 'danger'
      }
    },
  }
</script>Role

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
