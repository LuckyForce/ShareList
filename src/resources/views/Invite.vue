// resources/views/Invite.vue
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
        const result = await this.checkInvite(this.$route.params.id);
        this.message = result;
    },
    methods: {
        checkInvite: async function (id) {
            //Fetch invite token from url
            console.log(id);
            //Axios request to verify user
            const result = await axios
                .post("/api/list/invite/accept", {
                    invite: id,
                })
                .then(function (response) {
                    this.loaded = true;
                    return response.data.message;
                })
                .catch(function (error) {
                    return error.response.data.error;
                });
            console.log(result);
            return result;
        },
    },
};
</script>
