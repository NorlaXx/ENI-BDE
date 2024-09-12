<?php

namespace App\Tests\Lieu;

use App\Entity\Lieu;
use App\Repository\LieuRepository;
use PHPUnit\Framework\TestCase;

class LieuTest extends TestCase
{
    public function testFindAll(){
        $lieu = new Lieu();
        $lieu->setName('picine de Bréquiny');
        $lieu->setLongitude(1.234567);
        $lieu->setLatitude(1.234567);
        $lieu->setAddress('12 Bd Albert 1er');
        $lieu->setPostalCode('35000');
        $lieu->setCity('Rennes');
        $lieu->setFileName('file.jpg');

        $lieuRepository = $this->createMock(LieuRepository::class);
        $lieuRepository->method('findAll')
            ->willReturn([$lieu]);

        $this->assertCount(1, $lieuRepository->findAll());
        $this->assertEquals('picine de Bréquiny', $lieu->getName());
        $this->assertEquals(1.234567, $lieu->getLongitude());
        $this->assertEquals(1.234567, $lieu->getLatitude());
        $this->assertEquals('12 Bd Albert 1er', $lieu->getAddress());
        $this->assertEquals('35000', $lieu->getPostalCode());
        $this->assertEquals('Rennes', $lieu->getCity());
        $this->assertEquals('file.jpg', $lieu->getFileName());
    }
}
