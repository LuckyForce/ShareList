// resources/components/Header.vue
<template>
    <nav class="flex items-center justify-between flex-wrap color-2">
        <div class="flex justify-center align-center">
            <router-link to="/" class="flex items-center nav-link">
                <img :src="imgPath" alt="Logo" class="sm:h-12 h-8" />
                Home
            </router-link>
            <router-link
                v-if="isLoggedIn"
                to="/lists"
                class="flex items-center nav-link"
            >
                Lists
            </router-link>
        </div>
        <div class="flex items-center flex-shrink-0 text-white">
            <router-link to="/about" class="nav-link">About Us</router-link>
            <router-link
                v-if="isLoggedIn === false"
                to="/register"
                class="nav-link"
                >Register</router-link
            >
            <router-link v-if="isLoggedIn" to="/profile" class="nav-link"
                >Profile</router-link
            >
        </div>
    </nav>
</template>

<script>
import { mainLogin } from "../js/utilities";
export default {
    //Create Property isLoggedIn which results with the value of the checkLogin() function
    data() {
        return {
            isLoggedIn: false,
            imgPath: window.location.origin + "/images/logo.png",
        };
    },
    async mounted() {
        //Get LocalStorage Email
        const email = window.localStorage.getItem("email");
        //Get LocalStorage Password
        const pwd = window.localStorage.getItem("pwd");

        this.isLoggedIn = await mainLogin(email, pwd);
    },
    watch: {
        async $route(to, from) {
            this.isLoggedIn = await this.checkLogin();
        },
    },
    methods: {
        checkLogin: async function () {
            //Check if account data is set in cookies
            const email = window.localStorage.getItem("email");
            const pwd = window.localStorage.getItem("pwd");
            if (email && pwd) {
                return true;
            } else {
                return false;
            }
        },
    },
};
</script>
