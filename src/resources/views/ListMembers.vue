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
        <h1 class="mt-6 text-4xl flex justify-center">List Of Users</h1>

        <div class="flex flex-col w-full justify-center my-8">
            <div class="md:w-2/6 w-4/6 mx-auto flex flex-col">
                <div
                    v-for="invite in invites"
                    :key="invite.in_id"
                    class="list-card mb-1 flex mt-1"
                    @click="this.getList(list.in_id)"
                >
                    <p class="ml-1 md:w-3/6 mr-auto text-lg mt-1">
                        {{ invite.in_email }}
                    </p>
                    <div
                        v-if="invite.write"
                        class="m-auto">
                        <svg
                            class="w-4 h-4 fill-current text-gray-500"
                            viewBox="0 0 20 20"
                        >
                            <path
                                d="M12.3 3.7l4 4L4 20H0v-4L12.3 3.7zm1.4-1.4L16 0l4 4-2.3 2.3-4-4z"
                            />
                        </svg>
                    </div>
                    <div class="flex mt-auto mb-auto">
                        <input type="checkbox" id="choose" value="HTML" />
                    </div>
                </div>
            </div>
            <div class="flex justify-center space-x-4">
                <button class="btn-create flex justify-center">
                    Add Write
                </button>
                <button class="btn-create flex justify-center">
                    Remove Write
                </button>
                <button class="btn-delete1 flex justify-center">
                    Delete User
                </button>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            invites: [
                {
                    in_id: 1,
                    in_email: "hgjdsk@gmail.com",
                    in_created: "2020-05-05",
                    write: true,
                },
                {
                    in_id: 2,
                    in_email: "kkej@gmail.com",
                    in_created: "2020-05-05",
                    write: false,
                },
                {
                    in_id: 3,
                    in_email: "iohn@gmailc.com",
                    in_created: "2020-05-05",
                    write: true,
                },
            ],
        };
    },
    async mounted() {
        this.getMembers();
    },
    methods: {
        getMembers: async function () {},
        removeMembers: async function () {},
        removeWriteAccesses: async function () {},
        addWriteAccesses: async function () {},
    },
};
</script>
