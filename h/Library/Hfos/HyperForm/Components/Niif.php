<?php

/**
 * Hotel Front-Office Solution
 *
 * LICENSE
 *
 * This source file is subject to license that is bundled
 * with this package in the file docs/LICENSE.txt.
 *
 * @package     Back-Office
 * @copyright   BH-TECK Inc. 2009-2010
 * @version     $Id$
 */

class NiifHyperComponent extends UserComponent
{

    private static function _getNombreNiif($value)
    {
        if (!empty($value)) {
            $niif = BackCacher::getNiif($value);
            if ($niif!=false) {
                return $niif->getNombre();
            } else {
                return 'NO EXISTE LA NIIF';
            }
        }
        return '';
    }

    public static function build($name, $component, $value=null, $context=null)
    {
        $nombreNiif = self::_getNombreNiif($value);
        $code = '<table cellspacing="0" cellpadding="0" class="cuentaCompleter">
            <tr>
                <td>'.Tag::numericField(array($name, 'size' => '12', 'maxlength' => 12, 'value' => $value)).'</td>
                <td>'.Tag::textField(array($name.'_det', 'size' => 35, 'class' => 'niifDetalle', 'value' => $nombreNiif)).'</td>
            </tr>
        </table>
        <script type="text/javascript">HfosCommon.addNiifCompleter("'.$name.'", "'.$context.'")</script>';
        return $code;
    }

    public static function getDetail($value)
    {
        if (!empty($value)) {
            return $value . ' / ' . self::_getNombreNiif($value);
        } else {
            return '';
        }
    }

    public static function info()
    {
        return 'Un código de niif contable válido';
    }

}