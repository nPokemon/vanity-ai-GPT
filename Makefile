up:
	docker-compose up --build

rm-all:
	docker container rm $(shell docker ps -aq) -f

ps:
	docker ps

prune-all:
	docker system prune -a

exec:
	docker exec -t -i $(NAME) bash

exec-api:
	docker exec -t -i hunter-ai-api-api-1 bash
