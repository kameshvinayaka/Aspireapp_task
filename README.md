# Aspire loan task
As per the task requirement project designed,
this application contains for 6 apis 

## for installation 
## Step 1 
clone the repository to you local 

## Step 2 
Edit .env file in the root directory and add you local database details as follow

###
DB_DATABASE=aspire_loan_api \
DB_USERNAME=root \
DB_PASSWORD=

## Step 3 
run the following command for database migration and seeding data into the database \

### php artisan migrate:fresh --seed

## step 4 

Check the postman collection for api reference link in bellow \

[Api Collection link](https://documenter.getpostman.com/view/1352680/UVeAuows)

## Step 5 
Start server using the command \
### php artisan serve

## For test login use below credientials 
email : kameshvinayaka@gmail.com \
password : 123456 
####
after login you will get a token for api access 
with that token you access loan apis

Note : token can be sent through header Authorisation berrer token

add accept Application/Json in header acccept for json response 



# Thank you