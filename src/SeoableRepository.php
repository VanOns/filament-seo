<?php

namespace VanOns\FilamentSeo;

use Illuminate\Routing\Route;
use ReflectionException;
use ReflectionMethod;
use Throwable;
use VanOns\FilamentSeo\Contracts\Seoable;

class SeoableRepository
{
    public function getSeoable(): Seoable
    {
        try {
            $route = request()->route();
            if ($route instanceof Route && $seoable = $this->getSeoableFromAttribute($route)) {
                return $seoable;
            }
        } catch (Throwable $e) {
        }

        return app(config('seo.fallback'));
    }

    /**
     * @throws ReflectionException
     */
    protected function getSeoableFromAttribute(Route $route): ?Seoable
    {
        $parts = explode('@', $route->getActionName());
        $controller = $parts[0];
        $method = $parts[1] ?? '__invoke';

        if (!class_exists($controller)) {
            return null;
        }

        $reflection = new ReflectionMethod($controller, $method);

        $attribute = collect($reflection->getAttributes(Attributes\Seoable::class))->first()?->newInstance();

        if (!$attribute) {
            return null;
        }

        $parameters = $route->parameters();

        // If not parameter is set, we check if a model instance has been injected
        if (!$attribute->parameter) {
            return collect($parameters)->first(fn($parameter, $key) => is_a($parameter, $attribute->model));
        }

        // If a parameter is set, we try to query the model
        if (isset($parameters[$attribute->parameter])) {
            return $attribute->model::where($attribute->column, $parameters[$attribute->parameter])->first();
        }

        return null;
    }
}
