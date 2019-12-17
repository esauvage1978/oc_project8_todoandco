<?php

namespace App\Tests\Fonctionnal\Controller;

use App\Repository\UserRepository;
use Faker\Factory;
use Faker\Generator;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class TaskControllerTest extends webTestCase
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

    public function testShowList()
    {
        $crawler = $this->browser->request(
            'GET',
            'tasks'
        );

        $this->assertSame(200, $this->browser->getResponse()->getStatusCode());
    }
}
