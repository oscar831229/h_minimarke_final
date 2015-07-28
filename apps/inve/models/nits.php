<?php

class Nits extends ActiveRecord
{

	/**
	 * @var string
	 */
	protected $nit;

	/**
	 * @var string
	 */
	protected $clase;

	/**
	 * @var integer
	 */
	protected $tipodoc;

	/**
	 * @var string
	 */
	protected $nombre;

	/**
	 * @var string
	 */
	protected $direccion;

	/**
	 * @var string
	 */
	protected $telefono;

	/**
	 * @var string
	 */
	protected $ciudad;

	/**
	 * @var integer
	 */
	protected $locciu;

	/**
	 * @var string
	 */
	protected $autoret;

	/**
	 * @var string
	 */
	protected $fax;

	/**
	 * @var string
	 */
	protected $contacto;

	/**
	 * @var string
	 */
	protected $estado_nit;

	/**
	 * @var string
	 */
	protected $resp_iva;

	/**
	 * @var string
	 */
	protected $cupo;

	/**
	 * @var string
	 */
	protected $tipo_nit;

	/**
	 * @var string
	 */
	protected $ap_aereo;

	/**
	 * @var string
	 */
	protected $grab;

	/**
	 * @var string
	 */
	protected $plazo;

	/**
	 * @var string
	 */
	protected $lista;

	/**
	 * @var float
	 */
	protected $porce_cree;

	/**
	 * @var int
	 */
	protected $retecompras_id;

	/**
	 * @var string
	 */
	protected $email;

	/**
	 * @var int
	 */
	protected $celular;


	/**
	 * Metodo para establecer el valor del campo nit
	 * @param string $nit
	 */
	public function setNit($nit){
		$this->nit = $nit;
	}

	/**
	 * Metodo para establecer el valor del campo clase
	 * @param string $clase
	 */
	public function setClase($clase){
		$this->clase = $clase;
	}

	/**
	 * Metodo para establecer el valor del campo tipodoc
	 * @param integer $tipodoc
	 */
	public function setTipodoc($tipodoc){
		$this->tipodoc = $tipodoc;
	}

	/**
	 * Metodo para establecer el valor del campo nombre
	 * @param string $nombre
	 */
	public function setNombre($nombre){
		$this->nombre = $nombre;
	}

	/**
	 * Metodo para establecer el valor del campo direccion
	 * @param string $direccion
	 */
	public function setDireccion($direccion){
		$this->direccion = $direccion;
	}

	/**
	 * Metodo para establecer el valor del campo telefono
	 * @param string $telefono
	 */
	public function setTelefono($telefono){
		$this->telefono = $telefono;
	}

	/**
	 * Metodo para establecer el valor del campo ciudad
	 * @param string $ciudad
	 */
	public function setCiudad($ciudad){
		$this->ciudad = $ciudad;
	}

	/**
	 * Metodo para establecer el valor del campo locciu
	 * @param integer $locciu
	 */
	public function setLocciu($locciu){
		$this->locciu = $locciu;
	}

	/**
	 * Metodo para establecer el valor del campo autoret
	 * @param string $autoret
	 */
	public function setAutoret($autoret){
		$this->autoret = $autoret;
	}

	/**
	 * Metodo para establecer el valor del campo fax
	 * @param string $fax
	 */
	public function setFax($fax){
		$this->fax = $fax;
	}

	/**
	 * Metodo para establecer el valor del campo contacto
	 * @param string $contacto
	 */
	public function setContacto($contacto){
		$this->contacto = $contacto;
	}

	/**
	 * Metodo para establecer el valor del campo estado_nit
	 * @param string $estado_nit
	 */
	public function setEstadoNit($estado_nit){
		$this->estado_nit = $estado_nit;
	}

	/**
	 * Metodo para establecer el valor del campo resp_iva
	 * @param string $resp_iva
	 */
	public function setRespIva($resp_iva){
		$this->resp_iva = $resp_iva;
	}

	/**
	 * Metodo para establecer el valor del campo cupo
	 * @param string $cupo
	 */
	public function setCupo($cupo){
		$this->cupo = $cupo;
	}

	/**
	 * Metodo para establecer el valor del campo tipo_nit
	 * @param string $tipo_nit
	 */
	public function setTipoNit($tipo_nit){
		$this->tipo_nit = $tipo_nit;
	}

	/**
	 * Metodo para establecer el valor del campo ap_aereo
	 * @param string $ap_aereo
	 */
	public function setApAereo($ap_aereo){
		$this->ap_aereo = $ap_aereo;
	}

	/**
	 * Metodo para establecer el valor del campo grab
	 * @param string $grab
	 */
	public function setGrab($grab){
		$this->grab = $grab;
	}

