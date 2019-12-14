<?php

namespace App\Tests\Unitaire\Entity;

use App\Entity\User;
use App\Manager\UserManager;
use Faker\Factory;
use Faker\Generator;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserManagerTest extends WebTestCase
{
    /**
     * @var User
     */
    protected static $user;

    /**
     * @var UserManager
     */
    protected static $manager;

    /**
     * @var Generator
     */
    protected static $faker;

    public static function setUpBeforeClass(): void
    {
        $kernel = self::bootKernel();
        self::$manager = $kernel->getContainer()->get(UserManager::class);
        self::$faker = Factory::create('fr_FR');
    }

    public function testCreateUser()
    {
        $user = new User();
        $user
            ->setUsername(self::$faker->userName)
            ->setEmail(self::$faker->email)
            ->setPlainPassword(self::$faker->password);

        $this->assertTrue(self::$manager->update($user));
    }

    public function testCreateUserKo()
    {
        $user = new User();
        $user
            ->setUsername('ab')
            ->setRoles(['ROLE_USER'])
            ->setEmail(self::$faker->email)
            ->setPlainPassword(self::$faker->password);

        $this->assertFalse(self::$manager->update($user));
        $this->assertNotNull(self::$manager->getErrors($user));
    }

    public function testMessageError()
    {
        $error = 'liste des erreurs';

        self::$user = $this->getMockBuilder('App\Entity\User')
            ->disableOriginalConstructor()
            ->getMock();

        self::$manager = $this->getMockBuilder('App\Manager\UserManager')
            ->disableOriginalConstructor()
            ->getMock();

        self::$manager
            ->expects($this->once())
            ->method('getErrors')
            ->with(self::$user)
            ->will($this->returnValue($error));

        $this->assertEquals($error, self::$manager->getErrors(self::$user));
    }
}
