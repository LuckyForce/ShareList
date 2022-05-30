<template>
    <div class="h-full flex flex-col">
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
        <div v-if="authorized && found">
            <h1 class="mt-6 text-4xl flex justify-center">Edit</h1>
            <div class="flex w-full flex-wrap justify-center my-8">
                <div class="bg-white rounded-xl p-2 my-0 mx-2">
                    <div>
                        <h2 class="mb-3 text-2xl text-center">
                            Edit Name or Description
                        </h2>
                        <div class="grid md:grid-cols-2 grid-cols-1 gap-y-4">
                            <label class="">List Name</label>
                            <div>
                                <input
                                    class="bg-gray-200 appearance-none border-2 border-gray-200 rounded w-full p-2 text-gray-700 leading-tight focus:outline-none focus:bg-white focus:border-blue-400"
                                    type="text"
                                    placeholder="List Name"
                                    :value="list.l_name"
                                    @input="list.l_name = $event.target.value"
                                />
                                <p
                                    v-if="l_name_invalid"
                                    class="w-full invalid-input"
                                >
                                    Invalid List Name
                                </p>
                            </div>
                            <label class="">List Description</label>
                            <div>
                                <textarea
                                    class="w-full block text-base font-normal text-gray-700 bg-gray-200 bg-clip-padding border border-solid border-gray-200 rounded transition ease-in-out focus:text-gray-700 focus:bg-white focus:border-blue-400 focus:outline-none"
                                    rows="3"
                                    placeholder="List Description"
                                    :value="list.l_description"
                                    @input="
                                        list.l_description = $event.target.value
                                    "
                                ></textarea>
                                <p
                                    v-if="l_description_invalid"
                                    class="w-full invalid-input"
                                >
                                    Invalid List Description
                                </p>
                            </div>
                        </div>
                        <button class="button my-2" @click="updateList()">
                            {{ changesButton }}
                        </button>
                    </div>

                    <div class="my-2">
                        <h2 class="mb-3 text-2xl text-center">Transfer List</h2>
                        <div class="grid md:grid-cols-2 grid-cols-1 gap-y-4">
                            <label for="member">Select Member:</label>
                            <div>
                                <select
                                    name="member"
                                    class="w-full text-gray-700 bg-gray-200 rounded py-1"
                                    v-model="member"
                                >
                                    <option selected>/</option>
                                    <option
                                        v-for="member in members"
                                        :value="member.id"
                                        :key="member.id"
                                    >
                                        {{ member.email }}
                                    </option>
                                </select>
                                <p
                                    v-if="no_member_selected"
                                    class="w-full invalid-input"
                                >
                                    No Member Selected
                                </p>
                            </div>
                            <label for="transfer-password">
                                Account Password:
                            </label>
                            <div>
                                <input
                                    name="transfer-password"
                                    class="bg-gray-200 appearance-none border-2 border-gray-200 rounded w-full p-2 text-gray-700 leading-tight focus:outline-none focus:bg-white focus:border-blue-400"
                                    id="transfer-password"
                                    type="password"
                                    placeholder="Password"
                                    v-model="transferPassword"
                                    @input="
                                        transferPasswordInvalid = false;
                                        transferButton = `Transfer List`;
                                    "
                                />
                                <p
                                    v-if="transferPasswordInvalid"
                                    class="w-full invalid-input"
                                >
                                    Invalid Password
                                </p>
                            </div>
                        </div>
                        <button class="button my-2" @click="transferList()">
                            {{ transferButton }}
                        </button>
                    </div>
                    <div>
                        <h2 class="mb-3 text-2xl text-center">Delete List</h2>
                        <p class="flex justify-center invalid-input text-center">
                            Do you really want to delete this list?<br />This
                            action is irreversible.<br />Items, accesses and invites
                            to this list will be permanently deleted.
                        </p>
                        <div class="md:w-2/3 w-full mx-auto mt-3">
                            <input
                                class="bg-gray-200 appearance-none border-2 border-gray-200 rounded w-full py-2 px-4 text-gray-700 leading-tight focus:outline-none focus:bg-white focus:border-red-400"
                                type="password"
                                placeholder="Password"
                                v-model="deletePassword"
                                @input="
                                    deletePasswordInvalid = false;
                                    deleteButton = `Delete List`;
                                "
                            />
                            <span
                                v-if="deletePasswordInvalid"
                                class="invalid-input"
                            >
                                Invalid Password
                            </span>
                            <button
                                type="submit"
                                class="btn-delete1 w-full flex justify-center"
                                @click="deleteList()"
                            >
                                Delete Permanently
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div
            v-if="!loading && !authorized"
            class="h-full flex justify-center items-center text-center sm:text-4xl text-lg text-gray-500"
        >
            You are not authorized to view this part of the list.
        </div>
        <div
            v-if="!loading && !found"
            class="h-full flex justify-center items-center text-center sm:text-4xl text-lg text-gray-500"
        >
            The list you are looking for does not exist.
        </div>
        <div
            v-if="loading && !authorized && !found"
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
    </div>
