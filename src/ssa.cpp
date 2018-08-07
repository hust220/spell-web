#include<iostream>
#include<fstream>
#include<sstream>
#include<vector>
#include<string>
#include<map>
#include<cmath>
#include <stdlib.h>
using namespace std; 

void print_help(){
  cout << "\nWRONG NUMBER OF PARAMETERS\n" << endl; 
  cout << "./ssa sride.txt\n" << endl; 
} 

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


int main(int argc, char*argv[]){

  if(argc!=2){
    print_help(); 
    exit(0); 
  }

  ifstream in(argv[1]);
  if(in.is_open()==false){
    cout << "no data file " << argv[1] << endl;
    exit(0);
  }

  double ssa; 
  string line; 
  while (getline(in,line)){
    vector<string> vfields = parse_line(line,' ');
    if(vfields[0]=="ASG"){
    ssa += atof(vfields[9].c_str());
    }
  }
  cout << ssa << endl; 

}
