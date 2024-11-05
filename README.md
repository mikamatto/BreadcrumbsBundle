# BreadcrumbsBundle

`Mikamatto\BreadcrumbsBundle` is a Symfony bundle that provides easy-to-configure breadcrumbs functionality using a YAML file. It supports a simple, chain-based breadcrumb structure and includes caching for optimized performance.

## Installation

Use Composer to install the bundle:

```bash
composer require mikamatto/breadcrumbs-bundle
```


## Configuration
By default, the breadcrumbs configuration file should be located at `config/packages/breadcrumbs.yaml`. The structure inside this file should start with a breadcrumbs_bundle root key, as follows:
```yaml
breadcrumbs_bundle:
  routes:
    app_page_index:
      label: 'Pages'

    app_page_edit:
      label: 'Edit Page'
      chain: ['app_page_index']

    app_page_new:
      label: 'New Page'
      chain: ['app_page_index']

    app_page_show:
      label: 'Page Details'
      chain: ['app_page_index']
```
Each route is defined by:
- **label**: The breadcrumb label which will be rendered in the breadcrumb.
- **chain**: Optional. Defines the chain of preceding routes (as an array of route names) which will be rendered recursively.
  
### Custom File Path
If you prefer a custom file name, you can specify it in your configuration file as follows:
```yaml
breadcrumbs_bundle:
  breadcrumbs_file: '%kernel.project_dir%/config/custom_breadcrumbs.yaml'
  routes:
    ...
```
**Note:** If you use a custom filename but do not specify it in the configuration, the bundle will work; however, caching will default to config/packages/breadcrumbs.yaml, which may lead to outdated breadcrumbs being displayed. Defining the path explicitly ensures cache invalidation occurs correctly.

This approach maintains flexibility while ensuring caching behaves as expected for any filename or location.

## Usage

### 1. Twig Function

Use the Twig function `getBreadcrumbs()` to retrieve the breadcrumb chain for the current route.
```twig
{% for breadcrumb in getBreadcrumbs() %}
    <a href="{{ breadcrumb.url }}">{{ breadcrumb.label }}</a>
    {% if not loop.last %} > {% endif %}
{% endfor %}
```
This will output something like:
```
Home > Pages > Page Details
```
Note: To keep things lighter, the common root ‘Home’ can be hardcoded in Twig instead of being defined in each route chain.

### 2. Service: BreadcrumbsProcessor

You can also access the underlying service: the bundle exposes a `BreadcrumbsProcessor` service, which generates breadcrumb arrays for a given route.

Inject the service (Mikamatto\BreadcrumbsBundle\Service\BreadcrumbsProcessor) and call generateBreadcrumbs():
```php
public function index(BreadcrumbsProcessor $breadcrumbsProcessor)
{
    $breadcrumbs = $breadcrumbsProcessor->generateBreadcrumbs('app_page_show', ['id' => 1]);
    // Result based on example YAML above:
    // [
    //     ['label' => 'Pages', 'url' => '/page/'],
    //     ['label' => 'Page Details', 'url' => '/page/1']
    // ]
}
```

## Caching

The bundle caches the breadcrumbs YAML file for performance. When the YAML file changes, the cache automatically invalidates and updates. A dedicated cache pool is used, defined in your bundle’s service configuration.

## Example Output

The output of `getBreadcrumbs()` in Twig (or `generateBreadcrumbs()` as a service) will look like this:
```php
[
    ['label' => 'Pages', 'url' => '/page/'],
    ['label' => 'Page Details', 'url' => '/page/1']
]
```

## Summary

- Breadcrumbs YAML configuration defines route labels and chains.
- Service `BreadcrumbsProcessor` generates breadcrumbs for specified routes.
- Twig function `getBreadcrumbs()` automatically provides breadcrumbs for the current route.
- Caching is automatically managed on YAML changes for optimized performance.

This structure allows you to integrate breadcrumbs easily with flexible customization options.
