<?php

namespace Tests\Support;

use Illuminate\Routing\Route;
use Kayrunm\Polybind\Exceptions\ParameterNotFound;
use Kayrunm\Polybind\Support\ParameterResolver;
use Kayrunm\Polybind\Types\IntersectionType;
use Kayrunm\Polybind\Types\Type;
use Kayrunm\Polybind\Types\UnionType;
use PHPUnit\Framework\TestCase;
use Tests\Fixtures\Controllers\BasicController;
use Tests\Fixtures\Models\Comment;
use Tests\Fixtures\Models\Post;

class ParameterResolverTest extends TestCase
{
    private ParameterResolver $parameterResolver;

    protected function setUp(): void
    {
        parent::setUp();

        $this->parameterResolver = new ParameterResolver();
    }

    public function test_returns_null_the_parameter_does_not_exist_in_a_closure(): void
    {
        $route = $this->createRoute(function ($foo, $bar) {
            // ...
        });

        $type = $this->parameterResolver->getParameterType('baz', $route);

        $this->assertNull($type);
    }

    public function test_returns_null_for_a_parameter_without_a_type_from_a_closure(): void
    {
        $route = $this->createRoute(function ($post) {
            // ...
        });

        $type = $this->parameterResolver->getParameterType('post', $route);

        $this->assertNull($type);
    }

    public function test_returns_the_type_for_a_parameter_from_a_closure(): void
    {
        $route = $this->createRoute(function (Post $post) {
            // ...
        });

        $type = $this->parameterResolver->getParameterType('post', $route);

        $this->assertInstanceOf(Type::class, $type);
        $this->assertSame(Post::class, $type->toArray()[0]);
    }

    public function test_returns_the_union_type_for_a_parameter_from_a_closure(): void
    {
        $route = $this->createRoute(function (Post|Comment $model) {
            // ...
        });

        $type = $this->parameterResolver->getParameterType('model', $route);

        $this->assertInstanceOf(UnionType::class, $type);
        $this->assertSame(Post::class, $type->toArray()[0]);
        $this->assertSame(Comment::class, $type->toArray()[1]);
    }

    public function test_returns_the_intersection_type_for_a_parameter_from_a_closure(): void
    {
        $route = $this->createRoute(function (Post&Comment $model) {
            // ...
        });

        $type = $this->parameterResolver->getParameterType('model', $route);

        $this->assertInstanceOf(IntersectionType::class, $type);
        $this->assertSame(Post::class, $type->toArray()[0]);
        $this->assertSame(Comment::class, $type->toArray()[1]);
    }

    public function test_returns_null_when_the_parameter_does_not_exist_in_a_controller(): void
    {
        $route = $this->createRoute([BasicController::class, 'index']);

        $type = $this->parameterResolver->getParameterType('baz', $route);

        $this->assertNull($type);
    }

    public function test_returns_null_for_a_parameter_without_a_type_from_a_controller(): void
    {
        $route = $this->createRoute([BasicController::class, 'show']);

        $type = $this->parameterResolver->getParameterType('model', $route);

        $this->assertNull($type);
    }

    public function test_returns_the_type_for_a_parameter_from_a_controller(): void
    {
        $route = $this->createRoute([BasicController::class, 'showSpecific']);

        $type = $this->parameterResolver->getParameterType('model', $route);

        $this->assertInstanceOf(Type::class, $type);
        $this->assertSame(Comment::class, $type->toArray()[0]);
    }

    public function test_returns_the_union_type_for_a_parameter_from_a_controller(): void
    {
        $route = $this->createRoute([BasicController::class, 'showUnion']);

        $type = $this->parameterResolver->getParameterType('model', $route);

        $this->assertInstanceOf(UnionType::class, $type);
        $this->assertSame(Comment::class, $type->toArray()[0]);
        $this->assertSame(Post::class, $type->toArray()[1]);
    }

    public function test_returns_the_intersection_type_for_a_parameter_from_a_controller(): void
    {
        $route = $this->createRoute([BasicController::class, 'showIntersection']);

        $type = $this->parameterResolver->getParameterType('model', $route);

        $this->assertInstanceOf(IntersectionType::class, $type);
        $this->assertSame(Comment::class, $type->toArray()[0]);
        $this->assertSame(Post::class, $type->toArray()[1]);
    }

    private function createRoute(mixed $uses): Route
    {
        $uses = is_array($uses) ? implode('@', $uses) : $uses;

        return new Route('GET', '/', [
            'uses' => $uses,
        ]);
    }
}
