<?php
/*
  Debug Mode
*/
define('DEBUG_MODE', true);

/*
  System Settings
*/
define('TIMEZONE', 'Asia/Tokyo');
define('DATABASE_SOURCE', 'sqlite:../database.sqlite3');

/*
  Response Settings
*/
define('PAGE_MAX', 1);

/*
  User Settings
*/
define('LOGIN_ID', 'admin');
define('LOGIN_PASSWORD', 'password');

define('LOGIN_PASSWORD_HASH', password_hash(LOGIN_PASSWORD, PASSWORD_DEFAULT));