# ServerSideSearch

This script allows you to quickly check out and search your server. You don't
need SSH access, just upload a single file (`sss.php`) and open it with your
browser. You can change the folders and do an extended search (with or without
RegExp). The search does a fulltext search (searches the content of the files).

## Setup

Open the `sss.php` file in an editor and write your password into the `$password`
variable (e.g. `$password = 'your password';`). You can hash your plain password
once with md5 (e.g. `$password = '513106c051f94528f1d386926aa65e1a';`) optionally.
You can change the username, too (the default username is `FirePanther`, case
insensitive).

After adding the password upload the `sss.php` file with your FTP client to your
server/webspace. If you're done using it you can remove it just for security
reasons.

The other files are just for developers. You don't have to concat the files,
with the already concated `sss.php` file you can start straightaway.

## Password

The password variable (@see Setup step) is required (can't be empty). There is
no default password just for security reasons (if you leave the default password
and forget the file on your server someone could see your files like your
database php file with the password).

Just for a little extra security you can set your password md5 hashed (once).
If you just want the quickly search some files on your server and remove it
you can add a short password without hashing it.

## Preview

[See some functionalities, click here for more screenshots](https://github.com/FirePanther/ServerSideSearch/tree/master/screenshots)  
[![ServerSideScript Preview](http://i.dv.tl/sss-preview.png)](https://github.com/FirePanther/ServerSideSearch/tree/master/screenshots)

# License

## The MIT License (MIT)
### Copyright (c) 2016 Suat Secmen

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.