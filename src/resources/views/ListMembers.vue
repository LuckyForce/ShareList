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
                    <p class="ml-1 md:w-3/6 mr-auto text-lg mt-3">
                        {{ invite.in_email }}
                    </p>
                    <div class="flex">
                    <input type="checkbox" id="choose" value="HTML">
                    </div>
                </div>
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
                },
                {
                    in_id: 2,
                    in_email: "kkej@gmail.com",
                    in_created: "2020-05-05",
                },
                {
                    in_id: 3,
                    in_email: "iohn@gmailc.com",
                    in_created: "2020-05-05",
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
