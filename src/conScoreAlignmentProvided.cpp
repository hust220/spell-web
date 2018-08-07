#include<iostream>
#include<fstream>
#include<sstream>
#include<vector>
#include<string>
#include<map>
#include<cmath>
#include <stdlib.h>
using namespace std; 


static double empty = 0.2;


vector<string> parse_line(string line,char separator){
  vector<string> fields;
  string field;
  int ifield = 0;
  for(int i=0; i<line.size(); i++){
    if(line[i]!=separator){
      field.push_back(line[i]);
      ifield = 1;
    }
    if(line[i]==separator || i==line.size()-1){
      if(ifield==1) {
        fields.push_back(field);
        field.erase();
        ifield = 0;
      }
    }
  }
  return fields;
};


map<string,double> getWeights(map<string,string> mseqs, int ifirst, int ilast){

  // cut off
  //  double empty = 0.2; 

  vector< map<string,int> > vmaa;

  map<string,string>::const_iterator it;
  it=mseqs.begin(); 
  int plen = (*it).second.size(); 

  for(int i=0; i<plen; i++){
    map<string,int> maa;
    for(it=mseqs.begin(); it!=mseqs.end(); ++it){
      string aa = (*it).second.substr(i,1);
      maa[aa]++;
    }
    vmaa.push_back(maa);
  }

  /*
  cout << "VMAA: " << vmaa.size() << endl;
  for(int i=0; i<vmaa.size(); i++){
    cout << " ======== " << i+1 << " ============" << endl;
    map<string,int> maa = vmaa[i];
    int naa = 0;
    map<string,int>::const_iterator it;
    for(it=maa.begin(); it != maa.end(); ++it){
      naa++;
      cout << (*it).first << " " << (*it).second << endl;
    }
    cout << "   " << naa << endl;
    //    cout << "VT: " << mseq["Vt"] << endl;
  }
  */

  map<string,double> mweights; 
  for(it=mseqs.begin(); it!=mseqs.end(); ++it){
    double aweight = 0;
    int nL = 0;  
    for(int i=ifirst; i<=ilast; i++){
      map<string,int> maa = vmaa[i]; 
      if((maa["."] + maa["-"])/(mseqs.size()+0.)<empty){
        string aa = (*it).second.substr(i,1);
        aweight += 1./maa[aa]; 
	nL++;
      }
    }
    mweights[(*it).first] = aweight/nL;
  }

  return mweights;

}

vector<double> getWeightsOld(map<string,string> mseqs){


  /*

  cout << "VMAA: " << vmaa.size() << endl;
  for(int i=0; i<vmaa.size(); i++){
    cout << " ======== " << i+1 << " ============" << endl;
    map<string,int> maa = vmaa[i];
    int naa = 0;
    map<string,int>::const_iterator it;
    for(it=maa.begin(); it != maa.end(); ++it){
      naa++;
      cout << (*it).first << " " << (*it).second << endl;
    } 
    cout << "   " << naa << endl; 
    //    cout << "VT: " << mseq["Vt"] << endl;                  
  }
  */

} 

string convert2letter(string name){
  if(name=="ARG") return "R"; 
  if(name=="HIS") return "H";
  if(name=="LYS") return "K";
  if(name=="ASP") return "D";
  if(name=="GLU") return "E";
  if(name=="SER") return "S"; 
  if(name=="THR") return "T"; 
  if(name=="ASN") return "N";
  if(name=="GLN") return "Q";
  if(name=="CYS") return "C";
  if(name=="GLY") return "G";
  if(name=="PRO") return "P";
  if(name=="ALA") return "A";
  if(name=="VAL") return "V"; 
  if(name=="ILE") return "I";
  if(name=="LEU") return "L";
  if(name=="MET") return "M";
  if(name=="PHE") return "F";
  if(name=="TYR") return "Y";
  if(name=="TRP") return "W";
  cout << "wrong amino acis code " << endl; 
  exit(0); 
  return "000";
}

struct stride{
  stride(){iflag=0;};
  stride(int iflag, string strideFile):iflag(iflag),strideFile(strideFile){};
  void init(); 
  int iflag;
  string querySeq; 
  string strideFile;
};

