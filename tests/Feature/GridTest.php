<?php

namespace Tests\Feature;

use App\Models\GridCell;
use Database\Seeders\GridCellSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GridTest extends TestCase
{
    use RefreshDatabase;
    
    /** @test */
    public function test_the_city_is_shown_in_a_grid()
    { 
        $this->seed(GridCellSeeder::class);
        
        $response = $this->get('/grid');

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

        $response = $this->get('/grid');
        $response->assertSee('available');
        $response->assertSee('occupied');
    }

    /** @test */
    public function test_grid_cell_stays_a_square()
    {
        $this->seed(GridCellSeeder::class);

        $response = $this->get('/grid');
        $response->assertSee('grid-cell');
    }

    public function test_page_is_shown_without_grid_cells()
    {
        $response = $this->get('/grid');

        $response->assertStatus(200);
        $response->assertSee('City area');
    }
}
