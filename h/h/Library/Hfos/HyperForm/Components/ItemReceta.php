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
 * @copyright 	BH-TECK Inc. 2009-2010
 * @version		$Id$
 */

class ItemRecetaHyperComponent extends UserComponent {

    /**
     * Devuelve el nombre de un Item o receta
     * @param string $value ID
     * @param string $tipo
     * @return string
     */
    private static function _getNombreItem($value, $tipo)
    {
        if ($value!==null&&$value!=='') {
            if ($tipo=="I") {
                $inve = BackCacher::getInve($value);
                if($inve!=false){
                    return $inve->getDescripcion();
                } else {
                    return 'NO EXISTE EL ITEM';
                }
            } else {
                $receta = EntityManager::get('Recetap')->findFirst("numero_rec='$value'");
                if($receta!=false){
                    return $receta->getNombre();
                } else {
                    return 'NO EXISTE LA RECETA';
                }
            }
        }
        return '';
    }

    /**
     * Método que crea un campo autocomplete de referencias y receta
     *
     * @param string $name
     * @param string $component
     * @param string $value
     * @param string $context
     * @return string
     */
    public static function build($name, $component, $value=null, $context=null)
    {
        $nombreItem = self::_getNombreItem($value, 'I');
        if (!$nombreItem) {
            $nombreItem = self::_getNombreItem($value, 'R');
        }
        $code = '<table cellspacing="0" cellpadding="0" class="itemRecetaCompleter">
			<tr>
               <td>' . Tag::selectStatic($name . '_tipo', array(
                    'I' => 'Referencia',
                    'R' => 'Receta'
                    ), 'class: itemRecetaDetalleCompleter') .
               '</td>
				<td>' . Tag::textField(array($name, 'size' => '7', 'maxlength' => 15, 'value' => $value)) . '</td>
				<td>
					' . Tag::textField(array($name.'_det', 'size' => 30, 'class' => 'itemDetalle', 'value' => $nombreItem)) . '
				</td>
			</tr>
		</table>
		<script type="text/javascript">HfosCommon.addItemRecetaCompleter("' . $name . '", "' . $context . '")</script>';
        return $code;
    }

    /**
     * Obtiene la presentación del item o receta
     * @param $value
     * @param string $tipo
     * @return string
     */
    public static function getDetail($value, $tipo='I')
    {
        if ($value!='') {
            return $value.' / '.self::_getNombreItem($value, $tipo);
        } else {
            return '';
        }
    }

    /**
     * @return string
     */
    public static function info()
    {
        return 'El documento de un item/receta válido';
    }

}
