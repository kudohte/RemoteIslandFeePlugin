<?php

namespace Plugin\RemoteIslandFeePlugin;

use Eccube\Common\EccubeNav;

class Nav implements EccubeNav
{
    /**
     * @return array
     */
    public static function getNav()
    {
        return [
            'setting' => [
                'children' => [
                    'shop' => [
                        'children' => [
                            'shop_remote_island_fee' => [
                                'name' => 'admin.setting.shop.remote_island_fee_info',
                                'url' => 'admin_setting_shop_remote_island_fee'
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }
}