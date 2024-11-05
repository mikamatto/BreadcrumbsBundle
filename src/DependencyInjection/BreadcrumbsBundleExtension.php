<?php

namespace Mikamatto\BreadcrumbsBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class BreadcrumbsBundleExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        // Process the configuration
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        // Set the breadcrumbs_file parameter
        $container->setParameter('breadcrumbs.breadcrumbs_file', $config['breadcrumbs_file']);

        // Set the routes parameter
        $container->setParameter('breadcrumbs.routes', $config['routes']);

        // Load service definitions
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../../config'));
        $loader->load('services.yaml');
    }
}