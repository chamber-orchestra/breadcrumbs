<?php

declare(strict_types=1);

namespace Tests\Unit;

use ChamberOrchestra\Breadcrumbs\Breadcrumbs;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

final class BreadcrumbsTest extends TestCase
{
    public function testEmptyByDefault(): void
    {
        $breadcrumbs = new Breadcrumbs();

        self::assertSame([], $breadcrumbs->getCrumbs());
        self::assertCount(0, $breadcrumbs);
    }

    public function testAddCrumb(): void
    {
        $breadcrumbs = new Breadcrumbs();
        $result = $breadcrumbs->addCrumb('Home', 'app_home');

        self::assertSame($breadcrumbs, $result);
        self::assertCount(1, $breadcrumbs);
        self::assertSame([
            ['name' => 'Home', 'route' => 'app_home', 'routeParams' => []],
        ], $breadcrumbs->getCrumbs());
    }

    public function testAddCrumbWithParams(): void
    {
        $breadcrumbs = new Breadcrumbs();
        $breadcrumbs->addCrumb('Article', 'app_article', ['id' => 42]);

        self::assertSame(
            ['name' => 'Article', 'route' => 'app_article', 'routeParams' => ['id' => 42]],
            $breadcrumbs->getCrumbs()[0],
        );
    }

    public function testAddCrumbWithNullRoute(): void
    {
        $breadcrumbs = new Breadcrumbs();
        $breadcrumbs->addCrumb('Current Page');

        self::assertNull($breadcrumbs->getCrumbs()[0]['route']);
    }

    public function testAddCrumbAppendOrder(): void
    {
        $breadcrumbs = new Breadcrumbs();
        $breadcrumbs->addCrumb('First', 'route_1');
        $breadcrumbs->addCrumb('Second', 'route_2');
        $breadcrumbs->addCrumb('Third', 'route_3');

        $crumbs = $breadcrumbs->getCrumbs();
        self::assertCount(3, $crumbs);
        self::assertSame('First', $crumbs[0]['name']);
        self::assertSame('Second', $crumbs[1]['name']);
        self::assertSame('Third', $crumbs[2]['name']);
    }

    public function testAddCrumbPrepend(): void
    {
        $breadcrumbs = new Breadcrumbs();
        $breadcrumbs->addCrumb('Second', 'route_2');
        $breadcrumbs->addCrumb('First', 'route_1', [], true);

        $crumbs = $breadcrumbs->getCrumbs();
        self::assertCount(2, $crumbs);
        self::assertSame('First', $crumbs[0]['name']);
        self::assertSame('Second', $crumbs[1]['name']);
    }

    public function testAddCrumbPrependMultiple(): void
    {
        $breadcrumbs = new Breadcrumbs();
        $breadcrumbs->addCrumb('Third', 'route_3');
        $breadcrumbs->addCrumb('Second', 'route_2', [], true);
        $breadcrumbs->addCrumb('First', 'route_1', [], true);

        $crumbs = $breadcrumbs->getCrumbs();
        self::assertSame('First', $crumbs[0]['name']);
        self::assertSame('Second', $crumbs[1]['name']);
        self::assertSame('Third', $crumbs[2]['name']);
    }

    public function testAddRequestCrumb(): void
    {
        $request = new Request();
        $request->attributes->set('_route', 'app_dashboard');
        $request->attributes->set('_route_params', ['locale' => 'en']);

        $breadcrumbs = new Breadcrumbs();
        $result = $breadcrumbs->addRequestCrumb('Dashboard', $request);

        self::assertSame($breadcrumbs, $result);
        self::assertSame(
            ['name' => 'Dashboard', 'route' => 'app_dashboard', 'routeParams' => ['locale' => 'en']],
            $breadcrumbs->getCrumbs()[0],
        );
    }

    public function testAddRequestCrumbWithNoRouteParams(): void
    {
        $request = new Request();
        $request->attributes->set('_route', 'app_home');

        $breadcrumbs = new Breadcrumbs();
        $breadcrumbs->addRequestCrumb('Home', $request);

        self::assertSame([], $breadcrumbs->getCrumbs()[0]['routeParams']);
    }

