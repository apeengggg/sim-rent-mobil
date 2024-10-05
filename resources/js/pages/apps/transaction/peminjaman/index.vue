<script setup>
import avatar1 from '@images/avatars/avatar-1.png'
import avatar2 from '@images/avatars/avatar-2.png'
import avatar3 from '@images/avatars/avatar-3.png'
import avatar4 from '@images/avatars/avatar-4.png'
import eCommerce2 from '@images/eCommerce/2.png'
import pages1 from '@images/pages/1.png'
import pages2 from '@images/pages/2.png'
import pages3 from '@images/pages/3.png'
import pages5 from '@images/pages/5.jpg'
import pages6 from '@images/pages/6.jpg'
import { VCol, VDivider, VRow } from 'vuetify/components'

const avatars = [
  avatar1,
  avatar2,
  avatar3,
  avatar4,
]

const isCardDetailsVisible = ref(false)
</script>

<template>
  <VRow>
    <VCol cols="12">
      <VCard title="Peminjaman Mobil">
        <VCardText class="mt-2">
            <VRow>
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
                  :readonly="!tanggal_peminjaman.includes('to')"
                  clear-icon="tabler-x"
                  v-if="tanggal_peminjaman.includes('to') && tanggal_peminjaman != '' && tanggal_peminjaman != null"
                />
              </VCol>
              <VCol
                cols="12"
                sm="4"
              >
              <VTextField
                  v-if="tanggal_peminjaman.includes('to') && tanggal_peminjaman != '' && tanggal_peminjaman != null"
                  v-model="param_query.model"
                  label="Model Mobil"
                  clearable
                  :readonly="!tanggal_peminjaman.includes('to')"
                  placeholder="Name"
                  density="compact"
                  v-on:keyup.enter="doSearch(1)"
                />
              </VCol>
              <VCol
                cols="12"
                :sm="4"
              >
              <AppDateTimePicker
                v-model="tanggal_peminjaman"
                label="PIlih Tanggal"
                :config="{ mode: 'range', disable: [{ from: '1970-01-01', to: yesterday }]}"
                clearable
                @change="changeDate()"
              />
              </VCol>
            </VRow>
          </VCardText>
        <VDivider />
        <VCardText v-if="tanggal_peminjaman.includes('to') && tanggal_peminjaman != '' && tanggal_peminjaman != null">
          <VRow v-if="!loading">
            <VCol
              sm="6"
              cols="12"
              v-for="(mobil, index) in data"
            >
              <VCard>
                <div class="d-flex justify-space-between flex-wrap flex-md-nowrap flex-column flex-md-row">
                  <div class="ma-auto pa-5">
                    <VImg
                      width="137"
                      :src="mobil.foto"
                    />
                  </div>
        
                  <VDivider :vertical="$vuetify.display.mdAndUp" />
        
                  <div>
                    <VCardItem>
                      <VCardTitle>{{mobil.merek_mobil}}</VCardTitle>
                      <div>
                        <span>{{mobil.model}}</span>
                      </div>
                      <div>
                        <span>{{mobil.warna}}</span>
                      </div>
                      <div>
                        <span>{{mobil.no_plat}}</span>
                      </div>
                    </VCardItem>
        
                    <VCardText>
                      {{mobil.description}}
                    </VCardText>
        
                    <VCardText class="text-subtitle-1">
                      <span>Tarif Per Hari :</span> <span class="font-weight-bold">{{ mobil.tarif }}</span>
                    </VCardText>
        
                    <VCardActions class="justify-space-between" v-if="mobil.is_avail == 1">
                      <VBtn @click="doPeminjamanForm(mobil.mobil_id, mobil)">
                        <VIcon icon="tabler-shopping-cart-plus" />
                        <span class="ms-2">Mulai Sewa!</span>
                      </VBtn>
                    </VCardActions>
                    <VCardText v-else>
                      <span color="error">Tidak Tersedia</span>
                    </VCardText>
                  </div>
                </div>
              </VCard>
            </VCol>
          </VRow>
          <VRow v-else class="justify-center">
            <VProgressCircular
              :size="50"
              color="primary"
              indeterminate
            />
          </VRow>
        </VCardText>
        <VCardText v-else>
          <VRow class="justify-center text-center">
            <VCol cols="12">
              <span>Pilih tanggal terlebih dahulu</span>
            </VCol>
          </VRow>
        </VCardText>
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
</template>

