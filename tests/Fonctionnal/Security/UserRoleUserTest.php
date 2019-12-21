<?php

namespace App\Tests\Fonctionnal\Security;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class UserRoleUserTest extends webTestCase
{
    /**
     * @var KernelBrowser
     */
    private $browser;

    /**
     * var UserRepository.
     */
    private $repo;

    /**
     * var User.
     */
    private $user;

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

        $this->user = $this->repo->findOneBy(['username' => 'Paul']);

        $token = new UsernamePasswordToken($this->user, null, $firewallName, ['ROLE_USER']);

        $session->set('_security_'.$firewallContext, serialize($token));
        $session->save();

        $cookie = new Cookie($session->getName(), $session->getId());
        $this->browser->getCookieJar()->set($cookie);
    }

    public function testUserModify()
    {
        $this->browser->request(
            'GET',
            'users/'.$this->user->getId().'/edit'
        );
        $this->assertSame(403, $this->browser->getResponse()->getStatusCode());
    }

    /**
     * @dataProvider urlAndCode
     */
    public function testUrlStatic($url, $code)
    {
        $this->browser->request(
            'GET',
            $url
        );
        $this->assertSame($code, $this->browser->getResponse()->getStatusCode());
    }

    public function urlAndCode()
    {
        return [
            ['users', 403],
            ['users/create', 403],
            ['tasks', 200],
            ['tasks/create', 200],
        ];
    }
}
