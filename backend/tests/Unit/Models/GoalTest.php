<?php

namespace Tests\Unit\Models;

use App\Models\Goal;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GoalTest extends TestCase
{
    use RefreshDatabase;

    protected function makeGoal(array $attributes = []): Goal
    {
        return Goal::create(array_merge([
            'name' => 'Amizade',
            'slug' => 'friendship',
            'order' => 1,
            'is_active' => true,
        ], $attributes));
    }

    public function test_can_create_goal(): void
    {
        $goal = $this->makeGoal();

        $this->assertDatabaseHas('goals', [
            'id' => $goal->id,
            'name' => 'Amizade',
            'slug' => 'friendship',
        ]);
    }

    public function test_can_read_goal(): void
    {
        $goal = $this->makeGoal();

        $found = Goal::find($goal->id);

        $this->assertNotNull($found);
        $this->assertSame('Amizade', $found->name);
    }

    public function test_can_update_goal(): void
    {
        $goal = $this->makeGoal();

        $goal->update(['name' => 'Adoção', 'slug' => 'adoption']);

        $this->assertDatabaseHas('goals', [
            'id' => $goal->id,
            'name' => 'Adoção',
            'slug' => 'adoption',
        ]);
    }

    public function test_can_delete_goal(): void
    {
        $goal = $this->makeGoal();

        $goal->delete();

        $this->assertSoftDeleted('goals', ['id' => $goal->id]);
        $this->assertNull(Goal::find($goal->id));
    }

    public function test_goal_slug_is_unique(): void
    {
        $this->makeGoal(['slug' => 'friendship']);

        $this->expectException(QueryException::class);

        $this->makeGoal(['name' => 'Outra Amizade', 'slug' => 'friendship']);
    }

    public function test_goal_casts_attributes(): void
    {
        $goal = $this->makeGoal(['order' => '2', 'is_active' => 1]);

        $this->assertIsInt($goal->order);
        $this->assertIsBool($goal->is_active);
    }

    public function test_goal_has_pets_relationship(): void
    {
        $goal = $this->makeGoal();

        $this->assertInstanceOf(
            HasMany::class,
            $goal->pets()
        );
    }
}
