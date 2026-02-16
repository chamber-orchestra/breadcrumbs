<?php

declare(strict_types=1);

/*
 * This file is part of the ChamberOrchestra package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChamberOrchestra\Breadcrumbs;

use Symfony\Component\HttpFoundation\Request;

/**
 * @implements \ArrayAccess<int, array{name: string, route: ?string, routeParams: array<string, mixed>}>
 * @implements \IteratorAggregate<int, array{name: string, route: ?string, routeParams: array<string, mixed>}>
 */
class Breadcrumbs implements \ArrayAccess, \IteratorAggregate, \Countable
{
    /** @var array<int, array{name: string, route: ?string, routeParams: array<string, mixed>}> */
    private array $crumbs = [];

    /** @return array<int, array{name: string, route: ?string, routeParams: array<string, mixed>}> */
    public function getCrumbs(): array
    {
        return $this->crumbs;
    }

    /** @param array<string, mixed> $params */
    public function addCrumb(string $name, ?string $route = null, array $params = [], bool $prepend = false): self
    {
        $crumb = [
            'name' => $name,
            'route' => $route,
            'routeParams' => $params,
        ];

        if (true === $prepend) {
            \array_unshift($this->crumbs, $crumb);

            return $this;
        }

        $this->crumbs[] = $crumb;

        return $this;
    }

    public function addCrumbsClosure(\Closure $closure): self
    {
        $closure($this);

        return $this;
    }

    public function addRequestCrumb(string $name, Request $request): self
    {
        /** @var ?string $route */
        $route = $request->attributes->get('_route');

        /** @var array<string, mixed> $params */
        $params = $request->attributes->get('_route_params', []);

        return $this->addCrumb($name, $route, $params);
    }

    public function offsetExists(mixed $offset): bool
    {
        return isset($this->crumbs[$offset]);
    }

    public function offsetGet(mixed $offset): mixed
    {
        return $this->crumbs[$offset];
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        if (null === $offset) {
            $this->crumbs[] = $value;
        } else {
            $this->crumbs[$offset] = $value;
        }
    }

    public function offsetUnset(mixed $offset): void
    {
        unset($this->crumbs[$offset]);
    }

    /** @return \ArrayIterator<int, array{name: string, route: ?string, routeParams: array<string, mixed>}> */
    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->crumbs);
    }

    public function count(): int
    {
        return \count($this->crumbs);
    }
}
