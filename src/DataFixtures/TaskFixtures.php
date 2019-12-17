<?php

namespace App\DataFixtures;

use App\Entity\Task;
use App\Manager\TaskManager;
use App\Validator\TaskValidator;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;

class TaskFixtures extends Fixture implements DependentFixtureInterface
{
    /**
     * @var TaskManager
     */
    private $taskManager;

    /**
     * @var TaskValidator
     */
    private $validator;

    public function __construct(TaskValidator $validator, TaskManager $taskManager)
    {
        $this->validator = $validator;
        $this->taskManager = $taskManager;
    }

    public function load(ObjectManager $manager)
    {
        for ($i = 0; $i < 50; ++$i) {
            $instance = $this->initialise(new Task());

            $this->checkAndPersist($manager, $instance);
        }
        $manager->flush();
    }

    private function checkAndPersist(ObjectManager $manager, Task $instance)
    {
        $this->taskManager->initialise($instance);

        if ($this->validator->isValid($instance)) {
            $manager->persist($instance);
        } else {
            var_dump('Validator : '.$this->validator->getErrors($instance));
        }
    }

    private function initialise(Task $instance): Task
    {
        $faker = Factory::create('fr_FR');

        $instance
            ->setTitle($faker->realText(20))
            ->setCreatedAt($faker->dateTimeBetween($startDate = '-2 years', $endDate = 'now', $timezone = 'Europe/Paris'))
            ->setContent($faker->realText(200))
            ->setIsDone($faker->boolean);

        if ($faker->boolean) {
            $instance->setUser($this->getReference(UserFixtures::KEY.'-'.mt_rand(0, count(userFixtures::DATA) - 1)));
        }

        return $instance;
    }

    public function getDependencies()
    {
        return [
            UserFixtures::class,
        ];
    }
}
