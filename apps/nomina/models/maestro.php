<?php

class Maestro extends ActiveRecord {

	/**
	 * @var string
	 */
	protected $codigo;

	/**
	 * @var string
	 */
	protected $cedula;

	/**
	 * @var string
	 */
	protected $primer_apellido;

	/**
	 * @var string
	 */
	protected $segund_apellido;

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
	protected $cargo;

	/**
	 * @var string
	 */
	protected $centro_costo;

	/**
	 * @var string
	 */
	protected $sexo;

	/**
	 * @var string
	 */
	protected $e_civil;

	/**
	 * @var string
	 */
	protected $libreta_mil;

	/**
	 * @var string
	 */
	protected $retfte;

	/**
	 * @var string
	 */
	protected $porc_ret;

	/**
	 * @var string
	 */
	protected $contrato;

	/**
	 * @var string
	 */
	protected $forma_pago;

	/**
	 * @var string
	 */
	protected $fondo_ces;

	/**
	 * @var string
	 */
	protected $ubica;

	/**
	 * @var string
	 */
	protected $eps;

	/**
	 * @var string
	 */
	protected $fondo_pens;

	/**
	 * @var string
	 */
	protected $sueldo;

	/**
	 * @var string
	 */
	protected $auxilio;

	/**
	 * @var Date
	 */
	protected $f_nace;

	/**
	 * @var Date
	 */
	protected $f_ingreso;

	/**
	 * @var Date
	 */
	protected $f_retiro;

	/**
	 * @var string
	 */
	protected $estado;

	/**
	 * @var string
	 */
	protected $vivienda;

	/**
	 * @var Date
	 */
	protected $f_u_pago;

	/**
	 * @var Date
	 */
	protected $f_aumento;

	/**
	 * @var Date
	 */
	protected $f_vence;

	/**
	 * @var string
	 */
	protected $dias_vacm;

	/**
	 * @var string
	 */
	protected $tiempo;

	/**
	 * @var string
	 */
	protected $horasd;


	/**
	 * Metodo para establecer el valor del campo codigo
	 * @param string $codigo
	 */
	public function setCodigo($codigo){
		$this->codigo = $codigo;
	}

	/**
	 * Metodo para establecer el valor del campo cedula
	 * @param string $cedula
	 */
	public function setCedula($cedula){
		$this->cedula = $cedula;
	}

	/**
	 * Metodo para establecer el valor del campo primer_apellido
	 * @param string $primer_apellido
	 */
	public function setPrimerApellido($primer_apellido){
		$this->primer_apellido = $primer_apellido;
	}

