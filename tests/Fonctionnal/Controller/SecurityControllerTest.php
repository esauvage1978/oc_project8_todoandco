<?php

namespace App\Tests\Fonctionnal\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SecurityControllerTest extends webTestCase
{
    public function testLoginLogout()
    {
        $browser = static::createClient();

        $crawler = $browser->request('GET', '/login');

        $form = $crawler->selectButton('login')->form();

        $form['username'] = 'Manu';
        $form['password'] = 'u12345678';

        $browser->submit($form);
        $crawler = $browser->followRedirect();
        $this->assertSame($crawler->getUri(), 'http://localhost/', 'Les urls de login sont différentes');

        $browser->request('GET', '/logout');
        $crawler = $browser->followRedirect();
        $this->assertSame($crawler->getUri(), 'http://localhost/', 'Les urls de logout sont différentes');
    }
}
