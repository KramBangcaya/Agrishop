## How to Install Application

1.  composer install or composer update

2.  npm install

3.  cp .env.example .env

4.  php artisan key:generate

5.  php artisan migrate

6.  php artisan db:seed
7.  change DB_DATABASE=agrishop in .env

8.  create database name agrishop

9.  php artisan passport:install

10. php artisan key:generate

11. Register to have Access

12. Default Account

    demo users
    name = Example User
    email = test@example.com

        name = Example Admin User
        email = admin@example.com

        name = Example Super-Admin User
        email = superadmin@example.com

    Default Password = password

php artisan storage:link for image storage to public
#   S w i f t D r o p 
 
 #   S w i f t D r o p 
 
 





CREATE TABLE orders (
  id int(11) NOT NULL,
  buyer_name varchar(255) DEFAULT NULL,
  product_name varchar(255) DEFAULT NULL,
  timedate timestamp NULL DEFAULT NULL,
  seller_name varchar(255) DEFAULT NULL,
  seller_number varchar(20) DEFAULT NULL,
  seller_address text DEFAULT NULL,
  order_status varchar(50) DEFAULT NULL,
  photo varchar(255) DEFAULT NULL,
  totalPayment int(11) DEFAULT NULL,
  product_quantity int(11) DEFAULT NULL,
  seller_id int(11) DEFAULT NULL,
  buyer_address text DEFAULT NULL,
  reason_cancel text DEFAULT NULL
)
