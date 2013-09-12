An agile project management system  with dedicated support for Scrum and distributed software development.

Installation instruction:

1. Put "rockhopper.sql" and "k0m3kt.php" to a directory outside your web root, e.g., if it's "/var/www/html", make a directory "inc" under "www" (sudo mkdir inc) and put those two files there;

2. Put other PHP files to your rockhopper web directory, and change their file permissions to be 644 (sudo chmod 644 *.php);

3. If the directory you created in step 1 is not "../../inc", i.e., a directory called "inc" that is two levels up from the rockhopper directory, you need to update the path to "rockhopper.sql" and "k0m3kt.php" in the PHP files;

4. Change the "$user" and "$pass" values in "k0m3kt.php" (and other fields if applicable) to match your DB and user credentials; 

5. Run the setup and user test in your web browser, e.g. 

   http://localhost/rockhopper/setup.php
   http://localhost/rockhopper/user_test.php

   you can also check your database via mysql or phpmyadmin to see if things are working correctly.

