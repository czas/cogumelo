<?php


Cogumelo::load('c_view/View');
testmodule::load('controllers/data/CousaController');


class Cousadmin extends View
{

	var $cousacontrol;
	function __construct($base_dir){
		parent::__construct($base_dir);

		$this->cousacontrol = new CousaController();
	}

	function accessCheck() {
		return true;
	}


	function lista() {
		$cousas = $this->cousacontrol->listItems();

		while($cou = $cousas->fetch()) {
			echo "<br>";
			var_dump($cou);
		}
	}


	function mostra_cousa($url = false) {

	var_dump($this->cousacontrol->find($url) );

	}

	function crea() {

		$novacousa = array('name'=> 'Cousa Adams', 'fingers' => 5,'hobby' => 'tocar o piano');

		$this->cousacontrol->create($novacousa);

		echo "Creado nova entrada para cousa";
	}
}