<script>
  import api from "@/apis/CommonAPI"
  import utils from "@/utils/CommonUtils"
  import Swal from 'sweetalert2'
  import AppDateTimePicker from '@core/components/AppDateTimePicker.vue'

  export default {
    components: {
      AppDateTimePicker
    },
    created(){
      const yesterday = new Date()
      yesterday.setDate(yesterday.getDate() - 1)
      this.yesterday = yesterday.toISOString().split('T')[0]
      console.log("🚀 ~ created ~ this.yesterday:", this.yesterday)
    },
    mounted(){
      this.doGetMerekMobil()
    },
    data(){
      return {
        loading: true,
        yesterday: '',
        tanggal_peminjaman: '',
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
          tanggal_mulai: '',
          tanggal_selesai: ''
        },
        body: {
          merek_mobil: '',
          merek_mobil_id: '',
          status: ''
        },
        selectedStatus: 1,
        data: [],
        errorMessage: '',
        successMessage: '',
        page: {
          totalRecords: 0,
          totalPages: 0,
          pageNo: 1,
          pageSize: 10
        },
        orderBy: 'is_return',
        dir: 'asc',
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
      doPeminjamanForm(mobil_id, mobil){
        const tanggal_peminjaman = this.tanggal_peminjaman.split('to')
        const tanggal_mulai = tanggal_peminjaman[0].trim()
        const tanggal_selesai = tanggal_peminjaman[1].trim()
        let url = `/apps/transaction/peminjaman/form/${mobil_id}?tanggal_mulai=${tanggal_mulai}&tanggal_selesai=${tanggal_selesai}`
        localStorage.setItem('mobil_pinjaman', JSON.stringify(mobil))
        this.$router.push(url)
      },
      changeDate(){
        if(this.tanggal_peminjaman.includes('to')){
          const tanggal = this.tanggal_peminjaman.split('to')
          console.log("🚀 ~ changeDate ~ tanggal:", tanggal)
          this.param_query.tanggal_mulai = tanggal[0].trim()
          this.param_query.tanggal_selesai = tanggal[1].trim()
          this.doSearch(1)
        }
      },
      async doGetMerekMobil(){
        let uri = `/api/v1/mobils/combo`;
        let responseBody = await api.jsonApi(uri,'GET');
        if( responseBody.status != 200 ){
          let msg = Array.isArray(responseBody.message) ? responseBody.message.toString() : responseBody.message;
          this.errorMessage = msg
        }else{
          this.merek_mobil = responseBody.data.merek_mobil
        }
      },
      async doSearch(page){
        this.loading = true
        let param = `orderBy=${this.orderBy}&dir=${this.dir}&perPage=${this.page.pageSize}&page=${page}&tanggal_mulai=${this.param_query.tanggal_mulai}&tanggal_selesai=${this.param_query.tanggal_selesai}`

        if(this.param_query.merek_mobil_id != "" && this.param_query.merek_mobil_id != null){
          param += `&merek_mobil_id=${this.param_query.merek_mobil_id}`
        }

        if(this.param_query.model != "" && this.param_query.model != null){
          param += `&model=${this.param_query.model}`
        }

        let uri = `/api/v1/transaksi/peminjaman?${param}`;
        let responseBody = await api.jsonApi(uri,'GET');
        console.log("🚀 ~ doSearch ~ responseBody:", responseBody)
        if( responseBody.status != 200 ){
          let msg = Array.isArray(responseBody.message) ? responseBody.message.toString() : responseBody.message;
          this.errorMessage = msg
        }else{
          this.data = responseBody.data.data;

          this.data.map(obj => {
            obj.tarif_asli = obj.tarif
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

          this.loading = false
        }
      },
    },
  }
</script>

<style lang="scss" scoped>
.avatar-center {
  position: absolute;
  border: 3px solid rgb(var(--v-theme-surface));
  inset-block-start: -2rem;
  inset-inline-start: 1rem;
}

// membership pricing
.member-pricing-bg {
  position: relative;
  background-color: rgba(var(--v-theme-on-surface), var(--v-hover-opacity));
}

.membership-pricing {
  sup {
    inset-block-start: 9px;
  }
}
</style>

<route lang="yaml">
  meta:
    requiresLogin: true
    isAdmin: false
</route>
