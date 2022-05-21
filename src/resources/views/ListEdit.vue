<template>
    <div>
        <button class="btn-create ml-3">Back to List</button>
        <h1 class="mt-6 text-4xl flex justify-center">List Edit</h1>
        <div class="w-full my-8 gap-24 flex-wrap flex justify-center">
            <div class="w-100 p-2 bg-white rounded-xl p-2">
                <p class="mb-3 text-2xl">Bearbeite:</p>
                <div class="flex">
                    <div>
                        <div class="mb-9 mt-1 ml-2">ListName</div>
                        <div class="mb-9 mt-1 ml-2">ListDescription</div>
                        <div class="mb-9 mt-1 ml-2">TransferList</div>
                    </div>
                    <div>
                        <div class="md:w-2/3 ml-20">
                            <input class="bg-gray-200 appearance-none border-2 border-gray-200 rounded w-full py-2 px-4 text-gray-700 leading-tight focus:outline-none focus:bg-white focus:border-blue-400" id="inline-full-name" type="text" value="'Listenname'">
                        </div>
                        <br />
                        <div class="md:w-2/3 ml-20">
                            <input class="bg-gray-200 appearance-none border-2 border-gray-200 rounded w-full py-2 px-4 text-gray-700 leading-tight focus:outline-none focus:bg-white focus:border-blue-400" id="inline-full-name" type="text" value="'Listenbeschreibung'">
                        </div>
                        <br />
                        <button class="btn-edit">Invite Member</button>
                    </div>
                </div>
                <br />
                <br />
                <br />
                <p class="flex justify-center">Do you really want to delete this list?</p>
                <button type="submit" class="btn-delete1 flex justify-content-center">Delete</button>
                <div class="md:w-2/3 ml-20">
                            <input class="bg-gray-200 appearance-none border-2 border-gray-200 rounded w-full py-2 px-4 text-gray-700 leading-tight focus:outline-none focus:bg-white focus:border-red-400" id="inline-full-name" type="text" value="'Password'">
                        </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {};
</script>
