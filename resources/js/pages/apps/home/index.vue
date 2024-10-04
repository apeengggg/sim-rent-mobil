<script setup>
import laptopGirl from '@images/illustrations/laptop-girl.png'

const features = [
  {
    feature: '14-days free trial',
    basic: true,
    standard: true,
    enterprise: true,
    addOnAvailable: {
      starter: false,
      pro: false,
      enterprise: false,
    },
  },
  {
    feature: 'No user limit',
    basic: false,
    standard: false,
    enterprise: true,
    addOnAvailable: {
      starter: false,
      pro: false,
      enterprise: false,
    },
  },
  {
    feature: 'Product Support',
    basic: false,
    standard: true,
    enterprise: true,
    addOnAvailable: {
      starter: false,
      pro: false,
      enterprise: false,
    },
  },
  {
    feature: 'Email Support',
    basic: false,
    standard: false,
    enterprise: true,
    addOnAvailable: {
      starter: false,
      pro: true,
      enterprise: false,
    },
  },
  {
    feature: 'Integrations',
    basic: false,
    standard: true,
    enterprise: true,
    addOnAvailable: {
      starter: false,
      pro: false,
      enterprise: false,
    },
  },
  {
    feature: 'Removal of Front branding',
    basic: false,
    standard: false,
    enterprise: true,
    addOnAvailable: {
      starter: false,
      pro: true,
      enterprise: false,
    },
  },
  {
    feature: 'Active maintenance & support',
    basic: false,
    standard: false,
    enterprise: true,
    addOnAvailable: {
      starter: false,
      pro: false,
      enterprise: false,
    },
  },
  {
    feature: 'Data storage for 365 days',
    basic: false,
    standard: false,
    enterprise: true,
    addOnAvailable: {
      starter: false,
      pro: false,
      enterprise: false,
    },
  },
]

const faqs = [
  {
    question: 'What counts towards the 100 responses limit?',
    answer: 'Donec placerat, lectus sed mattis semper, neque lectus feugiat lectus, varius pulvinar diam eros in elit. Pellentesque convallis laoreet laoreet.Donec placerat, lectus sed mattis semper, neque lectus feugiat lectus, varius pulvinar diam eros in elit. Pellentesque convallis laoreet laoreet.',
  },
  {
    question: 'How do you process payments?',
    answer: 'We accept Visa®, MasterCard®, American Express®, and PayPal®. So you can be confident that your credit card information will be kept safe and secure.',
  },
  {
    question: 'What payment methods do you accept?',
    answer: '2Checkout accepts all types of credit and debit cards.',
  },
  {
    question: 'Do you have a money-back guarantee?',
    answer: 'Yes. You may request a refund within 30 days of your purchase without any additional explanations.',
  },
]
</script>

<template>
  <VCard class="pt-6">
    <div class="text-right mr-6">
      <VBtn color="Info" class="mr-2" @click="doRegister()">
        Register
      </VBtn>
      <VBtn color="primary" @click="doLogin()">
        Login
      </VBtn>
    </div>
    <VCardText class="pt-12 mb-16 pb-16">
      <VRow>
        <VCol
          cols="12"
          sm="8"
          md="12"
          lg="10"
          class="mx-auto"
        >
          <Katalog md="4" />
        </VCol>
      </VRow>
    </VCardText>
  </VCard>
</template>

<script>
import Katalog from '@/views/apps/home/Katalog.vue'

export default {
  components: {
    Katalog
  },
  mounted(){
  },
  methods:{
    doLogin(){
      this.$router.push('/apps/login')
    },
    doRegister(){
      this.$router.push('/apps/register')
    },
  }
}
</script>

<style lang="scss">
.page-pricing-free-trial-banner-bg {
  /* stylelint-disable-next-line color-function-notation */
  background-color: rgba(var(--v-theme-primary), var(--v-selected-opacity));
}

@media screen and (min-width: 960px) {
  .free-trial-illustrator {
    position: absolute;
    inset-block-end: 0;
    inset-inline-end: 10%;
  }
}

@media screen and (max-width: 959px) {
  .free-trial-illustrator {
    position: relative;
    inset-block-end: -11px;
  }
}
</style>

<route lang="yaml">
  meta:
    layout: blank
  </route>
  
