<?php

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class simulationSpeedTest extends DuskTestCase
{
    public function testCanAdjustSimulationSpeed()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('http://groepjeb.test/')
                ->type('email', 'test@admin.com')
                ->type('password', 'test')
                ->press('Login')
                ->waitForLocation('/grid')
                ->pause(3000);

            // Check if simulation clock JavaScript loaded
            $browser->assertScript('window.simulationClock !== undefined');
            $browser->script('window.initialSpeed = window.simulationClock.speed;');

            // Change speed to 60 minutes per second
            $browser->type('#simulation-speed', '60')
                ->press('[data-simulation-speed-confirm]');

            // Verify speed was updated
            $browser->assertScript('window.simulationClock.speed === 60');

            // Change speed to 120 minutes per second
            $browser->type('#simulation-speed', '120')
                ->press('[data-simulation-speed-confirm]');

            // Verify speed was updated
            $browser->assertScript('window.simulationClock.speed === 120');

            // Change speed to 1 minute per second (slowest)
            $browser->type('#simulation-speed', '1')
                ->press('[data-simulation-speed-confirm]');

            // Verify speed was updated
            $browser->assertScript('window.simulationClock.speed === 1');
        });
    }

    public function testSpeedInputClampsToValidRange()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('http://groepjeb.test/')
                ->type('email', 'test@admin.com')
                ->type('password', 'test')
                ->press('Login')
                ->waitForLocation('/grid')
                ->pause(3000);

            // Check if simulation clock JavaScript loaded
            $browser->assertScript('window.simulationClock !== undefined');
            
            // Try to set speed below minimum (should clamp to 1)
            $browser->type('#simulation-speed', '0')
                ->press('[data-simulation-speed-confirm]');

            $browser->assertScript('window.simulationClock.speed === 1');

            // Try to set speed above maximum (1440 minutes = full day per second)
            $browser->type('#simulation-speed', '9999')
                ->press('[data-simulation-speed-confirm]');

            $browser->assertScript('window.simulationClock.speed === 1440');
        });
    }

    public function testSimulationVisuallySpeedsUp()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('http://groepjeb.test/')
                ->type('email', 'test@admin.com')
                ->type('password', 'test')
                ->press('Login')
                ->waitForLocation('/grid')
                ->pause(3000);

            // Check if simulation clock JavaScript loaded
            $browser->assertScript('window.simulationClock !== undefined');
            
            // Set a high speed to make the effect visible
            $browser->type('#simulation-speed', '480')
                ->press('[data-simulation-speed-confirm]');

            // Record the initial time display
            $browser->script('window.initialTime = document.querySelector("[data-simulation-clock-time]").textContent;');

            // Wait for the simulation to advance (at 480 min/s, 8 minutes pass per second)
            $browser->pause(2000);

            // Get the time after waiting
            $browser->script('window.afterTime = document.querySelector("[data-simulation-clock-time]").textContent;');

            // Verify the time has changed (simulation advanced)
            $browser->assertScript('window.initialTime !== window.afterTime');
        });
    }

    public function testSpeedPersistsAfterPageReload()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('http://groepjeb.test/')
                ->type('email', 'test@admin.com')
                ->type('password', 'test')
                ->press('Login')
                ->waitForLocation('/grid')
                ->pause(3000);

            // Check if simulation clock JavaScript loaded
            $browser->assertScript('window.simulationClock !== undefined');
            
            // Set a specific speed
            $browser->type('#simulation-speed', '100')
                ->press('[data-simulation-speed-confirm]');

            $browser->assertScript('window.simulationClock.speed === 100');

            // Reload the page
            $browser->refresh()
                ->waitForLocation('/grid');

            // Verify speed persisted (stored in localStorage)
            $browser->assertScript('window.simulationClock.speed === 100');
        });
    }
}