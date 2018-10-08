#
# combines all GPX files into a single file to represent the whole trip
# some reduction in size may be implemented using the -x error, 0.1 option
#

NAME=Track
OTYP=kml,track=1,points=0,trackdata=0
OTYP=gpx

OUTFILE="combined.gpx"
XX=""
for IFILE in `ls raw/*.GPX` ; do
XX="${XX} -f ${IFILE}"
done	

/Applications/GPSBabelFE.app/Contents/MacOS/gpsbabel -t \
	-i gpx  ${XX} \
	-x track,title=${NAME},pack \
	-x nuketypes,waypoints,routes \
	-x simplify,crosstrack,error=.02k \
	-o ${OTYP} -F ${OUTFILE}