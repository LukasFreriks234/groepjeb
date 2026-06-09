<?php

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class CreateFunctionTest extends DuskTestCase
{
    public function testFormElements()
    {
        $this->browse(function (Browser $browser) {

        $browser->visit('http://groepjeb.test/')
            ->type('email', 'test@admin.com')
            ->type('password', 'test')
            ->press('Login')
            ->waitForLocation('/grid')
            ->clickLink('Functions')
            ->waitForLocation('/overview')
            ->press('Create function')
            ->assertSee('Create Function')
            ->type('#input-name', 'test function')
            ->select('category', 'Mobility')
            ->select('related_function', '7')
            ->type('relationship_recreation', '3')
            ->type('relationship_mobility', '-5')
            ->type('Safety', '2')
            ->type('Services', '8')
            ->press('Create function');
            $browser->pause(8000);
        });
    }
}