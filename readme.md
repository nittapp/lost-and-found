# Introduction
Lost and found service for the NITT master app.

# Setup 
 1. To get it running, install docker and docker-compose on your host machine.
 2. Copy `.env.example` to `.env`
 3. Run `sh keygen.sh` and paste the base_64 key in APP_KEY in.
 4. The above cmd creates a folder called `logs`, and creates the files : nginx-error.log and nginx-access.log for server logs.
 5. Run `docker-compose up` from inside the project directory. The app will now be available from http://0.0.0.0:8080
 6. To add datatables, run  `docker exec laf-app php artisan migrate`
 7. Run this command to set permission defaults for the app, `docker exec laf-app php artisan db:seed`
 8. The application's pma is now availabe at `0.0.0.0:3000`, use the credentials in the `.env`
