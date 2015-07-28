<?php

class FormatoCheque extends ActiveRecord {

	/**
	 * @var integer
	 */
	protected $id;

	/**
	 * @var integer
	 */
	protected $chequeras_id;

	/**
	 * @var string
	 */
	protected $medida;

	/**
	 * @var string
	 */
	protected $r_ano;

	/**
	 * @var string
	 */
	protected $p_ano;

	/**
	 * @var string
	 */
	protected $r_mes;

	/**
	 * @var string
	 */
	protected $p_mes;

	/**
	 * @var string
	 */
	protected $r_dia;

	/**
	 * @var string
	 */
	protected $p_dia;

	/**
	 * @var string
	 */
	protected $r_valor;

	/**
	 * @var string
	 */
	protected $p_valor;

	/**
	 * @var string
	 */
	protected $r_tercero;

	/**
	 * @var string
	 */
	protected $p_tercero;

	/**
	 * @var string
	 */
	protected $r_suma;

	/**
	 * @var string
	 */
	protected $p_suma;

	/**
	 * @var string
	 */
	protected $r_numero;

	/**
	 * @var string
	 */
	protected $p_numero;

	/**
	 * @var string
	 */
	protected $r_nota;

	/**
	 * @var string
	 */
	protected $p_nota;

	/**
	 * @var string
	 */
	protected $r_cuenta;

	/**
	 * @var string
	 */
	protected $p_cuenta;

	/**
	 * @var string
	 */
	protected $r_detalle;

	/**
	 * @var string
	 */
	protected $p_detalle;

	/**
	 * @var string
	 */
	protected $r_debito;

	/**
	 * @var string
	 */
	protected $p_debito;

	/**
	 * @var string
	 */
	protected $r_credito;

	/**
	 * @var string
	 */
	protected $p_credito;

	/**
	 * @var string
	 */
	protected $r_valor_movi;

	/**
	 * @var string
	 */
	protected $p_valor_movi;

	/**
	 * @var string
	 */
	protected $r_empresa;

	/**
	 * @var string
	 */
	protected $p_empresa;

	/**
	 * @var string
	 */
	protected $r_num_cheque;

	/**
	 * @var string
	 */
	protected $p_num_cheque;

	/**
	 * @var string
	 */
	protected $r_cuenta_bancaria;

	/**
	 * @var string
	 */
	protected $p_cuenta_bancaria;


	/**
	 * Metodo para establecer el valor del campo id
	 * @param integer $id
	 */
	public function setId($id){
		$this->id = $id;
	}

	/**
	 * Metodo para establecer el valor del campo chequeras_id
	 * @param integer $chequeras_id
	 */
	public function setChequerasId($chequeras_id){
		$this->chequeras_id = $chequeras_id;
	}

	/**
	 * Metodo para establecer el valor del campo medida
	 * @param string $medida
	 */
	public function setMedida($medida){
		$this->medida = $medida;
	}

	/**
	 * Metodo para establecer el valor del campo r_ano
	 * @param string $r_ano
	 */
	public function setRAno($r_ano){
		$this->r_ano = $r_ano;
	}

	/**
	 * Metodo para establecer el valor del campo p_ano
	 * @param string $p_ano
	 */
	public function setPAno($p_ano){
		$this->p_ano = $p_ano;
	}

	/**
	 * Metodo para establecer el valor del campo r_mes
	 * @param string $r_mes
	 */
	public function setRMes($r_mes){
		$this->r_mes = $r_mes;
	}

	/**
	 * Metodo para establecer el valor del campo p_mes
	 * @param string $p_mes
	 */
	public function setPMes($p_mes){
		$this->p_mes = $p_mes;
	}

	/**
	 * Metodo para establecer el valor del campo r_dia
	 * @param string $r_dia
	 */
	public function setRDia($r_dia){
		$this->r_dia = $r_dia;
	}

