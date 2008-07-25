<?php
/**
 *
 * @filesource
 * @copyright		
 * @link			
 * @package			
 * @subpackage		
 * @since			
 * @version			
 * @modifiedby		
 * @lastmodified	
 * @license			
 * @author 			d.didomenico@channelweb.it
 */

/**
 * Tags handling
 */
class TagsController extends ModulesController {

	var $helpers 	= array('BeTree', 'BeToolbar');
	var $components = array('BeTree', 'Permission');
	var $uses = array('ObjectCategory') ;
	var $paginate = array("ObjectCategory" 	=>
						array(
							"limit" => 250, 
							"conditions" => "ObjectCategory.object_type_id is null", 
							"order" => array("ObjectCategory.label" => "asc")
						)
					); 
	protected $moduleName = 'tags';

	public function index($id = null, $order = "", $dir = true, $page = 1, $dim = 20) {
		//$data = $this->paginate("ObjectCategory");
		$data = $this->ObjectCategory->getTags(true);
		$this->set("numTags", count($data));
		$this->set('tags', $data);
	}

	public function view($id = null) {
		$tag = array();
		$referenced = array();
		
		if(isset($id)) {
			$tag = $this->ObjectCategory->findById($id);
			if($tag == null || $tag === false) {
				throw new BeditaException(__("Error loading tag: ", true).$id);
			}
			
			$referenced = $this->ObjectCategory->getContentsByTag($tag["label"]);
			$tag["weight"] = count($referenced);
		}
		
		$this->set('tag',	$tag);
		$this->set("referenced", $referenced);		
		$this->selfUrlParams = array("id", $id);
	 }

	public function save() {
		$this->checkWriteModulePermission();
		if(empty($this->data)) 
			throw new BeditaException( __("No data", true));
		$new = (empty($this->data['id'])) ? true : false ;
		// format custom properties
		$this->Transaction->begin() ;
		if(!$this->ObjectCategory->save($this->data)) {
			throw new BeditaException(__("Error saving tag", true), $this->ObjectCategory->validationErrors);
		}
		$this->Transaction->commit();
		$this->userInfoMessage(__("Tag saved", true)." - ".$this->data["label"]);
		$this->eventInfo("tag [". $this->data["label"]."] saved");
	}

	public function delete() {
		$this->checkWriteModulePermission();
		$objectsListDeleted = $this->deleteObjects("ObjectCategory");
		$this->userInfoMessage(__("Tag deleted", true) . " -  " . $objectsListDeleted);
		$this->eventInfo("Tag $objectsListDeleted deleted");
	}

	public function listAllTags() {
		$this->layout = "empty";
		$this->set("listTags",$this->ObjectCategory->getTags(true));
	}
	
	protected function forward($action, $esito) {
		$REDIRECT = array(
			"save"	=> 	array(
								"OK"	=> "/tags/view/{$this->ObjectCategory->id}",
								"ERROR"	=> "/tags/view/{$this->ObjectCategory->id}" 
						), 
			"delete" =>	array(
								"OK"	=> "/tags",
								"ERROR"	=> "/tags/view/{@$this->params['pass'][0]}" 
						)
		) ;
		if(isset($REDIRECT[$action][$esito])) return $REDIRECT[$action][$esito] ;
		return false ;
	}
}

?>