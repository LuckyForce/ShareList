<template>
    <div class="h-full">
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
            <h1 class="mt-6 text-4xl flex justify-center">List Of Users</h1>

            <div class="flex flex-col w-full justify-center my-8">
                <div
                    class="md:w-3/6 w-full md:px-0 px-2 mx-auto flex flex-col mb-4"
                >
                    <div
                        v-for="member in members"
                        :key="member.id"
                        class="member-card my-1 flex"
                        @click="selectMember(member.id)"
                    >
                        <p
                            class="ml-1 md:w-3/6 mr-auto md:text-lg xs:text-base text-sm mt-1"
                        >
                            {{ member.email }}
                        </p>
                        <div v-if="member.write" class="m-auto">
                            <svg
                                class="w-4 h-4 fill-current text-gray-500"
                                viewBox="0 0 20 20"
                            >
                                <path
                                    d="M12.3 3.7l4 4L4 20H0v-4L12.3 3.7zm1.4-1.4L16 0l4 4-2.3 2.3-4-4z"
                                />
                            </svg>
                        </div>
                        <div class="mr-0 m-auto">
                            <svg
                                class="w-10 h-10 fill-current text-gray-500 border-2 border-gray-200"
                                viewBox="0 0 20 20"
                            >
                                <path
                                    v-if="selectedMembers.includes(member.id)"
                                    d="M10 8.586L2.929 1.515 1.515 2.929 8.586 10l-7.071 7.071 1.414 1.414L10 11.414l7.071 7.071 1.414-1.414L11.414 10l7.071-7.071-1.414-1.414L10 8.586z"
                                />
                            </svg>
                        </div>
                    </div>
                    <span
                        v-if="members.length === 0 && !loading"
                        class="text-center text-gray-500 text-4xl"
                    >
                        No members yet
                    </span>
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
                </div>
            </div>
            <div class="flex justify-center gap-x-4 px-4 my-4">
                <button
                    class="btn-create flex justify-center"
                    @click="addWriteAccesses()"
                >
                    {{ addWriteButton }}
                </button>
                <button
                    class="btn-create flex justify-center"
                    @click="removeWriteAccesses()"
                >
                    {{ removeWriteButton }}
                </button>
                <button
                    class="btn-delete1 flex justify-center"
                    @click="removeMembers()"
                >
                    {{ deleteButton }}
                </button>
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
            members: [],
            loading: true,
            found: false,
            authorized: false,
            selectedMembers: [],
            addWriteButton: "Add Write",
            removeWriteButton: "Remove Write",
            deleteButton: "Delete User",
            deleteButtonPushedOnce: false,
        };
    },
    async mounted() {
        await this.getMembers();
    },
    methods: {
        getMembers: async function () {
            this.members = [];
            this.loading = true;

            const token = await getToken();

            const response = await axios
                .post("/api/list/members", {
                    token: token,
                    list: this.$route.params.id,
                })
                .then((response) => {
                    this.members = response.data.members;
                    this.authorized = true;
                    this.found = true;
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
        },
        selectMember: async function (member) {
            console.log("Selecting: " + member);
            console.log(this.selectedMembers);
            if (this.selectedMembers.includes(member)) {
                this.selectedMembers = await this.selectedMembers.filter(
                    (m) => m !== member
                );
            } else {
                this.selectedMembers.push(member);
            }
            this.deleteButtonPushedOnce = false;
            this.deleteButton = "Delete User";
        },
        removeMembers: async function () {
            //Check if Delete button is pushed twice
            if (this.deleteButtonPushedOnce) {
                this.deleteButton = "Deleting...";

                const token = await getToken();

                //Go through all selected members and delete them
                await this.selectedMembers.forEach(async (member) => {
                    const response = await axios
                        .post("/api/list/member/remove", {
                            token: token,
                            list: this.$route.params.id,
                            member: member,
                        })
                        .then((response) => {
                            console.log(response.message);
                        })
                        .catch((error) => {
                            console.log(error.response.data.error);
                        });
                });

                this.selectedMembers = [];
                this.deleteButton = "Delete User";
                this.deleteButtonPushedOnce = false;
                await this.getMembers();
            } else {
                this.deleteButton = "Are you sure?";
                this.deleteButtonPushedOnce = true;
            }
        },
        removeWriteAccesses: async function () {
            this.removeWriteButton = "Removing...";

            const token = await getToken();

            //Go through all selected member and remove write access
            await this.selectedMembers.forEach(async (member) => {
                const response = await axios
                    .post("/api/list/member/write", {
                        token: token,
                        list: this.$route.params.id,
                        member: member,
                        write: false,
                    })
                    .then((response) => {
                        console.log(response.message);
                    })
                    .catch((error) => {
                        console.log(error.response.data.error);
                    });
            });

            this.selectedMembers = [];
            this.removeWriteButton = "Remove Write";
            await this.getMembers();
        },
        addWriteAccesses: async function () {
            this.addWriteButton = "Adding...";

            const token = await getToken();

            //Go through all selected members and add write access
            await this.selectedMembers.forEach((member) => {
                axios
                    .post("/api/list/member/write", {
                        token: token,
                        list: this.$route.params.id,
                        member: member,
                        write: true,
                    })
                    .then((response) => {
                        console.log(response.message);
                    })
                    .catch((error) => {
                        console.log(error.response.data.error);
                    });
            });
            this.selectedMembers = [];
            this.addWriteButton = "Add Write";
            await this.getMembers();
        },
    },
};
</script>
