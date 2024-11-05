<?php

namespace Mikamatto\BreadcrumbsBundle\Service;

use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Cache\CacheInterface;

class BreadcrumbsProcessor
{
    private array $breadcrumbs;

    public function __construct(
        private string $breadcrumbsFile, 
        private RouterInterface $router,
        private CacheInterface $cache
    )
    {
        $this->breadcrumbs = $this->loadBreadcrumbs();
    }

    /**
     * Get the full breadcrumb chain for a specific route.
     *
     * @param string $currentRoute The current route name
     * @param array $routeParams Dynamic route parameters (e.g., id => 123)
     * 
     * @return array The full breadcrumb path as an array
     */
    public function generateBreadcrumbs(string $currentRoute, array $routeParams = []): array
    {
        $breadcrumbs = [];

        if (isset($this->breadcrumbs[$currentRoute])) {
            // Add the current route with its parameters
            $breadcrumbs[] = $this->generateBreadcrumbItem($this->breadcrumbs[$currentRoute], $routeParams, $currentRoute);
        
            // Handle the chain (if any)
            if (isset($this->breadcrumbs[$currentRoute]['chain'])) {
                $chain = $this->breadcrumbs[$currentRoute]['chain'];
                
                // Reverse the chain here in PHP (before rendering)
                $chain = array_reverse($chain);
                
                // Add each route in the chain to the breadcrumb array
                foreach ($chain as $previousRoute) {
                    if (isset($this->breadcrumbs[$previousRoute])) {
                        // Pass an empty array for routeParams for previous routes
                        $breadcrumbs[] = $this->generateBreadcrumbItem($this->breadcrumbs[$previousRoute], [], $previousRoute);
                    }
                }
            }
        }

        return array_reverse($breadcrumbs);
    }

    /**
     * Load the breadcrumbs from the YAML file or the cache.
     * If the file has changed, it clears the cache pool and recaches.
     *
     * @return array
     */
    private function loadBreadcrumbs(): array
    {
        if (!file_exists($this->breadcrumbsFile)) {
            return []; // Return an empty array if the file doesn't exist
        }
    
        // Get the current modification time of the YAML file
        $currentModificationTime = filemtime($this->breadcrumbsFile);
    
        // Define the cache key for the last modification time
        $lastModificationTimeKey = 'breadcrumbs_last_modification_time';
    
        // Get the last stored modification time from the cache
        $lastModificationTime = $this->cache->get($lastModificationTimeKey, function() {
            return 0; // Default to 0 if no entry exists
        });
    
        // Check if the file has been modified
        if ($currentModificationTime !== $lastModificationTime) {
            // If modified, clear the cache for breadcrumbs config
            $this->cache->delete('breadcrumbs_config'); // Clear the specific cache entry
    
            // Now set the new last modification time in the cache
            $this->cache->get($lastModificationTimeKey, function() use ($currentModificationTime) {
                return $currentModificationTime; // This stores the current modification time
            });
        }
    
        // Now attempt to get the breadcrumbs from the cache
        return $this->cache->get('breadcrumbs_config', function() {
            // If not cached, parse the YAML file and cache the result
            $config = Yaml::parseFile($this->breadcrumbsFile) ?? [];
            
            // Now access the routes with the updated structure
            return $config['breadcrumbs_bundle']['routes'] ?? [];
        });
    }

    /**
     * Generate a breadcrumb item based on the provided data.
     *
     * @param array $breadcrumbData
     * @param array $routeParams
     * @param string $route
     * @return array
     */
    private function generateBreadcrumbItem(array $breadcrumbData, array $routeParams, string $route): array
    {
        $breadcrumb = [];
        $breadcrumb['label'] = $breadcrumbData['label'];
    
        // Prepare the parameters for URL generation
        // Use all parameters from routeParams directly
        $params = $routeParams;
    
        // Filter out any null values to avoid broken URLs
        $params = array_filter($params, fn($v) => $v !== null);
        
        // Generate the URL using the parameters and the route
        try {
            $breadcrumb['url'] = $this->router->generate($route, $params);
        } catch (\Exception $e) {
            // Handle the exception gracefully, possibly log it or provide a default URL
            $breadcrumb['url'] = '#'; // Fallback URL if generation fails
        }
    
        return $breadcrumb;
    }
}