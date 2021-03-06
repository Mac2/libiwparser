<?php
/*
 * ----------------------------------------------------------------------------
 * "THE BEER-WARE LICENSE" (Revision 42):
 * <benjamin.woester@googlemail.com> wrote this file. As long as you retain
 * this notice you can do whatever you want with this stuff. If we meet some
 * day, and you think this stuff is worth it, you can buy me a beer in return.
 * Benjamin Wöster
 * ----------------------------------------------------------------------------
 */
/**
 * @author     Benjamin Wöster <benjamin.woester@googlemail.com>
 * @author     Mac <MacXY@herr-der-mails.de>
 * @package    libIwParsers
 * @subpackage parsers
 */

///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////

/**
 * Base class for parsers
 *
 * This class provides attributes and methods, needed by many concrete
 * parsers. By collecting them in this base class, we reduce redundant
 * code.
 */
class ParserBaseC extends ParserFunctionC
{
    const ALL_WHITESPACE      = 'ALL_WHITESPACE';
    const LEADING_WHITESPACE  = 'LEADING_WHITESPACE';
    const TRAILING_WHITESPACE = 'TRAILING_WHITESPACE';

    /**
     * @var string identifier of the concrete parser
     */
    private $_strIdentifier = '';

    /**
     * @var string identifier of the concrete parser
     */
    private $_strName = '';

    /**
     * @var string regExp, checking if the concrete parser is able to parse the
     *             text provided.
     */
    private $_reCanParseText = '';

    /**
     * @var string regExp, finding the begin of the data section
     */
    private $_reBeginData = '';

    /**
     * @var string regExp, finding the end of the data section
     */
    private $_reEndData = '';

    /**
     * @var string text to be parsed
     */
    private $_strText = '';

    /////////////////////////////////////////////////////////////////////////////

    /**
     * protected constructor
     *
     * As this class isn't meant to do anything on its own,
     * we protect it from beeing instantiated.
     *
     * Only deriving classes shall be able to instantiate it.
     */
    protected function __construct()
    {
    }

    /////////////////////////////////////////////////////////////////////////////

    /**
     * Strips useless boiler plate text
     *
     * Many pages of IceWars contain data, that is simply of no use for us.
     * By using _reBeginData and _reEndData regular expressions, we can strip
     * down the text to the data sections.
     */
    protected function stripTextToData()
    {
        if ($this->getText() === '') {
            return;
        }

        $reStartData = $this->getRegExpStartData();
        $reEndData   = $this->getRegExpEndData();

        $text = '';
        if (!empty($reStartData)) {
            $aStart = preg_split($reStartData, $this->getText());

            for ($n = 1; $n < count($aStart); $n++) { //! bei Mehrfach-Berichten (Universum, Highscore, etc) alle verarbeiten
                if (isset($aStart[$n])) {
                    if (!empty($reEndData)) {
                        $aEnd = preg_split($reEndData, $aStart[$n]); //! jeden Bericht einzeln auf ein Ende prüfen

                        if (isset($aEnd[0])) {
                            $text .= trim($aEnd[0]);
                        }
                    } else {
                        $text .= $aStart[$n];
                    }
                }
            }

            $this->setText($text);
        }

        return;
    }

    /////////////////////////////////////////////////////////////////////////////

    /**
     * @see ParserI::canParseText()
     */
    public function canParseText($text)
    {
        $retVal = preg_match($this->getRegExpCanParseText(), $text, $aMatches);

        switch ($retVal) {
            case 0:
                return false;
                break;
            case $retVal > 0:
                $this->setText($text);

                return true;
                break;
            case false:
                $e = get_class($this) . '::canParseText - ERROR in preg_match...';
                throw new Exception($e);
                break;
        }
    }

    /////////////////////////////////////////////////////////////////////////////

    /**
     * @see ParserI::getIdentifier()
     */
    public function getIdentifier()
    {
        return $this->_strIdentifier;
    }

    /////////////////////////////////////////////////////////////////////////////

    /**
     * @see ParserI::getName()
     */
    public function getName()
    {
        return $this->_strName;
    }

    /////////////////////////////////////////////////////////////////////////////

    protected function getRegExpCanParseText()
    {
        return $this->_reCanParseText;
    }

