<?php

namespace App\Tests\Fonctionnal\Controller;

use App\Repository\UserRepository;
use Faker\Factory;
use Faker\Generator;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class UserControllerTest extends webTestCase
{
    /**
     * @var Generator
     */
    protected static $faker;

    /**
     * @var KernelBrowser
     */
    private $browser;

    /**
     * @var UserRepository
     */
    private $repo;

    protected function setUp(): void
    {
        $this->browser = static::createClient();
        $this->repo = $this->browser->getContainer()->get(UserRepository::class);
        $this->logIn();
    }

    private function logIn()
    {
        $session = $this->browser->getContainer()->get('session');

        $firewallName = 'main';
        $firewallContext = 'main';

        $user = $this->repo->findOneBy(['username' => 'Manu']);
        $token = new UsernamePasswordToken($user, null, $firewallName, ['ROLE_ADMIN']);

        $session->set('_security_'.$firewallContext, serialize($token));
        $session->save();

        $cookie = new Cookie($session->getName(), $session->getId());
        $this->browser->getCookieJar()->set($cookie);
    }

    public static function setUpBeforeClass(): void
    {
        self::$faker = Factory::create('fr_FR');
    }

    public function testUserCreateEmptyValue()
    {
        $crawler = $this->browser->request(
            'GET',
            'users/create'
        );

        $this->assertSame(200, $this->browser->getResponse()->getStatusCode());
        $form = $crawler->selectButton('create')->form();

        $form['user[username]'] = '';
        $form['user[plainpassword][first]'] = '';
        $form['user[plainpassword][second]'] = '';
        $form['user[email]'] = '';

        $crawler = $this->browser->submit($form);

        $this->assertSame($crawler->getUri(), 'http://localhost/users/create');
    }

    public function testUserCreateUsernamebadValue()
    {
        $crawler = $this->browser->request(
            'GET',
            'users/create'
        );

        $this->assertSame(200, $this->browser->getResponse()->getStatusCode());
        $form = $crawler->selectButton('create')->form();

        $form['user[username]'] = '2';
        $form['user[plainpassword][first]'] = 'test';
        $form['user[plainpassword][second]'] = 'test';
        $form['user[email]'] = 'test@live.fr';

        $crawler = $this->browser->submit($form);

        $this->assertSame($crawler->getUri(), 'http://localhost/users/create');

        $this->assertStringContainsString('doit avoir plus de 3 caractères', $crawler->text());
    }

    public function testUserCreateUser()
    {
        $crawler = $this->browser->request(
            'GET',
            'users/create'
        );

        $this->assertSame(200, $this->browser->getResponse()->getStatusCode());
        $form = $crawler->selectButton('create')->form();

        $password = self::$faker->password.'13246456';
        $username = self::$faker->userName;
        $form['user[username]'] = $username;
        $form['user[plainpassword][first]'] = $password;
        $form['user[plainpassword][second]'] = $password;
        $form['user[email]'] = self::$faker->email;

        $this->browser->submit($form);
        $crawler = $this->browser->followRedirect();

        $this->assertSame($crawler->getUri(), 'http://localhost/users');

        $this->assertStringContainsString($username, $crawler->text());

        return $username;
    }

    /**
     * @depends testUserCreateUser
     */
    public function testUserCreateDoublonUsername(string $username)
    {
        $crawler = $this->browser->request(
            'GET',
            'users/create'
        );

        $this->assertSame(200, $this->browser->getResponse()->getStatusCode());
        $form = $crawler->selectButton('create')->form();

        $password = self::$faker->password.'13246456';

        $form['user[username]'] = $username;
        $form['user[plainpassword][first]'] = $password;
        $form['user[plainpassword][second]'] = $password;
        $form['user[email]'] = self::$faker->email;

        $crawler = $this->browser->submit($form);

        $this->assertStringContainsString('est déjà utilisé', $crawler->text());
    }

    /**
     * @depends testUserCreateUser
     */
    public function testUserModify(string $username)
    {
        $userId = $this->repo->findOneBy(['username' => $username])->getId();
        $crawler = $this->browser->request(
            'GET',
            'users/'.$userId.'/edit'
        );

        $this->assertSame(200, $this->browser->getResponse()->getStatusCode(), 'show page');
        $form = $crawler->selectButton('modify')->form();

        $password = 'U123456789';
        $email = self::$faker->email;
        $form['user[username]'] = $username;
        $form['user[plainpassword][first]'] = $password;
        $form['user[plainpassword][second]'] = $password;
        $form['user[email]'] = $email;

        $this->browser->submit($form);
        $crawler = $this->browser->followRedirect();

        $this->assertStringContainsString($email, $crawler->text());
    }

    /**
     * @depends testUserModify
     */
    public function testUserLogout()
    {
        $this->browser->request(
            'GET',
            'logout'
        );
        $this->assertSame(302, $this->browser->getResponse()->getStatusCode(), 'logout');
    }
}
