# --- T2-COPYRIGHT-NOTE-BEGIN ---
# This copyright note is auto-generated by ./scripts/Create-CopyPatch.
# 
# T2 SDE: target/generic/build.sh
# Copyright (C) 2004 - 2005 The T2 SDE Project
# 
# More information can be found in the files COPYING and README.
# 
# This program is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; version 2 of the License. A copy of the
# GNU General Public License can be found in the file COPYING.
# --- T2-COPYRIGHT-NOTE-END ---

# This is the shortest possible target build.sh script. Some targets will
# add code after calling pkgloop() or modify pkgloop's behavior by defining
# a new pkgloop_action() function.

pkgloop

echo_header "Packaging build."

if [ "$SDECFG_IMAGE" -a -e target/share/$SDECFG_IMAGE/build.sh ]; then
	echo_status "Creating output image ..."
	. target/share/$SDECFG_IMAGE/build.sh
fi