    public function testAddCrumbsClosure(): void
    {
        $breadcrumbs = new Breadcrumbs();
        $result = $breadcrumbs->addCrumbsClosure(function (Breadcrumbs $b) {
            $b->addCrumb('Home', 'app_home');
            $b->addCrumb('About', 'app_about');
        });

        self::assertSame($breadcrumbs, $result);
        self::assertCount(2, $breadcrumbs);
        self::assertSame('Home', $breadcrumbs->getCrumbs()[0]['name']);
        self::assertSame('About', $breadcrumbs->getCrumbs()[1]['name']);
    }

    public function testFluentChaining(): void
    {
        $breadcrumbs = new Breadcrumbs();
        $breadcrumbs
            ->addCrumb('Home', 'app_home')
            ->addCrumb('Category', 'app_category', ['slug' => 'news'])
            ->addCrumb('Article');

        self::assertCount(3, $breadcrumbs);
    }

    // ArrayAccess tests

    public function testOffsetExists(): void
    {
        $breadcrumbs = new Breadcrumbs();
        $breadcrumbs->addCrumb('Home', 'app_home');

        self::assertTrue(isset($breadcrumbs[0]));
        self::assertFalse(isset($breadcrumbs[1]));
    }

    public function testOffsetGet(): void
    {
        $breadcrumbs = new Breadcrumbs();
        $breadcrumbs->addCrumb('Home', 'app_home');

        self::assertSame('Home', $breadcrumbs[0]['name']);
        self::assertSame('app_home', $breadcrumbs[0]['route']);
    }

    public function testOffsetSet(): void
    {
        $breadcrumbs = new Breadcrumbs();
        $breadcrumbs->addCrumb('Home', 'app_home');

        $breadcrumbs[0] = ['name' => 'Updated', 'route' => 'updated_route', 'routeParams' => []];

        self::assertSame('Updated', $breadcrumbs[0]['name']);
    }

    public function testOffsetUnset(): void
    {
        $breadcrumbs = new Breadcrumbs();
        $breadcrumbs->addCrumb('Home', 'app_home');
        $breadcrumbs->addCrumb('About', 'app_about');

        unset($breadcrumbs[0]);

        self::assertFalse(isset($breadcrumbs[0]));
        self::assertTrue(isset($breadcrumbs[1]));
    }

    // Iterator tests

    public function testIterator(): void
    {
        $breadcrumbs = new Breadcrumbs();
        $breadcrumbs->addCrumb('Home', 'app_home');
        $breadcrumbs->addCrumb('About', 'app_about');

        $names = [];
        foreach ($breadcrumbs as $key => $crumb) {
            $names[] = $crumb['name'];
            self::assertIsInt($key);
        }

        self::assertSame(['Home', 'About'], $names);
    }

    public function testIteratorMultiplePasses(): void
    {
        $breadcrumbs = new Breadcrumbs();
        $breadcrumbs->addCrumb('Home', 'app_home');

        $count1 = 0;
        foreach ($breadcrumbs as $crumb) {
            $count1++;
        }

        $count2 = 0;
        foreach ($breadcrumbs as $crumb) {
            $count2++;
        }

        self::assertSame(1, $count1);
        self::assertSame(1, $count2);
    }

    public function testIteratorEmpty(): void
    {
        $breadcrumbs = new Breadcrumbs();
        $count = 0;

        foreach ($breadcrumbs as $crumb) {
            $count++;
        }

        self::assertSame(0, $count);
    }

    // Countable tests

    public function testCount(): void
    {
        $breadcrumbs = new Breadcrumbs();

        self::assertCount(0, $breadcrumbs);

        $breadcrumbs->addCrumb('Home', 'app_home');
        self::assertCount(1, $breadcrumbs);

        $breadcrumbs->addCrumb('About', 'app_about');
        self::assertCount(2, $breadcrumbs);
    }
}
