<?php

namespace Tests\Unit;

use App\Models\Functions;
use App\Models\GridCell;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class DeleteNewFunctionTest extends TestCase
{
	use RefreshDatabase;

	public function test_it_deletes_a_new_function_from_a_grid_cell(): void
	{
		DB::table('categories')->insert([
			'category' => 'Services',
		]);

		$function = Functions::create([
			'name' => 'School',
			'image' => 'images/functions/school.png',
			'category' => 'Services',
		]);

		$cell = GridCell::create([
			'x_coordinate' => 1,
			'y_coordinate' => 1,
			'is_available' => false,
			'destination_type' => $function->id,
		]);

		$admin = User::create([
			'name' => 'Admin User',
			'email' => 'admin@example.com',
			'password' => Hash::make('password'),
			'role' => 'admin',
		]);

		$this->actingAs($admin)
			->post('/remove-function', [
				'id' => $cell->id,
			])
			->assertOk()
			->assertJson([
				'success' => true,
			]);

		$this->assertDatabaseHas('grid_cells', [
			'id' => $cell->id,
			'destination_type' => null,
			'is_available' => true,
		]);
	}
}
