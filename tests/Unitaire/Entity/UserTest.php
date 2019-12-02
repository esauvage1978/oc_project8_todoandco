<?php

namespace App\Tests\Unitaire\Entity;

use App\Entity\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    /**
     * @var User
     */
    protected static $entity;

    public static function setUpBeforeClass(): void
    {
        self::$entity = new User();
    }

    public function testId()
    {
        $this->assertNull(self::$entity->getId());
    }

    public function testUsername()
    {
        $value = 'testUsername';

        $this->assertNull(self::$entity->getUsername());
        self::$entity->setUsername($value);
        $this->assertSame($value, self::$entity->getUsername());

        self::$entity = new User();
    }

    public function testPassword()
    {
        $value = 'testPassword';

        $this->assertNull(self::$entity->getPassword());
        self::$entity->setPassword($value);
        $this->assertSame($value, self::$entity->getPassword());
    }

    public function testEmail()
    {
        $value = 'testemail@live.fr';

        $this->assertNull(self::$entity->getEmail(), 'test null');
        self::$entity->setEmail($value);
        $this->assertSame($value, self::$entity->getEmail(), 'test same');
    }
}
