<?php

namespace Mikamatto\BreadcrumbsBundle;

use Mikamatto\BreadcrumbsBundle\DependencyInjection\BreadcrumbsBundleExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;


class BreadcrumbsBundle extends Bundle
{
    public function getContainerExtension(): ?\Symfony\Component\DependencyInjection\Extension\ExtensionInterface
    {
        if (null === $this->extension) {
            $this->extension = new BreadcrumbsBundleExtension();
        }

        return $this->extension;
    }
}