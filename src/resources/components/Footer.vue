// resources/components/Footer.vue
<template>
    <footer class="grid grid-cols-3 color-2 p-5 mt-auto mb-0">
        <div class="flex justify-center align-center">
            <router-link to="/about" class="text-2xl text-white mx-auto">About Us</router-link>
        </div>
        <div class="flex justify-center align-center mb-3">
            <a
                href="mailto:info@adrian-schauer.at"
                class="text-2xl text-white mx-auto mb-3"
                >Contact</a
            >
        </div>
        <div class="flex justify-center align-center mb-10 mt-3">
            <router-link to="/privacypolicy" class="text-2xl text-white mx-auto">Privacy Policy</router-link>
        </div>
        <div>
            <p class="text-center text-white my-auto content-center">
                &copy; 2022 Adrian Schauer
            </p>
        </div>
    </footer>
</template>

<script>
export default {};
</script>
