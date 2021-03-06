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
 * Its identifier: de_alli_kasse_log_member
 */
class ParserAlliKasseLogMemberC extends ParserBaseC implements ParserI
{

    /////////////////////////////////////////////////////////////////////////////

    public function __construct()
    {
        parent::__construct();

        $this->setIdentifier('de_alli_kasse_log_member');
        $this->setName("Allianzkasse Auszahlungen(Mitglieder)");
        $this->setRegExpCanParseText('/Allianzkasse.*Kasseninhalt.*Auszahlung.*Auszahlungslog.*Auszahlungslog.*der\sletzten\sdrei\sWochen/smU');
        $this->setRegExpBeginData('/Auszahlungslog\san\sSpieler\sw.{1,5}hrend\sder\sletzten\sdrei\sWochen/');
        $this->setRegExpEndData('/Auszahlungslog\san\sWings\/etc\sder\sletzten/');
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
                $log = new DTOParserAlliKasseLogMemberResultC;

                $log->iDateTime = HelperC::convertDateTimeToTimestamp($result['reDateTime']);
                $log->iCredits  = PropertyValueC::ensureInteger($result['iCredits']);

                $log->strFromUser = PropertyValueC::ensureString($result['strFromUser']);
                $log->strToUser   = PropertyValueC::ensureString($result['strToUser']);
                if (isset($result['strReason'])) {
                    $log->strReason = PropertyValueC::ensureString($result['strReason']);
                }
                $retVal->aLogs[] = $log;
            }

        } else if ($fRetVal !== false && $fRetVal == 0) {
            $parserResult->bSuccessfullyParsed = true;
            $parserResult->aErrors[]           = 'no Data found';
        } else {
            $parserResult->bSuccessfullyParsed = false;
            $parserResult->aErrors[]           = 'Unable to match the de_alli_kasse_log_member pattern.';
        }

    }

    /////////////////////////////////////////////////////////////////////////////

    private function getRegularExpression()
    {
        $reDateTime = $this->getRegExpDateTime();
        $reFromUser = $this->getRegExpUserName();
        $reToUser   = $this->getRegExpUserName();
        $reInteger  = $this->getRegExpDecimalNumber();
        $reReason   = '[^\n]*?';

        $regExp = '/';

        $regExp .= '(?P<reDateTime>' . $reDateTime . ')';
        $regExp .= '\svon\s';
        $regExp .= '(?P<strFromUser>' . $reFromUser . ')';
        $regExp .= '\san\s';
        $regExp .= '(?P<strToUser>' . $reToUser . ')';
        $regExp .= '\s';
        $regExp .= '(?P<iCredits>' . $reInteger . ')';
        $regExp .= '\sCredits\sausgezahlt';

        $regExp .= '(?:\sGrund\swar\s';
        $regExp .= '(?P<strReason>' . $reReason . ')';
        $regExp .= '\.)?';

        $regExp .= '/';

        return $regExp;
    }

}