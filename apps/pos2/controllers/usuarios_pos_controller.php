<?php

/**
 * Hotel Front-Office Solution
 *
 * LICENSE
 *
 * This source file is subject to license that is bundled
 * with this package in the file docs/LICENSE.txt.
 *
 * @package 	Back-Office
 * @copyright 	BH-TECK Inc. 2009-2014
 * @version		$Id$
 */

class Usuarios_PosController extends StandardForm
{

	public $scaffold = true;

	public function beforeInsert($usuarioPos)
	{
		$clave = sprintf("%04s", mt_rand(0, 9999));
		$usuarioPos->clave = sha1($clave);
		Flash::success('La clave del usuario es "'.$clave.'", por favor no la olvide');
	}

	public function beforeUpdate($usuarioPos)
	{
		$usuario = new UsuariosPos();
		$usuario->find($this->getRequestParam("fl_id", "int"));
		$usuarioPos->clave = $usuario->clave;
	}

	public function initialize()
	{

		$this->setTemplateAfter('admin_menu');

		$this->setFormCaption('Mantenimiento de Usuarios');
		$this->setTitleImage('pos2/users.png');

		$this->ignore('clave');
		$this->setTextUpper('nombre');

		$this->setComboStatic('perfil', array(
			array('Administradores', 'ADMINISTRADOR'),
			array('Cajeros', 'CAJERO'),
			array('Meseros', 'MESERO'),
			array('JefeDeAyB', 'JEFE DE A&B'),
			array('CapitanDeMeseros', 'CAPITAN DE MESEROS'),
		));

		$this->setComboStatic('estado', array(
			array('A', 'ACTIVO'),
			array('I', 'INACTIVO')
		));
	}
}
