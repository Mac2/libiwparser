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

namespace libIwParsers\de\parsers;

use libIwParsers\PropertyValueC;
use libIwParsers\DTOParserResultC;
use libIwParsers\ParserBaseC;
use libIwParsers\ParserI;
use libIwParsers\HelperC;

use libIwParsers\de\parserResults\DTOParserInfoGebResultC;
use libIwParsers\de\parserResults\DTOParserInfoGebResultGebC;

///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////

/**
 * Parses a Building Information
 *
 * This parser is responsible for parsing the information of a building
 *
 * Its identifier: de_info_geb
 */
class ParserInfoGebC extends ParserBaseC implements ParserI
{

    /////////////////////////////////////////////////////////////////////////////

    public function __construct()
    {
        parent::__construct();

        $this->setIdentifier('de_info_geb');
        $this->setName("Geb&auml;udeinformationen");
        $this->setRegExpCanParseText('/Geb.{1,3}udeinfo\s+Geb.{1,3}udeinfo|Geb.{1,3}udeinfo.+Farbenlegende/s');
        $this->setRegExpBeginData('');
        $this->setRegExpEndData('');
    }

    /////////////////////////////////////////////////////////////////////////////

    /**
     * @see ParserI::parseText()
     */
    public function parseText(DTOParserResultC $parserResult)
    {
        $parserResult->objResultData = new DTOParserInfoGebResultC();
        $retVal =& $parserResult->objResultData;

        $this->stripTextToData();

        $regExp       = $this->getRegularExpression();
        $regExpRess   = $this->getRegularExpressionRess();
        $regExpMain   = $this->getRegularExpressionMaintenance();
        $regExpEffect = $this->getRegularExpressionEffect();
        $regExpTime   = $this->getRegularExpressionTime();

        $aResult = array();
        $fRetVal = preg_match_all($regExp, $this->getText(), $aResult, PREG_SET_ORDER);
        if ($fRetVal !== false && $fRetVal > 0) {
            $parserResult->bSuccessfullyParsed = true;

            foreach ($aResult as $result) {        
                $retObj = new DTOParserInfoGebResultGebC();
                
                $retObj->aResearchsNeeded = HelperC::convertBracketStringToArray($result['strResearchsNeeded']);
                $retObj->aBuildingsNeeded = HelperC::convertBracketStringToArray($result['strBuildingsNeeded']);
                if (in_array("Gruppe", $retObj->aBuildingsNeeded)) {
                    unset($retObj->aBuildingsNeeded[array_search("Gruppe", $retObj->aBuildingsNeeded)]);
                }

                if (isset($result['strResearchsDevelop'])) {
                    $retObj->aResearchsDevelop = HelperC::convertBracketStringToArray($result['strResearchsDevelop']);
                }

                if (isset($result['strBuildingsDevelop'])) {
                    $retObj->aBuildingsDevelop = HelperC::convertBracketStringToArray($result['strBuildingsDevelop']);
                    if (in_array("Gruppe", $retObj->aBuildingsDevelop)) {
                        unset($retObj->aBuildingsDevelop[array_search("Gruppe", $retObj->aBuildingsDevelop)]);
                    }
                }
                if (isset($result['strPlanetNeeded'])) {
                    $retObj->strPlanetNeeded = PropertyValueC::ensureString($result['strPlanetNeeded']);
                }

                if (isset($result['strObjectsNeeded'])) {
                    $retObj->strObjectTypesNeeded = PropertyValueC::ensureString($result['strObjectsNeeded']);
                }

                if (isset($result['strPlanetPropertiesNeeded'])) {
                    $retObj->aPlanetPropertiesNeeded = explode(" ", PropertyValueC::ensureString($result['strPlanetPropertiesNeeded']));
                }

                $retObj->strGebName = trim(PropertyValueC::ensureString($result['strBuildingName']));

                if (isset($result['comment'])) {
                    $retObj->strGebComment = trim(PropertyValueC::ensureString($result['comment']));
                } else if (isset($result['commentS'])) {
                    $retObj->strGebComment = trim(PropertyValueC::ensureString($result['commentS']));
                }

                $retObj->iHS     = PropertyValueC::ensureInteger($result['iHS']);
                $retObj->imaxAnz = PropertyValueC::ensureInteger($result['imaxAnz']);

                if (!empty($result['stufe'])) {
                    $retObj->bIsStufenGeb = true;
                    $retObj->iStufe       = PropertyValueC::ensureInteger($result['stufe']);
                }

                //! Kosten
                $treffer = array();
                preg_match_all($regExpRess, $result['kosten'], $treffer, PREG_SET_ORDER);
                $stufe = 0;
                foreach ($treffer as $teff) {
                    if (!empty($teff['stufe'])) {
                        $stufe = PropertyValueC::ensureInteger($teff['stufe']);
                        if (isset ($teff["rise_type"]) && strpos($teff["rise_type"], "global") !== false) {
                            $retObj->strRiseType = "global";
                        } else if (isset ($teff["rise_type"]) && strpos($teff["rise_type"], "global") === false) {
                            $retObj->strRiseType = "local";
                        }
                        continue;
                    }

                    if (!empty($stufe) && !empty($teff['resource_name'])) {
                        if (count($retObj->aCosts) == 0) {
                            $retObj->aCosts[] = array(
                                'strResourceName' => PropertyValueC::ensureEnum($teff['resource_name'], 'eResources'),
                                'iResourceCount'  => PropertyValueC::ensureInteger($teff['resource_count']),
                                'stufe'           => $stufe
                            );
                        } else {
                            $exist = false;
                            foreach ($retObj->aCosts as $dat) {

                                if ($dat['iResourceCount'] == PropertyValueC::ensureInteger($teff['resource_count']) && $dat["strResourceName"] == PropertyValueC::ensureEnum($teff['resource_name'], 'eResources') && $stufe > $dat["stufe"]) {
                                    $exist = true;
                                    break;
                                }
                            }
                            if (!$exist) {
                                $retObj->aCosts[] = array(
                                    'strResourceName' => PropertyValueC::ensureEnum($teff['resource_name'], 'eResources'),
                                    'iResourceCount'  => PropertyValueC::ensureInteger($teff['resource_count']),
                                    'stufe'           => $stufe
                                );
                            }
                        }
                    } else {
                        $retObj->aCosts[] = array(
                            'strResourceName' => PropertyValueC::ensureEnum($teff['resource_name'], 'eResources'),
                            'iResourceCount'  => PropertyValueC::ensureInteger($teff['resource_count']),
                            'stufe'           => 0
                        );
                    }
                }

                //! unterhalt
                $treffer = array();
                preg_match_all($regExpMain, $result['unterhalt'], $treffer, PREG_SET_ORDER);
                foreach ($treffer as $teff) {
                    $retObj->aMaintenance[] = array(
                        'strResourceName' => PropertyValueC::ensureEnum($teff['resource_name'], 'eResources'),
                        'iResourceCount'  => PropertyValueC::ensureInteger($teff['resource_count'])
                    );
                }

                //! Bauzeit
                $treffer = array();
                preg_match_all($regExpTime, $result['dauer'], $treffer, PREG_SET_ORDER);
                $stufe = 0;
                foreach ($treffer as $teff) {
                    if (!empty($teff['stufe'])) {
                        $stufe = PropertyValueC::ensureInteger($teff['stufe']);
                    }

                    if (!empty($stufe) && !empty($teff['build_time'])) {
                        if (count($retObj->aBuildTime) == 0) {
                            $retObj->aBuildTime[] = array(
                                'iBuildTime' => HelperC::convertMixedDurationToSeconds($teff["build_time"]),
                                'stufe'      => $stufe
                            );
                        } else {
                            $exist = false;
                            foreach ($retObj->aBuildTime as $dat) {

                                if ($dat['iBuildTime'] == HelperC::convertMixedDurationToSeconds($teff["build_time"]) && $stufe > $dat["stufe"]) {
                                    $exist = true;
                                    break;
                                }
                            }
                            if (!$exist) {
                                $retObj->aBuildTime[] = array(
                                    'iBuildTime' => HelperC::convertMixedDurationToSeconds($teff["build_time"]),
                                    'stufe'      => $stufe
                                );
                            }
                        }
                    } else { //! keine Verteuerung
                        $retObj->aBuildTime[] = array(
                            'iBuildTime' => HelperC::convertMixedDurationToSeconds($teff["build_time"]),
                            'stufe'      => 0
                        );
                    }
                }

                //! Nutzen
                $treffer = array();
                preg_match_all($regExpEffect, $result['bringt'], $treffer, PREG_SET_ORDER);
                $stufe = 0;
                foreach ($treffer as $teff) {
                    if (!empty($teff['stufe'])) {
                        $stufe = PropertyValueC::ensureInteger($teff['stufe']);
                        continue;
                    }
                    if (!empty($stufe) && !empty($teff['resource_name'])) {
                        if (count($retObj->aEffect) == 0) {
                            $retObj->aEffect[] = array(
                                'strResourceName' => PropertyValueC::ensureEnum($teff['resource_name'], 'eResources'),
                                'iResourceCount'  => PropertyValueC::ensureFloat($teff['resource_count']),
                                'stufe'           => $stufe
                            );
                        } else {
                            $last = $retObj->aEffect[count($retObj->aEffect) - 1];
                            if (!empty($last) && $last['iResourceCount'] != PropertyValueC::ensureFloat($teff['resource_count'])) {
                                $retObj->aEffect[] = array(
                                    'strResourceName' => PropertyValueC::ensureEnum($teff['resource_name'], 'eResources'),
                                    'iResourceCount'  => PropertyValueC::ensureFloat($teff['resource_count']),
                                    'stufe'           => $stufe
                                );
                            }
                        }
                    } else {
                        $retObj->aEffect[] = array(
                            'strResourceName' => PropertyValueC::ensureEnum($teff['resource_name'], 'eResources'),
                            'iResourceCount'  => PropertyValueC::ensureFloat($teff['resource_count']),
                            'stufe'           => 0
                        );
                    }
                }
            
            //! @todo zerstoerbar durch ...
                $retVal->aGeb[$retObj->strGebName] = $retObj;
            }
        } else {
            $parserResult->bSuccessfullyParsed = false;
            $parserResult->aErrors[]           = 'Unable to match the pattern.';
        }

    }

