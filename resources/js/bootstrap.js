import axios from 'axios';
window.axios = axios;
import './header'

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
