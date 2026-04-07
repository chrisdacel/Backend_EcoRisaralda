<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\UserNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationsApiTest extends TestCase
{
    use RefreshDatabase;

    // Using actingAs() instead of headers to support standard session-based 'auth' middleware


    public function test_user_can_list_notifications(): void
    {
        $user = User::factory()->create();
        UserNotification::create([
            'user_id' => $user->id,
            'type' => 'review',
            'title' => 'Nueva reseña',
            'message' => 'Alguien dejó una reseña en tu sitio',
        ]);

        $response = $this->actingAs($user)->getJson('/api/user/notifications');

        $response->assertStatus(200);
    }

    public function test_user_can_mark_notification_as_read(): void
    {
        $user = User::factory()->create();
        $notification = UserNotification::create([
            'user_id' => $user->id,
            'type' => 'review',
            'title' => 'Nueva reseña',
            'message' => 'Test message',
        ]);

        $response = $this->actingAs($user)->postJson(
            "/api/user/notifications/{$notification->id}/read"
        );

        $response->assertStatus(200)
                 ->assertJson(['message' => 'Notificación marcada como leída']);
    }

    public function test_user_can_archive_notification(): void
    {
        $user = User::factory()->create();
        $notification = UserNotification::create([
            'user_id' => $user->id,
            'type' => 'review',
            'title' => 'Test',
            'message' => 'Test message',
        ]);

        $response = $this->actingAs($user)->postJson(
            "/api/user/notifications/{$notification->id}/archive"
        );

        $response->assertStatus(200)
                 ->assertJson(['message' => 'Notificación archivada']);
    }

    public function test_user_can_archive_all_notifications(): void
    {
        $user = User::factory()->create();
        UserNotification::create([
            'user_id' => $user->id,
            'type' => 'review',
            'title' => 'Test 1',
            'message' => 'Message 1',
        ]);
        UserNotification::create([
            'user_id' => $user->id,
            'type' => 'event',
            'title' => 'Test 2',
            'message' => 'Message 2',
        ]);

        $response = $this->actingAs($user)->postJson('/api/user/notifications/archive-all');

        $response->assertStatus(200)
                 ->assertJson(['message' => 'Todas las notificaciones archivadas']);
    }

    public function test_unauthenticated_user_cannot_access_notifications(): void
    {
        $response = $this->getJson('/api/user/notifications');

        $response->assertStatus(401);
    }
}
