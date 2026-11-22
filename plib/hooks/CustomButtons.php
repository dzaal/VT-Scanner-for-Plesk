<?php
/**
 * "StingyScan" in Plesk's left navigation: for the administrator, resellers,
 * customers, and the Power User view (which shows the customer-style menu).
 */
class Modules_Stingyscan_CustomButtons extends pm_Hook_CustomButtons
{
    public function getButtons()
    {
        // ADMIN/RESELLER_NAVIGATION: their left menu. HOSTING_PANEL_NAVIGATION: the admin's menu in
        // the Power User view. HOSTING_PANEL_TABS: the customer's left menu (and a tab on the subscription page).
        $places = [self::PLACE_ADMIN_NAVIGATION, self::PLACE_RESELLER_NAVIGATION];
        foreach (['PLACE_HOSTING_PANEL_NAVIGATION', 'PLACE_HOSTING_PANEL_TABS'] as $c) {
            if (defined(self::class . '::' . $c)) $places[] = constant(self::class . '::' . $c);
        }
        return [[
            'place'       => $places,
            'title'       => 'StingyScan',
            'description' => 'Malware, phishing and spam monitoring for your websites',
            'icon'        => pm_Context::getBaseUrl() . 'images/icon.png',
            'link'        => pm_Context::getActionUrl('index'),
        ]];
    }
}
