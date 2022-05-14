export const getToken = async function () {
    return "HI"
}

export const mainLogin = async function (email, password) {
    const result = await axios.post("/api/user/login", {
        email: email,
        password: password
    }).then(response => {
        //Set Expires and Token
        sessionStorage.setItem("expires", response.data.expires);
        sessionStorage.setItem("token", response.data.token);
        return true;
    }).catch(error => {
        console.log(error.response.data);
        //Remove Local Storage
        localStorage.removeItem("email");
        localStorage.removeItem("pwd");
        return false;
    }
    );
    return result;
}