	/**
	 * Metodo para establecer el valor del campo p_dia
	 * @param string $p_dia
	 */
	public function setPDia($p_dia){
		$this->p_dia = $p_dia;
	}

	/**
	 * Metodo para establecer el valor del campo r_valor
	 * @param string $r_valor
	 */
	public function setRValor($r_valor){
		$this->r_valor = $r_valor;
	}

	/**
	 * Metodo para establecer el valor del campo p_valor
	 * @param string $p_valor
	 */
	public function setPValor($p_valor){
		$this->p_valor = $p_valor;
	}

	/**
	 * Metodo para establecer el valor del campo r_tercero
	 * @param string $r_tercero
	 */
	public function setRTercero($r_tercero){
		$this->r_tercero = $r_tercero;
	}

	/**
	 * Metodo para establecer el valor del campo p_tercero
	 * @param string $p_tercero
	 */
	public function setPTercero($p_tercero){
		$this->p_tercero = $p_tercero;
	}

	/**
	 * Metodo para establecer el valor del campo r_suma
	 * @param string $r_suma
	 */
	public function setRSuma($r_suma){
		$this->r_suma = $r_suma;
	}

	/**
	 * Metodo para establecer el valor del campo p_suma
	 * @param string $p_suma
	 */
	public function setPSuma($p_suma){
		$this->p_suma = $p_suma;
	}

	/**
	 * Metodo para establecer el valor del campo r_numero
	 * @param string $r_numero
	 */
	public function setRNumero($r_numero){
		$this->r_numero = $r_numero;
	}

	/**
	 * Metodo para establecer el valor del campo p_numero
	 * @param string $p_numero
	 */
	public function setPNumero($p_numero){
		$this->p_numero = $p_numero;
	}

	/**
	 * Metodo para establecer el valor del campo r_nota
	 * @param string $r_nota
	 */
	public function setRNota($r_nota){
		$this->r_nota = $r_nota;
	}

	/**
	 * Metodo para establecer el valor del campo p_nota
	 * @param string $p_nota
	 */
	public function setPNota($p_nota){
		$this->p_nota = $p_nota;
	}

	/**
	 * Metodo para establecer el valor del campo r_cuenta
	 * @param string $r_cuenta
	 */
	public function setRCuenta($r_cuenta){
		$this->r_cuenta = $r_cuenta;
	}

	/**
	 * Metodo para establecer el valor del campo p_cuenta
	 * @param string $p_cuenta
	 */
	public function setPCuenta($p_cuenta){
		$this->p_cuenta = $p_cuenta;
	}

	/**
	 * Metodo para establecer el valor del campo r_detalle
	 * @param string $r_detalle
	 */
	public function setRDetalle($r_detalle){
		$this->r_detalle = $r_detalle;
	}

	/**
	 * Metodo para establecer el valor del campo p_detalle
	 * @param string $p_detalle
	 */
	public function setPDetalle($p_detalle){
		$this->p_detalle = $p_detalle;
	}

	/**
	 * Metodo para establecer el valor del campo r_debito
	 * @param string $r_debito
	 */
	public function setRDebito($r_debito){
		$this->r_debito = $r_debito;
	}

	/**
	 * Metodo para establecer el valor del campo p_debito
	 * @param string $p_debito
	 */
	public function setPDebito($p_debito){
		$this->p_debito = $p_debito;
	}

	/**
	 * Metodo para establecer el valor del campo r_credito
	 * @param string $r_credito
	 */
	public function setRCredito($r_credito){
		$this->r_credito = $r_credito;
	}

	/**
	 * Metodo para establecer el valor del campo p_credito
	 * @param string $p_credito
	 */
	public function setPCredito($p_credito){
		$this->p_credito = $p_credito;
	}

	/**
	 * Metodo para establecer el valor del campo r_valor_movi
	 * @param string $r_valor_movi
	 */
	public function setRValorMovi($r_valor_movi){
		$this->r_valor_movi = $r_valor_movi;
	}

