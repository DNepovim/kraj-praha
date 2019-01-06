
# Junák - český skaut
We are a part of the largest non-formal educational NGO for children and youth in the Czech Republic [Junák - český skaut](https://www.skaut.cz/en).

This repo contains the website for scouts from Prague - capital city of the Czech Republic - [praha.skauting.cz](https://praha.skauting.cz) (czech only).

All contributors are welcome. Send me an e-mail to [nik@skaut.cz](mailto://nik@skaut.cz).

All issue reports and feature requests are welcome to.

## Run project

Requirements: PHP stack,  [Composer](https://getcomposer.org), [mango-cli](https://github.com/manGoweb/mango-cli)

```sh
git clone git@github.com:DNepovim/kraj-praha.git
cd kraj-praha
composer install
npm install
mango dev
```

Additional steps:
- Create a new database
- Create your `config/config.local.neon` based on `config.local.sample.neon`.
- Make directories `log/`,  `temp/`, `public/wp-content/*` writeable for web process

## Project structure

* `app` - Nette MVC application
* `config` - All configuration in one place
* `public` - Public directory to be set as document_root dir
  * `assets` - compiled theme assets, do not edit them here
  * `wp-content` - WP content directory
  * `wp-core` - WP distribution installed via composer
* `theme` - main WP theme with all templates and original assets
* `vendor` - composer packages

## Theme development

You are going to spent the most of your time in the `theme` directory. Follow these code architecture instructions to avoid a loss of your sanity:

* Use `index.php` and other WP template files as controllers (php code only). Controller should define and fill a context for an actual template.
* Use templates `views/*.latte` as views. All the HTML chunks belong here. Work with given context only and do not execute unnecessary php code.
* Assets source directories are `styles`, `scripts` and `images` and the [mango-cli](https://github.com/manGoweb/mango-cli) compiles them to the `public/assets` distribution directory.

## Manage WP plugins

```sh
composer install wpackagist-plugin/PLUGINNAME
```

Thanks to [wpackagist](http://wpackagist.org) repository, you can install all plugins and themes from [official WordPress directory](http://plugins.svn.wordpress.org) via composer.

Installed plugins are used as [mu-plugins](http://codex.wordpress.org/Must_Use_Plugins), which cannot be disabled or removed from administration.
Beware: not all plugins can work that way, especially ones that need some sort of activation initialization steps.

Applications deployed to production servers cannot install, update, or remove plugins at all. All changes must be tested, versioned and properly deployed instead.

## Based on MangoPress
Fine tuned WordPress structure with the horse power of the Nette Framework, all utilizing the [Composer](https://getcomposer.org) and [mango-cli](https://github.com/manGoweb/mango-cli).

Copyright 2014 by [manGoweb s.r.o.](http://www.mangoweb.cz) Code released under [the MIT license](LICENSE).
