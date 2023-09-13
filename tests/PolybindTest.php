<?php

namespace Tests;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Kayrunm\Polybind\Exceptions\PolybindException;
use Kayrunm\Polybind\Polybind;
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

    public function test_can_resolve_model_in_basic_controller(): void
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

    public function test_can_resolve_model_in_single_action_controller(): void
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

    public function test_can_resolve_model_in_closure_based_controller(): void
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

    public function test_can_resolve_model_with_custom_model_type_key(): void
    {
        Models\Comment::query()->create([
            'uuid' => 'ecbdc8de-daaf-44a9-b4e8-938e94d9c862',
        ]);

        $this->get('/custom-model-type/comment/1')
            ->assertOk()
            ->assertJsonPath('id', 1)
            ->assertJsonPath('uuid', 'ecbdc8de-daaf-44a9-b4e8-938e94d9c862');

        Models\Post::query()->create([
            'uuid' => 'd910c260-3940-4b78-b8c3-f781a29230cd',
        ]);

        $this->get('/custom-model-type/post/1')
            ->assertOk()
            ->assertJsonPath('id', 1)
            ->assertJsonPath('uuid', 'd910c260-3940-4b78-b8c3-f781a29230cd');
    }

    public function test_can_resolve_model_with_custom_model_id_key(): void
    {
        Models\Comment::query()->create([
            'uuid' => 'ecbdc8de-daaf-44a9-b4e8-938e94d9c862',
        ]);

        $this->get('/custom-model-id/comment/1')
            ->assertOk()
            ->assertJsonPath('id', 1)
            ->assertJsonPath('uuid', 'ecbdc8de-daaf-44a9-b4e8-938e94d9c862');

        Models\Post::query()->create([
            'uuid' => 'd910c260-3940-4b78-b8c3-f781a29230cd',
        ]);

        $this->get('/custom-model-id/post/1')
            ->assertOk()
            ->assertJsonPath('id', 1)
            ->assertJsonPath('uuid', 'd910c260-3940-4b78-b8c3-f781a29230cd');
    }

    public function test_can_resolve_model_with_custom_model_key(): void
    {
        Models\Comment::query()->create([
            'uuid' => 'ecbdc8de-daaf-44a9-b4e8-938e94d9c862',
        ]);

        $this->get('/custom-model-id/comment/1')
            ->assertOk()
            ->assertJsonPath('id', 1)
            ->assertJsonPath('uuid', 'ecbdc8de-daaf-44a9-b4e8-938e94d9c862');

        Models\Post::query()->create([
            'uuid' => 'd910c260-3940-4b78-b8c3-f781a29230cd',
        ]);

        $this->get('/custom-model-id/post/1')
            ->assertOk()
            ->assertJsonPath('id', 1)
            ->assertJsonPath('uuid', 'd910c260-3940-4b78-b8c3-f781a29230cd');
    }

    public function test_can_resolve_model_with_updated_config_values(): void
    {
        config([
            'polybind.defaults.type_param' => 'type',
            'polybind.defaults.id_param' => 'uuid',
            'polybind.defaults.model_param' => 'post',
            'polybind.defaults.resolver' => fn (Builder $query, $value) => $query->where('uuid', $value)->firstOrFail(),
        ]);

        Models\Post::query()->create([
            'uuid' => 'd910c260-3940-4b78-b8c3-f781a29230cd',
        ]);

        $this->get('/custom/post/d910c260-3940-4b78-b8c3-f781a29230cd')
            ->assertOk()
            ->assertJsonPath('id', 1)
            ->assertJsonPath('uuid', 'd910c260-3940-4b78-b8c3-f781a29230cd');
    }

    public function test_can_resolve_model_with_custom_resolver(): void
    {
        Polybind::setResolver(function ($query, $value) {
            return $query->where('uuid', $value)->firstOrFail();
        });

        Models\Comment::query()->create([
            'uuid' => 'ecbdc8de-daaf-44a9-b4e8-938e94d9c862',
        ]);

        $this->get('/basic/comment/ecbdc8de-daaf-44a9-b4e8-938e94d9c862')
            ->assertOk()
            ->assertJsonPath('id', 1)
            ->assertJsonPath('uuid', 'ecbdc8de-daaf-44a9-b4e8-938e94d9c862');

        Models\Post::query()->create([
            'uuid' => 'd910c260-3940-4b78-b8c3-f781a29230cd',
        ]);

        $this->get('/basic/post/d910c260-3940-4b78-b8c3-f781a29230cd')
            ->assertOk()
            ->assertJsonPath('id', 1)
            ->assertJsonPath('uuid', 'd910c260-3940-4b78-b8c3-f781a29230cd');

        Polybind::setResolver();
    }

    public function test_can_resolve_model_of_specified_type(): void
    {
        $this->withoutExceptionHandling();

        Models\Comment::query()->create([
            'uuid' => 'ecbdc8de-daaf-44a9-b4e8-938e94d9c862',
        ]);

        $this->get('/specific/comment/1')
            ->assertOk()
            ->assertJsonPath('id', 1)
            ->assertJsonPath('uuid', 'ecbdc8de-daaf-44a9-b4e8-938e94d9c862');
    }

    public function test_throws_error_for_model_not_of_specified_type(): void
    {
        Models\Post::query()->create([
            'uuid' => 'ecbdc8de-daaf-44a9-b4e8-938e94d9c862',
        ]);

        $this->get('/specific/post/1')->assertNotFound();
    }

    public function test_can_resolve_model_of_specified_union_type(): void
    {
        Models\Comment::query()->create([
            'uuid' => 'ecbdc8de-daaf-44a9-b4e8-938e94d9c862',
        ]);

        $this->get('/union/comment/1')
            ->assertOk()
            ->assertJsonPath('id', 1)
            ->assertJsonPath('uuid', 'ecbdc8de-daaf-44a9-b4e8-938e94d9c862');

        Models\Post::query()->create([
            'uuid' => '03c38235-b7bb-4c51-9121-6236f3ff772c',
        ]);

        $this->get('/union/post/1')
            ->assertOk()
            ->assertJsonPath('id', 1)
            ->assertJsonPath('uuid', '03c38235-b7bb-4c51-9121-6236f3ff772c');
    }

    public function test_throws_error_for_model_not_of_specified_union_type(): void
    {
        Models\Author::query()->create([
            'uuid' => 'ecbdc8de-daaf-44a9-b4e8-938e94d9c862',
        ]);

        $this->get('/union/author/1')->assertNotFound();
    }

    public function test_can_resolve_model_of_specified_interface(): void
    {
        Models\Comment::query()->create([
            'uuid' => 'ecbdc8de-daaf-44a9-b4e8-938e94d9c862',
        ]);

        $this->get('/interface/comment/1')
            ->assertOk()
            ->assertJsonPath('id', 1)
            ->assertJsonPath('uuid', 'ecbdc8de-daaf-44a9-b4e8-938e94d9c862');
    }

    public function test_throws_error_for_model_not_of_specified_interface(): void
    {
        Models\Author::query()->create([
            'uuid' => 'ecbdc8de-daaf-44a9-b4e8-938e94d9c862',
        ]);

        $this->get('/interface/author/1')->assertNotFound();
    }
}
