<?php

namespace Tests\Feature\Admin;

use App\Models\Category;
use App\Models\Destination;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DestinationSlugRegressionTest extends TestCase
{
    use RefreshDatabase;

    protected function admin(): User
    {
        return User::factory()->create(['role' => 'admin']);
    }

    /**
     * Regresi: menyimpan ulang destinasi tanpa mengganti nama pernah gagal
     * dengan error 500 (Duplicate entry) karena slug dihitung ulang dari
     * nama setiap update, walau destinasi itu sengaja punya slug berbeda
     * dari hasil slug otomatis namanya (mis. dua destinasi bernama sama
     * di kategori berbeda).
     */
    public function test_updating_destination_without_changing_name_keeps_its_existing_slug(): void
    {
        $categoryA = Category::factory()->create(['name' => 'Ruang Publik', 'slug' => 'ruang-publik']);
        $categoryB = Category::factory()->create(['name' => 'Belanja', 'slug' => 'belanja']);

        Destination::factory()->create([
            'category_id' => $categoryA->id,
            'name' => 'Jalan Malioboro',
            'slug' => 'jalan-malioboro',
        ]);

        $secondEntry = Destination::factory()->create([
            'category_id' => $categoryB->id,
            'name' => 'Jalan Malioboro',
            'slug' => 'jalan-malioboro-belanja-oleh-oleh',
        ]);

        $response = $this->actingAs($this->admin())->put(
            route('admin.destinations.update', $secondEntry),
            $this->validPayload($secondEntry, ['name' => 'Jalan Malioboro'])
        );

        $response->assertRedirect(route('admin.destinations.index'));
        $response->assertSessionHasNoErrors();

        $this->assertSame('jalan-malioboro-belanja-oleh-oleh', $secondEntry->fresh()->slug);
    }

    /**
     * Kalau nama BENAR-BENAR diganti dan hasil slug-nya bentrok dengan
     * destinasi lain, sistem harus auto-suffix (-2, -3, dst.) alih-alih
     * membiarkan constraint database gagal.
     */
    public function test_renaming_destination_into_a_colliding_slug_gets_auto_suffixed(): void
    {
        Destination::factory()->create(['name' => 'Jalan Malioboro', 'slug' => 'jalan-malioboro']);
        $target = Destination::factory()->create(['name' => 'Soto Kadipiro', 'slug' => 'soto-kadipiro']);

        $response = $this->actingAs($this->admin())->put(
            route('admin.destinations.update', $target),
            $this->validPayload($target, ['name' => 'Jalan Malioboro'])
        );

        $response->assertRedirect(route('admin.destinations.index'));

        $this->assertSame('jalan-malioboro-2', $target->fresh()->slug);
    }

    /**
     * @return array<string, mixed>
     */
    private function validPayload(Destination $destination, array $overrides = []): array
    {
        return array_merge([
            'category_id' => $destination->category_id,
            'name' => $destination->name,
            'description' => 'Deskripsi tes.',
            'recommendation_reason' => 'Alasan tes.',
            'address' => 'Alamat tes',
            'latitude' => -7.8,
            'longitude' => 110.36,
            'price_min' => 0,
            'price_max' => 0,
            'open_time' => '08:00',
            'close_time' => '17:00',
            'is_active' => '1',
        ], $overrides);
    }
}
