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
use libIwParsers\de\parserResults\DTOParserMsgResultC;
use libIwParsers\de\parserResults\DTOParserMsgResultMsgC;

///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////

/**
 * Parser for Messages
 *
 * This parser is responsible for parsing messages and selecting waste
 *
 * Its identifier: de_msg
 */
class ParserMsgC extends ParserBaseC implements ParserI
{

    /////////////////////////////////////////////////////////////////////////////

    public function __construct()
    {
        parent::__construct();

        $this->setIdentifier('de_msg');
        $this->setName('Nachrichtenzentrale');
//      $this->setRegExpCanParseText('/Nachrichtenzentrale.*(?:Nachrichtenzentrale.*Nachrichtenzentrale|Neue\sNachricht\sverfassen)/smU');		//! Mac: requires Ungreedy U Modifier because charsize could be larger than 500k!
        $this->setRegExpCanParseText('/' . $this->getRegularExpressionHeader() . '/mxU');
        $this->setRegExpBeginData('/(?:(Nachrichtenzentrale\s\-\sNachrichten[\s\n]+\b\w+\b\s+alle\sNachrichten\s.+ffnen\s\/\salle\sNachrichten\sschliessen\s\/\salle\sNachrichten\sselektieren\s\/\salle\sNachrichten\sdeselektieren\s+Seitenanzeige[\d\[\]\s\n]+)|(Nachrichtenzentrale\s\-\sNeue\sNachrichten[\s\n]neue\sNachrichten))/s');
        $this->setRegExpEndData('/Seitenanzeige[\d\[\]\s\n]+alle\sNachrichten\s.+ffnen.+alle\sNachrichten\sschliessen.+alle\sNachrichten\sselektieren.+alle\sNachrichten\sdeselektieren/s');
    }

    /////////////////////////////////////////////////////////////////////////////

