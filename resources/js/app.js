import './bootstrap';

window.axios.interceptors.response.use(
    response => response,
    error => {
        if (error.response && error.response.status === 419) {
            // Force a reload to get a fresh session and CSRF token
            window.location.reload();
        }
        return Promise.reject(error);
    }
);
