#
# Fix the track for the 18th. The data was lost on the GPS so I used basecamp to construct a track from a route.
# This provided a track with no time value.
# The command below adds the time back in
#
BABEL=/Applications/GPSBabelFE.app/Contents/MacOS/gpsbabel
IN_FILE=no-time-2018-07-18.GPX
START_TIME=201807060000
FAKETIME=f20180718060000+01
POINT_INTERVAL=01
OUT_FILE=out.GPX
# gpsbabel -i gpx -f no-time-2018-07-18.GPX -x track,faketime=f20180718060000+01 -o gpx -F out.GPX
# /Applications/GPSBabelFE.app/Contents/MacOS/gpsbabel -i gpx -f ${IN_FILE} -x track,faketime=f${START_TIME}+${POINT_INTERVAL} -o gpx -F ${OUT_FILE}
/Applications/GPSBabelFE.app/Contents/MacOS/gpsbabel -i gpx -f ${IN_FILE} -x track,faketime=${FAKETIME} -o gpx -F ${OUT_FILE}
cp ${OUT_FILE} ../180718.GPX