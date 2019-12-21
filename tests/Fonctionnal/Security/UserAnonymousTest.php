<?php

namespace App\Tests\Fonctionnal\Security;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserAnonymousTest extends webTestCase
{
    /**
     * @dataProvider urlAndCode
     */
    public function testUrlStatic($url, $code)
    {
        $browser = static::createClient();
        $browser->request(
            'GET',
            $url
        );
        $this->assertSame($code, $browser->getResponse()->getStatusCode());
    }

    public function urlAndCode()
    {
        return [
            ['users', 302],
            ['users/create', 302],
            ['tasks', 302],
            ['tasks/create', 302],
        ];
    }

    public function testUserModify()
    {
        $browser = static::createClient();
        $repo = $browser->getContainer()->get(UserRepository::class);
        $user = $repo->findOneBy(['username' => 'Paul']);
        $browser->request(
            'GET',
            'users/'.$user->getId().'/edit'
        );
        $this->assertSame(302, $browser->getResponse()->getStatusCode());
    }
}
