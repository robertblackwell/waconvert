#
# takes each raw GPX file in turn and ensures that all tracks in that file
# are combined, that only track data is retained, and ensures human readability.
#

SRCFILE="./combined.gpx"
DESTFILE="./combined.kml"

/Applications/GPSBabelFE.app/Contents/MacOS/gpsbabel -t \
	-i gpx -f ${SRCFILE} \
	-x track,title=${SRCFILE},pack \
	-x nuketypes,waypoints,routes \
	-o kml -F ${DESTFILE}


echo create daily Done