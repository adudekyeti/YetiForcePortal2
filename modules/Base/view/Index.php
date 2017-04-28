<?php
/**
 * Users view class
 * @package YetiForce.View
 * @license licenses/License.html
 * @author Mariusz Krzaczkowski <m.krzaczkowski@yetiforce.com>
 * @author Radosław Skrzypczak <r.skrzypczak@yetiforce.com>
 */
namespace YF\Modules\Base\View;

use YF\Core;
use YF\Core\Session;

abstract class Index extends \YF\Core\Controller
{

	protected $viewer = false;

	public function __construct()
	{
		parent::__construct();
	}

	public function loginRequired()
	{
		return true;
	}

	function checkPermission(\YF\Core\Request $request)
	{
		$moduleName = $request->getModule();
		$userInstance = \YF\Core\User::getUser();
		$modulePermission = $userInstance->isPermitted($moduleName);
		if (!$modulePermission) {
			throw new \AppException('LBL_MODULE_PERMISSION_DENIED');
		}
		return true;
	}

	/**
	 * Get viewer
	 * @param \YF\Core\Request $request
	 * @return \YF\Core\Viewer
	 */
	public function getViewer(\YF\Core\Request $request)
	{
		if (!$this->viewer) {
			$moduleName = $request->getModule();

			$viewer = new \YF\Core\Viewer();
			$viewer->assign('MODULE_NAME', $moduleName);
			$viewer->assign('VIEW', $request->get('view'));
			$viewer->assign('ACTION_NAME', $request->getAction());
			$this->viewer = $viewer;
		}
		return $this->viewer;
	}

	public function getPageTitle(\YF\Core\Request $request)
	{
		return $request->getAction();
	}

	public function preProcess(\YF\Core\Request $request, $display = true)
	{
		$viewer = $this->getViewer($request);
		$viewer->assign('PAGETITLE', $this->getPageTitle($request));
		$viewer->assign('HEADER_SCRIPTS', $this->getHeaderScripts($request));
		$viewer->assign('STYLES', $this->getHeaderCss($request));
		$viewer->assign('LANGUAGE', \YF\Core\Language::getLanguage());
		$viewer->assign('LANG', \YF\Core\Language::getShortLanguageName());
		$viewer->assign('USER', \YF\Core\User::getUser());
		if ($display) {
			$this->preProcessDisplay($request);
		}
	}

	protected function preProcessTplName(\YF\Core\Request $request)
	{
		return 'Header.tpl';
	}

	//Note : To get the right hook for immediate parent in PHP,
	// specially in case of deep hierarchy
	//TODO: Need to revisit this.
	/* function preProcessParentTplName(YF\Core\Request $request) {
	  return parent::preProcessTplName($request);
	  } */

	protected function preProcessDisplay(\YF\Core\Request $request)
	{
		$viewer = $this->getViewer($request);
		if (Session::has('systemError')) {
			$viewer->assign('ERRORS', Session::get('systemError'));
			unset($_SESSION['systemError']);
		}
		$viewer->view($this->preProcessTplName($request), $request->getModule());
	}

	public function postProcess(\YF\Core\Request $request)
	{
		$viewer = $this->getViewer($request);
		$viewer->assign('FOOTER_SCRIPTS', $this->getFooterScripts($request));

		if (\Config::getBoolean('debugApi') && Session::has('debugApi') && Session::get('debugApi')) {
			$viewer->assign('DEBUG_API', Session::get('debugApi'));
			$viewer->view('DebugApi.tpl');
			Session::set('debugApi', false);
		}
		$viewer->view('Footer.tpl');
	}

	/**
	 * Retrieves css styles that need to loaded in the page
	 * @param \YF\Core\Request $request - request model
	 * @return \YF\Core\Script[]
	 */
	public function getHeaderCss(\YF\Core\Request $request)
	{
		$cssFileNames = [
			'libraries/Scripts/pace/pace.css',
			'vendor/twbs/bootstrap/dist/css/bootstrap.css',
			'libraries/Scripts/chosen/chosen.css',
			'libraries/Scripts/chosen/chosen.bootstrap.css',
			'libraries/Scripts/ValidationEngine/css/validationEngine.jquery.css',
			'libraries/Scripts/select2/select2.css',
			'layouts/' . \YF\Core\Viewer::getLayoutName() . '/skins/icons/userIcons.css',
			'layouts/' . \YF\Core\Viewer::getLayoutName() . '/skins/basic/styles.css',
			'vendor/fortawesome/font-awesome/css/font-awesome.css',
			'vendor/datatables/datatables/media/css/jquery.dataTables_themeroller.css',
			'vendor/datatables/datatables/media/css/dataTables.bootstrap.css',
			'libraries/Scripts/bootstrap-daterangepicker/daterangepicker.css',
		];

		$headerCssInstances = $this->convertScripts($cssFileNames, 'css');
		return $headerCssInstances;
	}