    /**
     * @see  ParserI::parseText()
     * @todo Messages können vielleicht später an die Fabrik überreicht werden.
     */
    public function parseText(DTOParserResultC $parserResult)
    {

        $parserResult->objResultData = new DTOParserMsgResultC();
        $retVal =& $parserResult->objResultData;

        $this->stripTextToData();

        $regExp = $this->getRegularExpression();

        $aResult = array();
        $fRetVal = preg_match_all($regExp, $this->getText(), $aResult, PREG_SET_ORDER);

        if ($fRetVal !== false && $fRetVal > 0) {
            $parserResult->bSuccessfullyParsed = true;

            $retVal->iMessageCount = 0;

            foreach ($aResult as $result) {
                $retVal->iMessageCount++;

                if ($result['msgIsSystem'] == 'Systemnachricht' || $result['msgIsSystem'] == 'von:Systemnachricht') {
                    $bIsSystemNachricht = true;
                    $iMsgDateTime       = HelperC::convertDateTimeToTimestamp($result['msgSystemDateTime']);
                    $strMsgAuthor       = '@System';
                    $eParserType        = $result['msgSystemType'];
                } else {
                    $bIsSystemNachricht = false;
                    $iMsgDateTime       = HelperC::convertDateTimeToTimestamp($result['msgUserDateTime']);
                    $strMsgAuthor       = $result['msgAuthor'];
                    $eParserType        = $result['msgUserType'];
                }

                $strMsgTitle   = $result['msgTitle'];
                $strParserText = $result['msgText'];

                $msg = new DTOParserMsgResultMsgC;

                $msg->bIsSystemNachricht  = PropertyValueC::ensureBoolean($bIsSystemNachricht);
                $msg->strMsgTitle         = PropertyValueC::ensureString($strMsgTitle);
                $msg->strMsgAuthor        = PropertyValueC::ensureString($strMsgAuthor);
                $msg->eParserType         = PropertyValueC::ensureString($eParserType);
                $msg->strParserText       = PropertyValueC::ensureString($strParserText);
                $msg->iMsgDateTime        = PropertyValueC::ensureInteger($iMsgDateTime);
                $msg->bSuccessfullyParsed = true;

                switch ($msg->eParserType) {
                    case "Transport":
                        $parser = new ParserMsgTransportC;
                        $result = new DTOParserResultC ($parser);
                        if ($parser->canParseMsg($msg)) {
                            $parser->parseMsg($result);
                            $msg                      = $result->objResultData;
                            $retVal->aTransportMsgs[] = $msg;
//                          $retVal->aMsgs[] = & $retVal->aTransportMsgs[count($retVal->aTransportMsgs)-1];
                        }
                        break;
                    case "Massdriverpaket":
                        $msg->eParserType = "Transport";
                        $parser           = new ParserMsgTransportC;
                        $result           = new DTOParserResultC ($parser);
                        if ($parser->canParseMsg($msg)) {
                            $parser->parseMsg($result);
                            $msg                       = $result->objResultData;
                            $retVal->aMassdriverMsgs[] = $msg;
//                          $retVal->aMsgs[] = & $retVal->aTransportMsgs[count($retVal->aTransportMsgs)-1];
                        }
                        break;
                    case "Rückkehr":
                        $parser = new ParserMsgReverseC;
                        $result = new DTOParserResultC ($parser);
                        if ($parser->canParseMsg($msg)) {
                            $parser->parseMsg($result);
                            $msg                    = $result->objResultData;
                            $retVal->aReverseMsgs[] = $msg;
//                          $retVal->aMsgs[] = & $retVal->aReverseMsgs[count($retVal->aReverseMsgs)-1];
                        }
                        break;
                    case "Übergabe":
                        $parser = new ParserMsgGaveC;
                        $result = new DTOParserResultC ($parser);
                        if ($parser->canParseMsg($msg)) {
                            $parser->parseMsg($result);
                            $msg                 = $result->objResultData;
                            $retVal->aGaveMsgs[] = $msg;
//                          $retVal->aMsgs[] = & $retVal->aGaveMsgs[count($retVal->aGaveMsgs)-1];
                        }
                        break;
                    case "Sondierung (Schiffe/Def/Ress)":
                        if (strpos($msg->strMsgTitle, "Eigener Planet wurde sondiert") !== false
                            || strpos($msg->strMsgTitle, "Sondierung vereitelt") !== false
                        ) {
                            $parser = new ParserMsgSondierungC();
                            $parser->setMsg($msg);
                            $result = new DTOParserResultC ($parser);
                            $parser->parseMsg($result);
                            $msg                       = $result->objResultData;
                            $retVal->aSondierungMsgs[] = $msg;
                            if (!empty($msg->aErrors)) {
                                $retVal->aErrors[] = $msg->aErrors;
                            }

                        } //! Mac: da keine weitere Verarbeitung nötig ist, einfach hier die Koordinaten parsen und fertig
                        //! @todo: besser einen eigenen ParserMsgScanFailC Parser verwenden, damit alles konsistent ist (-> Overhead?)
                        else if (strpos($msg->strMsgTitle, "Sondierung fehlgeschlagen") !== false) {
                            $msg->strCoords = "";
                            $msg->aCoords   = array("gal" => 0, "sys" => 0, "planet" => 0);
                            if (preg_match('/Sondierung des Planeten (\d+):(\d+):(\d+)/', $msg->strParserText, $match) > 0) {
                                $msg->strCoords = $match[1] . ":" . $match[2] . ":" . $match[3];
                                $msg->aCoords   = array("gal" => $match[1], "sys" => $match[2], "planet" => $match[3]);
                            }
                            $retVal->aScanFailMsgs[] = $msg;
                        } else {
                            $parser = new ParserMsgScanSchiffeDefRessC();
                            $result = new DTOParserResultC ($parser);
                            if ($parser->canParseMsg($msg)) {
                                $parser->parseMsg($result);
                                $msg                               = $result->objResultData;
                                $retVal->aScanSchiffeDefRessMsgs[] = $msg;
//                              $retVal->aMsgs[] = & $retVal->aScanSchiffeDefRessMsgs[count($retVal->aScanSchiffeDefRessMsgs)-1];
                                if (!empty($msg->aErrors)) {
                                    $retVal->aErrors[] = $msg->aErrors;
                                }
                            }
                        }
                        break;
                    case "Sondierung (Gebäude/Ress)":
                        if (strpos($msg->strMsgTitle, "Eigener Planet wurde sondiert") !== false
                            || strpos($msg->strMsgTitle, "Sondierung vereitelt") !== false
                        ) {
                            $parser = new ParserMsgSondierungC();
                            $parser->setMsg($msg);
                            $result = new DTOParserResultC ($parser);
                            $parser->parseMsg($result);
                            $msg                       = $result->objResultData;
                            $retVal->aSondierungMsgs[] = $msg;
                            if (!empty($msg->aErrors)) {
                                $retVal->aErrors[] = $msg->aErrors;
                            }
                        } else if (strpos($msg->strMsgTitle, "Sondierung fehlgeschlagen") !== false) {
                            $msg->strCoords = "";
                            $msg->aCoords   = array("gal" => 0, "sys" => 0, "planet" => 0);
                            if (preg_match('/Sondierung des Planeten (\d+):(\d+):(\d+)/', $msg->strParserText, $match) > 0) {
                                $msg->strCoords = $match[1] . ":" . $match[2] . ":" . $match[3];
                                $msg->aCoords   = array("gal" => $match[1], "sys" => $match[2], "planet" => $match[3]);
                            }
                            $retVal->aScanFailMsgs[] = $msg;
                        } else {
                            $parser = new ParserMsgScanGebRessC();
                            $result = new DTOParserResultC ($parser);
                            if ($parser->canParseMsg($msg)) {
                                $parser->parseMsg($result);
                                $msg                        = $result->objResultData;
                                $retVal->aScanGebRessMsgs[] = $msg;
                                $retVal->aErrors[]          = $msg->aErrors;
                            }
                        }
                        break;
                    case "Sondierung (Geologie)":
                        if (strpos($msg->strMsgTitle, "Eigener Planet wurde sondiert") !== false
                            || strpos($msg->strMsgTitle, "Sondierung vereitelt") !== false
                        ) {
                            $parser = new ParserMsgSondierungC();
                            $parser->setMsg($msg);
                            $result = new DTOParserResultC ($parser);
                            $parser->parseMsg($result);
                            $msg                       = $result->objResultData;
                            $retVal->aSondierungMsgs[] = $msg;
                            if (!empty($msg->aErrors)) {
                                $retVal->aErrors[] = $msg->aErrors;
                            }
                        } else if (strpos($msg->strMsgTitle, "Sondierung fehlgeschlagen") !== false) {
                            $msg->strCoords = "";
                            $msg->aCoords   = array("gal" => 0, "sys" => 0, "planet" => 0);
                            if (preg_match('/Sondierung des Planeten (\d+):(\d+):(\d+)/', $msg->strParserText, $match) > 0) {
                                $msg->strCoords = $match[1] . ":" . $match[2] . ":" . $match[3];
                                $msg->aCoords   = array("gal" => $match[1], "sys" => $match[2], "planet" => $match[3]);
                            }
                            $retVal->aScanFailMsgs[] = $msg;
                        } else {
                            //! Mac: Werden ueber die vorhandenen Links im ParserXMLC ausgewertet
                            $retVal->iMessageCount--;
                            //            $parser = new ParserMsgGeoscansC;
                            //            $result = new DTOParserResultC ($parser);
                            //            if ($parser->canParseMsg($msg))
                            //            {
                            //              $parser->parseMsg ($result);
                            //              $msg = $result->objResultData;
                            //              $retVal->aScanGeoMsgs[] = $msg;
                            //               $retVal->aMsgs[] = & $retVal->aScanGeoMsgs[count($retVal->aScanGeoMsgs)-1];
                            //            }
                        }
                        break;
                    case "Banküberweisung":
                        // @todo!
                        $retVal->aMsgs[] = $msg;
                        break;
                    case "Angriff":
                        // @todo!
                        $retVal->aMsgs[] = $msg;
                        break;
                    case "Ressourcen abholen":
                        $parser = new ParserMsgTransfairC;
                        $result = new DTOParserResultC ($parser);
                        if ($parser->canParseMsg($msg)) {
                            $parser->parseMsg($result);
                            $msg                      = $result->objResultData;
                            $retVal->aTransfairMsgs[] = $msg;
//                          $retVal->aMsgs[] = & $retVal->aTransfairMsgs[count($retVal->aTransfairMsgs)-1];
                        }
                        break;
                    case "Ressourcenhandel":
                        // @todo!
                        $retVal->aMsgs[] = $msg;
                        break;
                    case "Stationieren":
                        // @todo!
                        $retVal->aMsgs[] = $msg;
                        break;
                    default:
                        $retVal->aMsgs[] = $msg;
                        break;
                }
            }
        } else {
            $parserResult->bSuccessfullyParsed = false;
            $parserResult->aErrors[]           = 'Unable to match the Msg pattern.';
        }

    }

