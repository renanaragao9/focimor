<?php

namespace Tests\Unit\Models;

use App\Models\Size;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SizeTest extends TestCase
{
    use RefreshDatabase;

    protected function makeSize(array $attributes = []): Size
    {
        return Size::create(array_merge([
            'name' => 'Pequeno',
            'slug' => 'small',
            'order' => 1,
            'is_active' => true,
        ], $attributes));
    }

    public function test_can_create_size(): void
    {
        $size = $this->makeSize();

        $this->assertDatabaseHas('sizes', [
            'id' => $size->id,
            'name' => 'Pequeno',
            'slug' => 'small',
        ]);
    }

    public function test_can_read_size(): void
    {
        $size = $this->makeSize();

        $found = Size::find($size->id);

        $this->assertNotNull($found);
        $this->assertSame('Pequeno', $found->name);
    }

    public function test_can_update_size(): void
    {
        $size = $this->makeSize();

        $size->update(['name' => 'Grande', 'slug' => 'large']);

        $this->assertDatabaseHas('sizes', [
            'id' => $size->id,
            'name' => 'Grande',
            'slug' => 'large',
        ]);
    }

    public function test_can_delete_size(): void
    {
        $size = $this->makeSize();

        $size->delete();

        $this->assertSoftDeleted('sizes', ['id' => $size->id]);
        $this->assertNull(Size::find($size->id));
    }

    public function test_size_slug_is_unique(): void
    {
        $this->makeSize(['slug' => 'small']);

        $this->expectException(QueryException::class);

        $this->makeSize(['name' => 'Outro Pequeno', 'slug' => 'small']);
    }

    public function test_size_casts_attributes(): void
    {
        $size = $this->makeSize(['order' => '3', 'is_active' => 1]);

        $this->assertIsInt($size->order);
        $this->assertIsBool($size->is_active);
    }

    public function test_size_has_pets_relationship(): void
    {
        $size = $this->makeSize();

        $this->assertInstanceOf(
            HasMany::class,
            $size->pets()
        );
    }
}
