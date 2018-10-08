#makefile
BINDIR := ~/bin
BUILDDIR:=	build
PHARNAME := waconvert.phar
TARGET := 	build/$(PHARNAME)
PHARIZER := scripts/create-phar.php
SRCPREFIX := `pwd`
WADATA := $(HOME)/Sites/whiteacorn/data

# STARTED OFF TO BE A PHAR BUILD PRODUCT BUT THAT WILL NOT WORK
# AS THE EXECUTABLE NEEDS THE CONTENTS OF preconverted_data

php :=  $(shell find src -type f -name "*.php") $(shell find vendor -type f -name "*.php")  

build/wactl: $(TARGET)
	# take the phar extension off the end

$(TARGET): Makefile $(PHARIZER) $(php)
	# create the phar
	$(PHARIZER) $(TARGET) $(SRCPREFIX) src/Stub.php src vendor 
	#make it executable
	chmod 775 $(TARGET)

install:
	cp -v $(TARGET) $(BINDIR)/$(PHARNAME)

clean:
	rm -v $(BUILDDIR)/*

wa:
	@cp -rv $(WADATA) test/data-clean