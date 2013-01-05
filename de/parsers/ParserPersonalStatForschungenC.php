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

namespace libIwParsers\de\parsers;

use libIwParsers\PropertyValueC;
use libIwParsers\DTOParserResultC;
use libIwParsers\ParserBaseC;
use libIwParsers\ParserI;
use libIwParsers\HelperC;
use libIwParsers\de\parserResults\DTOParserPersonalStatForschungenResultC;
use libIwParsers\de\parserResults\DTOParserPersonalStatForschungenResearchResultC;

///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////

/**
 * Parser for the research stats
 *
 * This parser is responsible for parsing the reserach statistic in the personal menu
 *
 * Its identifier: de_personal_stat_forschungen
 */
class ParserPersonalStatForschungenC extends ParserBaseC implements ParserI
{

    /////////////////////////////////////////////////////////////////////////////

    public function __construct()
    {
        parent::__construct();

        $this->setIdentifier('de_personal_stat_forschungen');
        $this->setName('persönlicher Forschungsablauf');
        $this->setRegExpCanParseText('/Statistiken\s\-\sForschungsablauf[\n\s]+Datum\s+Vergangene\s+Zeit\s+Forschung[\n\s]+/sm');
        $this->setRegExpBeginData($this->getRegExpCanParseText());
        $this->setRegExpEndData('');
    }

    /////////////////////////////////////////////////////////////////////////////

    /**
     * @see ParserI::parseText()
     */
    public function parseText(DTOParserResultC $parserResult)
    {
        $parserResult->objResultData = new DTOParserPersonalStatForschungenResultC();
        $retVal =& $parserResult->objResultData;

        $this->stripTextToData();

        $regExp = $this->getRegularExpression();

        $aResult = array();
        $fRetVal = preg_match_all($regExp, $this->getText(), $aResult, PREG_SET_ORDER);

        if ($fRetVal !== false && $fRetVal > 0) {
            $parserResult->bSuccessfullyParsed = true;

            foreach ($aResult as $result) {
                $iDateExpired = -1;
                $iDateOfResearch = -1;
                $strResearch = '';

                $iDateOfResearch = HelperC::convertDateTimeToTimestamp($result['dateOfResearch']);
                $iDateExpired = HelperC::convertMixedDurationToSeconds($result['dateExpired']);
                $strResearch = $result['research'];

                $research = new DTOParserPersonalStatForschungenResearchResultC;

                $research->iDateExpired = PropertyValueC::ensureInteger($iDateExpired);
                $research->iDateOfResearch = PropertyValueC::ensureInteger($iDateOfResearch);
                $research->strResearch = PropertyValueC::ensureString(trim($strResearch));

                $retVal->aResearchs[] = $research;
            }
        } else {
            $parserResult->bSuccessfullyParsed = false;
            $parserResult->aErrors[] = 'Unable to match the pattern.';
        }

    }

    /////////////////////////////////////////////////////////////////////////////

    private function getRegularExpression()
    {
        /**
         * die Daten sind Zeilen, von denen jede folgendermaßen aussieht:
         * Datum | Vergangene Zeit | Forschung
         */

        $reDateOfResearch = $this->getRegExpDateTime();
        $reDateExpired = $this->getRegExpMixedTime();
        $reResearch = $this->getRegExpSingleLineText();

        $regExp = '/^';
        $regExp .= '(?P<dateOfResearch>' . $reDateOfResearch . ')\s*';
        $regExp .= '(?P<dateExpired>' . $reDateExpired . ')\s*';
        $regExp .= '(?P<research>' . $reResearch . ')';
        $regExp .= '$/m';

        return $regExp;
    }

}