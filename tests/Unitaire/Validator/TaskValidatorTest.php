<?php

namespace App\Tests\Unitaire\Entity;

use App\Entity\Task;
use App\Validator\TaskValidator;
use Faker\Factory;
use Faker\Generator;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TaskValidatorTest extends WebTestCase
{
    /**
     * @var Task
     */
    protected static $entity;

    /**
     * @var TaskValidator
     */
    protected static $validator;

    /**
     * @var Generator
     */
    protected static $faker;

    public static function setUpBeforeClass(): void
    {
        $kernel = self::bootKernel();
        self::$validator = $kernel->getContainer()->get(TaskValidator::class);
        self::$entity = new Task();
        self::$faker = Factory::create('fr_FR');
    }

    protected function setUp(): void
    {
        self::$entity->setTitle(self::$faker->realText(20));
        self::$entity->setContent(self::$faker->realText(20));
    }

    public function testValidatorOK()
    {
        $this->assertNotNull(self::$entity);
        $retour = self::$validator->isValid(self::$entity);
        $this->assertTrue($retour);
    }

    /**
     * @depends testValidatorOK
     */
    public function testValidatorKoTitleLess3()
    {
        $value = 'ae';
        self::$entity->setTitle($value);
        $retour = self::$validator->isValid(self::$entity);
        $this->assertFalse($retour);
        $error = self::$validator->getErrors((self::$entity));
        $this->assertStringContainsString('doit avoir plus de 3 caractères', $error);
    }

    /**
     * @depends testValidatorOK
     */
    public function testValidatorKoNameGreater255()
    {
        while (strlen(self::$entity->getTitle()) <= 255) {
            self::$entity->setTitle(self::$faker->text(300));
        }
        $retour = self::$validator->isValid(self::$entity);
        $this->assertFalse($retour);
    }

    /**
     * @depends testValidatorOK
     */
    public function testValidatorKoContentLess3()
    {
        $value = 'ae';
        self::$entity->setContent($value);
        $retour = self::$validator->isValid(self::$entity);
        $this->assertFalse($retour);
        $error = self::$validator->getErrors((self::$entity));
        $this->assertStringContainsString('doit avoir plus de 3 caractères', $error);
    }
}
