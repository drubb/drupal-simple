# Composer template for Drupal projects

This project template should provide a kickstart for managing your site
dependencies with [Composer](https://getcomposer.org/). Based on the excellent [Drupal Composer Project](https://github.com/drupal-composer/drupal-project)
it introduces some modifications:

- Dev dependencies like Behat or PHPUnit are removed, you can add them yourself as needed.
- Public and private files are placed outside the web root.
- The config sync folder is placed outside the web root.
- During installation, a local settings file is used to point to modified files and config location.
- Creates "contrib" and "custom" folders for modules, themes, libraries, profiles and drush plugins by default.
- Adds the [Composer Parallel Install](https://github.com/hirak/prestissimo) plugin to speed up downloads.
- Adds the [Composer Merge](https://github.com/wikimedia/composer-merge-plugin) plugin to manage dependencies of custom modules.
- Adds the [Asset Packagist](https://asset-packagist.org) repository to manage npm / bower dependencies.

## Usage

First you need to [install composer](https://getcomposer.org/doc/00-intro.md#installation-linux-unix-osx).

After that you can create the project:

```
composer create-project -s dev drubb/drupal-simple some-dir
```

Replace 'some-dir' by your project root folder, or '.' for current directory.

If you get an outdated version of Drupal core, please check your php extensions. Latest versions of core
might require additional extensions. Here's an example how to check this:

```
composer why-not drupal/core:8.6.1
```

## What does the template do?

When installing the given `composer.json` some tasks are taken care of:

* The current stable Drupal 8 Version will be installed in the `web`-directory.
* Autoloader is implemented to use the generated composer autoloader in `vendor/autoload.php`,
  instead of the one provided by Drupal (`web/vendor/autoload.php`).
* Modules (packages of type `drupal-module`) will be placed in `web/modules/contrib/`
* Themes (packages of type `drupal-theme`) will be placed in `web/themes/contrib/`
* Profiles (packages of type `drupal-profile`) will be placed in `web/profiles/contrib/`
* Libraries (packages of type `drupal-library`) will be placed in `web/libraries/contrib/`
* Drush plugins (packages of type `drupal-drush`) will be placed in `drush/contrib`
* Creates default writable versions of `settings.php`, `settings.local.php` and `services.yml`.
* Creates `sites/default/files`-directory, using a symlink to `files/public`
* Latest version of Drush is installed locally for use at `vendor/bin/drush`.
* Latest version of Drupal Console is installed locally for use at `vendor/bin/drupal`.

## Updating Drupal Core

This project will attempt to keep all of your Drupal Core files up-to-date; the
project [drupal-composer/drupal-scaffold](https://github.com/drupal-composer/drupal-scaffold)
is used to ensure that your scaffold files are updated every time drupal/core is
updated. If you customize any of the "scaffolding" files (eg .htaccess or robots.txt),
you may need to merge conflicts if any of your modified files are updated in a
new release of Drupal core.

Follow the steps below to update your core files (run this commands in your project root):

1. Run `composer update drupal/core --with-dependencies` to update Drupal Core and its dependencies.
2. Run `git diff` to determine if any of the scaffolding files has changed.
   Review the files for any changes and restore any customizations to e.g.
  `.htaccess` or `robots.txt`. Commit your changes to git.

## Installing contributed modules

You can add contributed modules using the composer *require* command. Example:

```composer require drupal/devel```

Run this command in your project root.


## Updating contributed modules

You can update contributed modules using the composer *update* command. Example:

```composer update drupal/devel --with-dependencies```

Run this command in your project root.

## Using Composer with custom modules

You can add Composer support to your custom modules using the following steps (run these commands in your module folder):

* Add a basic composer.json file to your module's folder: `composer init --n`
* Add dependencies for your custom module: `composer require mpdf/mpdf --no-update`

In this example, the custom module is using the mpdf PHP library. The flag '--no-update' tells
Composer not to download the library to your custom module's folder, as we need to store it in the
central vendor folder of our project. The [Composer Merge](https://github.com/wikimedia/composer-merge-plugin) plugin takes care of this, we'll just need
to update our main Composer manifest (composer.json):

```
composer update --lock
```
Run this command in your project root. This will add our custom module's dependency to the main Composer manifest.

### How can I apply patches to downloaded modules?

If you need to apply patches, you can do so with the
[composer-patches](https://github.com/cweagans/composer-patches) plugin.

To add a patch to Drupal module foobar insert the patches section in the extra
section of composer.json, in your project root:

```json
"extra": {
    "patches": {
        "drupal/foobar": {
            "Patch description": "URL to patch"
        }
    }
}
```
