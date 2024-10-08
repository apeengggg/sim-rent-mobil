import { setupLayouts } from 'virtual:generated-layouts'
import { createRouter, createWebHistory } from 'vue-router'
import routes from '~pages'
import jwtDecode from 'jwt-decode';

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      redirect: to => {
        return { name: 'apps-login' }
      }
    },
    ...setupLayouts(routes),
  ],
})

function isAuthenticated() {
  const token = localStorage.getItem('token');
  

  if (!token) {
    return false; // No token
  }

  try {
    const decodedToken = jwtDecode(token);
    
    const currentTime = Date.now() / 1000;

    if (decodedToken.exp && decodedToken.exp < currentTime) {
      return false; // Token expired
    }

    return true; // Token valid
  } catch (error) {
    console.error('Invalid token:', error);
    return false; // Invalid token
  }
}


// Docs: https://router.vuejs.org/guide/advanced/navigation-guards.html#global-before-guards
router.beforeEach((to, from, next) => {

  const user_data = JSON.parse(localStorage.getItem('user_data'))

  if ((to.meta.requiresLogin && !isAuthenticated()) || (user_data && user_data.role_name.toLowerCase() === 'user' && to.meta.isAdmin)) {
    // If not authenticated, redirect to login page
    return next({ name: 'apps-login'})
  }
  // // If authenticated or route doesn't require auth, proceed as normal
  next();
})
export default router
