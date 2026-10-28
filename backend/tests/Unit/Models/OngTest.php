<?php

namespace Tests\Unit\Models;

use App\Models\Ong;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class OngTest extends TestCase
{
    use RefreshDatabase;

    protected function makeOng(array $attributes = []): Ong
    {
        return Ong::create(array_merge(['name' => 'ONG Patas', 'slug' => 'ong-patas-'.Str::random(5), 'status' => 'pending'], $attributes));
    }

    public function test_has_members_relationship_with_role(): void
    {
        $ong = $this->makeOng();
        $user = User::create(['name' => 'Membro', 'email' => 'm@example.com', 'password' => 'Senha123']);

        $ong->users()->attach($user->id, ['role' => 'admin']);

        $this->assertInstanceOf(BelongsToMany::class, $ong->users());
        $this->assertSame('admin', $ong->users()->first()->pivot->role);
        $this->assertSame('admin', $ong->roleOf($user->id));
        $this->assertNull($ong->roleOf(999));
        $this->assertSame($ong->id, $user->ongs()->first()->id);
    }

    public function test_can_create(): void
    {
        $record = $this->makeOng();

        $this->assertDatabaseHas('ongs', ['id' => $record->id, 'name' => 'ONG Patas']);
    }

    public function test_can_read(): void
    {
        $this->assertNotNull(Ong::find($this->makeOng()->id));
    }

    public function test_can_update(): void
    {
        $record = $this->makeOng();

        $record->update(['name' => 'ONG Focinhos']);

        $this->assertDatabaseHas('ongs', ['id' => $record->id, 'name' => 'ONG Focinhos']);
    }

    public function test_can_delete(): void
    {
        $record = $this->makeOng();

        $record->delete();

        $this->assertSoftDeleted('ongs', ['id' => $record->id]);
        $this->assertNull(Ong::find($record->id));
    }

    public function test_has_posts_relationship(): void
    {
        $this->assertInstanceOf(HasMany::class, $this->makeOng()->posts());
    }

    public function test_slug_is_unique(): void
    {
        $this->makeOng(['slug' => 'x']);

        $this->expectException(QueryException::class);

        $this->makeOng(['slug' => 'x']);
    }

    public function test_relationship(): void
    {
        $this->assertInstanceOf(HasMany::class, $this->makeOng()->pets());
    }
}
