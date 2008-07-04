<?php
//
// Definition of classname class
//
// Created on: <Jul 2, 2008 2008 12:06:11 PM nfrp>
//
// ## BEGIN COPYRIGHT, LICENSE AND WARRANTY NOTICE ##
// SOFTWARE NAME: eZ publish
// SOFTWARE RELEASE: 3.10.x
// COPYRIGHT NOTICE: Copyright (C) 1999-2006 eZ systems AS
// SOFTWARE LICENSE: GNU General Public License v2.0
// NOTICE: >
//   This program is free software; you can redistribute it and/or
//   modify it under the terms of version 2.0  of the GNU General
//   Public License as published by the Free Software Foundation.
//
//   This program is distributed in the hope that it will be useful,
//   but WITHOUT ANY WARRANTY; without even the implied warranty of
//   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//   GNU General Public License for more details.
//
//   You should have received a copy of version 2.0 of the GNU General
//   Public License along with this program; if not, write to the Free
//   Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
//   MA 02110-1301, USA.
//
//
// ## END COPYRIGHT, LICENSE AND WARRANTY NOTICE ##
//

$http = eZHTTPTool::instance();
// for now only JSon is supported
// @TODO : add other reponse type handler :
//         * XML
//         * flat file
$outputType = ( $Params['OutputType'] != '' ) ? trim( $Params['OutputType'] ) : 'json' ;
$classID = ( is_numeric( $Params['ClassID'] ) ) ? trim( $Params['ClassID'] ) : false ;

$datatypeIni = eZINI::instance( 'datatype.ini' );
$maxAutocompleteResults = $datatypeIni->hasVariable( 'eZKeywordSettings', 'MaxAutocompleteResults' ) ? $datatypeIni->variable( 'eZKeywordSettings', 'MaxAutocompleteResults' ) : 20 ;

// YUI Autocomplete sends the typed-in text as GET param, named 'query' :
$autocompleteString = $http->hasGetVariable( 'query' ) ? trim( $http->getVariable( 'query' ) ) : '' ;

// @TODO : implement some cache system in order to alleviate the fetch overload in case of large keyword databases
$db = eZDB::instance();
$query = "SELECT DISTINCT( ezkeyword.keyword ) FROM ezkeyword WHERE keyword LIKE '" . $db->escapeString( $autocompleteString ) . '%' . "' ";

if ( $classID )
{
    $query .= ' AND class_id=' . $classID . ' ';
}

$query .= "LIMIT $maxAutocompleteResults";
$wordArray = $db->arrayQuery( $query );

// build the response array
$reponseArray = array();
foreach ( $wordArray as $resultItem )
{
    $reponseArray[] = array( 'Keyword' => $resultItem['keyword'] );
}
$resultArray = array( 'ResultSet' => array( 'Result' => $reponseArray ) );
                    
// @WARNING : PHP5 only. 
echo json_encode( $resultArray );
eZExecution::cleanExit();

?>