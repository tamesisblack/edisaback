require('./bootstrap');

window.Vue = require('vue').default;
Vue.component('example-component', require('./components/ExampleComponent.vue').default);
Vue.component('tablero-component', require('./components/TableroComponent.vue').default);
Vue.component('evento-component', require('./components/EventosComponent.vue').default);
Vue.component('cartilla-component', require('./components/CartillaComponent.vue').default);
Vue.component('cliente-component', require('./components/ClientesComponent.vue').default);
Vue.component('jugador-component', require('./components/JugadoresComponent.vue').default);
Vue.component('cartilla-component', require('./components/CartillaComponent.vue').default);

import Vuetify from 'vuetify';
Vue.use(Vuetify);

import { i18n } from '../plugins/i18n'
Vue.use(i18n);

const app = new Vue({
    el: '#app',
    i18n,
    vuetify: new Vuetify(),
});
