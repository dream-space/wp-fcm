## wp-fcm
WordPress Plugin FCM(Firebase Cloud Messaging) for Android

## endpoint plugin
* URL    : www.sample-domain.com/?api-fcm=register
* TYPE   : POST
* HEADER : Content-Type : application/json
* BODY   : 
```
{
    "regid": "APA91bHScXXXXXXXX",
    "serial": "Android device serial number D511E1ZR611XXXXX",
    "device_name": "Brand Name (samsung SM-XX, Sony D-XXX, etc)",
    "os_version": "5.0 or 4.1 etc"
}
```

* RESPONS : 
```
{
  "status": "success or failed",
  "message": "Message Content"
}
```


## implement fcm
this article may help you :
* http://blog.dream-space.web.id/?p=116
* https://firebase.google.com/docs/cloud-messaging/android/client


## notification body
* JSON
```
{
  "title": "Title Text",
  "content": "Content Text",
  "post_id": post_id in number (optional)
}
```

* Android
```Java
@Override
public void onMessageReceived(RemoteMessage remoteMessage) {
    if (remoteMessage.getData().size() > 0) { // validate nullable
        Map<String, String> data = remoteMessage.getData();
        String title    = data.get("title");
        String content  = data.get("content");
        Integer post_id = Integer.parseInt(data.get("post_id")); // can be null
        
        // Your action display notification here
    }
}
```

### purchase project implementation 
https://codecanyon.net/item/koran-wordpress-app-with-push-notification-20/17470988



[<img target="_blank" src="https://www.paypal.com/en_US/i/btn/btn_donate_LG.gif">](https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=SMF4CTJ44XZ9Y)
