<template>
    <div class="flex flex-col">
        <div class="flex m-4">
            <router-link
                class="button"
                :to="{
                    name: 'List',
                    params: {
                        id: $route.params.id,
                    },
                }"
            >
                Back to list
            </router-link>
        </div>
        <h1 class="mt-6 text-4xl flex justify-center">List Edit</h1>
        <div class="flex w-full flex-wrap justify-center my-8">
            <div class="bg-white rounded-xl p-2 my-0">
                <p class="mb-3 text-2xl">Edit:</p>
                <div class="grid md:grid-cols-2 grid-cols-1 gap-y-4">
                    <label class="">List Name</label>
                    <input
                        class="bg-gray-200 appearance-none border-2 border-gray-200 rounded w-full py-2 px-4 text-gray-700 leading-tight focus:outline-none focus:bg-white focus:border-blue-400"
                        id="inline-full-name"
                        type="text"
                        value="List Name"
                    />
                    <label class="">List Description</label>
                    <textarea
                        class="block text-base font-normal text-gray-700 bg-gray-200 bg-clip-padding border border-solid border-gray-200 rounded transition ease-in-out m-0 focus:text-gray-700 focus:bg-white focus:border-blue-400 focus:outline-none"
                        id="exampleFormControlTextarea1"
                        rows="3"
                        placeholder="List Description"
                    ></textarea>
                    <label class="">Transfer List</label>
                    <select
                        name="user"
                        id="userListEdit"
                        class="text-gray-700 bg-gray-200 rounded py-1"
                    >
                        <option value="KeinUser">/</option>
                        <option value="user1">User1</option>
                        <option value="user2">User2</option>
                        <option value="user3">User3</option>
                    </select>
                </div>
                <p class="flex justify-center">
                    Do you really want to delete this list?
                </p>
                <div class="md:w-2/3 ml-20 mt-3">
                    <input
                        class="bg-gray-200 appearance-none border-2 border-gray-200 rounded w-full py-2 px-4 text-gray-700 leading-tight focus:outline-none focus:bg-white focus:border-red-400"
                        id="inline-full-name"
                        type="text"
                        value="'Password'"
                    />
                </div>
                <div class="flex justify-center">
                    <button
                        type="submit"
                        class="btn-delete1 flex justify-center"
                    >
                        Delete
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import {getToken} from '../js/utilities';

export default {
    data() {
        return {
        };
    },
    mounted() {
        this.getList();
    },
    methods: {
        getList: async function () {

        },
        updateList: async function () {

        },
        deleteList: async function () {

        },
        transferList: async function () {

        },
    },
};
</script>