    /////////////////////////////////////////////////////////////////////////////

    private function getRegularExpressionHeader()
    {
        $reTitle       = $this->getRegExpSingleLineText();
        $reAuthor      = $this->getRegExpLowUserName();
        $reDateTime    = $this->getRegExpDateTime();
        $reShipActions = $this->getRegExpShipActions();

        $reHeader = '(?:';
        $reHeader .= '(\t\b)' . $reTitle;
        $reHeader .= '\s+';
        $reHeader .= '(?:' . $reAuthor;
        $reHeader .= '\s+';
        $reHeader .= $reDateTime;
        $reHeader .= '[\s\n]+';
        $reHeader .= '(?:Spielernachricht|Outbox)';
        $reHeader .= '[\t\s]+';
        $reHeader .= 'Antworten[\s|\t]+Petzen\s\-\sDem\sAdmin\smelden';
        $reHeader .= '[\s\n]+';
        $reHeader .= '|';
        $reHeader .= '(?:Systemnachricht|von:Systemnachricht)';
        $reHeader .= '\s+';
        $reHeader .= $reDateTime;
        $reHeader .= '[\s\n]+';
        $reHeader .= $reShipActions;
        $reHeader .= '[\s\n]+';
        $reHeader .= ')';
        $reHeader .= ')';

        return $reHeader;
    }

