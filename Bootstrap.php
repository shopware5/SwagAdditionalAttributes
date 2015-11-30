<?php
/**
 * Shopware 4.0
 * Copyright ? 2012 shopware AG
 *
 * According to our dual licensing model, this program can be used either
 * under the terms of the GNU Affero General Public License, version 3,
 * or under a proprietary license.
 *
 * The texts of the GNU Affero General Public License with an additional
 * permission and of our proprietary license can be found at and
 * in the LICENSE file you have received along with this program.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * "Shopware" is a registered trademark of shopware AG.
 * The licensing of the program under the AGPLv3 does not imply a
 * trademark license. Therefore any rights, title and interest in
 * our trademarks remain entirely with us.
 */

/**
 * Shopware SwagAdditionalAttributes - Bootstrap
 *
 * @category  Shopware
 * @package   Shopware\Plugins\SwagAdditionalAttributes
 * @copyright Copyright (c) 2012, shopware AG (http://www.shopware.de)
 */
class Shopware_Plugins_Backend_SwagAdditionalAttributes_Bootstrap extends Shopware_Components_Plugin_Bootstrap
{
	/**
	 * Install SwagAdditionalAttributes
	 * Adds db-attributes swagattr21 - swagattr40
	 *
	 * @return bool
	 */
	public function install()
	{
		$this->subscribeEvent(
			'Enlight_Controller_Action_PostDispatch_Backend_Article',
			'onLoadArticle'
		);
		$this->subscribeEvent(
			'Shopware_Modules_Articles_sGetArticlesByCategory_FilterSql',
			'onFilterSql'
		);
		$this->subscribeEvent(
			'Shopware_Modules_Articles_GetArticleById_FilterSQL',
			'onFilterSql'
		);
		$this->subscribeEvent(
			'Shopware_Modules_Articles_sGetProductByOrdernumber_FilterSql',
			'onFilterSql'
		);
		$this->subscribeEvent(
			'Shopware_Modules_Articles_GetPromotionById_FilterSql',
			'onFilterSql'
		);
		for($i = 21; $i <= 40; $i++){
			Shopware()->Models()->addAttribute(
				's_articles_attributes',
				'swag',
				'attr'.$i,
				'TEXT',
				true
			);
		}
		Shopware()->Models()->generateAttributeModels(array('s_articles_attributes'));
		return true;
	}

	public function onLoadArticle(Enlight_Event_EventArgs $args)
	{
		$request = $args->getSubject()->Request();
		$view = $args->getSubject()->View();

		if($request->getActionName() != "load"){
			return;
		}

		$view->addTemplateDir($this->Path().'Views/');
  		$view->extendsTemplate('backend/swag_additional_attributes/article/model/attribute.js');
	}

	public function onFilterSql(Enlight_Event_EventArgs $args)
	{
		$sql = $args->getReturn();

		$newSql = 'attr20,';

		for($i = 21; $i <= 40; $i++){
			$newSql .= 'swag_attr'.$i.',';
		}

		$sql = str_replace('attr20,', $newSql, $sql);

		return $sql;
	}

	/**
	 * Returns the version of the plugin as a string
	 *
	 * @return string
	 */
	public function getVersion()
	{
        $info = json_decode(file_get_contents(__DIR__ . DIRECTORY_SEPARATOR .'plugin.json'), true);

        if ($info) {
            return $info['currentVersion'];
        } else {
            throw new Exception('The plugin has an invalid version file.');
        }
	}

	/**
	 * Returns the well-formatted name of the plugin
	 * as a sting
	 *
	 * @return string
	 */
	public function getLabel()
	{
	    return 'Weitere Artikel-Attribute';
	}

	/**
	 * Returns the meta information about the plugin
	 * as an array.
	 * Keep in mind that the plugin description located
	 * in the info.txt.
	 *
	 * @return array
	 */
	public function getInfo()
	{
	    return array(
	        'version'     => $this->getVersion(),
	        'label'       => $this->getLabel(),
	        'link'        => 'http://www.shopware.de/',
	        'description' => file_get_contents($this->Path() . 'info.txt')
	    );
	}
}