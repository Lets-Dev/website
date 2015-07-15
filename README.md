# "Let's Dev !" website

## What is it ?

This project is the source code of the website of the association "Let's Dev !", which will be used by the members of the association to manage their teams, submit their projects, among other things

## What is "Let's Dev !" ?

"Let's Dev !" is an association that were created in and acts inside IG2I, an engineer school in Lens, France. Its goal is to promote teamwork and learning of new programming languages by organizing programming "challenges" between teams of 3 to 10 people.

## Licensing

This project is licensed under the Creative Commons Attribution-NonCommercial-ShareAlike 4.0 International License. For more info, please read the LICENSE file

## Documentation

The documentation for this project is available in the "doc" folder (to be opened in an internet navigator)

**BE CAREFUL :** All school years are registered by the year at the beginning of the school year (ex: 2015 for 2015-2016)

## Installation

In order to correctly use this website, you need to create an include/credentials.php file and set your DB username and password, and the SALT key.

```php
<?php
    $db = new PDO("mysql:host=sql.sofianeg.com;dbname=lets-dev","","");

    $salt = '';
?>
```

**BE CAREFUL :** Make sure to not upload the credentials.php file with your own credentials !

## Authors

The authors of this project are members of the current "Let's dev !" association. For a precise list of all the authors, see the AUTHORS file

## Contact

For any kind of contact, you can join the association by mailing us at lets-dev@ig2i.fr

