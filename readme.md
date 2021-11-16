# Database module

This module is based on the Custom Tables library ([source](https://github.com/rubengc/CT)).

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

No documentation yet - for now, see `lib/example.php`.

