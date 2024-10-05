<script setup>
import navItems from '@/navigation/vertical'
import { useThemeConfig } from '@core/composable/useThemeConfig'

// Components
import Footer from '@/layouts/components/Footer.vue'
import NavBarI18n from '@/layouts/components/NavBarI18n.vue'
import NavBarNotifications from '@/layouts/components/NavBarNotifications.vue'
import NavbarShortcuts from '@/layouts/components/NavbarShortcuts.vue'
import NavbarThemeSwitcher from '@/layouts/components/NavbarThemeSwitcher.vue'
import NavSearchBar from '@/layouts/components/NavSearchBar.vue'
import UserProfile from '@/layouts/components/UserProfile.vue'

// @layouts plugin
import { VerticalNavLayout } from '@layouts'

const { appRouteTransition, isLessThanOverlayNavBreakpoint } = useThemeConfig()
const { width: windowWidth } = useWindowSize()
</script>

<template>
  <VerticalNavLayout
    :nav-items="navItemsFilter"
  >
    <!-- 👉 navbar -->
    <template #navbar="{ toggleVerticalOverlayNavActive }">
      <div class="d-flex h-100 align-center">
        <VBtn
          v-if="isLessThanOverlayNavBreakpoint(windowWidth)"
          icon
          variant="text"
          color="default"
          class="ms-n3"
          size="small"
          @click="toggleVerticalOverlayNavActive(true)"
        >
          <VIcon
            icon="tabler-menu-2"
            size="24"
          />
        </VBtn>

        <!-- <NavSearchBar class="ms-lg-n3" /> -->

        <VSpacer />

        <!-- <NavBarI18n /> -->
        <!-- <NavbarThemeSwitcher /> -->
        <!-- <NavbarShortcuts /> -->
        <!-- <NavBarNotifications class="me-2" /> -->
        <UserProfile />
      </div>
    </template>

    <!-- 👉 Pages -->
    <RouterView v-slot="{ Component }">
      <Transition
        :name="appRouteTransition"
        mode="out-in"
      >
        <Component :is="Component" />
      </Transition>
    </RouterView>

    <!-- 👉 Footer -->
    <template #footer>
      <Footer />
    </template>

    <!-- 👉 Customizer -->
    <TheCustomizer />
  </VerticalNavLayout>
</template>

<script>
  export default {
    created(){
      this.setupMenu()
    },
    data(){
      return {
        navItemsFilter: []
      }
    },
    methods: {
      setupMenu(){
        const role = JSON.parse(localStorage.getItem('user_data')).role_name
        let isAdmin = false
        if(role.toLowerCase() != "user"){
          isAdmin = true
        }
        if (isAdmin) {
          this.navItemsFilter = navItems.filter(item => {

            if (item.children) {
              const filteredChildren = item.children.filter(child => child.isAdmin);
              if (filteredChildren.length > 0) {
                return { ...item,  ...filteredChildren };
              }
            }

            return true;
          });
        } else {
          this.navItemsFilter = navItems.filter(item => {
            if (!item.isAdmin) return true;

            // 
            if (item.children) {
              const filteredChildren = item.children.filter(child => !child.isAdmin);
              if (filteredChildren.length > 0) {
                return { ...item, ...filteredChildren };
              }
            }

            return false;
          });
        }
      }
    }
  }

</script>
