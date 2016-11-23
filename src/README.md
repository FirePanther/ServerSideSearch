Rename `config.sample.php` into `config.php` and edit the file. Change the
username and write your password. Instead of a clear text password you can add
an md5 checksum of your password.

After editing these files you have to concat it with the `../concat.php` script
or with running `gulp` (don't forget `npm i` before).

If you just want to edit your username and password without concating just edit
the `../sss.php` script.