void stride::init(){ 

  ifstream in(strideFile.c_str());
  stringstream sqseq; 
  stringstream outStride; 
  string line;
  while(getline(in,line)){
    while(line.size()<80) line += " ";
    vector<string> vparsed = parse_line(line,' ');
    string lineMod; 
    if(vparsed[0]=="REM" && vparsed[1]=="|---Residue---|" && vparsed[2]=="|--Structure--|"){
      lineMod = line.substr(0,80) + "  |--Alignment--|" + "  |----Depth----|" + "  |----Score----|" + "  |--Insertion--|" + "  |-Consensus-|" + "   |Conservation|"; 
    } else if(vparsed[0]=="ASG"){
      lineMod = line.substr(0,80) + string(14,' ') + "ALN" + "              1/1" + "              100" + "                0" + "               " + "                0";
      sqseq << convert2letter(vparsed[1]); 
    } else {
      lineMod = line.substr(0,80) + string(17,' ') + string(17,' ') + string(17,' ') + string(17,' ') + string(15,' ') + string(17,' ');
    }
    outStride << lineMod << "\n";
  }

  querySeq = sqseq.str();

  filebuf outf; 
  outf.open(strideFile.c_str(),ios::out);
  ostream os(&outf); 

  os << outStride.str(); 

  outf.close();
  in.close();


}

struct aln{
  aln(string queryName, int ifirst, int isecond, string alnID, string alnName):queryName(queryName),ifirst(ifirst),isecond(isecond),alnID(alnID),alnName(alnName){};
  string queryName;
  int ifirst; 
  int isecond; 
  string alnID;
  string alnName;
}; 


vector<aln> getPfamScanData(string file){

  vector<aln> vresult; 
  ifstream in(file.c_str());
  string queryName = ""; 

  int nlines = 0;
  string line;
  while(getline(in,line)){
    vector<string> vparsed = parse_line(line,' ');
    if(line.size()!=0){
    if(vparsed[0].substr(0,1)!="#"){
      if(vparsed[0]!=""){ nlines++; }
      if(nlines==1) queryName = vparsed[0]; 
    }
    }
  }
  if(queryName.size()==0){ 
    cout << "Query sequence is not found. Check PfamScan file." << endl; 
    exit(0); 
  }

  in.clear(); 
  in.seekg(0, std::ios::beg); 

  while(getline(in,line)){
    vector<string> vparsed = parse_line(line,' ');
    if(line.size()!=0){
      if(vparsed[0].substr(0,1)!="#"){
        if(vparsed[0]==queryName){
          int istart = atoi(vparsed[1].c_str()); 
          int iend = atoi(vparsed[2].c_str());
          vresult.push_back(aln(queryName,istart,iend,vparsed[5],vparsed[6]));
	}
      }
    }
  }
  return vresult;
}

string conv_double_to_str(double val, int precision){
  stringstream st; 
  st << val; 
  string str = st.str(); 

  string base = "";
  string exponent = ""; 
  int iposition = -10; 
  for(int i=0; i<str.size(); i++){
    if(str.substr(i,1)=="e" || str.substr(i,1)=="E"){
      iposition = i; 
    }
  }
  if(iposition>=0){
    base = str.substr(0,iposition); 
    exponent = str.substr(iposition+1,str.size()-iposition-1);
    int iexp = atoi(exponent.c_str());
    double ibase = atof(base.c_str()); 
    string str1 = "";
    if(iexp<0){
      if(ibase>0) str1 += "0."; 
      if(ibase<0) str1 += "-0.";
      for(int i=0; i<-iexp-1; i++) str1 += "0";
      for(int i=0; i<base.size(); i++){
        if(base.substr(i,1)!="-" && base.substr(i,1)!="."){  
          str1 += base.substr(i,1);
        }
      }
      if(fabs(ibase)>10){
        cout << "dot is not in a right position; report from conv_double_to_str" << endl; 
        exit(0);       
      }
      str = str1; 
    } else {
      cout << "the number is too high; report from conv_double_to_str" << endl; 
      exit(0);
    } 
  } 
  int ic = -1;
  int iflag = 0; 
  int isize = 0; 
  for(int i=0; i<str.size();i++){
    isize++; 
    if(iflag==1) ic++;
    if(ic==precision) break;
    if(str.substr(i,1)=="."){
      iflag=1; 
      ic = 0; 
    }
  }
  if(isize+1<str.size()) str.erase(isize+1); 
  if(ic==-1){
    str += "."; 
    ic = 0;
  }
  if(ic<precision){
    int nit = precision - ic; 
    for(int i=0; i<nit; i++){
      str += "0";
    }
  }
  return str;
}

