VENDORBIN="../vendor/bin"
LITETEST=@$(VENDORBIN)/litetest.phar
TESTS_DIR = $(shell pwd)
DATA_DIR =$(TESTS_DIR)/test_data
TEST_DATA_SRC=$(TESTS_DIR)/test_data_src/data

all:
	$(LITETEST) --bootstrap-file="include/bootstrap.php" unit_tests/test1.php
