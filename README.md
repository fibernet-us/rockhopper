An agile project management system  with dedicated support for Scrum and distributed software development.

Installation instruction:

1. Put "inc" to a directory outside your web root, e.g., if the web root is "/var/www/html", put "inc" under "www";

2. Edit the "$user" and "$pass" values in "k0m3kt.php" (and other fields if applicable) to match your DB and user credentials; 

3. Put "rockhopper" under your web root directory and make sure file permissions are correct (644 for files, 755 for directories);

4. Edit "connect.php" and "setup.php" for the correct path to "rockhopper.sql" and "k0m3kt.php" (i.e., the path of "inc");

5. Run the setup and test in your web browser, e.g. 

   http://localhost/rockhopper/setup.php
   http://localhost/rockhopper/user_test.php
   http://localhost/rockhopper/index.php

