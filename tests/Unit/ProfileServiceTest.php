<?php

namespace Moe\Profiles\Tests\Unit;

use Illuminate\Database\Eloquent\Model;
use Moe\Profiles\Facades\Profile;
use Moe\Profiles\Models\Profile as ProfileModel;
use Moe\Profiles\Tests\TestCase;

class ProfileServiceTest extends TestCase
{
    private Model $user;

    protected function setUp(): void
    {
        parent::setUp();

        if (!\Schema::hasTable('users')) {
            \Schema::create('users', function ($table) {
                $table->id();
            });
        }

        $this->user = new class extends Model
        {
            protected $table = 'users';
            public $timestamps = false;
        };

        $this->user->saveQuietly();
    }

    public function test_set_and_get_string(): void
    {
        Profile::set($this->user, 'bio', 'Hello world', 'string');

        $this->assertSame('Hello world', Profile::get($this->user, 'bio'));
    }

    public function test_set_and_get_integer(): void
    {
        Profile::set($this->user, 'age', 25, 'integer');

        $this->assertSame(25, Profile::get($this->user, 'age'));
        $this->assertIsInt(Profile::get($this->user, 'age'));
    }

    public function test_set_and_get_boolean(): void
    {
        Profile::set($this->user, 'is_public', true, 'boolean');

        $this->assertTrue(Profile::get($this->user, 'is_public'));
        $this->assertIsBool(Profile::get($this->user, 'is_public'));
    }

    public function test_set_and_get_json(): void
    {
        $social = ['twitter' => '@user', 'github' => 'user'];
        Profile::set($this->user, 'social', $social, 'json');

        $this->assertSame($social, Profile::get($this->user, 'social'));
    }

    public function test_get_returns_default_when_missing(): void
    {
        $this->assertSame('fallback', Profile::get($this->user, 'nonexistent', 'fallback'));
        $this->assertNull(Profile::get($this->user, 'nonexistent'));
    }

    public function test_has(): void
    {
        Profile::set($this->user, 'exists', 'yes');

        $this->assertTrue(Profile::has($this->user, 'exists'));
        $this->assertFalse(Profile::has($this->user, 'does-not-exist'));
    }

    public function test_forget(): void
    {
        Profile::set($this->user, 'temp', 'value');
        $this->assertTrue(Profile::has($this->user, 'temp'));

        Profile::forget($this->user, 'temp');
        $this->assertFalse(Profile::has($this->user, 'temp'));
        $this->assertNull(Profile::get($this->user, 'temp'));
    }

    public function test_get_all(): void
    {
        Profile::set($this->user, 'bio', 'Hi', 'string');
        Profile::set($this->user, 'age', 30, 'integer');
        Profile::set($this->user, 'public', true, 'boolean');

        $all = Profile::getAll($this->user);

        $this->assertCount(3, $all);
        $this->assertSame('Hi', $all['bio']);
        $this->assertSame(30, $all['age']);
        $this->assertTrue($all['public']);
    }

    public function test_set_multiple(): void
    {
        Profile::setMultiple($this->user, [
            'name' => 'John',
            'age' => ['value' => 28, 'type' => 'integer'],
            'active' => ['value' => true, 'type' => 'boolean'],
        ]);

        $this->assertSame('John', Profile::get($this->user, 'name'));
        $this->assertSame(28, Profile::get($this->user, 'age'));
        $this->assertTrue(Profile::get($this->user, 'active'));
    }

    public function test_isolation_between_users(): void
    {
        $user2 = new class extends Model
        {
            protected $table = 'users';
            public $timestamps = false;
        };
        $user2->saveQuietly();

        Profile::set($this->user, 'key', 'user1-value');
        Profile::set($user2, 'key', 'user2-value');

        $this->assertSame('user1-value', Profile::get($this->user, 'key'));
        $this->assertSame('user2-value', Profile::get($user2, 'key'));
    }

    public function test_infer_types(): void
    {
        Profile::set($this->user, 'str', 'hello');
        Profile::set($this->user, 'int', 42);
        Profile::set($this->user, 'bool', true);
        Profile::set($this->user, 'arr', ['a' => 1]);

        $this->assertSame('hello', Profile::get($this->user, 'str'));
        $this->assertSame(42, Profile::get($this->user, 'int'));
        $this->assertTrue(Profile::get($this->user, 'bool'));
        $this->assertSame(['a' => 1], Profile::get($this->user, 'arr'));
    }
}
