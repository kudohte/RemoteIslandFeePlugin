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
                            'shop_remote_island_fee_index' => [
                                'name' => 'admin.setting.shop.remote_island_fee_list',
                                'url'  => 'admin_setting_shop_remote_island_fee'
                            ],
                            'shop_remote_island_fee_edit' => [
                                'name' => 'admin.setting.shop.remote_island_fee_registed',
                                'url'  => 'admin_setting_shop_remote_island_fee_edit'
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }
}