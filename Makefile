# This file is part of the Memento Extension to MediaWiki
# http://www.mediawiki.org/wiki/Extension:Memento
#
# LICENSE
# This program is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 2 of the License, or
# (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License along
# with this program; if not, write to the Free Software Foundation, Inc.,
# 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
# http://www.gnu.org/copyleft/gpl.html
# 
# Makefile

# Settings
SOURCEDIR=MementoHeaders
BUILDDIR=build
PACKAGEDIR=${SOURCEDIR}

# the resulting packaged binary
BINFILE=MementoHeaders.zip

# commands and variables used for deployment/undeployment
ZIPCMD=zip -r
CP=cp
RM=rm
CHMOD=chmod

.PHONY: clean
.PHONY: verify

# default target
all: package
	@echo "Done with build"

${BUILDDIR}:
	@echo ""
	@echo "#########################"
	@echo "Preparing build directory '${BUILDDIR}'"
	@mkdir -p "${BUILDDIR}"
	@echo "#########################"
	@echo ""

# create ZIP file used for release
package: ${BUILDDIR}
	@echo ""
	@echo "#########################"
	@echo "Creating package"
	@${CP} -R ${SOURCEDIR} ${BUILDDIR}/${PACKAGEDIR}
	@find ${BUILDDIR}/${PACKAGEDIR} -type d -exec chmod 0755 {} \;
	@find ${BUILDDIR}/${PACKAGEDIR} -type f -exec chmod 0644 {} \;
	@cd ${BUILDDIR}; ${ZIPCMD} ${BINFILE} ${PACKAGEDIR}
	@cd ${BUILDDIR}; ${RM} -rf ${PACKAGEDIR}
	@echo "#########################"
	@echo "ZIP file is available at ${BUILDDIR}/${BINFILE}"
	@echo "#########################"
	@echo ""

clean:
	@echo ""
	@echo "#########################"
	@echo "Cleaning up"
	-${RM} -rf ${BUILDDIR}
	@echo "Done cleaning..."
	@echo "#########################"
	@echo ""


