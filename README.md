# Groundskeeper

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Build Status][ico-travis]][link-travis]
[![Coverage Status][ico-scrutinizer]][link-scrutinizer]
[![Quality Score][ico-code-quality]][link-code-quality]
[![Total Downloads][ico-downloads]][link-downloads]

Groundskeeper will clean your weedy HTML.

## Install

Via Composer

``` bash
$ composer require kevintweber/groundskeeper
```

## Usage

Here is a simple example:

``` php
use Groundskeeper\Groundskeeper;

$groundskeeper = new Groundskeeper(array(
    'output' => 'pretty'
));
$groundskeeper->setLogger($myLogger); // Will log changes to your HTML.

$cleanHtml = $groundskeeper->clean($dirtyHtml);
```

### Options
* clean-strategy
  * Options: "none", "standard", "aggressive"; Default: "standard"
  * Describes how the HTML document will be cleaned.
    * none - No cleaning will be done.
    * standard - Standard compliant HTML will be output.
    * aggressive - Like "standard" plus non-standard elements will be removed.
* error-strategy
  * Options: "none", "throw", "fix"; Default: "fix"
  * Describes how Groundskeepers will handle malformed HTML.
    * none - Nothing will happen when malformed HTML is encountered.
    * throw - Will throw an exception when malformed HTML is encountered.
    * fix - Will attempt to fix the malformed HTML.
* indent-spaces
  * Options: integer greater than or equals to 0; Default: 4
  * The number of spaces for indentation when using pretty output.
* output
  * Options: "compact", "pretty"; Default: "compact"
  * Describes how the HTML will be output.
    * compact - Will remove all whitespace between elements, and will set "indent-spaces" to 0.
    * pretty - One element per line with indentation. Handy for debugging.
* remove-types
  * Options: "none" or comma seperated list of any of the following: "cdata", "comment", "doctype", "element", "text"; Default: "cdata,comment"
  * Describes which token types will be removed from the output.

## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Testing

``` bash
$ phpunit
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email kevintweber@gmail.com instead of using the issue tracker.

## Credits

- [Kevin Weber][link-author]
- [All Contributors][link-contributors]

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/kevintweber/groundskeeper.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/kevintweber/Groundskeeper/master.svg?style=flat-square
[ico-scrutinizer]: https://img.shields.io/scrutinizer/coverage/g/kevintweber/Groundskeeper.svg?style=flat-square
[ico-code-quality]: https://img.shields.io/scrutinizer/g/kevintweber/Groundskeeper.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/kevintweber/groundskeeper.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/kevintweber/groundskeeper
[link-travis]: https://travis-ci.org/kevintweber/Groundskeeper
[link-scrutinizer]: https://scrutinizer-ci.com/g/kevintweber/Groundskeeper/code-structure
[link-code-quality]: https://scrutinizer-ci.com/g/kevintweber/Groundskeeper
[link-downloads]: https://packagist.org/packages/kevintweber/groundskeeper
[link-author]: https://github.com/kevintweber
[link-contributors]: ../../contributors
