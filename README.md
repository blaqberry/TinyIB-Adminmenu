# TinyIB-Admin Menu
A custom admin menu for the TinyIB-mappy/9chan imageboard software.

9chan IB Software is coming soon dw abt it.

To use this make sure youre on the 2012 TinyIB-mappy if youre using that.
https://code.ivysaur.me/tinyib-mappy

TinyIB was not made by me and i am only making additions to it.

# requirements
well a webserver with tinyib-mappy 2012 and PHP 7.* or higher (lower versions prob work but i aint tested that yet)

# login.php

Login.php is a basic login prompt which leads to select.php.

# auth.php 
Checks if the login was correct and if it was you itll allow you access to the rest of the sites.

# select.php
select.php has a list of the three main tools.

# boardsadd.php
Lets you add new boards.

# boardsettings.php
universally edits all the settings.php's in */inc/

# headerupdate.php

headerupdate.php currently only works with 9chan itself, i will update it sometime soon.

It adds removes or edits header.html universally in every board to add the boards on the top of the page like on 4chan.

# SETUP

To set this up, setup normal TinyIB Mappy from https://code.ivysaur.me/tinyib-mappy
Heres a Tutorial: https://www.youtube.com/watch?v=uwNjIt12D6g
Place these: (the regular files from tinyib-mappy)
  /inc/
  /src/
  /thumb/
    boardscnv.php
    boardupdate.php
    import_locks.php
    index.php
    rss.php
    tinyib.db

Into a folder with the name /*/ (example /a/ for /a/nime)
and place that folder into /boards/ in the htaccess folder/root of the webserver.

it should look like this:

/boards/
        /*/
            /inc/
            /etc../

Make a blank board and add it to the /boards/ folder. Make sure the board folder is called "blank"

it should look like this:

/boards/
        /*/
            /inc/
            /etc../
        /blank/
            /inc/
            /etc../

Place the auth.php ; boardsadd.php ; boardsettings.php ; headerupdate.php ; login.php ; select.php 
in the root of the webserver (where /boards/ is located)

# Final Setup stuffs

Go into login.php with a texteditor and change the credentials to the login page. 
Ive labeled them CHANGEME
# How to use

When youre done with the setup just head to yourdomain.com/login.php and use your credentials.

Have fun!
