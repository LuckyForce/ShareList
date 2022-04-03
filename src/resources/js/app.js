// resources/js/app.js
require("./bootstrap");

import { createApp } from "vue";
import { createRouter, createWebHashHistory } from "vue-router";
//Components
import Home from "../components/Home.vue";
import Login from "../components/Login.vue";

//Routes
const routes = [
  {
    path: "/",
    component: Home
  },
  {
    path: "/login",
    component: Login
  }
];

//Create Router
const router = createRouter({
  history: createWebHashHistory(),
  routes
})

const app = createApp({
  components: {
    Home,
    Login
  },
});

app.use(router);

app.mount("#app");
