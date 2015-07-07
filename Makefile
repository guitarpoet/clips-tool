test: clean_cache
	cd tests && phpunit
clean_cache:
	rm -f tests/application/cache/*
