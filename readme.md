# Database module

This module is based on the Custom Tables library ([source](https://github.com/rubengc/CT)).

It has been converted to use a unique prefix `tdb_`, to prevent conflict with other plugins that may be using the original library. This lets us customize and extend this module as needed.

## Install

Create or add to the plugin's `composer.json`

```json
{
  "repositories": [{
    "type": "vcs",
    "url": "git@bitbucket.org:/tangibleinc/tangible-database-module.git"
  }],
  "require": {
    "tangible/database": "dev-master"
  },
  "minimum-stability": "dev"
}
```

Run on command line

```sh
composer install
```

Later, regularly keep module up to date

```sh
composer update
```


## Load

At the top of the plugin entry file

```php
require __DIR__ . '/vendor/tangible/database/index.php';
```


## Usage

No documentation yet - for now, see `example.php`.

