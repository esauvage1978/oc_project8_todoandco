<?php

namespace App\Tests\Unitaire\Entity;

use App\Entity\User;
use App\Manager\UserManager;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserManagerTest extends WebTestCase
{
    /**
     * @var User
     */
    protected static $entity;

    /**
     * @var UserManager
     */
    protected static $manager;

    public static function setUpBeforeClass(): void
    {
        $kernel = self::bootKernel();
        self::$entity = new User();
    }

    public function testMessageError()
    {
        $values = [
            'id' => '1',
            'username' => 'test',
            'email' => 'test@live.fr',
        ];
        $error = 'liste des erreurs';

        self::$entity = $this->getMockBuilder('App\Entity\User')
            ->disableOriginalConstructor()
            ->getMock();

        self::$manager = $this->getMockBuilder('App\Manager\UserManager')
            ->disableOriginalConstructor()
            ->getMock();

        self::$manager
            ->expects($this->once())
            ->method('getErrors')
            ->with(self::$entity)
            ->will($this->returnValue($error));

        $this->assertEquals($error, self::$manager->getErrors(self::$entity));
    }
}
