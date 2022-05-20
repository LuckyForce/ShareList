<template>
    <div class="h-full sm:p-5 p-1">
        <div
            v-if="loading"
            class="h-full flex justify-center items-center text-center sm:text-4xl text-lg"
        >
            <div class="spinner"></div>
        </div>
        <div v-else class="h-full flex flex-col">
            <h1 class="text-4xl text-center">{{ list.l_name }}</h1>
            <p class="text-lg text-gray-600 text-center">
                {{ list.l_description }}
            </p>
            <div v-if="admin">
                <h2 class="text-2xl font-bold mb-1">Settings:</h2>
                <div class="flex gap-x-5">
                    <router-link
                        :to="{
                            name: 'ListEdit',
                            params: { id: $route.params.id },
                        }"
                        class="button"
                    >
                        Edit List
                    </router-link>
                    <router-link
                        :to="{
                            name: 'ListMembers',
                            params: { id: $route.params.id },
                        }"
                        class="button"
                    >
                        Members
                    </router-link>
                    <router-link
                        :to="{
                            name: 'ListInvites',
                            params: { id: $route.params.id },
                        }"
                        class="button"
                    >
                        Invites
                    </router-link>
                </div>
                <hr class="my-5" />
            </div>
            <div class="flex flex-col justify-center gap-y-2 mb-4">
                <div
                    v-for="item in items"
                    :key="item.i_id"
                    @click="selectItem(item.i_id, item.i_content)"
                    :class="[
                        item.i_id === selectedItem ? 'itemSelected' : '',
                        'item-card',
                    ]"
                >
                    <h2 class="text-xl mb-2 ml-1">{{ item.i_content }}</h2>
                    <span>Last updated at: {{ item.i_lastupdated }}</span>
                </div>
            </div>
            <div
                v-if="items.length === 0"
                class="text-sm text-gray-600 text-center mb-4"
            >
                There are no items in this list yet.
            </div>
            <div v-if="write" class="mt-auto flex flex-col gap-y-2">
                <div class="text-gray-500 text-lg">
                    <span v-if="selectedItem === null">New Item</span>
                    <span v-else>Changing: {{ selectedItem }}</span>
                </div>
                <input
                    id="itemInput"
                    v-model="itemInput"
                    class="input"
                    type="text"
                    placeholder="Item"
                    @keypress="keydownInput($event)"
                />
                <button class="button text-2xl w-full" @click="enterButton()">
                    <span v-if="selectedItem === null">Add</span>
                    <span v-else>Update</span>
                </button>
                <button class="button text-2xl w-full" @click="deleteItem()">
                    <span v-if="selectedItem === null">Cancel</span>
                    <span v-else>Delete</span>
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
            onPage: true,
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
            selectedItem: null,
        };
    },
    props: {
        itemInput: {
            type: String,
        },
    },
    async mounted() {
        await this.loadList();
    },
    watch: {
        async $route(to, from) {
            this.onPage = false;
        },
    },
    methods: {
        loadList: async function () {
            //Get id of list
            const listId = this.$route.params.id;

            // Get list every 5 seconds per post request but get the first one immediately
            this.getList(listId);
            const interval = setInterval(async () => {
                if (!this.onPage) {
                    // Stop the interval if the user navigates away
                    clearInterval(interval);
                }
                this.getList(listId);
            }, 10000);
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
        selectItem: async function (i_id, i_content) {
            //Check if the user has write permissions if so allow them to edit the item
            if (this.write) {
                //Check if selected Item is the same as the already selected. In this case deselect the Item.
                if (i_id === this.selectedItem) {
                    //Remove Content of input and set selectedItem null
                    this.selectedItem = null;
                    await this.clearInput();
                } else {
                    //Set selectedItem and fill input with content.
                    this.selectedItem = i_id;
                    //Get input
                    const input = document.getElementById("itemInput");
                    //Clear input
                    input.value = i_content;
                }
            }
        },
        keydownInput: async function (event) {
            //Check if enter is pressed
            if (event.keyCode === 13) {
                await this.enterButton();
            }
        },
        enterButton: async function () {
            //Check if selectedItem is set
            if (this.selectedItem !== null) {
                //Update Item
                await this.updateItem();
            } else {
                //Create Item
                await this.addItem();
            }
        },
        addItem: async function () {
            //Check if input is empty
            if (
                this.itemInput === "" ||
                this.itemInput === null ||
                this.itemInput === undefined
            ) {
                return;
            }

            //Push new item to items. THIS IS ONLY FOR LOADING REASONS
            this.items.push({
                i_l_id: this.list.l_id,
                i_id: null,
                i_content: this.itemInput,
                i_lastupdated: "Loading...",
                i_checked: false,
            });

            //Get Token
            const token = await getToken();

            const response = await axios
                .post("/api/list/item/add", {
                    token: token,
                    list: this.list.l_id,
                    content: this.itemInput,
                })
                .then(async (response) => {
                    await this.clearInput();
                    this.getList(this.list.l_id);
                    console.log(response.data);
                    return true;
                })
                .catch((error) => {
                    console.log(error);
                    return false;
                });
            console.log(response);
        },
        updateItem: async function () {
            //Check if input is empty if so delete item
            if (
                this.itemInput === "" ||
                this.itemInput === null ||
                this.itemInput === undefined
            ) {
                this.deleteItem();
                return;
            }

            //Update Item. THIS IS ONLY FOR LOADING REASONS
            this.items.forEach((item) => {
                if (item.i_id === this.selectedItem) {
                    item.i_content = this.itemInput;
                }
            });

            //Get Token
            const token = await getToken();

            const response = await axios
                .post("/api/list/item/update", {
                    token: token,
                    list: this.list.l_id,
                    item: this.selectedItem,
                    content: this.itemInput,
                })
                .then(async (response) => {
                    await this.clearInput();
                    this.selectedItem = null;
                    this.getList(this.list.l_id);
                    console.log(response.data);
                    return true;
                })
                .catch((error) => {
                    console.log(error);
                    return false;
                });
            console.log(response);
        },
        deleteItem: async function () {
            //Check if item is selected
            if (this.selectedItem === null) {
                await this.clearInput();
            }

            //Delete Item. THIS IS ONLY FOR LOADING REASONS
            this.items.forEach((item, index) => {
                if (item.i_id === this.selectedItem) {
                    this.items.splice(index, 1);
                }
            });

            //Get Token
            const token = await getToken();

            const response = await axios
                .post("/api/list/item/delete", {
                    token: token,
                    list: this.list.l_id,
                    item: this.selectedItem,
                })
                .then(async (response) => {
                    await this.clearInput();
                    this.selectedItem = null;
                    this.getList(this.list.l_id);
                    console.log(response.data);
                    return true;
                })
                .catch((error) => {
                    console.log(error);
                    return false;
                });
            console.log(response);
        },
        clearInput: async function () {
            //Props are readonly so we cant use this.itemInput = ""
            //Get input
            const input = document.getElementById("itemInput");
            console.log(input);
            //Clear input
            input.value = "";
        },
    },
};
</script>
