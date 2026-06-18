<?php

namespace App\Services\AI;

interface AIServiceInterface
{
    public function classify(string $description): array;

    public function name(): string;
}
