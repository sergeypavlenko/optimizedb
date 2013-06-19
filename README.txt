Known issue on sites with Drupal - overgrowth table cache_form. The table stores the forms cache site and Drupal cache_form not clear when the Cron-tasks. cache_form may be large, in many times greater than the total size of all data in the database. Very often this problem, site owners know when they are running out of disk space, and the problem starts with the site.

The module "OptimizeDB" can for Cron to clean the table cache_form, the cleaning operation table is safe, even in event of a fall database server problems on the website will not be. Also, cleaning cache_form can run independently from the admin area.

The module "OptimizeDB" is also able to optimize the database tables and display their sizes. Optimizing the tables on Cron, can not perform, because it may cause problems with the database, so this setting is not available. However, you can configure the notification of the need to perform an optimization of tables. When the time comes, the site will display a message about the need to perform an optimization, the message can be hidden or perform optimization, and the message will disappear.

If a database table of your site work on the engine InnoDB, it is necessary from time to time to optimize the table. The fact that the disk on which the database resides, and tables may greatly be fragmented in the course of work and to get rid of of fragmentation, it is necessary to carry out optimization.

I advise you before optimize performance, if your server often have problems with the database server, make a backup of the database. The danger of losing data when the through module the optimization of the tables is the same as in the performance optimization through PHPMyAdmin.

Destination of the module OptimizeDB:

Cleaning the table cache_form
Cleaning the tables on Cron
Optimize database tables

Features:

The output size of the tables in the database
Working with MySQL and PgSQL
Similar projects:

DB Maintenance - differences https://drupal.org/node/2013040