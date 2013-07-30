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
use libIwParsers\de\parserResults\DTOParserInfoForschungResultC;
use libIwParsers\de\parserResults\DTOParserInfoForschungResultForschungC;

///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////

/**
 * Parses a Forschung Information
 *
 * This parser is responsible for parsing the information of a research
 *
 * Its identifier: de_info_forschung
 */
class ParserInfoForschungC extends ParserBaseC implements ParserI
{

    /////////////////////////////////////////////////////////////////////////////

    public function __construct()
    {
        parent::__construct();

        $this->setIdentifier('de_info_forschung');
        $this->setName("Forschungsinfo");
        $this->setRegExpCanParseText('/Forschungsinfo:.+Status.+Farbenlegende:/sm');
        $this->setRegExpBeginData('/Forschungsinfo\:/');
        $this->setRegExpEndData('/Farbenlegende/');
    }

    /////////////////////////////////////////////////////////////////////////////

    /**
     * @see ParserI::parseText()
     */
    public function parseText(DTOParserResultC $parserResult)
    {
        $parserResult->objResultData = new DTOParserInfoForschungResultC();
        $retVal =& $parserResult->objResultData;

        $this->stripTextToData();

        $regExp     = $this->getRegularExpression();
        $regExpRess = $this->getRegularExpressionRess();

        $aResult = array();
        $fRetVal = preg_match_all($regExp, $this->getText(), $aResult, PREG_SET_ORDER);

        if ($fRetVal !== false && $fRetVal > 0) {
            $parserResult->bSuccessfullyParsed = true;

            //! @TODO Mac: mehrfach Bericht funktionieren noch nicht zuverlässig, da der Comment Block zu allg. ist und alle weiteren Infos mitschlucken kann
            foreach ($aResult as $result) {        
                $retObj = new DTOParserInfoForschungResultForschungC();

                $retObj->aResearchsNeeded       = HelperC::convertBracketStringToArray($result['strResearchsNeeded']);
                $retObj->aBuildingsNeeded       = HelperC::convertBracketStringToArray($result['strBuildingsNeeded']);
                $retObj->aObjectsNeeded         = HelperC::convertBracketStringToArray($result['strObjectsNeeded']);
                $retObj->aResearchsDevelop      = HelperC::convertBracketStringToArray($result['strResearchsDevelop']);
                $retObj->aBuildingsDevelop      = HelperC::convertBracketStringToArray($result['strBuildingsDevelop']);
                $retObj->aBuildingLevelsDevelop = HelperC::convertBracketStringToArray($result['strBuildingLevelsDevelop']);
                $retObj->aDefencesDevelop       = HelperC::convertBracketStringToArray($result['strDefencesDevelop']);
                $retObj->aGeneticsDevelop       = HelperC::convertBracketStringToArray($result['strGeneticsDevelop']);
                $retObj->strResearchName        = PropertyValueC::ensureString($result['strResearchName']);
                $retObj->strAreaName            = PropertyValueC::ensureString($result['strAreaName']);
                $retObj->strStatus              = PropertyValueC::ensureString($result['strStatus']);

                if (!empty($result['first'])) {
                    $retObj->strResearchComment = PropertyValueC::ensureString($result['comment']);
                    $retObj->strResearchFirst   = PropertyValueC::ensureString($result['first']);
                } else {
                    $retObj->strResearchComment = PropertyValueC::ensureString($result['commentonly']);
                }
                $retObj->iFP               = PropertyValueC::ensureInteger($result['fp']);
                $retObj->iPeopleResearched = PropertyValueC::ensureInteger($result['count']);

                if (empty($result['prozent'])) {
                    $result['prozent'] = 100;
                }
                if (empty($result['malus'])) {
                    $result['malus'] = 100;
                }
                $result['faktor'] = (float)$result['prozent'] * $result['malus'] / 100;

                $retObj->iResearchCosts = PropertyValueC::ensureInteger($result['faktor']);

                if (!empty($result['strPrototypName'])) {
                    $retObj->bIsPrototypResearch = true;
                    $retObj->strPrototypName     = PropertyValueC::ensureString($result['strPrototypName']);
                }

                if (!empty($result['kosten'])) {
                    $treffer = array();
                    if (preg_match_all($regExpRess, $result['kosten'], $treffer, PREG_SET_ORDER)) {
                        $retObj->bIsResearchcostsVisible=true;
                        foreach ($treffer as $teff) {
                            $retObj->aCosts[] = array(
                                'strResourceName' => PropertyValueC::ensureEnum($teff['resource_name'], 'eResources'),
                                'iResourceCount'  => PropertyValueC::ensureInteger($teff['resource_count'])
                            );
                        }
                    } else {
                        //$parserResult->bSuccessfullyParsed = false;
                        //$parserResult->aErrors[]           = 'Unable to find ressnames.';
                    }
                }
                if (!empty($result['strMiscCosts'])) {
                    $retObj->strMiscCosts = $result['strMiscCosts'];
                }
                $retVal->aResearch[$retObj->strResearchName] = $retObj;
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
        $regExpRess .= '(?P<resource_name>' . $reResource . ')\:\s(?P<resource_count>' . $this->getRegExpDecimalNumber() . ')';
        $regExpRess .= '/mx';

        return $regExpRess;
    }

    /////////////////////////////////////////////////////////////////////////////

    private function getRegularExpression()
    {
        /**
         */

        $reResearch      = $this->getRegExpSingleLineText3(); //! accepted also numbers in ResearchName
        $reSchiffeName   = $this->getRegExpSingleLineText3();
        $reBracketString = $this->getRegExpBracketString();
        $reAreas         = $this->getRegExpAreas();
        $reFP            = $this->getRegExpDecimalNumber();
        $reCosts         = $this->getRegExpDecimalNumber();
        $reResource      = $this->getRegExpResource();


        $regExp = '/';
        $regExp .= '(?P<strResearchName>' . $reResearch . ')\s*?';
        $regExp .= '\n+';
        $regExp .= 'Status\s+?';
        $regExp .= '(?P<strStatus>' . '(?:erforscht|erforschbar|nicht\serforschbar|erforschbar,\saber\sGeb.{1,3}ude\sfehlt)' . ')\s*?';
        $regExp .= '\n+';
        $regExp .= 'Gebiet\s+?';
        $regExp .= '(?P<strAreaName>' . $reAreas . ')\s*?';
        $regExp .= '\n+';
        $regExp .= '(?:';
        //! schon erforschte Variante
        $regExp .= '   (?:(?P<comment>[\s\S]*?)';
        $regExp .= '   \n+|)';
        $regExp .= '   (?:Zuerst\serforscht\svon\s*(?P<first>[^\n]+?';
        $regExp .= '   \n+))';
        $regExp .= '|';
        //! noch nicht erforschte Variante
        $regExp .= '   (?:(?P<commentonly>[\s\S]*?)';
        $regExp .= '   \n+|)';
        $regExp .= ')';

        $regExp .= 'Kosten\s+?';
        $regExp .= '(?P<fp>' . $reFP . ')\sForschungspunkte';
        $regExp .= '(?P<kosten>(?:\s' . $reResource . '\:\s' . $reCosts . ')+|(?:\s\:\s' . $reCosts . ')+|)';
        $regExp .= '[\n\s]+';

        $regExp .= '(\s+?';
        $regExp .= '(?P<strMiscCosts>' . 'Die\srealen\sForschungskosten\ssind\svon\sweiteren\sParametern\sabh.{1,3}ngig\.)';
        $regExp .= '\s+?)?';

        $regExp .= '(?:\s*?\(von\s(?P<count>\d+)(?:\\\){0,2}%\sLeuten\serforscht,\s(?P<prozent>\d+)(?:\\\){0,2}%\sFPKosten\)';
        $regExp .= '\n+';
        $regExp .= '|)';

        $regExp .= '(?:[\s\n]*?Aufgrund\svon\sgenerellen\stechnischen\sUnverst.{1,3}ndnis\sim\sUniversum,\sliegen\sdie\sForschungskosten\sbei\s(?P<malus>\d+)\s(?:\\\){0,2}%\.';
        $regExp .= '\n+';
        $regExp .= '|)';

        $regExp .= '[\n\s]*';

        $regExp .= '(?:';
        $regExp .= '\s*Prototyp\s+?';
        $regExp .= 'Die\sForschung\sbringt\seinen\sPrototyp\svon\s(?P<strPrototypName>' . $reSchiffeName . ')\s*?';
        $regExp .= '\n+';
        $regExp .= '|)';

        $regExp .= '\s*Voraussetzungen\sForschungen\s+?';
        $regExp .= '(?P<strResearchsNeeded>' . $reBracketString . '|)';
        $regExp .= '\s+?';

        $regExp .= 'Voraussetzungen\sGeb.{1,3}ude\s+?';
        $regExp .= '(?P<strBuildingsNeeded>' . $reBracketString . '|)';
        $regExp .= '\s+?';

        $regExp .= 'Voraussetzungen\sObjekte\s+?';
        $regExp .= '(?P<strObjectsNeeded>' . $reBracketString . '|)';
        $regExp .= '\s+?';

        $regExp .= '(?:\s+?';
        $regExp .= '(?P<strMiscNeeded>' . 'Es\ssind\sweitere\sVoraussetzungen\szu\serf.{1,3}llen,\sum\sdiese\sForschung\serforschen\szu\skönnen\.' . ')?';
        $regExp .= '\s+?)?';

        $regExp .= 'Erm.{1,3}glicht\sForschungen\s+?';
        $regExp .= '(?P<strResearchsDevelop>(' . $reBracketString . ')|)';
        $regExp .= '\s+?';

        $regExp .= 'Erm.{1,3}glicht\sGeb.{1,3}ude\s+?';
        $regExp .= '(?P<strBuildingsDevelop>' . $reBracketString . '|)';
        $regExp .= '\s+?';

        $regExp .= 'Erm.{1,3}glicht\sGeb.{1,3}udestufen\s+?';
        $regExp .= '(?P<strBuildingLevelsDevelop>' . $reBracketString . '|)';
        $regExp .= '\s+?';

        $regExp .= 'Erm.{1,3}glicht\sVerteidigungsanlagen\s+?';
        $regExp .= '(?P<strDefencesDevelop>' . $reBracketString . '|)';
        $regExp .= '\s+?';

        $regExp .= 'Erm.{1,3}glicht\sGenetikoptionen\s*?';
        $regExp .= '(?P<strGeneticsDevelop>' . $reBracketString . '|)';

        $regExp .= '/mx';

        return $regExp;
    }

}