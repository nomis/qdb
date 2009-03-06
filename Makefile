.PHONY: dist ps

CP=cp
GREP=grep
LS=ls
MKDIR=mkdir
RM=rm
SVN=svn
TAR=tar
TOUCH=touch
XARGS=xargs

NAME=qdb-2_4

dist:
	$(RM) -rf .tmp/ $(NAME).tar.gz
	$(MKDIR) -p .tmp/$(NAME)/

	$(SVN) up
	$(SVN) ls -R | $(GREP) -E '/$$' | $(XARGS) -I '{}' $(MKDIR) .tmp/$(NAME)/'{}'
	$(SVN) ls -R | $(GREP) -vE '/$$' | $(XARGS) -I '{}' $(CP) --preserve=mode,timestamps,links '{}' .tmp/$(NAME)/'{}'
	$(SVN) ls -R | $(GREP) -E '/$$' | $(XARGS) -I '{}' $(TOUCH) -r '{}' .tmp/$(NAME)/'{}'
	$(TOUCH) -r Makefile .tmp/$(NAME)/
	$(TAR) --owner 0 --group 0 -C .tmp/ -jf $(NAME).tar.bz2 -c $(NAME)

	$(RM) -rf .tmp/
	$(LS) -lh $(NAME).tar.bz2

ps:
	$(SVN) ls -R | $(GREP) -vE '/$$' | $(XARGS) $(GREP) -l '$$Id' | $(XARGS) $(SVN) ps svn:keywords Id
