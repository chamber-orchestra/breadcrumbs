<?php

declare(strict_types=1);

/*
 * This file is part of the ChamberOrchestra package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Unit;

use ChamberOrchestra\Breadcrumbs\Breadcrumbs;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

final class BreadcrumbsTest extends TestCase
{
    private Breadcrumbs $breadcrumbs;

    protected function setUp(): void
    {
        $this->breadcrumbs = new Breadcrumbs();
    }

    #[Test]
    public function newInstanceIsEmpty(): void
    {
        self::assertCount(0, $this->breadcrumbs);
        self::assertSame([], $this->breadcrumbs->getCrumbs());
    }

    #[Test]
    public function addCrumbAppendsByDefault(): void
    {
        $this->breadcrumbs
            ->addCrumb('Home', 'app_home')
            ->addCrumb('Products', 'app_products', ['category' => 'books']);

        $crumbs = $this->breadcrumbs->getCrumbs();

        self::assertCount(2, $crumbs);
        self::assertSame('Home', $crumbs[0]['name']);
        self::assertSame('app_home', $crumbs[0]['route']);
        self::assertSame([], $crumbs[0]['routeParams']);
        self::assertSame('Products', $crumbs[1]['name']);
        self::assertSame(['category' => 'books'], $crumbs[1]['routeParams']);
    }

    #[Test]
    public function addCrumbWithNullRoute(): void
    {
        $this->breadcrumbs->addCrumb('Current Page');

        $crumbs = $this->breadcrumbs->getCrumbs();

        self::assertCount(1, $crumbs);
        self::assertNull($crumbs[0]['route']);
        self::assertSame([], $crumbs[0]['routeParams']);
    }

    #[Test]
    public function addCrumbPrepends(): void
    {
        $this->breadcrumbs
            ->addCrumb('Products', 'app_products')
            ->addCrumb('Home', 'app_home', [], prepend: true);

        $crumbs = $this->breadcrumbs->getCrumbs();

        self::assertSame('Home', $crumbs[0]['name']);
        self::assertSame('Products', $crumbs[1]['name']);
    }

    #[Test]
    public function addCrumbReturnsFluentInterface(): void
    {
        $result = $this->breadcrumbs->addCrumb('Home', 'app_home');

        self::assertSame($this->breadcrumbs, $result);
    }

    #[Test]
    public function addCrumbsClosureReceivesInstance(): void
    {
        $result = $this->breadcrumbs->addCrumbsClosure(function (Breadcrumbs $crumbs): void {
            $crumbs->addCrumb('Home', 'app_home');
            $crumbs->addCrumb('About', 'app_about');
        });

        self::assertSame($this->breadcrumbs, $result);
        self::assertCount(2, $this->breadcrumbs);
    }

    #[Test]
    public function addRequestCrumbExtractsRouteFromRequest(): void
    {
        $request = Request::create('/products');
        $request->attributes->set('_route', 'app_products');
        $request->attributes->set('_route_params', ['id' => 42]);

        $this->breadcrumbs->addRequestCrumb('Products', $request);

        $crumbs = $this->breadcrumbs->getCrumbs();

        self::assertCount(1, $crumbs);
        self::assertSame('Products', $crumbs[0]['name']);
        self::assertSame('app_products', $crumbs[0]['route']);
        self::assertSame(['id' => 42], $crumbs[0]['routeParams']);
    }

    #[Test]
    public function countReturnsNumberOfCrumbs(): void
    {
        self::assertCount(0, $this->breadcrumbs);

        $this->breadcrumbs->addCrumb('Home', 'app_home');
        self::assertCount(1, $this->breadcrumbs);

        $this->breadcrumbs->addCrumb('About', 'app_about');
        self::assertCount(2, $this->breadcrumbs);
    }

    #[Test]
    public function iteratesOverCrumbs(): void
    {
        $this->breadcrumbs
            ->addCrumb('Home', 'app_home')
            ->addCrumb('Products', 'app_products')
            ->addCrumb('Detail', 'app_detail');

        $names = [];

        foreach ($this->breadcrumbs as $key => $crumb) {
            $names[] = $crumb['name'];
            self::assertIsInt($key);
        }

        self::assertSame(['Home', 'Products', 'Detail'], $names);
    }

    #[Test]
    public function iteratorRewindsCorrectly(): void
    {
        $this->breadcrumbs->addCrumb('Home', 'app_home');

        \iterator_to_array($this->breadcrumbs);
        $second = \iterator_to_array($this->breadcrumbs);

        self::assertCount(1, $second);
        self::assertSame('Home', $second[0]['name']);
    }

    #[Test]
    public function getIteratorReturnsArrayIterator(): void
    {
        $this->breadcrumbs->addCrumb('Home', 'app_home');

        self::assertInstanceOf(\ArrayIterator::class, $this->breadcrumbs->getIterator());
    }

    #[Test]
    public function offsetExistsReturnsTrueForValidIndex(): void
    {
        $this->breadcrumbs->addCrumb('Home', 'app_home');

        self::assertTrue(isset($this->breadcrumbs[0]));
        self::assertFalse(isset($this->breadcrumbs[1]));
    }

    #[Test]
    public function offsetGetReturnsCrumb(): void
    {
        $this->breadcrumbs->addCrumb('Home', 'app_home');

        self::assertSame('Home', $this->breadcrumbs[0]['name']);
    }

    #[Test]
    public function offsetSetReplacesCrumb(): void
    {
        $this->breadcrumbs->addCrumb('Home', 'app_home');

        $this->breadcrumbs[0] = ['name' => 'Dashboard', 'route' => 'app_dashboard', 'routeParams' => []];

        self::assertSame('Dashboard', $this->breadcrumbs[0]['name']);
        self::assertCount(1, $this->breadcrumbs);
    }

    #[Test]
    public function offsetUnsetRemovesCrumb(): void
    {
        $this->breadcrumbs
            ->addCrumb('Home', 'app_home')
            ->addCrumb('About', 'app_about');

        unset($this->breadcrumbs[0]);

        self::assertCount(1, $this->breadcrumbs);
        self::assertFalse(isset($this->breadcrumbs[0]));
        self::assertTrue(isset($this->breadcrumbs[1]));
    }
}
