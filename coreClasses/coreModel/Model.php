<?php

Cogumelo::load('coreModel/VO.php');
Cogumelo::load('coreModel/Facade.php');


/**
 * Merge of VO and Data controller concepts
 *
 * @package Cogumelo Model
 */
Class Model extends VO {

  var $dataFacade;


  var $customFacade = false;
  var $customDAO = false;
  var $moduleDAO = false;
  var $filters = array();


  function __construct( $datarray= array(), $otherRelObj = false ) {
    $this->setData( $datarray, $otherRelObj );

    if($this->customFacade) {
      $this->dataFacade = new $customFacade();
    }
    if( $this->customDAO ) {
      $this->dataFacade = new Facade( false,  $this->customDAO, $this->moduleDAO);
    }
    else {
      $this->dataFacade = new Facade( $this );
    }

  }



  /**
  * List items from table
  *
  * @param array $parameters array of filters
  *
  * @return array VO array
  */
  function listItems( array $parameters = array() )
  {

    $p = array(
        'filters' => false,
        'range' => false,
        'order' => false,
        'fields' => false,
        'affectsDependences' => false,
        'cache' => false
      );
    $parameters =  array_merge($p, $parameters );

    Cogumelo::debug( 'Called listItems on '.get_called_class() );
    $data = $this->dataFacade->listItems(
                                          $parameters['filters'],
                                          $parameters['range'],
                                          $parameters['order'],
                                          $parameters['fields'],
                                          $parameters['affectsDependences'],
                                          $parameters['cache']
                                        );

    return $data;
  }


  /**
  * Count items from table
  *
  * @param array $parameters array of filters
  *
  * @return array VO array
  */
  function listCount( array $parameters= array() )
  {

    $p = array(
        'filters' => false,
        'cache' => false
      );
    $parameters =  array_merge($p, $parameters );

    Cogumelo::debug( 'Called listCount on '.get_called_class() );
    $data = $this->dataFacade->listCount( $parameters['filters']);

    return $data;
  }

  function getFilters(){
    return $this->filters;
  }




  /**
  * save item
  *
  * @param array $parameters array of filters
  *
  * @return object  VO
  */
  function save( array $parameters= array() )
  {

    $p = array(
        'affectsDependences' => false
      );
    $parameters =  array_merge($p, $parameters );


    // Save all dependences
    if($parameters['affectsDependences']) {
      $depsInOrder = $this->getDepInLinearArray();

      while( $selectDep = array_pop($depsInOrder) ) {
          $selectDep['ref']->save( array('affectsDependences' => false) );
      }
    }
    // Save only this Model
    else {
      Cogumelo::debug( 'Called save on '.get_called_class(). ' with "'.$this->getFirstPrimarykeyId().'" = '. $this->getter( $this->getFirstPrimarykeyId() ) );
      return $this->saveOrUpdate();
    }

  }

  /**
  * save item
  *
  * @param object $voObj voObject
  *
  * @return object  VO
  */
  private function saveOrUpdate( $voObj = false ){
    $retObj = false; 

    if(!$voObj) {
      $voObj = $this;
    }



    if( $voObj->exist() ) {
      //echo  $this->getVOClassName().":update ";
      $retObj = $this->dataFacade->Update( $voObj );
    }
    else {
      //echo $this->getVOClassName().":create ";
      $retObj = $this->dataFacade->Create( $voObj );
    }

    return $retObj;
  }

  /**
  * if VO exist
  *
  * @param object $voObj voObject
  *
  * @return boolean
  */
  function exist($voObj = false) {
    $ret = false;

    if(!$voObj) {
      $voObj = $this;
    }
    
    if($filters = $voObj->data) {

      if( $this->listCount( array('filters'=>$filters) ) ) {
        $ret = true;
      }
    }

    return $ret;
  }


  /**
  * delete item
  *
  * @param array $parameters array of filters
  *
  * @return object  VO
  */
  function delete( array $parameters = array() ) {

    $p = array(
        'affectsDependences' => false
      );
    $parameters =  array_merge($p, $parameters );


    // Delete all dependences
    if($parameters['affectsDependences']) {
      $depsInOrder = $this->getDepInLinearArray();

      while( $selectDep = array_pop($depsInOrder) ) {
          Cogumelo::debug( 'Called delete on '.get_called_class().' with "'.$selectDep['ref']->getFirstPrimarykeyId().'" = '. $selectDep['ref']->getter( $selectDep['ref']->getFirstPrimarykeyId() ) );
          $selectDep['ref']->dataFacade->deleteFromKey( $selectDep['ref']->getFirstPrimarykeyId(), $selectDep['ref']->getter( $selectDep['ref']->getFirstPrimarykeyId() )  );
      }
    }
    // Delete only this Model
    else {
      Cogumelo::debug( 'Called delete on '.get_called_class().' with "'.$this->getFirstPrimarykeyId().'" = '. $this->getter( $this->getFirstPrimarykeyId() ) );
      $this->dataFacade->deleteFromKey( $this->getFirstPrimarykeyId(), $this->getter( $this->getFirstPrimarykeyId() )  );
    }

    return true;
  }


}