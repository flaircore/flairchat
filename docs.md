# Filters:

### flair_chat_load_users
```php
	add_filter('flair_chat_load_users', function ($users){
				return $users;
			}, 10, 1);
```

### flair_chat_sent_message
```php
			add_filter('flair_chat_sent_message', function ($message){
				$message = array(
					'message'  => $message['message'] . ' newest messagge',
					'from_uid' => $message['from_uid'],
					'to_uid'   => $message['to_uid'],
					'is_read'  => $message['is_read'], //false,
					'should_send' => $message['should_send'], //true,
				);
				
				// If should_send is false message is not sent

				return $message;
			}, 10, 1);

```