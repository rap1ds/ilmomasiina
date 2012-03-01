# ILMOMASIINA
_Originally by Mikko Koski_

These instructions are mainly for AYY-related groups who are running their
Ilmomasiina installation on AYY's Otax server.


## SAFE UPGRADE FROM EXISTING ILMOMASIINA 2.0 INSTALLATION

1. Move the current version to a backup directory for later reference, e.g.

    mv ilmomasiina ilmomasiina_2011

2. Unpack this new version of Ilmomasiina to either the final directory you
   are planning to use (if you are sure there aren't users online at the
   moment) or to a new temporary directory.

   For example to use directory "ilmomasiina_2012":

    unzip ilmomasiina-xxx.zip
    mv ilmomasiina-xxx/src ilmomasiina_2012

   (N.B.! Check the directory structure and .zip file name specific to the
   version you have downloaded before blindly running these commands.)

3. Update the database name, username and password to new Ilmomasiina's
   file "DBinterface.php". (You can find this information in the same file on
   your old Ilmomasiina installation.)

4. In file "classes/Configurations.php" write your
   * installation root dir (you can find this out with command "pwd",
     e.g. "/home/yourgroup/www-data/ilmomasiina/"),
   * web root (e.g. "/ilmomasiina"), and
   * admin email (which will be printed on the footer of every page as
     contact information).

   (Note that, if you are using a temporary test directory name write that
   here now, and change it later, when you have tested the installation and
   moved it to the final place.)

5. If you want, you can also change the "From" address of outgoing
   confirmation emails in "classes/ConfirmationMail.php".

6. Note that the current version of the Ilmomasiina uses Apache server's
   extention called "mod_rewrite" to make the URLs more user friendly.
   At Otax you might have to ask the admins (tietotekniikka ÄT ayy.fi) to
   enable "AllowOverride FileInfo" for your Otax account, if the new
   Ilmomasiina's links do not work.

7. When you have tested that the installation works, move the new ilmomasiina
   to the directory you want (e.g. `mv ilmomasiina_2012 ilmomasiina`) and make
   changes to the configuration files you edited at step 4 (unless you already
   installed Ilmomasiina to the final directory).

8. Test that both signing up and admin* interface work. Whooh, done!


* = If you have problems with authenticating to ilmomasiina/admin, check that
the .htpasswd file you are using is in a place where the Ilmomasiina finds it.
Or alternatively, write the full path to "classes/AdminPasswordProtector.php"
after text "$authenticationOk = ...", e.g.
`$this->http_authenticate($username, $password, '/home/yourgroup/.htpasswd');`


## DB LIBRARIES FOR PHP 5.3 COMPATIBILITY

(In early 2012 this was already OK on Otax server. But on other servers you
might have to take care of these.)

PHP 5.3 compatibility requires installing latest betas of MDB2:

pear install MDB2-2.5.0b3
pear install channel://pear.php.net/MDB2_Driver_mysql-1.5.0b3
