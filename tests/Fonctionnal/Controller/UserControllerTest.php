<?php

namespace App\Tests\Fonctionnal\Controller;

use App\Repository\UserRepository;
use Faker\Factory;
use Faker\Generator;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerTest extends webTestCase
{
    /**
     * @var Generator
     */
    protected static $faker;

    /**
     * @var UserRepository
     */
    protected static $repository;

    public static function setUpBeforeClass(): void
    {
        self::$faker = Factory::create('fr_FR');
    }

    public function testAnonymousShowUserCreate()
    {
        $browser = static::createClient();

        $crawler = $browser->request(
            'GET',
            'users/create'
        );
        $this->assertSame(200, $browser->getResponse()->getStatusCode(), 'Affichage de la page');
    }

    public function testAnonymousShowUserList()
    {
        $browser = static::createClient();

        $crawler = $browser->request(
            'GET',
            'users'
        );
        $this->assertSame(200, $browser->getResponse()->getStatusCode(), 'Affichage de la page');
    }

    public function testUserCreateEmptyValue()
    {
        $browser = static::createClient();

        $crawler = $browser->request(
            'GET',
            'users/create'
        );

        $this->assertSame(200, $browser->getResponse()->getStatusCode());
        $form = $crawler->selectButton('create')->form();

        $form['user[username]'] = '';
        $form['user[password][first]'] = '';
        $form['user[password][second]'] = '';
        $form['user[email]'] = '';

        $crawler = $browser->submit($form);

        $this->assertSame($crawler->getUri(), 'http://localhost/users/create', 'Les url sont différentes');
    }

    public function testUserCreateUsernamebadValue()
    {
        $browser = static::createClient();

        $crawler = $browser->request(
            'GET',
            'users/create'
        );

        $this->assertSame(200, $browser->getResponse()->getStatusCode());
        $form = $crawler->selectButton('create')->form();

        $form['user[username]'] = '2';
        $form['user[password][first]'] = 'test';
        $form['user[password][second]'] = 'test';
        $form['user[email]'] = 'test@live.fr';

        $crawler = $browser->submit($form);

        $this->assertSame($crawler->getUri(), 'http://localhost/users/create', 'Les url sont différentes');

        $this->assertStringContainsString('doit avoir plus de 3 caractères', $crawler->text());
    }

    public function testUserCreateUser()
    {
        $browser = static::createClient();

        $crawler = $browser->request(
            'GET',
            'users/create'
        );

        $this->assertSame(200, $browser->getResponse()->getStatusCode());
        $form = $crawler->selectButton('create')->form();

        $password = self::$faker->password;
        $username = self::$faker->userName;
        $form['user[username]'] = $username;
        $form['user[password][first]'] = $password;
        $form['user[password][second]'] = $password;
        $form['user[email]'] = self::$faker->email;

        $browser->submit($form);
        $crawler = $browser->followRedirect();

        $this->assertSame($crawler->getUri(), 'http://localhost/users', 'Les url sont différentes');

        $this->assertStringContainsString($username, $crawler->text());

        return $username;
    }

    /**
     * @depends testUserCreateUser
     */
    public function testUserCreateDoublonUsername(string $username)
    {
        $browser = static::createClient();

        $crawler = $browser->request(
            'GET',
            'users/create'
        );

        $this->assertSame(200, $browser->getResponse()->getStatusCode());
        $form = $crawler->selectButton('create')->form();

        $password = self::$faker->password;

        $form['user[username]'] = $username;
        $form['user[password][first]'] = $password;
        $form['user[password][second]'] = $password;
        $form['user[email]'] = self::$faker->email;

        $crawler = $browser->submit($form);

        $this->assertStringContainsString('est déjà utilisé', $crawler->text());
    }

    /**
     * @depends testUserCreateUser
     */
    public function testUserModify(string $username)
    {
        $browser = static::createClient();
        $kernel = static::bootKernel();
        self::$repository = $kernel->getContainer()->get(UserRepository::class);
        $id = self::$repository->findOneBy(['username' => $username])->getId();
        $crawler = $browser->request(
            'GET',
            'users/'.$id.'/edit'
        );

        $this->assertSame(200, $browser->getResponse()->getStatusCode());
        $form = $crawler->selectButton('modify')->form();

        $password = self::$faker->password;
        $email = self::$faker->email;
        $form['user[username]'] = $username;
        $form['user[password][first]'] = $password;
        $form['user[password][second]'] = $password;
        $form['user[email]'] = $email;

        $browser->submit($form);
        $crawler = $browser->followRedirect();

        $this->assertStringContainsString($email, $crawler->text());
    }
}
