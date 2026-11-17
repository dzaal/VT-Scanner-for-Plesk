<?php
/**
 * "VT Site Scanner" in Plesk's left navigation: for the administrator, resellers,
 * customers, and the Power User view (which shows the customer-style menu).
 */
class Modules_VirustotalExtension_CustomButtons extends pm_Hook_CustomButtons
{
    public function getButtons()
    {
        $places = [self::PLACE_ADMIN_NAVIGATION, self::PLACE_RESELLER_NAVIGATION];
        if (defined(self::class . '::PLACE_HOSTING_PANEL_NAVIGATION')) {
            $places[] = self::PLACE_HOSTING_PANEL_NAVIGATION;
        }
        return [[
            'place'       => $places,
            'title'       => 'VT Site Scanner',
            'description' => 'Malware, phishing and spam monitoring for your websites',
            'icon'        => pm_Context::getBaseUrl() . 'images/icon.png',
            'link'        => pm_Context::getActionUrl('index'),
        ]];
    }
}
