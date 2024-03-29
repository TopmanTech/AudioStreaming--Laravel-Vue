import { createApp } from 'vue'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import { clickaway } from '@/directives'
import App from './App.vue'

createApp(App)
  .component('Icon', FontAwesomeIcon)
  .directive('AudioStreaming-clickaway', clickaway)
  .mount('#app')
