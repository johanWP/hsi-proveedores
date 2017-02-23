<?php

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Hash;
use App\User;
/**
 * Class AcachaAdminLTELaravelTest.
 */
class AcachaAdminLTELaravelTest extends TestCase
{
//    use DatabaseMigrations;

    /*
     * Overwrite createApplication to add Http Kernel
     * see: https://github.com/laravel/laravel/pull/3943
     *      https://github.com/laravel/framework/issues/15426
     */
    public function createApplication()
    {
        $app = require __DIR__.'/../bootstrap/app.php';

        $app->make(Illuminate\Contracts\Http\Kernel::class);

        $app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

        return $app;
    }

    /**
     * Set up tests.
     */
    public function setUp()
    {
        parent::setUp();
        App::setLocale('es');
    }

    /**
     * Test Landing Page.
     *
     * @return void
     */
    public function testLandingPage()
    {
        $this->visit('/home')
            ->seePageIs('/login');
    }

    /**
     * Test Landing Page.
     *
     * @return void
     */
    public function testLandingPageWithUserLogged()
    {
//        $user = factory(\App\User::class)->create();
        $user = App\User::find(1);
        $this->actingAs($user)
            ->visit('/home')
            ->see('Usted está en el sistema!')
            ->see($user->name);
    }

    /**
     * Test Login Page.
     *
     * @return void
     */
    public function testLoginPage()
    {
        $this->visit('/login')
            ->see('Ingrese su número de CUIT');
    }

    /**
     * Test Login.
     *
     * @return void
     */
    public function testLogin()
    {
//        $user = factory(App\User::class)->create(['password' => Hash::make('passw0RD')]);
        $user = App\User::find(1);
        $this->visit('/login')
            ->type($user->cuit, 'cuit')
            ->type('123456', 'password')
            ->press('Iniciar Sesión')
            ->seePageIs('/facturas');
    }

    /**
     * Test Login.
     *
     * @return void
     */
    public function testLoginRequiredFields()
    {
        $this->visit('/login')
            ->type('', 'cuit')
            ->type('', 'password')
            ->press('Iniciar Sesión')
            ->see('El campo cuit es obligatorio')
            ->see('El campo password es obligatorio');
    }


    /**
     * Test Password reset Page.
     *
     * @return void
     */
    public function testPasswordResetPage()
    {
        $this->visit('/password/reset')
            ->see('Restablecer Contraseña');
    }

    /**
     * Test home page is only for authorized Users.
     *
     * @return void
     */
    public function testHomePageForUnauthenticatedUsers()
    {
        $this->visit('/home')
            ->seePageIs('/login');
    }

    /**
     * Test home page works with Authenticated Users.
     *
     * @return void
     */
    public function testHomePageForAuthenticatedUsers()
    {
//        $user = factory(App\User::class)->create();
        $user = App\User::find(1);
        $this->actingAs($user)
            ->visit('/home')
            ->see($user->name);
    }

    /**
     * Test log out.
     *
     * @return void
     */
    public function testLogout()
    {
//        $user = factory(App\User::class)->create();
        $user = App\User::find(1);
        $form = $this->actingAs($user)->visit('/home')->getForm('logout');

        $this->actingAs($user)
            ->visit('/home')
            ->makeRequestUsingForm($form)
            ->seePageIs('/login');
    }

    /**
     * Test 404 Error page.
     *
     * @return void
     */
    public function test404Page()
    {
        $this->get('asdasdjlapmnnk')
            ->seeStatusCode(404);
    }


    /**
     * Test send password reset.
     *
     * @return void
     */
    public function testSendPasswordReset()
    {
//        $user = factory(App\User::class)->create();
        $user = App\User::find(1);
        $this->visit('password/reset')
            ->type($user->email, 'email')
            ->press('Enviar el enlace para restablecer la contraseña')
            ->see('¡Se ha enviado el correo para restablecer su contraseña!');
    }

    /**
     * Test send password reset user not exists.
     *
     * @return void
     */
    public function testSendPasswordResetUserNotExists()
    {
        $this->visit('password/reset')
            ->type('notexistingemail@gmail.com', 'email')
            ->press('Enviar el enlace para restablecer la contraseña')
            ->see('No podemos encontrar esa dirección de correo.');
    }
}
