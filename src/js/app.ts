import Vue from "vue";
import Vuex from "vuex";
import App from "./App.vue";

Vue.use(Vuex);

const store = new Vuex.Store({
    strict: process.env.NODE_ENV !== "production",
    state: {
        count: 0,
    },
    getters: {
        COUNT: (state) => {
            return state.count;
        },
    },
    mutations: {
        SET_COUNT: (state, payload) => {
            state.count = payload;
        },
    },
});

new Vue({
    el: "#app",
    render: (h) => h(App),
    store,
});