<?php

class CargosHotel extends ActiveRecord {

	/**
	 * @var integer
	 */
	protected $codcar;

	/**
	 * @var string
	 */
	protected $descripcion;

	/**
	 * @var string
	 */
	protected $tipmov;

	/**
	 * @var string
	 */
	protected $coddep;

	/**
	 * @var integer
	 */
	protected $codgru;

	/**
	 * @var integer
	 */
	protected $gruest;

	/**
	 * @var string
	 */
	protected $cueven;

	/**
	 * @var string
	 */
	protected $interf;

	/**
	 * @var string
	 */
	protected $movven;

	/**
	 * @var string
	 */
	protected $codcen;

	/**
	 * @var string
	 */
	protected $incbas;

	/**
	 * @var string
	 */
	protected $ivainc;

	/**
	 * @var string
	 */
	protected $poriva;

	/**
	 * @var string
	 */
	protected $cueiva;

	/**
	 * @var string
	 */
	protected $ceniva;

	/**
	 * @var string
	 */
	protected $porimp;

	/**
	 * @var string
	 */
	protected $cueimp;

	/**
	 * @var string
	 */
	protected $cenimp;

	/**
	 * @var string
	 */
	protected $porser;

	/**
	 * @var string
	 */
	protected $cueser;

	/**
	 * @var string
	 */
	protected $censer;

	/**
	 * @var string
	 */
	protected $ingter;

	/**
	 * @var string
	 */
	protected $porcos;

	/**
	 * @var string
	 */
	protected $cueter;

	/**
	 * @var string
	 */
	protected $center;

	/**
	 * @var string
	 */
	protected $cree;

	/**
	 * @var string
	 */
	protected $afehue;

	/**
	 * @var string
	 */
	protected $muecaj;

	/**
	 * @var string
	 */
	protected $descaj;

	/**
	 * @var string
	 */
	protected $comcon;

	/**
	 * @var string
	 */
	protected $ajuiva;

	/**
	 * @var string
	 */
	protected $ajuimp;

	/**
	 * @var string
	 */
	protected $ajuser;

	/**
	 * @var string
	 */
	protected $estado;


	/**
	 * Metodo para establecer el valor del campo codcar
	 * @param integer $codcar
	 */
	public function setCodcar($codcar){
		$this->codcar = $codcar;
	}

	/**
	 * Metodo para establecer el valor del campo descripcion
	 * @param string $descripcion
	 */
	public function setDescripcion($descripcion){
		$this->descripcion = $descripcion;
	}

	/**
	 * Metodo para establecer el valor del campo tipmov
	 * @param string $tipmov
	 */
	public function setTipmov($tipmov){
		$this->tipmov = $tipmov;
	}

	/**
	 * Metodo para establecer el valor del campo coddep
	 * @param string $coddep
	 */
	public function setCoddep($coddep){
		$this->coddep = $coddep;
	}

	/**
	 * Metodo para establecer el valor del campo codgru
	 * @param integer $codgru
	 */
	public function setCodgru($codgru){
		$this->codgru = $codgru;
	}

	/**
	 * Metodo para establecer el valor del campo gruest
	 * @param integer $gruest
	 */
	public function setGruest($gruest){
		$this->gruest = $gruest;
	}

	/**
	 * Metodo para establecer el valor del campo cueven
	 * @param string $cueven
	 */
	public function setCueven($cueven){
		$this->cueven = $cueven;
	}

	/**
	 * Metodo para establecer el valor del campo interf
	 * @param string $interf
	 */
	public function setInterf($interf){
		$this->interf = $interf;
	}

	/**
	 * Metodo para establecer el valor del campo movven
	 * @param string $movven
	 */
	public function setMovven($movven){
		$this->movven = $movven;
	}

	/**
	 * Metodo para establecer el valor del campo codcen
	 * @param string $codcen
	 */
	public function setCodcen($codcen){
		$this->codcen = $codcen;
	}

	/**
	 * Metodo para establecer el valor del campo incbas
	 * @param string $incbas
	 */
	public function setIncbas($incbas){
		$this->incbas = $incbas;
	}

	/**
	 * Metodo para establecer el valor del campo ivainc
	 * @param string $ivainc
	 */
	public function setIvainc($ivainc){
		$this->ivainc = $ivainc;
	}

	/**
	 * Metodo para establecer el valor del campo poriva
	 * @param string $poriva
	 */
	public function setPoriva($poriva){
		$this->poriva = $poriva;
	}

