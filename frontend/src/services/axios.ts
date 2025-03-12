import axios from 'axios'

const axiosInstance = axios.create({
  baseURL: `${import.meta.env.VITE_API_URL}/api`,
  headers: {
    Authorization: import.meta.env.VITE_API_KEY,
    'Content-Type': 'application/json',
  },
  withCredentials: false,
})

export default axiosInstance
