#include<iostream>
#include<fstream>
#include<sstream>
#include<vector>
#include<string>
#include<map>
#include<cmath>
#include <stdlib.h>
using namespace std; 


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


void print_help(){
  cout << "\nWRONG NUMBER OF PARAMETERS: \n" << endl; 
  cout << "./checkAlignmentFormat multiple_alignment_file\n" << endl; 
  exit(0);
}


int main(int argc, char*argv[]){

 if(argc!=2){
  print_help(); 
  exit(0);
 }

 filebuf outf; 
 outf.open(argv[2],ios::out);
 ostream os(&outf); 


 ifstream in(argv[1]); 
 if(in.is_open()==false){
   cout << "Fatal error: no alignment file " << argv[1] << endl;
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
 maa["U"]=1; 
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
   //   cout << "FASTA format is recognized" << endl; 
   cout << "1" << endl;
 } else {
   vector<string> vparsed = parse_line(line,' '); 
   if(vparsed.size()>=2 && vparsed[0]=="#" && vparsed[1]=="STOCKHOLM"){
     AlignmentType = "STOCKHOLM"; 
     //     cout << "STOCKHOLM format is recognized" << endl;
     cout << "2" << endl;
   } else {
     //     cout << "Alignment type is not recognized ..." << endl; 
     cout << "0" << endl; 
   }
 }

}
