// resources/views/Verification.vue
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
        const result = await checkVerification(this.$route.params.id, this.$route.params.token);
        this.message = result;
    }
};

async function checkVerification(id, token) {
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
            return response.data.message;
        })
        .catch(function (error) {
            return error.response.data.error;
        });
    console.log(result);
    return result;
}
</script>