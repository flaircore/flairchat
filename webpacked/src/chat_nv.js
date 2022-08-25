import './scss/styles.scss'
import {Chat} from './chat/Chat'

const messages_url = flairChatData.messages_url
const new_message_url = flairChatData.new_msg_url
const currentId = parseInt(flairChatData.current_id)
const headers = flairChatData.headers
const pusher_configs = {
    app_key: flairChatData.app_key,
    cluster: flairChatData.cluster,
    presence_url: flairChatData.presence_url,
}

// Initialize chat app
new Chat(messages_url, new_message_url, pusher_configs, currentId, headers);







