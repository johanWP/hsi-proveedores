<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class RolePermissionTest extends TestCase
{

    private $user;

    public function createApplication()
    {
        $app = require __DIR__.'/../bootstrap/app.php';

        $app->make(Illuminate\Contracts\Http\Kernel::class);

        $app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

        return $app;
    }

    public function setUp()
    {
        parent::setUp();
        App::setLocale('es');
        $this->user = App\User::find(1);
    }

    /**
     * A basic test example.
     *
     * @return void
     */
//    public function testExample()
//    {
//        $this->visit('/roles')
//            ->type('rol de prueba', 'name')
//            ->type('descripción de prueba')
//            ->click('Guardar')
//            ->
//    }
}
