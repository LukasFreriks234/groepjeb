<?php

namespace Tests\Unit;

use App\Models\Effects;
use App\Models\Functions;
use App\Models\User;
use App\Notifications\FunctionEdited;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class EffectEditTest extends TestCase
{
	use RefreshDatabase;

	public function test_it_updates_the_effect_points_of_a_function(): void
	{
		DB::table('categories')->insert([
			'category' => 'Services',
		]);

		$function = Functions::create([
			'name' => 'School',
			'image' => 'images/functions/school.png',
			'category' => 'Services',
		]);

		Effects::create([
			'id' => $function->id,
			'Safety' => 1,
			'Recreation' => 2,
			'Environmental Quality' => 3,
			'Services' => 4,
			'Mobility' => 5,
		]);

		$admin = User::create([
			'name' => 'Admin User',
			'email' => 'admin@example.com',
			'password' => Hash::make('password'),
			'role' => 'admin',
		]);

		Notification::fake();

		$this->actingAs($admin)
			->patch('/functions/' . $function->id . '/update', [
				'name' => 'School',
				'category' => 'Services',
				'Safety' => 6,
				'Recreation' => 7,
				'Environmental_Quality' => 8,
				'Services' => 9,
				'Mobility' => 10,
			])
			->assertRedirect(route('functions.show', $function->id));

		$this->assertDatabaseHas('effects', [
			'id' => $function->id,
			'Safety' => 6,
			'Recreation' => 7,
			'Environmental Quality' => 8,
			'Services' => 9,
			'Mobility' => 10,
		]);

		Notification::assertSentTo($admin, FunctionEdited::class);
	}
}