string convert2code(string name){
  if(name=="r" || name=="R") return "ARG"; 
  if(name=="h" || name=="H") return "HIS";
  if(name=="k" || name=="K") return "LYS";
  if(name=="d" || name=="D") return "ASP";
  if(name=="e" || name=="E") return "GLU";
  if(name=="s" || name=="S") return "SER"; 
  if(name=="t" || name=="T") return "THR"; 
  if(name=="n" || name=="N") return "ASN";
  if(name=="q" || name=="Q") return "GLN";
  if(name=="c" || name=="C") return "CYS";
  if(name=="g" || name=="G") return "GLY";
  if(name=="p" || name=="P") return "PRO";
  if(name=="a" || name=="A") return "ALA";
  if(name=="v" || name=="V") return "VAL"; 
  if(name=="i" || name=="I") return "ILE";
  if(name=="l" || name=="L") return "LEU";
  if(name=="m" || name=="M") return "MET";
  if(name=="f" || name=="F") return "PHE";
  if(name=="y" || name=="Y") return "TYR";
  if(name=="w" || name=="W") return "TRP";
  cout << "wrong amino acis code: " << name << endl; 
  exit(0); 
  return "000";
}

string convert2uppercase(string letter){
  if(letter=="r") return "R";
  if(letter=="h") return "H";
  if(letter=="k") return "K";
  if(letter=="d") return "D";
  if(letter=="e") return "E";
  if(letter=="s") return "S";
  if(letter=="t") return "T";
  if(letter=="n") return "N";
  if(letter=="q") return "Q";
  if(letter=="c") return "C";
  if(letter=="g") return "G";
  if(letter=="p") return "P";
  if(letter=="a") return "A";
  if(letter=="v") return "V";
  if(letter=="i") return "I";
  if(letter=="l") return "L";
  if(letter=="m") return "M";
  if(letter=="f") return "F";
  if(letter=="y") return "Y";
  if(letter=="w") return "W";
  return letter;
}


