export const getToken = async function (router) {
    //Get token out of session storage
    const token = window.sessionStorage.getItem('token');
    //Get expires out of session storage
    const expires = window.sessionStorage.getItem('expires');
    //If token is not expired
    if (token !== null && expires !== null) {
        //If token is expired
        if (expires < new Date().getTime()) {
            console.log('Token expired');
            //Remove token and expires from session storage
            window.sessionStorage.removeItem('token');
            window.sessionStorage.removeItem('expires');
            //Try login again
            const success = await mainLogin();
            //If login was successful
            if (success) {
                //Return token
                return window.sessionStorage.getItem('token');
            }
            //If login was not successful
            else {
                await mainLogout();
                return false;
            }
        }
        //Return token
        return token;
    }else{
        console.log('No token');
        //Get Email and Password
        const email = window.localStorage.getItem("email");
        const pwd = window.localStorage.getItem("pwd");
        //Try login again
        const success = await mainLogin(email, pwd);
        //If login was successful
        if (success) {
            //Return token
            return window.sessionStorage.getItem('token');
        }
        //If login was not successful
        else {
            mainLogout();
        }
    }
}

export const mainLogin = async function (email, password) {
    const result = await axios.post("/api/user/login", {
        email: email,
        password: password
    }).then(response => {
        //Set Expires and Token
        window.sessionStorage.setItem("expires", response.data.expires);
        window.sessionStorage.setItem("token", response.data.token);
        return true;
    }).catch(async error => {
        console.log(error.response.data.error);
        //If login was not successful, logout
        await mainLogout();
        return false;
    }
    );
    console.log("Login: "+result);
    return result;
}

export const mainLogout = async function () {
    //Delete localStorage
    window.localStorage.removeItem("email");
    window.localStorage.removeItem("pwd");
    window.localStorage.removeItem("verified");
    //Delete sessionStorage
    window.sessionStorage.removeItem("token");
    window.sessionStorage.removeItem("expires");
}

