# WordPress Base
Prepared WordPress project.

## Requirements
[Composer](https://getcomposer.org/), [NPM](https://www.npmjs.com/).

## Starting a new project
```
git clone git@github.com:DNepovim/wordpress-base.git
composer install
npm install
```
- create new database
- create `wp-config.php` based on `wp-config-sample.php`
```
gulp
```

## Template structure
Stored on `wp-content/themes/template`:
- `dist` - compiled files for production
- `src` - working directory
  - `images`
  - `scripts` 
  - `styles` - Less styles
    - `global`
    - `libs`
  - `templates` - Jade template
    - `componets`
    - `layouts`
    - `parts`

## Uses
[Gulp](http://gulpjs.com)

[Browsersync](https://www.browsersync.io)

[WordPress Packagist](https://wpackagist.org)

[Meta Box](https://metabox.io)

[HTML Editor Syntax Highlighter](https://wordpress.org/plugins/html-editor-syntax-highlighter)

[Tracy](https://tracy.nette.org/cs)
