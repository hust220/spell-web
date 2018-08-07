#include<iostream>
#include<fstream>
#include<sstream>
#include<vector>
#include<string>
#include<map>
#include<cmath>
#include <algorithm>
#include <stdlib.h>
#include "TROOT.h"
#include "TStyle.h"
#include "TPad.h"
#include "TFile.h"
#include "TTree.h"
#include "TH1F.h"
#include "TF1.h"
#include "TH2F.h"
#include "TProfile.h"
#include "TObjArray.h"
#include "TGraph.h"
#include "TPaveText.h"
#include "TCanvas.h"
#include "TArrow.h"
#include "TLatex.h"
using namespace std; 

void print_help(){
  cout << "\nWRONG NUMBER OF PARAMETERS\n" << endl; 
  cout << "./ssa sride.txt  [split_energy.txt] [pdb_file]\n" << endl; 
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

struct ares{
  ares(int iRes,double ssa):iRes(iRes),ssa(ssa){};
  ares(int iRes,double ssa,string ssType):iRes(iRes),ssa(ssa),ssType(ssType){};
  ares(int inum, int iRes,double ssa,string ssType):inum(inum),iRes(iRes),ssa(ssa),ssType(ssType){};
  ares(int inum, int iRes,double ssa):inum(inum),iRes(iRes),ssa(ssa){};
  ares(int inum, int iRes,double ssa, double cons):inum(inum),iRes(iRes),ssa(ssa),cons(cons){};
  int inum; 
  int iRes; 
  double ssa; 
  double cons;
  string ssType;  
};

struct asite{
  asite(ares theRes1, ares theRes2):theRes1(theRes1),theRes2(theRes2){};
  ares theRes1; 
  ares theRes2;
  int itight; 
  double splitE;
};

void splitEnergySinglePlot(char* strideFile, char* splitFile, vector<asite>& vsplit){


  ifstream inSplit(splitFile);
  if(inSplit.is_open()==false){
    cout << "no data file " << splitFile << endl;
    exit(0);
  }

  ifstream inStride(strideFile);
  if(inStride.is_open()==false){
    cout << "no data file " << strideFile << endl;
    exit(0);
  }

  vector<int> vresi;
  vector<double> venergy;
  string line;
  while (getline(inSplit,line)){
    vector<string> vfields = parse_line(line,' ');
    vresi.push_back(atoi(vfields[0].c_str()));
    venergy.push_back(atof(vfields[4].c_str()));
  }
  int nbins = vresi.back() - vresi[0] + 1;

  TCanvas *c1 = new TCanvas("c1", "",69,22,554,813);

  //  TCanvas *c1 = new TCanvas("c1", "",10,32,1183,1787);
  c1->SetRightMargin(0.053554);
  c1->SetBottomMargin(0.13554);
  gStyle->SetOptStat(0);
  c1->ToggleEventStatus();
  c1->Range(0,0,1,1);
  c1->SetBorderSize(2);
  c1->SetFrameFillColor(0);
  c1->SetFillColor(0);
  gStyle->SetFrameBorderMode(0);
  gStyle->SetFrameFillColor(0);
  gStyle->SetPalette(1);
  gStyle->SetCanvasColor(0);
  gStyle->SetPalette(1);
  gStyle->SetCanvasColor(0);
  gStyle->SetFillColor(0);
  gStyle->SetStatBorderSize(1);
  gPad->SetFillColor(0);

  TH1F* hE = new TH1F("hE","",nbins,vresi[0]-0.5,vresi.back()+0.5);
  for(int i=0; i<nbins; i++){
    hE->SetBinContent(i+1,venergy[i]);
  }
  //  hE->Draw();
  //  hE->SetLineColor(2);
  //  hE->SetLineWidth(3);


  TH1F* hSAA = new TH1F("hSAA","",nbins,vresi[0]-0.5,vresi.back()+0.5);
  TH1F* hCons = new TH1F("hCons","",nbins,vresi[0]-0.5,vresi.back()+0.5);

  while (getline(inStride,line)){
    vector<string> vfields = parse_line(line,' ');
    if(vfields[0]=="ASG"){
      int iRes = atoi(vfields[3].c_str());
      double ssa = atof(vfields[9].c_str());
      double cons = atof(vfields[13].c_str());
      if(cons==100) cons=4;
      if(iRes>=vresi[0] && iRes<=vresi.back()){
	hSAA->SetBinContent(iRes-vresi[0]+1,ssa);
	hCons->SetBinContent(iRes-vresi[0]+1,cons);
      }
    }
  }

  Float_t small = 1e-5;
  Float_t medium = 0.053554;

  TPad *c1_1 = new TPad("c1_1", "c1_1",0.01,0.51,0.99,0.99);
  c1_1->Draw();
  c1_1->cd();
  c1_1->SetFillColor(0);
  c1_1->SetBorderMode(0);
  c1_1->SetBorderSize(0);
  c1_1->SetFrameBorderMode(0);
  //  c1_1->SetBottomMargin(small);
  c1_1->SetRightMargin(medium);

  hE->Draw();
  c1_1->Modified();
  c1->cd();  


  TPad *c1_2 = new TPad("c1_2", "c1_2",0.01,0.26,0.99,0.51);
  c1_2->Draw();
  c1_2->cd();
  c1_2->SetFillColor(0);
  c1_2->SetBorderMode(0);
  c1_2->SetBorderSize(0);
  c1_2->SetFrameBorderMode(0);
  //  c1_2->SetTopMargin(small);
  //  c1_2->SetBottomMargin(small);  
  c1_2->SetRightMargin(medium);

  hSAA->Draw();
  c1_2->Modified();
  c1->cd();


  TPad *c1_3 = new TPad("c1_3", "c1_3",0.01,0.01,0.99,0.25);
  c1_3->Draw();
  c1_3->cd();
  c1_3->SetFillColor(0);
  c1_3->SetBorderMode(0);
  c1_3->SetBorderSize(0);
  c1_3->SetFrameBorderMode(0);
  //  c1_3->SetTopMargin(small);
  c1_3->SetRightMargin(medium);

  cout << "K2" << endl;

  hCons->Draw();
  c1_3->Modified();
  c1->cd();

  c1->Update();
  c1->SaveAs("plot.png");

}

void splitEnergy(char* strideFile, char* splitFile, vector<asite>& vsplit){

  vector<asite> vsplit_temp;

  ifstream inSplit(splitFile);
  if(inSplit.is_open()==false){
    cout << "no data file " << splitFile << endl;
    exit(0);
  }

  vector<int> vresi;
  vector<double> venergy;
  string line;
  while (getline(inSplit,line)){
    vector<string> vfields = parse_line(line,' ');
    vresi.push_back(atoi(vfields[0].c_str()));
    venergy.push_back(atof(vfields[4].c_str()));
  }
  int nbins = vresi.back() - vresi[0] + 1;
  
  // split energy plot

  Float_t small = 1e-5;
  Float_t medium = 0.053554;

  TCanvas *c1 = new TCanvas("c1", "",10,32,1183,787);
  c1->SetRightMargin(small);
  c1->SetTopMargin(small);
  //  c1->SetRightMargin(0.053554);
  //  c1->SetBottomMargin(0.13554);
  gStyle->SetOptStat(0);
  c1->ToggleEventStatus();
  c1->Range(0,0,1,1);
  c1->SetBorderSize(2);
  c1->SetFrameFillColor(0);
  c1->SetFillColor(0);
  gStyle->SetFrameBorderMode(0);
  gStyle->SetFrameFillColor(0);
  gStyle->SetPalette(1);
  gStyle->SetCanvasColor(0);
  gStyle->SetPalette(1);
  gStyle->SetCanvasColor(0);
  gStyle->SetFillColor(0);
  gStyle->SetStatBorderSize(1);
  gPad->SetFillColor(0);

  TH1F* hE = new TH1F("hE","",nbins,vresi[0]-0.5,vresi.back()+0.5); 
  for(int i=0; i<nbins; i++){
    hE->SetBinContent(i+1,venergy[i]);
  }
  hE->Draw();
  hE->SetLineColor(1); 
  hE->SetLineWidth(3);
  TAxis* axisx = hE->GetXaxis();
  axisx->SetTitleSize(0.05);
  //  axisx->SetTitle("Residue Number");
  axisx->SetLabelSize(0.05);

  TAxis* axisy = hE->GetYaxis();
  axisy->SetTitleSize(0.05);
  axisy->SetTitle("Split Energy");

  //  gPad->SetGrid();
  c1->Update();

  TLatex Tl;
  vector<TLine*> vl;
  vector<TArrow*> va;
  double rangeY = fabs(gPad->GetUymax() - gPad->GetUymin()); 
  double arrowSize = rangeY/4;
  for(int i=0; i<vsplit.size(); i++){
    double cx = (double)vsplit[i].theRes1.iRes;
    double cy = venergy[vsplit[i].theRes1.iRes-vresi[0]]; 
    double cy1 = 0; 
    if(cy+arrowSize<gPad->GetUymax()){
      cy1 = cy+arrowSize; 
    } else {
      cy1 = cy-arrowSize;
    }
    double xmin = hE->GetXaxis()->GetXmin();
    double xmax = hE->GetXaxis()->GetXmax();
    cout << "TLine: " << cx << " " << cy << " " << cx << " " << cy1 << endl;
    if(cx>xmin && cx<xmax){
      vsplit_temp.push_back(vsplit[i]);
      va.push_back(new TArrow(cx,cy1,cx,cy,0.05,">"));
      vl.push_back(new TLine(cx+0.5,gPad->GetUymin(),cx+0.5,gPad->GetUymax()));
      stringstream ss; 
      ss << vsplit[i].theRes1.iRes << "-" << vsplit[i].theRes2.iRes;
      Tl.DrawLatex(cx+1,gPad->GetUymin()+rangeY*6/7,ss.str().c_str());
    }
  }
  for(int i=0; i<va.size(); i++){
    /*
    va[i]->SetLineWidth(3);
    va[i]->SetAngle(20);
    va[i]->Draw();
    */
    vl[i]->SetLineWidth(3); 
    vl[i]->SetLineColor(2);
    vl[i]->Draw();
  }

  c1->Update();
  c1->SaveAs("splitEnergy.png");


  // solvent accessibility area plot                                                                                                                                                        

  ifstream inStride(strideFile);
  if(inStride.is_open()==false){
    cout << "no data file " << strideFile << endl;
    exit(0);
  }
 

  TCanvas *c2 = new TCanvas("c2", "",10,32,1183,787);
  //  TCanvas *c2 = new TCanvas("c2", "",10,32,1183,500);
  c2->SetRightMargin(small);
  c2->SetTopMargin(small);
  //  c2->SetRightMargin(0.053554);
  //  c2->SetBottomMargin(0.13554);
  gStyle->SetOptStat(0);
  c2->ToggleEventStatus();
  c2->Range(0,0,1,1);
  c2->SetBorderSize(2);
  c2->SetFrameFillColor(0);
  c2->SetFillColor(0);
  gStyle->SetFrameBorderMode(0);
  gStyle->SetFrameFillColor(0);
  gStyle->SetPalette(1);
  gStyle->SetCanvasColor(0);
  gStyle->SetPalette(1);
  gStyle->SetCanvasColor(0);
  gStyle->SetFillColor(0);
  gStyle->SetStatBorderSize(1);
  gPad->SetFillColor(0);


  TH1F* hSAA = new TH1F("hSAA","",nbins,vresi[0]-0.5,vresi.back()+0.5);
  TH1F* hCons = new TH1F("hCons","",nbins,vresi[0]-0.5,vresi.back()+0.5);

  while (getline(inStride,line)){
    vector<string> vfields = parse_line(line,' ');
    if(vfields[0]=="ASG"){
    int iRes = atoi(vfields[3].c_str());
    double ssa = atof(vfields[9].c_str());
    double cons = atof(vfields[13].c_str());
    if(cons==100) cons=4;
    if(iRes>=vresi[0] && iRes<=vresi.back()){
      hSAA->SetBinContent(iRes-vresi[0]+1,ssa);
      hCons->SetBinContent(iRes-vresi[0]+1,cons);
    }
    }
  }

  hSAA->Draw();
  hSAA->SetLineColor(1);
  hSAA->SetLineWidth(3);
  hSAA->SetFillColor(1);
  TAxis* axisx1 = hSAA->GetXaxis();
  axisx1->SetTitleSize(0.05);
  axisx1->SetLabelSize(0.05);
  //  axisx1->SetTitle("Residue Number");

  TAxis* axisy1 = hSAA->GetYaxis();
  axisy1->SetTitleSize(0.05);
  axisy1->SetTitle("SAA");

  c2->Update();

  double rangeY1 = fabs(gPad->GetUymax() - gPad->GetUymin());
  TLatex Tl1;
  vector<TLine*> vl1;
  for(int i=0; i<vsplit.size(); i++){
    double cx = (double)vsplit[i].theRes1.iRes;
    double xmin = hE->GetXaxis()->GetXmin();
    double xmax = hE->GetXaxis()->GetXmax();
    if(cx>xmin && cx<xmax){
      vl1.push_back(new TLine(cx+0.5,gPad->GetUymin(),cx+0.5,gPad->GetUymax()));
      stringstream ss;
      ss << vsplit[i].theRes1.iRes << "-" << vsplit[i].theRes2.iRes;
      Tl1.DrawLatex(cx+1,gPad->GetUymin()+rangeY1*6/7,ss.str().c_str());
    }
  }

  for(int i=0; i<vl1.size(); i++){
    vl1[i]->SetLineWidth(3);
    vl1[i]->SetLineColor(2);
    vl1[i]->Draw();
  }


  c2->Update();
  c2->SaveAs("saa.png");

  TCanvas *c3 = new TCanvas("c3", "",10,32,1183,787);
  //  TCanvas *c3 = new TCanvas("c3", "",10,32,1183,500);
  c3->SetRightMargin(small);
  c3->SetTopMargin(small);
  //  c2->SetRightMargin(0.053554);                                                                                                                                                          
  //  c2->SetBottomMargin(0.13554);                                                                                                                                                          
  gStyle->SetOptStat(0);
  c3->ToggleEventStatus();
  c3->Range(0,0,1,1);
  c3->SetBorderSize(2);
  c3->SetFrameFillColor(0);
  c3->SetFillColor(0);
  gStyle->SetFrameBorderMode(0);
  gStyle->SetFrameFillColor(0);
  gStyle->SetPalette(1);
  gStyle->SetCanvasColor(0);
  gStyle->SetPalette(1);
  gStyle->SetCanvasColor(0);
  gStyle->SetFillColor(0);
  gStyle->SetStatBorderSize(1);
  gPad->SetFillColor(0);

  hCons->Draw();
  hCons->SetLineColor(1);
  hCons->SetLineWidth(3);
  hCons->SetFillColor(1);
  TAxis* axisx2 = hCons->GetXaxis();
  axisx2->SetTitleSize(0.05);
  axisx2->SetLabelSize(0.05);
  axisx2->SetTitle("Residue Number");                                                                                                                                                    

  TAxis* axisy2 = hCons->GetYaxis();
  axisy2->SetTitleSize(0.05);
  axisy2->SetTitle("Cons");

  c3->Update();

  double rangeY2 = fabs(gPad->GetUymax() - gPad->GetUymin());
  TLatex Tl2;
  vector<TLine*> vl2;
  for(int i=0; i<vsplit.size(); i++){
    double cx = (double)vsplit[i].theRes1.iRes;
    double xmin = hE->GetXaxis()->GetXmin();
    double xmax = hE->GetXaxis()->GetXmax();
    if(cx>xmin && cx<xmax){
      vl2.push_back(new TLine(cx+0.5,gPad->GetUymin(),cx+0.5,gPad->GetUymax()));
      stringstream ss;
      ss << vsplit[i].theRes1.iRes << "-" << vsplit[i].theRes2.iRes;
      Tl2.DrawLatex(cx+1,gPad->GetUymin()+rangeY2*6/7,ss.str().c_str());
    }
  }

  for(int i=0; i<vl2.size(); i++){
    vl2[i]->SetLineWidth(3);
    vl2[i]->SetLineColor(2);
    vl2[i]->Draw();
  }



  c3->Update();
  c3->SaveAs("cons.png");

  vsplit = vsplit_temp;

}

struct adist{
  adist(int iRes1, int iRes2, double dist): iRes1(iRes1), iRes2(iRes2), dist(dist){};
  double dist; 
  int iRes1; 
  int iRes2;
}; 

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
        if(name=="CA") vatoms.push_back(advcoord(name,iCode,resName,chainID,resSeq,coord(x,y,z),1.00,tempFactor));
      }
    }
  }
  if(vatoms.back().name!="-1") vatoms.push_back(advcoord("-1"));
  return vatoms;
}

