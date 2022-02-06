import apiFactory from "../factories/ApiFactory";

export default {
    login(user) {
        return new Promise((resolve, reject) => {
            apiFactory.post('/login', user)
                .then(
                    (success) => resolve(success.data),
                    (error) => reject(error)
                )
        })
    }
}