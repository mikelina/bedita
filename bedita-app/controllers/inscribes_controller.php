<?php
/**
 * Modulo Iscrizioni.
 *
 * PHP versions 4 
 *
 * CakePHP :  Rapid Development Framework <http://www.cakephp.org/>
 * Copyright (c)	2006, Cake Software Foundation, Inc.
 *								1785 E. Sahara Avenue, Suite 490-204
 *								Las Vegas, Nevada 89104
 *
 * @filesource
 * @copyright		Copyright (c) 2006
 * @link			
 * @package			
 * @subpackage		
 * @since			
 * @version			
 * @modifiedby		
 * @lastmodified	
 * @license			
 */
class InscribesController extends AppController {
	var $components = array('BeAuth');
	var $helpers 	= array('Bevalidation');
	var $uses	 	= array('Area');
	
	/**
	 * Nome modello
	 *
	 * @var string
	 */
	var $name = 'Inscribes' ;

	/**
	 * 
	 *
	 * @todo TUTTO
	 */
	function index() {
		
		$this->Session->setFlash("DA IMPLEMENTARE");
		return ;
	}

}

?>