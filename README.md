The idea of the Memento Headers extension is it to make it as straightforward to access articles of the past as it is to access their current version.  **This extension just provides the Memento headers for MediaWiki** so that Memento clients browse old versions of pages just like they do in web archives.  This extension requires an external TimeGate server be set up to find these old versions, but by default uses the one provided by the Memento project.

If you want an extension that does everything for Memento internally, along with some extra features, try the full Memento Extension at https://www.mediawiki.org/wiki/Extension:Memento.

The Memento framework allows you to see versions of articles as they existed at some date in the past. All you need to do is enter a URL of an article in your browser and specify the desired date in a browser plug-in. This way you can browse the Web of the past. What the Memento Headers extension will present to you is a version of the article as it existed on or very close to the selected date. Obviously, this will only work if previous (archived) versions are available on the Web. Fortunately, MediaWiki is a Content Management System which implies that it maintains all revisions made to an article. This extension leverages this archiving functionality and provides native Memento support for MediaWiki.

This package contains the source code and build script for the Memento Headers MediaWiki Extension.

# Directory Contents

* Makefile - the build script for creating release packages
* README.md - this file
* MementoHeaders/ - the source code for this extension
* scripts/ - support scripts for coding standards verification

# Installation

To install this package within MediaWiki perform the following:
* copy the MementoHeaders directory into the extensions directory of your MediaWiki installation
* add the following to the LocalSettings.php file in your MediaWiki installation:
```
    require_once("$IP/extensions/Memento/Memento.php");
```

# Configuration

This extension has sensible defaults, but allows the following settings to be added to LocalSettings.php in order to alter its behavior:

* $wgMementoIncludeNamespaces - is an array of Mediawiki Namespace IDs (e.g. the integer values for Talk, Template, etc.) to include for Mementofication, default is an array containing just 0 (Main); the list of Mediawiki Namespace IDs is at http://www.mediawiki.org/wiki/Manual:Namespace

* $wgMementoTimeGateURLPrefix - the first part of the TimeGate URL, to which the extension will direct Memento users for datetime negotiation; the default is the Memento-supported TimeGate at http://mementoweb.org/proxy/mediawiki/timegate/

# Packaging

To package the Memento Headers MediaWiki Extension, type the following from this directory:
```
  make
```

# Code compliance verification

Running the code compliance requires phpcs.

This git repository uses and external repository for coding convention rules, so we can update the coding convention rules at any time.  The git command for performing the initial import is:

```
    git submodule update --init
```

To see if the code complies with Mediawiki's coding conventions, run:

```
    make verify
```

To force coding standards for simple-to-fix issues, you will need to have Python and the requests library installed, but you can type:
```
  make forcecs
```
