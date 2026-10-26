<?php

namespace Tests\Feature\Admin;

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategorySlugRegressionTest extends TestCase
{
    use RefreshDatabase;

    protected function admin(): User
    {
        return User::factory()->create(['role' => 'admin']);
    }

    public function test_updating_category_without_changing_name_keeps_its_existing_slug(): void
    {
        $category = Category::factory()->create(['name' => 'Kuliner', 'slug' => 'kuliner']);

        $response = $this->actingAs($this->admin())->put(
            route('admin.categories.update', $category),
            ['name' => 'Kuliner', 'icon' => 'kuliner']
        );

        $response->assertRedirect(route('admin.categories.index'));
        $response->assertSessionHasNoErrors();

        $this->assertSame('kuliner', $category->fresh()->slug);
    }

    public function test_renaming_category_into_a_colliding_slug_gets_auto_suffixed(): void
    {
        Category::factory()->create(['name' => 'Kuliner', 'slug' => 'kuliner']);
        $target = Category::factory()->create(['name' => 'Belanja', 'slug' => 'belanja']);

        $response = $this->actingAs($this->admin())->put(
            route('admin.categories.update', $target),
            ['name' => 'Kuliner', 'icon' => 'kuliner']
        );

        $response->assertRedirect(route('admin.categories.index'));

        $this->assertSame('kuliner-2', $target->fresh()->slug);
    }
}
