<?php

namespace Tests\Feature;

use App\Models\Functions;
use App\Models\GridCell;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class SavedGridOverviewTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function test_saved_grid_overview_lists_saved_snapshots()
    {
        $user = User::create([
            'name' => 'Planner User',
            'email' => 'planner@example.com',
            'password' => 'password',
            'role' => 'cityplanner',
        ]);

        $this->actingAs($user);

        DB::table('categories')->insert(['category' => 'Safety']);

        $function = Functions::create([
            'name' => 'Park',
            'image' => 'images/park.png',
            'category' => 'Safety',
        ]);

        $cell = GridCell::create([
            'x_coordinate' => 0,
            'y_coordinate' => 0,
            'is_available' => false,
            'destination_type' => $function->id,
        ]);

        DB::table('saved_grid')->insert([
            'name' => 'Morning layout',
            'grid_cell_id' => $cell->id,
            'item_type' => 'function',
            'function_id' => $function->id,
            'event_id' => null,
            'recurring_id' => null,
            'occurs_at' => null,
            'route_order' => null,
            'created_at' => '2026-06-23 10:00:00',
            'updated_at' => '2026-06-23 10:00:00',
        ]);

        $response = $this->get('/saved-grids');

        $response->assertOk();
        $response->assertViewHas('savedGrids', function ($savedGrids) {
            return $savedGrids->count() === 1;
        });
        $response->assertSee('Saved grids');
        $response->assertSee('Morning layout');
    }

    /** @test */
    public function test_saved_grid_can_be_loaded_from_the_overview_page()
    {
        $user = User::create([
            'name' => 'Planner User',
            'email' => 'planner2@example.com',
            'password' => 'password',
            'role' => 'cityplanner',
        ]);

        $this->actingAs($user);

        DB::table('categories')->insert(['category' => 'Safety']);

        $function = Functions::create([
            'name' => 'Park',
            'image' => 'images/park.png',
            'category' => 'Safety',
        ]);

        $cell = GridCell::create([
            'x_coordinate' => 0,
            'y_coordinate' => 0,
            'is_available' => true,
            'destination_type' => null,
        ]);

        DB::table('saved_grid')->insert([
            'name' => 'Morning layout',
            'grid_cell_id' => $cell->id,
            'item_type' => 'function',
            'function_id' => $function->id,
            'event_id' => null,
            'recurring_id' => null,
            'occurs_at' => null,
            'route_order' => null,
            'created_at' => '2026-06-23 10:00:00',
            'updated_at' => '2026-06-23 10:00:00',
        ]);

        $response = $this->post('/saved-grids/load', [
            'name' => 'Morning layout',
            'created_at' => '2026-06-23 10:00:00',
        ]);

        $response->assertRedirect('/grid');
        $response->assertSessionHas('status', 'Loaded grid "Morning layout".');

        $this->assertDatabaseHas('grid_cells', [
            'id' => $cell->id,
            'destination_type' => $function->id,
            'is_available' => 0,
        ]);
    }
}