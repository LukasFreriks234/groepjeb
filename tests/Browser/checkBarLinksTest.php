<?php

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class checkBarLinksTest extends DuskTestCase
{
    public function testCRUDBar()
{
    $this->browse(function (Browser $browser) {

        $browser->visit('http://groepjeb.test/')
            ->type('email', 'test@admin.com')
            ->type('password', 'test')
            ->press('Login')
            ->waitForLocation('/grid')
            ->assertPathIs('/grid')
            ->assertSee('Grid')
            ->assertSee('Functions')
            ->assertSee('Log out');
        $browser->pause(4000);
    });
}

}
