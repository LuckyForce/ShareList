export const getToken = async function (router) {
    //Get token out of session storage
    const token = sessionStorage.getItem('token');
    //Get expires out of session storage
    const expires = sessionStorage.getItem('expires');
    //If token is not expired
    if (token && expires) {
        //If token is expired
        if (expires < new Date().getTime()) {
            //Remove token and expires from session storage
            sessionStorage.removeItem('token');
            sessionStorage.removeItem('expires');
            //Try login again
            const success = await mainLogin();
            //If login was successful
            if (success) {
                //Return token
                return sessionStorage.getItem('token');
            }
            //If login was not successful
            else {
                mainLogout(router);
            }
        }
        //Return token
        return token;
    }
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

export const mainLogout = async function (router) {
    //Delete localStorage
    window.localStorage.removeItem("email");
    window.localStorage.removeItem("password");
    //Delete sessionStorage
    window.sessionStorage.removeItem("token");
    window.sessionStorage.removeItem("expires");
    //Redirect to homepage
    router.push("/");
}

