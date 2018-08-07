#include<iostream>
#include<fstream>
#include<sstream>
#include<vector>
#include<string>
#include<map>
#include<cmath>
#include <stdlib.h>
using namespace std; 

struct coord{
  coord(){};
  coord(double x,double y,double z):x(x),y(y),z(z){};
  double x;
  double y;
  double z;
};

struct advcoord{
  advcoord(){};
  advcoord(string number):name(number){};
  advcoord(string name,int resSeq,coord dim):name(name),resSeq(resSeq),dim(dim){};
  advcoord(string name,string resName,string chainName,int resSeq,coord dim,double tempFactor):
    name(name),resName(resName),chainName(chainName),resSeq(resSeq),dim(dim),tempFactor(tempFactor){};
  advcoord(string name,string altLoc, string resName,string chainName,int resSeq,coord dim,double occupancy, double tempFactor):
    name(name),altLoc(altLoc),resName(resName),chainName(chainName),resSeq(resSeq),dim(dim),occupancy(occupancy), tempFactor(tempFactor){};
  advcoord(string chainName, int resSeq, string altLoc):chainName(chainName),resSeq(resSeq),altLoc(altLoc){};
  advcoord(string chainName, string resName, int resSeq, string altLoc):chainName(chainName),resName(resName),resSeq(resSeq),altLoc(altLoc){};
  bool operator!=(advcoord);
  bool operator==(advcoord);
  void operator=(const advcoord& coord); 
  string name;
  string altLoc; 
  string resName;
  string chainName;
  int resSeq;
  coord dim;
  double occupancy; 
  double tempFactor; 
};

bool advcoord::operator!=(advcoord test){
  if(name==test.name && resName==test.resName && chainName==test.chainName){
    return false; 
  } else {
    return true; 
  }
}

bool advcoord::operator==(advcoord test){
  if(name==test.name && resName==test.resName && chainName==test.chainName){
    return true; 
  } else {
    return false; 
  }
}

void advcoord::operator=(const advcoord& coord){
  name=coord.name;
  altLoc=coord.altLoc;
  resName=coord.resName;
  chainName=coord.chainName;
  resSeq=coord.resSeq;
  dim.x = coord.dim.x;
  dim.y = coord.dim.y; 
  dim.z = coord.dim.z; 
  occupancy = coord.occupancy;
  tempFactor = coord.tempFactor;
}

void remove_blank(string& name){
  string new_name; 
  for(int i=0; i<name.length(); i++){
    char c = name[i]; 
    if(c!=' ') new_name.push_back(c);
  }
  name = new_name;   
};

vector< vector<advcoord> > getDataResidues(string pdb_name){

  cout << "INSIDE" << endl; 

  vector< vector<advcoord> > vresult; 

  ifstream in(pdb_name.c_str());
  if(in.is_open()==false){
    cout << "Fatal error from getData: no pdb file " << pdb_name << endl;
    exit(0);
  }

  vector<advcoord> vatoms;
  map<string,int> atomap;
  int resSeqPrev = -1000;
  string chainPrev = "0000000000000";
  string iCodePrev = "0000000000000";
  int modelID = 1;
  string line; 
  while (getline(in,line)){
    string title = line.substr(0,5);
    remove_blank(title);
    if(title=="MODEL"){
      int lsize = line.size();
      modelID = atoi(line.substr(5,lsize-6).c_str());
    }
    if(title=="ATOM"){
      int serial = atoi(line.substr(6,5).c_str()); 
      string name = line.substr(12,4);
      remove_blank(name);
      string altLoc = line.substr(16,1);
      remove_blank(altLoc); 
      string resName = line.substr(17,3);
      remove_blank(resName);
      string chainID = line.substr(21,1);
      remove_blank(chainID);  
      int resSeq = atoi(line.substr(22,4).c_str()); 
      string iCode = line.substr(26,1);
      remove_blank(iCode); 
      double x = atof(line.substr(30,8).c_str());
      double y = atof(line.substr(38,8).c_str());
      double z = atof(line.substr(46,8).c_str());
      double occupancy = atof(line.substr(54,6).c_str());
      double tempFactor = atof(line.substr(60,9).c_str());
      if(resSeq!=resSeqPrev || iCode!=iCodePrev || chainID!=chainPrev){
        if(resSeqPrev!=-1000){
	  vresult.push_back(vatoms);
	  vatoms.erase( vatoms.begin(), vatoms.end());
        }
        atomap.erase(atomap.begin(),atomap.end());
        resSeqPrev = resSeq;
        chainPrev = chainID;
        iCodePrev = iCode;
      }
      if(modelID==1 && atomap[name]==0){
        atomap[name] = 1; 
        vatoms.push_back(advcoord(name,iCode,resName,chainID,resSeq,coord(x,y,z),1.00,tempFactor));
      }
    }
  }
  vresult.push_back(vatoms); 

  return vresult; 

}

