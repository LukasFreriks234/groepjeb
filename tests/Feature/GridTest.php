<?php

namespace Tests\Feature;

use App\Models\Functions;
use App\Models\GridCell;
use App\Models\User;
use Database\Seeders\GridCellSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class GridTest extends TestCase
{
    use RefreshDatabase;
    
    /** @test */
    public function test_the_city_is_shown_in_a_grid()
    { 
        $this->seed(GridCellSeeder::class);
        
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertViewHas('cells', function ($cells) {
            return $cells->count() === 12;
        });

        $response->assertSee('metropolis-grid');
    }

    /** @test */
    public function test_availeble_grid_cells()
    {
        GridCell::create([
            'x_coordinate' => 0, 
            'y_coordinate' => 0, 
            'is_available' => true
        ]);

        GridCell::create([
            'x_coordinate' => 1, 
            'y_coordinate' => 0, 
            'is_available' => false
        ]);

        $response = $this->get('/');
        $response->assertSee('available');
        $response->assertSee('occupied');
    }

    /** @test */
    public function test_grid_cell_stays_a_square()
    {
        $this->seed(GridCellSeeder::class);

        $response = $this->get('/');
        $response->assertSee('grid-cell');
    }

    public function test_page_is_shown_without_grid_cells()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('City area');
    }

    /** @test */
    public function test_a_named_grid_can_be_saved()
    {
        $user = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => 'password',
            'role' => 'admin',
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

        $response = $this->post('/grid/save', [
            'name' => 'Test grid',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('status', 'Saved grid "Test grid".');

        $this->assertDatabaseHas('saved_grid', [
            'name' => 'Test grid',
            'grid_cell_id' => $cell->id,
            'item_type' => 'function',
            'function_id' => $function->id,
        ]);
    }
}
