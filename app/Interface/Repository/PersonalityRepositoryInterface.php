<?php

namespace App\Interface\Repository;

interface PersonalityRepositoryInterface
{
    public function findMany();

    public function findOneById(int $id);
    
    public function findByEmail(string $email);

    public function create(object $payload);

    public function update(object $payload, int $id);

    public function delete(int $id);
}
