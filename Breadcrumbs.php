<?php

declare(strict_types=1);

namespace ChamberOrchestra\Breadcrumbs;

use Symfony\Component\HttpFoundation\Request;

class Breadcrumbs implements \ArrayAccess, \Iterator, \Countable
{
    private array $crumbs = [];
    private int $position = 0;

    public function getCrumbs(): array
    {
        return $this->crumbs;
    }

    public function addCrumb(string $name, ?string $route = null, array $params = [], bool $prepend = false): self
    {
        $crumb = [
            'name' => $name,
            'route' => $route,
            'routeParams' => $params,
        ];

        if ($prepend) {
            array_unshift($this->crumbs, $crumb);

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
        return $this->addCrumb(
            $name,
            $request->attributes->get('_route'),
            $request->attributes->get('_route_params', []),
        );
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
        $this->crumbs[$offset] = $value;
    }

    public function offsetUnset(mixed $offset): void
    {
        unset($this->crumbs[$offset]);
    }

    public function current(): mixed
    {
        return $this->crumbs[$this->position];
    }

    public function next(): void
    {
        ++$this->position;
    }

    public function key(): int
    {
        return $this->position;
    }

    public function valid(): bool
    {
        return isset($this->crumbs[$this->position]);
    }

    public function rewind(): void
    {
        $this->position = 0;
    }

    public function count(): int
    {
        return count($this->crumbs);
    }
}
