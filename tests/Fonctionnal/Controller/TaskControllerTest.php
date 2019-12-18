<?php

namespace App\Tests\Fonctionnal\Controller;

use App\Entity\Task;
use App\Repository\TaskRepository;
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

    /**
     * @var TaskRepository
     */
    private $taskRepo;

    protected function setUp(): void
    {
        $this->browser = static::createClient();
        $this->repo = $this->browser->getContainer()->get(UserRepository::class);
        $this->taskRepo = $this->browser->getContainer()->get(TaskRepository::class);
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
        $this->browser->request(
            'GET',
            'tasks'
        );

        $this->assertSame(200, $this->browser->getResponse()->getStatusCode());
    }

    public function testTaskModify()
    {
        /** @var Task $task */
        $tasks = $this->taskRepo->findAll();
        $this->assertGreaterThan(0, count($tasks));
        $task = $tasks[0];
        $this->assertNotNull($task->getId());

        $crawler = $this->browser->request(
            'GET',
            'tasks/'.$task->getId().'/edit'
        );

        $this->assertSame(200, $this->browser->getResponse()->getStatusCode());
        $form = $crawler->selectButton('save')->form();

        $title = 'Nouveau titre';
        $form['task[title]'] = $title;

        $this->browser->submit($form);
        $crawler = $this->browser->followRedirect();

        $this->assertStringContainsString($title, $crawler->text());
    }
}
