#include "Logger.h"
using namespace std;

Logger::Logger( string& rootDir, string& filename ) {
  // Setup the Log directory
  string cmd = "mkdir -p " + rootDir + "/";
  system( cmd.c_str() );
  cmd = "cd " + rootDir;
  system( cmd.c_str() );

  // Open up the Log file
  outFile.open( filename.c_str(), ios::app );
  if ( outFile.fail() ) {
    cerr << "LOGGER: Error opening Log file [" << filename << "]." << endl;
    exit( 1 );
  }
}

Logger::~Logger() {
  outFile.close();
}

void Logger::log( string msg ) {
  tm * clock = getTimePointer();
  outFile << "[" << clock->tm_year+1900 << "-" << clock->tm_mon+1 << "-"
	  << clock->tm_mday << " " << clock->tm_hour << ":" << clock->tm_min << ":"
	  << clock->tm_sec << "] " << msg << endl;
}

tm* Logger::getTimePointer() {
  time_t T = time(NULL);
  tm * now = localtime(&T);
  return (now);
}

