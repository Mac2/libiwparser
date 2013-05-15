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

namespace libIwParsers\de\parsers;

use libIwParsers\PropertyValueC;
use libIwParsers\DTOParserResultC;
use libIwParsers\ParserBaseC;
use libIwParsers\ParserI;
use libIwParsers\HelperC;

use libIwParsers\de\parserResults\DTOParserForschungResultC;
use libIwParsers\de\parserResults\DTOParserForschungResearchedResultC;
use libIwParsers\de\parserResults\DTOParserForschungOpenResultC;
use libIwParsers\de\parserResults\DTOParserForschungProgressResultC;

///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////

/**
 * Parses a Planet Information
 *
 * This parser is responsible for parsing the complete research page
 *
 * Its identifier: de_forschung
 */
class ParserForschungC extends ParserBaseC implements ParserI
{

    /////////////////////////////////////////////////////////////////////////////

    public function __construct()
    {
        parent::__construct();

        $this->setIdentifier('de_forschung');
        $this->setName("Forschungs&uuml;bersicht");
        $this->setRegExpCanParseText('/Forschung.*Alle\sForschungen\sanzeigen/s');
        $this->setRegExpBeginData('/Es\swerden\momentan\s.*\spro\sStunde\serzeugt.*Alle\sForschungen\sanzeigen/'); //! Mac: Damit verschw. FP geparsed werden
        $this->setRegExpEndData('');
    }

    /////////////////////////////////////////////////////////////////////////////

