<?php

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Illuminate\Support\Str;

class CreateFunctionTest extends DuskTestCase
{
    public function testFormElements()
    {
        $this->browse(function (Browser $browser) {

        $uniqueName = 'test function ' . Str::uuid();

        $browser->visit('http://groepjeb.test/')
            ->type('email', 'test@admin.com')
            ->type('password', 'test')
            ->press('Login')

            ->waitForLocation('/grid')
            ->clickLink('Functions')
            ->waitForLocation('/overview')

            ->press('Create function')
            ->assertSee('Create Function')

            ->attach('image', public_path('images/GasStation.png'))
            ->select('category', 'Mobility')
            ->select('related_function', '7')
            ->type('relationship_recreation', '3')
            ->type('relationship_mobility', '-5')
            ->type('Safety', '2')
            ->type('Services', '8')
            ->press('Create function')
            ->pause(2000)

            ->waitForText('The name field is required.')
            ->assertSee('The name field is required.')

            ->type('#input-name', $uniqueName)
            ->attach('image', public_path('images/GasStation.png'))
            ->press('Create function')

            ->waitForLocation('/overview')
            ->assertSee($uniqueName)
            
            ->clickLink('Grid')
            ->waitForLocation('/grid')
            
            ->assertSee($uniqueName, '@functionsList');

            $browser->pause(3000);
        });
    }
}