	/**
	 * Metodo para establecer el valor del campo p_valor_movi
	 * @param string $p_valor_movi
	 */
	public function setPValorMovi($p_valor_movi){
		$this->p_valor_movi = $p_valor_movi;
	}

	/**
	 * Metodo para establecer el valor del campo r_empresa
	 * @param string $r_empresa
	 */
	public function setREmpresa($r_empresa){
		$this->r_empresa = $r_empresa;
	}

	/**
	 * Metodo para establecer el valor del campo p_empresa
	 * @param string $p_empresa
	 */
	public function setPEmpresa($p_empresa){
		$this->p_empresa = $p_empresa;
	}

	/**
	 * Metodo para establecer el valor del campo r_num_cheque
	 * @param string $r_num_cheque
	 */
	public function setRNumCheque($r_num_cheque){
		$this->r_num_cheque = $r_num_cheque;
	}

	/**
	 * Metodo para establecer el valor del campo p_num_cheque
	 * @param string $p_num_cheque
	 */
	public function setPNumCheque($p_num_cheque){
		$this->p_num_cheque = $p_num_cheque;
	}

	/**
	 * Metodo para establecer el valor del campo r_cuenta_bancaria
	 * @param string $r_cuenta_bancaria
	 */
	public function setRCuentaBancaria($r_cuenta_bancaria){
		$this->r_cuenta_bancaria = $r_cuenta_bancaria;
	}

	/**
	 * Metodo para establecer el valor del campo p_cuenta_bancaria
	 * @param string $p_cuenta_bancaria
	 */
	public function setPCuentaBancaria($p_cuenta_bancaria){
		$this->p_cuenta_bancaria = $p_cuenta_bancaria;
	}


	/**
	 * Devuelve el valor del campo id
	 * @return integer
	 */
	public function getId(){
		return $this->id;
	}

	/**
	 * Devuelve el valor del campo chequeras_id
	 * @return integer
	 */
	public function getChequerasId(){
		return $this->chequeras_id;
	}

	/**
	 * Devuelve el valor del campo medida
	 * @return string
	 */
	public function getMedida(){
		return $this->medida;
	}

	/**
	 * Devuelve el valor del campo r_ano
	 * @return string
	 */
	public function getRAno(){
		return $this->r_ano;
	}

	/**
	 * Devuelve el valor del campo p_ano
	 * @return string
	 */
	public function getPAno(){
		return $this->p_ano;
	}

	/**
	 * Devuelve el valor del campo r_mes
	 * @return string
	 */
	public function getRMes(){
		return $this->r_mes;
	}

	/**
	 * Devuelve el valor del campo p_mes
	 * @return string
	 */
	public function getPMes(){
		return $this->p_mes;
	}

	/**
	 * Devuelve el valor del campo r_dia
	 * @return string
	 */
	public function getRDia(){
		return $this->r_dia;
	}

	/**
	 * Devuelve el valor del campo p_dia
	 * @return string
	 */
	public function getPDia(){
		return $this->p_dia;
	}

	/**
	 * Devuelve el valor del campo r_valor
	 * @return string
	 */
	public function getRValor(){
		return $this->r_valor;
	}

	/**
	 * Devuelve el valor del campo p_valor
	 * @return string
	 */
	public function getPValor(){
		return $this->p_valor;
	}

	/**
	 * Devuelve el valor del campo r_tercero
	 * @return string
	 */
	public function getRTercero(){
		return $this->r_tercero;
	}

	/**
	 * Devuelve el valor del campo p_tercero
	 * @return string
	 */
	public function getPTercero(){
		return $this->p_tercero;
	}

	/**
	 * Devuelve el valor del campo r_suma
	 * @return string
	 */
	public function getRSuma(){
		return $this->r_suma;
	}

	/**
	 * Devuelve el valor del campo p_suma
	 * @return string
	 */
	public function getPSuma(){
		return $this->p_suma;
	}

