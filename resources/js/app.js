import {createApp, h} from 'vue'
import {createInertiaApp} from '@inertiajs/inertia-vue3'
import {InertiaProgress} from '@inertiajs/progress'

import './theme/theme'
import './theme/datatables'
import './theme/fullcalendar'
import './theme/settings'
import '../css/light.css'

import {library} from '@fortawesome/fontawesome-svg-core'
import {
    faBackward, faBan, faBarcode,
    faChartArea, faCheck, faCheckCircle,
    faClipboardCheck, faCreditCard, faEdit, faGear, faInbox,
    faInr, faMoneyCheck, faPaperclip,
    faPaperPlane,
    faPencil, faPlus, faPowerOff,
    faQrcode, faRefresh, faRightFromBracket, faSave, faSignal, faSliders,
    faTicket,
    faToolbox,
    faTrain, faTrashCan, faUpload,
    faUser, faUserLock, faUserSecret, faUsersGear
} from '@fortawesome/free-solid-svg-icons'
import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";

library.add(faUpload)
library.add(faSignal)
library.add(faPaperclip)
library.add(faTrashCan)
library.add(faBan)
library.add(faCreditCard)
library.add(faBarcode)
library.add(faMoneyCheck)
library.add(faUserSecret)
library.add(faUsersGear)
library.add(faUserLock)
library.add(faUser)
library.add(faCheck)
library.add(faRightFromBracket)
library.add(faGear)
library.add(faSliders)
library.add(faInbox)
library.add(faBackward)
library.add(faSave)
library.add(faPlus)
library.add(faToolbox)
library.add(faTrain)
library.add(faInr)
library.add(faRefresh)
library.add(faQrcode)
library.add(faPencil)
library.add(faTicket)
library.add(faClipboardCheck)
library.add(faPaperPlane)
library.add(faChartArea)
library.add(faEdit)
library.add(faCheckCircle)
library.add(faPowerOff)
library.add(faUser)


createInertiaApp({
    resolve: name => require(`./Pages/${name}`),
    setup({el, App, props, plugin}) {
        createApp({render: () => h(App, props)})
            .use(plugin)
            .component('font-awesome-icon', FontAwesomeIcon)
            .mount(el)
    },
})


InertiaProgress.init({
    color: '#ff0000',
    includeCSS: true,
    showSpinner: false,
})
