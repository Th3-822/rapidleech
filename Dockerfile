FROM ubuntu:20.04

WORKDIR /app

RUN apt -qq update

ENV DEBIAN_FRONTEND=noninteractive

RUN apt -qq install -y git wget php npm \
    python3 python3-pip  bash curl \
    build-essential apache2 php7.4-curl

COPY . .

CMD ["bash", "start.sh"]
