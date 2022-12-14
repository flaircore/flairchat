=== Flair Chat ===
Contributors: bahson
Donate link: https://flaircore.com/flair-core/paypal_payment
Tags: chat, realtime chat, live chat
Requires at least: 5.7
Tested up to: 6.0
Stable tag: 1.0.6
Requires PHP: 7.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Flair Chat provides a real time chat feature for your logged in users. Allows them to send and receive messages.

== Description ==

This WordPress plugin provides a live chat block for logged in users only,
and uses the pusher api [pusher.com/channels/pricing](https://pusher.com/channels/pricing), Your site will
probably do alright with the free tier.

== Installation ==

1.  Install via the Wordpress plugin repository or download and place in /wp-content/plugins directory
2.  Activate the plugin through the \'Plugins\' menu in WordPress
3.  See this plugin's configuration section.

== Configuration ==
1. Create an account with [https://dashboard.pusher.com/accounts/sign_in](https://dashboard.pusher.com/accounts/sign_in)
2. After step 1 above, create a Channels app and click on the **App Keys** and note the app details ie; app_id, key, secret and cluster.
3. From your wordpress plugins listing page, below the FlairChat plugin is the configuration link, click on that and fill in
the form with the right information from step 2 above ie; app_id, key, secret and cluster.


== Screenshots ==

1. Message thread mini view.
2. Mini view user list.
3. Mini view message list.
4. Minimized chat view.
5. Maximized chat view.
6. Maximized chat view.
7. Candido(example user) to Admin(example user) view.
8. Admin(example user) to Candido(example user) view.

== Frequently Asked Questions ==

= Are messages stored in a DB? Do you use websockets  or long polling? =

Yeah, messages are stored in the DB, and communication to front end via [pusher api](https://pusher.com/) (I think they use we sockets under the hood).

= Is it for admin & user/subscriber chat, or can any subscriber chat with this tool among them? I mean, a chat like FB chat? =

With this release (1.0.0), anyone logged in can chat,  but I will add an input to select the roles to exclude from chat in the next minor release.

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

= 1.0.4 =

Downloaded the vendor items to be included in the 1.0.3 fix above.


= 1.0.5 =

Fixed count unread issue, when no users were found.


= 1.0.6 =

Added custom filter hooks, so developers can easily extend/build on this plugin.
These are;
* flair_chat_load_users:
* flair_chat_sent_message:
See docs.md for more details
