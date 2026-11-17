<?php
/**
 * Permissions hook — controls which Plesk roles can access this extension.
 *
 * Class name follows Plesk's auto-loading convention:
 *   Modules_<CamelCaseModuleId>_Hooks_<FileName>
 *   module id: virustotal-extension → VirustotalExtension
 */
class Modules_VirustotalExtension_Hooks_Permissions extends pm_Hook_Abstract
{
    /** Admin always has access */
    public function isAvailableForAdmin(): bool
    {
        return true;
    }

    /** Resellers can access — they see only their own clients' domains */
    public function isAvailableForReseller(): bool
    {
        return true;
    }

    /** Clients can access — they see only their own domains */
    public function isAvailableForClient(): bool
    {
        return true;
    }
}
