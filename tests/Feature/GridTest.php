<?php

namespace Tests\Feature;

use App\Models\GridCell;
use Database\Seeders\GridCellSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GridTest extends TestCase
{
    use RefreshDatabase;
    
    public function the_city_is_shown_in_a_grid()
    { 
        $this->seed(GridCellSeeder::class);
        
        $response = $this->get('/grid');

        $response->assertStatus(200);
        $response->assertViewHas('cells', function ($cells) {
            return $cells->count() === 12;
        });

        $response->assertSee('metropolis-grid');
    }

    public function availeble_grid_cells()
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

    public function grid_cell_stays_a_square()
    {
        $response = $this->get('/grid');
        $response->assertSee('grid-cell');
    }
}
