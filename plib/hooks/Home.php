<?php
/**
 * Adds the "StingyScan" card to the admin Home dashboard (Plesk 18.0.60+).
 * New-style SDK hooks must return an anonymous class instance.
 */
return new class extends \Plesk\SDK\Hook\Home
{
    public function getBlocks(): array
    {
        return [new Modules_Stingyscan_Hook_HomeBlock()];
    }
};
