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
  cout << "./stripAligned multiple_alignment_file output_fasta_file\n" << endl; 
  exit(0);
}


int main(int argc, char*argv[]){

 if(argc!=3){
  print_help(); 
  exit(0);
 }

 filebuf outf; 
 outf.open(argv[2],ios::out);
 ostream os(&outf); 


 ifstream in(argv[1]); 
 if(in.is_open()==false){
   cout << "Fatal error: no fasta file " << argv[1] << endl;
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
 vector<string> seq; 
 int icount=0; 
 while (getline(in,line)){
   if(line.substr(0,1)==">"){
     if(sname!="000000"){
       stringstream sseq; 
       for(int i=0; i<seq.size(); i++){
	 sseq << seq[i];
         if((i+1)%60==0) sseq << "\n"; 
       }
       os << sname << "\n"; 
       os << sseq.str() << "\n";
       seq.erase(seq.begin(),seq.end());
     }
     sname = line; 
   } else {
     for(int i=0; i<line.size(); i++){
       string letter = line.substr(i,1); 
       if(maa[letter]==1) seq.push_back(line.substr(i,1)); 
     }
   }
 }

 stringstream sseq;
 for(int i=0; i<seq.size(); i++){
   sseq << seq[i];
   if((i+1)%60==0) sseq << "\n";
 }
 os << sname << "\n";
 os << sseq.str() << "\n";

 } else if (AlignmentType == "STOCKHOLM"){

 map<string,int> mseq; 
 map< string,vector<string> > mvseq; 

 while (getline(in,line)){
   vector<string> vparsed = parse_line(line,' ');
   if(vparsed[0].substr(0,1)!="#" && vparsed[0]!="//"){
     string seqname = vparsed[0];
     string record = vparsed[1];
     for(int i=0; i<record.size(); i++){
       string letter = record.substr(i,1);
       //       if(maa[letter]==1) mvseq[seqname].push_back(record.substr(i,1));
       if(letter!="." && letter!="-") mvseq[seqname].push_back(record.substr(i,1));
     }
   }   
 }
 map< string,vector<string> >::iterator itr; 
 for(itr=mvseq.begin(); itr!=mvseq.end(); itr++){
   string sname = (*itr).first;
   vector<string> record = (*itr).second; 
   stringstream sseq;
   for(int i=0; i<record.size(); i++){
     sseq << record[i];
     if((i+1)%60==0) sseq << "\n";
   }
   os << ">" << sname << "\n";
   os << sseq.str() << "\n";
 }


 }

}