	/**
	 * Metodo para establecer el valor del campo cueiva
	 * @param string $cueiva
	 */
	public function setCueiva($cueiva){
		$this->cueiva = $cueiva;
	}

	/**
	 * Metodo para establecer el valor del campo ceniva
	 * @param string $ceniva
	 */
	public function setCeniva($ceniva){
		$this->ceniva = $ceniva;
	}

	/**
	 * Metodo para establecer el valor del campo porimp
	 * @param string $porimp
	 */
	public function setPorimp($porimp){
		$this->porimp = $porimp;
	}

	/**
	 * Metodo para establecer el valor del campo cueimp
	 * @param string $cueimp
	 */
	public function setCueimp($cueimp){
		$this->cueimp = $cueimp;
	}

	/**
	 * Metodo para establecer el valor del campo cenimp
	 * @param string $cenimp
	 */
	public function setCenimp($cenimp){
		$this->cenimp = $cenimp;
	}

	/**
	 * Metodo para establecer el valor del campo porser
	 * @param string $porser
	 */
	public function setPorser($porser){
		$this->porser = $porser;
	}

	/**
	 * Metodo para establecer el valor del campo cueser
	 * @param string $cueser
	 */
	public function setCueser($cueser){
		$this->cueser = $cueser;
	}

	/**
	 * Metodo para establecer el valor del campo censer
	 * @param string $censer
	 */
	public function setCenser($censer){
		$this->censer = $censer;
	}

	/**
	 * Metodo para establecer el valor del campo ingter
	 * @param string $ingter
	 */
	public function setIngter($ingter){
		$this->ingter = $ingter;
	}

	/**
	 * Metodo para establecer el valor del campo porcos
	 * @param string $porcos
	 */
	public function setPorcos($porcos){
		$this->porcos = $porcos;
	}

	/**
	 * Metodo para establecer el valor del campo cueter
	 * @param string $cueter
	 */
	public function setCueter($cueter){
		$this->cueter = $cueter;
	}

	/**
	 * Metodo para establecer el valor del campo center
	 * @param string $center
	 */
	public function setCenter($center){
		$this->center = $center;
	}

	/**
	 * Metodo para establecer el valor del campo cree
	 * @param string $cree
	 */
	public function setCree($cree){
		$this->cree = $cree;
	}

	/**
	 * Metodo para establecer el valor del campo afehue
	 * @param string $afehue
	 */
	public function setAfehue($afehue){
		$this->afehue = $afehue;
	}

	/**
	 * Metodo para establecer el valor del campo muecaj
	 * @param string $muecaj
	 */
	public function setMuecaj($muecaj){
		$this->muecaj = $muecaj;
	}

	/**
	 * Metodo para establecer el valor del campo descaj
	 * @param string $descaj
	 */
	public function setDescaj($descaj){
		$this->descaj = $descaj;
	}

	/**
	 * Metodo para establecer el valor del campo comcon
	 * @param string $comcon
	 */
	public function setComcon($comcon){
		$this->comcon = $comcon;
	}

	/**
	 * Metodo para establecer el valor del campo ajuiva
	 * @param string $ajuiva
	 */
	public function setAjuiva($ajuiva){
		$this->ajuiva = $ajuiva;
	}

	/**
	 * Metodo para establecer el valor del campo ajuimp
	 * @param string $ajuimp
	 */
	public function setAjuimp($ajuimp){
		$this->ajuimp = $ajuimp;
	}

	/**
	 * Metodo para establecer el valor del campo ajuser
	 * @param string $ajuser
	 */
	public function setAjuser($ajuser){
		$this->ajuser = $ajuser;
	}

	/**
	 * Metodo para establecer el valor del campo estado
	 * @param string $estado
	 */
	public function setEstado($estado){
		$this->estado = $estado;
	}


	/**
	 * Devuelve el valor del campo codcar
	 * @return integer
	 */
	public function getCodcar(){
		return $this->codcar;
	}

	/**
	 * Devuelve el valor del campo descripcion
	 * @return string
	 */
	public function getDescripcion(){
		return $this->descripcion;
	}

	/**
	 * Devuelve el valor del campo tipmov
	 * @return string
	 */
	public function getTipmov(){
		return $this->tipmov;
	}

	/**
	 * Devuelve el valor del campo coddep
	 * @return string
	 */
	public function getCoddep(){
		return $this->coddep;
	}

	/**
	 * Devuelve el valor del campo codgru
	 * @return integer
	 */
	public function getCodgru(){
		return $this->codgru;
	}

