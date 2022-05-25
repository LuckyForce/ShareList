<template>
    <div>
        <button class="btn-create ml-3">Back to List</button>
        <h1 class="mt-6 text-4xl flex justify-center">List Invite</h1>

        <div class="flex flex-col w-full justify-center my-8">
            <div class="md:w-1/6 w-5/6 mx-auto flex flex-col">
                <input
                    class="border-2 border-gray-200 rounded py-2 px-4 text-gray-700 leading-tight focus:outline-none focus:bg-white focus:border-blue-400"
                    id="inline-full-name"
                    type="text"
                    placeholder="Email"
                />
                <button class="btn-create flex justify-center">
                    Invite User
                </button>
            </div>
        </div>

        <div class="flex flex-col w-full justify-center my-8">
            <div class="md:w-2/6 w-4/6 mx-auto flex flex-col">
                <div>
                    <div
                        v-for="invite in invites"
                        :key="invite.in_id"
                        class="list-card mb-1 flex mt-1"
                        @click="this.getList(list.l_id)"
                    >
                        <p class="ml-1 md:w-3/6 mr-auto text-lg mt-3">
                            {{ invite.in_email }}
                        </p>
                        <button class="btn-delete1 flex justify-center ml-auto">
                          Delete User
                        </button>
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
                },
                {
                    in_id: 2,
                    in_email: "kkej@gmail.com",
                },
                {
                    in_id: 3,
                    in_email: "iohn@gmailc.com",
                },
            ],
        };
    },
    mounted() {
        console.log(this.invites);
    },
};
</script>
