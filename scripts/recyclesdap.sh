#!/bin/bash
#This script deletes current SDAPs project and recreate it.
rm -rf pmccs_aundh
sdaps ./pmccs_aundh setup_tex test.tex
echo "Success"