	/**
	 * Retrieves headers scripts that need to loaded in the page
	 * @param \YF\Core\Request $request - request model
	 * @return <array> - array of \YF\Core\Script
	 */
	public function getHeaderScripts(\YF\Core\Request $request)
	{
		$headerScriptInstances = [
			'libraries/Scripts/pace/pace.js',
		];
		$jsScriptInstances = $this->convertScripts($headerScriptInstances, 'js');
		return $jsScriptInstances;
	}

	/**
	 * Scripts
	 * @param \YF\Core\Request $request
	 * @return \YF\Core\Script[]
	 */
	public function getFooterScripts(\YF\Core\Request $request)
	{
		$moduleName = $request->getModule();
		$action = $request->getAction();
		$shortLang = \YF\Core\Language::getShortLanguageName();
		$validLangScript = "libraries/Scripts/ValidationEngine/js/languages/jquery.validationEngine-$shortLang.js";
		if (!file_exists($validLangScript)) {
			$validLangScript = "libraries/Scripts/ValidationEngine/js/languages/jquery.validationEngine-en.js";
		}
		$jsFileNames = [
			'vendor/components/jquery/jquery.js',
			'libraries/Scripts/jquery/jquery.class.js',
			'libraries/Scripts/jquery-pjax/jquery.pjax.js',
			'vendor/twbs/bootstrap/dist/js/bootstrap.js',
			'libraries/Scripts/chosen/chosen.jquery.js',
			'libraries/Scripts/select2/select2.full.js',
			'libraries/Scripts/moment.js/moment.js',
			'libraries/Scripts/bootstrap-daterangepicker/daterangepicker.js',
			'vendor/datatables/datatables/media/js/jquery.dataTables.js',
			'libraries/Scripts/ValidationEngine/js/jquery.validationEngine.js',
			$validLangScript,
			'vendor/datatables/datatables/media/js/dataTables.bootstrap.js',
			'layouts/' . \YF\Core\Viewer::getLayoutName() . '/resources/Connector.js',
			'layouts/' . \YF\Core\Viewer::getLayoutName() . '/resources/app.js',
			'layouts/' . \YF\Core\Viewer::getLayoutName() . "/modules/Base/resources/Header.js",
			'layouts/' . \YF\Core\Viewer::getLayoutName() . "/modules/Base/resources/$action.js",
			'layouts/' . \YF\Core\Viewer::getLayoutName() . "/modules/$moduleName/resources/$action.js",
		];

		$jsScriptInstances = $this->convertScripts($jsFileNames, 'js');
		return $jsScriptInstances;
	}

	public function convertScripts($fileNames, $fileExtension)
	{
		$scriptsInstances = [];

		foreach ($fileNames as $fileName) {
			$script = new \YF\Core\Script();
			$script->set('type', $fileExtension);
			// external javascript source file handling
			if (strpos($fileName, 'http://') === 0 || strpos($fileName, 'https://') === 0) {
				$scriptsInstances[] = $script->set('src', self::resourceUrl($fileName));
				continue;
			}
			$minFilePath = str_replace('.' . $fileExtension, '.min.' . $fileExtension, $fileName);
			if (\Config::getBoolean('minScripts') && file_exists($minFilePath)) {
				$scriptsInstances[] = $script->set('src', self::resourceUrl($minFilePath));
			} else if (file_exists($fileName)) {
				$scriptsInstances[] = $script->set('src', self::resourceUrl($fileName));
			}
		}
		return $scriptsInstances;
	}

	public function resourceUrl($url)
	{
		if (stripos($url, '://') === false && $fs = @filemtime($url)) {
			$url = $url . '?s=' . $fs;
		}
		return $url;
	}
}
