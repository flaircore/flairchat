import './scss/styles.scss'
import {Chat} from './chat/Chat'

    const messages_url = flairChatData.messages_url
    const new_message_url = flairChatData.new_msg_url
    const currentId = flairChatData.current_id
    const headers = flairChatData.headers
    const pusher_configs = {
      app_key: flairChatData.app_key,
      cluster: flairChatData.cluster,
      presence_url: flairChatData.presence_url,
    }

    // Initialize chat app
    new Chat(messages_url, new_message_url, pusher_configs, currentId, headers);


// (function ($, Drupal, drupalSettings) {
//
//   $(document).ready(function () {
//     const messages_url = drupalSettings.dru_chat.msgs_url
//     const new_message_url = drupalSettings.dru_chat.new_msg_url
//     const currentId = drupalSettings.dru_chat.current_id
//     const pusher_configs = {
//       app_key: drupalSettings.dru_chat.pusher_app_key,
//       cluster: drupalSettings.dru_chat.pusher_cluster,
//       presence_url: drupalSettings.dru_chat.presence_url,
//     }
//
//     // Initialize chat app
//     new Chat(messages_url, new_message_url, pusher_configs, currentId);
//
//   })
//
// })(jQuery, Drupal, drupalSettings)






