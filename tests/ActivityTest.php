<?php

use App\Entity\Activity;
use App\Repository\ActivityRepository;
use PHPUnit\Framework\TestCase;

class ActivityTest extends TestCase
{
    public function testFindAllWithEmpty(): void
    {
        $activityRepository = $this->createMock(ActivityRepository::class);
        $activityRepository->method('findAll')
            ->willReturn([]);

        $this->assertCount(0, $activityRepository->findAll());
    }

    public function testFindAll(): void
    {
        $activity1 = new Activity();
        $activity1->setName('Activity 1');
        $activity1->setDescription('Description 1');

        $activity2 = new Activity();
        $activity2->setName('Activity 2');
        $activity2->setDescription('Description 2');

        $activityRepository = $this->createMock(ActivityRepository::class);
        $activityRepository->method('findAll')
            ->willReturn([$activity1, $activity2]);

        $activities = $activityRepository->findAll();

        $this->assertEquals('Activity 1', $activities[0]->getName());
        $this->assertEquals('Description 1', $activities[0]->getDescription());
        $this->assertEquals('Activity 2', $activities[1]->getName());
        $this->assertEquals('Description 2', $activities[1]->getDescription());
    }
}