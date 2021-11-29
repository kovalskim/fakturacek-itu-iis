<?php

/** Author: Martin Kovalski */

declare(strict_types=1);

namespace App\Router;

use Nette;
use Nette\Application\Routers\RouteList;
use Nette\Routing\Route;


final class RouterFactory
{
	use Nette\StaticClass;

    /**
     * @var string[]
     */
    private static $public_dictionary = [
	    'ONas' => 'AboutUs',
        'Kontakt' => 'Contact',
        'HlavniStranka' => 'Homepage',
        'zapomenuteHeslo' => 'forgottenPassword',
        'noveHeslo' => 'newPassword',
        'overitUcet' => 'verifyAccount',
        'Registrace' => 'Registration'
    ];

    /**
     * @var string[]
     */
    private static $business_dictionary = [
        'Ucetni' => 'Accountant',
        'Kategorie' => 'Categories',
        'ZmenaHesla' => 'ChangePassword',
        'Klienti' => 'Clients',
        'Vydaje' => 'Expenses',
        'detail' => 'document',
        'Nastenka' => 'Homepage',
        'Fakturace' => 'Invoicing',
        'faktura' => 'invoice',
        'novaFaktura' => 'newInvoice',
        'Profil' => 'Profile',
        'upravit' => 'edit',
        'nahratAvatar' => 'upload',
        'NastaveniFaktury' => 'SettingInvoices',
        'Statistiky' => 'Statistics'
    ];

    /**
     * @var string[]
     */
    private static $admin_dictionary = [
        'Administratori' => 'Administrators',
        'ZmenaHesla' => 'ChangePassword',
        'Nastenka' => 'Homepage',
        'Profil' => 'Profile',
        'upravit' => 'edit',
        'nahratAvatar' => 'upload',
        'Texty' => 'Texts',
        'Uzivatele' => 'Users'
    ];

    /**
     * @var string[]
     */
    private static $accountant_dictionary = [
        'ZmenaHesla' => 'ChangePassword',
        'Klienti' => 'Clients',
        'souhrn' => 'summary',
        'faktura' => 'invoice',
        'Nastenka' => 'Homepage',
        'Profil' => 'Profile',
        'upravit' => 'edit',
        'nahratAvatar' => 'upload'
    ];

    /** Filter in and out for translate english name of presenters to czech urls */
	public static function createRouter(): RouteList
	{
		$router = new RouteList;

		/** Admin RouteList */
        $admin = new RouteList('Admin');
        $admin ->addRoute('admin/<presenter>/<action>[/<id>]', [
            'presenter' => 'Homepage',
            'action' => 'default',
            null => [
                Route::FILTER_IN => function (array $params) {
                    if(isset(self::$admin_dictionary[$params['presenter']]))
                    {
                        $params['presenter'] = self::$admin_dictionary[$params['presenter']];
                    }
                    if(isset(self::$admin_dictionary[$params['action']]))
                    {
                        $params['action'] = self::$admin_dictionary[$params['action']];
                    }
                    return $params;
                },
                Route::FILTER_OUT => function (array $params) {
                    $translate = array_search($params['presenter'], self::$admin_dictionary, true);
                    if($translate)
                    {
                        $params['presenter'] = $translate;
                    }

                    $translate_action = array_search($params['action'], self::$admin_dictionary, true);
                    if($translate_action)
                    {
                        $params['action'] = $translate_action;
                    }
                    return $params;
                },
            ]
        ]);
        $router[] = $admin;

        /** Business RouteList */
        $business = new RouteList('Business');
        $business->addRoute('osvc/<presenter>/<action>[/<id>]', [
            'presenter' => 'Homepage',
            'action' => 'default',
            null => [
                Route::FILTER_IN => function (array $params) {
                    if(isset(self::$business_dictionary[$params['presenter']]))
                    {
                        $params['presenter'] = self::$business_dictionary[$params['presenter']];
                    }
                    if(isset(self::$business_dictionary[$params['action']]))
                    {
                        $params['action'] = self::$business_dictionary[$params['action']];
                    }
                    return $params;
                },
                Route::FILTER_OUT => function (array $params) {
                    $translate = array_search($params['presenter'], self::$business_dictionary, true);
                    if($translate)
                    {
                        $params['presenter'] = $translate;
                    }

                    $translate_action = array_search($params['action'], self::$business_dictionary, true);
                    if($translate_action)
                    {
                        $params['action'] = $translate_action;
                    }
                    return $params;
                },
            ]
        ]);
        $router[] = $business;

        /** Accountant RouteList */
        $accountant = new RouteList('Accountant');
        $accountant ->addRoute('ucetni/<presenter>/<action>[/<id>]', [
            'presenter' => 'Homepage',
            'action' => 'default',
            null => [
                Route::FILTER_IN => function (array $params) {
                    if(isset(self::$accountant_dictionary[$params['presenter']]))
                    {
                        $params['presenter'] = self::$accountant_dictionary[$params['presenter']];
                    }
                    if(isset(self::$accountant_dictionary[$params['action']]))
                    {
                        $params['action'] = self::$accountant_dictionary[$params['action']];
                    }
                    return $params;
                },
                Route::FILTER_OUT => function (array $params) {
                    $translate = array_search($params['presenter'], self::$accountant_dictionary, true);
                    if($translate)
                    {
                        $params['presenter'] = $translate;
                    }

                    $translate_action = array_search($params['action'], self::$accountant_dictionary, true);
                    if($translate_action)
                    {
                        $params['action'] = $translate_action;
                    }
                    return $params;
                },
            ]
        ]);
        $router[] = $accountant;

        /** Public RouteList */
        $public = new RouteList('Public');
        $public->addRoute('<presenter>/<action>[/<id>]', [
            'presenter' => 'Homepage',
            'action' => 'default',
                null => [
                    Route::FILTER_IN => function (array $params) {
                        if(isset(self::$public_dictionary[$params['presenter']]))
                        {
                            $params['presenter'] = self::$public_dictionary[$params['presenter']];
                        }
                        if(isset(self::$public_dictionary[$params['action']]))
                        {
                            $params['action'] = self::$public_dictionary[$params['action']];
                        }
                        return $params;
                    },
                    Route::FILTER_OUT => function (array $params) {
                        $translate = array_search($params['presenter'], self::$public_dictionary, true);
                        if($translate)
                        {
                            $params['presenter'] = $translate;
                        }

                        $translate_action = array_search($params['action'], self::$public_dictionary, true);
                        if($translate_action)
                        {
                            $params['action'] = $translate_action;
                        }
                        return $params;
                    },
                ]
        ]);
        $router[] = $public;

        /** others */
		$router->addRoute('<presenter>/<action>[/<id>]', 'Public:Homepage:default');

		return $router;
	}
}
