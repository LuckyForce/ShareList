// resources/views/Invite.vue
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
        const result = await this.checkInvite(this.$route.params.id);
        this.message = result;
        this.loaded = true;
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
