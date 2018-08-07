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

struct stride{
  stride(){iflag=0;};
  stride(int iflag, string strideFile):iflag(iflag),strideFile(strideFile){};
  void init(); 
  int iflag;
  string strideFile;
};

void stride::init(){ 

  ifstream in(strideFile.c_str());
  stringstream outStride; 
  string line;
  while(getline(in,line)){
    while(line.size()<80) line += " ";
    vector<string> vparsed = parse_line(line,' ');
    string lineMod; 
    if(vparsed[0]=="REM" && vparsed[1]=="|---Residue---|" && vparsed[2]=="|--Structure--|"){
      lineMod = line.substr(0,80) + "  |--Alignment--|" + "  |----Depth----|" + "  |----Score----|" + "  |--Insertion--|" + "  |-Consensus-|" + "   |Conservation|"; 
    } else if(vparsed[0]=="ASG"){
      lineMod = line.substr(0,80) + string(15,' ') + "NO" + "              1/1" + "              100" + "                0" + "             NO" + "                0";
    } else {
      lineMod = line.substr(0,80) + string(17,' ') + string(17,' ') + string(17,' ') + string(17,' ') + string(15,' ') + string(17,' ');
    }
    outStride << lineMod << "\n";
  }

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

string conv2CAP(string let){
  if(let=="a") return "A";
  if(let=="r") return "R"; 
  if(let=="n") return "N";
  if(let=="d") return "D";
  if(let=="c") return "C";
  if(let=="q") return "Q";
  if(let=="e") return "E";
  if(let=="g") return "G";      
  if(let=="h") return "H";
  if(let=="i") return "I";  
  if(let=="l") return "L";
  if(let=="k") return "K";
  if(let=="m") return "M";
  if(let=="f") return "F";
  if(let=="p") return "P";   
  if(let=="s") return "S";
  if(let=="t") return "T";
  if(let=="w") return "W";
  if(let=="y") return "Y";
  if(let=="v") return "V";
  return let;
}

int inAlphabet(string let){
  if(let=="a" || let=="A") return 1;
  if(let=="r" || let=="R") return 1;
  if(let=="n" || let=="N") return 1;
  if(let=="d" || let=="D") return 1;
  if(let=="c" || let=="C") return 1;
  if(let=="q" || let=="Q") return 1;
  if(let=="e" || let=="E") return 1;
  if(let=="g" || let=="G") return 1;
  if(let=="h" || let=="H") return 1;
  if(let=="i" || let=="I") return 1;
  if(let=="l" || let=="L") return 1;
  if(let=="k" || let=="K") return 1;
  if(let=="m" || let=="M") return 1;
  if(let=="f" || let=="F") return 1;
  if(let=="p" || let=="P") return 1;
  if(let=="s" || let=="S") return 1;
  if(let=="t" || let=="T") return 1;
  if(let=="w" || let=="W") return 1;
  if(let=="y" || let=="Y") return 1;
  if(let=="v" || let=="V") return 1;
  return -1; 
}


void computeConservation(aln pfamData, stride strideData){

  string alnFile = "q" + pfamData.alnName + ".sto";
  ifstream inaln(alnFile.c_str());
  if(inaln.is_open()==false){
    cout << "no alignment file " << alnFile << endl;
    exit(0);
  }

  string line; 
  getline(inaln,line);
  vector<string> vparsed = parse_line(line,' '); 
  if(vparsed[1]!="STOCKHOLM"){
    cout << "not a STOCKHOLM format" << endl;
    exit(0);
  } 

  map<string,string> mseqs;                                                                                                                                      
  while (getline(inaln,line)){
    vector<string> vparsed = parse_line(line,' ');
    if(vparsed.size()>0 && vparsed[0].substr(0,1)!="#" && vparsed[0].substr(0,1)!="/"){
      mseqs[vparsed[0]] += vparsed[1];
    }
  }

  string querySeq = mseqs[pfamData.queryName]; 
  cout << querySeq << endl; 

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

  map<string,double> mweights = getWeights(mseqs,ifirst,ilast); 

  map<string,double> mBckgrProb; 
  mBckgrProb["A"] = 0.074;
  mBckgrProb["R"] = 0.042; 
  mBckgrProb["N"] = 0.044; 
  mBckgrProb["D"] = 0.059; 
  mBckgrProb["C"] = 0.033; 
  mBckgrProb["Q"] = 0.037; 
  mBckgrProb["E"] = 0.058; 
  mBckgrProb["G"] = 0.074; 
  mBckgrProb["H"] = 0.029; 
  mBckgrProb["I"] = 0.038; 
  mBckgrProb["L"] = 0.076; 
  mBckgrProb["K"] = 0.072; 
  mBckgrProb["M"] = 0.018; 
  mBckgrProb["F"] = 0.04; 
  mBckgrProb["P"] = 0.05; 
  mBckgrProb["S"] = 0.081; 
  mBckgrProb["T"] = 0.062; 
  mBckgrProb["W"] = 0.013; 
  mBckgrProb["Y"] = 0.033; 
  mBckgrProb["V"] = 0.068;

  vector<double> ventropy;  
  vector<string> vconsensus; 
  vector<double> vconservation; 
  for(int i=0; i<querySeq.size(); i++){
    double nTotal = 0;
    double nTotal_1 = 0; 
    map<string,double> maa;
    map<string,string>::const_iterator it;
    for(it=mseqs.begin(); it!=mseqs.end(); ++it){
      string aa = (*it).second.substr(i,1);
      maa[aa] += mweights[(*it).first];
      nTotal += mweights[(*it).first];
      //      maa[aa] += 1.;
      //      nTotal += 1.;
      if(aa!="." && aa!="-") nTotal_1 += mweights[(*it).first];
    }
    map<string,double>::const_iterator it1;
    double entropy = 0.;
    string aconsensus; 
    double aconservation = -100; 
    //    cout << "-----" << endl;
    for(it1=maa.begin(); it1!=maa.end();++it1){
      //      double prob = (*it1).second/nTotal;
      //      entropy += -prob*log2(prob); // Shannon 
      string aa = (*it1).first;
      double probBckgr = mBckgrProb[conv2CAP(aa)];
      double prob = (*it1).second/nTotal_1;
      //      if(aa!="." && aa!="-"){
      if(inAlphabet(aa)==1){
        entropy += prob*log2(prob/probBckgr); // relative
      } else {
        entropy += 0.;
      }
      //      cout << "       S: " << entropy << "    A: " << prob/probBckgr << "   p: " << prob <<  "   " << prob*log2(prob/probBckgr) << "  " << probBckgr << " " << (*it1).first << endl; 
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
        if(j>=pfamData.ifirst && j<=pfamData.isecond){
          line.replace(97-pfamData.alnName.size(),pfamData.alnName.size(),pfamData.alnName); 
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

void print_help(){
  cout << "\nWRONG NUMBER OF PARAMETERS: \n" << endl;
  //  cout << "./conScore name_of_sequence_in_alignment multiple_alignment_file \n" << endl;
  cout << "./conScore pfam.scan.txt [ stride.txt ]\n" << endl;  
  exit(0);
}


int main(int argc, char*argv[]){

  if(argc!=2 && argc!=3) print_help(); 

  ifstream in(argv[1]);
  if(in.is_open()==false){
    cout << "no pfam scan file " << argv[1] << endl;
    exit(0);
  }

  // read PfamScan file
  vector<aln> valn = getPfamScanData(argv[1]);
  for(int i=0; i<valn.size(); i++){
    cout << valn[i].ifirst << " " << valn[i].isecond << " " << valn[i].alnName << " " << valn[i].alnID << endl;
  }


  // read stride file 
  stride strideData;
  strideData.iflag = 0;
  if(argc==3){
    strideData.iflag = 1;
    strideData.strideFile = argv[2];
    strideData.init(); 
  }

  for(int i=0; i<valn.size(); i++){
    computeConservation(valn[i], strideData); 
  }

  /*
  string line; 
  getline(in,line); 
  vector<string> vparsed = parse_line(line,' ');
  if(vparsed[1]!="STOCKHOLM"){
    cout << "not a STOCKHOLM format" << endl; 
    exit(0);
  }

  map<string,string> mseqs, mqual;
  while (getline(in,line)){
    vector<string> vparsed = parse_line(line,' ');
    if(vparsed.size()>0 && vparsed[0].substr(0,1)!="#" && vparsed[0].substr(0,1)!="/"){
      mseqs[vparsed[0]] += vparsed[1];
    }
    if(vparsed.size()==4 && vparsed[0]=="#=GR" && vparsed[2]=="PP"){
      mqual[vparsed[1]] += vparsed[3];
    }
  }

  if(mqual.size()==0){
    cout << "No posterior probabilities" << endl; 
    exit(0);
  }
  int ifirst = 0; 
  while(mqual[argv[1]].substr(ifirst,1)!="*") ifirst++;  
  int ilast = mqual[argv[1]].size()-1; 
  while(mqual[argv[1]].substr(ilast,1)!="*") ilast--;

  map<string,double> mweights = getWeights(mseqs,ifirst,ilast);

  string querySeq = mseqs[argv[1]];

  vector<double> ventropy;

  //  for(int i=ifirst; i<=ilast; i++){
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
    for(it1=maa.begin(); it1!=maa.end();++it1){
      double prob = (*it1).second/nTotal;
      entropy += -prob*log2(prob);
    }
    ventropy.push_back(entropy);
  }

  cout << "SEQ: " << querySeq.size() << " " << ventropy.size() << endl; 

  for(int i=0; i<querySeq.size(); i++){
    string aa = querySeq.substr(i,1);
    if(aa!="-" && aa!="."){
      cout << aa << " " << ventropy[i] << endl; 
    }
  }
*/
  /*
  string seq =  mseqs["Vt"];
  for(int i=ifirst; i<ilast; i++){
    cout << i << " " << seq.substr(i,1) << " " << ventropy[i-ifirst] <<  endl;
  }
  */
  
  /*
  map<string,double>::const_iterator it;
  for(it=mweights.begin(); it != mweights.end(); ++it){
    cout << (*it).first << " " << (*it).second << endl; 
  }
  */

}

