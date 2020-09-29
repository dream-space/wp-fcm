## wp-fcm
WordPress Plugin FCM(Firebase Cloud Messaging) for Android

## endpoint plugin
* URL    : www.sample-domain.com/?api-fcm=register
* TYPE   : POST
* HEADER : Content-Type : application/json
*          Security : YOUR_SECURITY_CODE
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
    FcmNotif fcmNotif = new FcmNotif();
    if (remoteMessage.getData().size() > 0) {
        Map<String, String> data = remoteMessage.getData();
        fcmNotif.post_id = data.get("post_id") == null ? -1 : Integer.parseInt(data.get("post_id"));
        fcmNotif.title = data.get("title");
        fcmNotif.content = data.get("content");
        fcmNotif.image = data.get("image");
    } else if (remoteMessage.getNotification() != null) {
        RemoteMessage.Notification rn = remoteMessage.getNotification();
        fcmNotif.title = rn.getTitle();
        fcmNotif.content = rn.getBody();
    }
    // Your action display notification here
}
```

* Subscribe topic
```Java
FirebaseMessaging.getInstance().subscribeToTopic("ALL-DEVICE").addOnCompleteListener(new OnCompleteListener<Void>() {
    @Override
    public void onComplete(@NonNull Task<Void> task) {
        sharedPref.setSubscibeNotif(task.isSuccessful());
    }
});
```

### purchase project implementation 
https://codecanyon.net/item/koran-wordpress-app-with-push-notification-20/17470988



[<img target="_blank" src="https://www.paypal.com/en_US/i/btn/btn_donate_LG.gif">](https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=SMF4CTJ44XZ9Y)
