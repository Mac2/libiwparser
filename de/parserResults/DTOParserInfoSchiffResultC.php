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
 * @author     Mac <MacXY@herr-der-mails.de>
 * @package    libIwParsers
 * @subpackage parsers_de
 */

///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////

/**
 * Result DTO of parser de_info_schiff
 */
class DTOParserInfoSchiffResultC
{
    /**
     * @soap
     * @var string $strSchiffName the name of the ship
     */
    public $strSchiffName = '';
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
     * @var array $aActions array of actions the ship can do
     */
    public $aActions = array();

    /**
     * @soap
     * @var integer $iGschwdSol the sol speed (between planets in the same sol)
     */
    public $iGschwdSol = 0;

    /**
     * @soap
     * @var integer $iGschwdGal the gal speed (between sols in the same gal)
     */
    public $iGschwdGal = 0;

    /**
     * @soap
     * @var boolean $bCanLeaveGalaxy can this ship leave his galaxy?
     */
    public $bCanLeaveGalaxy = false;
    public $bCanBeTransported = false;

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
     * @var boolean $bIsTransporter is this ship a transporter?
     */
    public $bIsTransporter = false;

    /**
     * @soap
     * @var integer $iKapa1 how much eisen (1/2 stahl, 1/4 vv4a, 1/3 brause) can this ship transport?
     */
    public $iKapa1 = 0;

    /**
     * @soap
     * @var integer $iKapa2 how much energy (1/2 eis, 1/2 wasser) can this ship transport?
     */
    public $iKapa2 = 0;

    /**
     * @soap
     * @var integer $iKapaBev how mutch people can this ship transport?
     */
    public $iKapaBev = 0;

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
     * @var boolean $bIsCarrier is this ship a carrier?
     */
    public $bIsCarrier = false;

    /**
     * @soap
     * @var integer $iShipKapa1 how much ships can this carrier transport?
     */
    public $iShipKapa1 = 0;

    /**
     * @soap
     * @var integer $iShipKapa2 how much ships can this carrier transport?
     */
    public $iShipKapa2 = 0;
    public $iShipKapa3 = 0;

    /**
     * @soap
     * @var integer value for Battle
     */
    public $iAttack = 0;
    public $iDefence = 0;
    public $strWeaponClass = '';
    public $iArmour_kin = 0;
    public $iArmour_grav = 0;
    public $iArmour_electr = 0;
    public $iShields = 0;
    public $iAccuracy = 0; //! Zielgenauigkeit
    public $iMobility = 0; //! Wendigkeit

    public $iNoEscort = 0; //! Anzahl Geleitschutz
    public $fBonusAtt = 1.;
    public $fBonusDef = 1.;

    public $strWerftTyp = "";

}