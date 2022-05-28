<template>
    <div class="h-full sm:p-5 p-1">
        <div
            v-if="loading"
            class="h-full flex justify-center items-center text-center sm:text-4xl text-lg"
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
        <div
            v-if="!loading && authorized && found"
            class="h-full flex flex-col lg:w-1/2 lg:mx-auto"
        >
            <div v-if="!admin" class="flex justify-end">
                <button
                    @click="leaveList()"
                    class="btn-delete1"
                >
                    {{ leaveListButton }}
                </button>
            </div>
            <h1 class="text-4xl text-center">{{ list.l_name }}</h1>
            <p class="text-lg text-gray-600 text-center mb-4">
                {{ list.l_description }}
            </p>
            <div v-if="admin">
                <h2 class="text-2xl font-bold mb-1">Settings:</h2>
                <div class="grid xs:grid-cols-3 grid-cols-1 gap-5">
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
                        'item-card flex',
                    ]"
                >
                    <div class="mr-auto">
                        <h2 class="text-xl mb-2 ml-1">{{ item.i_content }}</h2>
                        <span>Last updated at: {{ item.i_lastupdated }}</span>
                    </div>
                    <div
                        class="xs:w-1/12 w-2/12 h-full flex justify-center items-center"
                        @click="checkItem(item.i_id)"
                    >
                        <!--Checked Button-->
                        <div
                            v-if="item.i_checked"
                            class="flex items-center justify-center"
                        >
                            <svg
                                class="w-full h-full text-green-500"
                                fill="currentColor"
                                viewBox="0 0 20 20"
                            >
                                <path
                                    fill-rule="evenodd"
                                    d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                    clip-rule="evenodd"
                                ></path>
                            </svg>
                        </div>
                        <!--Unchecked Button-->
                        <div v-else class="flex items-center justify-center">
                            <svg
                                class="w-full h-full text-gray-500"
                                fill="currentColor"
                                viewBox="0 0 20 20"
                            >
                                <path
                                    fill-rule="evenodd"
                                    d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                    clip-rule="evenodd"
                                ></path>
                            </svg>
                        </div>
                    </div>
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
                    class="input"
                    type="text"
                    placeholder="Item"
                    @keypress="keydownInput($event)"
                    @input="itemInput = $event.target.value"
                    :value="itemInput"
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
        <div
            v-if="!loading && !authorized"
            class="h-full flex justify-center items-center text-center sm:text-4xl text-lg text-gray-500"
        >
            You are not authorized to view this list.
        </div>
        <div
            v-if="!loading && !found"
            class="h-full flex justify-center items-center text-center sm:text-4xl text-lg text-gray-500"
        >
            The list you are looking for does not exist.
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
            authorized: false,
            found: false,
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
            itemInput: "",
            leaveListButton: "Leave List",
            leaveListButtonPressedOnce: false,
        };
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
                    this.authorized = true;
                    this.found = true;
                    console.log(response.data);
                    return true;
                })
                .catch((error) => {
                    console.log(error);
                    if (error.response.status === 401) {
                        this.authorized = false;
                        this.found = true;
                    } else if (error.response.status === 404) {
                        this.found = false;
                        this.authorized = true;
                    }
                    return false;
                });
            this.loading = false;
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
                    //const input = document.getElementById("itemInput");
                    //Clear input
                    //input.value = i_content;
                    //console.log(input.value);
                    this.itemInput = i_content;
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
            //const input = document.getElementById("itemInput");
            //console.log(input);
            //Clear input
            //input.value = "";
            this.itemInput = "";
        },
        checkItem: async function (i_id) {
            console.log(i_id);
            //Check if user has write permissions
            if (!this.write) return;

            //Set Item to checked
            this.items.forEach((item) => {
                if (item.i_id === i_id) {
                    item.i_checked = !item.i_checked;
                }
            });
            //Get Token
            const token = await getToken();
            const response = await axios
                .post("/api/list/item/check", {
                    token: token,
                    list: this.list.l_id,
                    item: i_id,
                })
                .then(async (response) => {
                    this.getList(this.list.l_id);
                    console.log(response.data);
                    return true;
                })
                .catch((error) => {
                    console.log(error);
                    return false;
                });
        },
        leaveList: async function () {
            //Check if button got pushed once before
            if (!this.leaveListButtonPressedOnce){
                this.leaveListButtonPressedOnce = true;
                this.leaveListButton = "Are you sure you want to leave this list?";
                return;
            }

            this.leaveListButton = "Leaving...";

            //Get Token
            const token = await getToken();
            const response = await axios
                .post("/api/list/leave", {
                    token: token,
                    list: this.list.l_id,
                })
                .then(async (response) => {
                    //Redirect to lists
                    this.$router.push("/lists");
                })
                .catch((error) => {
                    this.leaveListButtonPressedOnce = false;
                    this.leaveListButton = error.response.data.error;
                });
        },
    },
};
</script>
