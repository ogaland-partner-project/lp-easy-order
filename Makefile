du:
	docker-compose up -d nginx
de:
	docker-compose exec -u root work bash
due:
	docker-compose up -d
	docker-compose exec -u root work bash
dd:
	docker-compose down
dues:
	service docker start
	docker-compose up -d nginx
	docker-compose exec -u root work bash