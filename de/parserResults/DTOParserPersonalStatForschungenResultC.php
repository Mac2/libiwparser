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

namespace libIwParsers\de\parserResults;

///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////

/**
 * Result DTO of parser de_personal_stat_forschungen
 */
class DTOParserPersonalStatForschungenResultC
{
    /**
     * @soap
     * @var array $aResearchs an array of objects of type
     *      DTOParserPersonalStatForschungenResearchResultC, which represent the researchs
     *      of the user
     */
    public $aResearchs = array();
}

/////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////

class DTOParserPersonalStatForschungenResearchResultC
{
    /**
     * @soap
     * @var string $strResearch the name of the research
     */
    public $strResearch = '';

    /**
     * @soap
     * @var int $iDateExpired the number of seconds since the user exists ingame
     */
    public $iDateExpired = 0;

    /**
     * @soap
     * @var int $iDateOfResearch the number of seconds since 1900
     */
    public $iDateOfResearch = 0;
}