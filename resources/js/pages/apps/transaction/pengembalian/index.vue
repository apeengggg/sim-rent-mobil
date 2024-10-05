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
        <VCard title="Data Transaksi">
          <!-- 👉 Filters -->
          <VCardText>
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
              <VTextField
                  v-model="param_query.plat_no"
                  label="Plat Nomor"
                  clearable
                  placeholder="Plat Nomor"
                  density="compact"
                  v-on:keyup.enter="doSearch(1)"
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
            </VRow>
           
          </VCardText>

          <VTable class="text-no-wrap">
            <thead>
              <tr>
                <th scope="col">
                  NO
                </th>
                <th scope="col">
                  NAMA PEMINJAM
                </th>
                <th scope="col">
                  MOBIL
                </th>
                <th scope="col">
                  NO PLAT
                </th>
                <th scope="col">
                  WAKTU PEMINJAMAN
                </th>
                <th scope="col">
                  DURASI
                </th>
                <th scope="col">
                  WAKTU KEMBALI
                </th>
                <th scope="col">
                  TARIF TOTAL
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
                v-for="(tr, index) in this.data"
                :key="tr.transaksi_id"
              >
                <td>
                  <div class="d-flex align-center">
                    <span class="text-capitalize text-base">{{ ( (page.pageNo-1) * page.pageSize) + index + 1 }}</span>
                  </div>
                </td>
                <td>
                  <span class="text-capitalize text-base text-truncate">{{ tr.nama }}</span>
                </td>
                <td>
                  <div class="d-flex align-center">
                    <div class="d-flex flex-column">
                      <h6 class="text-base">
                        {{ tr.merek_mobil }}
                      </h6>
                      <span class="text-sm text-disabled">{{ tr.model }}</span>
                    </div>
                  </div>
                </td>
                <td>
                  <span class="text-capitalize text-base">{{ tr.no_plat }}</span>
                </td>
                <td>
                  <span class="text-capitalize text-base">{{ tr.tanggal_mulai + ' -' }}</span>
                  <br>
                  <span class="text-capitalize text-base">{{ tr.tanggal_selesai }}</span>
                </td>
                <td>
                  <span class="text-capitalize text-base">{{ tr.durasi }}</span>
                </td>
                <td>
                  <span class="text-capitalize text-base">{{ tr.tanggal_kembali || '-' }}</span>
                </td>
                <td>
                  <span class="text-capitalize text-base">{{ tr.total_tarif }}</span>
                </td>
                <td>
                  <VChip
                    label
                    :color="resolveStatusPeminjaman(tr.is_return)"
                    size="small"
                    class="text-capitalize"
                  >
                    {{ tr.is_return == 1 ? 'Dikembalikan' : 'Belum Dikembalikan'}}
                  </VChip>
                </td>
                <td
                  class="text-center"
                  style="width: 5rem;"
                >
                    <VBtn
                      icon
                      size="x-small"
                      :disabled="tr.is_return == 1"
                      color="default"
                      variant="text"
                      @click="doUpdate(tr)"
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

    <VDialog
      v-model="isDialogVisible"
      class="v-dialog-sm"
      persistent
    >
      <!-- Dialog Activator -->
      <!-- <template #activator="{ props }">
        <VBtn v-bind="props">
          Open Dialog
        </VBtn>
      </template> -->

      <!-- Dialog close btn -->
      <DialogCloseBtn @click="cancelUpdate()" />

      <!-- Dialog Content -->
      <VCard title="Form Pengembalian">
        <VDivider class="mt-2 mb-2"/>
        <VCardText>
          <VRow>
            <VCol cols="12" v-if="successMessageModal != ''">
            <VAlert color="success" variant="tonal" @click="() => this.successMessageModal = ''">
              {{successMessageModal}}
            </VAlert>
            </VCol>
            <VCol cols="12" v-if="errorMessageModal != ''">
              <VAlert color="error" variant="tonal" @click="() => this.errorMessageModal = ''">
                {{errorMessageModal}}
              </VAlert>
            </VCol>
          </VRow>
          <VRow>
            <VCol cols="12">
              <VTextField
                v-model="modal.no_sim"
                label="Nomor SIM"
                type="number"
                required
                placeholder="Nomor SIM"
                density="compact"
              />
            </VCol>
            <VCol cols="12">
              <VTextField
                v-model="modal.no_plat"
                label="Plat Nomor"
                readonly
                placeholder="Plat Nomor"
                density="compact"
              />
            </VCol>
            <VCol cols="12">
              <VTextField
                v-model="modal.tanggal_kembali"
                label="Tanggal Kembali"
                readonly
                placeholder="Tanggal Kembali"
                density="compact"
              />
            </VCol>
          </VRow>
          <VRow>
            <VCol cols="12">
              <table>
                <tr>
                  <td style="max-width">Tarif Per Hari</td>
                  <td> : </td>
                  <td>{{modal.tarif}}</td>
                </tr>
                <tr>
                  <td>Pengembalian</td>
                  <td> : </td>
                  <td>{{modal.tanggal_selesai}}</td>
                </tr>
                <tr>
                  <td>Durasi Pinjam</td>
                  <td> : </td>
                  <td>{{modal.durasi + ' Hari'}}</td>
                </tr>
              </table>
              <VDivider class="mt-2 mb-2"/>
              <table>
                <tr v-if="modal.denda != ''">
                  <td>Denda</td>
                  <td> : </td>
                  <td>{{modal.denda}}</td>
                </tr>
                <tr v-if="modal.telat_hari != ''">
                  <td>Telat Hari</td>
                  <td> : </td>
                  <td>{{modal.telat_hari + ' Hari'}}</td>
                </tr>
                <tr>
                  <td>Total Tarif</td>
                  <td> : </td>
                  <td>{{modal.total_tarif}}</td>
                </tr>
              </table>
            </VCol>
            
          </VRow>
        </VCardText>

        <VCardText class="d-flex justify-end gap-3 flex-wrap">
          <VBtn
            color="secondary"
            variant="tonal"
            @click="cancelUpdate()"
          >
            Cancel
          </VBtn>
          <VBtn @click="doSave()">
            <VProgressCircular
              v-if="loading"
              :size="20"
              indeterminate
            />
            <span v-else>Submit</span>
          </VBtn>
        </VCardText>
      </VCard>
    </VDialog>
  </section>
