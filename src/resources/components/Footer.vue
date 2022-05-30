// resources/components/Footer.vue
<template>
    <footer class="color-2 p-5 mt-auto mb-0">
        <div class="grid sm:grid-cols-3 grid-cols-1 gap-2">
            <div class="flex justify-center align-center">
                <router-link
                    to="/about"
                    class="text-2xl text-white mx-auto text-center link"
                    >About Us</router-link
                >
            </div>
            <div class="flex justify-center align-center">
                <a
                    href="mailto:info@adrian-schauer.at"
                    class="text-2xl text-white mx-auto text-center link"
                    >Contact</a
                >
            </div>
            <div class="flex justify-center align-center">
                <router-link
                    to="/privacypolicy"
                    class="text-2xl text-white mx-auto text-center link"
                    >Privacy Policy</router-link
                >
            </div>
        </div>
        <div class="flex justify-center mt-5">
            <span class="text-white"> &copy; 2022 Adrian Schauer </span>
        </div>
    </footer>
</template>

<script>
export default {};
</script>
