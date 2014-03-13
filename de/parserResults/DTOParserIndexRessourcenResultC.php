<?php
/*
 * ----------------------------------------------------------------------------
 * "THE BEER-WARE LICENSE" (Revision 42):
 * <MacXY@herr-der-mails.de> wrote this file. As long as you retain
 * this notice you can do whatever you want with this stuff. If we meet some
 * day, and you think this stuff is worth it, you can buy me a beer in return.
 * Mac
 * ----------------------------------------------------------------------------
 */
/**
 * @author     Mac <MacXY@herr-der-mails.de>
 * @package    libIwParsers
 * @subpackage parsers_de
 */

///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////

/**
 * Result DTO of parser de_index_ressources
 */
class DTOParserIndexRessourcenResultC
{
    /**
     * @soap
     * @var array $aCoords the coords
     */
    public $aCoords = '';

    /**
     * @soap
     * @var string $strCoords the coords
     */
    public $strCoords = '';

    /**
     * @soap
     * @var string $strObjectType the type of this kolo
     */
    public $strObjectType = '';

    /**
     * @soap
     * @var array $aData of DTOParserWirtschaftPlaniressRessResultC
     */
    public $aData = array();

}

///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////

/**
 */
class DTOParserIndexRessourcenRessResultC
{
    /**
     * @soap
     * @var string $strResourceName name of the resource
     */
    public $strResourceName = array();

    /**
     * @soap
     * @var integer $iResourceVorrat amount of ress at the planet
     */
    public $iResourceVorrat = 0;

    /**
     * @soap
     * @var float $fResourceProduction production of the resource
     */
    public $fResourceProduction = 0.00;

    /**
     * @soap
     * @var integer $iResourceBunker what can be stored without to be raided
     */
    public $iResourceBunker = 0;

    /**
     * @soap
     * @var integer $iResourceLager storage capa (or 0 at eisen,vv4a,stahl)
     */
    public $iResourceLager = 0;
}

class DTOParserIndexRessourcenBevResultC
{
    /**
     * @soap
     * @var string $strResourceName name of the resource
     */
    public $strResourceName = array();

    /**
     * @soap
     * @var integer $iBevfrei
     */
    public $iBevfrei = 0;

    /**
     * @soap
     * @var integer $iBevges
     */
    public $iBevges = 0;

    /**
     * @soap
     * @var integer $iBevmax
     */
    public $iBevmax = 0;
}