	/**
	 * Metodo para establecer el valor del campo segund_apellido
	 * @param string $segund_apellido
	 */
	public function setSegundApellido($segund_apellido){
		$this->segund_apellido = $segund_apellido;
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
	 * Metodo para establecer el valor del campo cargo
	 * @param string $cargo
	 */
	public function setCargo($cargo){
		$this->cargo = $cargo;
	}

	/**
	 * Metodo para establecer el valor del campo centro_costo
	 * @param string $centro_costo
	 */
	public function setCentroCosto($centro_costo){
		$this->centro_costo = $centro_costo;
	}

	/**
	 * Metodo para establecer el valor del campo sexo
	 * @param string $sexo
	 */
	public function setSexo($sexo){
		$this->sexo = $sexo;
	}

	/**
	 * Metodo para establecer el valor del campo e_civil
	 * @param string $e_civil
	 */
	public function setECivil($e_civil){
		$this->e_civil = $e_civil;
	}

	/**
	 * Metodo para establecer el valor del campo libreta_mil
	 * @param string $libreta_mil
	 */
	public function setLibretaMil($libreta_mil){
		$this->libreta_mil = $libreta_mil;
	}

	/**
	 * Metodo para establecer el valor del campo retfte
	 * @param string $retfte
	 */
	public function setRetfte($retfte){
		$this->retfte = $retfte;
	}

	/**
	 * Metodo para establecer el valor del campo porc_ret
	 * @param string $porc_ret
	 */
	public function setPorcRet($porc_ret){
		$this->porc_ret = $porc_ret;
	}

	/**
	 * Metodo para establecer el valor del campo contrato
	 * @param string $contrato
	 */
	public function setContrato($contrato){
		$this->contrato = $contrato;
	}

	/**
	 * Metodo para establecer el valor del campo forma_pago
	 * @param string $forma_pago
	 */
	public function setFormaPago($forma_pago){
		$this->forma_pago = $forma_pago;
	}

	/**
	 * Metodo para establecer el valor del campo fondo_ces
	 * @param string $fondo_ces
	 */
	public function setFondoCes($fondo_ces){
		$this->fondo_ces = $fondo_ces;
	}

	/**
	 * Metodo para establecer el valor del campo ubica
	 * @param string $ubica
	 */
	public function setUbica($ubica){
		$this->ubica = $ubica;
	}

	/**
	 * Metodo para establecer el valor del campo eps
	 * @param string $eps
	 */
	public function setEps($eps){
		$this->eps = $eps;
	}

	/**
	 * Metodo para establecer el valor del campo fondo_pens
	 * @param string $fondo_pens
	 */
	public function setFondoPens($fondo_pens){
		$this->fondo_pens = $fondo_pens;
	}

	/**
	 * Metodo para establecer el valor del campo sueldo
	 * @param string $sueldo
	 */
	public function setSueldo($sueldo){
		$this->sueldo = $sueldo;
	}

	/**
	 * Metodo para establecer el valor del campo auxilio
	 * @param string $auxilio
	 */
	public function setAuxilio($auxilio){
		$this->auxilio = $auxilio;
	}

	/**
	 * Metodo para establecer el valor del campo f_nace
	 * @param Date $f_nace
	 */
	public function setFNace($f_nace){
		$this->f_nace = $f_nace;
	}

	/**
	 * Metodo para establecer el valor del campo f_ingreso
	 * @param Date $f_ingreso
	 */
	public function setFIngreso($f_ingreso){
		$this->f_ingreso = $f_ingreso;
	}

	/**
	 * Metodo para establecer el valor del campo f_retiro
	 * @param Date $f_retiro
	 */
	public function setFRetiro($f_retiro){
		$this->f_retiro = $f_retiro;
	}

	/**
	 * Metodo para establecer el valor del campo estado
	 * @param string $estado
	 */
	public function setEstado($estado){
		$this->estado = $estado;
	}

	/**
	 * Metodo para establecer el valor del campo vivienda
	 * @param string $vivienda
	 */
	public function setVivienda($vivienda){
		$this->vivienda = $vivienda;
	}

	/**
	 * Metodo para establecer el valor del campo f_u_pago
	 * @param Date $f_u_pago
	 */
	public function setFUPago($f_u_pago){
		$this->f_u_pago = $f_u_pago;
	}

	/**
	 * Metodo para establecer el valor del campo f_aumento
	 * @param Date $f_aumento
	 */
	public function setFAumento($f_aumento){
		$this->f_aumento = $f_aumento;
	}

	/**
	 * Metodo para establecer el valor del campo f_vence
	 * @param Date $f_vence
	 */
	public function setFVence($f_vence){
		$this->f_vence = $f_vence;
	}

	/**
	 * Metodo para establecer el valor del campo dias_vacm
	 * @param string $dias_vacm
	 */
	public function setDiasVacm($dias_vacm){
		$this->dias_vacm = $dias_vacm;
	}

	/**
	 * Metodo para establecer el valor del campo tiempo
	 * @param string $tiempo
	 */
	public function setTiempo($tiempo){
		$this->tiempo = $tiempo;
	}

	/**
	 * Metodo para establecer el valor del campo horasd
	 * @param string $horasd
	 */
	public function setHorasd($horasd){
		$this->horasd = $horasd;
	}


	/**
	 * Devuelve el valor del campo codigo
	 * @return string
	 */
	public function getCodigo(){
		return $this->codigo;
	}

	/**
	 * Devuelve el valor del campo cedula
	 * @return string
	 */
	public function getCedula(){
		return $this->cedula;
	}

	/**
	 * Devuelve el valor del campo primer_apellido
	 * @return string
	 */
	public function getPrimerApellido(){
		return $this->primer_apellido;
	}

	/**
	 * Devuelve el valor del campo segund_apellido
	 * @return string
	 */
	public function getSegundApellido(){
		return $this->segund_apellido;
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
	 * Devuelve el valor del campo cargo
	 * @return string
	 */
	public function getCargo(){
		return $this->cargo;
	}

	/**
	 * Devuelve el valor del campo centro_costo
	 * @return string
	 */
	public function getCentroCosto(){
		return $this->centro_costo;
	}

	/**
	 * Devuelve el valor del campo sexo
	 * @return string
	 */
	public function getSexo(){
		return $this->sexo;
	}

	/**
	 * Devuelve el valor del campo e_civil
	 * @return string
	 */
	public function getECivil(){
		return $this->e_civil;
	}

	/**
	 * Devuelve el valor del campo libreta_mil
	 * @return string
	 */
	public function getLibretaMil(){
		return $this->libreta_mil;
	}

	/**
	 * Devuelve el valor del campo retfte
	 * @return string
	 */
	public function getRetfte(){
		return $this->retfte;
	}

	/**
	 * Devuelve el valor del campo porc_ret
	 * @return string
	 */
	public function getPorcRet(){
		return $this->porc_ret;
	}

	/**
	 * Devuelve el valor del campo contrato
	 * @return string
	 */
	public function getContrato(){
		return $this->contrato;
	}

	/**
	 * Devuelve el valor del campo forma_pago
	 * @return string
	 */
	public function getFormaPago(){
		return $this->forma_pago;
	}

	/**
	 * Devuelve el valor del campo fondo_ces
	 * @return string
	 */
	public function getFondoCes(){
		return $this->fondo_ces;
	}

	/**
	 * Devuelve el valor del campo ubica
	 * @return string
	 */
	public function getUbica(){
		return $this->ubica;
	}

	/**
	 * Devuelve el valor del campo eps
	 * @return string
	 */
	public function getEps(){
		return $this->eps;
	}

	/**
	 * Devuelve el valor del campo fondo_pens
	 * @return string
	 */
	public function getFondoPens(){
		return $this->fondo_pens;
	}

	/**
	 * Devuelve el valor del campo sueldo
	 * @return string
	 */
	public function getSueldo(){
		return $this->sueldo;
	}

	/**
	 * Devuelve el valor del campo auxilio
	 * @return string
	 */
	public function getAuxilio(){
		return $this->auxilio;
	}

	/**
	 * Devuelve el valor del campo f_nace
	 * @return Date
	 */
	public function getFNace(){
		if($this->f_nace){
			return new Date($this->f_nace);
		} else {
			return null;
		}
	}

	/**
	 * Devuelve el valor del campo f_ingreso
	 * @return Date
	 */
	public function getFIngreso(){
		if($this->f_ingreso){
			return new Date($this->f_ingreso);
		} else {
			return null;
		}
	}

	/**
	 * Devuelve el valor del campo f_retiro
	 * @return Date
	 */
	public function getFRetiro(){
		if($this->f_retiro){
			return new Date($this->f_retiro);
		} else {
			return null;
		}
	}

	/**
	 * Devuelve el valor del campo estado
	 * @return string
	 */
	public function getEstado(){
		return $this->estado;
	}

	/**
	 * Devuelve el valor del campo vivienda
	 * @return string
	 */
	public function getVivienda(){
		return $this->vivienda;
	}

	/**
	 * Devuelve el valor del campo f_u_pago
	 * @return Date
	 */
	public function getFUPago(){
		if($this->f_u_pago){
			return new Date($this->f_u_pago);
		} else {
			return null;
		}
	}

	/**
	 * Devuelve el valor del campo f_aumento
	 * @return Date
	 */
	public function getFAumento(){
		if($this->f_aumento){
			return new Date($this->f_aumento);
		} else {
			return null;
		}
	}

	/**
	 * Devuelve el valor del campo f_vence
	 * @return Date
	 */
	public function getFVence(){
		if($this->f_vence){
			return new Date($this->f_vence);
		} else {
			return null;
		}
	}

	/**
	 * Devuelve el valor del campo dias_vacm
	 * @return string
	 */
	public function getDiasVacm(){
		return $this->dias_vacm;
	}

	/**
	 * Devuelve el valor del campo tiempo
	 * @return string
	 */
	public function getTiempo(){
		return $this->tiempo;
	}

	/**
	 * Devuelve el valor del campo horasd
	 * @return string
	 */
	public function getHorasd(){
		return $this->horasd;
	}

}

