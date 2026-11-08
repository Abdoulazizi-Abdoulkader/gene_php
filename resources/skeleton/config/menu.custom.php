<?php

declare(strict_types=1);

/*
 * Personnalisation du menu. gene crée ce fichier une fois et ne l'écrase jamais ensuite.
 * Reçoit les entrées générées (config/menu.php) et renvoie le menu affiché.
 *
 * Exemple : renommer une entrée et en masquer une autre
 *     $menu = array_map(fn ($i) => $i['path'] === '/client' ? ['label' => 'Clients', 'path' => '/client'] : $i, $menu);
 *     return array_values(array_filter($menu, fn ($i) => $i['path'] !== '/journal'));
 *
 * @param list<array{label: string, path: string}> $menu
 *
 * @return list<array{label: string, path: string}>
 */
return static fn(array $menu): array => $menu;
