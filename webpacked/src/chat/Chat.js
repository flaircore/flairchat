import Pusher from 'pusher-js'

class Chat {
  constructor(messages_url, new_message_url, pusher_configs, current_id, headers = {}, appConfigs = {}) {
    const chatBlockView = document.querySelector("#chat-block-main")
      //const chatMainView = document.querySelector("#chat-block-main .chat-block")
    const maximizeBtn = document.querySelector("#chat-block-main .maximize")
    const minimizeBtn = document.querySelector("#chat-block-main .minimize")
    const users = document.querySelectorAll("#chat-block-main .users-wrapper .users-list .user")

    // Go back button on smaller screens.
    const resetView = chatBlockView.querySelector('.chat-controls .go-back')

    const usersView = chatBlockView.querySelector('.users-wrapper')
    const messagesView = chatBlockView.querySelector('.messages-view')
    const chatInput = chatBlockView.querySelector('[type="input"], [name="message"]')
    const totalUnread = chatBlockView.querySelector( '.chat-controls .total')

    chatInput
      .addEventListener('keyup', async (e) => await this.sendNewMessage(e))

    const controls = [maximizeBtn, minimizeBtn]

    controls.forEach(item => {
      item.addEventListener('click', event => {
        this.updateControls(event)
      })
    })
    this.controls = controls
    this.totalUnread = totalUnread
    this.chatBlockView  = chatBlockView
    this.usersView  = usersView
    this.messagesView  = messagesView
    this.resetView = resetView
    this.chatInput = chatInput
    this.resetView.addEventListener('click', this.showUserListing)
    this.messagesUrl = messages_url
    this.newMessageUrl = new_message_url
    this.receiverId = ''
    this.currentId = current_id
    this.headers = headers
    this.notificationFile = appConfigs.notification_file
    this.notificationOption = appConfigs.notification_option
    // Holds the messages pages (pager) item
    this.pages = {
      current_page: null,
      total_pages: 0,
      total_items: ""
    }

    users.forEach(user => {
      user.addEventListener('click',  async () => await this.showChats(user))

    })

    // Observer definitions
    this.observerConfig = {
      attributes: true,
      attributeOldValue: true,
      attributeFilter: ['class'],
      childList: true,
      subtree: true
    };

    const options = {
      root: null,
      //threshold: 1, //@TODO only works on windows!!!
      rootMargin: "60%" // fetch earlier
      // rootMargin: "-10px" // fetch later
    }
    /**
     * Observes scroll position on the particular
     * DOM elements it's assigned to.
     * @type {IntersectionObserver}
     */
    this.intersectionObserver =
      new IntersectionObserver(entries => {
          this.observeIntersections(entries)
        },
        options
      )


     // Observes attribute changes on the particular
     // DOM elements it's assigned to.

    // this.mutationObserver = new MutationObserver(entries => {
    //   this.observeMutations(entries)
    // })


    const pusher = new Pusher(pusher_configs.app_key, {
      cluster: pusher_configs.cluster,
      headers: headers,
    })

    const channel = pusher.subscribe('my-channel')
    channel.bind('dru-chat-event', (data) => this.druChatEvent(data))
  }

  updateControls = (event) => {

    // Toggle now-show class between the controls
    this.controls.forEach(control => {
      control.classList.toggle('no-show')
    });

    // Toggle children-hidden class too, on main block
    this.chatBlockView.classList.toggle('children-hidden')
    // Toggle show for div with users and messages listing
    this.chatBlockView.querySelector('.chat-block')
      .classList.toggle('no-show')

    // Use the event details to determine what other
    // parts (attributes) of the chat to update

  }

  // Resets user view on smaller screens.
  showUserListing = () => {
    this.resetView.classList.toggle('no-show')

    // Hide message view and show users listing instead.
    // Like reset what's done in show chats,
    this.usersView.classList.toggle('no-show')
    this.messagesView.classList.toggle('no-show')
  }

  showChats = async (user) => {

    // Remove active class from previous if any
    this.usersView.querySelector('.active')
      ?.classList?.remove('active')

    // remove inactive state from input
    this.chatInput.removeAttribute('disabled')


    // Add active class to current.
    user.classList.add('active')
    const userId = user.getAttribute('id');
    this.receiverId = userId
    // Reset pages too.
    this.pages = {
      current_page: null,
      total_pages: 0,
      total_items: ""
    }

    // Reset message view
    this.messagesView.querySelector('.message-list')
      .innerHTML = ''

    // Toggle for mobile apps .users-wrapper and messages-view
    // User listing vs message listing view.

    this.usersView.classList.toggle('no-show')
    this.messagesView.classList.toggle('no-show')
    this.resetView.classList.toggle('no-show')
    // Display btn to go back to user listing too

    // Update message header.

    this.messagesView.querySelector('.header img').src
      = user.querySelector('img').src

    this.messagesView.querySelector('.header .username').innerText
      = user.querySelector('.display-name').innerText
    await this.loadChats()
  }

