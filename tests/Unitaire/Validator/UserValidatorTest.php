<?php

namespace App\Tests\Unitaire\Entity;

use App\Entity\User;
use App\Validator\UserValidator;
use Faker\Factory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserValidatorTest extends WebTestCase
{
    /**
     * @var User
     */
    protected static $entity;

    /**
     * @var UserValidator
     */
    protected static $validator;

    /**
     * @var Factory
     */
    protected static $faker;

    public static function setUpBeforeClass(): void
    {
        $kernel = self::bootKernel();
        self::$validator = $kernel->getContainer()->get(UserValidator::class);
        self::$entity = new User();
        self::$faker = Factory::create('fr_FR');
    }

    protected function setUp(): void
    {
        self::$entity->setUsername(self::$faker->userName);
        self::$entity->setEmail(self::$faker->email);
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
    public function testValidatorKoNameLess3()
    {
        $value = 'ae';
        self::$entity->setUsername($value);
        $retour = self::$validator->isValid(self::$entity);
        $this->assertFalse($retour);
        $error = self::$validator->getErrors((self::$entity));
        $this->assertStringContainsString('doit avoir plus de 3 caractères', $error);
    }

    /**
     * @depends testValidatorOK
     */
    public function testValidatorKoNameGreater25()
    {
        while (strlen(self::$entity->getUsername()) <= 25) {
            self::$entity->setUsername(self::$faker->text(30));
        }
        $retour = self::$validator->isValid(self::$entity);
        $this->assertFalse($retour);
    }
}
