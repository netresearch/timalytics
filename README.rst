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

Access
======

Timalytics has no login of its own. Which users' data a visitor sees is set
in ``config.php``:

- ``arAllowedUsers``: only the listed users. Requiring
  ``src/timetrackersessionuser.php`` in ``config.php`` fills it with the user
  logged into the timetracker and, for a project leader, the members of their
  teams. The file reads the timetracker's PHP session from
  ``../../app/cache/*/sessions/`` relative to ``src/``, so Timalytics has to
  run in a subdirectory of a timetracker installation that keeps its sessions
  there.
- ``allowAllUsers = true``: every user. Use this only where the web server
  restricts who can reach Timalytics.
- Neither: only the user that ``arIpUser`` maps the visitor's IP address to.
  Without such a mapping every page answers "Invalid user".

The same rule applies to the user selection, the standup tool and the
bookings listed on the ticket page. The project and support pages total the
bookings of all users per customer and project, so they are available only
with ``allowAllUsers = true`` and answer "Access denied" otherwise.

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


Feiertage
=========
Aktualisierung der Feiertagsdatei:

#. Download der aktuellen .ics-Datei von
   https://www.schulferien.org/deutschland/ical/ nach ``data/``
#. ``./scripts/import-ical.php``
#. ``data/`` committen
