<?php
/**
 * VtDataDir — centrale locatie voor persistente extensie-data.
 *
 * Binnen Plesk: /usr/local/psa/var/modules/stingyscan/data/
 * CLI/cron:     zelfde pad (altijd beschikbaar op de server)
 */
class VtDataDir
{
    const BASE = '/usr/local/psa/var/modules/stingyscan/data';

    public static function path(string $filename): string
    {
        return self::BASE . '/' . $filename;
    }

    public static function ensureExists(): void
    {
        if (!is_dir(self::BASE)) {
            mkdir(self::BASE, 0750, true);
        }
    }
}
