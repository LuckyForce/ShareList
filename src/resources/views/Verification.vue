// resources/views/Verification.vue
<template>
    <div class="flex flex-col justify-center align-middle h-full">
        <div
            v-if="!loaded"
            class="text-xl sm:text-5xl text-gray-700 text-center"
        >
            <div class="lds-spinner">
                <div></div>
                <div></div>
                <div></div>
                <div></div>
                <div></div>
                <div></div>
                <div></div>
                <div></div>
                <div></div>
                <div></div>
                <div></div>
                <div></div>
            </div>
        </div>
        <div v-if="loaded">
            <p class="text-xl sm:text-5xl text-gray-700 text-center">
                {{ message }}
            </p>
            <p class="text-lg sm:text-2xl text-gray-700 text-center">
                You can close this Window now.
            </p>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            message: "",
            loaded: false,
        };
    },
    async mounted() {
        const result = await this.checkVerification(
            this.$route.params.id,
            this.$route.params.token
        );
        this.message = result;
        this.loaded = true;
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
