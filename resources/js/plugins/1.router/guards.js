import { canNavigate } from '@layouts/plugins/casl'

export const setupGuards = router => {
  // 👉 router.beforeEach
  // Docs: https://router.vuejs.org/guide/advanced/navigation-guards.html#global-before-guards

  router.beforeEach(to => {
    /*
         * If it's a public route, continue navigation. This kind of pages are allowed to visited by login & non-login users. Basically, without any restrictions.
         * Examples of public routes are, 404, under maintenance, etc.
         */
    if (to.meta.public)
      return

    /**
         * Check if user is logged in by checking if token & user data exists in local storage
         * Feel free to update this logic to suit your needs
         */
    const isLoggedIn = !!(useCookie('userData').value && useCookie('accessToken').value)
    console.log('isLoggedIn', isLoggedIn);
    console .log('to', to);
    /*
          If user is logged in and is trying to access login like page, redirect to home
          else allow visiting the page
          (WARN: Don't allow executing further by return statement because next code will check for permissions)
         */
    if (to.meta.unauthenticatedOnly) {
        console.log('entre al primer if');
      if (isLoggedIn){
        console.log('entre al segundo if');
        return '/dashboard'
      }else{
        console.log('entre al segundo if');
        return undefined}
    }
    if (!canNavigate(to) && to.matched.length) {
      /* eslint-disable indent */
            return isLoggedIn
                ? { name: 'not-authorized' }
                : {
                    name: 'login',
                    /* query: {
                        ...to.query,
                        to: to.fullPath !== '/' ? to.path : undefined,
                    }, */
                }
            /* eslint-enable indent */
    }
  })

  /* router.beforeEach(to => {
    const isLoggedIn = !!(useCookie('userData').value && useCookie('accessToken').value)

    if (to.meta.requiresAuth && !isLoggedIn) {
      return {
        name: 'login',
        query: {
            ...to.query,
            to: to.fullPath !== '/' ? to.path : undefined,
        },
      }
    }
  }) */
}
