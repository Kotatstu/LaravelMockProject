import axios from "axios";

//axios object generally return {status, headers, data} so when using the respone use .data instead of .value
const api = axios.create({
  baseURL: 'http://127.0.0.1:8000/api',
  headers: {
    'Accept': 'application/json',
  },
})

export default api