	/**
	 * Metodo para establecer el valor del campo plazo
	 * @param string $plazo
	 */
	public function setPlazo($plazo){
		$this->plazo = $plazo;
	}

	/**
	 * Metodo para establecer el valor del campo lista
	 * @param string $lista
	 */
	public function setLista($lista){
		$this->lista = $lista;
	}

	/**
	 * Metodo para establecer el valor del campo porce_cree
	 * @param float $porce_cree
	 */
	public function setPorceCree($porce_cree){
		$this->porce_cree = (float) $porce_cree;
	}

	/**
	 * Metodo para establecer el valor del campo retecompras_id
	 * @param int $retecompras_id
	 */
	public function setRetecomprasId($retecompras_id){
		$this->retecompras_id = (int) $retecompras_id;
	}

	/**
	 * Metodo para establecer el valor del campo email
	 * @param string $email
	 */
	public function setEmail($email){
		$this->email = $email;
	}

	/**
	 * Metodo para establecer el valor del campo celular
	 * @param int $cedula
	 */
	public function setCelular($celular){
		$this->celular = $celular;
	}

	/**
	 * Devuelve el valor del campo nit
	 * @return string
	 */
	public function getNit(){
		return $this->nit;
	}

	/**
	 * Devuelve el valor del campo clase
	 * @return string
	 */
	public function getClase(){
		return $this->clase;
	}

	/**
	 * Devuelve el valor del campo tipodoc
	 * @return integer
	 */
	public function getTipodoc(){
		return $this->tipodoc;
	}

	/**
	 * Devuelve el valor del campo nombre
	 * @return string
	 */
	public function getNombre(){
		return $this->nombre;
	}

	/**
	 * Devuelve el valor del campo direccion
	 * @return string
	 */
	public function getDireccion(){
		return $this->direccion;
	}

	/**
	 * Devuelve el valor del campo telefono
	 * @return string
	 */
	public function getTelefono(){
		return $this->telefono;
	}

	/**
	 * Devuelve el valor del campo ciudad
	 * @return string
	 */
	public function getCiudad(){
		return $this->ciudad;
	}

	/**
	 * Devuelve el valor del campo locciu
	 * @return integer
	 */
	public function getLocciu(){
		return $this->locciu;
	}

	/**
	 * Devuelve el valor del campo autoret
	 * @return string
	 */
	public function getAutoret(){
		return $this->autoret;
	}

	/**
	 * Devuelve el valor del campo fax
	 * @return string
	 */
	public function getFax(){
		return $this->fax;
	}

	/**
	 * Devuelve el valor del campo contacto
	 * @return string
	 */
	public function getContacto(){
		return $this->contacto;
	}

	/**
	 * Devuelve el valor del campo estado_nit
	 * @return string
	 */
	public function getEstadoNit(){
		return $this->estado_nit;
	}

	/**
	 * Devuelve el valor del campo resp_iva
	 * @return string
	 */
	public function getRespIva(){
		return $this->resp_iva;
	}

	/**
	 * Devuelve el valor del campo cupo
	 * @return string
	 */
	public function getCupo(){
		return $this->cupo;
	}

	/**
	 * Devuelve el valor del campo tipo_nit
	 * @return string
	 */
	public function getTipoNit(){
		return $this->tipo_nit;
	}

	/**
	 * Devuelve el valor del campo ap_aereo
	 * @return string
	 */
	public function getApAereo(){
		return $this->ap_aereo;
	}

	/**
	 * Devuelve el valor del campo grab
	 * @return string
	 */
	public function getGrab(){
		return $this->grab;
	}

	/**
	 * Devuelve el valor del campo plazo
	 * @return string
	 */
	public function getPlazo(){
		return $this->plazo;
	}

	/**
	 * Devuelve el valor del campo lista
	 * @return string
	 */
	public function getLista(){
		return $this->lista;
	}

	/**
	 * Devuelve el valor del campo porce_cree
	 * @return string
	 */
	public function getPorceCree(){
		return $this->porce_cree;
	}

	/**
	 * Devuelve el valor del campo retecompras_id
	 * @return int
	 */
	public function getRetecomprasId(){
		return $this->retecompras_id;
	}

	/**
	 * Devuelve el valor del campo email
	 * @return string
	 */
	public function getEmail(){
		return $this->email;
	}

	/**
	 * Devuelve el valor del campo celular
	 * @return int
	 */
	public function getCelular(){
		return $this->celular;
	}

