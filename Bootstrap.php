<?php
/*
 * (c) shopware AG <info@shopware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
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
        for ($i = 21; $i <= 40; $i++) {
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

        if ($request->getActionName() != "load") {
            return;
        }

        $view->addTemplateDir($this->Path().'Views/');
        $view->extendsTemplate('backend/swag_additional_attributes/article/model/attribute.js');
    }

    public function onFilterSql(Enlight_Event_EventArgs $args)
    {
        $sql = $args->getReturn();

        $newSql = 'attr20,';

        for ($i = 21; $i <= 40; $i++) {
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