vector<advcoord> getData(string pdb_name){

  ifstream in(pdb_name.c_str());
  if(in.is_open()==false){
    cout << "Fatal error from getData: no pdb file " << pdb_name << endl;
    exit(0);
  }

  vector<advcoord> vatoms;
  map<string,int> atomap;
  int resSeqPrev = -1000;
  string chainPrev = "0000000000000";
  string iCodePrev = "0000000000000";
  int modelID = 1;
  string line; 
  while (getline(in,line)){
    string title = line.substr(0,5);
    remove_blank(title);
    if(title=="MODEL"){
      int lsize = line.size();
      modelID = atoi(line.substr(5,lsize-6).c_str());
    }
    if(title=="TER"){
      //      vatoms.push_back(advcoord("-1"));
    }
    if(title=="ATOM"){
      int serial = atoi(line.substr(6,5).c_str()); 
      string name = line.substr(12,4);
      remove_blank(name);
      string altLoc = line.substr(16,1);
      remove_blank(altLoc); 
      string resName = line.substr(17,3);
      remove_blank(resName);
      string chainID = line.substr(21,1);
      remove_blank(chainID);  
      int resSeq = atoi(line.substr(22,4).c_str()); 
      string iCode = line.substr(26,1);
      remove_blank(iCode); 
      double x = atof(line.substr(30,8).c_str());
      double y = atof(line.substr(38,8).c_str());
      double z = atof(line.substr(46,8).c_str());
      double occupancy = atof(line.substr(54,6).c_str());
      double tempFactor = atof(line.substr(60,9).c_str());
      if(chainID!=chainPrev && vatoms.size()!=0 && vatoms.back().name!="-1") vatoms.push_back(advcoord("-1"));
      if(resSeq!=resSeqPrev || iCode!=iCodePrev || chainID!=chainPrev){
        atomap.erase(atomap.begin(),atomap.end());
        resSeqPrev = resSeq;
        chainPrev = chainID;
        iCodePrev = iCode;
      }
      if(modelID==1 && atomap[name]==0){
        atomap[name] = 1; 
        vatoms.push_back(advcoord(name,iCode,resName,chainID,resSeq,coord(x,y,z),1.00,tempFactor));
      }
    }
  }
  if(vatoms.back().name!="-1") vatoms.push_back(advcoord("-1"));
  return vatoms;
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



void write_in_pdb_format(ostream& os, vector<advcoord> rpcoord){

  if(rpcoord.size()==0) {
    return; 
  }

  for(int i=0; i<rpcoord.size(); i++){
    string atomstr(80,' ');
    atomstr.replace(0,6,"ATOM  ");
    stringstream serial; 
    serial << i+1;   
    atomstr.replace(11-serial.str().size(),serial.str().size(),serial.str());
    //    atomstr.replace(12,4,"CA");
    if(rpcoord[i].name.size()<4){
      atomstr.replace(13,3,rpcoord[i].name); 
    }else{
      atomstr.replace(12,4,rpcoord[i].name);
    }
    atomstr.replace(17,3,rpcoord[i].resName);
    atomstr.replace(21,1,rpcoord[i].chainName); 

    stringstream sresSeq;
    sresSeq << rpcoord[i].resSeq;
    atomstr.replace(26-sresSeq.str().size(),sresSeq.str().size(),sresSeq.str());  
    atomstr.replace(26,1,rpcoord[i].altLoc);
    string stx = conv_double_to_str(rpcoord[i].dim.x,3);
    atomstr.replace(38-stx.size(),stx.size(),stx); 
    string sty = conv_double_to_str(rpcoord[i].dim.y,3);
    atomstr.replace(46-sty.size(),sty.size(),sty);
    string stz = conv_double_to_str(rpcoord[i].dim.z,3);
    atomstr.replace(54-stz.size(),stz.size(),stz);
    string soccup = conv_double_to_str(1.00,2);
    atomstr.replace(56,4,soccup);
    string temp = conv_double_to_str(rpcoord[i].tempFactor,2);
    atomstr.replace(66-temp.size(),temp.size(),temp);

    os << atomstr << "\n"; 
  
  }
  string atomstr(80,' ');
  atomstr.replace(0,6,"TER   "); 
  stringstream serial; 
  serial << rpcoord.size()+1;   
  atomstr.replace(11-serial.str().size(),serial.str().size(),serial.str());
  atomstr.replace(17,3,rpcoord.back().resName);
  atomstr.replace(21,1,rpcoord.back().chainName); 
  stringstream sresSeq;
  sresSeq << rpcoord.back().resSeq;
  atomstr.replace(26-sresSeq.str().size(),sresSeq.str().size(),sresSeq.str());  

  os << atomstr << "\n"; 
}


void write_pdb_as_orig(string ostrfile, vector<advcoord> vdata){
  filebuf outf; 
  outf.open(ostrfile.c_str(),ios::out);
  ostream os(&outf); 

  vector<advcoord> vchain; 
  for(int i=0; i<vdata.size(); i++){
    if(vdata[i].name!="-1"){
      vchain.push_back(vdata[i]);
    } else {
      write_in_pdb_format(os, vchain);
      vchain.erase(vchain.begin(),vchain.end());
    }
  }
  outf.close();
}

void print_help(){
  cout << "\nWRONG NUMBER OF PARAMETERS: \n" << endl; 
  cout << "./split pdb_file split_site \n" << endl; 
  cout << "split is performed after provided split site\n" << endl; 
  cout << "split_site is given in the naitive protein residue numbering scheme \n" << endl; 
  exit(0);
}


int main(int argc, char*argv[]){

  if(argc!=3) print_help(); 


  vector<advcoord> vchain = getData(argv[1]);
  vector<advcoord> v1ch; 
  for(int i=0; i<vchain.size(); i++){
    v1ch.push_back(vchain[i]); 
    if(vchain[i].name=="-1") break;
  }

  int isplit = atoi(argv[2]);
  vector<advcoord> vpart1, vpart2; 
  for(int i=0; i<v1ch.size(); i++){
    if(v1ch[i].name!="-1"){
      if(v1ch[i].resSeq <= isplit){
      vpart1.push_back(v1ch[i]); 
      } else {
      vpart2.push_back(v1ch[i]); 
      }
    }
  }
  vpart1.push_back(advcoord("-1")); 
  vpart2.push_back(advcoord("-1"));
 
  //  cout << vpart1.size() << " " << vpart2.size() << endl;

  //  write_pdb_as_orig("whole.pdb",v1ch);
  write_pdb_as_orig("part1.pdb",vpart1);
  write_pdb_as_orig("part2.pdb",vpart2);
  
}
