<?php
/**
 * This file is part of the Memento Headers Extension to MediaWiki
 * http://www.mediawiki.org/wiki/Extension:MementoHeaders
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

// Set up the extension
$wgExtensionCredits['other'][] = array(
	'path' => __FILE__,
	'name' => 'Memento Headers',
	'descriptionmsg' => 'mementoheaders-desc',
	'url' => 'https://www.mediawiki.org/wiki/Extension:MementoHeaders',
	'author' => '[http://www.mediawiki.org/wiki/User:Shawnmjones Shawn M. Jones]',
	'version' => '1.0.2'
);

// set up the messages file
$wgMessagesDirs['MementoHeaders'] = __DIR__ . '/i18n';
$wgExtensionMessagesFiles['MementoHeaders'] = __DIR__ . '/MementoHeaders.i18n.php';

// set up the core class
$wgAutoloadClasses['MementoHeaders'] = __DIR__ . '/MementoHeaders.body.php';

// set default value for settings
$wgMementoTimeGateURLPrefix = "http://timetravel.mementoweb.org/mediawiki/timegate/";
$wgMementoIncludeNamespaces = array( 0 );

// instantiate entry point
$wgMementoHeaders = new MementoHeaders();

// Set up the hooks for this class
$wgHooks['ArticleViewHeader'][] = $wgMementoHeaders;
