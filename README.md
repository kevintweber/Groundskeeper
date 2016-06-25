# Groundskeeper

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Build Status][ico-travis]][link-travis]
[![Coverage Status][ico-scrutinizer]][link-scrutinizer]
[![Quality Score][ico-code-quality]][link-code-quality]

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
$groundskeeper->setLogger($myLogger); // Optional: will log changes to your HTML.

$cleanHtml = $groundskeeper->clean($dirtyHtml);
```

### Options
* `clean-strategy`: Describes how the HTML document will be cleaned.
  * Options: `none`, `lenient`, `standard`, `aggressive`; Default: `standard`
    * `none` - No cleaning will be done.
    * `lenient` - Like `standard` except no markup will be removed.
    * `standard` - Standard compliant HTML will be output.  Unfixable malformed HTML will be removed.
    * `aggressive` - Like "standard" plus non-standard elements will be removed. (TODO)
* `element-blacklist`: Describes which elements will be removed from the output.
  * Options: Comma seperated list of elements; Default: `` (empty list)
* `indent-spaces`: The number of spaces for indentation when using pretty output.
  * Options: integer greater than or equal to 0; Default: 4
* `output`: Describes how the HTML will be output.
  * Options: `compact`, `pretty`; Default: `compact`
    * `compact` - Will remove all whitespace between elements, and will set `indent-spaces` to 0.
    * `pretty` - One element per line with indentation.  Handy for debugging.
* `type-blacklist`: Describes which token types will be removed from the output.
  * Options: Comma seperated list of any of the following: `cdata`, `comment`, `doctype`, `element`, `php`, `text`; Default: `cdata,comment`

## Todo
1. Implement ```aggressive``` cleaning strategy.
2. ```pretty``` output should inline certain elements.
3. Add option to remove / sanitize all JS attributes.

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
