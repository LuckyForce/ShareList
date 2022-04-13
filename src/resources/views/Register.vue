<template>
    <div>
        <router-link to="/" class="cover"></router-link>
        <div id="register" class="login-form">
            <h2 class="text-2xl text-blue-400 mx-auto mb-5">Register</h2>
            <label for="register-email">E-Mail</label>
            <input
                type="text"
                name="register-email"
                id="register-email"
                placeholder="max.mustermann@gmail.com"
                class="input"
            />
            <label for="register-password">Password</label>
            <input
                type="password"
                name="register-password"
                id="register-password"
                placeholder="******"
                class="input"
            />
            <label for="register-password-confirm">Confirm Password</label>
            <input
                type="password"
                name="register-password-confirm"
                id="register-password-confirm"
                placeholder="******"
                class="input"
            />
            <div class="flex align-center">
                <input
                    type="checkbox"
                    name="register-agreement-confirm"
                    id="register-agreement-confirm"
                    class="my-auto mr-1"
                />
                <label for="register-agreement-confirm"
                    >I hereby agree to the terms and conditions stated in
                    <a routerLink="/about" class="underline">About Us</a> of
                    GJokes.</label
                >
            </div>
            <p
                class="bg-red-600 rounded-md p-1 w-full mt-2 text-center text-white"
            >
                ErrorMessage
            </p>
            <button class="btn-register">Register</button>
            <p>
                Already have an account?
                <router-link to="/login" class="underline">Login</router-link>
            </p>
        </div>
    </div>
</template>

<script>
export default {};
</script>
