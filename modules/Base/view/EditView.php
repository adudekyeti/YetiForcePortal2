<?php
/**
 * Edit view class
 * @package YetiForce.View
 * @license licenses/License.html
 * @author Mariusz Krzaczkowski <m.krzaczkowski@yetiforce.com>
 */
namespace YF\Modules\Base\View;

use YF\Core\Request;
use YF\Core\Api;

class EditView extends Index
{

	/**
	 * Process
	 * @param \Request $request
	 */
	public function process(Request $request)
	{
		$module = $request->getModule();
		$record = $request->get('record');
		$api = Api::getInstance();
		$moduleStructure = $api->call($module . '/Fields');
		$recordDetail = $api->setCustomHeaders(['X-RAW-DATA' => 1])->call("$module/Record/$record", [], 'get');
		$recordModel = \YF\Modules\Base\Model\Record::getInstance($module);
		if (!isset($recordDetail['data'])) {
			$recordDetail['data'] = [];
		}
		if (!isset($recordDetail['rawData'])) {
			$recordDetail['rawData'] = [];
		}
		if (!isset($recordDetail['id'])) {
			$recordDetail['id'] = null;
		}
		$recordModel->setData($recordDetail['data'])->setRawData($recordDetail['rawData'])->setId($recordDetail['id']);
		$fields = [];
		foreach ($moduleStructure['fields'] as $field) {
			if ($field['isEditable']) {
				$fieldInstance = \YF\Modules\Base\Model\Field::getInstance($module);
				$fields[$field['blockId']][] = $fieldInstance->setData($field);
			}
		}
		$viewer = $this->getViewer($request);
		$viewer->assign('RECORD', $recordModel);
		$viewer->assign('FIELDS', $fields);
		$viewer->assign('BREADCRUMB_TITLE', (isset($recordDetail['name'])) ? $recordDetail['name'] : '');
		$viewer->assign('BLOCKS', $moduleStructure['blocks']);
		$viewer->view('EditView.tpl', $module);
	}

	/**
	 * Scripts
	 * @param \YF\Core\Request $request
	 * @return \YF\Core\Script[]
	 */
	public function getFooterScripts(\YF\Core\Request $request)
	{
		$headerScriptInstances = parent::getFooterScripts($request);
		$moduleName = $request->getModule();
		$jsFileNames = [
			'layouts/' . \YF\Core\Viewer::getLayoutName() . "/modules/Base/resources/EditView.js",
			'layouts/' . \YF\Core\Viewer::getLayoutName() . "/modules/$moduleName/resources/EditView.js",
		];

		$jsScriptInstances = $this->convertScripts($jsFileNames, 'js');
		$headerScriptInstances = array_merge($headerScriptInstances, $jsScriptInstances);
		return $headerScriptInstances;
	}
}
