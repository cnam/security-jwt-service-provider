help:
	@echo "USAGE\n\n" \
		"start - for running\n" \
		"test  - for testing\n" \
		"clean - for stop and removing\n"

start:
	@docker run --rm \
		-v $(CURDIR):/data \
		-v $$HOME/.composer/cache:/cache \
		imega/composer update
	@docker-compose up -d

clean:
	@docker-compose stop
	@docker-compose rm --force

restart: clean start

test:
	@docker run --rm -t \
	 	-v `pwd`:/var/www/mockserver -w /var/www/mockserver \
	 	--link securityjwtserviceprovider_proxy_1:mockserver.test \
	 	cnam/php-behat \
	 	php5 /vendor/bin/behat --format pretty

.PHONY: start test clean restart
