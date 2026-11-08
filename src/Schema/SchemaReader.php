<?php

declare(strict_types=1);

namespace Gene\Schema;

/** Lit la structure d'une base. Une implémentation par SGBD. */
interface SchemaReader
{
    public function read(): Schema;
}
