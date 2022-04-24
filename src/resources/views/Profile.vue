<template>
    <div>
        <div id="profile" class="profile-form">
            <h2 class="text-2xl text-blue-400 mx-auto mb-5">Edit Account Details</h2>
            <label for="profile-password">Old Password</label>
            <input
                type="password"
                name="profile-password"
                id="profile-password"
                placeholder="******"
                class="input"
            />
            <label for="profile-password-new">New Password</label>
            <input
                type="password"
                name="profile-password-new"
                id="profile-password-new"
                placeholder="******"
                class="input"
            />
            <label for="profile-password-confirm">Confirm Password</label>
            <input
                type="password"
                name="profile-password-confirm"
                id="profile-password-confirm"
                placeholder="******"
                class="input"
            />
            <button class="btn-save">Save</button>
            <button class="btn-deleteAccount">Delete Account</button>
        </div>
    </div>
</template>

<script>
export default {};
</script>