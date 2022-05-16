// resources/views/Login.vue
<template>
    <div class="h-full flex justify-center p-5">
        <div id="login" class="login-form">
            <h2 class="text-2xl text-blue-400 mx-auto mb-5">Login</h2>
            <label for="login-email">E-Mail</label>
            <input
                type="email"
                name="login-email"
                id="login-email"
                placeholder="max.mustermann@gmail.com"
                class="input"
                formControlName="email"
                @input="valid = true"
                v-model="email"
            />
            <label for="login-password">Password</label>
            <input
                type="password"
                name="login-password"
                id="login-password"
                placeholder="******"
                class="input"
                formControlName="password"
                @input="valid = true"
                v-model="password"
            />
            <div class="invalid-input">
                <span v-if="valid === false">Credentials are invalid.</span>
            </div>
            <button type="submit" class="btn-login" @click="login">Login</button>
            <p>
                Don't have an account?
                <router-link to="/register" class="underline"
                    >Register</router-link
                >
            </p>
        </div>
    </div>
</template>

<script>
import { mainLogin } from "../js/utilities";

export default {
    data() {
        return {
            valid: true,
            loginText: "Login",
            loginDisabled: false,
        };
    },
    props: {
        email: {
            type: String,
        },
        password: {
            type: String,
        },
    },
    methods: {
        login: async function () {
            //Disable Button
            this.loginText = "Logging in...";
            this.loginDisabled = true;

            //Set all to valid
            this.valid = true;

            //Check if credentials are valid
            const result = await mainLogin(this.email, this.password);

            //If credentials are valid, redirect to home
            if (result === true) {
                //Set localStorage
                localStorage.setItem("email", this.email);
                localStorage.setItem("pwd", this.password);
                localStorage.setItem("verified", true);
                //Redirect to lists
                this.$router.push("/lists");
            } else {
                this.valid = false;
            }

            //Reset Button
            this.loginText = "Login";
            this.loginDisabled = false;
        },
    },
};
</script>