bool sortfunction (adist d1, adist d2) { return (d1.dist<d2.dist); }

bool sortsplit (asite s1, asite s2) { return ((s1.theRes1.ssa+s1.theRes2.ssa)>(s2.theRes1.ssa+s2.theRes2.ssa)); }

bool sortsplitFinal (asite s1, asite s2) {
  if(s1.itight > s2.itight) return true;
  if(s1.itight < s2.itight) return false;
  if(s1.splitE>s2.splitE) return true;
  if(s1.splitE<s2.splitE) return false;
  return false;
}

bool sortenergy(pair<int,double> p1, pair<int,double> p2){return (p1.second<p2.second); }

bool sortindex(pair<int,double> p1, pair<int,double> p2){return (p1.first<p2.first); }

bool sortranges(pair<int,int> p1, pair<int,int> p2){return (p1.first<p2.first); }

vector< pair<int, double> > averageEnergy(vector< pair<int, double> > venergy, int nWindowAve,int nItr){
 vector< pair<int, double> > vAverageEnergy = venergy;
 vector< pair<int, double> > vAverageEnergyTemp;
 for(int iItr = 0; iItr<nItr; iItr++){
  vAverageEnergyTemp.erase(vAverageEnergyTemp.begin(),vAverageEnergyTemp.end());
  for(int i=0; i<vAverageEnergy.size(); i++){
    double aveVal = 0.;
    int jmin = max(0,i - nWindowAve);
    int jmax = min(i + nWindowAve,int(vAverageEnergy.size())-1);
    for(int j=jmin; j<jmax+1; j++){
      aveVal += vAverageEnergy[j].second/(jmax-jmin+1);
    }
    vAverageEnergyTemp.push_back(pair<int,double>(vAverageEnergy[i].first,aveVal));
  }
  vAverageEnergy = vAverageEnergyTemp;
 }
 return vAverageEnergy;
}