    /**
     * @see ParserI::parseText()
     */
    public function parseText(DTOParserResultC $parserResult)
    {
        $parserResult->objResultData = new DTOParserForschungResultC();
        $retVal =& $parserResult->objResultData;

        $this->stripTextToData();

        $regExp     = $this->getRegularExpression();
        $regExpRess = $this->getRegularExpressionRess();

        //! Mac: da sonst Probleme mit regExpAreas auftreten, muss hier eine Ersetzung erfolgen
        $this->setText(str_replace("Forschung wird", "Forschung_wird", $this->getText()));
        $this->setText(str_replace("orbitale Verteidigung", "orbitale_Verteidigung", $this->getText()));
        //$this->setText(str_replace("\n\n","",$this->getText()));

        $aResult = array();
        $fRetVal = preg_match_all($regExp, $this->getText(), $aResult, PREG_SET_ORDER);

        if ($fRetVal !== false && $fRetVal > 0) {
            $parserResult->bSuccessfullyParsed = true;
            $area_name                         = "";
            foreach ($aResult as $result) {
                if (!empty($result["globalMalusRed"]) || !empty($result["globalBonusRed"]) || !empty($result["FPverschw"]) || $result["FPverschw"] === "0") {
                    if (!empty($result["globalMalusRed"])) {
                        $retVal->iMalusRed = PropertyValueC::ensureInteger($result['globalMalusRed']);
                    }
                    if (!empty($result["globalBonusRed"])) {
                        $retVal->iBonusRed = PropertyValueC::ensureInteger($result['globalBonusRed']);
                    }
                    if (!empty($result["FPverschw"])) {
                        $retVal->iverschwFP = PropertyValueC::ensureInteger($result['FPverschw']);
                    }
                    continue;
                }

                if (!empty($result['area'])) {
                    $area_name = $result['area'];
                    continue;
                }

                if (isset($result['research'])) {
                    $result["research"] = str_replace("orbitale_Verteidigung", "orbitale Verteidigung", $result["research"]); //! Mac: da sonst Probleme mit regExpAreas auftreten, muss hier eine Ersetzung erfolgen
                } else {
                    $result["research"] = "";
                }

                if ($result["research"] == "erforscht") { //sollte nicht mehr der Fall sein?
                    $parserResult->bSuccessfullyParsed = false;
                    $parserResult->aErrors[]           = 'Unable to determine researchname (<pre>' . $result[0] . '</pre>)';
                    continue;
                }

                if (empty($result['state']) || !in_array($result['state'], array('erforscht', 'zu wenig Ress', 'forschen', '---', 'wird erforscht'))) {
                    $parserResult->bSuccessfullyParsed = false;
                    $parserResult->aErrors[]           = 'Unable to determine valid research status (' . $result["research"] . ')';
                    continue;
                }

                if (empty($result['prozent'])) {
                    $result['prozent'] = 100;
                }
                if (empty($result['malus'])) {
                    $result['malus'] = 100;
                }
                $result['faktor'] = (float)$result['prozent'] * $result['malus'] / 100;
                if (isset($result["fp"])) {
                    $result['fp'] = PropertyValueC::ensureInteger($result['fp']); //! da sonst beim Punkt abgeschnitten wird
                } else {
                    $result['fp'] = 0;
                }

                if ($result['state'] === 'erforscht') {
                    $ret                     = new DTOParserForschungResearchedResultC;
                    $ret->strResearchName    = PropertyValueC::ensureString($result['research']);
                    $ret->strResearchComment = PropertyValueC::ensureString($result['comment']);
                    $ret->strAreaName        = PropertyValueC::ensureString($area_name);
                    $ret->iFP                = PropertyValueC::ensureInteger($result['fp'] / $result['faktor'] * 100);
                    $ret->iPeopleResearched  = PropertyValueC::ensureInteger($result['count']);
                    $ret->iResearchCosts     = PropertyValueC::ensureInteger($result['faktor']);
                    $ret->iUserResearchTime  = HelperC::convertMixedDurationToSeconds($result['dauer']);

                    if (!empty($result['kosten'])) {
                        $treffer = array();
                        $kRetVal = preg_match_all($regExpRess, $result['kosten'], $treffer, PREG_SET_ORDER);
                        if ($kRetVal !== false && $kRetVal > 0) {
                            foreach ($treffer as $teff) {
                                $ret->aCosts[] = array(
                                    'strResourceName' => PropertyValueC::ensureEnum($teff['resource_name'], 'eResources'),
                                    'iResourceCount'  => PropertyValueC::ensureInteger($teff['resource_count'])
                                );
                            }
                        } else {
                            //$parserResult->aErrors[] = 'Unable to match the costs pattern (' .$ret->strResearchName. ').';
                        }
                    }
                    $retVal->aResearchsResearched[] = $ret;
                    continue;
                } else if ($result['state'] === '---' || $result['state'] === 'zu wenig Ress') {
                    $ret                     = new DTOParserForschungOpenResultC;
                    $ret->strResearchName    = PropertyValueC::ensureString($result['research']);
                    $ret->strResearchComment = PropertyValueC::ensureString($result['comment']);
                    $ret->strAreaName        = PropertyValueC::ensureString($area_name);
                    $ret->iFP                = PropertyValueC::ensureInteger($result['fp'] / $result['faktor'] * 100);
                    $ret->iPeopleResearched  = PropertyValueC::ensureInteger($result['count']);
                    $ret->iResearchCosts     = PropertyValueC::ensureInteger($result['faktor']);
                    $ret->iUserResearchTime  = HelperC::convertMixedDurationToSeconds($result['dauer']);

                    if (!empty($result['kosten'])) {
                        $treffer = array();
                        $kRetVal = preg_match_all($regExpRess, $result['kosten'], $treffer, PREG_SET_ORDER);
                        if ($kRetVal !== false && $kRetVal > 0) {
                            foreach ($treffer as $teff) {
                                $ret->aCosts[] = array(
                                    'strResourceName' => PropertyValueC::ensureEnum($teff['resource_name'], 'eResources'),
                                    'iResourceCount'  => PropertyValueC::ensureInteger($teff['resource_count'])
                                );
                            }
                        } else {
                            //$parserResult->aErrors[] = 'Unable to match the costs pattern (' .$ret->strResearchName. ').';
                        }
                    }

                    $retVal->aResearchsOpen[] = $ret;
                    continue;
                } else if ($result['state'] === 'forschen') {
                    $ret                     = new DTOParserForschungOpenResultC;
                    $ret->strResearchName    = PropertyValueC::ensureString($result['research']);
                    $ret->strResearchComment = PropertyValueC::ensureString($result['comment']);
                    $ret->strAreaName        = PropertyValueC::ensureString($area_name);
                    $ret->iFP                = PropertyValueC::ensureInteger($result['fp'] / $result['faktor'] * 100);
                    $ret->iPeopleResearched  = PropertyValueC::ensureInteger($result['count']);
                    $ret->iResearchCosts     = PropertyValueC::ensureInteger($result['faktor']);
                    $ret->iUserResearchTime  = HelperC::convertDateTimeToTimestamp($result['endtime']);

                    if (!empty($result['kosten'])) {
                        $treffer = array();
                        $kRetVal = preg_match_all($regExpRess, $result['kosten'], $treffer, PREG_SET_ORDER);
                        if ($kRetVal !== false && $kRetVal > 0) {
                            foreach ($treffer as $teff) {
                                $ret->aCosts[] = array(
                                    'strResourceName' => PropertyValueC::ensureEnum($teff['resource_name'], 'eResources'),
                                    'iResourceCount'  => PropertyValueC::ensureInteger($teff['resource_count'])
                                );
                            }
                        } else {
                            //$parserResult->aErrors[] = 'Unable to match the costs pattern for (' .$ret->strResearchName. ').';
                        }
                    }

                    $retVal->aResearchsOpen[] = $ret;
                    continue;
                } else if ($result['state'] == 'wird erforscht') {
                    $ret                     = new DTOParserForschungProgressResultC;
                    $ret->strResearchName    = PropertyValueC::ensureString($result['research']);
                    $ret->strResearchComment = PropertyValueC::ensureString($result['comment']);
                    $ret->strAreaName        = PropertyValueC::ensureString($area_name);
                    $ret->iFP                = PropertyValueC::ensureInteger($result['fp'] / $result['faktor'] * 100);
                    $ret->iPeopleResearched  = PropertyValueC::ensureInteger($result['count']);
                    $ret->iResearchCosts     = PropertyValueC::ensureInteger($result['faktor']);
                    if (isset($result['finish'])) {
                        $ret->iUserResearchTime = HelperC::convertDateTimeToTimestamp($result['finish']);
                    }
                    if (isset($result['expire'])) {
                        $ret->iUserResearchDuration = HelperC::convertMixedDurationToSeconds($result['expire']);
                    }
                    $retVal->aResearchsProgress[] = $ret;
                    continue;
                }
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
        $recount    = $this->getRegExpDecimalNumber();
        $regExpRess = '/';
        $regExpRess .= '(?P<resource_name>' . $reResource . ')\:\s(?P<resource_count>' . $recount . ')';
        $regExpRess .= '/mx';

        return $regExpRess;
    }

    private function getRegularExpression()
    {
        /**
         */

        $reResearch        = '[\wÖöäÄüÜ]+[^\n\t\:\+]{3,}(?<!erforscht)'; //! Mac: hier evtl. konrket 'erforscht' ausschliessen ?
        $reResearchComment = $this->getRegExpSingleLineText3();
        $reDecimalNumber   = $this->getRegExpDecimalNumber();
        $reDateTime        = $this->getRegExpDateTime();
        $reMixedTime       = $this->getRegExpMixedTime();
        $reAreas           = $this->getRegExpAreas();
        $reResource        = $this->getRegExpResource();

        $regExp = '/';

        //! allg. Infoblock (Infos ueber globale Mods, sind nicht immer vorhande!?)
        $regExp .= '(?:';
        $regExp .= 'Die\sForscher\shaben\sschon\s(?P<FPverschw>' . $reDecimalNumber . ')\sFP\serfolgreich\svergeudet,\sindem\ssie\swas\sv.{1,3}llig\sanderes\staten\sals\sforschen\.\s';
        $regExp .= '(?:' . $reResearchComment . '\s' . '){0,4}';
        $regExp .= 'Forschungen\sausblenden\s\/\sAlle\sForschungen\sanzeigen\s+';
        $regExp .= '  (?:Aktuelle\sEffekte\sauf\sForschungsboni\sund\s-mali:';
        $regExp .= '    \sReduktion\sdes\smaximalen\sMalus\sum\s(?P<globalMalusRed>\d{1,3})\sProzentpunkte,';
        $regExp .= '    \sErh.{0,3}hung\sdes\sBonus\sum\s(?P<globalBonusRed>\d{1,3})\sProzentpunkte\.';
        $regExp .= '  )?';
        $regExp .= ')';
        //! eigentlicher Forschungsblock
        $regExp .= '|';
        $regExp .= '(?:';
        //! keine aufeinanderfolgenden, identischen Zeilen ausser bei Raumfahrt,
        //! und keine Area gefolgt von einer Zahl -> Forschungspkt
        $regExp .= '    (?:^(?P<area>(?:'.$reAreas.'(?!\s(?:'.$reAreas.'|'.$reDecimalNumber.'\sForschungspunkte\s))|Raumfahrt(?=\sRaumfahrt)))\s|';
        $regExp .= '        (?:';
        $regExp .= '            (?P<research>'.$reResearch .')\s';
        $regExp .= '            (?P<comment>.*\n|)';
        $regExp .= '            (?P<fp>' . $reDecimalNumber . ')';
        $regExp .= '            \sForschungspunkte\s*';
        $regExp .= '            (?:\(von\s(?P<count>\d+)(?:\\\){0,2}%\sLeuten\serforscht,\s(?P<prozent>\d+)(?:\\\){0,2}%\sFPKosten\)\s|)';
        $regExp .= '            (?:Dauer\:\s(?P<dauer>'.$reMixedTime .')\s|)';
        $regExp .= '            (?P<kosten>(?:(?:'.$reResource.')\:\s'.$reDecimalNumber.'\s)+|)';
        $regExp .= '            (?:\s*(?:Ressourcen\sin\sabsehbarer\sZeit\snicht\svorhanden|Ressourcen\svorhanden\sin.*(?:\(Info\nben.{1,3}tigt\sIWSA\)|))\s|)';

        $regExp .= '            (?:[\s\n]*Aufgrund\svon\sgenerellen\stechnischen\sUnverst.{1,3}ndnis\sim\sUniversum\,\sliegen\sdie\sForschungskosten\sbei\s(?P<malus>\d+)\s(?:\%|\\\%)\.\s\?\s|)';
        $regExp .= '            (?:^(?:Forschung_wird\sangezeigt|Forschung_wird\snicht\sangezeigt|S|N|)\s|)'; //! evtl. nur beim FF vorhanden ?
        $regExp .= '            \s*';
        $regExp .= '            (?:Stufe\s\d\s|)';
        $regExp .= '            (?P<state>---|wird\serforscht|zu\swenig\sRess|forschen|erforscht)';
        $regExp .= '            (?:\nbis\:\s(?P<finish>'.$reDateTime.')\n\s*(?P<expire>'.$reMixedTime.')|)';
        $regExp .= '            (?:\n\s*(?P<duration>'.$reMixedTime.')\n\s*(?P<endtime>'.$reDateTime.')|)';
        $regExp .= '        )';
        $regExp .= '    )';
        $regExp .= ')';
        $regExp .= '/mx';

        return $regExp;
    }

}