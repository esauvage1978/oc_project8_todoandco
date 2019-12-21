<?php

namespace App\Tests\Unitaire\Entity;

use App\Entity\Task;
use App\Manager\TaskManager;
use Faker\Factory;
use Faker\Generator;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TaskManagerTest extends WebTestCase
{
    /**
     * @var Task
     */
    protected static $instance;

    /**
     * @var TaskManager
     */
    protected static $manager;

    /**
     * @var Generator
     */
    protected static $faker;

    public static function setUpBeforeClass(): void
    {
        $kernel = self::bootKernel();
        self::$manager = $kernel->getContainer()->get(TaskManager::class);
        self::$faker = Factory::create('fr_FR');
    }

    public function testCreateTask(): Task
    {
        $instance = new Task();
        $instance
            ->setTitle(self::$faker->realText(20))
            ->setContent(self::$faker->realText(200))
            ;

        $this->assertTrue(self::$manager->save($instance));

        return $instance;
    }

    public function testToogleTask()
    {
        $instance = new Task();
        $instance
            ->setTitle(self::$faker->realText(20))
            ->setContent(self::$faker->realText(200))
            ->setIsDone(false)
        ;
        self::$manager->toogle($instance);
        $this->assertTrue($instance->getIsDone());
        self::$manager->toogle($instance);
        $this->assertFalse($instance->getIsDone());
    }

    public function testCreateUserKo()
    {
        $instance = new Task();
        $instance
            ->setTitle('ab')
            ->setContent(self::$faker->realText(200));

        $this->assertFalse(self::$manager->save($instance));
        $this->assertNotNull(self::$manager->getErrors($instance));
    }

    public function testMessageError()
    {
        $error = 'liste des erreurs';

        self::$instance = $this->getMockBuilder('App\Entity\Task')
            ->disableOriginalConstructor()
            ->getMock();

        self::$manager = $this->getMockBuilder('App\Manager\TaskManager')
            ->disableOriginalConstructor()
            ->getMock();

        self::$manager
            ->expects($this->once())
            ->method('getErrors')
            ->with(self::$instance)
            ->will($this->returnValue($error));

        $this->assertEquals($error, self::$manager->getErrors(self::$instance));
    }
}