</template>

<script>
import { getToken } from "../js/utilities";

export default {
    data() {
        return {
            loading: true,
            authorized: false,
            found: false,
            list: {
                l_id: "",
                l_name: "",
                l_description: "",
                l_created: "",
                l_updated: "",
                l_user_id: "",
            },
            members: [],
            l_name_invalid: false,
            l_description_invalid: false,
            transferPasswordInvalid: false,
            no_member_selected: false,
            changesButton: "Save Changes",
            transferButton: "Transfer List",
            deletePasswordInvalid: false,
        };
    },
    props: {
        transferPassword: {
            type: String,
        },
        member: {
            type: String,
        },
        deletePassword: {
            type: String,
        },
    },
    async mounted() {
        await this.getList();
        await this.getMembers();
    },
    methods: {
        getList: async function () {
            const token = await getToken();
            const response = await axios
                .post("/api/list", {
                    token: token,
                    list: this.$route.params.id,
                })
                .then((response) => {
                    this.list = response.data.list;
                    this.found = true;
                    this.authorized = response.data.admin;
                })
                .catch((error) => {
                    console.log(error);
                    if (error.response.status === 404) {
                        this.found = false;
                        this.authorized = true;
                    }
                });

            this.loading = false;
        },
        getMembers: async function () {
            const token = await getToken();
            const response = await axios
                .post("/api/list/members", {
                    token: token,
                    list: this.$route.params.id,
                })
                .then((response) => {
                    this.members = response.data.members;
                })
                .catch((error) => {
                    console.log(error);
                });
        },
        updateList: async function () {
            // Check if list name is valid
            if (this.list.l_name.length < 1 || this.list.l_name.length > 20) {
                this.l_name_invalid = true;
            } else {
                this.l_name_invalid = false;
            }

            // Check if list description is valid
            if (
                this.list.l_description.length < 1 ||
                this.list.l_description.length > 255
            ) {
                this.l_description_invalid = true;
            } else {
                this.l_description_invalid = false;
            }

            // If list name and description are valid, update list
            if (!this.l_name_invalid && !this.l_description_invalid) {
                this.changesButton = "Saving Changes...";

                const token = await getToken();
                const response = await axios
                    .post("/api/list/update", {
                        token: token,
                        list: this.list.l_id,
                        name: this.list.l_name,
                        description: this.list.l_description,
                    })
                    .then((response) => {
                        this.changesButton = "Changes Saved";
                    })
                    .catch((error) => {
                        console.log(error);
                        this.changesButton = error.response.data.error;
                    });
            }
        },
        transferList: async function () {
            // Check if password is valid
            if (
                this.transferPassword === undefined ||
                this.transferPassword.length < 6
            ) {
                this.transferPasswordInvalid = true;
            } else {
                this.transferPasswordInvalid = false;
            }

            //Check if a member is selected
            if (this.member === undefined || this.member === "/") {
                this.no_member_selected = true;
            } else {
                this.no_member_selected = false;
            }

            console.log("Transferring list to " + this.member);

            // If password is valid, transfer list
            if (!this.transferPasswordInvalid) {
                this.transferButton = "Transferring List...";

                const token = await getToken();
                const response = await axios
                    .post("/api/list/transfer", {
                        token: token,
                        list: this.list.l_id,
                        password: this.transferPassword,
                        member: this.member,
                    })
                    .then((response) => {
                        //Redirect to list page
                        this.$router.push("/list/" + this.list.l_id);
                    })
                    .catch((error) => {
                        console.log(error);
                        if (error.response.data.error === "Invalid password") {
                            this.transferPasswordInvalid = true;
                        } else {
                            this.transferButton = error.response.data.error;
                        }
                    });
            }
        },
        deleteList: async function () {
            // Check if password is valid
            if (
                this.deletePassword === undefined ||
                this.deletePassword.length < 6
            ) {
                this.deletePasswordInvalid = true;
            } else {
                this.deletePasswordInvalid = false;
            }

            // If password is valid, delete list
            if (!this.deletePasswordInvalid) {
                this.transferButton = "Deleting List...";

                const token = await getToken();
                const response = await axios
                    .post("/api/list/delete", {
                        token: token,
                        list: this.list.l_id,
                        password: this.deletePassword,
                    })
                    .then((response) => {
                        //Redirect to lists
                        this.$router.push("/lists");
                    })
                    .catch((error) => {
                        console.log(error);
                        if (error.response.data.error === "Invalid password") {
                            this.deletePasswordInvalid = true;
                        } else {
                            this.transferButton = error.response.data.error;
                        }
                    });
            }
        },
    },
};
</script>
