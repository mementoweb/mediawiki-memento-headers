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
	public function onArticleViewHeader( &$article, &$outputDone, &$pcache ) {

		global $wgMementoTimeGateURLPrefix;
		global $wgMementoExcludeNamespaces;
		global $wgEnableAPI;

		// if we're in the list of excluded namespaces, bail out and do nothing
		if (  in_array( $article->getTitle()->getNamespace(), $wgMementoExcludeNamespaces ) ) {
			return true;
		}

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

					/* only provide TimeGate information if the Wiki has the
					 * API enabled, otherwise we tell the Memento client about
					 * a TimeGate that can't actual perform datetime negotiation
					 */
					if ( $wgEnableAPI === true ) {

						$apiURI = wfExpandUrl( wfScript( 'api' ) );
						$apiRelation = "http://mementoweb.org/terms/api/mediawiki/";
						$linkRelations[] = "<$apiURI>; rel=\"$apiRelation\"";

						/* TODO: find a cleaner way, if possible, than 
						 * concatenation to produce the TimeGate URI
						 *
						 * Concern: HTML Injection
						 * @see http://www.owasp.org/index.php/HTML_Injection
						 * 
						 * Analysis:
						 * The input for $wgMementoTimeGateURLPrefix can come
						 * from the LocalSettings.php file and the input for
						 * $originalURI comes from the $title->getFullURIL()
						 * function call above. This limits the attack vector
						 * to the LocalSettings.php file, as it is the only
						 * place where one of these two variables can be altered
						 * to produce a malicious result.
						 *
						 * This result is also used as an entry to the Link
						 * header and not as input to HTML, so there is no 
						 * possibility of HTML injection.  The worst-case
						 * scenario is that the admin injects a bad URI
						 * into the LocalSettings.php file for a Memento client
						 * to follow.
						 *
						 * For some level of input validation, in case of typing
						 * mistakes, we use filter_var.
						 */
						if ( filter_var( $wgMementoTimeGateURLPrefix, FILTER_VALIDATE_URL ) ) {
							$tgURI = $wgMementoTimeGateURLPrefix . $originalURI;
							$linkRelations[] = "<$tgURI>; rel=\"timegate\"";
						}

					}

					if ( $oldID != 0 ) {

						$linkRelations[] = "<$originalURI>; rel=\"original\"";

						$thisRevision = $article->getRevisionFetched();

						/* 
						 * I'm not sure when this would occur, seeing as
						 * we're wrapped in several if statements to
						 * prevent a bad title object from being loaded, 
						 * but just in case...
						 */
						if ( $thisRevision == null ) {
							throw new ErrorPageError( 'mementoheaders', 'bad-current-revision', array() );
						}

						$firstRevision = $title->getFirstRevision();
						$lastRevision = Revision::newFromTitle( $title );

						/* 
						 * I'm not sure when this would occur, seeing as
						 * we're wrapped in several if statements to
						 * prevent a bad title object from being loaded, 
						 * but just in case...
						 */
						if ( $firstRevison != null ) {
							$firstID = $firstRevision->getID();
							
							/* don't bother making headers if firstID is null;
							 * something is wrong in that case, but we didn't cause it
							 * so no need to put our extension's name on the error page
							 */
							if ( $firstID != null ) {
								$firstdt = wfTimestamp( TS_RFC2822, $firstRevision->getTimestamp() );
							
								if ( $firstdt != false ) {
									$firsturi = $title->getFullURL( array( "oldid" => $firstID ) );
									$linkRelations[] = "<$firsturi>; rel=\"first memento\"; datetime=\"$firstdt\"";
								}
							}
						}

						if ( $lastRevision != null ) {
							$lastID = $lastRevision->getID();
						
							/* don't bother making headers if lastID is null
							 * something is wrong in that case, but we didn't cause it
							 * so no need to put our extension's name on the error page
							*/
							if ( $lastID != null ) {
								$lastdt = wfTimestamp( TS_RFC2822, $lastRevision->getTimestamp() );
						
								if ( $lastdt != false ) {
									$lasturi = $title->getFullURL( array( "oldid" => $lastID ) );
									$linkRelations[] = "<$lasturi>; rel=\"last memento\"; datetime=\"$lastdt\"";
		        				}
						    }
						}

						$prevRevID = $title->getPreviousRevisionID( $oldID );
						$nextRevID = $title->getNextRevisionID( $oldID );

						/*
						 * $prevRevID == null when we are on the first revision,
						 * because there is no previous one
						 */
						if ( $prevRevID != null ) {
							$prevuri = $title->getFullURL( array( "oldid" => $prevRevID ) );
							$linkRelations[] = "<$prevuri>; rel=\"prev\"";
						}

						/*
						 * $nextRevID == null when we are on the last revision,
						 * because there is no next one
						 */
						if ( $nextRevID != null ) {
							$nexturi = $title->getFullURL( array( "oldid" => $nextRevID ) );
							$linkRelations[] = "<$nexturi>; rel=\"next\"";
						}

						$mementoTimestamp = $thisRevision->getTimestamp();
						$mementoDatetime = wfTimestamp( TS_RFC2822, $mementoTimestamp );
						$response->header( "Memento-Datetime:  $mementoDatetime", true );
					}

					$linkValue = implode( ', ', $linkRelations );
					$response->header( "Link: $linkValue", true );
				}
		}

		return true;
	}

}
