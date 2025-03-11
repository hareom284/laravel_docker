
# TO setup DDD Project

This project follows Domain-Driven Design principles and patterns. Below are instructions to help you set up the codebase and architecture.




## Requirement

 - php  v8.1
 - node ^18.8


## Installation

Here is a step-by-step guide for setting up the project's installation and architecture:

***This project follows Domain-Driven Design principles. Follow these steps to set it up:***



<b>1. Install Dependencies</b>

Run `composer install` to install PHP dependencies.

<b>2. Configure Environment</b>

Copy the example env file:


```
 cp .env.example .env
```

Open .env and update the database connection credentials and settings.

<b>3. Import Database </b>

Import the MySQL database from the `.sql` file provided. This contains the starting schema and data.


<b>4. Update Routes</b>

Open `vendor/hansschouten/laravel-pagebuilder/routes/web.php`

Delete the code inside web.php

<b>5. Add CSRF Meta Tag</b>

In `vendor/hansschouten/phpagebuilder/src/Modules/GrapesJS/resources/views/layout.php`, add this script above the closing `</body>` tag:

```

<script>
  $(document).ready(function() {

  // CSRF token setup

  var meta = document.createElement('meta'); 
  meta.setAttribute('name', 'csrf-token');
  meta.setAttribute('content', localStorage.getItem("_token"));
  var head = document.getElementsByTagName('head')[0];
  head.appendChild(meta);

  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

  });
</script>


```

<b>6. Remove session_start</b>

Open `ops-api.leaptech.sg/vendor/hansschouten/phpagebuilder/src/PHPageBuilder.php`

Comment away session_start(); in line 56

<b>7. Run Migrations & Seeders</b>

Fresh migrate and seed the database:

```
php artisan migrate:fresh --seed

```

<b>8. Link storage with public folder</b>

Run php artisan storage:link

<b>9. Setup plugin for PDF generation</b>

Download wkhtmltopdf from the official website

https://wkhtmltopdf.org/downloads.html

For Windows, add SYSTEM_ROOT="C:/Windows" to .env

<b>10. Start Development Servers</b>

Run the PHP development server:

```
 php artisan serve

```

And in a separate terminal, run:

```
yarn dev

```

That will start the frontend asset build process.

The app should now be running on `http://localhost:8000`














    