// resources/views/PageNotFound.vue
<template>
    <div class="flex flex-col justify-center align-middle h-full">
        <h1 class="text-2xl sm:text-9xl text-gray-700 text-center mb-4">
            404 Error
        </h1>
        <h2 class="text-xl sm:text-5xl text-gray-700 text-center">
            Page: {{ $route.params.pathMatch[0] }} not found.
        </h2>
    </div>
</template>

<script>
export default {};
</script>