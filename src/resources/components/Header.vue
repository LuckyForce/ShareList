// resources/components/Header.vue
<template>
    <nav class="flex items-center justify-between flex-wrap color-2">
        <div class="flex justify-center align-center">
            <router-link to="/" class="flex items-center nav-link ml-1 link">
                <img :src="imgPath" alt="Logo" class="sm:h-12 h-8" />
                Home
            </router-link>
            <router-link
                
                to="/lists"
                class="flex items-center nav-link link"
            >
                Lists
            </router-link>
        </div>
        <div class="flex items-center flex-shrink-0 text-white">
            <router-link to="/about" class="nav-link link">About Us</router-link>
            <router-link
                v-if="isLoggedIn === false"
                to="/register"
                class="nav-link link"
                >Register</router-link
            >
            <router-link v-if="isLoggedIn" to="/profile" class="nav-link link"
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
        console.log("Header Init");

        //Get LocalStorage Email
        const email = window.localStorage.getItem("email");
        //Get LocalStorage Password
        const pwd = window.localStorage.getItem("pwd");
        
        const verified = window.localStorage.getItem("verified");
        console.log("Verified: "+verified);

        if(verified !== undefined && verified !== null){
            console.log("Verified");
            this.isLoggedIn = await mainLogin(email, pwd);
        }else{
            this.isLoggedIn = false;
        }
        console.log("isLoggedIn: "+this.isLoggedIn);
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
            const verified = window.localStorage.getItem("verified");
            //Both cant be null
            if (email === null || pwd === null || verified === null || email === undefined || pwd === undefined || verified === undefined) {
                return false;
            } else {
                return true;
            }
        },
    },
};
</script>
