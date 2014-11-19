<?php
/**
 * jlmoot
 *
 * @version 1.0
 * @author Kunitsyn Vadim (vadim@joomline.ru)
 * @copyright (C) 2014 by Kunitsyn Vadim(http://www.joomline.ru)
 * @license GNU/GPL: http://www.gnu.org/copyleft/gpl.html
 **/

// no direct access
defined('_JEXEC') or die;

jimport('joomla.plugin.plugin');
require_once dirname(__FILE__).'/helper.php';

class plgContentJlmuut extends JPlugin
{

	public function onContentPrepare($context, &$article, &$params, $page = 0){
	if($context == 'com_content.article'){

		JPlugin::loadLanguage( 'plg_content_Jlmuut' );	

		$allow = plgJLMuutHelper::getalw($this->params);

		if (strpos($article->text, '{jlmuut-off}') !== false) {
			$article->text = str_replace("{jlmuut-off}","",$article->text);
			return true;
		}

		if (strpos($article->text, '{jlmuut}') === false && !$this->params->def('autoAdd')) {
			return true;
		}

		$exceptcat = is_array($this->params->def('categories')) ? $this->params->def('categories') : array($this->params->def('categories'));
		if (!in_array($article->catid,$exceptcat)) {
			$view = JRequest::getCmd('view');
			if ($view == 'article') {

				$doc = JFactory::getDocument();
				$apiId 		= $this->params->def('apiId');
				
				$cat_name   = '';
				$cat_name	= explode(";",$this->params->get('cat_name'));	
				$catname_array   = array();
				
				////////////ADD//////////////
				
				foreach ($cat_name as $cn){
					if ($cn!=''){
						$cn_temp = explode("::",$cn);
				
						if ((trim($cn_temp[0])!='')||($cn_temp[1]!='')){
							$cn_category_article = explode(",",trim($cn_temp[0]));
							foreach ($cn_category_article as $cca){
								$catname_array[$cca] = $cn_temp[1];									
							}
						}						
					}
				}
				
				if (isset($catname_array[$article->catid])){
					$cat_name = $catname_array[$article->catid];	
				} else {
					$cat_name = $this->params->get('cat_name_default');
				}
				
				
				////////////ADD//////////////
				
				$link 		= $this->params->def('link');
				$linknone		= '';
				if ($view == 'article') {
					$article_path	= $article->alias;
				} else { 
					$article_path = ''; 
				}
				
				if ($link==0){
					$linknone = 'display:none;';
				}
				else {}
				
				If ($typeviewerjq==1) {
					$doc->addCustomTag('<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.1/jquery.min.js"></script>');
				}
				If ($typeviewernojq==1) {
					$doc->addCustomTag ('<script type="text/javascript">var jqjlcomm = jQuery.noConflict();</script>');
				}
				else {}
				
				switch ($doc ->getlanguage())
				{   	
					case 'ru-ru' : $langmuut = 'ru';
				break;					
					case 'de-de' : $langmuut = 'de';
				break;
					case 'en-gb' : $langmuut = 'en';
				break;
					case 'uk-ua' : $langmuut = 'uk';
				break;
					case 'es-es' : $langmuut = 'es';
				break;
					case 'fr-fr' : $langmuut = 'fr';
				break;
					default      : $langmuut = 'en';
				break;
				}
	
				$moot_url = "https://muut.com/i/".$apiId."/".$cat_name."/".$article_path;
				
				$scriptPage = <<<HTML
					<a class="muut" href="$moot_url" data-show_title="false">Comments</a>
					<script src="//cdn.muut.com/1/moot.$langmuut.min.js"></script>	
					
HTML;
				$jll = '';
				if($allow){
					$scriptPage .= <<<HTML
					<div style="text-align: right; $linknone;">
						<a style="text-decoration:none; color: #c0c0c0; font-family: arial,helvetica,sans-serif; font-size: 5pt; " target="_blank" href="http://joomline.org">joomline.org</a>
					</div>
HTML;
				}

	
				if ($this->params->def('autoAdd') == 1) {
					$article->text .= $scriptPage;
				} else {
					$article->text = str_replace("{jlmuut}",$scriptPage,$article->text);
				}

			}
		} else {
			$article->text = str_replace("{jlmuut}","",$article->text);
		}

		}
	}

}
			