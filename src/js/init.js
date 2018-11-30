/* css imports */
import "../css/main.css";
/* js imports */
import Vue from "vue";
import App from "./App.vue";
new Vue({
    el: "#app",
    render: function (h) { return h(App); },
});
