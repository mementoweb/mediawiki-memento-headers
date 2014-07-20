<?php
/**
 * This file is part of the Memento Extension to MediaWiki
 * http://www.mediawiki.org/wiki/Extension:Memento
 *
 * @section LICENSE
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
 * http://www.gnu.org/copyleft/gpl.html
 *
 * @file
 */

/**
 * Ensure that this file is only executed in the right context.
 */
if ( ! defined( 'MEDIAWIKI' ) ) {
	echo "Not a valid entry point";
	exit( 1 );
}

$wgExtensionMessagesFiles['MementoHeaders'] = __DIR__ . '/MementoHeaders.i18n.php';

// Set up the extension
$wgExtensionCredits['other'][] = array(
	'path' => __FILE__,
	'name' => 'Memento Headers',
	'description' => 'extension-overview',
	'url' => 'https://www.mediawiki.org/wiki/Extension:MementoHeaders',
	'author' => array(
		'Harihar Shankar',
		'Herbert Van de Sompel',
		'Robert Sanderson',
		'Shawn M. Jones'
	),
	'version' => '0.1-SNAPSHOT'
);

$wgAutoloadClasses['MementoHeaders'] = __DIR__ . '/MementoHeaders.body.php';

$wgMementoTimeGateURLPrefix = "http://mementoweb.org/wiki/timegate/";

$wgMementoHeaders = new MementoHeaders();

// Set up the hooks for this class
$wgHooks['ArticleViewHeader'][] = $wgMementoHeaders;
