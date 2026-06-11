<?php

namespace Tests\Unit;

use App\Models\Functions;
use App\Models\User;
use App\Notifications\FunctionCreated;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class FunctionCreateTest extends TestCase
{
	use RefreshDatabase;

	public function test_it_creates_a_function_and_its_effects(): void
	{
		DB::table('categories')->insert([
			'category' => 'Services',
		]);

		$admin = User::create([
			'name' => 'Admin User',
			'email' => 'admin@example.com',
			'password' => bcrypt('password'),
			'role' => 'admin',
		]);

		Notification::fake();

		$response = $this->actingAs($admin)->post('/functions/store', [
			'name' => 'School',
			'category' => 'Services',
			'image' => UploadedFile::fake()->image('school.jpg'),
			'Safety' => 1,
			'Recreation' => 2,
			'Environmental_Quality' => 3,
			'Services' => 4,
			'Mobility' => 5,
		]);

		$response->assertRedirect(route('functions.index'));

		$function = Functions::where('name', 'School')->first();

		$this->assertNotNull($function);

		$this->assertDatabaseHas('effects', [
			'id' => $function->id,
			'Safety' => 1,
			'Recreation' => 2,
			'Environmental Quality' => 3,
			'Services' => 4,
			'Mobility' => 5,
		]);

		Notification::assertSentTo($admin, FunctionCreated::class);

		if ($function && $function->image) {
			$imagePath = public_path($function->image);

			if (file_exists($imagePath)) {
				unlink($imagePath);
			}
		}
	}
}
