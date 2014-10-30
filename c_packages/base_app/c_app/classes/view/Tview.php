<?php
Cogumelo::load('c_view/View.php');
common::autoIncludes();

class Tview extends View
{

  function __construct($base_dir){
    parent::__construct($base_dir);
  }

  /**
  * Evaluar las condiciones de acceso y reportar si se puede continuar
  * @return bool : true -> Access allowed
  */
  function accessCheck() {
    return true;
  }

  function main() {
    /*$this->template->addClientStyles('styles/table.less');
    $this->template->addClientScript('js/table.js');
    $this->template->setTpl('table.tpl');
    $this->template->exec();*/


    table::autoIncludes();


    // creamos obxecto taboa pasandolle o POST
    $tabla = new TableController($_POST);

    // establecemos pestañas, así como o key identificativo á hora de filtrar
    $tabla->setTabs('estado', array('1'=>'Activos', '2'=>'Papelera') );


    // establecemos os table filters 
    $tabla->setFilters(
      array(
        array('id'=> 'buscar', 'desc'=>'Búsqueda de cousas', 'type'=>'search', 'default'=> false),
        array('id'=> 'categoria', 'desc'=>'Categorías', 'type'=>'list', 'default'=> 5,
          'list' => array(
              1 => 'Elemento 1',
              2 => 'Elemento 2',
              3 => array('list_name'=>'Elemento 3', 'id'=> 'subcategoria', 'desc'=>'Subcategorías', 'type'=>'list',
                'list' => array(
                  1 => 'Elemento 1',
                  2 => 'Elemento 2',
                  3 => 'Elemento 3'
                )
              ),
              4 => 'Elemento 4'
          )
        )
      )
    );



    // Nome das columnas
    $tabla->setCol('id', 'Id');
    $tabla->setCol('name', 'Nome da cousa');
    $tabla->setCol('fingers', "Númerod de dedos");
    $tabla->setCol('nivel', "Nivel");


    // establecer reglas a campo concreto con expresions regulares
    $this->colRule('nivel', '^[8..10]%', 'Usuario molón');
    $this->colRule('nivel', '^[5..7]%', 'Usuario medio');
    $this->colRule('nivel', '^[i..4]%', 'Usuario cutre');


    // imprimimos o JSON da taboa
    $tabla->return_table_json($this->cousacontrol);



  } // function loadForm()
}