    /////////////////////////////////////////////////////////////////////////////

    private function getRegularExpressionRess()
    {
        /**
         */

        $reResource = $this->getRegExpResource();

        $regExpRess = '/';
        $regExpRess .= '(?:(?P<rise_type>globale\sAnzahl|Stufe)\s(?P<stufe>\d+)\:\s)|(?P<resource_name>' . $reResource . ')\:\s(?P<resource_count>' . $this->getRegExpDecimalNumber() . ')';
        $regExpRess .= '/mx';

        return $regExpRess;
    }

    /////////////////////////////////////////////////////////////////////////////

    private function getRegularExpressionMaintenance()
    {
        /**
         */

        $reResource = $this->getRegExpResource();

        $regExpRess = '/';
        $regExpRess .= '(?P<resource_count>' . $this->getRegExpDecimalNumber() . ')\s(?P<resource_name>' . $reResource . ')';
        $regExpRess .= '/mx';

        return $regExpRess;
    }

    /////////////////////////////////////////////////////////////////////////////

    private function getRegularExpressionTime()
    {
        /**
         */

        $reMixedTime = $this->getRegExpMixedTime();

        $regExpRess = '/';
        $regExpRess .= '(?:globale\sAnzahl\s(?P<stufe>\d+)\:\s)?(?P<build_time>' . $reMixedTime . ')';
        $regExpRess .= '/mx';

        return $regExpRess;
    }


