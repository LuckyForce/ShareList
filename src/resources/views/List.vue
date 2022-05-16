<template>
    <div class="h-full sm:p-5 p-1">
        <div
            v-if="loading"
            class="h-full flex justify-center items-center text-center sm:text-4xl text-lg"
        >
            List Loading...
        </div>
        <div v-else>
            <div v-if="admin">
                I am Admin
            </div>
            <h1 class="text-4xl text-center">{{ list.l_name }}</h1>
            <p class="text-lg text-gray-600 text-center">
                {{ list.l_description }}
            </p>
            <div
                v-for="item in items"
                :key="item.i_id"
                class="flex flex-col sm:p-5 p-1 gap-y-2"
            >
                <h2 class="text-xl mb-2 ml-1">{{ item.i_content }}</h2>
                <span>Last updated at: {{ item.i_lastupdated }}</span>
            </div>
            <div v-if="items.length === 0" class="text-sm text-gray-600 text-center">
                There are no items in this list yet.
            </div>
            <div v-if="write" class="mt-auto">
                <div class="flex justify-center">
                    <input
                        v-model="newItem"
                        class="w-full sm:w-1/2 p-1"
                        type="text"
                        placeholder="New item"
                    />
                    <button
                        @click="addItem"
                        class="w-full sm:w-1/2 p-1"
                        type="button"
                    >
                        Add
                    </button>
                </div>
            </div>
        </div>

        <div
            class="w-full my-8 gap-24 flex-wrap flex justify-center items-center hidden"
        >
            <div class="w-80 p-2 bg-white rounded-xl p-2">
                <p class="text-sm text-gray-600 mb-2 ml-1">
                    Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed
                    diam nonumy eirmod tempor invidunt ut labore et dolore magna
                    aliquyam
                </p>
                <button type="submit" class="btn-delete1">Delete</button>
                <button type="submit" class="btn-edit1">Show</button>
            </div>
            <input
                class="form-check-input appearance-none h-6 w-6 border border-gray-300 rounded-sm bg-white checked:bg-blue-600 checked:border-blue-600 focus:outline-none transition duration-200 mt-1 align-top bg-no-repeat bg-center bg-contain float-left mr-2 cursor-pointer"
                type="checkbox"
                value=""
                id="flexCheckDefault"
            />
            <label
                class="form-check-label inline-block text-gray-800"
                for="flexCheckDefault"
            >
            </label>
        </div>

        <div
            class="w-full my-8 gap-24 flex-wrap flex justify-center items-center hidden"
        >
            <div class="w-80 p-2 bg-white rounded-xl p-2">
                <p class="text-sm text-gray-600 mb-2 ml-1">
                    Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed
                    diam nonumy eirmod tempor invidunt ut labore et dolore magna
                    aliquyam
                </p>
                <button type="submit" class="btn-delete1">Delete</button>
                <button type="submit" class="btn-edit1">Show</button>
            </div>
            <input
                class="form-check-input appearance-none h-6 w-6 border border-gray-300 rounded-sm bg-white checked:bg-blue-600 checked:border-blue-600 focus:outline-none transition duration-200 mt-1 align-top bg-no-repeat bg-center bg-contain float-left mr-2 cursor-pointer"
                type="checkbox"
                value=""
                id="flexCheckDefault"
            />
            <label
                class="form-check-label inline-block text-gray-800"
                for="flexCheckDefault"
            >
            </label>
        </div>
        <div
            class="w-full my-8 gap-24 flex-wrap flex justify-center items-center hidden"
        >
            <div class="w-80 p-2 bg-white rounded-xl p-2">
                <p class="text-sm text-gray-600 mb-2 ml-1">
                    Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed
                    diam nonumy eirmod tempor invidunt ut labore et dolore magna
                    aliquyam
                </p>
                <button type="submit" class="btn-delete1">Delete</button>
                <button type="submit" class="btn-edit1">Show</button>
            </div>
            <input
                class="form-check-input appearance-none h-6 w-6 border border-gray-300 rounded-sm bg-white checked:bg-blue-600 checked:border-blue-600 focus:outline-none transition duration-200 mt-1 align-top bg-no-repeat bg-center bg-contain float-left mr-2 cursor-pointer"
                type="checkbox"
                value=""
                id="flexCheckDefault"
            />
            <label
                class="form-check-label inline-block text-gray-800"
                for="flexCheckDefault"
            >
            </label>
        </div>

        <div
            class="w-full my-8 gap-24 flex-wrap flex justify-center items-center hidden"
        >
            <div class="w-80 p-2 bg-white rounded-xl p-2">
                <p class="text-sm text-gray-600 mb-2 ml-1">
                    Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed
                    diam nonumy eirmod tempor invidunt ut labore et dolore magna
                    aliquyam
                </p>
                <button type="submit" class="btn-delete1">Post</button>
            </div>
        </div>
    </div>
</template>

<script>
import { getToken } from "../js/utilities";
export default {
    data() {
        return {
            loading: true,
            list: {
                l_created: "Loading...",
                l_description: "Loading...",
                l_id: "",
                l_name: "Loading...",
                l_u_id: null,
            },
            items: [],
            admin: false,
            write: false,
        };
    },
    mounted() {
        this.loadList();
    },
    methods: {
        loadList: async function () {
            //Get id of list
            const listId = this.$route.params.id;

            // Get list every 5 seconds per post request but get the first one immediately
            this.getList(listId);
            setInterval(async () => {
                this.getList(listId);
            }, 5000);
        },
        getList: async function (listId) {
            //Get Token
            const token = await getToken();

            const response = await axios
                .post("/api/list", {
                    token: token,
                    list: listId,
                })
                .then((response) => {
                    this.list = response.data.list;
                    this.items = response.data.items;
                    this.admin = response.data.admin;
                    this.write = response.data.write;
                    this.loading = false;
                    console.log(response.data);
                    return true;
                })
                .catch((error) => {
                    console.log(error);
                    return false;
                });
            console.log(response);
        },
    },
};
</script>
