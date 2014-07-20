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

/**
 * Entry point for extension, used by hooks
 *
 * This class generates the Memento Headers to be passed back to the client.
 */
class MementoHeaders {

	/**
	 * The ArticleViewHeader hook, used here to change the headers
	 * returned so MediaWiki can talk to Memento clients.
	 *
	 * @param Article $article: the article object
	 * @param boolean $outputDone: not used by this extension
	 * @param ParserCache $pcache: not used by this extension
	 *
	 * @return boolean true
	 */
	public function onArticleViewHeader( &$article, &$outputDone, &$pcache) {

		global $wgMementoTimeGateURLPrefix;
		global $wgEnableAPI;

		// avoid processing Mementos for nonexistent pages
		// if we're an article, do memento processing, otherwise don't worry
		// if we're a diff or edit page, Memento doesn't make sense
		if ( $article->getTitle()->isKnown() ) {
	
				$revision = $article->getRevisionFetched();
	
				// avoid processing Mementos for bad revisions,
				// let MediaWiki handle that case instead
				if ( is_object( $revision ) ) {
	
					$oldID = $article->getOldID();
	
					$out = $article->getContext()->getOutput();
					$request = $out->getRequest();
					$response = $request->response();
					$title = $article->getTitle();

					$originalURI = $title->getFullURL();

					$linkRelations = array();

					if ( $wgEnableAPI === true ) {

						$apiURI = wfExpandUrl( wfScript('api') );
						$apiRelation = "http://mementoweb.org/terms/api/mediawiki/";
						$linkRelations[] = "<$apiURI>; rel=\"$apiRelation\"";

						# TODO: find a cleaner way, if possible, than concatenation (wfExpandUrl)
						$tgURI = $wgMementoTimeGateURLPrefix . $originalURI;
						$linkRelations[] = "<$tgURI>; rel=\"timegate\"";

					}
	
					if ( $oldID != 0 ) {
						$mementoTimestamp = 
							$article->getRevisionFetched()->getTimestamp();
	
						$mementoDatetime = wfTimestamp( TS_RFC2822, $mementoTimestamp );
						$response->header( "Memento-Datetime:  $mementoDatetime", true );

						$firstRevision = $title->getFirstRevision();
						$firstdt = wfTimestamp( TS_RFC2822, $firstRevision->getTimestamp());
						$firsturi = $title->getFullURL( array( "oldid" => $firstRevision->getId() ) );

						$lastRevision = Revision::newFromTitle( $title );
						$lastdt = wfTimestamp( TS_RFC2822, $lastRevision->getTimestamp());
						$lasturi = $title->getFullURL( array( "oldid" => $lastRevision->getId() ) );

						$prevuri = $title->getFullURL( array( "oldid" => $title->getPreviousRevisionID() ) );
						$nexturi = $title->getFullURL( array( "oldid" => $title->getNextRevisionID() ) );

						$linkRelations[] = "<$originalURI>; rel=\"original\"";
						$linkRelations[] = "<$firsturi>; rel=\"first memento\"; datetime=\"$firstdt\"";; 
						$linkRelations[] = "<$lasturi>; rel=\"last memento\"; datetime=\"$lastdt\"";; 
						$linkRelations[] = "<$prevuri>; rel=\"prev\"";
						$linkRelations[] = "<$nexturi>; rel=\"next\"";
	
					}

					$linkValue = implode(', ', $linkRelations);
					$response->header( "Link: $linkValue", true );
				}
		}

		return true;
	}

}
