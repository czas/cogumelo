<?php

Cogumelo::load('coreView/View.php');


class I18nView extends View
{
  function __construct($baseDir){
    parent::__construct($baseDir);
  }

  function accessCheck() {
    return true;
  }

  function testi18n(){

    /***********************************/
    /*    Using the system function    */
    /***********************************/

    echo __('colo');
    echo __('miau');
    echo __('aquenometraduces');
     
    /***************************/
    /*    Using the library    */
    /***************************/
    /*
    require_once(DEPEN_MANUAL_REPOSITORY.'/Gettext/src/autoloader.php');
    require_once(DEPEN_MANUAL_REPOSITORY.'/Gettext/src/Translator.php');

    $translations = Gettext\Translations::fromPoFile(I18N_LOCALE.'/es_ES/LC_MESSAGES/messages.po');
    $t = new Gettext\Translator();
    Gettext\Translator::initGettextFunctions($t);
    $t->loadTranslations($translations);

    echo __('miau');
    */
  }

  function translate(){

    /***************************************/
    /*           Para probar TPL           */
    /***************************************/

    $domain = 'messages';
    $locale ="es_ES";
    $locale_dir = I18N_LOCALE;

    // o dominio só faría falla en caso de haber varios contextos, pero púxeno igual para evitar problemas 
    $this->template->assign('domain',$domain);
    $this->template->assign('milocale', $locale_dir);
    $this->template->setTpl('i18n.tpl');
    
    /***************************************/
    /*           Para probar JS            */
    /***************************************/
    /*
    $this->template->addClientScript('js/i18next.js');
    $this->template->addClientScript('js/i18next.min.js');
    $this->template->addClientScript('js/i18n.js');
    */

    //$this->template->addCss('css/cogumelo.table.css', 'client');
    $this->template->exec();   
  }
}

?>