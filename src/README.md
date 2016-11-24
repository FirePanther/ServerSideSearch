Rename `config.sample.php` into `config.php` and edit the file.
Change the username and write your password. Instead of a clear text password
you can add an md5 checksum of your password.  
If you're running `grunt dev` the password will be added to the `sss.php` (and
changes in the `src` folder will be watched, good for developing and testing),
if you run `grunt` an `sss.php` file will be generated without the password
(good for committing and pushing).

After editing these files you have to concat it with the `../concat.php` script
or with running `gulp` (don't forget `npm i` before).

If you just want to edit your username and password, without developing and
concating, just edit the `../sss.php` file instead.