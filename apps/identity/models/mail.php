<?php

class Mail extends ActiveRecord {

	/**
	 * @var integer
	 */
	protected $id;

	/**
	 * @var string
	 */
	protected $sid;

	/**
	 * @var string
	 */
	protected $dbname;

	/**
	 * @var string
	 */
	protected $mailbox;

	/**
	 * @var integer
	 */
	protected $ususen;

	/**
	 * @var integer
	 */
	protected $codusu;

	/**
	 * @var string
	 */
	protected $from_msg;

	/**
	 * @var string
	 */
	protected $headers;

	/**
	 * @var integer
	 */
	protected $timsen;

	/**
	 * @var string
	 */
	protected $subject;

	/**
	 * @var string
	 */
	protected $preview;

	/**
	 * @var string
	 */
	protected $message;

	/**
	 * @var integer
	 */
	protected $priority;

	/**
	 * @var integer
	 */
	protected $asize;

	/**
	 * @var string
	 */
	protected $hattach;

	/**
	 * @var string
	 */
	protected $type;

	/**
	 * @var string
	 */
	protected $status;


	/**
	 * Metodo para establecer el valor del campo id
	 * @param integer $id
	 */
	public function setId($id){
		$this->id = $id;
	}

	/**
	 * Metodo para establecer el valor del campo sid
	 * @param string $sid
	 */
	public function setSid($sid){
		$this->sid = $sid;
	}

	/**
	 * Metodo para establecer el valor del campo dbname
	 * @param string $dbname
	 */
	public function setDbname($dbname){
		$this->dbname = $dbname;
	}

	/**
	 * Metodo para establecer el valor del campo mailbox
	 * @param string $mailbox
	 */
	public function setMailbox($mailbox){
		$this->mailbox = $mailbox;
	}

	/**
	 * Metodo para establecer el valor del campo ususen
	 * @param integer $ususen
	 */
	public function setUsusen($ususen){
		$this->ususen = $ususen;
	}

	/**
	 * Metodo para establecer el valor del campo codusu
	 * @param integer $codusu
	 */
	public function setCodusu($codusu){
		$this->codusu = $codusu;
	}

	/**
	 * Metodo para establecer el valor del campo from_msg
	 * @param string $from_msg
	 */
	public function setFromMsg($from_msg){
		$this->from_msg = $from_msg;
	}

	/**
	 * Metodo para establecer el valor del campo headers
	 * @param string $headers
	 */
	public function setHeaders($headers){
		$this->headers = $headers;
	}

	/**
	 * Metodo para establecer el valor del campo timsen
	 * @param integer $timsen
	 */
	public function setTimsen($timsen){
		$this->timsen = $timsen;
	}

	/**
	 * Metodo para establecer el valor del campo subject
	 * @param string $subject
	 */
	public function setSubject($subject){
		$this->subject = $subject;
	}

	/**
	 * Metodo para establecer el valor del campo preview
	 * @param string $preview
	 */
	public function setPreview($preview){
		$this->preview = $preview;
	}

	/**
	 * Metodo para establecer el valor del campo message
	 * @param string $message
	 */
	public function setMessage($message){
		$this->message = $message;
	}

	/**
	 * Metodo para establecer el valor del campo priority
	 * @param integer $priority
	 */
	public function setPriority($priority){
		$this->priority = $priority;
	}

	/**
	 * Metodo para establecer el valor del campo asize
	 * @param integer $asize
	 */
	public function setAsize($asize){
		$this->asize = $asize;
	}

	/**
	 * Metodo para establecer el valor del campo hattach
	 * @param string $hattach
	 */
	public function setHattach($hattach){
		$this->hattach = $hattach;
	}

	/**
	 * Metodo para establecer el valor del campo type
	 * @param string $type
	 */
	public function setType($type){
		$this->type = $type;
	}

	/**
	 * Metodo para establecer el valor del campo status
	 * @param string $status
	 */
	public function setStatus($status){
		$this->status = $status;
	}


	/**
	 * Devuelve el valor del campo id
	 * @return integer
	 */
	public function getId(){
		return $this->id;
	}

	/**
	 * Devuelve el valor del campo sid
	 * @return string
	 */
	public function getSid(){
		return $this->sid;
	}

	/**
	 * Devuelve el valor del campo dbname
	 * @return string
	 */
	public function getDbname(){
		return $this->dbname;
	}

	/**
	 * Devuelve el valor del campo mailbox
	 * @return string
	 */
	public function getMailbox(){
		return $this->mailbox;
	}

	/**
	 * Devuelve el valor del campo ususen
	 * @return integer
	 */
	public function getUsusen(){
		return $this->ususen;
	}

	/**
	 * Devuelve el valor del campo codusu
	 * @return integer
	 */
	public function getCodusu(){
		return $this->codusu;
	}

	/**
	 * Devuelve el valor del campo from_msg
	 * @return string
	 */
	public function getFromMsg(){
		return $this->from_msg;
	}

	/**
	 * Devuelve el valor del campo headers
	 * @return string
	 */
	public function getHeaders(){
		return $this->headers;
	}

	/**
	 * Devuelve el valor del campo timsen
	 * @return integer
	 */
	public function getTimsen(){
		return $this->timsen;
	}

	/**
	 * Devuelve el valor del campo subject
	 * @return string
	 */
	public function getSubject(){
		return $this->subject;
	}

	/**
	 * Devuelve el valor del campo preview
	 * @return string
	 */
	public function getPreview(){
		return $this->preview;
	}

	/**
	 * Devuelve el valor del campo message
	 * @return string
	 */
	public function getMessage(){
		return $this->message;
	}

	/**
	 * Devuelve el valor del campo priority
	 * @return integer
	 */
	public function getPriority(){
		return $this->priority;
	}

	/**
	 * Devuelve el valor del campo asize
	 * @return integer
	 */
	public function getAsize(){
		return $this->asize;
	}

	/**
	 * Devuelve el valor del campo hattach
	 * @return string
	 */
	public function getHattach(){
		return $this->hattach;
	}

	/**
	 * Devuelve el valor del campo type
	 * @return string
	 */
	public function getType(){
		return $this->type;
	}

	/**
	 * Devuelve el valor del campo status
	 * @return string
	 */
	public function getStatus(){
		return $this->status;
	}

	/**
	 * Metodo inicializador de la Entidad
	 */
	protected function initialize(){
		$this->setSchema("hfos_mail");
	}

	public function afterCreate(){
		$this->sid = sha1($this->id);
		$this->save();
	}

}

