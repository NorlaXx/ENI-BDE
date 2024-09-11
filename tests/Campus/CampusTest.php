<?php

namespace App\Tests\Campus;

use App\Entity\Campus;
use App\Repository\CampusRepository;
use PHPUnit\Framework\TestCase;

class CampusTest extends TestCase
{
    public function testFindALl(){
        $campus = new Campus();
        $campus->setName('ENI - Rennes');
        $campus->setPostalCode('35131');
        $campus->setLongitude(1.234567);
        $campus->setLatitude(1.234567);
        $campus->setAddress('8 rue Léo Lagrange');
        $campus->setFileName('file.jpg');
        $campus->setNblimitPlaces(10);
        $campus->setCity('Chartres de bretagne');

        $campusRepository = $this->createMock(CampusRepository::class);
        $campusRepository->method('findAll')
            ->willReturn([$campus]);

        $this->assertCount(1, $campusRepository->findAll());
        $this->assertEquals('ENI - Rennes', $campus->getName());
        $this->assertEquals('35131', $campus->getPostalCode());
        $this->assertEquals(1.234567, $campus->getLongitude());
        $this->assertEquals(1.234567, $campus->getLatitude());
        $this->assertEquals('8 rue Léo Lagrange', $campus->getAddress());
        $this->assertEquals('file.jpg', $campus->getFileName());
        $this->assertEquals(10, $campus->getNblimitPlaces());
        $this->assertEquals('Chartres de bretagne', $campus->getCity());
    }
}
