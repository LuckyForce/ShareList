<template>
    <div>
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
        <h1 class="mt-6 text-4xl flex justify-center">List Invite</h1>

        <div class="flex flex-col w-full justify-center my-8">
            <div class="md:w-1/6 w-5/6 mx-auto flex flex-col">
                <input
                    class="border-2 border-gray-200 rounded py-2 px-4 text-gray-700 leading-tight focus:outline-none focus:bg-white focus:border-blue-400"
                    id="inviteInput"
                    type="text"
                    placeholder="E-Mail"
                    @keypress="createInvite($event)"
                    @input="inviteInput = $event.target.value"
                    :value="inviteInput"
                />
                <button
                    class="btn-create flex justify-center"
                    @click="createInvite()"
                >
                    {{ inviteButton }}
                </button>
            </div>
        </div>

        <div class="flex flex-col w-full justify-center my-8">
            <div class="md:w-2/6 w-4/6 mx-auto flex flex-col">
                <div
                    v-for="invite in invites"
                    :key="invite.in_id"
                    class="invite-card mb-1 flex mt-1"
                >
                    <div class="mr-auto">
                        <p class="ml-1 md:w-3/6 mr-auto text-lg mt-3">
                            {{ invite.u_email }}
                        </p>
                        <span class="ml-1 md:w-3/6 text-gray-500 text-sm mt-3">
                            {{ invite.in_created }}
                        </span>
                    </div>
                    <div class="flex justify-end">
                        <button
                            class="btn-delete1"
                            @click="deleteInvite(invite.in_id)"
                        >
                            Delete Invite
                        </button>
                    </div>
                </div>
                <span
                    v-if="invites.length === 0 && !loading"
                    class="text-center text-gray-500 text-4xl"
                >
                    No invites yet
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
    </div>
</template>

<script>
import { getToken } from "../js/utilities";

export default {
    data() {
        return {
            invites: [],
            inviteInput: "",
            inviteButton: "Invite User",
            loading: true,
        };
    },
    mounted() {
        console.log("ListInvites mounted");
        this.getInvites();
    },
    methods: {
        getInvites: async function () {
            //Clear invites
            this.loading = true;
            this.invites = [];

            //Get invites
            const token = await getToken();
            await axios
                .post("/api/list/invites", {
                    token: token,
                    list: this.$route.params.id,
                })
                .then((response) => {
                    this.invites = response.data.invites;
                })
                .catch((error) => {
                    console.log(error);
                });

            this.loading = false;
        },
        createInvite: async function ($event) {
            console.log("createInvite");
            //Check if event is undefined or enter key
            if ($event === undefined || $event.keyCode === 13) {
                //Check if input is empty
                if (this.inviteInput === "") {
                    console.log(this.inviteInput);
                    this.inviteButton = "Invite Field cannot be empty";
                    return;
                }

                //Check if input is a valid email
                const reg = /.+\@.+\..+/;
                if (!reg.test(this.inviteInput)) {
                    this.inviteButton = "Invalid E-Mail";
                    return;
                }

                this.inviteButton = "Inviting " + this.inviteInput;
                const email = this.inviteInput;

                const token = await getToken();

                await axios
                    .post("/api/list/invite", {
                        token: token,
                        list: this.$route.params.id,
                        email: email,
                    })
                    .then((response) => {
                        this.inviteButton = response.data.message;
                        this.inviteInput = "";
                        this.getInvites();
                    })
                    .catch((error) => {
                        this.inviteButton = error.response.data.error;
                    });
            } else this.inviteButton = "Invite User";
        },
        deleteInvite: async function (id) {
            const token = await getToken();
            const response = await axios
                .post("/api/list/invite/delete", {
                    token: token,
                    invite: id,
                })
                .then((response) => {
                    return response.data.message;
                })
                .catch((error) => {
                    return error.response.data.error;
                });

            console.log(response);
            this.getInvites();
        },
    },
};
</script>