    /////////////////////////////////////////////////////////////////////////////

    private function getRegularExpressionEffect()
    {
        /**
         */

        $reResource      = $this->getRegExpResource();
        $reResourceCount = $this->getRegExpFloatingDouble();

        $regExpRess = '/';
        $regExpRess .= '(?:(?:globale\sAnzahl|Stufe)\s(?P<stufe>\d+)\:\s)|(?P<resource_count>' . $reResourceCount . ')\s(?P<resource_name>' . $reResource . ')';
        $regExpRess .= '/mx';

        return $regExpRess;
    }

    /////////////////////////////////////////////////////////////////////////////

    private function getRegularExpression()
    {
        /**
         */

        $reResearch      = $this->getRegExpSingleLineText3(); //! accepted also numbers in ResearchName
        $reBracketString = $this->getRegExpBracketString();
        $reDecNumber     = $this->getRegExpDecimalNumber();
        $reResource      = $this->getRegExpResource();
        $reMixedTime     = $this->getRegExpMixedTime();
        $reObjects       = $this->getRegExpObjectTypes();
        $rePlanets       = $this->getRegExpPlanetTypes();

        $regExp = '/';
        $regExp .= 'Geb.{1,3}udeinfo\:\s';
        $regExp .= '(?P<strBuildingName>' . $reResearch . ')\s*?';
        $regExp .= '\n+';

        //! da kommentar zu unspezifisch ist, bei Stufengebaeuden explizit kommentar und Stufeninfos verknuepfen...
        $regExp .= '(?:';
        //! Stufengebaeude
        $regExp .= '(?P<commentS>(?:^[\s\S]*\n)?)';
        $regExp .= '(?:^Stufengeb.{1,3}ude';
        $regExp .= '(?:\s*' . $reResearch . '[\n\t])*';
        $regExp .= '^Stufe\s(?P<stufe>\d+)[\n\t]';
        $regExp .= '\s*)';
        //! nur Kommentar
        $regExp .= '|';
        $regExp .= '(?P<comment>(?:^[\s\S]*\n)?)';
        $regExp .= ')';

        $regExp .= 'Kosten\s+?(?P<kosten>(?:(?:(?:globale\sAnzahl|Stufe)\s(?:\d)+\:\s)?(?:' . $reResource . '\:\s' . $reDecNumber . '\s)*[\n\t]*)+)';
        $regExp .= 'Dauer\s+?(?P<dauer>(?:(?:globale\sAnzahl\s(?:\d)+\:\s)?' . $reMixedTime . '[\n\t]*)+)\s*';

        //! ausnahme fuer Forschungsgebaeude da sonst zu fuzzy
        $regExp .= '(?:';
        //! mehrzeilig -> Flabs
        $regExp .= 'bringt\s+?(?P<bringt>(?:';
        $regExp .= '(?:(?:(?:globale\sAnzahl|Stufe)\s(?:\d)+\:\s)' . $reResearch . '[\n\t])+';
        $regExp .= '|';
        //! nur eine zeile -> rest
        $regExp .= '(?:' . $reResearch . '[\n\t]){1,2}';
        $regExp .= '))';
        $regExp .= ')';

        $regExp .= '(?:^Kosten\s+(?P<unterhalt>(?:' . $reDecNumber . '\s' . $reResource . ',?\s*)+))?';

        $regExp .= '(?:Maximale\sAnzahl\s+';
        $regExp .= '(?P<imaxAnz>' . '\d+' . '(?:\s*\(global\))?)\s*?';
        $regExp .= '\n*)?';

        $regExp .= '(?:Entscheidungsgeb\sDieses\sGeb.{1,3}ude\skann\snicht\smehr\sgebaut\swerden,\swenn\seines\sder\sfolgenden\sGeb.{1,3}ude\sgebaut\swurde\:[\n\t]';
        $regExp .= '(?:' . $reResearch . '[\n\t])+';
        $regExp .= ')?';

        $regExp .= '(?:Punkte\s+?';
        $regExp .= '(?P<iHS>' . '\d+' . ')\s*?';
        $regExp .= '\n+)?';

        $regExp .= '\s*Voraussetzungen\sForschungen\s+?';
        $regExp .= '(?P<strResearchsNeeded>' . $reBracketString . ')?';
        $regExp .= '\s+?';
        $regExp .= 'Voraussetzungen\sGeb.{1,3}ude\s+?';
        $regExp .= '(?P<strBuildingsNeeded>' . $reBracketString . '(?:\(Gruppe\))?)?';
        $regExp .= '\s+?';
        $regExp .= '(?:Voraussetzungen\sPlaneteneigenschaften\s+?';
        $regExp .= '(?P<strPlanetPropertiesNeeded>' . $reResearch . ')?';
        $regExp .= '\s+?)?';

        $regExp .= '(?:Voraussetzungen\sPlanetentyp\s+?';
        $regExp .= '(?P<strPlanetNeeded>' . $rePlanets . ')?';
        $regExp .= '\s+?)?';

        $regExp .= '(?:Voraussetzungen\sKolonietyp\s+?';
        $regExp .= '(?P<strObjectsNeeded>' . $reObjects . ')?';
        $regExp .= '\s+?)?';

        $regExp .= '(?:\s*Dieses\sGeb.{1,3}ude\sben.{1,3}tigt\sweitere\skomplexe\sVoraussetzungen\s)?';

        $regExp .= '(?:';
        $regExp .= 'Kann\szerst.{1,3}rt\swerden\sdurch\s+?';
        $regExp .= '(?P<strDestroyable>(?:.*\s' . $reBracketString . '\s*)*)?';
        $regExp .= '\s+?)?';

        $regExp .= 'Erm.{1,3}glicht\sForschungen\s+?';
        $regExp .= '(?P<strResearchsDevelop>' . $reBracketString . ')?';
        $regExp .= '\s+?';

        $regExp .= 'Erm.{1,3}glicht\sGeb.{1,3}ude\s+?';
        $regExp .= '(?P<strBuildingsDevelop>' . $reBracketString . ')?';
        $regExp .= '\s+?';

        $regExp .= '/mx';

        return $regExp;
    }

}