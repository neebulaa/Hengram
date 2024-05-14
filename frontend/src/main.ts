import { createApp } from 'vue'
import App from './App.vue'
import router from './router';
import "./assets/css/bootstrap.css"
import "./assets/css/style.css"
import store from './store';

createApp(App).use(router).use(store).mount('#app');
