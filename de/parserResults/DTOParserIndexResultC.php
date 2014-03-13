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
 * Result DTO of parser de_index
 */
class DTOParserIndexResultC
{
    /**
     * @soap
     * @var array $aContainer
     */
    public $aContainer = array();

    /**
     * @soap
     * @var integer $iUnreadMsg
     */
    public $iUnreadMsg = 0;

    /**
     * @soap
     * @var integer $iUnreadAllyMsg
     */
    public $iUnreadAllyMsg = 0;

    /**
     * @soap
     * @var bool $bOngoingResearch
     */
    public $bOngoingResearch = false;
}

class DTOParserIndexResultIndexC
{
    /**
     * @soap
     * @var string $eParserType the type of the index container
     */
    public $eParserType = '';

    /**
     * @soap
     * @var string $strParserText the text of the index container
     */
    public $strParserText = '';
}