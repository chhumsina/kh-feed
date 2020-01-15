export default function({ $axios, store }) {
    $axios.onError(error => {

        if (!error.status) {
            store.$auth.logout();
        }

        const code = parseInt(error.response && error.response.status)

        if ([401, 403, 429].includes(code)) {
            store.$auth.logout();
        }
        return Promise.reject(error);
    });

    $axios.onRequest(() => {
        store.dispatch('validation/clearErrors');
    });
}