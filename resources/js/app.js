import './bootstrap';
import "@fortawesome/fontawesome-free/css/all.css";
import Swal from 'sweetalert2';
import Pusher from 'pusher-js';

window.Swal = Swal;

// window.pusher = new Pusher('my-app-key', {
//     cluster: '',
//     enabledTransports: ['ws', 'wss'],
//     forceTLS: false,
//     wsHost: '127.0.0.1',
//     wsPort: '6001',
// });

window.listen = function (data, desc, callback) {
    setInterval(() => {
        fetch(desc).then(data => data.json()).then((e) => {
            // console.log({ e, data })
            if (JSON.stringify(data) != JSON.stringify(e)) {
                data = e;
                callback(e);
                // console.log('updated')
            }
        })
    }, 3000)

}