	/**
	 * Devuelve el valor del campo r_numero
	 * @return string
	 */
	public function getRNumero(){
		return $this->r_numero;
	}

	/**
	 * Devuelve el valor del campo p_numero
	 * @return string
	 */
	public function getPNumero(){
		return $this->p_numero;
	}

	/**
	 * Devuelve el valor del campo r_nota
	 * @return string
	 */
	public function getRNota(){
		return $this->r_nota;
	}

	/**
	 * Devuelve el valor del campo p_nota
	 * @return string
	 */
	public function getPNota(){
		return $this->p_nota;
	}

	/**
	 * Devuelve el valor del campo r_cuenta
	 * @return string
	 */
	public function getRCuenta(){
		return $this->r_cuenta;
	}

	/**
	 * Devuelve el valor del campo p_cuenta
	 * @return string
	 */
	public function getPCuenta(){
		return $this->p_cuenta;
	}

	/**
	 * Devuelve el valor del campo r_detalle
	 * @return string
	 */
	public function getRDetalle(){
		return $this->r_detalle;
	}

	/**
	 * Devuelve el valor del campo p_detalle
	 * @return string
	 */
	public function getPDetalle(){
		return $this->p_detalle;
	}

	/**
	 * Devuelve el valor del campo r_debito
	 * @return string
	 */
	public function getRDebito(){
		return $this->r_debito;
	}

	/**
	 * Devuelve el valor del campo p_debito
	 * @return string
	 */
	public function getPDebito(){
		return $this->p_debito;
	}

	/**
	 * Devuelve el valor del campo r_credito
	 * @return string
	 */
	public function getRCredito(){
		return $this->r_credito;
	}

	/**
	 * Devuelve el valor del campo p_credito
	 * @return string
	 */
	public function getPCredito(){
		return $this->p_credito;
	}

	/**
	 * Devuelve el valor del campo r_valor_movi
	 * @return string
	 */
	public function getRValorMovi(){
		return $this->r_valor_movi;
	}

	/**
	 * Devuelve el valor del campo p_valor_movi
	 * @return string
	 */
	public function getPValorMovi(){
		return $this->p_valor_movi;
	}

	/**
	 * Devuelve el valor del campo r_empresa
	 * @return string
	 */
	public function getREmpresa(){
		return $this->r_empresa;
	}

	/**
	 * Devuelve el valor del campo p_empresa
	 * @return string
	 */
	public function getPEmpresa(){
		return $this->p_empresa;
	}

	/**
	 * Devuelve el valor del campo r_num_cheque
	 * @return string
	 */
	public function getRNumCheque(){
		return $this->r_num_cheque;
	}

	/**
	 * Devuelve el valor del campo p_num_cheque
	 * @return string
	 */
	public function getPNumCheque(){
		return $this->p_num_cheque;
	}

	/**
	 * Devuelve el valor del campo r_cuenta_bancaria
	 * @return string
	 */
	public function getRCuentaBancaria(){
		return $this->r_cuenta_bancaria;
	}

	/**
	 * Devuelve el valor del campo p_cuenta_bancaria
	 * @return string
	 */
	public function getPCuentaBancaria(){
		return $this->p_cuenta_bancaria;
	}

	public function beforeSave(){
		/*$items = array('ano', 'mes', 'dia', 'valor', 'tercero', 'suma', 'cuenta');
		foreach($items as $item){
			$rPos = $this->readAttribute('r_'.$item);
			if($rPos>40){
				$this->appendMessage(new ActiveRecordMessage('La posición vertical del item "'.$item.'" no puede ser mayor a 40', 'r_'.$item));
				return false;
			}
			$cPos = $this->readAttribute('p_'.$item);
			if($cPos>130){
				$this->appendMessage(new ActiveRecordMessage('La posición horizontal del item "'.$item.'" no puede ser mayor a 120', 'p_'.$item));
				return false;
			}
		}*/
	}

}

