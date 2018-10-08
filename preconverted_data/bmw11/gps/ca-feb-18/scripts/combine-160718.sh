#
#
# should be run with command ./scripts/combine-160718.sh
#
# from this folders parent folder
# 

NAME=160718
OTYP=kml,track=1,points=0,trackdata=0
OTYP=gpx

OUTFILE="raw/160718combined.GPX"
XX=""
for IFILE in `ls 160718/*.GPX` ; do
XX="${XX} -f ${IFILE}"
done	

/Applications/GPSBabelFE.app/Contents/MacOS/gpsbabel -t \
	-i gpx  ${XX} \
	-x track,title=${NAME},pack \
	-x nuketypes,waypoints,routes \
	-o ${OTYP} -F ${OUTFILE}