// resources/components/Header.vue
<template>
    <vue-nav-bar class="flex items-center justify-between flex-wrap color-2">
        <div class="flex justify-center align-center">
            <router-link
                to="/"
                class="flex items-center nav-link"
            >
                <img src="images/logo.png" alt="Logo" class="sm:h-12 h-8" />
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
            <router-link to="/about" class="nav-link"
                >About Us</router-link
            >
            <router-link
                v-if="isLoggedIn === false"
                to="/register"
                class="nav-link"
                >Register</router-link
            >
            <router-link
                v-if="isLoggedIn"
                to="/profile"
                class="nav-link"
                >Profile</router-link
            >
        </div>
    </vue-nav-bar>
</template>

<script>
export default {
    //Create Property isLoggedIn
    data() {
        return {
            isLoggedIn: true,
        };
    },
    //Create Method checkLoginStatus
    checkLoginStatus() {
        //Check if account data is set in cookies
        const email = window.localStorage.getItem("email");
        const pwd = window.localStorage.getItem("pwd");
        const token = window.sessionStorage.getItem("token");
        const expires = window.sessionStorage.getItem("expires");
        //If account data is set, check if token is still valid
        if (email && pwd) {
            //Check if token is still valid
            if (token && expires) {
                //Check if token is still valid
                if (expires > Date.now()) {
                    //Token is still valid, return true
                    return true;
                } else {
                    //Token is not valid, login again
                    return this.login(email, pwd);
                }
            } else {
                //Token is not set, login again
                return this.login(email, pwd);
            }
        } else {
            //Account data is not set, return false
            return false;
        }
    },
};
</script>
