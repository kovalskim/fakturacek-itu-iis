<?php

/** Author: Martin Kovalski */

declare(strict_types=1);

namespace App\Router;

use Nette;
use Nette\Application\Routers\RouteList;


final class RouterFactory
{
	use Nette\StaticClass;

	public static function createRouter(): RouteList
	{
		$router = new RouteList;

		/** Admin RouteList */
        $admin = new RouteList('Admin');
        $admin->addRoute('admin/<presenter>/<action>[/<id>]', 'Homepage:default');
        $router[] = $admin;

        /** Business RouteList */
        $business = new RouteList('Business');
        $business->addRoute('business/<presenter>/<action>[/<id>]', 'Homepage:default');
        $router[] = $business;

        /** Accountant RouteList */
        $accountant = new RouteList('Accountant');
        $accountant->addRoute('accountant/<presenter>/<action>[/<id>]', 'Homepage:default');
        $router[] = $accountant;

        /** Public RouteList */
        $public = new RouteList('Public');
        $public->addRoute('<presenter>/<action>[/<id>]', 'Homepage:default');
        $router[] = $public;

        /** others */
		$router->addRoute('<presenter>/<action>[/<id>]', 'Public:Homepage:default');

		return $router;
	}
}
