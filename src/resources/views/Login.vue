// resources/views/Login.vue
<template>
    <div>
        <router-link to="/" class="cover"></router-link>
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
            />
            <div class="invalid-input">
                <span> E-Mail is required </span>
                <span> E-Mail is not valid </span>
            </div>
            <label for="login-password">Password</label>
            <input
                type="password"
                name="login-password"
                id="login-password"
                placeholder="******"
                class="input"
                formControlName="password"
            />
            <div class="invalid-input">
                <span> Password is required </span>
                <span> Password is too short </span>
                <span> Password is too long </span>
            </div>
            <button type="submit" class="btn-login">Login</button>
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
export default {
    login() {
        console.log("Login");
    },
};
</script>
