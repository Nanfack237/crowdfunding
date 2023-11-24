## Crowdfunding API

### Environment setup

Crowdfunding API is build with the following setup

* Programming Language [PHP v8.1](https://www.php.net/) with the framework [Laravel v10.x](https://laravel.com/)
* SQL Database is [MySQL 5.7](https://www.mysql.com/)
* Dependencies installer [Composer](https://getcomposer.org/)

### Deployment

To run the API,

* Start the MySQL server and create database with name crowdfunding
* MySQL Information
>   DB_DATABASE=crowdfunding
>
>   DB_USERNAME=root
> 
>   DB_PASSWORD=

* Here are the following commands to run the Crowdfunding-API

Open the terminal, 

>   git clone [https://github.com/Nanfack237/crowdfunding.git](https://github.com/Nanfack237/crowdfunding)

>   cd crowdfunding 

Install all the dependencies
>   composer install --no-interaction --no-ansi

Create the database table and procedures
>   php artisan migrate

Run the command to start the server
>   php artisan serve

To view all the end points and their documentations,
>   Locally - [http://localhost:8000/request-docs](http://localhost:8000/request-docs)
