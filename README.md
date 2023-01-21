# calendly-server
Build a server that serve as like as Caliendly


php artisan migrate

php artisan db:seed

# start schedule worker to notify host and attendee before an hour of the meeting
php artisan schedule:work


# immediately run the schedule worker
php artisan schedule:run