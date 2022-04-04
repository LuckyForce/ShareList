// resources/views/PageNotFound.vue
<template>
    <div class="w-1/2 bg-white rounded-lg shadow-lg mx-auto p-4">
        <h1 class="text-2xl text-gray-700 text-center mb-4">
            404 View
        </h1>
        <p>
            Page: {{ $route.params.pathMatch[0] }} not found.
        </p>
    </div>
</template>

<script>
export default {};
</script>