<?php

namespace Tests\Unit;

use App\Models\Category;
use App\Models\Functions;
use App\Models\GridCell;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DragNDropTest extends TestCase
{
	use RefreshDatabase;

	public function test_it_swaps_two_grid_cells_when_a_function_is_moved(): void
	{
		DB::table('categories')->insert([
			'category' => 'Services',
		]);

		$firstFunction = Functions::create([
			'name' => 'School',
			'image' => 'school.png',
			'category' => 'Services',
		]);

		$secondFunction = Functions::create([
			'name' => 'Hospital',
			'image' => 'hospital.png',
			'category' => 'Services',
		]);

		$fromCell = GridCell::create([
			'x_coordinate' => 1,
			'y_coordinate' => 1,
			'is_available' => false,
			'destination_type' => $firstFunction->id,
		]);

		$toCell = GridCell::create([
			'x_coordinate' => 2,
			'y_coordinate' => 1,
			'is_available' => false,
			'destination_type' => $secondFunction->id,
		]);

		$admin = User::create([
			'name' => 'Admin User',
			'email' => 'admin@example.com',
			'password' => bcrypt('password'),
			'role' => 'admin',
		]);

		$this->actingAs($admin)
			->post('/grid/move-function', [
				'from_cell_id' => $fromCell->id,
				'to_cell_id' => $toCell->id,
				'function_id' => $firstFunction->id,
			])
			->assertOk()
			->assertJson([
				'success' => true,
			]);

		$this->assertDatabaseHas('grid_cells', [
			'id' => $fromCell->id,
			'destination_type' => $secondFunction->id,
			'is_available' => false,
		]);

		$this->assertDatabaseHas('grid_cells', [
			'id' => $toCell->id,
			'destination_type' => $firstFunction->id,
			'is_available' => false,
		]);
	}
}