  loadChats = async () => {

    // Just a limit to compare against current_pages.
    const limit = this.pages.total_pages ? this.pages.total_pages : -2;
    if (limit === (this.pages.current_page + 1)) return


    const messages = this.messagesView.querySelector('.message-list')

    // Determine if to auto scroll/focus with messages.innerHTML
    // const shouldScroll = messages.innerHTML ? false : true

    const messageList = await this.newMsgFromTemplate()

    // If no messages from request and it's the first request.
    // meaning messages.innerHTML is empty string, we show a message,
    // of "no chat between the two users".
    if (!messageList.childElementCount && !messages.innerHTML) {
      const noMsgWrapper = document.createElement('div')
      noMsgWrapper.classList.add('no-message')
      noMsgWrapper.style.height = 'auto'
      const messageDiv = document.createElement('div')
      messageDiv.classList.add('message', 'received')
      const msgParagraph = document.createElement('p')
      msgParagraph.innerText = "This is the very beginning of your chat with this user, go on " +
        "and send your first message."
      messageDiv.prepend(msgParagraph)
      noMsgWrapper.appendChild(messageDiv)
      messages.prepend(noMsgWrapper)
      // @TODO add height to this div
    }

    // Append to screen
    messages.prepend(messageList)

    //if (shouldScroll) {
      messages
        ?.querySelector(`[tabindex="${this.pages.current_page}"]`)
        ?.focus()
    //}

      //.focus({preventScroll: false})

    // @TODO scroll into view bug,

  }


  observeIntersections = (entries) => {

    entries.forEach( async (entry) => {

      if (entry.isIntersecting) {

        // Get the next items (pager).
         await this.loadChats();

        // Only observing the last item on the message list.
        this.intersectionObserver.unobserve(entry.target)

      }
    })
  }

  newMsgFromTemplate = async () => {

    const messageData = await this.fetchMessagesByPage()

    const newMessages = document.createDocumentFragment();
    const {messages, pages} = messageData
    this.pages = {...pages}

    // Reverse array since we're appending items in that order.
    messages?.reverse().forEach((data, index) => {
      //data.created = data.created * 1000
      const messageDiv = this.messageTemplateFromData(data)
      if (index === 0) {
        // Last item scroll Dom listener, so we load the
        // Next page(pager).
        //messageDiv.classList.add('testing-index-30')
        this.intersectionObserver.observe(messageDiv)
      }
      // Add tabindex to the message at the bottom to focus.
      if (messages.length === (index + 1)) {
        // Add tabindex for to focus()
        messageDiv.setAttribute('tabindex', pages.current_page)

      }
      newMessages.appendChild(messageDiv)
    })
    if (messages[0]) this.updateTotalUnRead(messages[0], 'is_read')
    return newMessages
  }

  sendNewMessage = async (event) => {

    // On larger screens, the message listing plus,
    // the input are displayed even if no user is
    // selected (receiver), we set a message and
    // disable input.
    const messages = this.messagesView.querySelector('.message-list')
    if (!this.receiverId) {
      const noReceiverWrapper = document.createElement('div')
      noReceiverWrapper.classList.add('no-message')
      //noReceiverWrapper.style.height = '80%'

      const messageDiv = document.createElement('div')
      messageDiv.classList.add('message', 'received')
      const msgParagraph = document.createElement('p')
      msgParagraph.innerText = "Please select a user from the left to chat with!."
      msgParagraph.classList.add('warning')
      messageDiv.prepend(msgParagraph)
      noReceiverWrapper.appendChild(messageDiv)
      messages.prepend(noReceiverWrapper)
      event.target.setAttribute('disabled', 'true')
      return
    }


    if (event.keyCode === 13 && this.receiverId && event.target.value) {

      // @TODO show error when value is empty

      const headers = {'Content-Type': 'application/x-www-form-urlencoded', ...this.headers}

      await fetch(this.newMessageUrl, {
        method: 'POST',
        mode: 'same-origin', // no-cors, *cors, same-origin
        cache: 'no-cache', // *default, no-cache, reload, force-cache, only-if-cached
        credentials: 'same-origin', // include, *same-origin, omit
        headers,
        redirect: 'follow', // manual, *follow, error
        referrerPolicy: 'no-referrer', // no-referrer, *no-referrer-when-downgrade, origin, origin-when-cross-origin, same-origin, strict-origin, strict-origin-when-cross-origin, unsafe-url
        body: new URLSearchParams({
          'receiver_id': this.receiverId,
          'message': event.target.value
        })

        // 'message': Drupal.checkPlain(event.target.value)
      })

      const messages = this.messagesView.querySelector('.message-list')

      // Append message to message view
      const data = {
        message: event.target.value,
        to: this.receiverId,
        from: this.currentId,
        is_read: 1,
        created: Date.now(),
      }
      const messageDiv = this.messageTemplateFromData(data)
      messages.appendChild(messageDiv)

      messages.scrollTop = messages.scrollHeight
      // Clear value
      event.target.value = ''
    }
  }

