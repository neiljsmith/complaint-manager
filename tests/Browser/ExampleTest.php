<?php
/*

https://medium.com/@splatEric/laravel-dusk-on-homestead-dc5711987595

# makes sure all your repos are up to date
sudo apt-get update

# chrome dependencies I think
sudo apt-get -y install libxpm4 libxrender1 libgtk2.0-0 libnss3 libgconf-2-4

# chromium is what I had success with on Codeship, so seemed a good option
sudo apt-get install chromium-browser

# XVFB for headless applications
sudo apt-get -y install xvfb gtk2-engines-pixbuf

# fonts for the browser
sudo apt-get -y install xfonts-cyrillic xfonts-100dpi xfonts-75dpi xfonts-base xfonts-scalable

# support for screenshot capturing
sudo apt-get -y install imagemagick x11-apps

Once all this has run through, you need to fire up xvfb on your homestead box. If you’re planning to do this on a regular basis, you’ll want to get this setup on boot, but for the sake of testing things out:

Xvfb -ac :0 -screen 0 1280x1024x16 &

*/

namespace Tests\Browser;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ExampleTest extends DuskTestCase
{
    /**
     * A basic browser test example.
     *
     * @return void
     */
    public function testBasicExample()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                    ->assertSee('Login');
        });
    }
}
