SPLIT := bin/split
CHECK := bin/check 
SSA := bin/ssa
ANAL := bin/anal
STRIP := bin/stripAligned
CSCORE := bin/conScore
PDB2SEQ := bin/pdb2seq
RENUM := bin/renumberBack
CKALNFORM := bin/checkAlignmentFormat
CSCOREALN := bin/conScoreAlignmentProvided
CONVALN2CLUSTL := bin/convAln2Clusl

OPT1 := -c -g -fPIC -O1

LIB := `root-config --libs`
INC := -I`root-config --incdir`

SRC := $(PWD)/src
OBJ := $(PWD)/lib

SRC_SPLIT := $(SRC)/split.cpp 
OBJ_SPLIT := $(OBJ)/split.o

SRC_CHECK := $(SRC)/check.cpp
OBJ_CHECK := $(OBJ)/check.o

SRC_SSA := $(SRC)/ssa.cpp
OBJ_SSA := $(OBJ)/ssa.o

SRC_ANAL := $(SRC)/anal.cpp
OBJ_ANAL := $(OBJ)/anal.o

SRC_STRIP := $(SRC)/stripAligned.cpp
OBJ_STRIP := $(OBJ)/stripAligned.o

SRC_CSCORE := $(SRC)/conScore.cpp
OBJ_CSCORE := $(OBJ)/conScore.o

SRC_PDB2SEQ := $(SRC)/pdb2seq.cpp
OBJ_PDB2SEQ := $(OBJ)/pdb2seq.o

SRC_RENUM := $(SRC)/renumberBack.cpp
OBJ_RENUM := $(OBJ)/renumberBack.o

SRC_CKALNFORM := $(SRC)/checkAlignmentFormat.cpp
OBJ_CKALNFORM := $(OBJ)/checkAlignmentFormat.o

SRC_CSCOREALN := $(SRC)/conScoreAlignmentProvided.cpp 
OBJ_CSCOREALN := $(OBJ)/conScoreAlignmentProvided.o

SRC_CONVALN2CLUSTL := $(SRC)/convAln2Clusl.cpp
OBJ_CONVALN2CLUSTL := $(OBJ)/convAln2Clusl.o


all : $(SPLIT) $(CHECK) $(SSA) $(ANAL) $(STRIP) $(CSCORE) $(PDB2SEQ) $(RENUM) $(CKALNFORM)  $(CSCOREALN) $(CONVALN2CLUSTL)

$(PDB2SEQ) : $(OBJ_PDB2SEQ)
	c++ $^ -o $@

$(OBJ_PDB2SEQ): $(SRC_PDB2SEQ)
	c++ $(OPT1) $^ -o $@

$(CSCORE) : $(OBJ_CSCORE)
	c++ $^ -o $@

$(OBJ_CSCORE): $(SRC_CSCORE)
	c++ $(OPT1) $^ -o $@

$(SPLIT)  : $(OBJ_SPLIT)
	c++ $^ -o $@

$(OBJ_SPLIT) : $(SRC_SPLIT)
	c++ $(OPT1) $^ -o $@


$(CHECK)  : $(OBJ_CHECK)
	c++  $^ -o $@

$(OBJ_CHECK) : $(SRC_CHECK)
	c++ $(OPT1) $^ -o $@

$(SSA)  : $(OBJ_SSA)
	c++  $^ -o $@

$(OBJ_SSA) : $(SRC_SSA)
	c++ $(OPT1) $^ -o $@


$(ANAL)  : $(OBJ_ANAL)
	c++ $^ -o $@

$(OBJ_ANAL) : $(SRC_ANAL)
	c++ $(OPT1) $^ -o $@


$(STRIP)  : $(OBJ_STRIP)
	c++  $^ -o $@

$(OBJ_STRIP) : $(SRC_STRIP)
	c++ $(OPT1) $^ -o $@

$(RENUM)  : $(OBJ_RENUM)
	c++  $^ -o $@

$(OBJ_RENUM) : $(SRC_RENUM)
	c++ $(OPT1) $^ -o $@

$(CKALNFORM)  : $(OBJ_CKALNFORM)
	c++  $^ -o $@

$(OBJ_CKALNFORM) : $(SRC_CKALNFORM)
	c++ $(OPT1) $^ -o $@

$(CSCOREALN) : $(OBJ_CSCOREALN)
	c++ $^ -o $@

$(OBJ_CSCOREALN) :  $(SRC_CSCOREALN)
	c++ $(OPT1) $^ -o $@


$(CONVALN2CLUSTL) : $(OBJ_CONVALN2CLUSTL)
	c++ $^ -o $@

$(OBJ_CONVALN2CLUSTL) :  $(SRC_CONVALN2CLUSTL)
	c++ $(OPT1) $^ -o $@
