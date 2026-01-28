const Auth = {
    getToken() {
        return localStorage.getItem('token');
    },

    saveToken(token) {
        localStorage.setItem('token', token);
    },

    isAuthenticated() {
        return this.getToken() !== null;
    },

    logout() {
        localStorage.removeItem('token');
        window.location.href = 'login.html';
    }
};
