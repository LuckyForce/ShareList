// resources/js/app.js
require("./bootstrap");

import { createApp } from "vue";
import { createRouter, createWebHistory } from "vue-router";
//Views
import Home from "../views/Home.vue";
import Login from "../views/Login.vue";
import PageNotFound from "../views/PageNotFound.vue";
//Components
import Header from "../components/Header.vue";
import Footer from "../components/Footer.vue";

//Routes
const routes = [
  {
    path: "/",
    component: Home,
    name: "Home",
  },
  {
    path: "/login",
    component: Login,
    name: "Login",
  },
  {
    path: "/:pathMatch(.*)*",
    component: PageNotFound,
    name: "PageNotFound",
  }
];

//Create Router
const router = createRouter({
  history: createWebHistory(),
  routes
})

const app = createApp({
  components: {
    Home,
    Login,
    'header-component': Header,
    'footer-component': Footer,
  }
});

app.use(router);

app.mount("#app");
