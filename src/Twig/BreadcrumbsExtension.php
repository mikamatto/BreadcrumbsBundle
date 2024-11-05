<?php

namespace Mikamatto\BreadcrumbsBundle\Twig;

use Mikamatto\BreadcrumbsBundle\Service\BreadcrumbsProcessor;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RouterInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class BreadcrumbsExtension extends AbstractExtension
{
    public function __construct(
        private RouterInterface $router,
        private BreadcrumbsProcessor $breadcrumbsProcessor,
        private RequestStack $requestStack
    ) {}

    public function getFunctions(): array
    {
        return [
            new TwigFunction('getBreadcrumbs', [$this, 'generateBreadcrumbs']),
        ];
    }

    public function generateBreadcrumbs(): array
    {
        // Get the current request and route
        $request = $this->requestStack->getCurrentRequest();
        $currentRoute = $request->attributes->get('_route');
        $routeParams = $request->attributes->get('_route_params', []);

        // Generate the breadcrumb chain for the current route
        return $this->breadcrumbsProcessor->generateBreadcrumbs($currentRoute, $routeParams);
    }
}