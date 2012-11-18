SRCS = $(wildcard lib/*.js)
INIT_SRCS = $(wildcard init-pages/*.js)
CONCAT = $(addprefix bin/work/, $(notdir $(INIT_SRCS)))
MINS = $(CONCAT:.js=.min.js)

all: distro-min
debug: lint build distro
quick: distro

build: $(SRCS)
	browserbuild -m lib/Brovine `find lib -name \*.js` > bin/brovine.js

debugbuild: $(SRCS)
	browserbuild -d -m lib/brovine `find lib -name \*.js` > bin/brovine.js

bin/work/%.js: init-pages/%.js build 
	cat bin/brovine.js $< > $@

distro: $(CONCAT)
	mv bin/work/*.js dist/

distro-min: $(MINS)
	mv bin/work/*.min.js dist/

bin/work/%.min.js: bin/work/%.js
	uglifyjs2 $< -c -o $@

lint: $(SRCS)
	jslint `find lib -name \*.js`
	jslint `find init-pages -name \*.js`