void computeConservation(string  querySeqID, char* alnFile, stride strideData){


  ifstream inaln(alnFile);
  if(inaln.is_open()==false){
    cout << "no alignment file " << alnFile << endl;
    exit(0);
  }
  /*
  map<string,int> maa;
  maa["R"]=1;
  maa["H"]=1;
  maa["K"]=1;
  maa["D"]=1;
  maa["E"]=1;
  maa["S"]=1;
  maa["T"]=1;
  maa["N"]=1;
  maa["Q"]=1;
  maa["C"]=1;
  maa["G"]=1;
  maa["P"]=1;
  maa["A"]=1;
  maa["V"]=1;
  maa["I"]=1;
  maa["L"]=1;
  maa["M"]=1;
  maa["F"]=1;
  maa["Y"]=1;
  maa["W"]=1;
  */
  string AlignmentType = "0";

  string line;
  getline(inaln,line);

  if(line.substr(0,1)==">"){
    AlignmentType = "FASTA";
    cout << "FASTA format is recognized" << endl;
  } else {
    vector<string> vparsed = parse_line(line,' ');
    if(vparsed.size()>=2 && vparsed[0]=="#" && vparsed[1]=="STOCKHOLM"){
      AlignmentType = "STOCKHOLM";
      cout << "STOCKHOLM format is recognized" << endl;
    } else {
      cout << "Alignment type is not recognized ..." << endl;
      exit(0);
    }
  }

  inaln.clear();
  inaln.seekg(0, ios::beg);

  map<string,string> mseqs;

  if(AlignmentType == "FASTA"){

    string sname = "000000";
    string seq;
    int icount=0;
    while (getline(inaln,line)){
      if(line.substr(0,1)==">"){
        if(sname!="000000"){
          mseqs[sname] = seq;
          seq.erase(seq.begin(),seq.end());
        }
        sname = line;
      } else {
        for(int i=0; i<line.size(); i++){
          string letter = convert2uppercase(line.substr(i,1));
          seq += line.substr(i,1);
        }
      }
    }
    mseqs[sname] = seq;

  } else if (AlignmentType == "STOCKHOLM"){

    while (getline(inaln,line)){
      vector<string> vparsed = parse_line(line,' ');
      if(vparsed.size()>0 && vparsed[0].substr(0,1)!="#" && vparsed[0]!="//"){
        string seqname = vparsed[0];
        string record = vparsed[1];
        for(int i=0; i<record.size(); i++){
          string letter = convert2uppercase(record.substr(i,1));
          mseqs[seqname] += letter;
        }
      }
    }

  } 
  inaln.close(); 

  string querySeq = mseqs[querySeqID]; 
  //  cout << querySeq << " " << querySeq.size() << endl; 

  /*
  int ifirst = 0, ilast = 0, j=0; 
  for(int i=0; i<querySeq.size(); i++){
    string aa = querySeq.substr(i,1);
    if(aa!="." && aa!="-"){
      j++;
      if(j==pfamData.ifirst) ifirst = i; 
      if(j==pfamData.isecond) ilast = i;
    }
  }
  cout << "NUM: " << ifirst << " " << ilast << endl;
  */

  int ifirst = 0, ilast = strideData.querySeq.size()-1; 
  map<string,double> mweights = getWeights(mseqs,ifirst,ilast); 

  vector<double> ventropy;  
  vector<string> vconsensus; 
  vector<double> vconservation; 
  for(int i=0; i<querySeq.size(); i++){
    double nTotal = 0;
    map<string,double> maa;
    map<string,string>::const_iterator it;
    for(it=mseqs.begin(); it!=mseqs.end(); ++it){
      string aa = (*it).second.substr(i,1);
      maa[aa] += mweights[(*it).first];
      nTotal += mweights[(*it).first];
      //      maa[aa] += 1.;
      //      nTotal += 1.;
    }
    map<string,double>::const_iterator it1;
    double entropy = 0.;
    string aconsensus; 
    double aconservation = -100; 
    for(it1=maa.begin(); it1!=maa.end();++it1){
      double prob = (*it1).second/nTotal;
      entropy += -prob*log2(prob);
      if((*it1).first!="-" &&  (*it1).first!="." && prob>aconservation){
	aconservation = prob; 
        aconsensus = (*it1).first; 
      }
    }
    ventropy.push_back(entropy);
    vconsensus.push_back(aconsensus); 
    vconservation.push_back(aconservation);
  }

  vector<int> vcoverage;
  vector<double> vqueryentropy; 
  vector<string> vqueryconsensus; 
  vector<double> vqueryconservation;
  vector<int> vinsertion;
  int nIns = 0; 
  for(int i=0; i<querySeq.size(); i++){
    string aa = querySeq.substr(i,1);
    if(aa=="-" || aa=="."){
      nIns++;
    }
    if(aa!="." && aa!="-"){
      vqueryentropy.push_back(ventropy[i]);
      vqueryconsensus.push_back(vconsensus[i]);
      vqueryconservation.push_back(vconservation[i]);
      map<string,int> maa;
      map<string,string>::const_iterator it;
      for(it=mseqs.begin(); it!=mseqs.end(); ++it){
	string aa1 = (*it).second.substr(i,1);
        maa[aa1] += 1;
      }
      vcoverage.push_back(mseqs.size()-maa["."]-maa["-"]);
      vinsertion.push_back(nIns);
      nIns = 0;
    }
  }

  //  cout << "INSERT: " << vinsertion.size() << " " << vqueryentropy.size() << endl;                  
              
  if(strideData.iflag==1){
    int j=0; 
    stringstream outStride;
    ifstream instride(strideData.strideFile.c_str());
    while(getline(instride,line)){
      vector<string> vparsed = parse_line(line,' ');
      if(vparsed[0]=="ASG"){
        j++;
        if(j>=1 && j<=strideData.querySeq.size()){
	  //          line.replace(97-pfamData.alnName.size(),pfamData.alnName.size(),pfamData.alnName); 
          stringstream ncov; 
          ncov << vcoverage[j-1] << "/" << mseqs.size(); 
          line.replace(114-ncov.str().size(),ncov.str().size(),ncov.str());
          string sen = conv_double_to_str(vqueryentropy[j-1],3);
	  line.replace(131-sen.size(),sen.size(),sen); 
          stringstream sins; 
          sins << vinsertion[j-1];
	  line.replace(148-sins.str().size(),sins.str().size(),sins.str());
          stringstream sconsens;
          sconsens << convert2code(vqueryconsensus[j-1]);
          line.replace(163-sconsens.str().size(),sconsens.str().size(),sconsens.str()); 
          stringstream sconserv;
          sconserv << conv_double_to_str(vqueryconservation[j-1],3);
          line.replace(180-sconserv.str().size(),sconserv.str().size(),sconserv.str());
	}
      } 
      outStride << line << "\n";
    }
    instride.close();
    //      cout << outStride.str() << endl;

    filebuf outf;
    outf.open(strideData.strideFile.c_str(),ios::out);
    ostream os(&outf);
    os << outStride.str();
    outf.close();

  }
  inaln.close();
}