</template>

<script>
  import api from "@/apis/CommonAPI"
  import utils from "@/utils/CommonUtils"
  import Swal from 'sweetalert2'
  import moment from 'moment'

  export default {
    components: {
    },
    mounted(){
      this.doSearch(1)
    },
    data(){
      return {
        isDialogVisible: false,
        status_peminjaman:[
          {
            title: 'Belum Dikembalikan',
            value: 0,
          },
          {
            title: 'Sudah Dikembalikan',
            value: 1,
          },
        ],
        param_query: {
          transaksi_id: '',
          no_plat: '',
        },
        modal: {
          no_sim: '',
          no_plat: '',
          transaksi_id: '',
          tanggal_selesai: '',
          tanggal_mulai: '',
          tarif: '',
          total_tarif: '',
          total_tarif_number: '',
          tanggal_kembali: moment(new Date()).format('D MMMM YYYY'),
          tanggal_kembali_normal: moment(new Date()).format('DD-MM-YYYY'),
          durasi: '',
          telat_hari: ''
        },
        loading: false,
        data: [],
        errorMessage: '',
        successMessage: '',
        errorMessageModal: '',
        successMessageModal: '',
        page: {
          totalRecords: 0,
          totalPages: 0,
          pageNo: 1,
          pageSize: 10
        },
        orderBy: 'is_return',
        dir: 'desc'
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
    },
    computed: {
      paginationData(){
        const firstIndex = this.data.length ? (this.page.pageNo - 1) * this.page.pageSize + 1 : 0
        const lastIndex = this.data.length + (this.page.pageNo - 1) * this.page.pageSize
  
        return `Showing ${ firstIndex } to ${ lastIndex } of ${ this.data.length } entries`
      }
    },
    methods: {
      cancelUpdate(){
        this.isDialogVisible = false
        this.modal = {
          no_sim: '',
          no_plat: '',
          transaksi_id: '',
          tanggal_selesai: '',
          tanggal_mulai: '',
          tarif: '',
          total_tarif: '',
          total_tarif_number: '',
          tanggal_kembali: moment(new Date()).format('D MMMM YYYY'),
          tanggal_kembali_normal: moment(new Date()).format('DD-MM-YYYY'),
          durasi: '',
          telat_hari: ''
        }
      },
      async doSave(){
        this.errorMessage = ''
        this.successMessage = ''
        this.errorMessageModal = ''
        this.successMessageModal = ''

        if(this.modal.no_sim == ''){
          this.errorMessageModal = 'No Sim Is Required'
        }

        let data = {
          transaksi_id: this.modal.transaksi_id,
          no_sim: this.modal.no_sim,
          tanggal_kembali: this.modal.tanggal_kembali_normal,
          total_tarif: this.modal.total_tarif_number,
        }

        try{
          this.loading = true

          let uri = `/api/v1/transaksi/pengembalian`;
          let responseBody = await api.jsonApi(uri, 'POST', JSON.stringify(data));

          if( responseBody.status != 200 ){
            let msg = Array.isArray(responseBody.message) ? responseBody.message.toString() : responseBody.message;
            this.errorMessageModal = msg
            window.scrollTo({
              top: 0,
              behavior: 'smooth' // You can also use 'auto' for instant scrolling
            });
          }else{
            this.successMessageModal = responseBody.message

            setTimeout(() => {
              this.cancelUpdate()
              window.scrollTo({
                top: 0,
                behavior: 'smooth' // You can also use 'auto' for instant scrolling
              });
              this.doSearch(1)
            }, 2000);
          }
          this.loading = false
        }catch(error){
          this.loading = false
          this.errorMessageModal = error.message
        }
      },
      doUpdate(data){
        this.isDialogVisible = true
        this.modal.no_plat = data.no_plat
        this.modal.transaksi_id = data.transaksi_id
        this.modal.tarif = utils.formatRupiah(data.tarif)
        this.modal.tanggal_selesai = data.tanggal_selesai

        const today = moment(new Date()).format('DD-MM-YYYY')
        const tanggal_selesai = moment(data.tanggal_selesai).format('DD-MM-YYYY')
        const durasi_pinjam = moment(data.tanggal_selesai).diff(moment(data.tanggal_mulai), 'days')
        this.modal.durasi = durasi_pinjam
        
        const tarif_normal = parseInt(data.tarif) * durasi_pinjam

        const telat = moment(new Date()).isAfter(data.tanggal_selesai)
        
        this.modal.total_tarif = utils.formatRupiah(tarif_normal)
        this.modal.total_tarif_number = tarif_normal

        if(telat && today != tanggal_selesai){
          const telat_hari = moment(new Date()).diff(moment(data.tanggal_selesai), 'days')
          
          const denda = ((parseInt(data.tarif) * 10) / 100) * telat_hari
          

          this.modal.denda = utils.formatRupiah(denda)
          this.modal.telat_hari = telat_hari
          this.modal.total_tarif = utils.formatRupiah(tarif_normal + (telat_hari * parseInt(data.tarif)) + denda)
          this.modal.total_tarif_number = tarif_normal + (telat_hari * parseInt(data.tarif)) + denda
          
        }
      },
      doGetById(mobil_id){
        this.$router.push(`/apps/masters/mobil/form/${mobil_id}`)
      },
      async doSearch(page){
        this.loading = true
        let param = `orderBy=${this.orderBy}&dir=${this.dir}&perPage=${this.page.pageSize}&page=${page}`

        if(this.param_query.no_plat != "" && this.param_query.no_plat != null){
          param += `&no_plat=${this.param_query.no_plat}`
        }

        let uri = `/api/v1/transaksi?${param}`;
        let responseBody = await api.jsonApi(uri,'GET');
        
        if( responseBody.status != 200 ){
          let msg = Array.isArray(responseBody.message) ? responseBody.message.toString() : responseBody.message;
          this.errorMessage = msg
          this.loading = false
        }else{
          this.data = responseBody.data.data;

          this.data.map(obj => {
            const tanggal_mulai = moment(obj.tanggal_mulai)
            const tanggal_selesai = moment(obj.tanggal_selesai)

            obj.durasi = tanggal_selesai.diff(tanggal_mulai, 'days') + ' Hari'
            obj.tanggal_mulai = moment(tanggal_mulai).format('D MMMM YYYY')
            obj.tanggal_selesai = moment(tanggal_selesai).format('D MMMM YYYY')
            if(obj.tanggal_kembali != null){
              obj.tanggal_kembali = moment(obj.tanggal_kembali).format('D MMMM YYYY')
            }

            obj.total_tarif = new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0, // You can adjust this if you want decimal places
                maximumFractionDigits: 0
            }).format(obj.total_tarif);
          })

          

          this.page.totalRecords= responseBody.data.total
          this.page.totalPages= responseBody.data.last_page
          this.page.pageNo= responseBody.data.current_page
          this.page.pageSize= responseBody.data.per_page
          this.loading = false
        }
      },
      resolveStatusPeminjaman(stat){
        if(stat == 1){
          return 'success'
        }
        
        return 'error'
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
    isAdmin: true
</route>
