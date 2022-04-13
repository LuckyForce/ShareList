// resources/components/Header.vue
<template>
    <nav class="flex items-center justify-between flex-wrap color-2">
        <div class="flex justify-center align-center">
            <router-link to="/" class="flex items-center nav-link">
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
export default {
    //Create Property isLoggedIn which results with the value of the checkLogin() function
    data() {
        return {
            isLoggedIn: checkLogin(),
        };
    },
};
function checkLogin() {
    //Check if account data is set in cookies
    const email = window.localStorage.getItem("email");
    const pwd = window.localStorage.getItem("pwd");
    //If account data is set, check if token is still valid
    if (email && pwd) {
        return true;
    } else {
        return false;
    }
}
</script>
