// resources/views/Verification.vue
<template>
    <div class="flex flex-col justify-center align-middle h-full">
        <p class="text-xl sm:text-5xl text-gray-700 text-center">
            {{ message }}
        </p>
        <p v-if="loaded" class="text-lg sm:text-2xl text-gray-700 text-center">
            You can close this Window now.
        </p>
    </div>
</template>

<script>
export default {
    data() {
        return {
            message: "Loading...",
            loaded: false,
        };
    },
    async mounted() {
        const result = await this.checkVerification(
            this.$route.params.id,
            this.$route.params.token
        );
        this.message = result;
    },
    methods: {
        checkVerification: async function (id, token) {
            let loaded = false;
            //Fetch verification token from url
            console.log(token);
            console.log(id);
            //Axios request to verify user
            const result = await axios
                .post("/api/user/verify", {
                    token: token,
                    id: id,
                })
                .then(function (response) {
                    console.log("Verified");
                    loaded = true;
                    return response.data.message;
                })
                .catch(function (error) {
                    console.log("Not verified");
                    return error.response.data.error;
                });
            this.loaded = loaded;
            console.log(result);
            return result;
        },
    },
};
</script>