  updateTotalUnRead = (data, type = null) => {
    const receiver = this.usersView.querySelector(`[id="${data.to}"]`)

    const unreadCount = receiver?.querySelector('.pending .unread-count')

    if (!type) {

      if (unreadCount?.innerText) {
        unreadCount.innerText = parseInt(unreadCount.innerText) + 1
      } else {
        const item = document.createElement('span')
        item.classList.add('unread-count')
        item.innerText = 1
        receiver.querySelector('.pending')
            .appendChild(item)
      }

      if (this.notificationOption) {
        const notifications = this.chatBlockView.querySelector("#notification #sound audio")
        notifications.play()
            .then(data => {
            })
            .catch(error => console.error('User must first interact with the chat block!'))
      }


      this.totalUnread.innerText = parseInt(this.totalUnread.innerText) + 1

    } else {
      // Bug when viewing other users messages, we need to pass from!! @TODO
      // @TODO bug on user update total unread accuracy!
      // Update total Unread
      const total = parseInt(this.totalUnread.innerText);
      // const receiver = this.usersView.querySelector(`[id="${from}"]`)
      //       if (receiver.classList.contains('active')) {
      const fromTotalItem = this.usersView.querySelector('.active .pending .unread-count')
      const fromTotal = parseInt(fromTotalItem.innerText)

      this.totalUnread.innerText = total <= 10 ? 0 : total - 10
      fromTotalItem.innerText = fromTotal <= 10 ? 0 : fromTotal - 10
    }
  }

  druChatEvent = ({from, to}) => {
    // If currentId is same as from,
    // update view the messages for the message sender.
    if (this.currentId === from) {
     // For this user the message is attached to the message view,
      // before the input field is cleared, message contains the
      // input field value @TODO about is_read.

    }
      // The receiver of the message now.
      // Which has multiple variants to consider for
      // each work-flow.
      // Play sound for receiver below too.
    else if (this.currentId === parseInt(to)) {

      // Check if 'from' has a class of active.
      // Update view chat if so
      // Else update unread count
      const receiver = this.usersView.querySelector(`[id="${from}"]`)
      if (receiver.classList.contains('active')) {
        // Load the last message between current user (this receiver) and sender (from).
        this.fetchMessagesByPage(from)
          .then((data) => {
            const messages = this.messagesView.querySelector('.message-list')
            // data.messages[0].created = data.messages[0].created * 1000
            const msgDiv = this.messageTemplateFromData(data.messages[0])
            messages.appendChild(msgDiv)

            messages.scrollTop = messages.scrollHeight
          })

      } else {
        // Update unread count.
        // Updates unread and plays sound
        this.updateTotalUnRead({to: from})
      }
    }
  }

  /**
   * Creates a message div from the data object.
   * @param data
   * @returns {HTMLDivElement}
   */
  messageTemplateFromData = data => {
    // Create a message div from data
    const messageDiv = document.createElement('div')
    if (parseInt(data.from) === parseInt(this.currentId)) {
      messageDiv.classList.add('message','sent')
    } else {
      messageDiv.classList.add('message','received')
    }
    const msgParagraph = document.createElement('p')
    msgParagraph.innerText = data.message
    const sentAt = document.createElement('span')

    sentAt.innerText = new Date(data.created).toLocaleString()
    msgParagraph.append(sentAt)
    messageDiv.appendChild(msgParagraph)
    return messageDiv
  }


  /**
   * Fetches message by receiver_id.
   * also updates the pages item {}.
   * @param receiver_id
   * @returns {Promise<any>}
   */
  fetchMessagesByPage = async (receiver_id = null) => {
    if (this.pages.current_page || this.pages.current_page === 0) {
      const current_page = this.pages.current_page += 1
      this.pages = {...this.pages, current_page}
    }

    let url = ''
    if (receiver_id) {
      url = this.messagesUrl + receiver_id + '?page=' + 0 + '&limit=1'
    } else {
      url = this.messagesUrl + this.receiverId + '?page=' +this.pages.current_page
    }

    const headers = {...this.headers}

    const res = await fetch(url, {
      method: 'GET',
      mode: 'same-origin',
      cache: 'no-cache',
      credentials: 'same-origin',
      headers,
      redirect: 'follow',
      })


    if (res.status === 200) {

      return await res.json();

    } else  {
      console.error('An error occurred!')
    }
  }

}

export { Chat }
