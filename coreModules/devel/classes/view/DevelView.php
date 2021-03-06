<?php

Cogumelo::load('coreView/View.php');
devel::autoIncludes();


class DevelView extends View
{

  public function __construct( $base_dir ) {
    parent::__construct( $base_dir );
  }

  /**
  * Evaluate the access conditions and report if can continue
  * @return bool : true -> Access allowed
  */
  public function accessCheck() {
    /*
    global $DEVEL_ALLOWED_HOSTS;
    if( !in_array($_SERVER["REMOTE_ADDR"], $DEVEL_ALLOWED_HOSTS) ){
    */
    if( !Cogumelo::getSetupValue( 'mod:devel:allowAccess' ) ) {
      Cogumelo::error("Must be developer to enter on this site");
      RequestController::redirect(SITE_URL_CURRENT.'');
    }
    else {
      if ( !isset($_SERVER['PHP_AUTH_PW']) || $_SERVER['PHP_AUTH_PW'] !== Cogumelo::getSetupValue( 'mod:devel:password' ) ) {
        header('WWW-Authenticate: Basic realm="Cogumelo Devel Confirm"');
        header('HTTP/1.0 401 Unauthorized');
        echo 'Acceso Denegado.';
        exit;
      }
      else {
        return true;
      }
    }
  }

  public function main( $url_path = '' ) {
    $this->template->setTpl('develpage.tpl', 'devel');

    $this->logs();
    $this->infoSetup();
    $this->DBSQL();
    $this->deploySQL();
    $this->infoUrls();

    $this->template->exec();
  }


  //
  // actions Logs
  //
  public function logs() {
    $list_file_logs_path = glob( Cogumelo::getSetupValue( 'logs:path' ).'/*.log' );
    $list_file_logs = str_replace( Cogumelo::getSetupValue( 'logs:path' ).'/', '', $list_file_logs_path );
    $list_file_logs = str_replace( '.log', '', $list_file_logs );
    $this->template->assign( 'list_file_logs' , $list_file_logs );
  }

  //LLamada a Ajax para buscar mas lineas
  public function read_logs() {
    $readerlogcontrol = new LogReaderController();
    $content_logs = $readerlogcontrol->read_logs();
    header("Content-Type: application/json"); //return only JSON data
    echo json_encode($content_logs);
  }

  public function DBSQL() {

    // ER diagram data
    Cogumelo::load('coreModel/VOUtils.php');
    $this->template->assign('erData', json_encode(VOUtils::getAllRelScheme()) );

    // SQL code
    $data_sql = $this->get_sql_tables();
    foreach( $data_sql as $k => $v ) {
      $data_sql[$k] = SqlFormatter::format($v);
    }
    $this->template->assign("data_sql" ,
      $data_sql
    );
  }

  public function deploySQL() {

    // ER diagram data
    Cogumelo::load('coreModel/VOUtils.php');
    $this->template->assign('erData', json_encode(VOUtils::getAllRelScheme()) );


    $this->template->assign("deploy_sql" ,  str_replace( "\n", '<br>',  $this->get_sql_deploy() ));

  }

  public function infoSetup() {
    //$this->template->assign("infoConf" , @Kint::dump( Cogumelo::getSetupValue() ) );
  }

  public function infoUrls() {
    $regexlist = new UrlListController();
    $this->template->assign("dataUrls",  $regexlist->listUrls());
  }


  //
  // Actions
  //
  public function get_sql_tables() {

    $fvotdbcontrol = new DevelDBController();
    return $fvotdbcontrol->simulationCreateTablesOnView();

  }

  public function get_sql_deploy() {

    $ret = false;

    ob_start(); // Start output buffering
    $fvotdbcontrol = new DevelDBController();
    $fvotdbcontrol->deploy();
    $ret= ob_get_contents(); // Store buffer in variable
    ob_end_clean(); // End buffering and clean up

    return [$ret];
  }

  public function get_debugger() {
    $temp_debugs = Cogumelo::objDebugPull();
    $result_debugs = array();
    header("Content-Type: application/json"); //return only JSON data
    if(isset($temp_debugs)){
      foreach( $temp_debugs as $val_debug ) {
        if($val_debug['creation_date']['minutes'] < 10){
          $val_debug['creation_date']['minutes'] = "0".$val_debug['creation_date']['minutes'];
        }
        if($val_debug['creation_date']['seconds'] < 10){
          $val_debug['creation_date']['seconds'] = "0".$val_debug['creation_date']['seconds'];
        }
        $temp_date = $val_debug['creation_date']['hours'].":".$val_debug['creation_date']['minutes'].":".$val_debug['creation_date']['seconds'];
        array_push( $result_debugs, array(
          'comment' => $val_debug['comment'],
          'date' => $temp_date,
          'debuging' => @Kint::dump( $val_debug['data'] )
        ));
      }
      echo json_encode($result_debugs);
    }
  }

  public function develPhpInfo() {
    phpinfo();
  }



  public function runGarbageCollectors() {
    header("Content-Type: text/plain");

    $modules = array( 'Filedata' );

    foreach( $modules as $moduleName ) {
      $modelName = $moduleName.'Model';
      $moduleName::load('model/'.$modelName.'.php');
      $vo = new $modelName();
      echo "Lanzando garbageCollector() del modelo ".$modelName."\n";
      $vo->garbageCollector();
      echo "...Listo\n\n";
    }
  }



  public function develPorto() {

    header("Content-Type: text/plain");
    echo "Porto\n\n";

    /*
      $rTypeIdName = $this->getRTypeIdName( $rTypeId );
      if( class_exists( $rTypeIdName ) ) {
        // error_log( "GeozzyResourceView: getRTypeCtrl = $rTypeIdName" );
        $rTypeIdName::autoIncludes();
        $rTypeCtrlClassName = $rTypeIdName.'Controller';
        $this->rTypeCtrl = new $rTypeCtrlClassName( $this );
      }

      $rTypeModel = new ResourcetypeModel();
      $rTypeList = $rTypeModel->listItems( array( 'filters' => array( 'id' => $rTypeId ) ) );
      if( $rTypeInfo = $rTypeList->fetch() ) {
        $rTypeIdName = $rTypeInfo->getter( 'idName' );
      }
    */
  }
}
