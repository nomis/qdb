.PHONY: dist ps

CP=cp
GREP=grep
LS=ls
MKDIR=mkdir
RM=rm
SVN=svn
TAR=tar
XARGS=xargs

NAME=qdb-2_0
EXCL=inc/config.php

dist:
	$(RM) -rf .tmp/ $(NAME).tar.gz
	$(MKDIR) -p .tmp/$(NAME)/

	$(SVN) ls -R | $(GREP) -E '/$$' | $(XARGS) -I '{}' $(MKDIR) -p .tmp/$(NAME)/'{}'
	$(SVN) ls -R | $(GREP) -vE '/$$' | $(XARGS) -I '{}' $(CP) --preserve=mode,timestamps,links '{}' .tmp/$(NAME)/'{}'
	$(TAR) --owner 0 --group 0 -C .tmp/ -zf $(NAME).tar.gz -c $(NAME)

	$(RM) -rf .tmp/
	$(LS) -lh $(NAME).tar.gz

ps:
	$(SVN) ls -R | $(GREP) -vE '/$$' | $(XARGS) $(GREP) -l '$$Id' | $(XARGS) $(SVN) ps svn:keywords Id
