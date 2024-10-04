<script setup>
import safeBoxWithGoldenCoin from '@images/misc/3d-safe-box-with-golden-dollar-coins.png'
import spaceRocket from '@images/misc/3d-space-rocket-with-smoke.png'
import dollarCoinPiggyBank from '@images/misc/dollar-coins-flying-pink-piggy-bank.png'

const props = defineProps({
  xs: {
    type: [
      Number,
      String,
    ],
    required: false,
  },
  sm: {
    type: [
      Number,
      String,
    ],
    required: false,
  },
  md: {
    type: [
      String,
      Number,
    ],
    required: false,
  },
  lg: {
    type: [
      String,
      Number,
    ],
    required: false,
  },
  xl: {
    type: [
      String,
      Number,
    ],
    required: false,
  },
})

const annualMonthlyPlanPriceToggler = ref(true)
</script>

<template>
  <!-- 👉 Title and subtitle -->
  <div class="text-center">
    <h4 class="text-h4 pricing-title mb-4">
      MIM - Rental Mobil
    </h4>
    <p class="mb-0">
      Menyediakan berbagai macam mobil yang terawat, nyaman, dan harga sewa terjangkau
    </p>
    <p>Pilih mobil yang ingin disewa sekarang!</p>
  </div>

  <!-- SECTION pricing plans -->
  <VRow>
    <VCol
      v-for="plan in katalog"
      :key="plan.logo"
      v-bind="props"
      cols="12"
    >
      <!-- 👉  Card -->
      <VCard
        flat
        border
        :class="plan.isPopular ? 'border-primary border-opacity-100' : ''"
      >
        <VCardText
          style="height: 4.125rem;"
          class="text-end"
        >
          <!-- 👉 Popular -->
          <VChip
            v-show="plan.isPopular"
            label
            color="primary"
            size="small"
          >
            Popular
          </VChip>
        </VCardText>

        <!-- 👉 Plan logo -->
        <VCardText class="text-center">
          <VImg
            :height="140"
            :src="plan.logo"
            class="mx-auto mb-5"
          />

          <!-- 👉 Plan name -->
          <h5 class="text-h5 mb-2">
            {{ plan.name }}
          </h5>
          <p class="mb-0">
            {{ plan.tagLine }}
          </p>
        </VCardText>

        <!-- 👉 Plan price  -->
        <VCardText class="position-relative text-center">
          <div class="d-flex justify-center align-center">
            <sup class="text-sm font-weight-medium me-1">$</sup>
            <h1 class="text-5xl font-weight-medium text-primary">
              {{ annualMonthlyPlanPriceToggler ? Math.floor(Number(plan.yearlyPrice) / 12) : plan.monthlyPrice }}
            </h1>
            <sub class="text-sm font-weight-medium ms-1 mt-4">/month</sub>
          </div>

          <!-- 👉 Annual Price -->
          <span
            v-show="annualMonthlyPlanPriceToggler"
            class="position-absolute text-caption font-weight-medium mt-1"
            style="inset-inline: 0;"
          >
            {{ plan.yearlyPrice === 0 ? 'free' : `USD ${plan.yearlyPrice}/Year` }}
          </span>
        </VCardText>

        <!-- 👉 Plan features -->
        <VCardText class="mt-5">
          <VList class="card-list">
            <VListItem
              v-for="feature in plan.features"
              :key="feature"
            >
              <template #prepend>
                <VIcon
                  :size="14"
                  icon="tabler-circle"
                  class="me-3"
                />
              </template>

              <VListItemTitle>
                {{ feature }}
              </VListItemTitle>
            </VListItem>
          </VList>
        </VCardText>
      </VCard>
    </VCol>
  </VRow>
  <!-- !SECTION  -->
</template>

<script>

export default {
  components: {

  },
  data(){
    return {
      katalog: [
      {
        name: 'Basic',
        tagLine: 'A simple start for everyone',
        logo: dollarCoinPiggyBank,
        monthlyPrice: 0,
        yearlyPrice: 0,
        isPopular: false,
        current: true,
        features: [
          '100 responses a month',
          'Unlimited forms and surveys',
          'Unlimited fields',
          'Basic form creation tools',
          'Up to 2 subdomains',
        ],
      },
      {
        name: 'Standard',
        tagLine: 'For small to medium businesses',
        logo: safeBoxWithGoldenCoin,
        monthlyPrice: 42,
        yearlyPrice: 460,
        isPopular: false,
        current: false,
        features: [
          'Unlimited responses',
          'Unlimited forms and surveys',
          'Instagram profile page',
          'Google Docs integration',
          'Custom “Thank you” page',
        ],
      },
      {
        name: 'Enterprise',
        tagLine: 'Solution for big organizations',
        logo: spaceRocket,
        monthlyPrice: 84,
        yearlyPrice: 690,
        isPopular: false,
        current: false,
        features: [
          'PayPal payments',
          'Logic Jumps',
          'File upload with 5GB storage',
          'Custom domain support',
          'Stripe integration',
        ],
      },
      {
        name: 'Enterprise',
        tagLine: 'Solution for big organizations',
        logo: spaceRocket,
        monthlyPrice: 84,
        yearlyPrice: 690,
        isPopular: false,
        current: false,
        features: [
          'PayPal payments',
          'Logic Jumps',
          'File upload with 5GB storage',
          'Custom domain support',
          'Stripe integration',
        ],
      },
      {
        name: 'Enterprise',
        tagLine: 'Solution for big organizations',
        logo: spaceRocket,
        yearlyPrice: 690,
        isPopular: false,
        current: false,
        features: [
          'PayPal payments',
          'Logic Jumps',
          'File upload with 5GB storage',
          'Custom domain support',
          'Stripe integration',
        ],
      }]
    }
  },
  mounted(){
  },
  methods: {

  }
}
</script>

<style lang="scss" scoped>
.card-list {
  --v-card-list-gap: 0.75rem;
}

.save-upto-chip {
  inset-block-start: -1.5rem;
  inset-inline-end: -7rem;
}
</style>
