.PHONY: dist

NAME=qdb-3_2

dist:
	git archive --format=tar --prefix=$(NAME)/ HEAD | bzip2 -9 > $(NAME).tar.bz2
	ls -lh $(NAME).tar.bz2
