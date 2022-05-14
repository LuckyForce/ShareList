// resources/views/Invite.vue
<template>
    <div class="flex flex-col justify-center align-middle h-full">
        <p class="text-xl sm:text-5xl text-gray-700 text-center">
            {{ message }}
        </p>
    </div>
</template>

<script>
export default {
    data() {
        return {
            message: "Loading...",
        };
    },
    async mounted(){
        const result = await checkInvite(this.$route.params.id);
        this.message = result;
    }
};

async function checkInvite(id) {
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
}
</script>