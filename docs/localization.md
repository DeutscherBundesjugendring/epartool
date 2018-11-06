# Localization

The application is fully translated. The translations are placed at several locations:


## *.po files
* `languages/<language_code>/admin.po` for strings used in administration
* `languages/<language_code>/web.po` for strings used in the public site

The `*.po` files can be easily edited by the [POEdit](https://poedit.net/) software. When saving the file in POEdit, a `*.mo` file witht he same name as the edited file is automatically generated. This is the compiled version of the translations and this is where the application looks for translations. Both files must always be updated in the repository at the same time.

Neither the `*.po` nor the `*.mo` files work with git. Attempts to merge leave them in an inconsistent state. The usual git workflow therefore does not work and careful management of the translation process must take place. Essentially **only one person can work at the `*.po` files at any given time**.

## Zend_Validate
* `languages_zend/<language_code>/Zend_Validate.php`

Many languages are supported by the Zend framework natively. Some are not. Those that are not have their validation messages translations supplied from the application. These are kept as plain old PHP arrays and can be edited by any text editor.

## Database
* `data/create-project-<labguage_code>.sql`

The application supports serving multiple projects in different languages from one codebase and one database. All localized strings must be therefore project specific.

There is one file per language. These files can be edited by any text editor, but care must be taken as these scripts are sent straight to database and they can break things if they contain errors.


 


