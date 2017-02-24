<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;

class UsuariosTest extends TestCase
{
    use DatabaseTransactions;
    
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
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testIndexUsuarios()
    {
        $this->actingAs($this->user)
            ->visit('/usuarios')
            ->see('CUIT');
    }


//    public function testAPIUsuarios()
//    {
//        $this->actingAs($this->user)
//            ->visit('/api/usuarios')
//            ->seeJsonStructure([
//              "draw",
//              "recordsTotal",
//              "recordsFiltered",
//              "data"=> [
//                  "id",
//                  "name",
//                  "cuit",
//                  "email",
//                  "created_at",
//                  "updated_at"
//                ]
//            ]);
//    }

}
