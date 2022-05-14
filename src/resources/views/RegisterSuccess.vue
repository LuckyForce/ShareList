// resources/views/Verification.vue
<template>
    <div class="flex flex-col justify-center align-middle h-full">
        <p class="text-lg sm:text-5xl text-gray-700 text-center">
            You successfully registered! <br>
            Please check your emails to verify your account. <br>
            After that, you will be automatically logged in or you can <router-link to="/login" class="underline">Login</router-link> yourself manually.
        </p>
    </div>
</template>

<script>
export default {
    async mounted(){
        //Get Email from localStorage
        const email = window.localStorage.getItem("email");

        //Check if email is set
        if(email){
            //Check if user is already verified each second.
            const interval = setInterval(async () => {
                const result = await this.checkVerification(email);
                if(result){
                    clearInterval(interval);
                    this.$router.push("/lists");
                }
            }, 5000);
        }
    },
    methods:
    {
        checkVerification: async function(email) {
            //Axios request to verify user
            const result = await axios
                .post("/api/user/check", {
                    email: email,
                })
                .then(function (response) {
                    console.log(response.data.message);
                    return true;
                })
                .catch(function (error) {
                    console.log(error.response.data.error);
                    return false;
                });
            console.log(result);
            return result;
        }
    }
};
</script>
