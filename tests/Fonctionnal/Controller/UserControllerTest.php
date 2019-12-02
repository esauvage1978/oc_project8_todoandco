<?php

namespace App\Tests\Fonctionnal\Controller;

use Faker\Factory;
use Faker\Generator;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerTest extends webTestCase
{
    /**
     * @var Generator
     */
    protected static $faker;

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

    public function testEmptyValue()
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

    public function testUsernamebadValue()
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

    public function testCreateUser()
    {
        $browser = static::createClient();

        $crawler = $browser->request(
            'GET',
            'users/create'
        );

        $this->assertSame(200, $browser->getResponse()->getStatusCode());
        $form = $crawler->selectButton('create')->form();

        $password = self::$faker->password;

        $form['user[username]'] = self::$faker->userName;
        $form['user[password][first]'] = $password;
        $form['user[password][second]'] = $password;
        $form['user[email]'] = self::$faker->email;

        $browser->submit($form);
        $crawler = $browser->followRedirect();

        $this->assertSame($crawler->getUri(), 'http://localhost/users', 'Les url sont différentes');
    }
}
