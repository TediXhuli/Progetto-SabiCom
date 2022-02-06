import Axios from 'axios';
import router from "../router";

const apiFactory = Axios.create({
  baseURL:process.env.VUE_APP_API_URL,
  headers:{
    'X-Requested-With':'XMLHttpRequest',
    'Content-Type':'application/json',
    'X-FROM-PLATFORM':'SUPPORT'
  }
});

apiFactory.interceptors.request.use(config => {
  const token = localStorage.getItem('access_token');
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config;
}, (error) => {
  if (error.response.status === 401) {
    router.push('login');
  }
  return Promise.reject(error.response.data);
});

apiFactory.interceptors.response.use(response => response, error => {
  if ((error.response && error.response.status === 401)) {
    alert(error.response.data.error);
    router.push({name:'login'});
  }
  return Promise.reject(error.response ? error.response.data : error);
});

export default apiFactory;
