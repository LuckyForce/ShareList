<template>
    <div>
        <h1 class="mt-6 sm:text-4xl text-lg flex justify-center">
            Your current Lists
        </h1>
        <div class="lg:px-24 md:px-12 sm:px-6 px-1">
            <!--TODO: Design List Card-->
            <div class="flex flex-col sm:p-5 p-1 gap-y-2">
                <span
                    v-if="!loaded"
                    class="text-center text-gray-600 sm:text-2xl text-lg"
                >
                    Loading...
                </span>
                <p
                    v-if="!lists.length && loaded"
                    class="text-center text-gray-600 sm:text-2xl text-lg"
                >
                    No lists yet? Create one by clicking the button below!
                </p>
                <div
                    v-for="list in lists"
                    :key="list.l_id"
                    class="list-card"
                    @click="this.getList(list.l_id)"
                >
                    <h2 class="text-xl mb-2 ml-1">{{ list.l_name }}</h2>
                    <p class="text-sm text-gray-600 mb-2 ml-1">
                        {{ list.l_description }}
                    </p>
                    <span>Created at: {{ list.l_created }}</span>
                </div>
            </div>
            <div class="sm:p-5 p-1">
                <button
                    type="submit"
                    class="btn-create text-2xl w-full"
                    @click="this.createList()"
                    :disabled="createListButtonDisabled"
                >
                    {{ this.createListButton }}
                </button>
            </div>
        </div>
    </div>
</template>

<script>
import { getToken } from "../js/utilities";

export default {
    data() {
        return {
            lists: [],
            createListButton: "Create List",
            createListButtonDisabled: false,
            loaded: false,
        };
    },
    mounted() {
        this.getLists();
    },
    methods: {
        getList: async function (id) {
            const link = `/list/${id}`;
            //Redirect to list
            this.$router.push(link);
        },
        getLists: async function () {
            //Get Token
            const token = await getToken();

            //If token is false
            if (token === false) {
                this.$router.push("/");
            }

            //Get the lists from the api per post request
            await axios
                .post("/api/lists", {
                    token: token,
                })
                .then((response) => {
                    console.log(response.data);
                    this.loaded = true;
                    this.lists = response.data.lists;
                })
                .catch((error) => {
                    console.log(error);
                });
        },
        createList: async function () {
            //Set the button to loading
            this.createListButton = "Loading...";
            //Disable the button
            this.createListButtonDisabled = true;

            //Get Token
            const token = await getToken();

            //If token is false
            if (token === false) {
                this.$router.push("/");
            }

            let success = false;

            //Get the lists from the api per post request
            const response = await axios
                .post("/api/list/create", {
                    token: token,
                })
                .then((response) => {
                    console.log(response.data);
                    success = true;
                    return response.data;
                })
                .catch((error) => {
                    console.log(error);
                    return error.response.data.error;
                });

            //If the list was created successfully redirect to the list
            if (success) {
                this.$router.push(`/list/${response.list}`);
            } else {
                this.createListButton = response;
            }
        },
    },
};
</script>
