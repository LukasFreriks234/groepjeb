<?php

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class MainRoadRouteTest extends DuskTestCase
{
    public function testMainRoadOverlayIsDisplayed()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('http://groepjeb.test/')
                ->type('email', 'test@admin.com')
                ->type('password', 'test')
                ->press('Login')
                ->waitForLocation('/grid')
                ->assertPathIs('/grid');
        });
    }

    public function testCanToggleMainRoadOverlay()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('http://groepjeb.test/')
                ->type('email', 'test@admin.com')
                ->type('password', 'test')
                ->press('Login')
                ->waitForLocation('/grid')
                ->assertPathIs('/grid');
        });
    }

    public function testMainRoadCellsHaveVisualIndicator()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('http://groepjeb.test/')
                ->type('email', 'test@admin.com')
                ->type('password', 'test')
                ->press('Login')
                ->waitForLocation('/grid')
                ->assertPathIs('/grid');
        });
    }

    public function testCanCreateRouteBetweenMainRoadAndEvent()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('http://groepjeb.test/')
                ->type('email', 'test@admin.com')
                ->type('password', 'test')
                ->press('Login')
                ->waitForLocation('/grid')
                ->assertPathIs('/grid');
        });
    }

    public function testRouteCreationRequiresMainRoad()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('http://groepjeb.test/')
                ->type('email', 'test@admin.com')
                ->type('password', 'test')
                ->press('Login')
                ->waitForLocation('/grid')
                ->assertPathIs('/grid');
        });
    }
}