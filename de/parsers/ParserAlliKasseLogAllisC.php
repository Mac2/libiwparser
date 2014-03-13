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
 * Parser for the alli bank
 *
 * This parser is responsible for parsing the alli bank
 *
 * Its identifier: de_alli_kasse_log_allis
 */
class ParserAlliKasseLogAllisC extends ParserBaseC implements ParserI
{

    /////////////////////////////////////////////////////////////////////////////

    public function __construct()
    {
        parent::__construct();

        $this->setIdentifier('de_alli_kasse_log_allis');
        $this->setName("Allianzkasse Auszahlungen(Allianzen)");
        $this->setRegExpCanParseText('/Allianzkasse.*Kasseninhalt.*Auszahlung.*Auszahlungslog.*Auszahlungslog.*der\sletzten\sdrei\sWochen/smU');
        $this->setRegExpBeginData('/Auszahlungslog\san\sWings\/etc\sder\sletzten\sdrei\sWochen\s*/');
        $this->setRegExpEndData('');
    }

    /////////////////////////////////////////////////////////////////////////////

    /**
     * @see ParserI::parseText()
     */
    public function parseText(DTOParserResultC $parserResult)
    {
        $parserResult->objResultData = new DTOParserAlliKasseLogResultC();
        $retVal =& $parserResult->objResultData;

        $this->stripTextToData();

        $regExp = $this->getRegularExpression();

        $aResult = array();
        $fRetVal = preg_match_all($regExp, $this->getText(), $aResult, PREG_SET_ORDER);
        if ($fRetVal !== false && $fRetVal > 0) {
            $parserResult->bSuccessfullyParsed = true;

            foreach ($aResult as $result) {
                $log = new DTOParserAlliKasseLogAllisResultC();

                $log->strFromUser = PropertyValueC::ensureString($result['strFromUser']);
                $log->strAlliName = PropertyValueC::ensureString($result['strAlliName']);
                $log->strAlliTag  = PropertyValueC::ensureString($result['strAlliTag']);
                $log->iDateTime   = HelperC::convertDateTimeToTimestamp($result['reDateTime']);
                $log->iCredits    = PropertyValueC::ensureInteger($result['iCredits']);

                $retVal->aLogs[] = $log;

            }
        } else if ($fRetVal !== false && $fRetVal == 0) {
            $parserResult->bSuccessfullyParsed = true;
            $parserResult->aErrors[]           = 'no Data found';
        } else {
            $parserResult->bSuccessfullyParsed = false;
            $parserResult->aErrors[]           = 'Unable to match the pattern.';
        }
    }

    /////////////////////////////////////////////////////////////////////////////

    private function getRegularExpression()
    {
        $reDateTime = $this->getRegExpDateTime();
        $reFromUser = $this->getRegExpUserName();
        $reInteger  = $this->getRegExpDecimalNumber();
        $reAlliance = $this->getRegExpSingleLineText();

        $regExp = '/^';
        $regExp .= '(?P<reDateTime>' . $reDateTime . ')';
        $regExp .= '\svon\s';
        $regExp .= '(?P<strFromUser>' . $reFromUser . ')';
        $regExp .= '\san\s';
        $regExp .= '(?P<strAlliName>' . $reAlliance . ')';
        $regExp .= '\s\[';
        $regExp .= '(?P<strAlliTag>' . '[^\]]+' . ')';
        $regExp .= '\]';
        $regExp .= '\s';
        $regExp .= '(?P<iCredits>' . $reInteger . ')';
        $regExp .= '\sCredits\sausgezahlt';

        $regExp .= '/';

        return $regExp;
    }

}