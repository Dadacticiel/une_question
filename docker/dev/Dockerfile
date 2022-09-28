FROM webdevops/php-nginx-dev:8.1

# Install node from nodesource
RUN curl -sL https://deb.nodesource.com/setup_16.x | bash - \
  && apt-get install -y nodejs

# Install yarn
RUN curl -sS https://dl.yarnpkg.com/debian/pubkey.gpg | apt-key add - \
 && echo "deb https://dl.yarnpkg.com/debian/ stable main" | tee /etc/apt/sources.list.d/yarn.list \
 && apt-get update -qq \
 && apt-get install -y yarn
 
# composer
RUN curl -sS https://getcomposer.org/installer | \
    php -- --install-dir=/usr/bin/ --filename=composer
	
COPY ./composer.* /app/

RUN curl -fsS https://platform.sh/cli/installer | php
RUN export PLATFORMSH_CLI_TOKEN=tEXj3SrR4Kq1O_v1h-GnvEsb5MxMRpHgfo40MsTytMM
RUN platform auth:api-token-login -y

WORKDIR /app