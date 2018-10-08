#
# takes each raw GPX file in turn and ensures that all tracks in that file
# are combined, that only track data is retained, and ensures human readability.
#

SRCDIR="raw"
DESTDIR="daily"

for IFILE in `cd ${SRCDIR}; ls *.GPX` ; do

	/Applications/GPSBabelFE.app/Contents/MacOS/gpsbabel -t \
		-i gpx -f ${SRCDIR}/${IFILE} \
		-x track,title=${IFILE},pack \
		-x nuketypes,waypoints,routes \
		-o gpx -F ${DESTDIR}/${IFILE}


done	
echo create daily Done