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

namespace libIwParsers\de\parserResults;

///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////

/**
 * @todo refactor. We really shouldn't need several objects describing a
 *       planet (compare DTOParserUniversumPlanetResultC and DTOPlanet).
 *
 * Maybe we can refactor DTOPlanet in way that it only holds the most basic
 * data plus an additional child object that cares for things like gravity
 * (which in turn could have a member $bIsInitialized).
 */
class DTOParserUniversumPlanetResultC
{
    /**
     * @soap
     * @var array $aCoords the coords
     *
     * @deprecated the problem with array is, that you need to know which key
     *             points to which value:
     * - Is it index based?
     * - Is it a dictionary?
     * - If yes, what are its keys named?
     *
     * @see        $objCoordinates
     */
    public $aCoords = array();

    /**
     * @soap
     * @var DTOCoordinatesC $objCoordinates an object describing
     *      the coordinates of the planet.
     */
    public $objCoordinates;

    /**
     * @soap
     * @var string $strCoords the coordinates in their
     * string representation.
     */
    public $strCoords = '';

    /**
     * @soap
     * @var string $strNebula the colour of the nebula (language dependent)
     * @see $eNebula for language independent information
     */
    public $strNebula = '';

    /**
     * @soap
     * @var string $eNebula one value of ePlanetSpecials describing the nebula
     *       the planet is in (or empty string if the planet is not in nebula).
     *       Be aware, that although this is one of the ePlanetSpecials values, this
     *       parser is unable to detect any other planet specials than nebula
     *       information! So you should not overwrite the specials of your stored
     *       planets with what this parser returns.
     * @todo check how enums can be transformed and transported
     */
    public $eNebula = '';

    /**
     * @soap
     * @var boolean $bHasNebula is this planet in a nebula?
     */
    public $bHasNebula = '';

    /**
     * @soap
     * @var integer $iIngamePlaiid the if of the coordination
     */
    public $iIngamePlaiid = '';

    /**
     * @soap
     * @var string $strPlanetType the type of the planet
     * @deprecated should be an enum value, use
     * @see        $ePlanetType
     */
    public $strPlanetType = '';

    /**
     * @soap
     * @var string $ePlanetType the type of the planet
     * @todo check how enums can be transformed and transported
     */
    public $ePlanetType = '';

    /**
     * @soap
     * @var string $strObjectType the type of the object
     * @deprecated should be an enum value
     * @see        $eObjectType
     */
    public $strObjectType = '';

    /**
     * @soap
     * @var string $eObjectType the type of the object at these coordinates
     * @todo check how enums can be transformed and transported
     */
    public $eObjectType = '';

    /**
     * @soap
     * @var string $strUserName the user of the object
     */
    public $strUserName = '';

    /**
     * @soap
     * @var string $strUserAlliance the allianze tag of the users alliance
     */
    public $strUserAlliance = '';

    /**
     * @soap
     * @var string $strPlanetName name of the object
     */
    public $strPlanetName = '';

}