
/**
 * Hotel Front-Office Solution
 *
 * LICENSE
 *
 * This source file is subject to license that is bundled
 * with this package in the file docs/LICENSE.txt.
 *
 * @package 	Back-Office
 * @copyright 	BH-TECK Inc. 2009-2010
 * @version		$Id$
 */

/**
 * Callbacks para restaurar formularios HyperForm
 */
HfosBindings.late('win-ordenes', 'afterRestore', HyperFormManager.restore.bind(this, 'ordenes'));
HfosBindings.late('win-entradas', 'afterRestore', HyperFormManager.restore.bind(this, 'entradas'));
HfosBindings.late('win-ajustes', 'afterRestore', HyperFormManager.restore.bind(this, 'ajustes'));
HfosBindings.late('win-traslados', 'afterRestore', HyperFormManager.restore.bind(this, 'traslados'));
HfosBindings.late('win-salidas', 'afterRestore', HyperFormManager.restore.bind(this, 'salidas'));
HfosBindings.late('win-pedidos', 'afterRestore', HyperFormManager.restore.bind(this, 'pedidos'));
HfosBindings.late('win-transformaciones', 'afterRestore', HyperFormManager.restore.bind(this, 'transformaciones'));
HfosBindings.late('win-referencias', 'afterRestore', HyperFormManager.restore.bind(this, 'referencias'));
HfosBindings.late('win-lineas', 'afterRestore', HyperFormManager.restore.bind(this, 'lineas'));
HfosBindings.late('win-producto', 'afterRestore', HyperFormManager.restore.bind(this, 'producto'));
HfosBindings.late('win-almacenes', 'afterRestore', HyperFormManager.restore.bind(this, 'almacenes'));
HfosBindings.late('win-centros', 'afterRestore', HyperFormManager.restore.bind(this, 'centros'));
HfosBindings.late('win-formapago', 'afterRestore', HyperFormManager.restore.bind(this, 'formapago'));
HfosBindings.late('win-terceros', 'afterRestore', HyperFormManager.restore.bind(this, 'terceros'));
HfosBindings.late('win-criterios', 'afterRestore', HyperFormManager.restore.bind(this, 'criterios'));
HfosBindings.late('win-unidades', 'afterRestore', HyperFormManager.restore.bind(this, 'unidades'));
HfosBindings.late('win-magnitudes', 'afterRestore', HyperFormManager.restore.bind(this, 'magnitudes'));
HfosBindings.late('win-conversion', 'afterRestore', HyperFormManager.restore.bind(this, 'conversion'));
