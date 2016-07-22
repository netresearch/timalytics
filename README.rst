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
#. create database and tables from data/tables.sql
#. copy over config.dist.php to config.php and fill in

- http://localhost/

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
