#!/bin/bash
#example : ./sdapshell.sh -p "./citizen_survey" -a "add" -A "convert" -f "file1" "file2" "file3"
while getopts ":p:a:A:f:" opt; do
  case "$opt" in
    f) files=$OPTARG ;;
    p) projectpath=$OPTARG ;;	 
    a) action=$OPTARG ;;	
    A) attrib="--$OPTARG" ;;	
    \?) echo "Invalid Arguments" ;; 	
  esac
done

shift $(( OPTIND - 1 ))
for file in "$@"; do
  files="$files $file"
done

sdaps $projectpath $action $attrib $files


