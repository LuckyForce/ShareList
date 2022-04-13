// resources/components/Footer.vue
<template>
    <footer class="grid sm:grid-cols-2 grid-cols-1 bg-1 p-5 mt-auto mb-0">
        <div class="flex justify-center align-center">
            <router-link to="/about" class="text-2xl text-white mx-auto">About Us</router-link>
        </div>
        <div class="flex justify-center align-center">
            <a
                href="mailto:info@adrian-schauer.at"
                class="text-2xl text-white mx-auto"
                >Contact</a
            >
        </div>
    </footer>
</template>

<script>
export default {};
</script>
