PHP_SERVICE := lunch_php

x := $(shell date +%Y-%m-%d)

build:
	@docker-compose build

up:
	@docker-compose up -d

test:
	@docker-compose exec -T $(PHP_SERVICE) bin/phpunit --testdox
	@echo ""
	@echo ""
	@echo "cURL Lunch API today ($x)"
	@curl -X GET 'http://127.0.0.1:1080/lunch'
	@echo ""
	@echo ""
	@echo "cURL Lunch API on a date all ingredients are fresh (2019-03-01)"
	@curl -X GET 'http://127.0.0.1:1080/lunch?date=2019-03-01'
	@echo ""
	@echo ""
	@echo "cURL Lunch API on a date some ingredients are past their best-before date (2019-03-08)"
	@curl -X GET 'http://127.0.0.1:1080/lunch?date=2019-03-08'
	@echo ""
	@echo ""
	@echo "cURL Lunch API on a date more ingredients are past their best-before date (2019-03-14)"
	@curl -X GET 'http://127.0.0.1:1080/lunch?date=2019-03-14'
	@echo ""
	@echo ""
	@echo "cURL Lunch API on a date no fresh ingredients remain (2019-03-28)"
	@curl -X GET 'http://127.0.0.1:1080/lunch?date=2019-03-28'
	@echo ""
	@echo ""
	@echo "cURL Lunch API with an invalid format in the date parameter"
	@curl -X GET 'http://127.0.0.1:1080/lunch?date=notADate'
	@echo ""
	@echo ""
	@echo "cURL Lunch API with an invalid value in date parameter"
	@curl -X GET 'http://127.0.0.1:1080/lunch?date=2021-99-99'
	@echo ""
	@echo ""

down:
	@docker-compose down

all:
	@make -s build
	@make -s up
	@make -s test

all_and_cleanup:
	@make -s build
	@make -s up
	@make -s test
	@make -s down
