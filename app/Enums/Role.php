<?php

namespace App\Enums;

enum Role: string
{
    case Owner   = 'owner';
    case Manager = 'manager';

    public function label(): string
    {
        return match($this) {
            Role::Owner   => 'Owner',
            Role::Manager => 'Manager',
        };
    }

    public function isOwner(): bool
    {
        return $this === Role::Owner;
    }

    public function isManager(): bool
    {
        return $this === Role::Manager;
    }
}
