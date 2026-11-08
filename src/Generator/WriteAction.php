<?php

declare(strict_types=1);

namespace Gene\Generator;

enum WriteAction: string
{
    case Created = 'créé';
    case Updated = 'mis à jour';
    case Unchanged = 'inchangé';
    case Skipped = 'conservé (modifié à la main)';
    case Overwritten = 'écrasé (--force)';
}
