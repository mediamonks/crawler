[![License](https://poser.pugx.org/mediamonks/rest-api-bundle/license)](https://packagist.org/packages/mediamonks/rest-api-bundle)

# MediaMonks Crawler

This tool allows you easily to crawl a website and get a DOM object for every url that was found.
We use this to crawl our own sites regardless of it was generated with server and/or client side content by using a Prerender.io client.
The resulting data can be used for creating a full site search and SEO purposes.

## Highlights

- Ships with Prerender & Prerender.io clients, uses Goutte by default
- Supports any Symfony BrowserKit client
- Supports both whitelisting and blacklisting of urls
- Supports url modification which allow you to prevent duplicates based on minor url differences
- Implements the [PSR-3 Logger Interface](http://www.php-fig.org/psr/psr-3/)

## Documentation

Documentation and examples can be found in the [/doc](/doc) folder.

## System Requirements

You need:

- **PHP >= 5.5.0**

To use the library.

## Install

Install this package by using Composer.

```
$ composer require mediamonks/crawler
```

## Security

If you discover any security related issues, please email robert@mediamonks.com instead of using the issue tracker.

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
