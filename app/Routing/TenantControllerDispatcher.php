<?php

namespace App\Routing;

use Illuminate\Routing\ControllerDispatcher;
use Illuminate\Routing\Route;

final class TenantControllerDispatcher extends ControllerDispatcher
{
    public function dispatch(Route $route, $controller, $method)
    {
        $routeParameters = $route->parametersWithoutNulls();

        // Remove the tenant context before dependency injection so it cannot
        // shift model and request arguments in legacy controller signatures.
        unset($routeParameters['loja']);

        $parameters = $this->resolveClassMethodDependencies(
            $routeParameters,
            $controller,
            $method
        );

        if (method_exists($controller, 'callAction')) {
            // Preserve associative parameters for framework controllers such as
            // ViewController. The regular base controller normalizes them itself.
            return $controller->callAction($method, $parameters);
        }

        return $controller->{$method}(...array_values($parameters));
    }
}
