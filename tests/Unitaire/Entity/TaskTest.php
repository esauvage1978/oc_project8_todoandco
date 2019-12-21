<?php

namespace App\Tests\Unitaire\Entity;

use App\Entity\Task;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class TaskTest extends TestCase
{
    /**
     * @var Task
     */
    protected static $entity;

    public static function setUpBeforeClass(): void
    {
        self::$entity = new Task();
    }

    public function testId()
    {
        $this->assertNull(self::$entity->getId());
    }

    public function testTitle()
    {
        $value = 'testTitle';

        $this->assertNull(self::$entity->getTitle());
        self::$entity->setTitle($value);
        $this->assertSame($value, self::$entity->getTitle());

        self::$entity = new Task();
    }

    public function testContent()
    {
        $value = 'testContent';

        $this->assertNull(self::$entity->getContent());
        self::$entity->setContent($value);
        $this->assertSame($value, self::$entity->getContent());

        self::$entity = new Task();
    }

    public function testIsDone()
    {
        $value = true;

        $this->assertFalse(self::$entity->getIsDone());
        self::$entity->setIsDone($value);
        $this->assertTrue(self::$entity->getIsDone());

        self::$entity = new Task();
    }

    public function testCreatedAt()
    {
        $value = new \DateTime();

        $this->assertNull(self::$entity->getCreatedAt());
        self::$entity->setCreatedAt($value);
        $this->assertSame($value, self::$entity->getCreatedAt());

        self::$entity = new Task();
    }

    public function testUser()
    {
        $this->assertNull(self::$entity->getUser());
        $user = new User();
        self::$entity->setUser($user);
        $this->assertSame($user, self::$entity->getUser());
    }
}
