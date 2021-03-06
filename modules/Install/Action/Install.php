<?php
/**
 * User action login class.
 *
 * @copyright YetiForce Sp. z o.o.
 * @license   YetiForce Public License 3.0 (licenses/LicenseEN.txt or yetiforce.com)
 * @author    Mariusz Krzaczkowski <m.krzaczkowski@yetiforce.com>
 */

namespace YF\Modules\Install\Action;

use Install\Model;

class Install extends \App\Controller\Action
{
	public function loginRequired()
	{
		return false;
	}

	public function process(\App\Request $request)
	{
		$install = Model\Install::getInstance($request->getModule());
		$install->save($request);
	}
}
