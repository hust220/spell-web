# .bashrc

# Source global definitions
if [ -f /etc/bashrc ]; then
	. /etc/bashrc
fi

# User specific aliases and functions

PATH="/root/perl5/bin${PATH:+:${PATH}}"; export PATH;
PERL5LIB="/root/perl5/lib/perl5${PERL5LIB:+:${PERL5LIB}}"; export PERL5LIB;
PERL_LOCAL_LIB_ROOT="/root/perl5${PERL_LOCAL_LIB_ROOT:+:${PERL_LOCAL_LIB_ROOT}}"; export PERL_LOCAL_LIB_ROOT;
PERL_MB_OPT="--install_base \"/root/perl5\""; export PERL_MB_OPT;
PERL_MM_OPT="INSTALL_BASE=/root/perl5"; export PERL_MM_OPT;

export PATH=/var/www/htdocs/spell/hmmer-3.1b2/src:$PATH
export PERL5LIB=/var/www/htdocs/spell/PfamScan:$PERL5LIB

source root/root/bin/thisroot.sh