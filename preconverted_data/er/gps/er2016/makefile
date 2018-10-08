BABEL=@/Applications/GPSBabelFE.app/Contents/MacOS/gpsbabel -t 
NUKE=-x nuketypes,waypoints,routes
INTYPE=-i gpx
OUTTYPE=-o gpx

RAW=$(wildcard ./raw/*.GPX) ./raw/160718combined.GPX
DAILY=$(shell ./scripts/replace.php $(RAW)) ./raw/160718combined.GPX

DEPS160718=$(wildcard ./160718/*.GPX)
RAW160718=./raw/160718combined.GPX
DAILY160718=./daily/160718combined.GPX


./daily/%.GPX: ./raw/%.GPX
	@echo rule make $@ from $<
	$(BABEL) $(NUKE) $(INTYPE) -f $< -x track,title=$<,pack $(OUTTYPE) -F $@  

all: 160718 combined.json

combined.json: combined.gpx
	@echo making combined.json
	@php ./src/GpxDocument.php

combined.gpx: $(DAILY) $(DAILY160718)
	@echo making combined.gpx
	@./scripts/combine.sh

160718: $(DAILY160718)


$(RAW160718): $(DEPS160718) 
	@echo make ./raw/160718combined.GPX from dependencies
	@./scripts/combine-160718.sh

clean:
	rm -R daily/*
	rm combined.gpx
	rm raw/160718combined.GPX