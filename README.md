# MauticCustomTags

## Installation

### Composer from Mautic root directory

composer require kuzmany/mautic-custom-tags-bundle

### Then:

1. Go to Mautic > Plugins and click to the button Install/Upgrade plugins
![image](https://user-images.githubusercontent.com/462477/34650614-28cf7e1a-f3c4-11e7-8653-2ffd04f62d4a.png)
2. New plugin should be added 
![image](https://user-images.githubusercontent.com/462477/36776213-434e7196-1c65-11e8-87a7-326a478b0ba1.png)
3. Then create email or page and you can use these tags:

`{getremoteurl=http://yourremote.url}`

The tag get content from your remote url. 

You can display remote content from external site in your email or page.

`{base64=customtextareafield}`

The tag decode base64 encoded content from contacts custom textarea field. 

You can pass base64 encode data to contact from API and then display in email or page.

![image](https://user-images.githubusercontent.com/462477/36776344-be299170-1c65-11e8-8b47-9f91ce1c9355.png)