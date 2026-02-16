[![PHP Composer](https://github.com/chamber-orchestra/breadcrumbs/actions/workflows/php.yml/badge.svg)](https://github.com/chamber-orchestra/breadcrumbs/actions/workflows/php.yml)
[![PHP CS Fixer](https://img.shields.io/badge/PHP%20CS%20Fixer-enabled-brightgreen.svg)](https://github.com/PHP-CS-Fixer/PHP-CS-Fixer)
[![Code Style: PER-CS](https://img.shields.io/badge/code%20style-PER--CS-blue.svg)](https://www.php-fig.org/per/coding-style/)
[![Code Style: Symfony](https://img.shields.io/badge/code%20style-Symfony-black.svg)](https://symfony.com/doc/current/contributing/code/standards.html)
[![PHPStan Level max](https://img.shields.io/badge/PHPStan-level%20max-brightgreen.svg)](https://phpstan.org/)
[![PHP 8.5](https://img.shields.io/badge/PHP-8.5-777BB4.svg)](https://www.php.net/)
[![Symfony 8.0](https://img.shields.io/badge/Symfony-8.0-000000.svg)](https://symfony.com/)
[![Latest Stable Version](https://poser.pugx.org/chamber-orchestra/breadcrumbs/v)](https://packagist.org/packages/chamber-orchestra/breadcrumbs)
[![License](https://poser.pugx.org/chamber-orchestra/breadcrumbs/license)](https://packagist.org/packages/chamber-orchestra/breadcrumbs)

# Breadcrumbs

A lightweight, iterable breadcrumb collection for Symfony applications. Implements `ArrayAccess`, `Iterator`, and `Countable` for seamless integration with Twig templates and controllers.

Part of the [Chamber Orchestra](https://github.com/chamber-orchestra) ecosystem.

## Requirements

- PHP ^8.5
- Symfony HttpFoundation ^8.0

## Installation

```bash
composer require chamber-orchestra/breadcrumbs
```

## Usage

### Adding breadcrumbs manually

```php
use ChamberOrchestra\Breadcrumbs\Breadcrumbs;

$breadcrumbs = new Breadcrumbs();
$breadcrumbs
    ->addCrumb('Home', 'app_home')
    ->addCrumb('Products', 'app_products', ['category' => 'books'])
    ->addCrumb('Current Page');
```

### Adding from a Symfony Request

```php
$breadcrumbs->addRequestCrumb('Current Page', $request);
```

Extracts `_route` and `_route_params` from the request attributes automatically.

### Prepending a crumb

```php
$breadcrumbs->addCrumb('Home', 'app_home', [], prepend: true);
```

### Using a closure

```php
$breadcrumbs->addCrumbsClosure(function (Breadcrumbs $crumbs) {
    $crumbs->addCrumb('Home', 'app_home');
    $crumbs->addCrumb('About', 'app_about');
});
```

### Iterating in Twig

```twig
<nav aria-label="breadcrumb">
    <ol>
        {% for crumb in breadcrumbs %}
            <li>
                {% if crumb.route %}
                    <a href="{{ path(crumb.route, crumb.routeParams) }}">{{ crumb.name }}</a>
                {% else %}
                    {{ crumb.name }}
                {% endif %}
            </li>
        {% endfor %}
    </ol>
</nav>
```

### Array access

```php
$first = $breadcrumbs[0];
$total = count($breadcrumbs);
```

## License

MIT
