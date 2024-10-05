<script setup>
import { VNodeRenderer } from '@layouts/components/VNodeRenderer'
import { themeConfig } from '@themeConfig'
import { VDivider } from 'vuetify/components';
</script>

<template>
  <VCard>
    <VRow class="justify-center">
      <VCol cols="12" v-if="successMessage != '' && successMessage != null">
        <VAlert color="success" variant="tonal" @click="() => this.successMessage = ''">
          {{successMessage}}
        </VAlert>
      </VCol>
      <VCol cols="12" v-if="errorMessage != '' && errorMessage != null">
        <VAlert color="error" variant="tonal" @click="() => this.errorMessage = ''">
          {{errorMessage}}
        </VAlert>
      </VCol>
    </VRow>
    <VCardText class="d-flex flex-wrap justify-space-between flex-column flex-sm-row">
      <!-- 👉 Left Content -->
      <div class="ma-sm-4">
        <div class="d-flex align-center mb-6">
          <!-- 👉 Logo -->
          <VNodeRenderer
            :nodes="themeConfig.app.logo"
            class="me-3"
          />

          <!-- 👉 Title -->
          <h6 class="font-weight-bold text-xl">
            {{ themeConfig.app.title }}
          </h6>
        </div>

        <!-- 👉 Address -->
        <p class="mb-0">
          Jl. Pinus V No 202, Bumi Arum Sari
        </p>
        <p class="mb-0">
          Cirebon Girang, Talun, Kabupaten Cirebon
        </p>
      </div>

      <!-- 👉 Right Content -->
      <div class="mt-4 ma-sm-4">
        <!-- 👉 Invoice Id -->
        <h6 class="d-flex align-center font-weight-medium justify-sm-end text-xl mb-3">
          <span class="me-3">Invoice</span>

          <span>
            <VTextField
              v-model="data.transaksi_id"
              disabled
              density="compact"
              style="width: 8.9rem;"
            />
            <VTooltip
              open-delay="500"
              location="top"
              activator="parent"
            >
              <span>{{ data.transaksi_id }}</span>
            </VTooltip>
          </span>
        </h6>

        <!-- 👉 Issue Date -->
        <p class="d-flex align-center justify-sm-end mb-3">
          <span class="me-3">Tanggal Peminjaman</span>
          <span>
            <AppDateTimePicker
              v-model="data.tanggal_mulai"
              density="compact"
              placeholder="DD-MM-YYYY"
              style="width: 8.9rem;"
              :config="{ dateFormat: 'd-m-Y', position: 'auto right' }"
            />
          </span>
        </p>

        <!-- 👉 Due Date -->
        <p class="d-flex align-center justify-sm-end mb-0">
          <span class="me-3">Tanggal Selesai Peminjaman</span>

          <span>
            <AppDateTimePicker
              v-model="data.tanggal_selesai"
              density="compact"
              placeholder="DD-MM-YYYY"
              style="width: 8.9rem;"
              :config="{ dateFormat: 'd-m-Y', position: 'auto right' }"
            />
          </span>
        </p>
      </div>
    </VCardText>
    <!-- !SECTION -->

    <VDivider />

    <VCardText>
      <VRow>
        <VCol cols="6">
          <h6 class="text-sm font-weight-medium mb-3">
            Data Peminjam
          </h6>

          <p class="mb-1">
            {{ user.nama || 'Mohamad Irfan Manaf' }}
          </p>
          <p class="mb-1">
            {{ user.no_sim || '1234567890123456' }}
          </p>
          <p class="mb-1">
            {{ user.alamat || 'Jl. Pinus V No 202' }}
          </p>
          <p class="mb-1">
            {{ user.telepon || '08815223912' }}
          </p>
        </VCol>
        <VCol cols="6">
          <h6 class="text-sm font-weight-medium mb-3">
            Data Mobil:
          </h6>

          <table>
            <tbody>
              <tr>
                <td class="pe-6">
                  Merek Mobil:
                </td>
                <td class="font-weight-semibold">
                  {{ mobil.merek_mobil || 'Toyota' }}
                </td>
              </tr>
              <tr>
                <td class="pe-6">
                  Model
                </td>
                <td>{{ mobil.model || 'Kijang Innova' }}</td>
              </tr>
              <tr>
                <td class="pe-6">
                  Warna:
                </td>
                <td>{{ mobil.warna || 'Hitam' }}</td>
              </tr>
              <tr>
                <td class="pe-6">
                  No Plat:
                </td>
                <td>{{ mobil.no_plat || 'E 8498 XX' }}</td>
              </tr>
              <tr>
                <td class="pe-6">
                  Tarif per Hari:
                </td>
                <td>{{ mobil.tarif || 'Rp. 500.000' }}</td>
              </tr>
              <tr>
                <td class="pe-6">
                  Durasi Peminjaman:
                </td>
                <td>{{ data.durasi + ' Hari' || 'Rp. 500.000' }}</td>
              </tr>
              <tr>
                <td class="pe-6">
                  Total Tagihan:
                </td>
                <td>{{ mobil.total_tarif || 'Rp. 1000.000' }}</td>
              </tr>
            </tbody>
          </table>
        </VCol>
      </VRow>
    </VCardText>
    <VDivider/>
    <VCardText>
      <VRow class="justify-end text-right">
        <VCol cols="12" class="justify-end text-right">
          <VBtn
            @click="doSave()"
          >
            Submit
          </VBtn>
        </VCol>
      </VRow>
    </VCardText> 
  </VCard>
