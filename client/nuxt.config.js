export default {
  generate: {subFolders: true},
  
  router: {
    base: '/',
    middleware: ['auth']
  },
  
  // Global page headers: https://go.nuxtjs.dev/config-head
  head: {
    title: 'vitamin-chat',
    htmlAttrs: {
      lang: 'en'
    },
    meta: [
      {charset: 'utf-8'},
      {name: 'viewport', content: 'width=device-width, initial-scale=1'},
      {hid: 'description', name: 'description', content: ''}
    ],
    link: [
      {rel: 'icon', type: 'image/x-icon', href: '/favicon.ico'}
    ]
  },
  
  // Global CSS: https://go.nuxtjs.dev/config-css
  css: [
    "~assets/scss/global.scss"
  ],
  
  // Plugins to run before rendering page: https://go.nuxtjs.dev/config-plugins
  plugins: [],
  
  // Auto import components: https://go.nuxtjs.dev/config-components
  components: true,
  
  // Modules for dev and build (recommended): https://go.nuxtjs.dev/config-modules
  buildModules: [
    // https://go.nuxtjs.dev/eslint
    '@nuxtjs/eslint-module',
    // https://go.nuxtjs.dev/stylelint
    '@nuxtjs/stylelint-module',
    // https://go.nuxtjs.dev/tailwindcss
    '@nuxtjs/tailwindcss',
    '@nuxtjs/laravel-echo'
  ],
  
  // Modules: https://go.nuxtjs.dev/config-modules
  modules: [
    '@nuxtjs/axios',
    '@nuxtjs/auth-next',
    "@nuxtjs/style-resources"
  ],
  
  // Build Configuration: https://go.nuxtjs.dev/config-build
  build: {},
  
  axios: {
    proxy: true,
    credentials: true,
    baseURL: 'http://vitamin-test.loc'
  },
  
  proxy: {
    '/api/': 'http://nginx',
  },
  
  auth: {
    strategies: {
      laravelSanctum: {
        provider: 'laravel/sanctum',
        url: 'http://vitamin-test.loc',
        endpoints: {
          login: {url: '/api/v1/auth/login', method: 'post'},
          logout: {url: '/api/v1/auth/logout', method: 'post'},
          user: {url: '/api/v1/auth/user', method: 'get'}
        }
      }
    },
    redirect: {
      login: '/login',
      logout: '/',
      callback: '/chat',
      home: '/'
    },
    token: {
      name: 'Bearer'
    },
    user: {
      property: 'user',
      autoFetch: false
    },
    cookie: {
      name: 'XSRF-TOKEN',
    },
    watchLoggedIn: true,
    plugins: [ { src: '~/plugins/auth.js', ssr: false } ]
  },
  echo: {
    broadcaster: 'pusher',
    authModule: true,
    connectOnLogin: true,
    forceTLS: false,
    authEndpoint: process.env.API_URL_BROWSER + '/api/broadcasting/auth',
    key: process.env.WEBSOCKET_KEY,
    wsHost: process.env.WEBSOCKET_HOST,
    wsPort: process.env.WEBSOCKET_PORT,
    encrypted: false,
    disableStats: true,
    auth: {
      headers: {
        'X-XSRF-TOKEN': ''
      }
    }
  },
  styleResources: {
    scss: ["./assets/scss/*.scss"]
  }
}