vector< pair<int, double> > derivative(vector< pair<int, double> > vAverageEnergy){
  vector< pair<int, double> > vDeriv;
  for(int i=1; i<vAverageEnergy.size(); i++){
    double deriv = vAverageEnergy[i].second - vAverageEnergy[i-1].second;
    vDeriv.push_back(pair<int,double>(vAverageEnergy[i].first,deriv));
  }
  return vDeriv;
}

vector< pair<int,int> >  rangesFromDeriv(vector< pair<int, double> > vDeriv, double derivThresh){
  vector< pair<int,int> > vresult;
  vector<int> vindex;
  // ranges with derivatives close to 0 
  for(int i=0; i<vDeriv.size(); i++){
    if(fabs(vDeriv[i].second)<derivThresh) vindex.push_back(vDeriv[i].first);
  }
  int ifirst = vindex[0];
  int isecond = vindex[0];
  for(int i=1; i<vindex.size(); i++){
    if(fabs(vindex[i]-vindex[i-1])>1){
      isecond =vindex[i-1];
      vresult.push_back(pair<int,int>(ifirst,isecond));
      ifirst = vindex[i];
    } 
  }
  vresult.push_back(pair<int,int>(ifirst,vindex.back()));
  // sites with a derivative passing through 0 
  for(int i=1; i<vDeriv.size(); i++){
    if(vDeriv[i].second*vDeriv[i-1].second<0){
      vresult.push_back(pair<int,int>(vDeriv[i-1].first,vDeriv[i-1].first));
    }
  }
  sort(vresult.begin(),vresult.end(),sortranges);

  cout << "----------- INSIDE RANGES -------------- " << vresult.size()  << endl;
  for(int i=0; i<vresult.size(); i++){
    cout << i << " " << vresult[i].first << " " << vresult[i].second << endl;
  }

  return vresult;
}

