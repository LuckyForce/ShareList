<template>
    <div>
        <h1 class="mt-6 text-4xl flex justify-center">List Edit</h1>
        <div class="w-full my-8 gap-24 flex-wrap flex justify-center">
            <div class="w-100 p-2 bg-white rounded-xl p-2">
                <p class="mb-3 text-2xl">Bearbeite:</p>
                <div class="flex">
                    <div>
                        <div class="mb-9 mt-1">ListName</div>
                        <div class="mb-9 mt-1">Inhalt 1</div>
                        <div class="mb-9 mt-1">Inhalt 2</div>
                        <div class="mb-9 mt-1">Inhalt 3</div>
                    </div>
                    <div>
                        <button class="btn-edit">Bearbeiten</button>
                        <br />
                        <button class="btn-edit">Bearbeiten</button>
                        <br />
                        <button class="btn-edit">Bearbeiten</button>
                        <br />
                        <button class="btn-edit">Bearbeiten</button>
                    </div>
                </div>
                <br />
                <br />
                <br />
                <p>Willst du diese Liste wirklich löschen?</p>
                <button type="submit" class="btn-delete1">Delete</button>
            </div>
        </div>
    </div>
</template>

<script>
export default {};
</script>
