BIN_NAME=json2yml
build: vendor
	php build-phar.php --bin="$(BIN_NAME)"
	chmod a+x $(BIN_NAME)

vendor:
	composer install --no-dev --optimize-autoloader

clean:
	-rm $(BIN_NAME)

clean-all: clean
	-rm -rf vendor

install:
	cp $(BIN_NAME) /usr/local/bin/
