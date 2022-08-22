<?php

namespace App\Entity;

interface TimeLogInterface
{
    public function setCreatedAt(\DateTimeImmutable $createdAt);
    public function setUpdatedAt(\DateTimeImmutable $updatedAt);

}