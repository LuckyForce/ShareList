<template>
    <div class="h-full flex justify-center p-5">
        <div v-if="!loggedIn" id="register" class="register-form">
            <h2 class="text-2xl text-blue-400 mx-auto mb-5">Register</h2>
            <label for="register-email">E-Mail</label>
            <input
                type="text"
                name="register-email"
                id="register-email"
                placeholder="max.mustermann@gmail.com"
                class="input"
                v-model="email"
            />
            <div class="invalid-input">
                <span v-if="emailValid === false">
                    Please enter a valid email address
                </span>
                <span v-if="emailTaken === true">
                    This email address is already in use
                </span>
            </div>
            <label for="register-password">Password</label>
            <input
                type="password"
                name="register-password"
                id="register-password"
                placeholder="******"
                class="input"
                v-model="password"
            />
            <div class="invalid-input">
                <span v-if="pwdTooShort">
                    Password must be at least 6 characters long
                </span>
                <span v-if="pwdTooLong">
                    Password must be at most 20 characters long
                </span>
            </div>
            <label for="register-password-confirm">Confirm Password</label>
            <input
                type="password"
                name="register-password-confirm"
                id="register-password-confirm"
                placeholder="******"
                class="input"
                v-model="passwordConfirm"
            />
            <span class="invalid-input" v-if="pwdConfirmWrong">
                Passwords do not match
            </span>
            <div class="flex align-center">
                <input
                    type="checkbox"
                    name="register-agreement-confirm"
                    id="register-agreement-confirm"
                    class="my-auto mr-1 min-w-10"
                    v-model="agreementConfirm"
                />
                <label for="register-agreement-confirm"
                    >I hereby agree to the terms and conditions stated in
                    <a routerLink="/about" class="underline">About Us</a> of
                    ShareList and take to knowledge that my login data will be
                    stored in cookies for functional reasons.
                </label>
            </div>
            <span class="invalid-input" v-if="aboutAccepted === false">
                Terms and conditions have to be accepted!</span
            >
            <button
                class="btn-register"
                :disabled="registerDisabled"
                @click="register"
            >
                {{ registerText }}
            </button>
            <p>
                Already have an account?
                <router-link to="/login" class="underline">Login</router-link>
            </p>
        </div>
        <div v-else class="flex flex-col justify-center align-middle h-full">
            <p class="text-lg sm:text-5xl text-gray-700 text-center">
                Looks like you are already logged in! To check out your lists click <router-link to="/lists" class="underline">here</router-link>
            </p>
        </div>
    </div>
</template>

<script>
import { mainCheckLoggedIn } from "../js/utilities";
export default {
    data() {
        return {
            emailTaken: false,
            emailValid: true,
            pwdTooShort: false,
            pwdTooLong: false,
            pwdConfirmWrong: false,
            aboutAccepted: true,
            registerText: "Register",
            registerDisabled: false,
            loggedIn: false,
        };
    },
    props: {
        email: {
            type: String,
        },
        password: {
            type: String,
        },
        passwordConfirm: {
            type: String,
        },
        agreementConfirm: {
            type: Boolean,
        },
    },
    async mounted() {
        console.log("Register Init");
        this.loggedIn = await mainCheckLoggedIn();
    },
    methods: {
        register: async function () {
            //Disable Button
            this.registerText = "Registering...";
            this.registerDisabled = true;

            //Set all to valid
            this.emailValid = true;
            this.pwdTooShort = false;
            this.pwdTooLong = false;
            this.pwdConfirmWrong = false;
            this.aboutAccepted = true;

            //Check if email is valid
            const reg = /.+\@.+\..+/;
            if (!reg.test(this.email)) {
                this.emailValid = false;
            }
            //Check if email is already in use but only if email is valid
            else {
                this.emailTaken = await axios
                    .post("/api/user/checkemail", {
                        email: this.email,
                    })
                    .then(function (response) {
                        console.log(response.data);
                        return false;
                    })
                    .catch(function (error) {
                        console.log(error);
                        return true;
                    });
            }
            //Check if password is at least 6 characters long
            if (this.password === undefined || this.password.length < 6) {
                this.pwdTooShort = true;
            }
            //Check if password is at most 20 characters long
            else if (this.password.length > 20) {
                this.pwdTooLong = true;
            }
            //Check if passwords match
            if (this.password !== this.passwordConfirm) {
                this.pwdConfirmWrong = true;
            }
            //Check if terms and conditions are accepted
            if (!this.agreementConfirm) {
                this.aboutAccepted = false;
            }
            //If all checks are passed, register user
            if (
                this.emailValid &&
                !this.emailTaken &&
                !this.pwdTooShort &&
                !this.pwdTooLong &&
                !this.pwdConfirmWrong &&
                this.agreementConfirm &&
                this.aboutAccepted
            ) {
                const success = await axios
                    .post("/api/user/register", {
                        email: this.email,
                        password: this.password,
                    })
                    .then(function (response) {
                        console.log(response.data);
                        return true;
                    })
                    .catch(function (error) {
                        console.log(error);
                        return false;
                    });

                if (success) {
                    //Save Cookies
                    window.localStorage.setItem("email", this.email);
                    window.localStorage.setItem("pwd", this.password);

                    //Redirect to success page
                    this.$router.push("/register/success");
                } else {
                    //Show Error Message on button.
                    this.registerText =
                        "Something went wrong! Please try again.";
                }
            } else {
                //Reactivate Button
                this.registerText = "Register";
            }

            //Enable Button
            this.registerDisabled = false;
        },
    },
};
</script>
