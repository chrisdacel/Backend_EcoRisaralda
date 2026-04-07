<?php

namespace Tests\Unit;

use App\Models\TuristicPlace;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TuristicPlaceModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_place_has_fillable_attributes(): void
    {
        $place = new TuristicPlace();

        $this->assertContains('name', $place->getFillable());
        $this->assertContains('description', $place->getFillable());
        $this->assertContains('localization', $place->getFillable());
        $this->assertContains('lat', $place->getFillable());
        $this->assertContains('lng', $place->getFillable());
        $this->assertContains('user_id', $place->getFillable());
        $this->assertContains('approval_status', $place->getFillable());
    }

    public function test_place_casts_open_days_to_array(): void
    {
        $place = new TuristicPlace();
        $casts = $place->getCasts();

        $this->assertArrayHasKey('open_days', $casts);
        $this->assertEquals('array', $casts['open_days']);
    }

    public function test_place_belongs_to_user(): void
    {
        $operator = User::factory()->operator()->create();
        $place = TuristicPlace::factory()->create(['user_id' => $operator->id]);

        $this->assertInstanceOf(User::class , $place->user);
        $this->assertEquals($operator->id, $place->user->id);
    }

    public function test_place_has_events_relationship(): void
    {
        $operator = User::factory()->operator()->create();
        $place = TuristicPlace::factory()->create(['user_id' => $operator->id]);

        $this->assertNotNull($place->events());
    }

    public function test_place_has_label_relationship(): void
    {
        $operator = User::factory()->operator()->create();
        $place = TuristicPlace::factory()->create(['user_id' => $operator->id]);

        $this->assertNotNull($place->label());
    }

    public function test_place_factory_creates_valid_place(): void
    {
        $operator = User::factory()->operator()->create();
        $place = TuristicPlace::factory()->create(['user_id' => $operator->id]);

        $this->assertNotNull($place->id);
        $this->assertNotNull($place->name);
        $this->assertNotNull($place->description);
        $this->assertEquals('approved', $place->approval_status);
    }
}
