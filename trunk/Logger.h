#ifndef LOGGER_H
#define LOGGER_H

#include <iostream>
#include <string>
#include <fstream>
#include <time.h>

class Logger {
 public:
  Logger( std::string&, std::string& );
  ~Logger();
  void log( std::string );

 private:
  // Function:
  tm* getTimePointer();

  // Member Variable:
  std::ofstream outFile;
};

#endif
