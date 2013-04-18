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
use libIwParsers\ParserMsgBaseC;
use libIwParsers\ParserMsgI;
use libIwParsers\HelperC;
use libIwParsers\de\parserResults\DTOParserIndexResearchResultC;
use libIwParsers\de\parserResults\DTOParserIndexResearchResultResearchC;

///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////

/**
 * Parser for Mainpage
 *
 * This parser is responsible for the Forschungs section on the Mainpage
 *
 * Its identifier: de_index_research
 */
class ParserIndexResearchC extends ParserMsgBaseC implements ParserMsgI
{

    /////////////////////////////////////////////////////////////////////////////

    public function __construct()
    {
        parent::__construct();

        $this->setIdentifier('de_index_research');
        $this->setCanParseMsg('Research');
    }

    /////////////////////////////////////////////////////////////////////////////

    /**
     * @see ParserMsgI::parseMsg()
     */
    public function parseMsg(DTOParserResultC $parserResult)
    {
        $parserResult->objResultData = new DTOParserIndexResearchResultC();
        $retVal =& $parserResult->objResultData;

        $regExp = $this->getRegularExpression();
        $msg    = $this->getMsg();

        $parserResult->strIdentifier = 'de_index_research';

        $aResult = array();
        $fRetVal = preg_match_all($regExp, $msg->strParserText, $aResult, PREG_SET_ORDER);
        if ($fRetVal !== false && $fRetVal > 0) {
            $parserResult->bSuccessfullyParsed = true;

            foreach ($aResult as $result) {
                $retObj = new DTOParserIndexResearchResultResearchC();

                $retObj->strResearchName = PropertyValueC::ensureString($result['strResearchName']);
                $retObj->iResearchEnd = HelperC::convertDateTimeToTimestamp($result['dtDateTime']);
                if (isset($result['mtMixedTime'])) {
                    $retObj->iResearchEndIn = HelperC::convertMixedDurationToSeconds($result['mtMixedTime']);
                }

                $retVal->aResearch[] = $retObj;
            }
        } else {
            $parserResult->bSuccessfullyParsed = false;
            $parserResult->aErrors[]           = 'Unable to match the pattern.';
            $parserResult->aErrors[]           = $msg->strParserText;
        }
    }

    /////////////////////////////////////////////////////////////////////////////


    private function getRegularExpression()
    {
        $reResearchName = $this->getRegExpSingleLineText();
        $reDateTime     = $this->getRegExpDateTime();
        $reMixedTime    = $this->getRegExpMixedTime();

        $regExp = '/
                (?P<strResearchName>' . $reResearchName . ')
                \s+
                (?P<dtDateTime>' . $reDateTime . ')
                \s
                (?P<mtMixedTime>' . $reMixedTime . ')?
                ';
        $regExp .= '/mxs';

        return $regExp;
    }

}