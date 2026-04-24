<?php

namespace App\Enums;

enum ActionStatut: string
{
    case ACTIF    = 'actif';
    case INACTIF  = 'inactif';
    case ARCHIVE  = 'archive';

    public function label(): string
    {
        return match($this) {
            self::ACTIF   => 'Actif',
            self::INACTIF => 'Inactif',
            self::ARCHIVE => 'Archivé',
        };
    }

    public function isActif(): bool
    {
        return $this === self::ACTIF;
    }

    public function toggle(): self
    {
        return match($this) {
            self::ACTIF   => self::INACTIF,
            self::INACTIF => self::ACTIF,
            self::ARCHIVE => self::ARCHIVE,
        };
    }

    public static function options(): array
    {
        return array_map(
            fn(self $case) => ['value' => $case->value, 'label' => $case->label()],
            self::cases()
        );
    }
}
