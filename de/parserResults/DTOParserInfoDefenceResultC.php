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

namespace libIwParsers\de\parserResults;

///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////

/**
 * Result DTO of parser de_info_defence
 */
class DTOParserInfoDefenceResultC
{
    /**
     * @soap
     * @var string $strSchiffName the name of the ship
     */
    public $strDefenceName = '';
    public $strAreaName = '';
    /**
     * @soap
     * @var integer $iProductionTime the default time to produce the ship
     */
    public $iProductionTime = 0;

    /**
     * @soap
     * @var array $aResearchs array of researchs, needed for the ship
     */
    public $aResearchs = array();

    /**
     * @soap
     * @var integer $iVerbrauchBrause the chemie costs
     */
    public $iVerbrauchBrause = 0;

    /**
     * @soap
     * @var integer $iVerbrauchEnergie the energy costs
     */
    public $iVerbrauchEnergie = 0;

    /**
     * @soap
     * @var array $aCosts further Costs for this ship
     */
    public $aCosts = array();

    /**
     * @soap
     * @var array $aEffectivity to other ship classes
     */
    public $aEffectivity = array();

    /**
     * @soap
     * @var integer value for Battle
     */
    public $iAttack = 0;
    public $iDefence = 0;
    public $strWeaponClass = '';
//  public $iArmour_kin = 0;
//  public $iArmour_grav = 0;
//  public $iArmour_electr = 0;
    public $iShields = 0;
    public $iAccuracy = 0; //! Zielgenauigkeit

}