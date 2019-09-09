/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');
window.Bus = new Vue();

import Flash from './plugins/flash.js';

Vue.use(Flash);

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i);
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default));

Vue.component('flash-message', require('./components/partials/flashmessage.vue').default);

Vue.component('item-table', require('./components/item/table.vue').default);
Vue.component('item-quantity-table', require('./components/item/quantity/table.vue').default);
Vue.component('order-table', require('./components/order/table.vue').default);
Vue.component('order-article-table', require('./components/order/article/table.vue').default);

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

const app = new Vue({
    el: '#app',
});

$('#menu-toggle').click(function() {
    $('#nav, #content-container').toggleClass('active');
    $('.collapse.in').toggleClass('in');
    $('a[aria-expanded=true]').attr('aria-expanded', 'false');
});

$('.collapse', 'nav#nav').on('show.bs.collapse', function(){
    $('a[data-target="#' + $(this).attr('id') +'"] i.fas', 'nav#nav').toggleClass("fa-caret-right fa-caret-down");
}).on('hide.bs.collapse', function(){
    $('a[data-target="#' + $(this).attr('id') +'"] i.fas', 'nav#nav').toggleClass("fa-caret-down fa-caret-right");
});