vector<double> meanEinInterval(vector< pair<int, int> > vrangesDeriv, vector< pair<int,double> > venergy){
  vector<double> vresult;
  for(int i=0; i<vrangesDeriv.size(); i++){
    double mean = 0.;
    for(int j=0; j<venergy.size(); j++){
      if(venergy[j].first>=vrangesDeriv[i].first && venergy[j].first<=vrangesDeriv[i].second){
	mean += venergy[j].second;
        if(venergy[j].first==vrangesDeriv[i].second){
	  vresult.push_back(mean/(vrangesDeriv[i].second-vrangesDeriv[i].first+1));
	  mean = 0.;
	}
      }
    }
  }
  /*
  double mean = 0.; 
  for(int i=0; i<venergy.size(); i++){
    for(int j=0; j<vrangesDeriv.size(); j++){
      if(venergy[i].first>=vrangesDeriv[j].first && venergy[i].first<=vrangesDeriv[j].second){
            mean += venergy[i].second;
            cout << "                       " << venergy[i].second << mean << "  " << j << " " << i << endl;
      }
      if(venergy[i].first==vrangesDeriv[j].second){
        cout << "        ..... " << vrangesDeriv[j].first << " " << vrangesDeriv[j].second << " " << mean << " " << vrangesDeriv[j].second-vrangesDeriv[j].first+1 << endl;
        vresult.push_back(mean/(vrangesDeriv[j].second-vrangesDeriv[j].first+1));
        mean = 0.;
      }
    }
  }
  */
  return vresult;
}

vector< pair<int,int> > mergeIntervals(vector< pair<int,int> > vranges){
  // vranges should be sorted by first coordinate
  vector< pair<int,int> > vrangesMerged;
  vrangesMerged.push_back(vranges[0]);
  for(int i=0; i<vranges.size(); i++){
    if(vranges[i].first>vrangesMerged.back().second){
      vrangesMerged.push_back(vranges[i]);
    } else {
      int jmin = vrangesMerged.back().first;
      int jmax;
      if(vranges[i].second > vrangesMerged.back().second){
	jmax = vranges[i].second;
      } else {
	jmax = vrangesMerged.back().second;
      }
      vrangesMerged.back().first = jmin;
      vrangesMerged.back().second = jmax;
    }
  }
  return vrangesMerged;
}


