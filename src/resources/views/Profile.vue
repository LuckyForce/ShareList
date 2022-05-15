<template>
    <div class="h-full flex justify-center p-5">
        <div class="my-auto flex flex-col gap-y-5">
            <h1 class="text-4xl mx-auto font-bold">
                Edit Account Details
            </h1>
            <button class="button" @click="logout">Logout</button>
            <div id="change-password" class="w-full profile-form">
                <h2 class="text-2xl text-blue-400 mx-auto mb-5">
                    Change Password
                </h2>
                <label for="profile-password-old">Old Password</label>
                <input
                    type="password"
                    name="profile-password-old"
                    id="profile-password-old"
                    placeholder="******"
                    class="input"
                    v-model="profilePasswordOld"
                />
                <div class="invalid-input">
                    <span v-if="pwdWrong"> Password is incorrect </span>
                </div>
                <label for="profile-password-new">New Password</label>
                <input
                    type="password"
                    name="profile-password-new"
                    id="profile-password-new"
                    placeholder="******"
                    class="input"
                    v-model="profilePasswordNew"
                />
                <div class="invalid-input">
                    <span v-if="pwdTooShort">
                        Password must be at least 6 characters long
                    </span>
                    <span v-if="pwdTooLong">
                        Password must be at most 20 characters long
                    </span>
                </div>
                <label for="profile-password-confirm"
                    >Confirm New Password</label
                >
                <input
                    type="password"
                    name="profile-password-confirm"
                    id="profile-password-confirm"
                    placeholder="******"
                    class="input"
                    v-model="profilePasswordConfirm"
                />
                <span class="invalid-input" v-if="pwdConfirmWrong">
                    Passwords do not match
                </span>
                <button class="btn-save" @click="changePassword">
                    {{ changePasswordButton }}
                </button>
            </div>
            <div id="change-password" class="w-full profile-form">
                <h2 class="text-2xl text-red-500 mx-auto mb-5">
                    Delete Account
                </h2>
                <p class="text-red-500 italic">
                    Beim Löschen des Accounts werden sämtliche Daten wie die persönlichen Daten des Nutzers und seine Listen gelöscht.
                </p>
                <label for="delete-password">Password</label>
                <input
                    type="password"
                    name="delete-password"
                    id="delete-password"
                    placeholder="******"
                    class="input"
                    v-model="deletePassword"
                />
                <span class="invalid-input" v-if="deleteWrong">
                    Password is incorrect
                </span>
                <button class="btn-deleteAccount" @click="deleteAccount">
                    {{ deleteAccountButton }}
                </button>
            </div>
        </div>
    </div>
</template>

<script>
import { getToken, mainLogout } from "../js/utilities";
export default {
    data() {
        return {
            changePasswordButton: "Change Password",
            pwdWrong: false,
            pwdTooShort: false,
            pwdTooLong: false,
            pwdConfirmWrong: false,
            deleteAccountButton: "Delete Account",
            deleteWrong: false,
        };
    },
    props: {
        profilePasswordOld: {
            type: String,
        },
        profilePasswordNew: {
            type: String,
        },
        profilePasswordConfirm: {
            type: String,
        },
        deletePassword: {
            type: String,
        },
    },
    methods: {
        logout: async function () {
            await mainLogout(this.$router);
        },
        changePassword: async function () {
            //Disable Button
            this.changePasswordButton = "Changing...";
            this.pwdWrong = false;
            this.pwdTooShort = false;
            this.pwdTooLong = false;
            this.pwdConfirmWrong = false;

            //Check if password is correct
            if (
                this.profilePasswordOld === undefined ||
                this.profilePasswordOld === ""
            ) {
                this.pwdWrong = true;
                this.changePasswordButton = "Change Password";
                return;
            }

            //Check if password is too short
            if (
                this.profilePasswordNew === undefined ||
                this.profilePasswordNew.length < 6
            ) {
                this.pwdTooShort = true;
                this.changePasswordButton = "Change Password";
                return;
            }

            //Check if password is too long
            else if (this.profilePasswordNew.length > 20) {
                this.pwdTooLong = true;
                this.changePasswordButton = "Change Password";
                return;
            }

            //Check if passwords match
            if (this.profilePasswordNew !== this.profilePasswordConfirm) {
                this.pwdConfirmWrong = true;
                this.changePasswordButton = "Change Password";
                return;
            }

            //Get Token
            const token = await getToken(this.$router);
            console.log(token);

            //If no errors, change password
            //Change password
            //Send request
            await axios
                .post("/api/user/changepwd", {
                    token: token,
                    oldPassword: this.profilePasswordOld,
                    newPassword: this.profilePasswordNew,
                })
                .then((response) => {
                    this.changePasswordButton = response.data.message;
                    //Set new password in local storage
                    window.localStorage.setItem("pwd", this.profilePasswordNew);
                })
                .catch((error) => {
                    console.log(error.response.data);
                    //If error
                    if (error.response.status === 401) {
                        //If wrong password
                        this.pwdWrong = true;
                        this.changePasswordButton = "Change Password";
                    } else {
                        this.changePasswordButton = error.response.data.error;
                    }
                });
        },
        deleteAccount: async function () {
            //Disable Button
            this.deleteAccountButton = "Deleting...";
            this.deleteWrong = false;

            //Check if password is correct
            if (
                this.deletePassword === undefined ||
                this.deletePassword === ""
            ) {
                this.deleteWrong = true;
                this.deleteAccountButton = "Delete Account";
                return;
            }

            //Test
            console.log(this.deletePassword);
            //Get Token
            const token = await getToken(this.$router);
            console.log(token);

            //If no errors, delete account
            //Delete account
            //Send request
            await axios
                .post("/api/user/delete", {
                    token: token,
                    password: this.deletePassword,
                })
                .then(async (response) => {
                    //Logout
                    await mainLogout(this.$router);
                })
                .catch((error) => {
                    console.log(error.response.data);
                    //If error
                    if (error.response.status === 401) {
                        //If wrong password
                        this.deleteWrong = true;
                        this.deleteAccountButton = "Delete Account";
                    } else {
                        this.deleteAccountButton = "Something went wrong! Please try again.";
                    }
                });
        },
    },
};
</script>
