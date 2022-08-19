# Laz0r AutoLoader
A [PSR-4] compliant SPL auto-loader for PHP 7.4 and 8.x

## Installation
Download a copy and extract it somewhere in your project, or
clone the git repository:

```sh
git clone 'https://github.com/Laz0rs/autoloader.git'
```

## Usage
The provided `bootstrap.php` script allows auto-loading PSR-4 namespaces using
any iterable, by calling the `setup` method. For example:

```php
(require "path/to/bootstrap.php")
    ->setup([
        ["My\\Namespace", "path/to/src/"],
        ["Vendor\\Widget", "path/to/vendor/widget/"],
    ]);
```

Setup requires arrays which have a namespace string as index 0 and a path
string as index 1. Namespaces may appear more than once, for example:

```php
[
    ["Psr\\Http\\Message", "lib/psr/http-message/src/"],
    ["Psr\\Http\\Message", "lib/psr/http-factory/src/"],
]
```
Namespaces need not be suffixed with a backslash.
A path may be omitted by passing the emtry string.
Paths to directories must be suffixed with a directory separator.
The script returns an instance of `Laz0r\AutoLoader\Container`.
See the file `src/Container.php` for details.

## Hacking
The coding standard for this project is [PSR-2-R].
Please also use PHPUnit and Psalm.

## License
The code in this project is licensed under the MIT license. See `COPYING`

[PSR-4]: <https://www.php-fig.org/psr/psr-4/>
[PSR-2-R]: <https://github.com/php-fig-rectified/fig-rectified-standards/blob/master/PSR-2-R-coding-style-guide.md>