</template>


<script>

import moment from 'moment'
import Swal from 'sweetalert2'
import api from "@/apis/CommonAPI"

export default {
  // unmounted(){
  //   localStorage.removeItem('mobil_pinjaman')
  // },
  mounted(){
    let user_data = JSON.parse(localStorage.getItem('user_data'))
    let mobil_data = JSON.parse(localStorage.getItem('mobil_pinjaman'))

    this.user.user_id = user_data.user_id
    this.user.nama = user_data.name
    this.user.alamat = user_data.alamat
    this.user.telepon = user_data.telepon
    this.user.no_sim = user_data.no_sim

    this.mobil.merek_mobil = mobil_data.merek_mobil
    this.mobil.mobil_id = mobil_data.mobil_id
    this.mobil.model = mobil_data.model
    this.mobil.warna = mobil_data.warna
    this.mobil.no_plat = mobil_data.no_plat
    this.mobil.tarif = mobil_data.tarif
    this.mobil.tarif_asli = mobil_data.tarif_asli
    this.mobil.description = mobil_data.description

    this.data.tanggal_mulai = moment(this.$route.query.tanggal_mulai);
    this.data.tanggal_selesai = moment(this.$route.query.tanggal_selesai);
    this.data.durasi = this.data.tanggal_selesai.diff(this.data.tanggal_mulai, 'days');

    this.data.tanggal_mulai = moment(this.data.tanggal_mulai).format('DD-MM-YYYY');
    this.data.tanggal_selesai = moment(this.data.tanggal_selesai).format('DD-MM-YYYY');

    this.mobil.total_tarif_asli = this.mobil.tarif_asli * parseInt(this.data.durasi)
    this.mobil.total_tarif = new Intl.NumberFormat('id-ID', {
      style: 'currency',
      currency: 'IDR',
      minimumFractionDigits: 0, // You can adjust this if you want decimal places
      maximumFractionDigits: 0
    }).format(this.mobil.total_tarif_asli);

    this.data.transaksi_id = this.generateRandomStringWithDate(5)
    
    
  },
  data(){
    return{
      errorMessage: '',
      successMessage: '',
      data: {
        transaksi_id: '',
        tanggal_mulai: '',
        tanggal_selesai: '',
        durasi: ''
      },
      user: {
        user_id: '',
        nama: '',
        alamat: '',
        telepon: '',
        no_sim: '',
      },
      mobil: {
        mobil_id: '',
        merek_mobil: '',
        model: '',
        warna: '',
        no_plat: '',
        tarif: '',
        tarif_asli: '',
        total_tarif: '',
        total_tarif_asli: '',
        description: ''
      },
      successMessage: '',
      erroMessage: ''
    }
  },
  methods: {
    clearMessage(){
      this.errorMessage = ''
      this.successMessage = ''
    },
    generateRandomStringWithDate(length) {
        const generateRandomString = (length) => {
          const characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
          let result = '';
          const charactersLength = characters.length;
          for (let i = 0; i < length; i++) {
            result += characters.charAt(Math.floor(Math.random() * charactersLength));
          }
          return result;
        };

        // Get the current date and time without special characters
        const now = new Date();
        const year = now.getFullYear();
        const month = String(now.getMonth() + 1).padStart(2, '0'); // Months are 0-indexed
        const day = String(now.getDate()).padStart(2, '0');

        // Concatenate date and time without special characters
        const cleanDateTime = `${year}${month}${day}`;
        
        // Generate a random string and concatenate with clean date-time
        const randomString = generateRandomString(length);
        return `${cleanDateTime}${randomString}`;
    },
    async doSave(){
      let data = {
        transaksi_id: this.data.transaksi_id,
        tanggal_mulai: this.data.tanggal_mulai,
        tanggal_selesai: this.data.tanggal_selesai,
        mobil_id: this.mobil.mobil_id,
        user_id: this.user.user_id,
        total_tarif: this.mobil.total_tarif_asli
      }

      this.clearMessage()

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

            let uri = `/api/v1/transaksi`;
            let responseBody = await api.jsonApi(uri, 'POST', JSON.stringify(data));
            console.log("🚀 ~ doSave ~ responseBody:", responseBody)

            if( responseBody.status != 200 ){
              let msg = Array.isArray(responseBody.message) ? responseBody.message.toString() : responseBody.message;
              console.log("🚀 ~ doSave ~ msg:", msg)
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
    }
  }
}
</script>

<route lang="yaml">
  meta:
    requiresLogin: true
    isAdmin: false
</route>