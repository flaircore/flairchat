# Flair Chat
- Contributors: bahson
- Donate link: https://flaircore.com/flair-core/paypal_payment
- Tags: chat, realtime chat, live chat
- Requires at least: 5.7
- Tested up to: 6.0
- Stable tag: 1.0.3
- Requires PHP: 7.0
- License: GPLv2 or later
- License URI: https://www.gnu.org/licenses/gpl-2.0.html

Flair Chat provides a real time chat feature for your logged in users. Allows them to send and receive messages.

### Description

This WordPress plugin provides a live chat block for logged in users only,
and uses the pusher api [pusher.com/channels/pricing](https://pusher.com/channels/pricing), Your site will
probably do alright with the free tier.

### Installation

1.  Install via the Wordpress plugin repository or download and place in /wp-content/plugins directory
2.  Activate the plugin through the \'Plugins\' menu in WordPress
3.  See this plugin's configuration section.

### Configuration ==
1. Create an account with [https://dashboard.pusher.com/accounts/sign_in](https://dashboard.pusher.com/accounts/sign_in)
2. After step 1 above, create a Channels app and click on the **App Keys** and note the app details ie; app_id, key, secret and cluster.
3. From your wordpress plugins listing page, below the FlairChat plugin is the configuration link, click on that and fill in
   the form with the right information from step 2 above ie; app_id, key, secret and cluster.

== Frequently Asked Questions ==

== Screenshots ==

1. Message thread mini view ![Message thread mini view ](/assets/screenshot-1.png).
2. Mini view user list ![Mini view user list](/assets/screenshot-2.png).
3. Mini view message list ![Mini View](/assets/screenshot-3.png).
4. Minimized chat view ![Mini View](/assets/screenshot-4.png).
5. Maximized chat view ![Max View](/assets/screenshot-5.png).
6. Maximized chat view ![Max View](/assets/screenshot-6.png).
7. Candido(example user) to Admin(example user) view ![Chat View](/assets/screenshot-7.png).
8. Admin(example user) to Candido(example user) view.![Chat View](/assets/screenshot-8.png).

== Changelog ==

= 1.0.0 =
First version

= 1.0.1 =
Fixed invalid date, error on to_uid just sent message.

= 1.0.2 =
Updates total unread in the chat controls on new message.
Updates total unread when user opens message view and also on scrolls.
Plays notification sound when new message received.
Enables Admins to disable or enable new message sound notifications via the configuration form.

= 1.0.3 =

Included /vendor directory which was missing from previous git actions.