    /////////////////////////////////////////////////////////////////////////////

    protected function getRegExpStartData()
    {
        return $this->_reBeginData;
    }

    /////////////////////////////////////////////////////////////////////////////

    protected function getRegExpEndData()
    {
        return $this->_reEndData;
    }

    /////////////////////////////////////////////////////////////////////////////

    protected function getText()
    {
        return $this->_strText;
    }

    /////////////////////////////////////////////////////////////////////////////

    protected function setIdentifier($value)
    {
        $this->_strIdentifier = PropertyValueC::ensureString($value);
    }

    /////////////////////////////////////////////////////////////////////////////

    protected function setName($value)
    {
        $this->_strName = PropertyValueC::ensureString($value);
    }

    /////////////////////////////////////////////////////////////////////////////

    protected function setRegExpCanParseText($value)
    {
        $this->_reCanParseText = PropertyValueC::ensureString($value);
    }

    /////////////////////////////////////////////////////////////////////////////

    protected function setRegExpBeginData($value)
    {
        $this->_reBeginData = PropertyValueC::ensureString($value);
    }

    /////////////////////////////////////////////////////////////////////////////

    protected function setRegExpEndData($value)
    {
        $this->_reEndData = PropertyValueC::ensureString($value);
    }

    /////////////////////////////////////////////////////////////////////////////

    /**
     * @todo I'm not sure about all these utf8 replacements,
     *       can you modify them to use utf8_encode/utf8_decode?
     *       Because eclipse/svn/dontknowwhat mixes them up all
     *       the time, and I don't want to break your tool!
     */
    public function setText($value)
    {
        //replace different line endings by \n
        $value = $this->cleanupLineEndings($value);

        $replacements = array(
            'blubbernde Gallertmasse'            => 'Bevölkerung',
            'Erdbeermarmelade'                   => 'Stahl',
            'Erdbeerkonfitüre'                   => 'VV4A',
            'Erdbeeren'                          => 'Eisen',
            'Brause'                             => 'chem. Elemente',
            'Vanilleeis'                         => 'Eis',
            'Eismatsch'                          => 'Wasser',
            'Traubenzucker'                      => 'Energie',
            ' Kekse '                            => ' Credits ',
            //! Mac: Workaround um nicht Teile eines Wortes/Gebäudenamens zu ersetzen

            'Systrans (Systransporter Klasse 1)' => 'Systrans (Systemtransporter Klasse 1)',
            'Lurch (Systransporter Klasse 2)'    => 'Lurch (Systemtransporter Klasse 2)',
            'Crux (Systransporter Kolonisten)'   => 'Crux (Systemtransporter Kolonisten)',
            'Pflaumenmus (kleiner Carrier)'      => 'Pflaumenmus (Carrier)',
            //! Mac: nötig, da Forschung anders heißt als das Schiff

            '\\\%'                               => '\%',

        );

        $value = str_replace(
            array_keys($replacements),
            array_values($replacements),
            $value
        );

        //undo magic quotes
        if (ini_get("magic_quotes_gpc") == 1) {
            $value = stripslashes($value);
        }

        //set text
        $this->_strText = PropertyValueC::ensureString($value);
    }

    /////////////////////////////////////////////////////////////////////////////

    protected function cleanupLineEndings($text)
    {
        //replace different line endings by \n (linux)
        $replacements = array(
            chr(13) . chr(10) => chr(10), //windows
            chr(13)           => chr(10), //mac
        );

        return str_replace(array_keys($replacements), array_values($replacements), $text);
    }

    /////////////////////////////////////////////////////////////////////////////

    /**
     * Strips whitespaces from lines.
     *
     * @param string $text   the text whos lines shall be striped
     * @param string $option one of ParserBaseC::XYZ_WHITESPACE constants
     *
     * @return string the striped text
     */
    protected function stripWhitespace($text, $option = ParserBaseC::ALL_WHITESPACE)
    {
        switch ($option) {
            case ParserBaseC::LEADING_WHITESPACE:
                $retVal = rtrim($text);
                break;
            case ParserBaseC::TRAILING_WHITESPACE:
                $retVal = ltrim($text);
                break;
            case ParserBaseC::ALL_WHITESPACE:
            default:
                $retVal = trim($text);
        }

        return $retVal;
    }

}