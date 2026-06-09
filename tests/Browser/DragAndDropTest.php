<?php

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class DragAndDropTest extends DuskTestCase
{
    public function test_drag_and_drop()
    {
        $this->browse(function (Browser $browser) {

            $browser->visit('http://groepjeb.test/')
                ->type('email', 'test@admin.com')
                ->type('password', 'test')
                ->press('Login')
                ->waitForLocation('/grid')

                ->assertPresent('#function6')
                ->assertPresent('.gridCell[data-id="1"]')

                ->drag('#function6', '.gridCell[data-id="1"]')
                ->pause(10000);
        });
    }
}