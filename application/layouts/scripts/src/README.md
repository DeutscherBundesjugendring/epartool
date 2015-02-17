Email Templates
===============

## Prerequisities

**Behold!** Ruby and [Premailer gem](https://github.com/premailer/premailer) are required:

```
$ gem install premailer
```

## Editing Template

Source templates are located in `application/layouts/scripts/src/`. All PHP tags must be enclosed in HTML comments since
Premailer doesn't correctly recognize PHP syntax.

For example, this:

```
<!-- <?=$this->bodyHtml;?> -->
```

Will output to this:

```
<?=$this->bodyHtml;?>
```

## Generating Final PHTML Template

Once you are finished with editing the source template, run the following Grunt task to build final PHTML template: 

```
$ grunt mail
```