int main(int argc, char*argv[]){

   filebuf outf;
   string ostrfile = "result.txt";
   outf.open(ostrfile.c_str(),ios::out);
   ostream os(&outf);


   cout << "inside ANALISYS: " << endl;
   cout << "argv[1]: " << argv[1] << endl; 
   cout << "argv[2]: " << argv[2] << endl; 
   cout << "argv[3]: " << argv[3] << endl; 


  if(argc!=2 && argc!=3 && argc!=4){
    print_help(); 
    exit(0); 
  }
 

  ifstream inE(argv[2]);
  if(inE.is_open()==false){  
    cout << "no data file " << argv[2] << endl;
    exit(0); 
  }
  vector< pair<int, double> > venergy;
  map<int,double> menergy;
  string line;
  while (getline(inE,line)){
    vector<string> vfields = parse_line(line,' ');
    int ires = atoi(vfields[0].c_str()); 
    double ene = atof(vfields[4].c_str()); 
    menergy[ires] = ene;
    venergy.push_back(pair<int, double>(ires,ene));
  }
  sort(venergy.begin(),venergy.end(),sortenergy); 

  double minE = 0;
  for(int i=0; i<venergy.size()-1; i++){
    if(venergy[i].second>-1000){
      if(fabs(venergy[i].second-venergy[i+1].second)<2){
        minE = venergy[i].second; 
        break;
      }
    }
  }
  sort(venergy.begin(),venergy.end(),sortindex);

  // SMOOTHED ENERGY PROFILE AND DERIVATIVES

  vector< pair<int, double> > vAverageEnergy = averageEnergy(venergy,1,5); 
  vector< pair<int, double> > vDeriv = derivative(vAverageEnergy);
  double derivThresh = 0.5;
  vector< pair<int,int> > vrangesDeriv = rangesFromDeriv(vDeriv,derivThresh); 
  vector<double> vmeanInIntervals = meanEinInterval(vrangesDeriv,vAverageEnergy);
  double joinTogetherThresh = fabs(minE)/20.;
  vector< pair<int,int> > vrangesExtended;
  for(int i=0; i<vrangesDeriv.size(); i++){
    double dE = 0.;
    int jmin = vrangesDeriv[i].first;
    while(dE<joinTogetherThresh){
      jmin--;
      //      dE = fabs(menergy[jmin]-vmeanInIntervals[i]);
      dE = fabs(menergy[jmin]-menergy[vrangesDeriv[i].first]);
      if(jmin<venergy[0].first) break; 
    }
    jmin++;
    dE=0.;
    int jmax = vrangesDeriv[i].second;
    while(dE<joinTogetherThresh){
      jmax++;
      //      dE = fabs(menergy[jmax]-vmeanInIntervals[i]);
      dE = fabs(menergy[jmax]-menergy[vrangesDeriv[i].second]);
      if(jmax>venergy.back().first) break;
    }
    jmax--;
    vrangesExtended.push_back(pair<int,int>(jmin,jmax));
  }
  sort(vrangesExtended.begin(),vrangesExtended.end(),sortranges);
  vector< pair<int, int> > vrangesExtendedMerged = mergeIntervals(vrangesExtended);

  cout << "======= RESIDUE & ENERGY & AVERAGE ENERGY ======= " << venergy.size() << " " <<  vAverageEnergy.size() << endl; 
  for(int i=0; i<vAverageEnergy.size(); i++){
    cout << vAverageEnergy[i].first << "\t" <<  venergy[i].second << "\t" << vAverageEnergy[i].second  << endl;
  }
  cout << "\n============= VDERIV ================ " << vDeriv.size() << endl;
  for(int i=0; i<vDeriv.size(); i++){
    cout << vDeriv[i].first << "\t" << vDeriv[i].second  << endl;
  }                                                                                                                              
  cout << "\n======= RANGES & EXTENDED RANGES ======= " << vrangesDeriv.size() << " " << vrangesExtended.size() <<  endl; 
  for(int i=0; i<vrangesExtended.size(); i++){
    cout << vrangesDeriv[i].first << " " << vrangesDeriv[i].second << "\t" << 
            vrangesExtended[i].first << " " << vrangesExtended[i].second << endl;
  }
  cout << "\n======= MERGED RANGES =======" << endl;
  for(int i=0; i<vrangesExtendedMerged.size(); i++){
    cout << vrangesExtendedMerged[i].first << "\t" << vrangesExtendedMerged[i].second << endl;
  }

  //remove the first and last ranges if the borders fall out of protein range        
  vector< pair<int,int> > vrangesExtendedTemp;
  for(int i=0;i<vrangesExtendedMerged.size(); i++){
    double dE; 
    if(i==0){
      dE = fabs(menergy[vrangesExtendedMerged[0].first]-menergy[venergy[0].first]);
      if(vrangesExtendedMerged[0].first>venergy[0].first && dE>2*joinTogetherThresh) vrangesExtendedTemp.push_back(vrangesExtendedMerged[0]);
    } else if(i==vrangesExtendedMerged.size()-1){
      dE = fabs(menergy[vrangesExtendedMerged.back().second]-menergy[venergy.back().first]);
      if(vrangesExtendedMerged.back().second<venergy.back().first && dE>2*joinTogetherThresh) vrangesExtendedTemp.push_back(vrangesExtendedMerged.back());
    } else {
      vrangesExtendedTemp.push_back(vrangesExtendedMerged[i]);
    }
  }
  vrangesExtendedMerged = vrangesExtendedTemp;

  //remove global minima arising from merging 
  vrangesExtendedTemp.erase(vrangesExtendedTemp.begin(),vrangesExtendedTemp.end());
  for(int i=0; i<vrangesExtendedMerged.size(); i++){
    int iflag = 0;
    if(i==0){
      if(menergy[vrangesExtendedMerged[i].first]<venergy[0].second) iflag++;
    } else {
      if(menergy[vrangesExtendedMerged[i].first]<menergy[vrangesExtendedMerged[i-1].second]) iflag++;
    }
    if(i==vrangesExtendedMerged.size()-1){ 
      if(menergy[vrangesExtendedMerged[i].second]<venergy.back().second) iflag++;
    } else {
      if(menergy[vrangesExtendedMerged[i].second]<menergy[vrangesExtendedMerged[i+1].first]) iflag++;
    }
    if(iflag!=2) vrangesExtendedTemp.push_back(vrangesExtendedMerged[i]);
  }
  vrangesExtendedMerged = vrangesExtendedTemp;

  cout << "\n============= RANGES MERGED, 1st, last INTERVALS AND MINIMA ARE REMOVED =============== " << endl;
  for(int i=0; i<vrangesExtendedMerged.size(); i++){
    cout << vrangesExtendedMerged[i].first << " " << vrangesExtendedMerged[i].second << endl;
  }


  // ORIGINAL ENERGY PROFILE

  // find maxima and minima in energy profile 
  int nWindow = 5;
  vector< pair<int, double> > vminE, vmaxE; 
  vector< pair<int, double> >  vminmaxE;
  vector<int> vIdE; 
  for(int i=nWindow; i<venergy.size()-nWindow; i++){
    int iflagMin = 1; 
    for(int j=i-nWindow; j<=i+nWindow; j++){
      if(venergy[i].second > venergy[j].second) iflagMin = 0; 
    }
    if(iflagMin==1){
       vminmaxE.push_back(pair<int, double>(venergy[i].first,venergy[i].second));
       vIdE.push_back(-1); 
    }
    int iflagMax = 1; 
    for(int j=i-nWindow; j<=i+nWindow; j++){
      if(venergy[i].second < venergy[j].second) iflagMax = 0;
    }
    if(iflagMax==1){
     vminmaxE.push_back(pair<int, double>(venergy[i].first,venergy[i].second));
     vIdE.push_back(1);     
    }
  }
  /*
  for(int i=0; i<vIdE.size(); i++){
    cout << vIdE[i] <<  " " << vminmaxE[i].first << " " << vminmaxE[i].second << endl; 
  }
  */
  vector< pair<int,int> > vrangesMerged;

  if(vIdE.size()>1){
    // check for validity abd refine defined maxima and minima 
   vector<int> vIdTempE;
   vector< pair<int, double> >  vminmaxETemp;
   vminmaxETemp.push_back(pair<int, double>(vminmaxE[0].first,vminmaxE[0].second));
   vIdTempE.push_back(vIdE[0]);
   for(int i=1; i<vIdE.size(); i++){
     if(vIdE[i]==1 && vIdE[i-1]==1){
       int imin = -100; 
       double minTempE = 10000; 
       for(int j=0; j<venergy.size(); j++){
	 if(venergy[j].first > vminmaxE[i-1].first && venergy[j].first<vminmaxE[i].first){
           if(venergy[j].second < minTempE){
	     minTempE = venergy[j].second; 
             imin = j;
	   }
	 }
       }
       vminmaxETemp.push_back(pair<int, double>(venergy[imin].first,venergy[imin].second)); 
       vIdTempE.push_back(-1);
     }
     if(vIdE[i]==-1 && vIdE[i-1]==-1){
       int imax = -100; 
       double maxTempE = -1000000.; 
       for(int j=0; j<venergy.size(); j++){
         if(venergy[j].first > vminmaxE[i-1].first && venergy[j].first<vminmaxE[i].first){
           if(venergy[j].second > maxTempE){
             maxTempE = venergy[j].second;
             imax = j;
	   }
	 }
       }
       vminmaxETemp.push_back(pair<int, double>(venergy[imax].first,venergy[imax].second));
       vIdTempE.push_back(1);
     }
     if(vIdE[i]==1 && vIdE[i-1]==-1 && vminmaxE[i].second<vminmaxE[i-1].second){
       int imax = -100;
       double maxTempE = -1000000.;
       for(int j=0; j<venergy.size(); j++){
         if(venergy[j].first > vminmaxE[i-1].first && venergy[j].first<vminmaxE[i].first){
	   if(venergy[j].second > maxTempE){
             maxTempE = venergy[j].second;
             imax = j;
	   }
	 }
       }
       int imin = -100;
       double minTempE = 10000;
       for(int j=0; j<venergy.size(); j++){
	 if(venergy[j].first > venergy[imax].first && venergy[j].first<vminmaxE[i].first){
           if(venergy[j].second < minTempE){
             minTempE = venergy[j].second;
             imin = j;
           }
	 }
       }
       vminmaxETemp.push_back(pair<int, double>(venergy[imax].first,venergy[imax].second));
       vminmaxETemp.push_back(pair<int, double>(venergy[imin].first,venergy[imin].second));
       vIdTempE.push_back(1);
       vIdTempE.push_back(-1);
     } 
     if(vIdE[i]==-1 && vIdE[i-1]==1 && vminmaxE[i].second>vminmaxE[i-1].second){
       int imin = -100;
       double minTempE = 10000;
       for(int j=0; j<venergy.size(); j++){
         if(venergy[j].first > vminmaxE[i-1].first && venergy[j].first<vminmaxE[i].first){
           if(venergy[j].second < minTempE){
             minTempE = venergy[j].second;
             imin = j;
           }
         }
       }
       int imax = -100;
       double maxTempE = -1000000.;
       for(int j=0; j<venergy.size(); j++){
         if(venergy[j].first > venergy[imin].first && venergy[j].first<vminmaxE[i].first){
           if(venergy[j].second > maxTempE){
             maxTempE = venergy[j].second;
             imax = j;
           }
         }
       }
       vminmaxETemp.push_back(pair<int, double>(venergy[imin].first,venergy[imin].second));
       vminmaxETemp.push_back(pair<int, double>(venergy[imax].first,venergy[imax].second));
       vIdTempE.push_back(-1);
       vIdTempE.push_back(1);
     }
     vminmaxETemp.push_back(pair<int, double>(vminmaxE[i].first,vminmaxE[i].second)); 
     vIdTempE.push_back(vIdE[i]);


   }
   /*
   for(int i=0; i<vIdTempE.size(); i++){
     cout << vIdTempE[i] <<  " " << vminmaxETemp[i].first << " " << vminmaxETemp[i].second << endl;
   }
   */
   // find ranges around defined maxima
   double threshE = fabs(minE)/15.;
   vector< pair<int,int> > vranges; 
   for(int i=0; i<vIdTempE.size(); i++){
     if(vIdTempE[i]==1){
       double dE = 0; 
       int jmin = vminmaxETemp[i].first;
       while(dE<threshE){
         jmin--;
         if(jmin<venergy[0].first) break; 
         dE = fabs(vminmaxETemp[i].second - menergy[jmin]);
       }
       jmin++;
       int jmax = vminmaxETemp[i].first;
       dE = 0;
       while(dE<threshE){
         jmax++; 
         if(jmax>venergy.back().first) break;
         dE = fabs(vminmaxETemp[i].second - menergy[jmax]);
       }
       jmax--;
       vranges.push_back(pair<int,int>(jmin,jmax));
     }
   }
   sort(vranges.begin(),vranges.end(),sortranges);


   // merge overlaping ranges 
   //   vector< pair<int,int> > vrangesMerged;
   vrangesMerged.push_back(vranges[0]);
   for(int i=0; i<vranges.size(); i++){
     if(vranges[i].first>vrangesMerged.back().second){
       vrangesMerged.push_back(vranges[i]);
     } else {
       int jmin = vrangesMerged.back().first; 
       int jmax; 
       if(vranges[i].second > vrangesMerged.back().second){
         jmax = vranges[i].second;        
       } else {
         jmax = vrangesMerged.back().second;
       }
       vrangesMerged.back().first = jmin; 
       vrangesMerged.back().second = jmax;
     }
   }

   /*
   for(int i=0; i<vranges.size(); i++){
     cout << "R " << vranges[i].first << " " << vranges[i].second << endl;
   }
   for(int i=0;i<vrangesMerged.size(); i++){
     cout << "V " << vrangesMerged[i].first << " " << vrangesMerged[i].second << endl;
   }
   */

   //remove global minima arising from merging
   vector< pair<int,int> > vrangesMergedTemp;
   for(int i=0;i<vrangesMerged.size(); i++){
     int istart = -1000; 
     int iend = -1000;
     for(int j=0; j<vminmaxETemp.size(); j++){
       if(vminmaxETemp[j].first > vrangesMerged[i].first && vminmaxETemp[j].first <  vrangesMerged[i].second && istart == -1000) istart = j;
       if(istart != -1000 && vminmaxETemp[j].first> vrangesMerged[i].first && vminmaxETemp[j].first <  vrangesMerged[i].second) iend = j;
     }
     if( (vIdTempE[istart]==1 && vIdTempE[iend]==1) || (vIdTempE[istart]==-1 && vIdTempE[iend]==1) || (vIdTempE[istart]==1 && vIdTempE[iend]==-1) ){
        vrangesMergedTemp.push_back(vrangesMerged[i]);
     }
   }

   //remove the first and last ranges if the borders fall out of protein range
   vrangesMerged = vrangesMergedTemp;
   vrangesMergedTemp.erase(vrangesMergedTemp.begin(),vrangesMergedTemp.end());
   for(int i=0;i<vrangesMerged.size(); i++){
     if(i==0){
       if(vrangesMerged[0].first>venergy[0].first) vrangesMergedTemp.push_back(vrangesMerged[0]);
     } else if(i==vrangesMerged.size()-1){
       if(vrangesMerged.back().second<venergy.back().first) vrangesMergedTemp.push_back(vrangesMerged.back());
     } else {
       vrangesMergedTemp.push_back(vrangesMerged[i]);
     }
   }
   vrangesMerged = vrangesMergedTemp;

  }

  // To switch between two different algorithms for calculating ranges 
  // To go to the ORIGINAL ENERGY PROFILE comment out the next line
  vrangesMerged = vrangesExtendedMerged;


  //  cout << "Reading stride data" << endl; 

  ifstream in(argv[1]);
  if(in.is_open()==false){
    cout << "no data file " << argv[1] << endl;
    exit(0);
  }

  vector< vector<ares> > vLoops; 
  vector< int > vTight;   

  vector<asite> vsplit;

  int nLines = 0; 
  vector<ares> vres; 
  //  string line; 
  int prevType = 0; 
  vector<ares> vres_temp;
  while (getline(in,line)){
    vector<string> vfields = parse_line(line,' ');
    if(vfields[0]=="ASG"){
      nLines++;
      if(vfields[6]=="Coil" || vfields[6]=="Turn" || vfields[6]=="Bridge"){
        if(prevType == 0){
          for(int j=0; j<vres_temp.size(); j++){ 
  	    vres.push_back(vres_temp[j]);
	  }
          vres_temp.erase(vres_temp.begin(),vres_temp.end());
	}
        prevType = 1;
        int inum = atoi(vfields[4].c_str());
        int iRes = atoi(vfields[3].c_str());
        double ssa = atof(vfields[9].c_str());
        double cons = atof(vfields[13].c_str());
        vres.push_back(ares(inum,iRes,ssa,cons));
      } else {
        int inum = atoi(vfields[4].c_str());
        int iRes = atoi(vfields[3].c_str());
        double ssa = atof(vfields[9].c_str());
        double cons = atof(vfields[13].c_str());
        if(prevType == 1){
	  vres.push_back(ares(inum,iRes,ssa,cons));
          prevType = 0;
	} else {
	  vres_temp.erase(vres_temp.begin(),vres_temp.end());
          vres_temp.push_back(ares(inum,iRes,ssa,cons)); 
	}
      } 
    }
  }
  in.close();

  //  cout << "End of stride data" << endl;

  cout << "\n====== RESIDUES IN LOOPS (NUMBER, RES_ID, SSA, CONS, ssType) =======" << endl;
  for(int i=0; i<vres.size(); i++){
    cout << vres[i].inum << "\t" << vres[i].iRes << "\t" << vres[i].ssa << "\t" << vres[i].cons << "\t" << vres[i].ssType << endl;
  }


  vector<ares> v0; 
  v0.push_back(vres[0]); 
  vLoops.push_back(v0); 

  for(int i=1; i<vres.size(); i++){
    if(vres[i].iRes-vres[i-1].iRes==1){
      vLoops.back().push_back(vres[i]); 
    } else {
      vector<ares> v0; 
      v0.push_back(vres[i]); 
      vLoops.push_back(v0);
    }
  }

  vector<advcoord> vc = getData(argv[3]);
  vc.pop_back();

  cout << "\n======= LOOPS (iFIRST, ILAST, FIRST_RES_ID, LAST_RES_ID) =======" << endl;
  cout << "nTotal=" << vLoops.size() << endl;
  for(int i=0; i<vLoops.size(); i++){
    vTight.push_back(-1); 
    vector<ares> v1 = vLoops[i]; 
    int ifirst = v1[0].inum-1; 
    int ilast = v1.back().inum-1; 
    vector<advcoord> vleft, vright; 
    cout << ifirst << "\t" << ilast <<  "\t" << v1[0].iRes << "\t" << v1.back().iRes << endl;

    int iLastPrev = -10000; 
    if(i!=0) iLastPrev = vLoops[i-1].back().inum-1; 
    int iFirstNext = 10000;
    if(i!=vLoops.size()-1) iFirstNext = vLoops[i+1][0].inum-1; 

    for(int j=ifirst-1; j>ifirst-11; j--){
      if(j<0 || j==iLastPrev) break;
      vleft.push_back(vc[j]); 
      //      cout << "   l: " << j << "  " << iLastPrev << endl; 
    }
    for(int j=ilast+1; j<ilast+11; j++){
      if(j==vc.size() || j==iFirstNext) break;
      vright.push_back(vc[j]);
      //      cout << "   r: " << j  << " " << iFirstNext << endl;
    }
    
    //    cout << "SIZEL: " << vleft.size() << " SIZER: " << vright.size() << endl;
    if(vleft.size()>=4 && vright.size()>=5){
      vector<adist> vdist; 
      for(int j=0; j<vleft.size(); j++){
        for(int k=0; k<vright.size(); k++){
          double dx = vleft[j].dim.x - vright[k].dim.x; 
          double dy = vleft[j].dim.y - vright[k].dim.y;
          double dz = vleft[j].dim.z - vright[k].dim.z;
          vdist.push_back(adist(vleft[j].resSeq,vright[k].resSeq,pow(dx*dx+dy*dy+dz*dz, 0.5)));
	}
      }
      sort(vdist.begin(), vdist.end(), sortfunction);
      vector<adist> vdist_filtered;
      map<int,int> mdist; 
      for(int j=0; j<vdist.size(); j++){
	if(mdist[vdist[j].iRes1] != 1 && mdist[vdist[j].iRes2] != 1){
	  vdist_filtered.push_back(vdist[j]); 
          mdist[vdist[j].iRes1] = 1; 
          mdist[vdist[j].iRes2] = 1; 
	} 
      }
      int nTight = 0; 
      for(int j=0; j<vdist_filtered.size(); j++){  
        if(vdist_filtered[j].dist<10) nTight++;
      }
      if(nTight>=3 && v1.size()>1) vTight.back()=1;
    }
  }


  cout << "\n======= TIGHT LOOPS ======= " << vLoops.size() << " " << vTight.size() <<  endl;
  for(int i=0; i<vTight.size(); i++){
    cout << vTight[i] << endl;
  }

  for(int i=0; i<vLoops.size(); i++){
    vector<ares> v1 = vLoops[i];
    //    if(vTight[i]==1){
      vector<asite> vsplit_temp; 
      for(int j=0; j<v1.size()-1; j++){
        int iflag = 0;
	//        cout << "..... " << v1[j].iRes << " " << v1[j+1].iRes << " " << i << endl;
        if(menergy[v1[j].iRes]/minE<0.8){
            iflag++;
	}
        for(int k=0; k<vrangesMerged.size(); k++){
	  if(v1[j].iRes>=vrangesMerged[k].first && v1[j].iRes<=vrangesMerged[k].second && v1[j+1].iRes>=vrangesMerged[k].first && v1[j+1].iRes<=vrangesMerged[k].second){
            iflag++; 
            break;
	  }
	}
        if(v1[j].ssa>30 && v1[j+1].ssa>30) iflag++;
	if((v1[j].cons<2 || v1[j].cons==100) && (v1[j+1].cons<2 || v1[j+1].cons==100)) iflag++;
        
	//	cout << "    - " << iflag << endl;
        if(iflag==4){
          vsplit_temp.push_back(asite(v1[j],v1[j+1]));
          vsplit_temp.back().itight=vTight[i];
          vsplit_temp.back().splitE = menergy[v1[j].iRes];
	}

      }
      sort(vsplit_temp.begin(), vsplit_temp.end(), sortsplit);
      if(vsplit_temp.size()>0){
         vsplit.push_back(vsplit_temp[0]);
         for(int j=0; j<vsplit_temp.size(); j++){
	   if(fabs(vsplit_temp[j].theRes1.iRes-vsplit_temp[0].theRes1.iRes)>5){
             vsplit.push_back(vsplit_temp[j]);
             break;
	   } 
	 }
      }
      sort(vsplit.begin(), vsplit.end(), sortsplitFinal);
      //    }
  }

  cout << "\n------- SELECTED SPLIT SITES -------" << endl;
  for(int i=0; i<vsplit.size(); i++){  
    cout << vsplit[i].theRes1.iRes << " " << vsplit[i].theRes2.iRes << " " << vsplit[i].theRes1.ssa << " " << vsplit[i].theRes2.ssa << " " << vsplit[i].splitE << " " << vsplit[i].itight << endl;  
    os << vsplit[i].theRes1.iRes << " " << vsplit[i].theRes2.iRes << " " << vsplit[i].theRes1.ssa << " " << vsplit[i].theRes2.ssa << " " << vsplit[i].splitE << " " << vsplit[i].itight <<  endl;  
  }
  cout << "" << endl;

  /*
  for(int i=0; i<vres.size()-1; i++){
    if(vres[i+1].iRes-vres[i].iRes==1) vsplit.push_back(asite(vres[i],vres[i+1]));
    //    cout << vres[i].iRes << " " << vres[i].ssa << endl; 
  }
  */

  /*
  vector<asite> vsplit_temp;
  for(int i=0; i<vsplit.size(); i++){
    if(vsplit[i].theRes1.ssa>30 && vsplit[i].theRes2.ssa>30){
      vsplit_temp.push_back(vsplit[i]);
    }
  }
  vsplit = vsplit_temp;

  //  splitEnergySinglePlot(argv[1], argv[2], vsplit);
  splitEnergy(argv[1], argv[2], vsplit);

  for(int i=0; i<vsplit.size(); i++){
    if(vsplit[i].theRes1.ssa>30 && vsplit[i].theRes2.ssa>30){
    cout << vsplit[i].theRes1.iRes << " " << vsplit[i].theRes2.iRes << " " << vsplit[i].theRes1.ssa << " " << vsplit[i].theRes2.ssa << endl;
    os << vsplit[i].theRes1.iRes << " " << vsplit[i].theRes2.iRes << " " << vsplit[i].theRes1.ssa << " " << vsplit[i].theRes2.ssa << endl;
    }
  }
  */
  outf.close();

}