map<string, string>  getAlignedSeqs (char* file){

  map<string,string> mseqs;  

  ifstream in(file); 
  if(in.is_open()==false){
    cout << "Fatal error: no alignment file " << file << endl;
    exit(0);
  }

  map<string,int> maa; 
  maa["R"]=1; 
  maa["H"]=1; 
  maa["K"]=1; 
  maa["D"]=1; 
  maa["E"]=1; 
  maa["S"]=1; 
  maa["T"]=1; 
  maa["N"]=1; 
  maa["Q"]=1; 
  maa["C"]=1; 
  maa["G"]=1; 
  maa["P"]=1;
  maa["A"]=1;
  maa["V"]=1; 
  maa["I"]=1; 
  maa["L"]=1; 
  maa["M"]=1; 
  maa["F"]=1; 
  maa["Y"]=1; 
  maa["W"]=1;


  string AlignmentType = "0"; 

  string line; 
  getline(in,line);

  if(line.substr(0,1)==">"){
    AlignmentType = "FASTA";
    cout << "FASTA format is recognized" << endl; 
  } else {
    vector<string> vparsed = parse_line(line,' '); 
    if(vparsed.size()>=2 && vparsed[0]=="#" && vparsed[1]=="STOCKHOLM"){
      AlignmentType = "STOCKHOLM"; 
      cout << "STOCKHOLM format is recognized" << endl;
    } else {
      cout << "Alignment type is not recognized ..." << endl; 
      exit(0);
    }
  }

  in.clear(); 
  in.seekg(0, ios::beg);

  if(AlignmentType == "FASTA"){

    string sname = "000000";
    string seq; 
    int icount=0; 
    while (getline(in,line)){
      if(line.substr(0,1)==">"){
	if(sname!="000000"){
          mseqs[sname] = seq;
	  seq.erase(seq.begin(),seq.end());
	}
	sname = line; 
      } else {
	for(int i=0; i<line.size(); i++){
	  string letter = convert2uppercase(line.substr(i,1));
	  if(maa[letter]==1) seq += line.substr(i,1); 
	}
      }
    }
    mseqs[sname] = seq; 

  } else if (AlignmentType == "STOCKHOLM"){

    while (getline(in,line)){
      vector<string> vparsed = parse_line(line,' ');
      if(vparsed.size()>0 && vparsed[0].substr(0,1)!="#" && vparsed[0]!="//"){
	string seqname = vparsed[0];
	string record = vparsed[1];
	for(int i=0; i<record.size(); i++){
	  string letter = convert2uppercase(record.substr(i,1));
	  if(letter!="." && letter!="-") mseqs[seqname] += letter; 
	}
      }   
    }

  }
  in.close();

  return mseqs;

}


void print_help(){
  cout << "\nWRONG NUMBER OF PARAMETERS: \n" << endl;
  cout << "./conScore multiple_seq_aln.aln stride.txt \n" << endl;    
  exit(0);
}


int main(int argc, char*argv[]){

  if(argc!=3) print_help(); 

  ifstream in(argv[1]);
  if(in.is_open()==false){
    cout << "no pfam scan file " << argv[1] << endl;
    exit(0);
  }

  /*
  // read PfamScan file
  vector<aln> valn = getPfamScanData(argv[1]);
  for(int i=0; i<valn.size(); i++){
    cout << valn[i].ifirst << " " << valn[i].isecond << " " << valn[i].alnName << " " << valn[i].alnID << endl;
  }
  */

  // read stride file 
  stride strideData;
  strideData.iflag = 0;
  if(argc==3){
    strideData.iflag = 1;
    strideData.strideFile = argv[2];
    strideData.init(); 
  }


  //  strideData.querySeq = "VFVKNPDGGSYAYAINPNSFILGLKQQIEDQQGLPKKQQQLEFQGQVLQDWLGLGIYGIQDSDTLILSKKKG";


  string querySeqID = "NULL";
  map<string, string> mseqs = getAlignedSeqs(argv[1]); 
  map<string,string>::const_iterator it;
  for(it=mseqs.begin(); it!=mseqs.end(); ++it){
    if((*it).second==strideData.querySeq) querySeqID = (*it).first;
  }

  //  aln(querySeqID, int ifirst, int isecond, string alnID, string alnName)

  computeConservation(querySeqID, argv[1], strideData); 

  //  cout << querySeqID << endl;
  //    cout << strideData.querySeq << endl;
  /*
  for(int i=0; i<valn.size(); i++){
    computeConservation(valn[i], strideData); 
  }
  */

}

