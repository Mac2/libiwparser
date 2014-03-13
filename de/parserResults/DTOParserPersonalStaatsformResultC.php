<?php
/*
 * ----------------------------------------------------------------------------
 * "THE BEER-WARE LICENSE" (Revision 42):
 * <martin@martimeo.de> wrote this file. As long as you retain
 * this notice you can do whatever you want with this stuff. If we meet some
 * day, and you think this stuff is worth it, you can buy me a beer in return.
 * Martin Martimeo
 * ----------------------------------------------------------------------------
 */
/**
 * @author     Martin Martimeo <martin@martimeo.de>
 * @package    libIwParsers
 * @subpackage parsers_de
 */

///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////

/**
 * Result DTO of parser de_personal_staatsform
 */
class DTOParserPersonalStaatsformResultC
{
    /**
     * @soap
     * @var boolean $bStaatsformChosen
     */
    public $bStaatsformChosen = false;

    /**
     * @soap
     * @var string $strStaatsform the choosen Staatsform
     */
    public $strStaatsform = '';

    /**
     * @soap
     * @var string $strVorteile die Vor und Nachteile
     */
    public $strVorteile = '';
}