<?php

namespace Tests;

use Illuminate\Database\Eloquent\Relations\Relation;
use Kayrunm\Polybind\PolybindException;
use Tests\Concerns\DefinesRoutes;
use Tests\Fixtures\Models;

class PolybindTest extends TestCase
{
    use DefinesRoutes;

    protected function setUp(): void
    {
        parent::setUp();

        Relation::morphMap([
            'comment' => Models\Comment::class,
            'post' => Models\Post::class,
        ]);
    }

    public function test_throws_an_exception_if_no_model_type_exists_on_the_route(): void
    {
        $this
            ->withoutExceptionHandling()
            ->expectException(PolybindException::class);

        $this->get('/fail/no-model-type');
    }

    public function test_can_resolve_polymorphic_route_in_basic_controller(): void
    {
        Models\Comment::query()->create([
            'uuid' => 'ecbdc8de-daaf-44a9-b4e8-938e94d9c862',
        ]);

        $this->get('/basic/comment/1')
            ->assertOk()
            ->assertJsonPath('id', 1)
            ->assertJsonPath('uuid', 'ecbdc8de-daaf-44a9-b4e8-938e94d9c862');

        Models\Post::query()->create([
            'uuid' => 'd910c260-3940-4b78-b8c3-f781a29230cd',
        ]);

        $this->get('/basic/post/1')
            ->assertOk()
            ->assertJsonPath('id', 1)
            ->assertJsonPath('uuid', 'd910c260-3940-4b78-b8c3-f781a29230cd');
    }

    public function test_can_resolve_polymorphic_route_in_single_action_controller(): void
    {
        Models\Comment::query()->create([
            'uuid' => 'ecbdc8de-daaf-44a9-b4e8-938e94d9c862',
        ]);

        $this->get('/single-action/comment/1')
            ->assertOk()
            ->assertJsonPath('id', 1)
            ->assertJsonPath('uuid', 'ecbdc8de-daaf-44a9-b4e8-938e94d9c862');

        Models\Post::query()->create([
            'uuid' => 'd910c260-3940-4b78-b8c3-f781a29230cd',
        ]);

        $this->get('/single-action/post/1')
            ->assertOk()
            ->assertJsonPath('id', 1)
            ->assertJsonPath('uuid', 'd910c260-3940-4b78-b8c3-f781a29230cd');
    }

    public function test_can_resolve_polymorphic_route_in_closure_based_controller(): void
    {
        Models\Comment::query()->create([
            'uuid' => 'ecbdc8de-daaf-44a9-b4e8-938e94d9c862',
        ]);

        $this->get('/closure/comment/1')
            ->assertOk()
            ->assertJsonPath('id', 1)
            ->assertJsonPath('uuid', 'ecbdc8de-daaf-44a9-b4e8-938e94d9c862');

        Models\Post::query()->create([
            'uuid' => 'd910c260-3940-4b78-b8c3-f781a29230cd',
        ]);

        $this->get('/closure/post/1')
            ->assertOk()
            ->assertJsonPath('id', 1)
            ->assertJsonPath('uuid', 'd910c260-3940-4b78-b8c3-f781a29230cd');
    }
}
