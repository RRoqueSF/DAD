import { ref, computed } from 'vue'
import { defineStore } from 'pinia'
import axios from 'axios'
import { useErrorStore } from '@/stores/error'
import { useRouter } from 'vue-router'
import avatarNoneAssetURL from '@/assets/avatar-none.png'


export const useAuthStore = defineStore('auth', () => {
    const router = useRouter()
    const storeError = useErrorStore()
    const user = ref(null)
    const token = ref('')
    
    const userName = computed(() => {
        return user.value ? user.value.name : ''
    })

    const userNickname = computed(() => {
        return user.value ? user.value.nickname : ''
    })

    const userFirstLastName = computed(() => {
        const names = userName.value.trim().split(' ')
        const firstName = names[0] ?? ''
        const lastName = names.length > 1 ? names[names.length - 1] : ''
        return (firstName + ' ' + lastName).trim()
    })

    const userEmail = computed(() => {
        return user.value ? user.value.email : ''
    })

    const userType = computed(() => {
        return user.value ? user.value.type : ''
    })

    const userBlockedStatus = computed(() => {
        return user.value ? user.value.blocked : false
    })

    const userBrainCoinsBalance = computed(() => {
        return user.value ? user.value.brain_coins_balance : 0
    })

    const userCustomData = computed(() => {
        return user.value ? user.value.custom : {}
    })

    const userPhotoUrl = computed(() => {
        const photoFile = user.value ? user.value.photoFileName ?? '' : ''
        if (photoFile) {
        return axios.defaults.baseURL.replaceAll("/api", photoFile)
        }
        return avatarNoneAssetURL
    })
    // Private function to clear user data and reset authorization
    const clearUser = () => {
        resetIntervalToRefreshToken()
        user.value = null
        token.value = ''
        localStorage.removeItem('token')
        axios.defaults.headers.common.Authorization = ''
        }

        const login = async (credentials) => {
            console.log('login')
            storeError.resetMessages()
            try {
            const responseLogin = await axios.post('auth/login', credentials)
            token.value = responseLogin.data.token
            localStorage.setItem('token', token.value)
            axios.defaults.headers.common.Authorization = 'Bearer ' + token.value
            const responseUser = await axios.get('users/me')
            console.log('execucao users/me no login')
            user.value = responseUser.data.data
            repeatRefreshToken()
            router.push({ name:'home' })
            return user.value
            } catch (e) {
            clearUser()
            storeError.setErrorMessages(e.response.data.message, e.response.data.errors,
            e.response.status, 'Authentication Error!')
            return false
            }
        }

    const logout = async () => {
        storeError.resetMessages()
        try {
            await axios.post('auth/logout')
            clearUser()
            router.push({ name: 'login' })
            return true

        } catch (e) {
            clearUser()
            storeError.setErrorMessages(e.response.data.message, [], e.response.status,
                'Authentication Error!')
            return false
        }
    }

    // Private functions and intervalToRefreshToken variable
    let intervalToRefreshToken = null
    const resetIntervalToRefreshToken = () => {
        if (intervalToRefreshToken) {
            clearInterval(intervalToRefreshToken)
        }
        intervalToRefreshToken = null
    }

    const repeatRefreshToken = () => {
        if (intervalToRefreshToken) {
            clearInterval(intervalToRefreshToken)
        }
        intervalToRefreshToken = setInterval(async () => {
            try {
                const response = await axios.post('auth/refreshtoken')
                token.value = response.data.token
                localStorage.setItem('token', token.value)
                axios.defaults.headers.common.Authorization = 'Bearer ' + token.value
                return true

            } catch (e) {
                clearUser()
                storeError.setErrorMessages(e.response.data.message,
                    e.response.data.errors, e.response.status, 'Authentication Error!')
                return false
            }
        }, 1000 * 60 * 110) // repeat every 110 minutes
        // To test the refresh token, replace previous line with the following code
        // This will repeat the refreshtoken endpoint every 10 seconds:
        //}, 1000 * 10)
        return intervalToRefreshToken
    }
    // Add this to ensure the token is set when app initializes


    const restoreToken = async function () {
        console.log('restoreToken')
        let storedToken = localStorage.getItem('token')
        if (storedToken) {
            try {
                token.value = storedToken
                axios.defaults.headers.common.Authorization = 'Bearer ' + token.value
                const responseUser = await axios.get('users/me')
                console.log('execucao users/me no restoreToken')
                user.value = responseUser.data.data
                repeatRefreshToken()
                return true
        } catch {
            clearUser()
            return false
            }
        }
        return false
        }
        const storedToken = localStorage.getItem('token')
        if (storedToken) {
            token.value = storedToken
            axios.defaults.headers.common.Authorization = `Bearer ${token.value}`
        }

    return {
        user, userName, userNickname, userEmail, userType, userFirstLastName, 
        userPhotoUrl, userBlockedStatus, userBrainCoinsBalance, userCustomData,
        login, logout, restoreToken,
    }
})