	/**
	 * Devuelve el valor del campo gruest
	 * @return integer
	 */
	public function getGruest(){
		return $this->gruest;
	}

	/**
	 * Devuelve el valor del campo cueven
	 * @return string
	 */
	public function getCueven(){
		return $this->cueven;
	}

	/**
	 * Devuelve el valor del campo interf
	 * @return string
	 */
	public function getInterf(){
		return $this->interf;
	}

	/**
	 * Devuelve el valor del campo movven
	 * @return string
	 */
	public function getMovven(){
		return $this->movven;
	}

	/**
	 * Devuelve el valor del campo codcen
	 * @return string
	 */
	public function getCodcen(){
		return $this->codcen;
	}

	/**
	 * Devuelve el valor del campo incbas
	 * @return string
	 */
	public function getIncbas(){
		return $this->incbas;
	}

	/**
	 * Devuelve el valor del campo ivainc
	 * @return string
	 */
	public function getIvainc(){
		return $this->ivainc;
	}

	/**
	 * Devuelve el valor del campo poriva
	 * @return string
	 */
	public function getPoriva(){
		return $this->poriva;
	}

	/**
	 * Devuelve el valor del campo cueiva
	 * @return string
	 */
	public function getCueiva(){
		return $this->cueiva;
	}

	/**
	 * Devuelve el valor del campo ceniva
	 * @return string
	 */
	public function getCeniva(){
		return $this->ceniva;
	}

	/**
	 * Devuelve el valor del campo porimp
	 * @return string
	 */
	public function getPorimp(){
		return $this->porimp;
	}

	/**
	 * Devuelve el valor del campo cueimp
	 * @return string
	 */
	public function getCueimp(){
		return $this->cueimp;
	}

	/**
	 * Devuelve el valor del campo cenimp
	 * @return string
	 */
	public function getCenimp(){
		return $this->cenimp;
	}

	/**
	 * Devuelve el valor del campo porser
	 * @return string
	 */
	public function getPorser(){
		return $this->porser;
	}

	/**
	 * Devuelve el valor del campo cueser
	 * @return string
	 */
	public function getCueser(){
		return $this->cueser;
	}

	/**
	 * Devuelve el valor del campo censer
	 * @return string
	 */
	public function getCenser(){
		return $this->censer;
	}

	/**
	 * Devuelve el valor del campo ingter
	 * @return string
	 */
	public function getIngter(){
		return $this->ingter;
	}

	/**
	 * Devuelve el valor del campo porcos
	 * @return string
	 */
	public function getPorcos(){
		return $this->porcos;
	}

	/**
	 * Devuelve el valor del campo cueter
	 * @return string
	 */
	public function getCueter(){
		return $this->cueter;
	}

	/**
	 * Devuelve el valor del campo center
	 * @return string
	 */
	public function getCenter(){
		return $this->center;
	}

	/**
	 * Devuelve el valor del campo cree
	 * @return string
	 */
	public function getCree(){
		return $this->cree;
	}

	/**
	 * Devuelve el valor del campo afehue
	 * @return string
	 */
	public function getAfehue(){
		return $this->afehue;
	}

	/**
	 * Devuelve el valor del campo muecaj
	 * @return string
	 */
	public function getMuecaj(){
		return $this->muecaj;
	}

	/**
	 * Devuelve el valor del campo descaj
	 * @return string
	 */
	public function getDescaj(){
		return $this->descaj;
	}

	/**
	 * Devuelve el valor del campo comcon
	 * @return string
	 */
	public function getComcon(){
		return $this->comcon;
	}

	/**
	 * Devuelve el valor del campo ajuiva
	 * @return string
	 */
	public function getAjuiva(){
		return $this->ajuiva;
	}

	/**
	 * Devuelve el valor del campo ajuimp
	 * @return string
	 */
	public function getAjuimp(){
		return $this->ajuimp;
	}

	/**
	 * Devuelve el valor del campo ajuser
	 * @return string
	 */
	public function getAjuser(){
		return $this->ajuser;
	}

	/**
	 * Devuelve el valor del campo estado
	 * @return string
	 */
	public function getEstado(){
		return $this->estado;
	}

	/**
	 * Metodo inicializador de la Entidad
	 */
	protected function initialize(){		
		$config = CoreConfig::readFromActiveApplication('config.ini', 'ini');
		if(isset($config->hfos->front_db)){
			$this->setSchema($config->hfos->front_db);
		} else {
			$this->setSchema('hotel2');
		}
		$this->setSource('cargos');
	}

}

