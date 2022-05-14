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
export default {
    //Create Property isLoggedIn which results with the value of the checkLogin() function
    data() {
        return {
            isLoggedIn: false,
            imgPath: window.location.origin + "/images/logo.png",
        };
    },
    async mounted(){
        this.isLoggedIn = await checkLogin();
    },
};
async function checkLogin() {
    //Check if account data is set in cookies
    const email = window.localStorage.getItem("email");
    const pwd = window.localStorage.getItem("pwd");
    //If account data is set, check if token is still valid
    if (email && pwd) {
        //Check if credentials are still valid
        const success = await login(email, pwd);
        return success;
    } else {
        return false;
    }
}

async function login(email, pwd) {
    const success = await axios
        .post("/api/user/login", {
            email: email,
            password: pwd,
        })
        .then((response) => {
            //Set token in session
            window.sessionStorage.setItem("token", response.data.token);
            //Set Expires in session
            window.sessionStorage.setItem("expires", response.data.expires);

            return true;
        })
        .catch((error) => {
            //If credentials are not valid, remove account data from cookies
            window.localStorage.removeItem("email");
            window.localStorage.removeItem("pwd");

            return false;
        });
    return success;
}
</script>
