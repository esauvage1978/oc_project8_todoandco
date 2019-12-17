<?php

namespace App\Tests\Fonctionnal\Security;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserAnonymousTest extends webTestCase
{
    public function testUserCreate()
    {
        $browser = static::createClient();
        $browser->request(
            'GET',
            'users/create'
        );
        $this->assertSame(302, $browser->getResponse()->getStatusCode());
    }

    public function testUserList()
    {
        $browser = static::createClient();
        $browser->request(
            'GET',
            'users'
        );
        $this->assertSame(302, $browser->getResponse()->getStatusCode());
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

    public function testTaskList()
    {
        $browser = static::createClient();
        $browser->request(
            'GET',
            'tasks'
        );
        $this->assertSame(302, $browser->getResponse()->getStatusCode());
    }
}
