#README


This folder contains the data and tools to build the gps tracks for the trip we did in 2016.

##Organization

This folder contains the following subfolders:

-	__raw__ the raw daily GPX file exportd from Garmin BaseCamp on the assumption that the UTC date is the same as the local date. There is one complexity - see below under 160718.

-	__160718__, this trip was done in two pieces:

	-	July 8th through July 18th, and 
	-	Aug 11th through not yet finished
	
	The first segment has a complexity in that I ended the whiteacorn journal at 7/18/2016 (160718) but I needed the tracks
	from the subsequent 3 days so in total for 18th, 19th, 20th, 21st to get us back to home base. This folder, 160718, contains the tracks for those days. 

	The script `combine-160718.sh` combines those 4 files into a single file `160718-combined.GPX` which lives in __raw__.

-	__not needed__, holds the tracks for the days between the two segments of this trip in the offchance that they are for some reason needed.

-	__daily__, contain gpx files of the form YYMMDD.GPX representing the track for a given day. These have been additionally processed so that each GPX file has only on `<trk>` element.	

-	__scripts__, contain shell scripts for processing the files into the final result.	