    /**
     * @todo msgText: überprüfung ob eine andere Möglichkeit als [^\t] besteht, da dies den IE ausschließt.
     */
    private function getRegularExpression()
    {
        /**
         * die Daten sind Blöcke, Spielernachrichten sind an dem Petzten zu erkennen,
         * Die variablen Textinhalte werden ueber den darauffolgenden Header begrenzt (bzw. das Ende des EingabeStrings im letzten Fall)
         * TODO: Zeilenumbrüche von \n erweitern fuer IE/Opera
         */

        $reTitle       = $this->getRegExpSingleLineText();
        $reAuthor      = $this->getRegExpLowUserName();
        $reDateTime    = $this->getRegExpDateTime();
        $reShipActions = $this->getRegExpShipActions();
        $reLine        = $this->getRegExpSingleLineText();

        $reHeader = $this->getRegularExpressionHeader();

        //Just even don't think to ask anything about this regexp, fu!
        $regExp = '/';
        $regExp .= '(?:\b(?P<msgTitle>' . $reTitle . ')';
        $regExp .= '\s+';
        $regExp .= '(?:(?P<msgAuthor>' . $reAuthor . ')';
        $regExp .= '\s+';
        $regExp .= '(?P<msgUserDateTime>' . $reDateTime . ')';
        $regExp .= '[\s\n]+';
        $regExp .= '(?P<msgUserType>Spielernachricht|Outbox)';
        $regExp .= '[\t\s]+';
        $regExp .= '(?:Antworten[\s|\t]+Petzen\s\-\sDem\sAdmin\smelden)';
        $regExp .= '[\s\n]+';
        $regExp .= '|';
        $regExp .= '(?P<msgIsSystem>Systemnachricht|von:Systemnachricht)';
        $regExp .= '\s+';
        $regExp .= '(?P<msgSystemDateTime>' . $reDateTime . ')';
        $regExp .= '[\s\n]+';
        $regExp .= '(?P<msgSystemType>' . $reShipActions . ')';
        $regExp .= '[\s\n]+';
        $regExp .= ')';
        $regExp .= ')';
        $regExp .= '(?:(?P<msgText>(' . $reLine . '(?:\n+|\Z))+)';
        $regExp .= '(?=' . $reHeader . '|\Z)';
        $regExp .= ')';
        $regExp .= '/mxU';

        return $regExp;
    }

}