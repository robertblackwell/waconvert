BABEL=@/Applications/GPSBabelFE.app/Contents/MacOS/gpsbabel -t 
NUKE=-x nuketypes,waypoints,routes
INTYPE=-i gpx
OUTTYPE=-o gpx

RAW=$(wildcard ./raw/*.GPX) 
DAILY=$(shell ./scripts/replace.php $(RAW)) 


./daily/%.GPX: ./raw/%.GPX
	@echo rule make $@ from $<
	$(BABEL) $(NUKE) $(INTYPE) -f $< -x track,title=$<,pack $(OUTTYPE) -F $@  

all: combined.json

combined.json: combined.gpx
	@echo making combined.json
	@php ./src/GpxDocument.php

combined.gpx: $(DAILY) 
	@echo making combined.gpx
	@./scripts/combine.sh


clean:
	rm -R daily/*
	rm combined.gpx
	rm raw/160718combined.GPX