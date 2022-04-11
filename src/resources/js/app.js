// resources/js/app.js
require("./bootstrap");

import { createApp } from "vue";
import { createRouter, createWebHistory } from "vue-router";
//Views
import Home from "../views/Home.vue";
import Login from "../views/Login.vue";
import Register from "../views/Register.vue";
import Profile from "../views/Profile.vue";
import Lists from "../views/Lists.vue";
import List from "../views/List.vue";
import ListEdit from "../views/ListEdit.vue";
import Invite from "../views/Invite.vue";
import Verification from "../views/Verification.vue";
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
    path: "/register",
    component: Register,
    name: "Register",
  },
  {
    path: "/profile",
    component: Profile,
    name: "Profile",
  },
  {
    path: "/lists",
    component: Lists,
    name: "Lists",
  },
  {
    path: "/list/:id",
    component: List,
    name: "List",
  },
  {
    path: "/list/:id/edit",
    component: ListEdit,
    name: "ListEdit",
  },
  {
    path: "/invite/:id",
    component: Invite,
    name: "Invite",
  },
  {
    path: "/verification/:id",
    component: Verification,
    name: "Verification",
  },
  {
    path: "/:pathMatch(.*)*",
    component: PageNotFound,
    name: "PageNotFound",
  },
];

//Create Router
const router = createRouter({
  history: createWebHistory(),
  routes
})

const app = createApp({
  components: {
    'header-component': Header,
    'footer-component': Footer,
  }
});

app.use(router);

app.mount("#app");
