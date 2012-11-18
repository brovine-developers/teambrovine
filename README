Mammary Gland Gene Database.
=========

**_Contributors:_ Sterling Hirsh, Therin Irwin, Trevor DeVore, Ryan Schroeder**

To get this to work, you need to add a "passwd.php" file in the
application/config directory. This file contains the configuration settings for
your local MySQL server. It has the following structure:

      <?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

      // Passwd file for database permissions.
      // DO NOT COMMIT

      $active_group = 'default';
      $active_record = TRUE;

      $db['default']['hostname'] = 'localhost';
      $db['default']['username'] = 'your_username';
      $db['default']['password'] = 'your password';
      $db['default']['database'] = 'yourDatabaseName';
      $db['default']['dbdriver'] = 'mysql';
      $db['default']['dbprefix'] = '';
      $db['default']['pconnect'] = TRUE;
      $db['default']['db_debug'] = TRUE;
      $db['default']['cache_on'] = FALSE;
      $db['default']['cachedir'] = '';
      $db['default']['char_set'] = 'utf8';
      $db['default']['dbcollat'] = 'utf8_general_ci';
      $db['default']['swap_pre'] = '';
      $db['default']['autoinit'] = TRUE;
      $db['default']['stricton'] = FALSE;

      ?>

Features
--------
 - **_Cache Control:_** If a Javascript file is changed on the server, it will show up immediately on the client despite caching attempts by the browser.