	protected function validation()
	{
		$this->validate('InclusionIn', array(
			'field' => 'clase',
			'domain' => array('C', 'E', 'A'),
			'message' => 'El campo "Clase" debe ser "CLIENTE", "EMPRESA" ó "EXTRANJERO"',
			'required' => true
		));
		if($this->ap_aereo>0){
			$porcentaje = (float) $this->ap_aereo;
			$ica = EntityManager::get('Ica')->findFirst("codigo='$porcentaje'");
			if($ica==false){
				$this->appendMessage(new ActiveRecordMessage('El porcentaje de ICA asignado al tercero no ha sido definido en el sistema', 'ap_aereo'));
			}
		}
		if($this->porce_cree>0){
			$porcentaje = (float) $this->porce_cree;
			$cuentasCree = EntityManager::get('CuentasCree')->findFirst("porce='$porcentaje'");
			if($cuentasCree==false){
				$this->appendMessage(new ActiveRecordMessage('El porcentaje de CREE asignado al tercero no ha sido definido en el sistema por la opción cuentas cree', 'porce_cree'));
			}
		}
		if($this->validationHasFailed()==true){
			return false;
		}

		$this->validate('Numericality', array(
           'field' => 'locciu',
            'message' => 'El campo "Ciudad" debe ser diligenciado',
            'required' => true
        ));


        if (!$this->nombre) {
                $this->appendMessage(new ActiveRecordMessage('El campo Nombre es obligatorio', 'nombre'));
        }
        if (!$this->direccion) {
                $this->appendMessage(new ActiveRecordMessage('El campo Dirección es obligatorio', 'direccion'));
        }
        if (!$this->telefono) {
                $this->appendMessage(new ActiveRecordMessage('El campo Teléfono es obligatorio', 'telefono'));
        }
        if (!is_numeric($this->locciu)|| !$this->locciu) {
                $this->appendMessage(new ActiveRecordMessage('El campo Ciudad es obligatorio', 'locciu'));
        }

        if($this->validationHasFailed()==true){
                return false;
        }
	}

	protected function beforeSave()
	{

		$this->nombre = i18n::strtoupper($this->nombre);
		if(!$this->locciu){
			$this->locciu = 0;
		}

		if (!$this->tipodoc) {
			if($this->clase=='C'){
				$this->tipodoc = 13;
			} else {
				if($this->clase=='A'){
					$this->tipodoc = 31;
				} else {
					if($this->clase=='A'){
						$this->tipodoc = 41;
					} else {
						$this->tipodoc = 0;
					}
				}
			}
		}

		if(!$this->autoret){
			$this->autoret = 'N';
		}

		if(!$this->estado_nit){
			$this->estado_nit = 'S';
		}

		if(!$this->resp_iva){
			$this->resp_iva = 'N';
		}

	}

	protected function beforeDelete()
	{
		if($this->countMovi()){
			$this->appendMessage(new ActiveRecordMessage('No se puede eliminar el tercero porque tiene movimiento', 'nit'));
			return false;
		}
		if($this->countActivos()){
			$this->appendMessage(new ActiveRecordMessage('No se puede eliminar el tercero porque tiene activos fijos asociados', 'nit'));
			return false;
		}
		if($this->countMovihead()){
			$this->appendMessage(new ActiveRecordMessage('No se puede eliminar el tercero porque tiene movimiento en Inventarios', 'nit'));
			return false;
		}
	}

	public function initialize()
	{

		$this->hasMany('nit', 'Movi', 'nit');
		$this->hasMany('nit', 'Activos', 'proveedor');
		$this->hasMany('nit', 'Movihead', 'nit');
		$this->belongsTo('locciu', 'Location', 'id');

		$this->addForeignKey('tipodoc', 'Tipodoc', 'codigo', array(
			'message' => 'El tipo de documento no es válido'
		));

	}

	public function getCiudadNombre(){
		return i18n::strtoupper(utf8_encode($this->getLocation()->getName()));
	}

	public function getTipoRegimen(){

		switch($this->estado_nit){

			case 'S':

				return 'SIMPLIFICADO';

			case 'C':

				return 'COMÚN';

			case 'G':

				return 'GRAN CONTRIBUYENTE';

			default:

				return 'INDEFINIDO/NO APLICA';

		}

	}


	public function getEsAutoretenedor(){

		if($this->autoret=='S'){

			return 'es autoretenedor';

		} else {

			if($this->autoret=='N'){

				return 'no es autoretenedor';

			} else {

				return 'no se ha definido si es autoretenedor o no (se asume que si)';

			}

		}

	}


	public function getResumenIca()
	{

		if ($this->autoret=='S') {

			return 'tiene un porcentaje de ICA de '.$this->ap_aereo;

		} else {

			if($this->autoret=='N'){

				return 'no se le calcula ICA';

			} else {

				return 'no se le calcula ICA';

			}

		}

	}



}
