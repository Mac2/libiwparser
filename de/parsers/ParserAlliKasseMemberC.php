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

///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////

/**
 * Parser for the alli bank
 *
 * This parser is responsible for parsing the alli bank
 *
 * Its identifier: de_alli_kasse_member
 */
class ParserAlliKasseMemberC extends ParserBaseC implements ParserI
{

    /////////////////////////////////////////////////////////////////////////////

    public function __construct()
    {
        parent::__construct();

        $this->setIdentifier('de_alli_kasse_member');
        $this->setName("Allianzkasse Mitglieder");
        $this->setRegExpCanParseText('/Allianzkasse.*Kasseninhalt.*Auszahlung.*Auszahlungslog.*Auszahlungslog.*der\sletzten\sdrei\sWochen/smU');
        $this->setRegExpBeginData('/Name\s+angenommen\s+gesamt\s+pro\s+Tag\s*/sm');
        $this->setRegExpEndData('/\(\*\)\.\.\snicht angenommen\,\s\(\*\*\)\.\.\shinter\sder\sNoobbarriere/smU');
    }

    /////////////////////////////////////////////////////////////////////////////

    /**
     * @see ParserI::parseText()
     */
    public function parseText(DTOParserResultC $parserResult)
    {
        $parserResult->objResultData = new DTOParserAlliKasseMemberResultC();
        $retVal =& $parserResult->objResultData;

        $this->stripTextToData();

        $regExp  = $this->getRegularExpression();
        $aResult = array();
        $fRetVal = preg_match_all($regExp, $this->getText(), $aResult, PREG_SET_ORDER);
        if ($fRetVal !== false && $fRetVal > 0) {
            $parserResult->bSuccessfullyParsed = true;
            foreach ($aResult as $result) {
                $member = new DTOParserAlliKasseMemberResultMemberC;

                $iDateTime    = HelperC::convertDateTimeToTimestamp($result['iDateTime']);
                $fCreditsPaid = PropertyValueC::ensureFloat($result['fCreditsPaid']);

                if (!empty($result['bHasNotAccepted'])) {
                    $member->bHasAccepted = false;
                    $member->strUser      = PropertyValueC::ensureString($result['strUser']);
                    //! seit Runde 11 sind Infos trotzdem vorhanden
                    $member->iCreditsPerDay = PropertyValueC::ensureInteger($result['iCreditsPerDay']);
                    $member->fCreditsPaid   = PropertyValueC::ensureFloat($fCreditsPaid);
                } else {

                    $member->strUser        = PropertyValueC::ensureString($result['strUser']);
                    $member->iCreditsPerDay = PropertyValueC::ensureInteger($result['iCreditsPerDay']);
                    $member->iDateTime      = PropertyValueC::ensureInteger($iDateTime);
                    $member->fCreditsPaid   = PropertyValueC::ensureFloat($fCreditsPaid);
                    $member->bHasAccepted   = true;
                }

                $retVal->aMember[] = $member;
            }
        } else {
            $parserResult->bSuccessfullyParsed = false;
            $parserResult->aErrors[]           = 'Unable to match the pattern.';
        }

    }

    /////////////////////////////////////////////////////////////////////////////

    private function getRegularExpression()
    {
        $reDateTime = $this->getRegExpDateTime();
        $reUser     = $this->getRegExpUserName();
        $reInteger  = $this->getRegExpDecimalNumber();
        $reExtended = $this->getRegExpFloatingDouble();

        $regExp = '/^';
        $regExp .= '(?P<strUser>' . $reUser . ')';
        $regExp .= '\s+';
        $regExp .= '(?:';
        $regExp .= '\((?P<bHasNotAccepted>\*)\)';
        $regExp .= '|';
        $regExp .= '(?P<iDateTime>' . $reDateTime . ')';
        $regExp .= ')';
        $regExp .= '\s+';
        $regExp .= '(?P<fCreditsPaid>' . $reExtended . ')';
        $regExp .= '\s+';
        $regExp .= '(?P<iCreditsPerDay>' . $reInteger . ')';
        $regExp .= '\spro\sTag';
        $regExp .= '/m';

        return $regExp;
    }

}