**********
Timalytics
**********

Analytics frontend for Netresearch Timetracker.

Requirements
============

Netresearch Timetracker installation.
- https://github.com/netresearch/timetracker

Installation
============

#. Checkout from Git repository
#. run composer install
#. create database and tables from data/tables.sql - if not using docker-compose
#. copy over config.dist.php to config.php and fill in

Starting
========

Docker
------

Starts a single PHP 7 Container as web server with Timalytics sources::

    $ run ./run.sh


DB server for Timalytics must be prepared manually.

- http://localhost:8888/

Docker + docker-compose
-----------------------

Starts a single PHP 7 Container as web server with Timalytics sources and a
linked MariaDB server with Timalytics database and tables prepared::

    $ docker-compose up web

- http://localhost:8888/

Access Timalytics database
..........................

You can use phpMyAdmin to access your Timalytics database::

    $ docker-compose up pma

- http://localhost:8080/

Features
========

Monthly view
------------

Displays a per user monthly view.

- http://localhost/index.php

Usually utilized to review work hours done.

Project view
------------

Displays all projects currently active with booked times.

- http://localhost/projects.php

Support
-------

Displays all tickets relating to support projects.

- http://localhost/support.php

Standuptool
-----------

Displays per user activities since last standup.

- http://localhost/standup.php

Mood
....

Am Ende der Beschreibung ein ``#c``, ``#l``, oder ``#s`` (cool, so lala, sucks)
schreiben.
Dann wird die Zeile entsprechend eingefärbt.
