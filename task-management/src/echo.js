import Echo from 'laravel-echo'
import Pusher from 'pusher-js'

window.Pusher = Pusher

const token = localStorage.getItem('token')

window.Echo = new Echo({
  broadcaster: 'reverb',
  key: 'rvr1dltddyublucdqy9v',
  wsHost: 'localhost',
  wsPort: 8080,
  forceTLS: false,
  disableStats: true,

  authEndpoint: 'http://localhost:8000/broadcasting/auth',

  auth: {
    headers: {
      Authorization: `Bearer ${token}`,
    },
  },
})

export default window.Echo
