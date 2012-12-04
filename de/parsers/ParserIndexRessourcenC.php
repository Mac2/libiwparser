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
use libIwParsers\de\parserResults\DTOParserIndexRessourcenResultC;
use libIwParsers\de\parserResults\DTOParserIndexRessourcenBevResultC;
use libIwParsers\de\parserResults\DTOParserIndexRessourcenRessResultC;

///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////

/**
 * Parser for Mainpage
 *
 * This parser is responsible for the Ressourcen section on the Mainpage
 *
 * Its identifier: de_index_ressourcen
 */
class ParserIndexRessourcenC extends ParserMsgBaseC implements ParserMsgI
{

    /////////////////////////////////////////////////////////////////////////////

    public function __construct()
    {
        parent::__construct();

        $this->setIdentifier('de_index_ressourcen');
        $this->setCanParseMsg('Ressen');
    }

    /////////////////////////////////////////////////////////////////////////////

    /**
     * @see ParserMsgI::parseMsg()
     */
    public function parseMsg(DTOParserResultC $parserResult)
    {
        $parserResult->objResultData = new DTOParserIndexRessourcenResultC();
        $retVal =& $parserResult->objResultData;

        $regExp = $this->getRegularExpression();
        $msg = $this->getMsg();

        $parserResult->strIdentifier = 'de_index_ressourcen';

        $aResult = array();

        $fRetVal = preg_match_all($regExp, $msg->strParserText, $aResult, PREG_SET_ORDER);

        if ($fRetVal !== false && $fRetVal > 0) {
            $parserResult->bSuccessfullyParsed = true;

            foreach ($aResult as $result) {

                $ress_name = PropertyValueC::ensureEnum($result['strRessName'], 'eResources' );
                if ($ress_name == "Bevölkerung") {
                    $retObj = new DTOParserIndexRessourcenBevResultC();
                    $retObj->strResourceName = $ress_name;
                    if (isset($result['Bevfrei'])) {
                        $retObj->iBevfrei = PropertyValueC::ensureInteger($result['Bevfrei']);
                    }
                    if (isset($result['Bevges'])) {
                        $retObj->iBevges = PropertyValueC::ensureInteger($result['Bevges']);
                    }
                    if (isset($result['Bevmax'])) {
                        $retObj->iBevmax = PropertyValueC::ensureInteger($result['Bevmax']);
                    }
                } else {
                    $retObj = new DTOParserIndexRessourcenRessResultC();
                    $retObj->strResourceName = $ress_name;
                    if (isset($result['vorrat'])) {
                        if ($ress_name == "Credits") {
                            $retObj->iResourceVorrat = (int)(PropertyValueC::ensureFloat($result['vorrat']));
                        } else {
                            $retObj->iResourceVorrat = PropertyValueC::ensureInteger($result['vorrat']);
                        }
                    }
                    if (isset($result['production'])) {
                        $retObj->fResourceProduction = PropertyValueC::ensureFloat($result['production']);
                    }
                }
                $retVal->aData[] = $retObj;
            }
        } else {
            $parserResult->bSuccessfullyParsed = false;
            $parserResult->aErrors[] = 'Unable to match the pattern.';
            $parserResult->aErrors[] = $msg->strParserText;
        }
    }

    /////////////////////////////////////////////////////////////////////////////


    /**
     */
    private function getRegularExpression()
    {

        $reRessNames = $this->getRegExpResource();
        $reRessProd = $this->getRegExpFloatingDouble();
        $reRessVorrat = $this->getRegExpDecimalNumber();

        $regExp = '/';
        $regExp .= '(?P<strRessName>' . $reRessNames . ')';
        $regExp .= '(?:\s' . $reRessNames . ')?\s';
        $regExp .= '(?:';
        $regExp .= '   \(' . $reRessVorrat . '\)\s+(?P<Bevfrei>' . $reRessVorrat . ')\s\/\s(?P<Bevges>' . $reRessVorrat . ')\s\/\s(?P<Bevmax>' . $reRessVorrat . ')\s\(frei\/gesamt\/max\)\s';
        $regExp .= ')?';
        $regExp .= '(?:\((?P<production>' . $reRessProd . ')\)\s+)?';
        $regExp .= '(?:\(Abbau\spro\sTag\s(?:\d{1,3}\.\d{1,3})(\\\%|\%)\)\s+)?'; //! Mac: hier explizt, da Punkt nicht abh. von den Accounteinstellungen
        $regExp .= '(?:(?P<vorrat>' . $reRessVorrat . '|' . $reRessProd . '))?';
        $regExp .= '/mxs';

        return $regExp;
    }

}