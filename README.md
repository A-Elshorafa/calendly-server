

## Create .env

  1- Create a file called .env at the root directory
  
  2- Copy .env.example to .env
      
      HINT: APP_URL, FRONTEND_URL, SANCTUM_STATEFUL_DOMAINS are set as defualt on .env.example 
            if you want to change them you've to keep the domain name `localhost` and change the port (for local testing)

  3- Create a sql DB and put your credientials on corresponding DB env variables
  
  4- Fill database env variables (DB_DATABASE, DB_USERNAME, and DB_PASSWORD)
  
  5- Fill mailer credientials (Recommend to use mailtrap.io for quick testing)
  
  6- ALLOW_SUBSCRIPTION_NOTIFICATION
  
    if value true it will send subscription mails automatically, change it to false to disable it
  
  7- Get your front-end https url and fill it instead of {https-front-end-url} and remove curly brackets
    
    if you don't have one follow calendly-client README.md to get your own https url

  8- Fill zoom oAuth app ZOOM_CLIENT_ID and ZOOM_CLIENT_SECRET
      
    Note: if you don't have one please follow instructions under *Create Your Own Zoom App*
          Also you can run the app without zoom integration the only thing you will be missing is the zoom url

## Create Your Own Zoom App

  1- create account on marketplace.zoom.us/
  
  2- Click develop
  
  3- Build app
  
  4- Choose OAuth type
  
  5- At App Credentials menu, under `Development` section put your ZOOM_REDIRECT_URL
  
    Note: you've to keep update your redirect if you changed your https url 
          as well as CLIENT_ID and CLIENT_SECERT you have to keep your Zoom app and .env updated
  
  6- At the end copy CLIENT_ID and CLIENT_SECERT and use them on ZOOM_CLIENT_ID and ZOOM_CLIENT_SECRET at .env
  
  7- good luck :)

## Run Commands
```
    1- composer install
    2- php artisan key:generate
    3- php artisan migrate
    4- php artisan db:seed
    5- php artisan schedule:work // run worker to send notification mails
    6- php artisan serve
```

> Enjoy schedulling :)